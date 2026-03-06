<?php
require_once __DIR__ . '/Andison/includes/supabase.php';

// Show first 20 rows of products table so we can see what brand/category fields contain
$products = andison_sb_select('products', 'select=id,brand,product_name,model,category_id,subcategory_id&limit=30&order=id.asc');
$brands   = andison_sb_select('brands', 'select=id,name,description&order=name');

header('Content-Type: text/plain');
echo "=== BRANDS TABLE ===\n";
if (empty($brands)) {
    echo "(empty)\n";
} else {
    foreach ($brands as $b) {
        echo "id={$b['id']}  name={$b['name']}\n";
    }
}

echo "\n=== PRODUCTS TABLE (first 30 rows) ===\n";
echo str_pad('id',6) . str_pad('brand',20) . str_pad('category_id',25) . str_pad('subcategory_id',28) . "product_name\n";
echo str_repeat('-', 100) . "\n";
if (empty($products)) {
    echo "(empty)\n";
} else {
    foreach ($products as $p) {
        echo str_pad((string)($p['id'] ?? ''),6)
           . str_pad((string)($p['brand'] ?? 'NULL'),20)
           . str_pad((string)($p['category_id'] ?? 'NULL'),25)
           . str_pad((string)($p['subcategory_id'] ?? 'NULL'),28)
           . ($p['product_name'] ?? '') . "\n";
    }
}

echo "\n=== TOTAL COUNTS ===\n";
$total = andison_sb_select('products', 'select=id&limit=10000');
echo "Total products in DB: " . count($total) . "\n";
$withBrand = array_filter($total, fn($r) => !empty($r['id'])); // just counting
echo "Brands in brands table: " . count($brands) . "\n";
