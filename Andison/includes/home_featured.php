<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_get_home_featured')) {
    function andison_get_home_featured(): array
    {
        $defaults = [
            'badge' => 'FEATURED',
            'title' => 'New Generation Industrial Drills Launched!',
            'description' => 'Discover our latest advancements in drilling technology offering unparalleled precision and durability for all heavy-duty applications. Explore the future of industrial performance.',
            'button_text' => 'Read More',
            'button_url' => '',
            'event_date' => '',
            'event_location' => '',
            'discount' => '',
            'offer_text' => '',
            'media_type' => 'picture',
            'image' => 'assets/Slider 2 - Panasonic Manual Welding Machines.jpg',
            'image_alt' => 'Featured Industrial Drill',
            'youtube_url' => '',
            'video_file' => '',
        ];

        // Try Supabase first
        $sbRows = andison_sb_select('home_featured', 'limit=1');
        $loaded = !empty($sbRows[0]) ? $sbRows[0] : null;

        if ($loaded === null) {
            // Fallback to local JSON
            $file = dirname(__DIR__) . '/data/home_featured.json';
            $loaded = andison_read_json_file($file, []);
            if (!is_array($loaded)) {
                return $defaults;
            }
        }

        $out = $defaults;
        foreach ($defaults as $key => $defaultVal) {
            if (!array_key_exists($key, $loaded)) {
                continue;
            }
            $val = $loaded[$key];
            if (is_string($defaultVal)) {
                $out[$key] = trim((string)$val);
            }
        }

        // Safety: if image path is empty, fall back.
        if (($out['image'] ?? '') === '') {
            $out['image'] = $defaults['image'];
        }
        if (($out['image_alt'] ?? '') === '') {
            $out['image_alt'] = $defaults['image_alt'];
        }

        return $out;
    }
}

if (!function_exists('andison_save_home_featured')) {
    function andison_save_home_featured(array $data): bool
    {
        $allowed = [
            'badge',
            'title',
            'description',
            'button_text',
            'button_url',
            'event_date',
            'event_location',
            'discount',
            'offer_text',
            'media_type',
            'image',
            'image_alt',
            'youtube_url',
            'video_file',
        ];

        $out = [];
        foreach ($allowed as $key) {
            $out[$key] = trim((string)($data[$key] ?? ''));
        }

        // Backup to local JSON
        $file = dirname(__DIR__) . '/data/home_featured.json';
        andison_write_json_file($file, $out);

        // Save to Supabase
        andison_sb_truncate('home_featured');
        return andison_sb_insert('home_featured', [$out]);
    }
}



