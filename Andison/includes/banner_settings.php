<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_banner_settings_defaults')) {
    function andison_banner_settings_defaults(): array
    {
        return [
            'inquiry_banner_message' => 'We reply in less than 24 hours, Mondays to Saturdays. For more immediate inquiries, please dial our landline or mobile number.',
        ];
    }
}

if (!function_exists('andison_banner_settings_decode_payload')) {
    function andison_banner_settings_decode_payload(string $payload, array $defaults): array
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

if (!function_exists('andison_get_banner_settings')) {
    function andison_get_banner_settings(): array
    {
        $defaults = andison_banner_settings_defaults();

        // Primary storage: Supabase (youtube_links section=banner_settings)
        $rows = andison_sb_select('youtube_links', 'section=eq.banner_settings&order=sort_order.asc&limit=1');
        if (!empty($rows[0]) && is_array($rows[0])) {
            $rawPayload = (string)($rows[0]['url'] ?? '');
            if (trim($rawPayload) !== '') {
                return andison_banner_settings_decode_payload($rawPayload, $defaults);
            }
        }

        return $defaults;
    }
}

if (!function_exists('andison_save_banner_settings')) {
    function andison_save_banner_settings(array $data): bool
    {
        if (!andison_supabase_is_enabled()) {
            error_log('BANNER: Supabase is not enabled. Missing ANDISON_SUPABASE_URL and/or ANDISON_SUPABASE_KEY.');
            return false;
        }
        
        $defaults = andison_banner_settings_defaults();
        $out = [];

        foreach ($defaults as $key => $defaultValue) {
            $out[$key] = trim((string)($data[$key] ?? ''));
        }

        $payload = json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!is_string($payload) || $payload === '') {
            error_log('BANNER: JSON encoding failed');
            return false;
        }

        // Try to delete existing records (but don't fail if nothing to delete)
        @andison_sb_delete('youtube_links', 'section=eq.banner_settings');

        // Insert new row
        $result = andison_sb_insert('youtube_links', [[
            'section' => 'banner_settings',
            'url' => $payload,
            'sort_order' => 1,
        ]]);
        
        if (!$result) {
            error_log('BANNER: Supabase insert failed. Check server error logs.');
        }
        
        return $result;
    }
}

if (!function_exists('andison_get_banner_message')) {
    function andison_get_banner_message(): string
    {
        $settings = andison_get_banner_settings();
        return (string)($settings['inquiry_banner_message'] ?? andison_banner_settings_defaults()['inquiry_banner_message']);
    }
}

if (!function_exists('andison_get_banner_json')) {
    function andison_get_banner_json(): string
    {
        $settings = andison_get_banner_settings();
        return json_encode($settings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
?>
