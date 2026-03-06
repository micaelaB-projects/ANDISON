<?php
require_once __DIR__ . '/Andison/includes/supabase.php';

// Fix image paths that start with "../assets/uploads/products/" 
// Change them to "Andison/assets/uploads/products/"
$products = andison_sb_select('products', 'select=id,image&limit=10000');
$fixed = 0;
foreach ($products as $p) {
    $img = $p['image'] ?? '';
    if (strpos($img, '../assets/uploads/products/') === 0) {
        $newImg = 'Andison/assets/uploads/products/' . basename($img);
        andison_sb_update('products', ['image' => $newImg], 'id=eq.' . (int)$p['id']);
        echo "Fixed id={$p['id']}: $img => $newImg\n";
        $fixed++;
    }
}
echo "\nTotal fixed: $fixed\n";
