<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_normalize_youtube_to_embed')) {
    function andison_normalize_youtube_to_embed(string $input): string
    {
        $input = trim($input);
        if ($input === '') {
            return '';
        }

        // If user pasted an iframe embed code, extract the src URL.
        if (preg_match('~\bsrc\s*=\s*(["\'])(https?://[^"\']+?)\1~i', $input, $m)) {
            $input = $m[2];
        }

        // Accept URLs without scheme.
        if (preg_match('~^(www\.)?youtube\.com/|^youtu\.be/~i', $input)) {
            $input = 'https://' . $input;
        }

        // If already an embed link, keep as-is.
        if (preg_match('~^https?://((www|m|music)\.)?youtube\.com/embed/([A-Za-z0-9_-]{6,})~i', $input, $m)) {
            return 'https://www.youtube.com/embed/' . $m[3];
        }
        if (preg_match('~^https?://(www\.)?youtube-nocookie\.com/embed/([A-Za-z0-9_-]{6,})~i', $input, $m)) {
            return 'https://www.youtube.com/embed/' . $m[2];
        }

        // If a youtu.be short link.
        $parts = @parse_url($input);
        if (is_array($parts) && !empty($parts['host'])) {
            $host = strtolower((string)$parts['host']);
            $path = (string)($parts['path'] ?? '');
            $path = $path === '' ? '' : ltrim($path, '/');

            // youtu.be/<id>
            if (preg_match('~(^|\.)youtu\.be$~', $host)) {
                $seg = explode('/', $path)[0] ?? '';
                if (preg_match('~^[A-Za-z0-9_-]{6,}$~', $seg)) {
                    return 'https://www.youtube.com/embed/' . $seg;
                }
            }

            // youtube.com (including m./music.) and youtube-nocookie.com
            if (preg_match('~(^|\.)(youtube\.com|youtube-nocookie\.com)$~', $host)) {
                // /watch?v=<id>
                if (stripos('/' . $path, '/watch') === 0) {
                    $query = [];
                    if (!empty($parts['query'])) {
                        parse_str((string)$parts['query'], $query);
                    }
                    if (!empty($query['v']) && preg_match('~^[A-Za-z0-9_-]{6,}$~', (string)$query['v'])) {
                        return 'https://www.youtube.com/embed/' . $query['v'];
                    }
                }

                // /shorts/<id>, /live/<id>, /embed/<id>, /v/<id>
                if (preg_match('~^(shorts|live|embed|v)/([A-Za-z0-9_-]{6,})~i', $path, $m)) {
                    return 'https://www.youtube.com/embed/' . $m[2];
                }
            }
        }

        // If a regular watch URL.
        if (preg_match('~^https?://(www\.)?youtube\.com/watch\?~i', $input)) {
            $parts = parse_url($input);
            $query = [];
            if (!empty($parts['query'])) {
                parse_str($parts['query'], $query);
            }
            if (!empty($query['v']) && preg_match('~^[A-Za-z0-9_-]{6,}$~', (string)$query['v'])) {
                return 'https://www.youtube.com/embed/' . $query['v'];
            }
        }

        // If user pasted only an ID.
        if (preg_match('~^[A-Za-z0-9_-]{6,}$~', $input)) {
            return 'https://www.youtube.com/embed/' . $input;
        }

        return '';
    }
}

if (!function_exists('andison_get_youtube_links')) {
    function andison_get_youtube_links(): array
    {
        $defaults = [
            'home_highlights' => [
                'https://www.youtube.com/embed/WhnNcK0O7Gc',
                'https://www.youtube.com/embed/3bQ5YW167pQ',
            ],
            
        ];

        // Try Supabase first
        $sbRows = andison_sb_select('youtube_links', 'order=section,sort_order&limit=100');
        if (!empty($sbRows)) {
            $bySection = [];
            foreach ($sbRows as $row) {
                $section = $row['section'] ?? '';
                if ($section !== '') {
                    $bySection[$section][] = $row['url'] ?? '';
                }
            }
            $out = $defaults;
            foreach ($defaults as $key => $_) {
                if (!isset($bySection[$key])) continue;
                $urls = array_values($bySection[$key]);
                for ($i = 0; $i < 2; $i++) {
                    $raw = trim((string)($urls[$i] ?? ''));
                    if ($raw === '') { $out[$key][$i] = ''; continue; }
                    $norm = andison_normalize_youtube_to_embed($raw);
                    $out[$key][$i] = $norm !== '' ? $norm : '';
                }
            }
            return $out;
        }

        // Fallback to local JSON
        $file = dirname(__DIR__) . '/data/youtube_links.json';
        $loaded = andison_read_json_file($file, []);
        if (!is_array($loaded)) {
            return $defaults;
        }

        $out = $defaults;
        foreach ($defaults as $key => $_defaultList) {
            if (!isset($loaded[$key]) || !is_array($loaded[$key])) {
                continue;
            }
            for ($i = 0; $i < 2; $i++) {
                if (!array_key_exists($i, $loaded[$key])) {
                    continue;
                }
                $raw = trim((string)$loaded[$key][$i]);
                if ($raw === '') { $out[$key][$i] = ''; continue; }
                $norm = andison_normalize_youtube_to_embed($raw);
                $out[$key][$i] = $norm !== '' ? $norm : '';
            }
        }
        return $out;
    }
}

if (!function_exists('andison_save_youtube_links')) {
    function andison_save_youtube_links(array $links): bool
    {
        $allowed = ['home_highlights'];
        $out = [];
        foreach ($allowed as $key) {
            $list = $links[$key] ?? [];
            if (!is_array($list)) {
                $list = [];
            }
            $list = array_values($list);
            $list = array_map('strval', $list);
            $list = array_map('trim', $list);
            $list = array_map('andison_normalize_youtube_to_embed', $list);
            $out[$key] = [$list[0] ?? '', $list[1] ?? ''];
        }

        // Backup to local JSON
        $file = dirname(__DIR__) . '/data/youtube_links.json';
        andison_write_json_file($file, $out);

        // Save to Supabase
        $rows = [];
        foreach ($out as $section => $urls) {
            andison_sb_delete('youtube_links', 'section=eq.' . rawurlencode($section));
            foreach ($urls as $i => $url) {
                $rows[] = ['section' => $section, 'url' => $url, 'sort_order' => $i];
            }
        }
        return andison_sb_insert('youtube_links', $rows);
    }
}



