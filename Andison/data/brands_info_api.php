<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/brands_info.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');

echo json_encode(andison_get_brands_info(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
