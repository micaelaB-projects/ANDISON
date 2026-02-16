<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';

if (!function_exists('andison_get_home_slider')) {
    function andison_get_home_slider(): array
    {
        $defaults = [
            'assets/HOME/photo_2026-02-02_14-29-26 (1).jpg',
            'assets/HOME/photo_2026-02-02_14-29-26 (2).jpg',
            'assets/HOME/photo_2026-02-02_14-29-26 (3).jpg',
            'assets/HOME/photo_2026-02-02_14-29-26 (4).jpg',
        ];

        $file = dirname(__DIR__) . '/data/home_slider.json';
        $loaded = andison_read_json_file($file, $defaults);
        if (!is_array($loaded)) {
            return $defaults;
        }

        // Ensure we always return exactly 4 items (fill missing with defaults)
        $out = [];
        for ($i = 0; $i < 4; $i++) {
            if (isset($loaded[$i]) && is_string($loaded[$i]) && trim($loaded[$i]) !== '') {
                $out[$i] = trim((string)$loaded[$i]);
            } else {
                $out[$i] = $defaults[$i] ?? '';
            }
        }

        return $out;
    }
}

if (!function_exists('andison_save_home_slider')) {
    function andison_save_home_slider(array $slides): bool
    {
        $file = dirname(__DIR__) . '/data/home_slider.json';

        $out = [];
        for ($i = 0; $i < 4; $i++) {
            $val = isset($slides[$i]) ? trim((string)$slides[$i]) : '';
            $out[$i] = $val;
        }

        return andison_write_json_file($file, $out);
    }
}



