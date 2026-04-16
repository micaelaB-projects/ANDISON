<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_footer_settings_defaults')) {
    function andison_footer_settings_defaults(): array
    {
        return [
            'brand_blurb' => 'Andison Industrial Sales Inc., is a leading local industrial supply company, delivering high quality solutions, representing various world-class brands since 1994.',
            'manila_title' => 'Manila',
            'manila_address' => 'Andison Bldg., Ground Flr. 917-919 Luzon St., Sta. Cruz, Manila, 1003 Philippines',
            'manila_phone_1' => '(+632) 8584-4958',
            'manila_phone_2' => '(+632) 8243-2873',
            'calabarzon_title' => 'Calabarzon',
            'calabarzon_address' => '29B P. Zamora Street, Batangas City, 4200 Philippines',
            'calabarzon_phone' => '(+6343) 425 4126',
            'contact_email' => 'info@andison-industrial.com',
            'facebook_url' => '',
            'linkedin_url' => '',
            'navigation_title' => 'Navigation',
            'copyright' => '© 2026 ANDISON INDUSTRIAL. All rights reserved.',
        ];
    }
}

if (!function_exists('andison_footer_settings_decode_payload')) {
    function andison_footer_settings_decode_payload(string $payload, array $defaults): array
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

if (!function_exists('andison_get_footer_settings')) {
    function andison_get_footer_settings(): array
    {
        $defaults = andison_footer_settings_defaults();

        // Primary storage: Supabase (youtube_links section=footer_settings)
        $rows = andison_sb_select('youtube_links', 'section=eq.footer_settings&order=sort_order.asc&limit=1');
        if (!empty($rows[0]) && is_array($rows[0])) {
            $rawPayload = (string)($rows[0]['url'] ?? '');
            if (trim($rawPayload) !== '') {
                return andison_footer_settings_decode_payload($rawPayload, $defaults);
            }
        }

        return $defaults;
    }
}

if (!function_exists('andison_save_footer_settings')) {
    function andison_save_footer_settings(array $data): bool
    {
        $defaults = andison_footer_settings_defaults();
        $out = [];

        foreach ($defaults as $key => $defaultValue) {
            $out[$key] = trim((string)($data[$key] ?? ''));
        }

        $payload = json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($payload) || $payload === '') {
            return false;
        }

        // Keep only one settings row in existing Supabase table.
        if (!andison_sb_delete('youtube_links', 'section=eq.footer_settings')) {
            return false;
        }

        return andison_sb_insert('youtube_links', [[
            'section' => 'footer_settings',
            'url' => $payload,
            'sort_order' => 0,
        ]]);
    }
}
