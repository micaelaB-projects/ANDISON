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
    function andison_get_brands_info(bool $forceFresh = false): array
    {
        // ── 5-minute file cache ────────────────────────────────────────────
        $cacheFile = dirname(__DIR__) . '/data/_cache/brands_full.cache';
        $cached = null;
        if (file_exists($cacheFile)) {
            $cached = @unserialize((string)file_get_contents($cacheFile));
        }
        if (!$forceFresh && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 300) {
            if (is_array($cached) && !empty($cached)) return $cached;
        }

        // ── Parallel Supabase fetch ────────────────────────────────────────
        $fetched     = andison_sb_select_multi([
            'brands'   => 'brands?order=name',
            'products' => 'products?select=*&limit=10000',
        ]);
        $brandsRaw      = $fetched['brands'] ?? [];
        $allProductsRaw = $fetched['products'] ?? [];

        $looksLikeList = static function ($value): bool {
            return is_array($value) && ($value === [] || array_keys($value) === range(0, count($value) - 1));
        };

        // If Supabase returns an error object instead of a rows list, prefer last known cache.
        if (!$looksLikeList($brandsRaw) || !$looksLikeList($allProductsRaw)) {
            if (is_array($cached) && !empty($cached)) return $cached;
            return [];
        }

        $brands      = $brandsRaw;
        $allProducts = $allProductsRaw;

        if (empty($brands)) {
            if (is_array($cached) && !empty($cached)) return $cached;
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
    function andison_save_single_brand(string $name, array $data, array $options = []): bool
    {
        if ($name === '') return false;

        $allowEmptyProducts = !empty($options['allowEmptyProducts']);
        $allowProductCountDecrease = !empty($options['allowProductCountDecrease']);

        $lockDir = dirname(__DIR__) . '/data/_cache/locks';
        @mkdir($lockDir, 0755, true);
        $lockFile = $lockDir . '/brand_' . md5(strtolower($name)) . '.lock';
        $lockHandle = @fopen($lockFile, 'c');
        if (is_resource($lockHandle)) {
            @flock($lockHandle, LOCK_EX);
        }

        $buildProductRows = static function (array $products, string $brandName): array {
            $rows = [];
            foreach ($products as $product) {
                $images = andison_normalize_product_images($product);
                $rows[] = [
                    'brand'          => $brandName,
                    'product_name'   => $product['product_name'] ?? ($product['name'] ?? ''),
                    'model'          => $product['model'] ?? '',
                    'type'           => $product['type'] ?? '',
                    'badge'          => $product['badge'] ?? '',
                    'description'    => $product['description'] ?? '',
                    'specifications' => $product['specifications'] ?? ($product['specs'] ?? ''),
                    'price'          => $product['price'] ?? '',
                    'image'          => $product['image'] ?? ($images[0] ?? ''),
                    'datasheet'      => $product['datasheet'] ?? '',
                    'images'         => json_encode($images),
                    'category_id'    => trim((string)($product['category_id'] ?? '')),
                    'subcategory_id' => trim((string)($product['subcategory_id'] ?? '')),
                    'sub_subcategory_id' => trim((string)($product['sub_subcategory_id'] ?? '')),
                ];
            }
            return $rows;
        };

        $insertWithFallback = static function (array $rows): bool {
            if (empty($rows)) return true;

            $ok = andison_sb_insert('products', $rows);
            if ($ok) return true;

            $noImgRows = array_map(static function (array $r): array {
                unset($r['images']);
                return $r;
            }, $rows);
            $ok = andison_sb_insert('products', $noImgRows);
            if ($ok) return true;

            $noSubSubRows = array_map(static function (array $r): array {
                unset($r['sub_subcategory_id']);
                return $r;
            }, $rows);
            $ok = andison_sb_insert('products', $noSubSubRows);
            if ($ok) return true;

            $noImgNoSubSubRows = array_map(static function (array $r): array {
                unset($r['images'], $r['sub_subcategory_id']);
                return $r;
            }, $rows);
            $ok = andison_sb_insert('products', $noImgNoSubSubRows);
            if ($ok) return true;

            $strippedRows = array_map(static function (array $r): array {
                return array_intersect_key($r, array_flip([
                    'brand', 'product_name', 'model', 'type', 'badge',
                    'description', 'specifications', 'price', 'image', 'datasheet',
                    'category_id', 'subcategory_id', 'sub_subcategory_id',
                ]));
            }, $rows);
            $ok = andison_sb_insert('products', $strippedRows);
            if ($ok) return true;

            $legacyStrippedRows = array_map(static function (array $r): array {
                unset($r['sub_subcategory_id']);
                return $r;
            }, $strippedRows);

            return andison_sb_insert('products', $legacyStrippedRows);
        };

        try {
            // ── 1. Save brand row (update if exists, insert if new) ──────────────
            $existing = andison_sb_select('brands', 'select=id&name=eq.' . rawurlencode($name) . '&limit=1');
            if (!empty($existing)) {
                andison_sb_update('brands', ['description' => $data['description'] ?? ''], 'name=eq.' . rawurlencode($name));
            } else {
                andison_sb_insert('brands', [['name' => $name, 'description' => $data['description'] ?? '']]);
            }

            $existingProductRows = andison_sb_select('products', 'brand=eq.' . rawurlencode($name) . '&limit=10000');
            $incomingProducts = is_array($data['products'] ?? null) ? $data['products'] : [];

            // Guard against stale/partial payloads that could wipe products.
            if ((!$allowProductCountDecrease && count($incomingProducts) < count($existingProductRows)) ||
                (!$allowEmptyProducts && empty($incomingProducts) && !empty($existingProductRows))) {
                return false;
            }

            // ── 2. Replace this brand's products: delete all then re-insert ───────
            // Delete by brand name first (primary key for brand-owned rows)
            if (!andison_sb_delete('products', 'brand=eq.' . rawurlencode($name))) {
                return false;
            }

            $ok = true;
            if (!empty($incomingProducts)) {
                $ok = $insertWithFallback($buildProductRows($incomingProducts, $name));
                if (!$ok && !empty($existingProductRows)) {
                    // Best-effort rollback to the pre-delete product set.
                    $insertWithFallback($buildProductRows($existingProductRows, $name));
                }
            }

            if (!$ok) {
                return false;
            }

            // ── 3. Bust page cache so next load re-fetches from Supabase ──────────
            @unlink(dirname(__DIR__) . '/data/_cache/brands_full.cache');

            return true;
        } finally {
            if (is_resource($lockHandle)) {
                @flock($lockHandle, LOCK_UN);
                @fclose($lockHandle);
            }
        }
    }
}

if (!function_exists('andison_save_brands_info')) {
    function andison_save_brands_info(array $brands): bool
    {
        $allOk = true;
        foreach ($brands as $name => $data) {
            if (!andison_save_single_brand($name, $data, [
                'allowEmptyProducts' => true,
                'allowProductCountDecrease' => true,
            ])) {
                $allOk = false;
            }
        }
        return $allOk;
    }
}



