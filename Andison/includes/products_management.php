<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

function andison_get_products_for_subcategory(string $categoryId, string $subcategoryId): array
{
    $filter = 'category_id=eq.' . rawurlencode($categoryId)
            . '&subcategory_id=eq.' . rawurlencode($subcategoryId)
            . '&limit=1000';
    $rows = andison_sb_select('products', $filter);

    if (!empty($rows)) {
        // Normalize Supabase column names to match local JSON schema
        return array_map(function (array $row): array {
            if (!isset($row['name']) && isset($row['product_name'])) {
                $row['name'] = $row['product_name'];
            }
            if (!isset($row['specs']) && isset($row['specifications'])) {
                $row['specs'] = $row['specifications'];
            }
            // Decode images JSON string from Supabase into a PHP array
            if (isset($row['images']) && is_string($row['images'])) {
                $decoded = json_decode($row['images'], true);
                if (is_array($decoded)) {
                    $row['images'] = $decoded;
                    // Ensure image (single) reflects first element
                    if (empty($row['image']) && !empty($decoded[0])) {
                        $row['image'] = $decoded[0];
                    }
                } else {
                    $row['images'] = $row['image'] ? [$row['image']] : [];
                }
            } elseif (!isset($row['images'])) {
                $row['images'] = $row['image'] ? [$row['image']] : [];
            }
            return $row;
        }, $rows);
    }

    // Fallback to local JSON
    $jsonFile = __DIR__ . '/../data/products/' . urlencode($categoryId) . '/' . urlencode($subcategoryId) . '.json';
    if (!file_exists($jsonFile)) return [];
    $content = file_get_contents($jsonFile);
    if ($content === false) return [];
    $products = json_decode($content, true);
    return is_array($products) ? $products : [];
}

function andison_get_products_for_category(string $categoryId): array
{
    // Query Supabase for all products under this category (no subcategory filter)
    $filter = 'category_id=eq.' . rawurlencode($categoryId) . '&limit=5000';
    $rows = andison_sb_select('products', $filter);

    if (!empty($rows)) {
        return array_map(function (array $row): array {
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
            return $row;
        }, $rows);
    }

    // Fallback: scan all JSON files in the category directory
    $dir = __DIR__ . '/../data/products/' . urlencode($categoryId);
    if (!is_dir($dir)) return [];
    $all = [];
    foreach (glob($dir . '/*.json') as $file) {
        $content = file_get_contents($file);
        if ($content === false) continue;
        $products = json_decode($content, true);
        if (is_array($products)) {
            $all = array_merge($all, $products);
        }
    }
    return $all;
}

function andison_save_products_for_subcategory(string $categoryId, string $subcategoryId, array $products): bool
{
    // Backup to local JSON
    $dir = __DIR__ . '/../data/products/' . urlencode($categoryId);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $jsonFile = $dir . '/' . urlencode($subcategoryId) . '.json';
    $handle = fopen($jsonFile, 'c');
    if ($handle !== false) {
        if (flock($handle, LOCK_EX)) {
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            flock($handle, LOCK_UN);
        }
        fclose($handle);
    }

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
    $products = andison_get_products_for_subcategory($categoryId, $subcategoryId);
    
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
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
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



