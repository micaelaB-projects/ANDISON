<?php
declare(strict_types=1);

require_once __DIR__ . '/Andison/includes/supabase.php';

// Test if we can delete a non-existent product (should succeed silently)
echo "Testing Supabase delete function...\n\n";

$testBrandFilter = 'brand=eq.ACES';
echo "Filter: " . $testBrandFilter . "\n";

// Try to count existing ACES products
$existing = andison_sb_select('products', 'brand=eq.ACES&limit=1000');
echo "Existing ACES products: " . count($existing) . "\n\n";

// Test delete with a dummy non-existent row
echo "Testing delete with non-existent id...\n";
$deleteResult = andison_sb_delete('products', 'id=eq.99999999');
echo "Delete result: " . ($deleteResult ? 'SUCCESS' : 'FAILED') . "\n\n";

// Show last request details
echo "Check /xampp/logs/apache_error.log for detailed error messages.\n";
?>
