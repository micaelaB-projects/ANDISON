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

if (!function_exists('andison_product_semantic_key')) {
    function andison_product_semantic_key(array $product): string
    {
        $normalize = static function (string $value): string {
            $value = strtolower(trim($value));
            return preg_replace('/\s+/', ' ', $value) ?? $value;
        };

        $brand = $normalize((string)($product['brand'] ?? ''));
        $model = $normalize((string)($product['model'] ?? ''));
        $name = $normalize((string)($product['product_name'] ?? ($product['name'] ?? '')));
        $type = $normalize((string)($product['type'] ?? ''));

        // Allow duplicate model entries (requested by admin workflow).
        // Dedupe only when the broader business-identifying fields are also the same.
        if ($model !== '') {
            $category = $normalize((string)($product['category_id'] ?? ''));
            $subcategory = $normalize((string)($product['subcategory_id'] ?? ''));
            $subSubcategory = $normalize((string)($product['sub_subcategory_id'] ?? ''));
            return implode('::', [$brand, $model, $name, $type, $category, $subcategory, $subSubcategory]);
        }

        return implode('::', [
            $brand,
            $name,
            $type,
        ]);
    }
}

if (!function_exists('andison_canonical_brand_name')) {
    function andison_canonical_brand_name(string $brand): string
    {
        $raw = trim($brand);
        $normalized = strtolower($raw);

        if ($normalized === 'dryrod. ii' || $normalized === 'dryrod ii' || $normalized === 'phoenix dry rod' || $normalized === 'phoenix dryrod') {
            return 'DryRod. II';
        }

        if ($normalized === 'bw technologies' || $normalized === 'bw') {
            return 'BW';
        }

        if ($normalized === 'hard worker' || $normalized === 'hard workers' || $normalized === 'hardworker') {
            return 'HARDWORKER';
        }

        return $raw;
    }
}

if (!function_exists('andison_brand_row_unpack')) {
    function andison_brand_row_unpack(array $brandRow): array
    {
        $raw = (string)($brandRow['description'] ?? '');
        $raw = trim($raw);
        if ($raw === '') {
            return ['description' => '', 'logo' => ''];
        }

        $decoded = json_decode($raw, true);
        if (
            is_array($decoded)
            && (string)($decoded['format'] ?? '') === 'andison_brand_v1'
        ) {
            return [
                'description' => trim((string)($decoded['description'] ?? '')),
                'logo' => trim((string)($decoded['logo'] ?? '')),
            ];
        }

        return ['description' => $raw, 'logo' => ''];
    }
}

if (!function_exists('andison_brand_row_pack')) {
    function andison_brand_row_pack(string $description, string $logoUrl = ''): string
    {
        $description = trim($description);
        $logoUrl = trim($logoUrl);

        if ($logoUrl === '') {
            return $description;
        }

        $payload = [
            'format' => 'andison_brand_v1',
            'description' => $description,
            'logo' => $logoUrl,
        ];

        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return (is_string($encoded) && $encoded !== '') ? $encoded : $description;
    }
}

