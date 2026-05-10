<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_aboutus_settings_defaults')) {
    function andison_aboutus_settings_defaults(): array
    {
        return [
            'company_text' => '<strong>Andison Industrial Sales Inc.</strong> stands as a significant industrial supplier for leading companies across the Philippines. Strategically situated amidst the expansive industrial landscape south of Metro Manila, Andison serves multi-national and export giants within <a class="about-inline-link" href="industries.php">automotive and motorcycle assembly factories, power generation, oil refineries</a>, petrochemical plants, metal fabrications, mining operations, shipyards, and other top contractors.

With specialized knowledge, Andison embraces the evolution of technology and consistently adopts new trends. We offer various solutions to our clientele by providing <span class="about-highlight-pill">high-quality products</span>, technical solutions, comprehensive support, and export services to meet the evolving needs of our clients.

Today, as representatives of various world-class brands, Andison has one of the industry\'s broadest portfolios of products, including <span class="about-highlight-pill">Robotic & Automated Welding Systems, Cutting Machines, Industrial Equipment, Tools & Supplies, Gas Detection Devices, Safety Products, and PPE</span> solutions.',
            'mission_text' => 'To deliver innovative solutions and high-quality products to businesses across the Philippines at cost-effective prices while cultivating lasting relationships with our industrial clients.',
            'vision_text' => 'To be the premier supplier of industrial solutions in the Philippines, contributing significantly to national industrialization and being the trusted partner for manufacturing excellence.',
            'building_image_url' => 'assets/about us/Andison Manila Picture - Edited.jpg',
            'mission_image_url' => 'assets/about us/Welding Machines.JPG',
            'vision_image_url' => 'assets/about us/Welding Robots.JPG',
        ];
    }
}

if (!function_exists('andison_aboutus_settings_decode_payload')) {
    function andison_aboutus_settings_decode_payload(string $payload, array $defaults): array
    {
        $decoded = json_decode(trim($payload), true);
        if (!is_array($decoded)) {
            return $defaults;
        }

        $out = $defaults;
        foreach ($defaults as $key => $defaultValue) {
            if (!array_key_exists($key, $decoded)) {
                continue;
            }
            $out[$key] = trim((string)$decoded[$key]);
        }

        return $out;
    }
}

if (!function_exists('andison_get_aboutus_settings')) {
    function andison_get_aboutus_settings(): array
    {
        $defaults = andison_aboutus_settings_defaults();

        // Primary storage: Supabase (youtube_links section=aboutus_settings)
        $rows = andison_sb_select('youtube_links', 'section=eq.aboutus_settings&order=sort_order.asc&limit=1');
        if (!empty($rows[0]) && is_array($rows[0])) {
            $rawPayload = (string)($rows[0]['url'] ?? '');
            if (trim($rawPayload) !== '') {
                return andison_aboutus_settings_decode_payload($rawPayload, $defaults);
            }
        }

        return $defaults;
    }
}

if (!function_exists('andison_save_aboutus_settings')) {
    function andison_save_aboutus_settings(array $data): bool
    {
        if (!andison_supabase_is_enabled()) {
            error_log('ABOUTUS: Supabase is not enabled. Missing ANDISON_SUPABASE_URL and/or ANDISON_SUPABASE_KEY.');
            return false;
        }
        
        $defaults = andison_aboutus_settings_defaults();
        $out = [];

        foreach ($defaults as $key => $defaultValue) {
            $out[$key] = trim((string)($data[$key] ?? ''));
        }

        $payload = json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($payload) || $payload === '') {
            error_log('ABOUTUS: JSON encoding failed');
            return false;
        }

        // Try to delete existing records (but don't fail if nothing to delete)
        @andison_sb_delete('youtube_links', 'section=eq.aboutus_settings');

        // Insert new row
        $result = andison_sb_insert('youtube_links', [[
            'section' => 'aboutus_settings',
            'url' => $payload,
            'sort_order' => 1,
        ]]);
        
        if (!$result) {
            error_log('ABOUTUS: Supabase insert failed. Check server error logs.');
        }
        
        return $result;
    }
}

if (!function_exists('andison_get_aboutus_json')) {
    function andison_get_aboutus_json(): string
    {
        $settings = andison_get_aboutus_settings();
        return json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
?>
