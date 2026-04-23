<?php
declare(strict_types=1);

if (!function_exists('andison_brand_order_file_path')) {
    function andison_brand_order_file_path(): string
    {
        return dirname(__DIR__) . '/data/brand_order.json';
    }
}

if (!function_exists('andison_brand_order_default')) {
    function andison_brand_order_default(): array
    {
        return [
            'Panasonic Connect',
            'Robot Systems Peripherals',
            'Kobelco',
            'Metrode',
            'DryRod. II',
            'Weldcraft',
            'Truweld',
            'Arcair',
            'MAGNAFLUX',
            'Tempilstik',
            'TANAKA',
            'CHIYODA',
            'Yutaka',
            'HARDWORKER',
            'Soyer',
            'Aquasol',
            'SK And GAL GAGE',
            'COPPUS',
            'BW',
            'RAE SYSTEMS',
            'WELDAS',
            'UVEX',
            'ACES',
            'MICROGARD',
            'ANSELL',
            'Alfra',
            'BOSCH',
            'Makita',
            'Weiler',
            'Garryson',
            'REVOLT',
            'Technotex',
            'Spilfyter',
            'Dalo',
            'MOTOLITE',
        ];
    }
}

if (!function_exists('andison_brand_order_normalize_name')) {
    function andison_brand_order_normalize_name(string $brand): string
    {
        $normalized = strtolower(trim($brand));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if ($normalized === 'robot systems' || $normalized === 'robot system peripherals' || $normalized === 'robot systems peripherals') {
            return 'robot systems peripherals';
        }
        if ($normalized === 'dryrod ii' || $normalized === 'dryrod. ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
            return 'dryrod. ii';
        }
        if ($normalized === 'hard worker' || $normalized === 'hard workers' || $normalized === 'hardworker') {
            return 'hardworker';
        }
        if ($normalized === 'bw technologies' || $normalized === 'bw') {
            return 'bw';
        }
        if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
            return 'rae systems';
        }
        if ($normalized === 'weller' || $normalized === 'weiler') {
            return 'weiler';
        }
        if ($normalized === 'spilfyter') {
            return 'spillfyter';
        }
        return $normalized;
    }
}

if (!function_exists('andison_brand_order_label')) {
    function andison_brand_order_label(string $brand): string
    {
        $normalized = strtolower(trim($brand));
        if ($normalized === 'robot systems' || $normalized === 'robot system peripherals' || $normalized === 'robot systems peripherals') {
            return 'Robot Systems Peripherals';
        }
        if ($normalized === 'hard worker' || $normalized === 'hard workers' || $normalized === 'hardworker') {
            return 'HARDWORKER';
        }
        if ($normalized === 'dryrod. ii' || $normalized === 'dryrod ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
            return 'DryRod. II';
        }
        if ($normalized === 'ansell') {
            return 'ANSELL';
        }
        if ($normalized === 'panasonic' || $normalized === 'panasonic connect') {
            return 'Panasonic Connect';
        }
        if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
            return 'RAE SYSTEMS';
        }
        if ($normalized === 'weller' || $normalized === 'weiler') {
            return 'Weiler';
        }
        return $brand;
    }
}

if (!function_exists('andison_load_brand_order')) {
    function andison_load_brand_order(): array
    {
        static $cached = null;
        if (is_array($cached)) {
            return $cached;
        }

        $default = andison_brand_order_default();
        $path = andison_brand_order_file_path();
        $order = $default;

        if (is_file($path)) {
            $raw = @file_get_contents($path);
            if (is_string($raw) && trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $seen = [];
                    $order = [];
                    foreach ($decoded as $brand) {
                        $label = trim((string)$brand);
                        if ($label === '') {
                            continue;
                        }
                        $key = strtolower($label);
                        if (isset($seen[$key])) {
                            continue;
                        }
                        $seen[$key] = true;
                        $order[] = $label;
                    }
                    foreach ($default as $brand) {
                        $key = strtolower($brand);
                        if (!isset($seen[$key])) {
                            $order[] = $brand;
                        }
                    }
                }
            }
        }

        $cached = $order;
        return $cached;
    }
}

if (!function_exists('andison_save_brand_order')) {
    function andison_save_brand_order(array $order): bool
    {
        $default = andison_brand_order_default();
        $normalizedDefault = [];
        foreach ($default as $brand) {
            $normalizedDefault[strtolower($brand)] = $brand;
        }

        $seen = [];
        $clean = [];
        foreach ($order as $brand) {
            $label = trim((string)$brand);
            if ($label === '') {
                continue;
            }
            $lookup = andison_brand_order_label($label);
            $key = strtolower($lookup);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $clean[] = $lookup;
        }

        foreach ($default as $brand) {
            $key = strtolower($brand);
            if (!isset($seen[$key])) {
                $clean[] = $brand;
            }
        }

        $path = andison_brand_order_file_path();
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $json = json_encode(array_values($clean), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if (!is_string($json) || $json === '') {
            return false;
        }

        return @file_put_contents($path, $json, LOCK_EX) !== false;
    }
}

if (!function_exists('andison_brand_order_rank')) {
    function andison_brand_order_rank(string $brand): int
    {
        static $rankMap = null;
        if (!is_array($rankMap)) {
            $rankMap = [];
            foreach (andison_load_brand_order() as $idx => $name) {
                $rankMap[andison_brand_order_normalize_name($name)] = $idx;
            }
        }

        $normalized = andison_brand_order_normalize_name($brand);
        return $rankMap[$normalized] ?? 10000;
    }
}

if (!function_exists('andison_sort_brand_list_by_order')) {
    function andison_sort_brand_list_by_order(array &$brands): void
    {
        usort($brands, static function (string $a, string $b): int {
            $rankA = andison_brand_order_rank(andison_brand_order_label($a));
            $rankB = andison_brand_order_rank(andison_brand_order_label($b));
            if ($rankA !== $rankB) {
                return $rankA <=> $rankB;
            }
            return strcasecmp(andison_brand_order_label($a), andison_brand_order_label($b));
        });
    }
}
