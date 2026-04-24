<?php
/**
 * Diagnostic script for BW brand save/preview issues
 */
declare(strict_types=1);

require_once __DIR__ . '/Andison/includes/brands_info.php';
require_once __DIR__ . '/Andison/includes/supabase.php';

// Check BW brand in database
echo "<h2>🔍 BW Brand Diagnostic</h2>\n";

// 1. Check what's stored in Supabase
echo "<h3>1. Database Record:</h3>\n";
$bwRecords = andison_sb_select('brands', 'select=id,name,description&name=eq.BW');
if (!empty($bwRecords)) {
    echo "<pre>" . htmlspecialchars(json_encode($bwRecords, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . "</pre>";
    $bwRecord = $bwRecords[0];
    
    echo "<h3>2. Unpacking Description Field:</h3>\n";
    $unpacked = andison_brand_row_unpack($bwRecord);
    echo "<pre>" . htmlspecialchars(json_encode($unpacked, JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p style='color:red;'>❌ NO BW BRAND FOUND IN DATABASE</p>";
}

// 2. Check brands_info cache and fetch
echo "<h3>3. Brands Info (with forced refresh):</h3>\n";
$brandsInfo = andison_get_brands_info(true);
if (isset($brandsInfo['BW'])) {
    echo "<pre>" . htmlspecialchars(json_encode([
        'description' => substr($brandsInfo['BW']['description'], 0, 100) . '...',
        'logo' => $brandsInfo['BW']['logo'],
        'product_count' => count($brandsInfo['BW']['products'] ?? [])
    ], JSON_PRETTY_PRINT)) . "</pre>";
} else {
    echo "<p style='color:red;'>❌ BW NOT IN BRANDS_INFO RESULT</p>";
    echo "<p>Available brands: " . htmlspecialchars(implode(', ', array_keys($brandsInfo))) . "</p>";
}

// 3. Check cache file
echo "<h3>4. Cache File Status:</h3>\n";
$cacheFile = __DIR__ . '/data/_cache/brands_full.cache';
if (file_exists($cacheFile)) {
    echo "<p>✓ Cache exists: " . date('Y-m-d H:i:s', filemtime($cacheFile)) . "</p>";
    echo "<p><a href='?action=clear_cache' style='padding:8px 12px; background:#ff6b6b; color:white; text-decoration:none; border-radius:4px;'>Clear Cache</a></p>";
} else {
    echo "<p>⚠ Cache does not exist (will be created on next request)</p>";
}

// Handle cache clear action
if (isset($_GET['action']) && $_GET['action'] === 'clear_cache') {
    @unlink($cacheFile);
    echo "<p style='color:green;'>✓ Cache cleared!</p>";
}

// 4. Check BW "BW Technologies" variants
echo "<h3>5. Check For Variant Names:</h3>\n";
$variants = ['BW', 'BW Technologies', 'BW TECHNOLOGIES'];
foreach ($variants as $variant) {
    $records = andison_sb_select('brands', 'select=id,name&name=ilike.' . rawurlencode($variant));
    if (!empty($records)) {
        echo "<p>✓ Found as: <strong>" . htmlspecialchars($records[0]['name']) . "</strong></p>";
    }
}

// 5. Test the pack/unpack functions
echo "<h3>6. Pack/Unpack Test:</h3>\n";
$testDesc = "This is a test description for BW";
$testLogo = "https://example.com/bw-logo.png";
$packed = andison_brand_row_pack($testDesc, $testLogo);
echo "<p><strong>Packed:</strong> " . htmlspecialchars(substr($packed, 0, 100)) . "...</p>";
$unpacked = andison_brand_row_unpack(['description' => $packed]);
echo "<p><strong>Unpacked description:</strong> " . htmlspecialchars($unpacked['description']) . "</p>";
echo "<p><strong>Unpacked logo:</strong> " . htmlspecialchars($unpacked['logo']) . "</p>";

?>
<style>
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 20px; background: #f5f5f5; }
h2 { color: #2b11db; border-bottom: 2px solid #2b11db; padding-bottom: 10px; }
h3 { color: #444; margin-top: 20px; }
pre { background: #f0f0f0; padding: 10px; border-radius: 4px; overflow-x: auto; }
p { line-height: 1.6; }
</style>
