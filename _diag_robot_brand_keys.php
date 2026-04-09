<?php
require __DIR__ . '/includes/brands_info.php';
$all = andison_get_brands_info(true);
$keys = array_keys($all);
sort($keys, SORT_NATURAL|SORT_FLAG_CASE);
foreach ($keys as $k) {
  if (stripos($k, 'robot') !== false) {
    echo $k . ' => ' . count($all[$k]['products'] ?? []) . PHP_EOL;
  }
}
?>
