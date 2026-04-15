<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

if (!function_exists('andison_home_featured_parse_end_date')) {
    function andison_home_featured_parse_end_date(string $rawDate): ?DateTimeImmutable
    {
        $rawDate = trim($rawDate);
        if ($rawDate === '') {
            return null;
        }

        $formats = [
            'F j, Y',
            'F d, Y',
            'M j, Y',
            'M d, Y',
            'Y-m-d',
            'm/d/Y',
            'n/j/Y',
            'm-d-Y',
            'n-j-Y',
        ];

        foreach ($formats as $format) {
            $parsed = DateTimeImmutable::createFromFormat('!' . $format, $rawDate);
            if ($parsed instanceof DateTimeImmutable) {
                $errors = DateTimeImmutable::getLastErrors();
                if (($errors['warning_count'] ?? 0) === 0 && ($errors['error_count'] ?? 0) === 0) {
                    return $parsed->setTime(0, 0, 0);
                }
            }
        }

        $ts = strtotime($rawDate);
        if ($ts === false) {
            return null;
        }

        return (new DateTimeImmutable('@' . $ts))
            ->setTimezone(new DateTimeZone(date_default_timezone_get()))
            ->setTime(0, 0, 0);
    }
}

if (!function_exists('andison_home_featured_is_active')) {
    function andison_home_featured_is_active(array $featured, ?DateTimeImmutable $now = null): bool
    {
        $endDate = andison_home_featured_parse_end_date((string)($featured['event_date'] ?? ''));
        if ($endDate === null) {
            return true;
        }

        $today = ($now ?? new DateTimeImmutable('now'))->setTime(0, 0, 0);
        return $endDate >= $today;
    }
}

if (!function_exists('andison_home_featured_looks_like_image_reference')) {
    function andison_home_featured_looks_like_image_reference(string $value): bool
    {
        $value = trim($value);
        if ($value === '') {
            return false;
        }

        if (preg_match('/\.(png|jpe?g|webp|gif|avif|svg)(\?.*)?$/i', $value) === 1) {
            return true;
        }

        return stripos($value, 'assets/') !== false;
    }
}

if (!function_exists('andison_home_featured_read_fallback_meta')) {
    function andison_home_featured_read_fallback_meta(array $row): array
    {
        $result = [
            'image_alt' => '',
            'title' => '',
            'description' => '',
        ];

        $raw = trim((string)($row['video_file'] ?? ''));
        if ($raw === '') {
            return $result;
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $result['image_alt'] = trim((string)($decoded['image_alt'] ?? ''));
            $result['title'] = trim((string)($decoded['title'] ?? ''));
            $result['description'] = trim((string)($decoded['description'] ?? ''));
            return $result;
        }

        // Backward compatibility with legacy plain-string storage in video_file.
        $result['image_alt'] = $raw;
        return $result;
    }
}

if (!function_exists('andison_home_featured_format_open_text')) {
    function andison_home_featured_format_open_text(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return '';
        }

        $paragraphs = preg_split('/\R{2,}/', $text) ?: [$text];
        $paragraphs = array_values(array_filter(array_map('trim', $paragraphs), static fn(string $paragraph): bool => $paragraph !== ''));

        $html = [];
        foreach ($paragraphs as $paragraph) {
            $html[] = '<p>' . nl2br(htmlspecialchars($paragraph, ENT_QUOTES)) . '</p>';
        }

        return implode('', $html);
    }
}

if (!function_exists('andison_get_home_featured')) {
    function andison_get_home_featured(bool $fallbackToDefaultOnExpired = false): array
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
            return $defaults;
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

        // Reuse existing optional columns to persist admin-managed fallback image config
        // without requiring a Supabase schema migration.
        $storedFallbackImage = trim((string)($loaded['youtube_url'] ?? ''));
        $storedFallbackMeta = andison_home_featured_read_fallback_meta($loaded);
        if (andison_home_featured_looks_like_image_reference($storedFallbackImage)) {
            $defaults['image'] = $storedFallbackImage;
            if ($storedFallbackMeta['image_alt'] !== '') {
                $defaults['image_alt'] = $storedFallbackMeta['image_alt'];
            }
        }
        if ($storedFallbackMeta['title'] !== '') {
            $defaults['title'] = $storedFallbackMeta['title'];
        }
        if ($storedFallbackMeta['description'] !== '') {
            $defaults['description'] = $storedFallbackMeta['description'];
        }

        // Safety: if image path is empty, fall back.
        if (($out['image'] ?? '') === '') {
            $out['image'] = $defaults['image'];
        }
        if (($out['image_alt'] ?? '') === '') {
            $out['image_alt'] = $defaults['image_alt'];
        }

        if ($fallbackToDefaultOnExpired && !andison_home_featured_is_active($out)) {
            return $defaults;
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

        // Save to Supabase
        andison_sb_truncate('home_featured');
        return andison_sb_insert('home_featured', [$out]);
    }
}



