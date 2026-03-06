<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_get_home_slider')) {
    function andison_get_home_slider(): array
    {
        $defaults = [
            'assets/HOME/photo_2026-02-02_14-29-26 (1).jpg',
            'assets/HOME/photo_2026-02-02_14-29-26 (2).jpg',
            'assets/HOME/photo_2026-02-02_14-29-26 (3).jpg',
            'assets/HOME/photo_2026-02-02_14-29-26 (4).jpg',
        ];

        // Try Supabase first
        $rows = andison_sb_select('home_slider', 'order=sort_order');
        if (!empty($rows)) {
            $out = [];
            for ($i = 0; $i < 4; $i++) {
                $url = $rows[$i]['image_url'] ?? '';
                $out[$i] = (is_string($url) && trim($url) !== '') ? trim($url) : ($defaults[$i] ?? '');
            }
            return $out;
        }

        // Fallback to local JSON
        $file = dirname(__DIR__) . '/data/home_slider.json';
        $loaded = andison_read_json_file($file, $defaults);
        if (!is_array($loaded)) {
            return $defaults;
        }

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
        $out = [];
        for ($i = 0; $i < 4; $i++) {
            $val = isset($slides[$i]) ? trim((string)$slides[$i]) : '';
            $out[$i] = $val;
        }

        // Backup to local JSON
        $file = dirname(__DIR__) . '/data/home_slider.json';
        andison_write_json_file($file, $out);

        // Save to Supabase
        andison_sb_truncate('home_slider');
        $rows = [];
        foreach ($out as $i => $url) {
            $rows[] = ['image_url' => $url, 'sort_order' => $i];
        }
        return andison_sb_insert('home_slider', $rows);
    }
}



