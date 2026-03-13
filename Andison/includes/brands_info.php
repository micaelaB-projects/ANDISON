<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_normalize_product_images')) {
    function andison_normalize_product_images(array $product): array
    {
        $images = $product['images'] ?? null;

        if (is_string($images)) {
            $decoded = json_decode($images, true);
            $images = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($images)) {
            $images = [];
        }

        $normalized = [];
        foreach ($images as $imageUrl) {
            $imageUrl = trim((string)$imageUrl);
            if ($imageUrl !== '') {
                $normalized[] = $imageUrl;
            }
        }

        if (empty($normalized)) {
            $singleImage = trim((string)($product['image'] ?? ''));
            if ($singleImage !== '') {
                $normalized[] = $singleImage;
            }
        }

        return array_values(array_unique($normalized));
    }
}

if (!function_exists('andison_get_brands_info')) {
    function andison_get_brands_info(): array
    {
        // ── 5-minute file cache ────────────────────────────────────────────
        $cacheFile = dirname(__DIR__) . '/data/_cache/brands_full.cache';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 300) {
            $cached = @unserialize((string)file_get_contents($cacheFile));
            if (is_array($cached) && !empty($cached)) return $cached;
        }

        // ── Parallel Supabase fetch ────────────────────────────────────────
        $fetched     = andison_sb_select_multi([
            'brands'   => 'brands?order=name',
            'products' => 'products?select=*&limit=10000',
        ]);
        $brands      = $fetched['brands'];
        $allProducts = $fetched['products'];

        if (empty($brands)) {
            return [];
        }

        // Group by brand — lowercase key for case-insensitive matching

        $sbByLower  = [];
        $sbOrigCase = [];
        foreach ($allProducts as $product) {
            $brand = trim((string)($product['brand'] ?? ''));
            if ($brand === '') continue;
            // Decode images JSON string from Supabase into array
            if (isset($product['images']) && is_string($product['images'])) {
                $dec = json_decode($product['images'], true);
                $product['images'] = is_array($dec) ? $dec : [];
                if (empty($product['image']) && !empty($product['images'][0])) {
                    $product['image'] = $product['images'][0];
                }
            } elseif (!isset($product['images'])) {
                $product['images'] = $product['image'] ? [$product['image']] : [];
            }
            $lk = strtolower($brand);
            $sbByLower[$lk][]  = $product;
            if (!isset($sbOrigCase[$lk])) $sbOrigCase[$lk] = $brand;
        }

        $result    = [];
        $processed = [];
        foreach ($brands as $brand) {
            $name = $brand['name'] ?? '';
            if ($name === '') continue;
            $lk = strtolower($name);
            $processed[$lk] = true;
            $result[$name] = [
                'description' => $brand['description'] ?? '',
                'products'    => $sbByLower[$lk] ?? [],
            ];
        }

        // Also surface brands that exist in products table but NOT in the brands table
        foreach ($sbByLower as $lk => $prods) {
            if (isset($processed[$lk])) continue;
            $nm = $sbOrigCase[$lk];
            $result[$nm] = ['description' => '', 'products' => $prods];
        }

        // ── Write cache ───────────────────────────────────────────────────
        @mkdir(dirname($cacheFile), 0755, true);
        @file_put_contents($cacheFile, serialize($result), LOCK_EX);

        return $result;
    }
}
/**
 * Save a SINGLE brand's description + product list to Supabase.
 * Safer than saving all brands — only touches the one brand that changed.
 */
if (!function_exists('andison_save_single_brand')) {
    function andison_save_single_brand(string $name, array $data): bool
    {
        if ($name === '') return false;

        // ── 1. Save brand row (update if exists, insert if new) ──────────────
        $existing = andison_sb_select('brands', 'select=id&name=eq.' . rawurlencode($name) . '&limit=1');
        if (!empty($existing)) {
            andison_sb_update('brands', ['description' => $data['description'] ?? ''], 'name=eq.' . rawurlencode($name));
        } else {
            andison_sb_insert('brands', [['name' => $name, 'description' => $data['description'] ?? '']]);
        }

        // ── 2. Replace this brand's products: delete all then re-insert ───────
        // Delete by brand name first (primary key for brand-owned rows)
        andison_sb_delete('products', 'brand=eq.' . rawurlencode($name));

        // Never delete other brands' rows here.
        // This save path is scoped to a single brand only.

        $productRows = [];
        foreach ($data['products'] ?? [] as $product) {
            $images = andison_normalize_product_images($product);
            $row = [
                'brand'          => $name,
                'product_name'   => $product['product_name'] ?? ($product['name'] ?? ''),
                'model'          => $product['model'] ?? '',
                'type'           => $product['type'] ?? '',
                'badge'          => $product['badge'] ?? '',
                'description'    => $product['description'] ?? '',
                'specifications' => $product['specifications'] ?? ($product['specs'] ?? ''),
                'price'          => $product['price'] ?? '',
                'image'          => $product['image'] ?? ($images[0] ?? ''),
                'datasheet'      => $product['datasheet'] ?? '',
                // Keep row keys consistent across the full batch so image-enabled rows
                // do not get stripped when another product in the same brand has no image yet.
                'images'         => json_encode($images),
                'category_id'    => trim((string)($product['category_id'] ?? '')),
                'subcategory_id' => trim((string)($product['subcategory_id'] ?? '')),
            ];
            $productRows[] = $row;
        }

        $ok = true;
        if (!empty($productRows)) {
            $ok = andison_sb_insert('products', $productRows);
            // If insert failed, retry without 'images' column (may not exist in all schemas)
            // but KEEP category_id and subcategory_id so category assignment is preserved.
            if (!$ok) {
                $noImgRows = array_map(function (array $r): array {
                    unset($r['images']);
                    return $r;
                }, $productRows);
                $ok = andison_sb_insert('products', $noImgRows);
            }
            // Final fallback: core columns only — always include category assignment
            if (!$ok) {
                $strippedRows = array_map(function (array $r): array {
                    return array_intersect_key($r, array_flip([
                        'brand', 'product_name', 'model', 'type', 'badge',
                        'description', 'specifications', 'price', 'image',
                        'category_id', 'subcategory_id',
                    ]));
                }, $productRows);
                $ok = andison_sb_insert('products', $strippedRows);
            }
        }

        // ── 3. Bust page cache so next load re-fetches from Supabase ──────────
        @unlink(dirname(__DIR__) . '/data/_cache/brands_full.cache');

        return $ok;
    }
}

if (!function_exists('andison_save_brands_info')) {
    function andison_save_brands_info(array $brands): bool
    {
        $allOk = true;
        foreach ($brands as $name => $data) {
            if (!andison_save_single_brand($name, $data)) {
                $allOk = false;
            }
        }
        return $allOk;
    }
}



