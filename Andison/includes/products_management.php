<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

function andison_normalize_product_row(array $row): array
{
    if (!isset($row['name']) && isset($row['product_name'])) {
        $row['name'] = $row['product_name'];
    }
    if (!isset($row['specs']) && isset($row['specifications'])) {
        $row['specs'] = $row['specifications'];
    }
    if (isset($row['images']) && is_string($row['images'])) {
        $decoded = json_decode($row['images'], true);
        if (is_array($decoded)) {
            $row['images'] = $decoded;
            if (empty($row['image']) && !empty($decoded[0])) {
                $row['image'] = $decoded[0];
            }
        } else {
            $row['images'] = $row['image'] ? [$row['image']] : [];
        }
    } elseif (!isset($row['images'])) {
        $row['images'] = $row['image'] ? [$row['image']] : [];
    }

    $row['category_id'] = trim((string)($row['category_id'] ?? ''));
    $row['subcategory_id'] = trim((string)($row['subcategory_id'] ?? ''));
    $row['sub_subcategory_id'] = trim((string)($row['sub_subcategory_id'] ?? ''));

    return $row;
}

if (!function_exists('andison_canonical_brand_for_dedupe')) {
    function andison_canonical_brand_for_dedupe(string $brand): string
    {
        $normalized = strtolower(trim($brand));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        static $aliases = [
            'phoenix dry rod' => 'dryrod. ii',
            'phoenix dryrod' => 'dryrod. ii',
            'dryrod ii' => 'dryrod. ii',
            'dryrod. ii' => 'dryrod. ii',
            'sk' => 'sk and gal gage',
            'gal gage' => 'sk and gal gage',
            'sk and gal gage' => 'sk and gal gage',
            'hard worker' => 'hardworker',
            'hard workers' => 'hardworker',
            'hardworker' => 'hardworker',
            'rae' => 'rae systems',
            'rac' => 'rae systems',
            'rae systems' => 'rae systems',
            'robot systems' => 'robot systems peripherals',
            'robot system peripherals' => 'robot systems peripherals',
            'robot systems peripherals' => 'robot systems peripherals',
            'weller' => 'weiler',
            'weiler' => 'weiler',
        ];

        return $aliases[$normalized] ?? $normalized;
    }
}

function andison_get_products_for_subcategory(string $categoryId, string $subcategoryId, int $limit = 0): array
{
    $rows = andison_sb_select(
        'products',
        'category_id=eq.' . rawurlencode($categoryId) . '&order=id.asc&limit=10000'
    );

    if (empty($rows)) {
        return [];
    }

    $subRows = andison_sb_select(
        'subcategories',
        'select=id&category_id=eq.' . rawurlencode($categoryId) . '&limit=1000'
    );
    $allowedParentSubIds = [];
    foreach ($subRows as $subRow) {
        $subRowId = trim((string)($subRow['id'] ?? ''));
        if ($subRowId !== '') {
            $allowedParentSubIds[$subRowId] = true;
        }
    }

    $subSubRows = andison_sb_select(
        'sub_subcategories',
        'select=id,subcategory_id&limit=5000'
    );

    $subSubParentMap = [];
    $childrenByParent = [];
    foreach ($subSubRows as $subSub) {
        $subSubId = trim((string)($subSub['id'] ?? ''));
        $parentSubId = trim((string)($subSub['subcategory_id'] ?? ''));
        if ($subSubId === '' || $parentSubId === '') {
            continue;
        }
        if (!empty($allowedParentSubIds) && !isset($allowedParentSubIds[$parentSubId])) {
            continue;
        }
        $subSubParentMap[$subSubId] = $parentSubId;
        $childrenByParent[$parentSubId][] = $subSubId;
    }

    $isSpecificSubSub = isset($subSubParentMap[$subcategoryId]);
    $targetChildIds = $childrenByParent[$subcategoryId] ?? [];

    $filtered = [];
    foreach ($rows as $row) {
        $storedSubId = trim((string)($row['subcategory_id'] ?? ''));
        $storedSubSubId = trim((string)($row['sub_subcategory_id'] ?? ''));

        // Backward compatibility: legacy rows may have saved sub-subcategory in subcategory_id.
        if ($storedSubSubId === '' && $storedSubId !== '' && isset($subSubParentMap[$storedSubId])) {
            $storedSubSubId = $storedSubId;
            $storedSubId = $subSubParentMap[$storedSubId];
        }

        $include = false;
        if ($isSpecificSubSub) {
            // Sub-subcategory page: include exact normalized match and legacy direct assignment.
            $include = ($storedSubSubId === $subcategoryId)
                || (trim((string)($row['subcategory_id'] ?? '')) === $subcategoryId);
        } else {
            // Parent subcategory page: include direct assignments and nested sub-subcategory assignments.
            $include = ($storedSubId === $subcategoryId)
                || in_array($storedSubSubId, $targetChildIds, true)
                || in_array(trim((string)($row['subcategory_id'] ?? '')), $targetChildIds, true);
        }

        if (!$include) {
            continue;
        }

        $row['subcategory_id'] = $storedSubId;
        if ($storedSubSubId !== '') {
            $row['sub_subcategory_id'] = $storedSubSubId;
        }
        $filtered[] = $row;
    }

    $deduped = [];
    foreach ($filtered as $row) {
        $key = isset($row['id'])
            ? 'id:' . (string)$row['id']
            : 'mk:' . strtolower(trim((string)($row['model'] ?? ''))) . '::' . strtolower(trim((string)($row['subcategory_id'] ?? ''))) . '::' . strtolower(trim((string)($row['sub_subcategory_id'] ?? '')));
        $deduped[$key] = $row;
    }

    $normalized = array_map('andison_normalize_product_row', array_values($deduped));
    if ($limit > 0) {
        return array_slice($normalized, 0, $limit);
    }
    return $normalized;
}

