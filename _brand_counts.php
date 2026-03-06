<?php
require_once __DIR__ . '/Andison/includes/supabase.php';
$products = andison_sb_select('products', 'select=brand&limit=10000');
$brandCounts = [];
foreach($products as $p){
    $b = trim($p['brand'] ?? '(none)');
    if($b === '') $b = '(none)';
    $brandCounts[$b] = ($brandCounts[$b] ?? 0) + 1;
}
header('Content-Type: text/plain');
arsort($brandCounts);
foreach($brandCounts as $b => $c) echo str_pad($c,5).' '.$b.PHP_EOL;
echo PHP_EOL.'Total products: '.count($products).PHP_EOL;
