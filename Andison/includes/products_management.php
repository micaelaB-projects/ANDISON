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
        $rows[] = array_merge($product, [
            'category_id'    => $categoryId,
            'subcategory_id' => $subcategoryId,
        ]);
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
    
    $targetDir = __DIR__ . '/../assets/uploads/products/' . urlencode($categoryId) . '/' . urlencode($subcategoryId);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $uniqId = bin2hex(random_bytes(4));
    $timestamp = date('YmdHis');
    $name = preg_replace('~[^a-z0-9._-]+~', '_', strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_FILENAME))) ?? 'image';
    $filename = $name . '_' . $timestamp . '_' . $uniqId . '.' . $ext;
    $path = $targetDir . '/' . $filename;
    
    if (!move_uploaded_file((string)($f['tmp_name'] ?? ''), $path)) {
        return null;
    }
    
    return 'andison/assets/uploads/products/' . urlencode($categoryId) . '/' . urlencode($subcategoryId) . '/' . $filename;
}