if (!function_exists('andison_product_record_score')) {
    function andison_product_record_score(array $product): int
    {
        $score = 0;

        $specifications = trim((string)($product['specifications'] ?? ($product['specs'] ?? '')));
        if ($specifications !== '') {
            $score += 120;
            if (str_contains($specifications, 'andison_specs_v2') || str_contains($specifications, 'andison_specs_v3')) {
                $score += 120;
            }
        }

        $description = trim((string)($product['description'] ?? ''));
        if ($description !== '') {
            $score += 30;
        }

        $datasheet = trim((string)($product['datasheet'] ?? ''));
        if ($datasheet !== '') {
            $score += 25;
        }

        $images = andison_normalize_product_images($product);
        $score += min(5, count($images)) * 5;

        $model = trim((string)($product['model'] ?? ''));
        if ($model !== '') {
            $score += 10;
        }

        $type = trim((string)($product['type'] ?? ''));
        if ($type !== '') {
            $score += 8;
        }

        $id = isset($product['id']) ? (int)$product['id'] : 0;
        if ($id > 0) {
            $score += min(1000, $id);
        }

        return $score;
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
            'products' => 'products?select=*&order=id.asc&limit=10000',
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

        $dedupeProducts = static function (array $products): array {
            $byPrimaryKey = [];

            foreach ($products as $product) {
                $signature = andison_product_semantic_key($product);
                $id = isset($product['id']) ? (int)$product['id'] : 0;
                $primaryKey = $signature !== '' ? ('mk:' . $signature) : ($id > 0 ? ('id:' . $id) : ('fk:' . md5(json_encode($product))));

                if (!isset($byPrimaryKey[$primaryKey])) {
                    $byPrimaryKey[$primaryKey] = $product;
                } else {
                    $existingScore = andison_product_record_score($byPrimaryKey[$primaryKey]);
                    $candidateScore = andison_product_record_score($product);
                    if ($candidateScore >= $existingScore) {
                        $byPrimaryKey[$primaryKey] = $product;
                    }
                }
            }

            return array_values($byPrimaryKey);
        };

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
            $brand = andison_canonical_brand_name($brand);
            $product['brand'] = $brand;
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
            $name = andison_canonical_brand_name((string)($brand['name'] ?? ''));
            if ($name === '') continue;
            $lk = strtolower($name);
            $processed[$lk] = true;
            $brandMeta = andison_brand_row_unpack(is_array($brand) ? $brand : []);
            $result[$name] = [
                'description' => $brandMeta['description'] ?? '',
                'logo' => $brandMeta['logo'] ?? '',
                'products'    => $dedupeProducts($sbByLower[$lk] ?? []),
            ];
        }

        // Also surface brands that exist in products table but NOT in the brands table
        foreach ($sbByLower as $lk => $prods) {
            if (isset($processed[$lk])) continue;
            $nm = $sbOrigCase[$lk];
            $result[$nm] = ['description' => '', 'logo' => '', 'products' => $dedupeProducts($prods)];
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
                $categoryId = trim((string)($product['category_id'] ?? ''));
                $subcategoryId = trim((string)($product['subcategory_id'] ?? ''));
                $subSubcategoryId = trim((string)($product['sub_subcategory_id'] ?? ''));
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
                    'category_id'    => $categoryId === '' ? null : $categoryId,
                    'subcategory_id' => $subcategoryId === '' ? null : $subcategoryId,
                    'sub_subcategory_id' => $subSubcategoryId === '' ? null : $subSubcategoryId,
                ];
            }
            return $rows;
        };

        $dedupeIncomingProducts = static function (array $products): array {
            $byPrimaryKey = [];

            foreach ($products as $product) {
                $signature = andison_product_semantic_key($product);
                $id = isset($product['id']) ? (int)$product['id'] : 0;
                $primaryKey = $signature !== '' ? ('mk:' . $signature) : ($id > 0 ? ('id:' . $id) : ('fk:' . md5(json_encode($product))));

                if (!isset($byPrimaryKey[$primaryKey])) {
                    $byPrimaryKey[$primaryKey] = $product;
                    continue;
                }

                $existingScore = andison_product_record_score($byPrimaryKey[$primaryKey]);
                $candidateScore = andison_product_record_score($product);
                if ($candidateScore >= $existingScore) {
                    $byPrimaryKey[$primaryKey] = $product;
                }
            }

            return array_values($byPrimaryKey);
        };

        $insertWithFallback = static function (array $rows): bool {
            if (empty($rows)) {
                error_log("insertWithFallback: No rows to insert");
                return true;
            }

            error_log("insertWithFallback: Attempting to insert " . count($rows) . " rows. First row fields: " . json_encode(array_keys($rows[0])));

            $ok = andison_sb_insert('products', $rows);
            if ($ok) {
                error_log("insertWithFallback: Insert succeeded on attempt 1");
                return true;
            }
            error_log("insertWithFallback: Attempt 1 failed, trying without 'images' field");

            $noImgRows = array_map(static function (array $r): array {
                unset($r['images']);
                return $r;
            }, $rows);
            $ok = andison_sb_insert('products', $noImgRows);
            if ($ok) {
                error_log("insertWithFallback: Insert succeeded on attempt 2 (no images)");
                return true;
            }
            error_log("insertWithFallback: Attempt 2 failed, trying without 'sub_subcategory_id'");

            $noSubSubRows = array_map(static function (array $r): array {
                unset($r['sub_subcategory_id']);
                return $r;
            }, $rows);
            $ok = andison_sb_insert('products', $noSubSubRows);
            if ($ok) {
                error_log("insertWithFallback: Insert succeeded on attempt 3 (no sub_subcategory_id)");
                return true;
            }
            error_log("insertWithFallback: Attempt 3 failed, trying without both");

            $noImgNoSubSubRows = array_map(static function (array $r): array {
                unset($r['images'], $r['sub_subcategory_id']);
                return $r;
            }, $rows);
            $ok = andison_sb_insert('products', $noImgNoSubSubRows);
            if ($ok) {
                error_log("insertWithFallback: Insert succeeded on attempt 4");
                return true;
            }
            error_log("insertWithFallback: Attempt 4 failed, trying stripped");

            $strippedRows = array_map(static function (array $r): array {
                return array_intersect_key($r, array_flip([
                    'brand', 'product_name', 'model', 'type', 'badge',
                    'description', 'specifications', 'price', 'image', 'datasheet',
                    'category_id', 'subcategory_id', 'sub_subcategory_id',
                ]));
            }, $rows);
            $ok = andison_sb_insert('products', $strippedRows);
            if ($ok) {
                error_log("insertWithFallback: Insert succeeded on attempt 5 (stripped)");
                return true;
            }
            error_log("insertWithFallback: Attempt 5 failed, trying legacy");

            $legacyStrippedRows = array_map(static function (array $r): array {
                unset($r['sub_subcategory_id']);
                return $r;
            }, $strippedRows);

            $ok = andison_sb_insert('products', $legacyStrippedRows);
            if ($ok) {
                error_log("insertWithFallback: Insert succeeded on attempt 6 (legacy)");
                return true;
            }
            error_log("insertWithFallback: ALL INSERT ATTEMPTS FAILED!");
            return false;
        };

        try {
            // ── 1. Save brand row (update if exists, insert if new) ──────────────
            $existing = andison_sb_select('brands', 'select=id,name,description&name=eq.' . rawurlencode($name) . '&limit=1');
            $incomingLogo = trim((string)($data['logo'] ?? ''));
            if (!empty($existing)) {
                $existingMeta = andison_brand_row_unpack((array)$existing[0]);
                $packedDescription = andison_brand_row_pack(
                    (string)($data['description'] ?? ''),
                    $incomingLogo !== '' ? $incomingLogo : (string)($existingMeta['logo'] ?? '')
                );
                andison_sb_update('brands', ['description' => $packedDescription], 'name=eq.' . rawurlencode($name));
            } else {
                $packedDescription = andison_brand_row_pack(
                    (string)($data['description'] ?? ''),
                    $incomingLogo
                );
                andison_sb_insert('brands', [['name' => $name, 'description' => $packedDescription]]);
            }

            $existingProductRows = andison_sb_select('products', 'brand=ilike.' . rawurlencode($name) . '&limit=10000');

            $normalizeBrand = static function (string $value): string {
                $value = strtolower(trim($value));
                return preg_replace('/\s+/', ' ', $value) ?? $value;
            };

            $allRowsForBrandScan = andison_sb_select('products', 'select=id,brand,product_name,model,type,category_id,subcategory_id,sub_subcategory_id&limit=10000');
            $targetBrandKey = $normalizeBrand($name);
            $normalizedBrandRows = [];
            if (is_array($allRowsForBrandScan)) {
                foreach ($allRowsForBrandScan as $scanRow) {
                    $scanBrand = $normalizeBrand((string)($scanRow['brand'] ?? ''));
                    if ($scanBrand !== '' && $scanBrand === $targetBrandKey) {
                        $normalizedBrandRows[] = $scanRow;
                    }
                }
            }

            if (!empty($normalizedBrandRows)) {
                $existingProductRows = array_merge($existingProductRows, $normalizedBrandRows);
            }
            $incomingProducts = is_array($data['products'] ?? null) ? $data['products'] : [];
            $incomingProducts = $dedupeIncomingProducts($incomingProducts);

            $existingSemanticCount = count($dedupeIncomingProducts($existingProductRows));
            $incomingSemanticCount = count($incomingProducts);

            // Guard against stale/partial payloads that could wipe products.
            if ((!$allowProductCountDecrease && $incomingSemanticCount < $existingSemanticCount) ||
                (!$allowEmptyProducts && empty($incomingProducts) && !empty($existingProductRows))) {
                return false;
            }

            // ── 2. Replace this brand's products: delete all then re-insert ───────
            // Delete by ilike and by explicit IDs to also clear legacy rows with inconsistent brand formatting.
            andison_sb_delete('products', 'brand=ilike.' . rawurlencode($name));

            $existingIds = array_values(array_unique(array_filter(array_map(static function (array $row): ?int {
                return isset($row['id']) ? (int)$row['id'] : null;
            }, $existingProductRows))));

            if (!empty($existingIds)) {
                foreach (array_chunk($existingIds, 200) as $idChunk) {
                    if (!empty($idChunk)) {
                        andison_sb_delete('products', 'id=in.(' . implode(',', $idChunk) . ')');
                    }
                }
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

if (!function_exists('andison_create_brand')) {
    function andison_create_brand(string $name, string $description = '', string $logoUrl = ''): bool
    {
        $name = trim(andison_canonical_brand_name($name));
        $description = trim($description);
        $logoUrl = trim($logoUrl);
        if ($name === '') {
            return false;
        }

        $existing = andison_sb_select('brands', 'select=id,name,description&name=eq.' . rawurlencode($name) . '&limit=1');
        if (!empty($existing[0])) {
            if ($description !== '' || $logoUrl !== '') {
                $existingMeta = andison_brand_row_unpack((array)$existing[0]);
                $packedDescription = andison_brand_row_pack(
                    $description !== '' ? $description : (string)($existingMeta['description'] ?? ''),
                    $logoUrl !== '' ? $logoUrl : (string)($existingMeta['logo'] ?? '')
                );
                andison_sb_update('brands', ['description' => $packedDescription], 'name=eq.' . rawurlencode($name));
            }
            @unlink(dirname(__DIR__) . '/data/_cache/brands_full.cache');
            return true;
        }

        $packedDescription = andison_brand_row_pack($description, $logoUrl);

        $ok = andison_sb_insert('brands', [[
            'name' => $name,
            'description' => $packedDescription,
        ]]);

        if ($ok) {
            @unlink(dirname(__DIR__) . '/data/_cache/brands_full.cache');
        }

        return $ok;
    }
}

if (!function_exists('andison_delete_brand')) {
    function andison_delete_brand(string $name): bool
    {
        $name = trim($name);
        if ($name === '') {
            return false;
        }

        // Remove linked products first so this brand cannot be surfaced from products fallback.
        $productsDeleted = andison_sb_delete('products', 'brand=ilike.' . rawurlencode($name));
        $brandDeleted = andison_sb_delete('brands', 'name=eq.' . rawurlencode($name));

        // Consider success if either side deleted rows; both calls are idempotent.
        $ok = $productsDeleted || $brandDeleted;
        if ($ok) {
            @unlink(dirname(__DIR__) . '/data/_cache/brands_full.cache');
        }

        return $ok;
    }
}



