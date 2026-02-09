<?php

declare(strict_types=1);

if (!function_exists('andison_read_json_file')) {
    function andison_read_json_file(string $path, array $default = []): array
    {
        if (!is_file($path)) {
            return $default;
        }

        $raw = @file_get_contents($path);
        if ($raw === false) {
            return $default;
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : $default;
    }
}

if (!function_exists('andison_write_json_file')) {
    function andison_write_json_file(string $path, array $data): bool
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return false;
        }

        return file_put_contents($path, $json, LOCK_EX) !== false;
    }
}
