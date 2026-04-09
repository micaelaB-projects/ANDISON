<?php
declare(strict_types=1);

require_once __DIR__ . '/Andison/includes/brands_info.php';

// Force fresh load from Supabase
$cache = andison_get_brands_info(true);

echo "Brand Keys Count: " . count(array_keys($cache)) . "\n";
echo "Brand Keys:\n";
$keys = array_keys($cache);
sort($keys);
foreach ($keys as $k) {
    echo "  - " . $k . "\n";
}

// Check for duplicates after case-normalization
$lowerKeys = array_map('strtolower', $keys);
$duplicates = array_filter(array_count_values($lowerKeys), function($count) { return $count > 1; });
if (!empty($duplicates)) {
    echo "\n\nCase-Insensitive Duplicates with Product Counts:\n";
    foreach ($duplicates as $dup => $count) {
        echo "  - " . $dup . " appears " . $count . " times\n";
        $originals = array_filter($keys, function($k) use ($dup) { return strtolower($k) === $dup; });
        foreach ($originals as $orig) {
            $productCount = count($cache[$orig]['products'] ?? []);
            echo "    * " . $orig . " - " . $productCount . " product(s)\n";
        }
    }
} else {
    echo "\n✓ No duplicates found!\n";
}
?>
