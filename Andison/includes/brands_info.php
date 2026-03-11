<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_get_brands_info')) {
    function andison_get_brands_info(): array
    {
        // ── 5-minute file cache ────────────────────────────────────────────
        $cacheFile = dirname(__DIR__) . '/data/_cache/brands_full.cache';
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

        if (empty($brands)) {
            $dataFile = dirname(__DIR__) . '/data/brands_info.json';
            $loaded = andison_read_json_file($dataFile, []);
            return (is_array($loaded) && !empty($loaded)) ? $loaded : [];
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

        // Also delete any stale duplicate rows that have the same model + category slot
        // but were saved under a different brand name (e.g. via categories admin or old import).
        // We do this per category slot to avoid touching other brands' products.
        $catSlots = [];
        foreach ($data['products'] ?? [] as $p) {
            $cId = trim((string)($p['category_id'] ?? ''));
            $sId = trim((string)($p['subcategory_id'] ?? ''));
            if ($cId !== '' && $sId !== '') {
                $catSlots[$cId . '::' . $sId][] = trim((string)($p['model'] ?? ''));
            }
        }
        foreach ($catSlots as $slot => $models) {
            [$cId, $sId] = explode('::', $slot, 2);
            foreach (array_filter(array_unique($models)) as $mdl) {
                andison_sb_delete(
                    'products',
                    'category_id=eq.' . rawurlencode($cId)
                    . '&subcategory_id=eq.' . rawurlencode($sId)
                    . '&model=eq.' . rawurlencode($mdl)
                );
            }
        }

        $productRows = [];
        foreach ($data['products'] ?? [] as $product) {
            $row = [
                'brand'          => $name,
                'product_name'   => $product['product_name'] ?? ($product['name'] ?? ''),
                'model'          => $product['model'] ?? '',
                'type'           => $product['type'] ?? '',
                'badge'          => $product['badge'] ?? '',
                'description'    => $product['description'] ?? '',
                'specifications' => $product['specifications'] ?? ($product['specs'] ?? ''),
                'price'          => $product['price'] ?? '',
                'image'          => $product['image'] ?? '',
            ];
            // Only include 'images' when there are actual images — column may not exist
            // on all Supabase environments: ALTER TABLE products ADD COLUMN IF NOT EXISTS images text;
            if (is_array($product['images'] ?? null) && !empty($product['images'])) {
                $row['images'] = json_encode(array_values($product['images']));
            }
            // Only include category fields when set — avoids failing if columns don't exist yet
            if (!empty($product['category_id'])) {
                $row['category_id']    = $product['category_id'];
                $row['subcategory_id'] = $product['subcategory_id'] ?? '';
            }
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

        // ── 4. Update local JSON cache for this brand ─────────────────────────
        $dataFile = dirname(__DIR__) . '/data/brands_info.json';
        $cached   = andison_read_json_file($dataFile, []);
        if (!is_array($cached)) $cached = [];

        // Capture OLD category slots BEFORE overwriting, so we can clean them up
        $oldProducts = $cached[$name]['products'] ?? [];
        $oldCatKeys  = [];
        foreach ($oldProducts as $op) {
            $oCId = trim((string)($op['category_id'] ?? ''));
            $oSId = trim((string)($op['subcategory_id'] ?? ''));
            if ($oCId !== '' && $oSId !== '') {
                $oldCatKeys[$oCId . '::' . $oSId] = true;
            }
        }

        $cached[$name] = $data;
        andison_write_json_file($dataFile, $cached);

        // ── 5. Sync categorised products to local category JSON files ──────────
        // Build new category groups from the current product list
        $newCatGroups = [];
        foreach ($data['products'] ?? [] as $product) {
            $cId = trim((string)($product['category_id'] ?? ''));
            $sId = trim((string)($product['subcategory_id'] ?? ''));
            if ($cId === '' || $sId === '') continue;
            $newCatGroups[$cId . '::' . $sId][] = $product;
        }

        // Process ALL keys: old ones (to clean up deleted products) + new ones (to add/update)
        $allKeys     = array_unique(array_merge(array_keys($oldCatKeys), array_keys($newCatGroups)));
        $productsDir = dirname(__DIR__) . '/data/products';

        foreach ($allKeys as $key) {
            [$cId, $sId] = explode('::', $key, 2);
            $jsonFile = $productsDir . '/' . urlencode($cId) . '/' . urlencode($sId) . '.json';

            // Read existing list, strip ALL entries for this brand (handles deletions & moves)
            $existingList = [];
            if (file_exists($jsonFile)) {
                $raw = @file_get_contents($jsonFile);
                if ($raw !== false) {
                    $parsed = json_decode($raw, true);
                    if (is_array($parsed)) {
                        foreach ($parsed as $p) {
                            if (($p['brand'] ?? '') !== $name) {
                                $existingList[] = $p;
                            }
                        }
                    }
                }
            }

            // Re-add this brand's products for this slot (empty if deleted/moved away)
            foreach ($newCatGroups[$key] ?? [] as $product) {
                $pName = $product['product_name'] ?? ($product['name'] ?? '');
                $imgs  = $product['images'] ?? [];
                if (!is_array($imgs)) $imgs = [];
                $existingList[] = [
                    'name'           => $pName,
                    'model'          => $product['model'] ?? '',
                    'type'           => $product['type'] ?? '',
                    'price'          => $product['price'] ?? '',
                    'badge'          => $product['badge'] ?? '',
                    'description'    => $product['description'] ?? '',
                    'specs'          => $product['specifications'] ?? ($product['specs'] ?? ''),
                    'image'          => $product['image'] ?? '',
                    'images'         => $imgs,
                    'brand'          => $name,
                    'category_id'    => $cId,
                    'subcategory_id' => $sId,
                ];
            }

            // Write the merged list back (even if empty — clears stale data)
            $dir = dirname($jsonFile);
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            @file_put_contents(
                $jsonFile,
                json_encode($existingList, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            );
        }

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



