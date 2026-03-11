<?php
declare(strict_types=1);

// Pull in Supabase helpers so andison_sb_select() is available when our function runs.
require_once __DIR__ . '/../Andison/includes/storage.php';
require_once __DIR__ . '/../Andison/includes/supabase.php';

/**
 * Scan Andison/data/products/{category}/*.json and group products by brand (lowercase key).
 */
if (!function_exists('_andison_products_by_brand_from_json')) {
    function _andison_products_by_brand_from_json(): array
    {
        $dataDir = dirname(__DIR__) . '/Andison/data/products';
        if (!is_dir($dataDir)) return [];

        $byBrand = [];
        foreach (glob($dataDir . '/*', GLOB_ONLYDIR) as $catDir) {
            foreach (glob($catDir . '/*.json') as $jsonFile) {
                $raw = @file_get_contents($jsonFile);
                if ($raw === false) continue;
                $items = json_decode($raw, true);
                if (!is_array($items)) continue;
                foreach ($items as $item) {
                    if (!is_array($item)) continue;
                    $brand = trim((string)($item['brand'] ?? ''));
                    if ($brand === '') continue;
                    $lk = strtolower($brand);
                    $byBrand[$lk]['name']         = $brand;
                    $byBrand[$lk]['products'][]   = $item;
                }
            }
        }
        return $byBrand;
    }
}

/**
 * Merged getter: Supabase data takes priority; category JSON files fill gaps.
 * Defined here BEFORE the Andison include so that file's function_exists guard skips it.
 */
if (!function_exists('andison_get_brands_info')) {
    function andison_get_brands_info(): array
    {
        // ── 5-minute file cache ────────────────────────────────────────────
        $cacheFile = __DIR__ . '/../Andison/data/_cache/brands_full.cache';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 0) {
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

        $sbByLower  = [];
        $sbOrigCase = [];
        foreach ($allProducts as $product) {
            $brand = trim((string)($product['brand'] ?? ''));
            if ($brand === '') continue;
            // Decode images JSON string from Supabase into a PHP array
            if (isset($product['images']) && is_string($product['images'])) {
                $dec = json_decode($product['images'], true);
                $product['images'] = is_array($dec) ? $dec : [];
                if (empty($product['image']) && !empty($product['images'][0])) {
                    $product['image'] = $product['images'][0];
                }
            } elseif (!isset($product['images'])) {
                $product['images'] = $product['image'] ? [$product['image']] : [];
            } else {
                // null in PHP means key exists but is null — treat same as missing
                $product['images'] = $product['image'] ? [$product['image']] : [];
            }
            $lk = strtolower($brand);
            $sbByLower[$lk][]  = $product;
            if (!isset($sbOrigCase[$lk])) $sbOrigCase[$lk] = $brand;
        }

        // Supplement with products from category JSON files
        $jsonData = _andison_products_by_brand_from_json();

        $result    = [];
        $processed = [];

        // Brands registered in the brands table
        foreach ($brands as $brand) {
            $name = $brand['name'] ?? '';
            if ($name === '') continue;
            $lk = strtolower($name);
            $processed[$lk] = true;
            $sbProds   = $sbByLower[$lk] ?? [];
            $jsonProds  = $jsonData[$lk]['products'] ?? [];
            // Prefer Supabase products; fall back to JSON if none in database
            $result[$name] = [
                'description' => $brand['description'] ?? '',
                'products'    => !empty($sbProds) ? $sbProds : $jsonProds,
            ];
        }

        // Brands only in the products table (not in brands table)
        foreach ($sbByLower as $lk => $prods) {
            if (isset($processed[$lk])) continue;
            $nm = $sbOrigCase[$lk];
            $result[$nm] = ['description' => '', 'products' => $prods];
            $processed[$lk] = true;
        }

        // Brands only in JSON files (not in Supabase at all)
        foreach ($jsonData as $lk => $info) {
            if (isset($processed[$lk])) continue;
            $result[$info['name']] = ['description' => '', 'products' => $info['products']];
        }

        // ── Write cache ───────────────────────────────────────────────────
        @mkdir(dirname($cacheFile), 0755, true);
        @file_put_contents($cacheFile, serialize($result), LOCK_EX);

        return $result;
    }
}

// Load remaining Andison helpers (andison_save_single_brand, andison_save_brands_info, etc.).
// andison_get_brands_info is already defined above, so the Andison file's guard skips it.
require_once __DIR__ . '/../Andison/includes/brands_info.php';
