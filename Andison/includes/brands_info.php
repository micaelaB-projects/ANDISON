<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';

if (!function_exists('andison_get_brands_info')) {
    function andison_get_brands_info(): array
    {
        $dataFile = dirname(__DIR__) . '/data/brands_info.json';
        $loaded = andison_read_json_file($dataFile, []);
        if (!empty($loaded) && is_array($loaded)) {
            return $loaded;
        }

        // No fallback - always use JSON data or empty array
        return [];
    }
}
if (!function_exists('andison_save_brands_info')) {
    function andison_save_brands_info(array $brands): bool
    {
        $dataFile = dirname(__DIR__) . '/data/brands_info.json';
        return andison_write_json_file($dataFile, $brands);
    }
}


