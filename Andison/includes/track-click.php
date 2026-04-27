<?php
declare(strict_types=1);

// Simple click tracking endpoint - no auth required for public website
require_once __DIR__ . '/analytics.php';

$clickType = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
$target = isset($_GET['target']) ? trim((string)$_GET['target']) : '';
$category = isset($_GET['category']) ? trim((string)$_GET['category']) : '';

if ($clickType && $target) {
    andison_track_click($clickType, $target, $category);
}

// Send minimal response
header('Content-Type: application/json');
echo json_encode(['tracked' => true]);
exit;
?>
