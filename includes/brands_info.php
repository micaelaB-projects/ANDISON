<?php
declare(strict_types=1);

// Pull in Supabase helpers so andison_sb_select() is available when our function runs.
require_once __DIR__ . '/../Andison/includes/supabase.php';

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

        $result    = [];
        $processed = [];

        // Brands registered in the brands table
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

        // Brands only in the products table (not in brands table)
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

// Load remaining Andison helpers (andison_save_single_brand, andison_save_brands_info, etc.).
// andison_get_brands_info is already defined above, so the Andison file's guard skips it.
require_once __DIR__ . '/../Andison/includes/brands_info.php';
