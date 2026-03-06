<?php
require_once __DIR__ . '/Andison/includes/brands_info.php';
$b = andison_get_brands_info();
echo "=== BRAND KEYS ===\n";
foreach (array_keys($b) as $k) echo $k . "\n";

echo "\n=== MOTOLITE PRODUCTS (image paths) ===\n";
$brandKey = null;
foreach (array_keys($b) as $k) {
    if (strcasecmp($k, 'MOTOLITE') === 0) { $brandKey = $k; break; }
}
if ($brandKey) {
    foreach ($b[$brandKey]['products'] ?? [] as $p) {
        echo "image=[" . ($p['image'] ?? '') . "]\n";
    }
} else {
    echo "MOTOLITE not found\n";
}

echo "\n=== UPLOAD DIR EXISTS? ===\n";
$dir = __DIR__ . '/Andison/assets/uploads/products';
echo $dir . " => " . (is_dir($dir) ? "YES" : "NO") . "\n";
$dir2 = __DIR__ . '/assets/uploads/products';
echo $dir2 . " => " . (is_dir($dir2) ? "YES" : "NO") . "\n";
