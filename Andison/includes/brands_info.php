<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_get_brands_info')) {
    function andison_get_brands_info(): array
    {
        $brands = andison_sb_select('brands', 'order=name');

        if (empty($brands)) {
            $dataFile = dirname(__DIR__) . '/data/brands_info.json';
            $loaded = andison_read_json_file($dataFile, []);
            return (is_array($loaded) && !empty($loaded)) ? $loaded : [];
        }

        // Fetch ALL products and group by brand — lowercase key for case-insensitive matching
        $allProducts = andison_sb_select('products', 'select=*&limit=10000');

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
        andison_sb_delete('products', 'brand=eq.' . rawurlencode($name));

        $productRows = [];
        foreach ($data['products'] ?? [] as $product) {
            $row = [
                'brand'          => $name,
                'product_name'   => $product['product_name'] ?? '',
                'model'          => $product['model'] ?? '',
                'type'           => $product['type'] ?? '',
                'badge'          => $product['badge'] ?? '',
                'description'    => $product['description'] ?? '',
                'specifications' => $product['specifications'] ?? '',
                'price'          => $product['price'] ?? '',
                'image'          => $product['image'] ?? '',
                'images'         => is_array($product['images'] ?? null) && !empty($product['images'])
                                        ? json_encode(array_values($product['images']))
                                        : null,
            ];
            // images[] array is preserved in brands_info.json (local cache) but not sent
            // to Supabase unless the column exists:
            //   ALTER TABLE products ADD COLUMN IF NOT EXISTS images text;
            if (!empty($product['category_id'])) {
                $row['category_id']    = $product['category_id'];
                $row['subcategory_id'] = $product['subcategory_id'] ?? '';
            }
            $productRows[] = $row;
        }

        $ok = true;
        if (!empty($productRows)) {
            $ok = andison_sb_insert('products', $productRows);
        }

        // ── 3. Update local JSON cache for this brand ─────────────────────────
        $dataFile = dirname(__DIR__) . '/data/brands_info.json';
        $cached   = andison_read_json_file($dataFile, []);
        if (!is_array($cached)) $cached = [];
        $cached[$name] = $data;
        andison_write_json_file($dataFile, $cached);

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



