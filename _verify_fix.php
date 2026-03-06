<?php
require_once __DIR__ . '/includes/brands_info.php';
$data = andison_get_brands_info();
header('Content-Type: text/plain');
$total = 0;
foreach($data as $brand => $info) {
    $count = count($info['products'] ?? []);
    $total += $count;
    if($count > 0) echo str_pad($count, 5) . ' ' . $brand . PHP_EOL;
}
echo PHP_EOL.'Brands with products: ' . count(array_filter($data, fn($b) => !empty($b['products']))) . PHP_EOL;
echo 'Total brands: ' . count($data) . PHP_EOL;
echo 'Total visible products: ' . $total . PHP_EOL;
