<?php
// brand_logo_resolver.php
// Usage: ?brand=Brand%20Name
include_once __DIR__ . '/brand_logo_map.php';
$brand = isset($_GET['brand']) ? urldecode($_GET['brand']) : '';
$brand = trim($brand);
if (isset($brand_logo_map[$brand])) {
    echo $brand_logo_map[$brand];
} else {
    http_response_code(404);
    echo '';
}