function andison_get_products_for_category(string $categoryId, int $limit = 0): array
{
    // Query Supabase for all products under this category (no subcategory filter)
    $filter = 'category_id=eq.' . rawurlencode($categoryId) . '&order=id.asc&limit=5000';
    $rows = andison_sb_select('products', $filter);

    if (!empty($rows)) {
        $scoreRow = static function (array $row): int {
            $score = 0;
            $specifications = trim((string)($row['specifications'] ?? ($row['specs'] ?? '')));
            if ($specifications !== '') {
                $score += 120;
                if (str_contains($specifications, 'andison_specs_v2') || str_contains($specifications, 'andison_specs_v3')) {
                    $score += 120;
                }
            }

            $description = trim((string)($row['description'] ?? ''));
            if ($description !== '') {
                $score += 30;
            }

            $datasheet = trim((string)($row['datasheet'] ?? ''));
            if ($datasheet !== '') {
                $score += 20;
            }

            $images = [];
            if (isset($row['images']) && is_string($row['images'])) {
                $decoded = json_decode($row['images'], true);
                $images = is_array($decoded) ? $decoded : [];
            } elseif (isset($row['images']) && is_array($row['images'])) {
                $images = $row['images'];
            } elseif (!empty($row['image'])) {
                $images = [$row['image']];
            }
            $score += min(5, count($images)) * 5;

            $id = isset($row['id']) ? (int)$row['id'] : 0;
            if ($id > 0) {
                $score += min(1000, $id);
            }

            return $score;
        };

        $deduped = [];
        foreach ($rows as $row) {
            $normalize = static function (string $value): string {
                $value = strtolower(trim($value));
                return preg_replace('/\s+/', ' ', $value) ?? $value;
            };

            $brandRaw = $normalize((string)($row['brand'] ?? ''));
            $brand = andison_canonical_brand_for_dedupe($brandRaw);
            $model = $normalize((string)($row['model'] ?? ''));
            $name = $normalize((string)($row['product_name'] ?? ($row['name'] ?? '')));
            $type = $normalize((string)($row['type'] ?? ''));

            $semanticKey = $model !== ''
                ? implode('::', [$brand, $model])
                : implode('::', [$brand, $name, $type]);
            $idKey = isset($row['id']) ? 'id:' . (string)$row['id'] : '';
            $key = $semanticKey !== '' ? ('mk:' . $semanticKey) : ($idKey !== '' ? $idKey : ('mk:fallback:' . md5(json_encode($row))));

            if (!isset($deduped[$key])) {
                $deduped[$key] = $row;
                continue;
            }

            $existingScore = $scoreRow($deduped[$key]);
            $candidateScore = $scoreRow($row);
            if ($candidateScore >= $existingScore) {
                $deduped[$key] = $row;
            }
        }

        $normalized = array_map('andison_normalize_product_row', array_values($deduped));
        if ($limit > 0) {
            return array_slice($normalized, 0, $limit);
        }
        return $normalized;
    }

    return [];
}

function andison_save_products_for_subcategory(string $categoryId, string $subcategoryId, array $products): bool
{
    // Save to Supabase: delete existing rows then insert fresh ones
    $filter = 'category_id=eq.' . rawurlencode($categoryId) . '&subcategory_id=eq.' . rawurlencode($subcategoryId);
    andison_sb_delete('products', $filter);

    if (empty($products)) return true;

    $rows = [];
    foreach ($products as $product) {
        $row = array_merge($product, [
            'category_id'    => $categoryId,
            'subcategory_id' => $subcategoryId,
        ]);
        // JSON-encode images array for Supabase text column
        // (requires: ALTER TABLE products ADD COLUMN IF NOT EXISTS images text;)
        if (isset($row['images']) && is_array($row['images'])) {
            $row['images'] = json_encode(array_values($row['images']));
        } elseif (!isset($row['images'])) {
            unset($row['images']); // omit if not present — avoids column-not-exist error
        }
        $rows[] = $row;
    }

    return andison_sb_insert('products', $rows);
}

function andison_get_product_by_id(string $categoryId, string $subcategoryId, string $productId): ?array
{
    $products = andison_get_products_for_subcategory($categoryId, $subcategoryId, 0);
    
    foreach ($products as $product) {
        if (($product['id'] ?? '') === $productId) {
            return $product;
        }
    }
    
    return null;
}

function andison_admin_is_product_image_upload(array $f): bool
{
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!isset($f['tmp_name']) || !is_file($f['tmp_name'])) {
        return false;
    }
    return true;
}

function andison_admin_store_product_image(array $f, string $categoryId, string $subcategoryId): ?string
{
    $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'jfif', 'png', 'webp', 'gif', 'avif'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $uniqId    = bin2hex(random_bytes(4));
    $timestamp = date('YmdHis');
    $name      = preg_replace('~[^a-z0-9._-]+~', '_', strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_FILENAME))) ?? 'image';
    $filename  = $name . '_' . $timestamp . '_' . $uniqId . '.' . $ext;

    $storagePath = urlencode($categoryId) . '/' . urlencode($subcategoryId) . '/' . $filename;
    return andison_sb_storage_upload_tmp($f, 'product-images', $storagePath);
}



