<?php
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}
require_once __DIR__ . '/Andison/includes/analytics.php';
andison_track_visit('brand');
$_btrack = isset($_GET['name']) ? trim(strip_tags($_GET['name'])) : '';
if ($_btrack) andison_track_brand_visit($_btrack);
require_once __DIR__ . '/Andison/includes/home_featured.php';
require_once __DIR__ . '/Andison/includes/home_slider.php';
require_once __DIR__ . '/Andison/includes/youtube_links.php';
require_once __DIR__ . '/includes/brands_info.php';

$featured = andison_get_home_featured();
$slides = andison_get_home_slider();
$ytLinks = andison_get_youtube_links();

$brand_name = isset($_GET['name']) ? htmlspecialchars(trim(strip_tags($_GET['name']))) : 'Brand';

if (!function_exists('andison_brand_lookup_candidates')) {
    function andison_brand_lookup_candidates(string $brand): array
    {
        $brand = trim($brand);
        if ($brand === '') {
            return [];
        }

        $normalized = strtolower($brand);
        $candidates = [$brand];

        if ($normalized === 'panasonic' || $normalized === 'panasonic connect') {
            $candidates[] = 'Panasonic Connect';
            $candidates[] = 'PANASONIC';
        }

        if ($normalized === 'dryrod. ii' || $normalized === 'dryrod ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
            $candidates[] = 'DryRod. II';
            $candidates[] = 'DryRod II';
            $candidates[] = 'Phoenix Dry Rod';
            $candidates[] = 'PHOENIX DRY ROD';
            $candidates[] = 'PHOENIX DRYROD';
        }

        if ($normalized === 'bw' || $normalized === 'bw technologies') {
            $candidates[] = 'BW';
            $candidates[] = 'BW Technologies';
            $candidates[] = 'BW TECHNOLOGIES';
        }

        if ($normalized === 'microgard') {
            $candidates[] = 'AlphaTec';
            $candidates[] = 'MICROGARD';
        }

        if ($normalized === 'alphatec') {
            $candidates[] = 'AlphaTec';
            $candidates[] = 'MICROGARD';
        }

        if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
            $candidates[] = 'RAE SYSTEMS';
            $candidates[] = 'RAC';
            $candidates[] = 'RAE';
        }

        if ($normalized === 'weller' || $normalized === 'weiler') {
            $candidates[] = 'WEILER';
            $candidates[] = 'Weiler';
            $candidates[] = 'Weller';
        }

        if ($normalized === 'robot systems' || $normalized === 'robot systems peripherals' || $normalized === 'robot system peripherals') {
            $candidates[] = 'Robot Systems';
            $candidates[] = 'ROBOT SYSTEMS';
            $candidates[] = 'Robot Systems Peripherals';
            $candidates[] = 'ROBOT SYSTEMS PERIPHERALS';
            $candidates[] = 'Robot System Peripherals';
            $candidates[] = 'ROBOT SYSTEM PERIPHERALS';
        }

        $seen = [];
        $unique = [];
        foreach ($candidates as $candidate) {
            $key = strtolower(trim((string)$candidate));
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = (string)$candidate;
        }

        return $unique;
    }
}

if (!function_exists('andison_brand_display_label_public')) {
    function andison_brand_display_label_public(string $brand): string
    {
        $normalized = strtolower(trim($brand));
        if ($normalized === 'dryrod. ii' || $normalized === 'dryrod ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
            return 'DryRod. II';
        }
        if ($normalized === 'bw' || $normalized === 'bw technologies') {
            return 'BW Technologies';
        }
        if ($normalized === 'ansell') {
            return 'ANSELL';
        }
        if ($normalized === 'microgard') {
            return 'AlphaTec';
        }
        if ($normalized === 'alphatec') {
            return 'AlphaTec';
        }
        if ($normalized === 'panasonic' || $normalized === 'panasonic connect') {
            return 'Panasonic Connect';
        }
        if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
            return 'RAE SYSTEMS';
        }
        if ($normalized === 'weller' || $normalized === 'weiler') {
            return 'WEILER';
        }
        if ($normalized === 'robot systems peripherals' || $normalized === 'robot systems' || $normalized === 'robot system peripherals') {
            return 'Robot Systems Peripherals';
        }
        return $brand;
    }
}

if (!function_exists('andison_pick_brand_info_bucket')) {
    function andison_pick_brand_info_bucket(array $allBrands, array $candidates): string
    {
        if (empty($allBrands) || empty($candidates)) {
            return '';
        }

        // Exact key with products first
        foreach ($candidates as $candidate) {
            if (isset($allBrands[$candidate]) && !empty($allBrands[$candidate]['products'])) {
                return (string)$candidate;
            }
        }

        // Case-insensitive key with products
        foreach ($candidates as $candidate) {
            $needle = strtolower(trim((string)$candidate));
            foreach ($allBrands as $key => $info) {
                if (strtolower((string)$key) === $needle && !empty($info['products'])) {
                    return (string)$key;
                }
            }
        }

        // Exact key fallback
        foreach ($candidates as $candidate) {
            if (isset($allBrands[$candidate])) {
                return (string)$candidate;
            }
        }

        // Case-insensitive fallback
        foreach ($candidates as $candidate) {
            $needle = strtolower(trim((string)$candidate));
            foreach ($allBrands as $key => $_info) {
                if (strtolower((string)$key) === $needle) {
                    return (string)$key;
                }
            }
        }

        return '';
    }
}

if (!function_exists('andison_brand_preferred_key')) {
    function andison_brand_preferred_key(string $displayName, string $fallbackKey, array $allBrands): string
    {
        $displayKey = strtolower(trim($displayName));
        if ($displayKey === '') {
            return $fallbackKey;
        }

        $bestKey = $fallbackKey;
        $bestScore = -1;

        foreach ($allBrands as $candidateKey => $candidateInfo) {
            if (!is_array($candidateInfo)) {
                continue;
            }

            $candidateDisplay = strtolower(trim(andison_brand_display_label_public((string)$candidateKey)));
            if ($candidateDisplay !== $displayKey) {
                continue;
            }

            $candidateLogo = trim((string)($candidateInfo['logo'] ?? ''));
            $candidateDescription = trim((string)($candidateInfo['description'] ?? ''));
            $candidateProducts = count($candidateInfo['products'] ?? []);
            $candidateKeyLower = strtolower(trim((string)$candidateKey));

            $score = 0;
            if ($candidateLogo !== '') {
                $score += 100000;
            }
            if ($candidateDescription !== '') {
                $score += 5000;
            }
            if ($candidateKeyLower === $displayKey) {
                $score += 1000;
            }
            if ($displayKey === 'alphatec' && $candidateKeyLower === 'alphatec') {
                $score += 200000;
            }
            $score += min(999, $candidateProducts);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestKey = (string)$candidateKey;
            }
        }

        return $bestKey;
    }
}

if (!function_exists('andison_brand_logo_fallback_file')) {
    function andison_brand_logo_fallback_file(string $brandName, array $logoMap): string
    {
        if (isset($logoMap[$brandName])) {
            return (string)$logoMap[$brandName];
        }

        $needle = strtolower(trim($brandName));
        foreach ($logoMap as $mapName => $mapFile) {
            if (strtolower(trim((string)$mapName)) === $needle) {
                return (string)$mapFile;
            }
        }

        return strtoupper($brandName);
    }
}

if (!function_exists('andison_normalize_badge_label')) {
    function andison_normalize_badge_label($rawBadge): string
    {
        $badge = trim((string)$rawBadge);
        if ($badge === '') {
            return '';
        }

        $normalized = strtolower(preg_replace('/\s+/', ' ', $badge) ?? $badge);
        if ($normalized === '-' || $normalized === '-- none --' || $normalized === 'none' || $normalized === 'n/a') {
            return '';
        }

        if ($normalized === 'new arrival' || $normalized === 'new') {
            return 'New';
        }
        if ($normalized === 'available' || $normalized === 'in stock' || $normalized === 'instock') {
            return 'Available';
        }
        if ($normalized === 'not available' || $normalized === 'unavailable' || $normalized === 'out of stock') {
            return 'Not Available';
        }
        if ($normalized === 'featured') {
            return 'Featured';
        }
        if ($normalized === 'best seller' || $normalized === 'bestseller') {
            return 'Best Seller';
        }
        if ($normalized === 'limited stock' || $normalized === 'limited') {
            return 'Limited Stock';
        }

        return $badge;
    }
}

if (!function_exists('andison_render_brand_description')) {
    function andison_render_brand_description($rawDescription): string
    {
        $value = trim((string)$rawDescription);
        if ($value === '') {
            return '';
        }

        $value = preg_replace('~<(script|style|iframe|object|embed|form|input|button|textarea|select)[^>]*>.*?</\\1>~is', '', $value) ?? $value;
        $value = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $value) ?? $value;
        $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><a><span><div><table><thead><tbody><tfoot><tr><th><td><img>';
        $safeHtml = strip_tags($value, $allowed);
        $safeHtml = preg_replace_callback('/\s(href|src)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', static function (array $m): string {
            $attr = strtolower((string)$m[1]);
            $uri = trim((string)$m[2], "\"'");
            $check = strtolower(trim($uri));
            if (str_starts_with($check, 'javascript:') || str_starts_with($check, 'data:text/html')) {
                $uri = '#';
            }
            return ' ' . $attr . '="' . htmlspecialchars($uri, ENT_QUOTES) . '"';
        }, $safeHtml) ?? $safeHtml;

        if (strip_tags($value) === $value) {
            return nl2br(htmlspecialchars($value));
        }

        return trim($safeHtml);
    }
}

// Map brand names to logo filenames
$logo_map = [
    'Panasonic Connect'      => 'PANASONIC',
    'BW'                     => 'BW TECHNOLOGIES',
    'BW Technologies'        => 'BW TECHNOLOGIES',
    'Weldcraft'              => 'WELDCRAFT',
    'Soyer'                  => 'SOYER',
    'Alfra'                  => 'ALFRA',
    'ACES'                   => 'ACES',
    'UVEX'                   => 'UVEX',
    'ANSELL'                 => 'ANSELL',
    'MICROGARD'              => 'MICROGARD',
    'AlphaTec'               => 'MICROGARD',
    'WELDAS'                 => 'WELDAS',
    'TANAKA'                 => 'TANAKA',
    'CHIYODA'                => 'CHIYODA',
    'HARDWORKER'             => 'HARDWORKER',
    'MAGNAFLUX'              => 'MAGNAFLUX',
    'COPPUS'                 => 'COPPUS',
    'BOSCH'                  => 'BOSCH',
    'MOTOLITE'               => 'MOTOLITE',
    'Aquasol'                => 'AQUASOL',
    'Arcair'                 => 'ARCAIR',
    'Dalo'                   => 'DALO',
    'DryRod. II'             => 'DRYROD',
    'Garryson'               => 'GARRYSON',
    'Kobelco'                => 'KOBELCO',
    'Makita'                 => 'MAKITA',
    'Metrode'                => 'METRODE',
    'RAE SYSTEMS'            => 'RAE SYSTEMS',
    'Robot Systems Peripherals' => 'ROBOT SYSTEMS',
    'SK And GAL GAGE'        => 'SK AND GAL GAGE',
    'Spilfyter'              => 'SPILFYTER',
    'Tempilstik'             => 'TEMPILSTIK',
    'Truweld'                => 'TRUWELD',
    'Weiler'                 => 'WEILER',
    'Weller'                 => 'WEILER',
    'Yutaka'                 => 'YUTAKA',
];

// Map brand display names to canonical brand names (for lookups)
$brand_name_map = [
    'BW'                     => 'BW Technologies',
    'RAE'                    => 'RAE SYSTEMS',
    'RAC'                    => 'RAE SYSTEMS',
    'Robot Systems'          => 'Robot Systems Peripherals',
    'Hard Workers'           => 'HARDWORKER',
    'Weller'                 => 'Weller',
    'Weiler'                 => 'Weiler',
    'Technotex'              => 'Technotex',
];

// Resolve brand_name to canonical name
$canonical_brand_name = isset($brand_name_map[$brand_name]) ? $brand_name_map[$brand_name] : $brand_name;

$logo_file = andison_brand_logo_fallback_file($canonical_brand_name, $logo_map);

// Brands that use .png instead of .jpg
$png_brands = ['ROBOT SYSTEMS', 'WELDCRAFT', 'REVOLT', 'TECHNOTEX'];
$logo_ext = in_array($logo_file, $png_brands) ? 'png' : 'jpg';

$brands_info_data = andison_get_brands_info(true);

// ── Clean up orphaned brand aliases (e.g., old MICROGARD after rename to AlphaTec) ────
if (function_exists('andison_sb_delete') && function_exists('andison_sb_select')) {
    $orphanedAliases = ['MICROGARD', 'Microgard', 'Hard Worker', 'Hard Workers'];
    foreach ($orphanedAliases as $oldName) {
        $canonical = strtolower(trim($oldName));
        if ($canonical === 'microgard' && isset($brands_info_data['AlphaTec'])) {
            // MICROGARD was renamed to AlphaTec; delete any stray MICROGARD record
            @andison_sb_delete('brands', 'name=eq.' . rawurlencode($oldName));
            @unlink(__DIR__ . '/Andison/data/_cache/brands_full.cache');
        }
    }
}

$brandDisplayToKey = [];
foreach (array_keys($brands_info_data) as $brandKey) {
    $display = andison_brand_display_label_public((string)$brandKey);
    $displayKey = strtolower(trim($display));
    if ($displayKey === '') {
        continue;
    }

    if (!isset($brandDisplayToKey[$displayKey])) {
        $brandDisplayToKey[$displayKey] = (string)$brandKey;
        continue;
    }

    $currentKey = $brandDisplayToKey[$displayKey];
    $currentCount = count($brands_info_data[$currentKey]['products'] ?? []);
    $newCount = count($brands_info_data[$brandKey]['products'] ?? []);
    if ($newCount > $currentCount) {
        $brandDisplayToKey[$displayKey] = (string)$brandKey;
    }
}

$requestedDisplayKey = strtolower(trim(andison_brand_display_label_public($brand_name)));
$resolvedBrandKey = '';
if (isset($brandDisplayToKey[$requestedDisplayKey])) {
    $resolvedBrandKey = (string)$brandDisplayToKey[$requestedDisplayKey];
}

$resolvedBrandKey = andison_brand_preferred_key(
    andison_brand_display_label_public($resolvedBrandKey !== '' ? $resolvedBrandKey : $brand_name),
    $resolvedBrandKey !== '' ? $resolvedBrandKey : $canonical_brand_name,
    $brands_info_data
);

$lookupCandidates = andison_brand_lookup_candidates($brand_name);
if ($canonical_brand_name !== $brand_name) {
    $lookupCandidates = array_merge($lookupCandidates, andison_brand_lookup_candidates($canonical_brand_name));
}

$productSourceKey = andison_pick_brand_info_bucket($brands_info_data, $lookupCandidates);

if ($resolvedBrandKey === '') {
    $resolvedBrandKey = $productSourceKey;
}

$resolvedBrandKey = andison_brand_preferred_key(
    andison_brand_display_label_public($resolvedBrandKey !== '' ? $resolvedBrandKey : $brand_name),
    $resolvedBrandKey !== '' ? $resolvedBrandKey : $canonical_brand_name,
    $brands_info_data
);

if ($resolvedBrandKey !== '' && isset($brands_info_data[$resolvedBrandKey])) {
    $brand_info = $brands_info_data[$resolvedBrandKey];
} else {
    $brand_info = [
        'description' => 'High-quality industrial products and solutions.',
        'logo' => '',
        'products'    => [],
    ];
}

if (
    empty($brand_info['products'])
    && $productSourceKey !== ''
    && isset($brands_info_data[$productSourceKey])
    && is_array($brands_info_data[$productSourceKey])
) {
    $fallbackProducts = $brands_info_data[$productSourceKey]['products'] ?? [];
    if (!empty($fallbackProducts)) {
        $brand_info['products'] = $fallbackProducts;
    }
}

$brand_products = $brand_info['products'] ?? [];

// Show the normalized/resolved brand label on page so admin edits are reflected on public view.
$brand_name = htmlspecialchars(andison_brand_display_label_public($resolvedBrandKey !== '' ? $resolvedBrandKey : $brand_name));

$brand_logo_src = trim((string)($brand_info['logo'] ?? ''));
if ($brand_logo_src === '') {
    $brand_logo_src = 'assets/brands/' . rawurlencode($logo_file) . '.' . $logo_ext;
}

/**
 * Auto-detect all numbered images for a product.
 * e.g. "Product 1.jpg" ? also finds "Product 2.jpg", "Product 3.jpg" etc.
 * Supports patterns:  "Name 1.ext"  "Name - 1.ext"  "Name (1).ext"  "Name - (1).ext"
 */
function andison_auto_images(string $webPath, array $explicit, string $baseDir): array {
    if (!empty($explicit)) return array_values(array_filter($explicit));
    if (!$webPath) return [];
    $result = [$webPath];
    $fsRel  = str_replace('%20', ' ', urldecode($webPath));
    $fsAbs  = $baseDir . '/' . $fsRel;
    if (!file_exists($fsAbs)) return $result;
    $fsDir   = dirname($fsAbs);
    $webDir  = dirname($webPath);
    $bn      = basename($fsAbs);
    // Match trailing: (sep)(optional-open-paren)1(optional-close-paren).ext
    if (!preg_match('/^(.*?)(\s*-\s*|\s+)\(?1\)?(\.[^.]+)$/i', $bn, $m)) return $result;
    $prefix   = $m[1];
    $sep      = $m[2];
    $ext      = $m[3];
    $hasParen = strpos($bn, '(1)') !== false;
    for ($n = 2; $n <= 8; $n++) {
        $nextBn = $hasParen ? $prefix.$sep.'('.$n.')'.$ext : $prefix.$sep.$n.$ext;
        if (file_exists($fsDir . '/' . $nextBn)) {
            $result[] = $webDir . '/' . str_replace(' ', '%20', $nextBn);
        } else break;
    }
    return $result;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $brand_name; ?> - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: Verdana, 'Segoe UI', Tahoma, Geneva, sans-serif;
            line-height: 1.6;
            color: #333;
            padding-top: 142px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-content {
            opacity: 1;
            transform: none;
            animation: brandContentEnter 220ms ease;
            transition: opacity 160ms ease, transform 160ms ease;
        }
        @keyframes brandContentEnter {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: none;
            }
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%);
            color: white;
            padding: 14px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1200;
            width: 100%;
        }

        .header-top {
            display: flex;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            gap: 20px;
            margin-bottom: 12px;
        }

        .logo {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .logo-box {
            background: transparent;
            color: #2b00d9;
            padding: 0;
            border-radius: 0;
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .logo-box img {
            height: 60px;
            width: auto;
            display: block;
        }

        .header-contact {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            flex: 0 0 auto;
        }

        .contact-link {
            color: rgba(255,255,255,0.95);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding-bottom: 8px;
            white-space: nowrap;
            position: relative;
            display: inline-block;
        }

        .contact-link::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 64px;
            height: 3px;
            background: rgba(255,255,255,0.18);
            bottom: -6px;
            border-radius: 2px;
            transition: transform 220ms ease;
        }

        .contact-link:hover::after,
        .contact-link:focus-visible::after {
            transform: translateX(-50%) scaleX(1);
        }
        /* Contact popover */
        .contact-dropdown {
            position: relative;
            display: inline-block;
        }

        .contact-popover {
            position: absolute;
            left: 50%;
            top: calc(100% + 12px);
            width: 320px;
            background: #fff;
            color: #111;
            border-radius: 8px;
            padding: 14px 16px;
            box-shadow: 0 10px 30px rgba(10,10,20,0.12);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateX(-50%) translateY(-6px) scale(0.98);
            transition: opacity 180ms ease, transform 180ms ease, visibility 180ms;
            z-index: 120;
        }

        .contact-popover::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: -8px;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #fff;
            filter: drop-shadow(0 -1px 0 rgba(0,0,0,0.03));
        }

        .contact-dropdown:not(.closed) .contact-link:hover ~ .contact-popover,
        .contact-dropdown:not(.closed) .contact-popover:hover {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateX(-50%) translateY(0) scale(1);
        }

        /* mobile: click-to-open; .open class used instead of hover */
        @media (max-width: 768px) {
            .contact-dropdown:not(.closed) .contact-link:hover ~ .contact-popover,
            .contact-dropdown:not(.closed) .contact-popover:hover {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transform: translateX(-50%) translateY(-6px) scale(0.98);
            }
            .contact-dropdown.open .contact-popover {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(0) scale(1);
            }
        }

        .contact-close {
            position: absolute;
            top: 8px;
            right: 8px;
            background: transparent;
            border: none;
            color: #666;
            font-weight: 700;
            font-size: 24px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            line-height: 1;
        }

        .contact-close:hover { background: rgba(0,0,0,0.06); color: #333; }

        /* when user explicitly closes, keep hidden until they move away */
        .contact-dropdown.closed .contact-popover {
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateX(-50%) translateY(-6px) scale(0.98) !important;
        }

        .contact-list { list-style: none !important; margin: 0 !important; padding: 6px 0 !important; display: block !important; visibility: visible !important; }
        .contact-list li { display:flex !important; gap:12px !important; align-items:center !important; padding:10px 6px !important; visibility: visible !important; }
        .contact-list .icon { font-size:18px !important; width:28px !important; text-align:center !important; color:#2B11DB !important; }
        .contact-list a { color: #111 !important; text-decoration:none !important; font-weight:600 !important; }
        .contact-list a:hover { text-decoration:underline; }

        /* compact on mobile */
        @media (max-width: 768px) {
            .contact-popover { width: 240px; padding: 8px 10px; }
            .contact-list { padding: 2px 0; }
            .contact-list li { gap: 8px; padding: 6px 4px; }
            .contact-list .icon { font-size: 14px; width: 20px; }
            .contact-list a { font-size: 12px; }
        }

        .search-bar {
            flex: 1 1 auto;
            display: flex;
            justify-content: center;
            max-width: 600px;
            margin: 0 0 0 20px;
        }

        .search-bar .search-field {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            height: 46px;
            padding: 10px 16px 10px 40px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            font-size: 15px;
            background: rgba(255,255,255,0.95);
            color: #333;
        }

        .search-bar input::placeholder {
            color: #999;
        }

        .search-bar .search-field i {
            position: absolute;
            left: 12px;
            font-size: 16px;
            pointer-events: none;
            color: #666;
        }

        .search-btn {
            display: none;
        }

        .inquiry-btn,
        .cart-icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg,  #00E5C8  0%, #347aec 100%);
            position: relative;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,188,212,0.4);
            gap: 8px;
        }

        .inquiry-btn:hover,
        .cart-icon-wrapper:hover {
            background: linear-gradient(135deg, #00ACC1, #00796B);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,188,212,0.5);
            color: white;
        }

        .inquiry-btn .btn-icon { display: inline; }
        .inquiry-btn .btn-text { display: inline; }

        .cart-badge {
            background: #c70d0d;
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(199,13,13,0.5);
            position: static;
            margin-left: 2px;
        }

        .cart-badge.hidden {
            display: none;
        }

        .right-actions {
            margin-left: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
        }

        /* Navigation */
        nav {
            position: relative;
            background: rgba(0, 215, 179, 0.85);
            backdrop-filter: blur(10px);
            overflow: visible;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px; /* space for the left Browse toggle */
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            min-height: 52px;
            gap: 0;
            justify-content: center;
        }

        /* Pin the browse toggle to the left side of the nav area */
        .browse-toggle {
            position: absolute;
            left: 12px;
            top: 20%;
            transform: translateY(-50%);
            z-index: 80;
            background: transparent;
            border: none;
            color: white;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            cursor: pointer;
            font-size: 15px;
            line-height: 1.6;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-wrap: nowrap;
            gap: 30px;
            margin: 0;
            padding: 0;
            width: 100%;
        justify-content: center;
        }

        .nav-list li { position: relative; }

        .nav-list a {
            text-decoration: none;
            display: block;
        }

        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        /* Glowing underline + dark active background for top-level nav links */
        .nav-list > li > a {
            position: relative;
            padding: 10px 14px;
            color: white;
            transition: color 180ms ease, background 180ms ease;
        }

        .nav-list > li > a::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 2px;
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 44px;
            height: 5px;
            border-radius: 6px;
            background: linear-gradient(90deg, #00ffd1 0%, #00d4aa 50%, #2B11DB 100%);
            box-shadow: 0 2px 10px rgba(0,212,170,0.35);
            pointer-events: none;
            transition: transform 180ms ease, width 180ms ease;
        }

        .nav-list > li > a:hover {
            background: rgba(0,0,0,0.10);
            border-radius: 6px;
        }

        .nav-list > li > a:hover::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
        }

        .nav-list > li > a.active {
            background: rgba(0,0,0,0.14);
            color: #fff;
            font-weight: 700;
            border-radius: 6px;
            box-shadow: inset 0 -6px 18px rgba(0,0,0,0.06);
        }

        .nav-list > li > a.active::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
        }

        
        .nav-dropdown::before { content: ''; position: absolute; top: -30px; left: 0; width: 100%; height: 30px; background: transparent; }
        
        .nav-dropdown::before { content: ''; position: absolute; top: -30px; left: 0; width: 100%; height: 30px; background: transparent; }
        .nav-dropdown {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            background: white;
            min-width: 280px;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
            z-index: 110;
            padding: 16px;
            margin-top: 8px;
        }

        .nav-list > li:hover .nav-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* Keep dropdown visible when hovering over it */
        .nav-dropdown:hover {
            opacity: 1;
            visibility: visible;
        }

        .nav-dropdown h4 {
            color: #2b00d9;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .nav-dropdown ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-dropdown ul li {
            margin: 0;
        }

        .nav-dropdown ul a {
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 12px;
            display: block;
            border-radius: 7px;
            transition: background 0.16s ease, color 0.16s ease;
        }

        .nav-dropdown ul a:hover,
        .nav-dropdown ul a:focus-visible {
            background: #e7ebf7;
            color: #2B11DB;
            outline: none;
        }

        .nav-dropdown p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        nav li:nth-child(3) .nav-dropdown {
            min-width: 770px;
            max-width: 770px;
            padding: 18px 20px;
        }

        .nav-dropdown ul a {
            display: block;
            border-radius: 7px;
            transition: background 0.16s ease, color 0.16s ease;
        }

        .nav-dropdown ul a:hover,
        .nav-dropdown ul a:focus-visible {
            background: #e7ebf7;
            color: #2B11DB;
            outline: none;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 2px 4px !important;
            margin-top: 10px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 82px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 140px !important;
            height: 74px !important;
            min-height: 74px !important;
            overflow: hidden !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a img {
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            max-height: none !important;
            object-fit: contain !important;
            display: block;
            pointer-events: all;
            cursor: pointer;
            transform: scale(1.65) !important;
            transform-origin: center center !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            cursor: pointer;
        }

        /* Ensure Industries dropdown item text stays readable on hover/focus. */
        nav li:nth-child(4) .nav-dropdown ul a {
            color: #374151 !important;
            font-weight: 600;
        }

        nav li:nth-child(4) .nav-dropdown ul a:hover,
        nav li:nth-child(4) .nav-dropdown ul a:focus-visible {
            background: #e2e7f5 !important;
            color: #1f2fa9 !important;
            outline: none;
        }

        /* Global dropdown readability (brand page does not load footer_modernize). */
        nav .nav-dropdown ul a:not(.andison-nav-brand-link) {
            color: #374151 !important;
            -webkit-text-fill-color: #374151 !important;
            font-weight: 600 !important;
            opacity: 1 !important;
            text-shadow: none !important;
        }

        nav .nav-dropdown ul a:not(.andison-nav-brand-link):hover,
        nav .nav-dropdown ul a:not(.andison-nav-brand-link):focus-visible,
        nav .nav-dropdown ul li.active > a:not(.andison-nav-brand-link),
        nav .nav-dropdown ul a[aria-current="page"]:not(.andison-nav-brand-link) {
            background: #e2e7f5 !important;
            color: #1f2fa9 !important;
            -webkit-text-fill-color: #1f2fa9 !important;
            outline: none !important;
            opacity: 1 !important;
            text-shadow: none !important;
        }

        /* Hero Section */
        .hero {
            position: relative;
            background: linear-gradient(135deg, rgba(43, 17, 219, 0.8) 0%, rgba(0, 215, 179, 0.8) 100%), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23888888" width="1200" height="600"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 80px 20px;
            aspect-ratio: 16;
            min-height: 400px;
            max-height: 700px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 80px;
            z-index: 1;
            box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.1);
        }

        .hero-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
            overflow: hidden;
        }

        .hero-slide {
            position: absolute;
            width: 40%;
            aspect-ratio: 16 / 9;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.3;
            transition: all 0.1s ease;
            transform: translateX(0) scale(0.85);
            filter: blur(4px);
            overflow: hidden;
        }

        .hero-slide.prev {
            left: 8%;
            opacity: 0.35;
            transform: translateX(-50px) scale(0.8);
            filter: blur(5px);
        }

        .hero-slide.active {
            left: 30%;
            opacity: 1;
            transform: translateX(0) scale(1);
            filter: blur(0);
            z-index: 10;
        }

        .hero-slide.next {
            right: 8%;
            opacity: 0.35;
            transform: translateX(50px) scale(0.8);
            filter: blur(5px);
        }

        /* blurred full-bleed background taken from the slide's background-image */
        .hero-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: inherit;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(15px) brightness(0.7) saturate(1.3);
            z-index: 0;
        }

        /* subtle dark overlay above the blur to improve text contrast */
        .hero-slide::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.2);
            z-index: 1;
        }

        /* centered clear image card on top of the blurred background */
        .hero-content {
            max-width: 900px;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
        }

        .hero-content h1,
        .hero-content p,
        .hero-content .cta-button {
            display: none;
        }

        .hero-thumb {
            width: 100%;
            height: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 12px;
            box-shadow: 0 18px 40px rgba(2,6,23,0.45);
            overflow: hidden;
            background-color: rgba(255,255,255,0.05);
            aspect-ratio: 16 / 9;
        }

        .hero-content {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .hero-content h1,
        .hero-content p,
        .hero-content .cta-button {
            display: none;
        }

        .hero-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 20;
        }

        .hero-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: background 0.1s;
        }

        .hero-dot.active {
            background: rgba(255,255,255,0.9);
        }

        .hero-dot:hover {
            background: rgba(255,255,255,0.7);
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }

        .cta-button {
            background: linear-gradient(135deg, #00D7B3 0%, #00C99A 100%);
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 215, 179, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 215, 179, 0.4);
        }

        /* Section */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }

        section {
            width: 100%;
            padding: 100px 20px;
            position: relative;
            z-index: 10;
            background: white;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
        }

        section h2 {
            text-align: center;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            color: #2B11DB;
            width: 100%;
            background: linear-gradient(90deg, #1565C0 0%, #00BCD4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
    
        .section-description {
            text-align: center;
            max-width: 750px;
            margin: 0 auto 60px;
            color: #8B4513;
            line-height: 1.9;
            width: 100%;
            box-sizing: border-box;
            padding: 0 20px;
            font-size: 15px;
            font-weight: 500;
        }

        /* Product Highlights */
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            padding: 0 20px;
        }

        .product-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e8eef7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.4s ease;
        }

        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(43, 17, 219, 0.15);
        }

        .product-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 320px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
            transition: transform 0.3s ease;
        }

        .product-image iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .product-image video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
        }

        .play-btn {
            width: 60px;
            height: 60px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            cursor: pointer;
            transition: background 0.1s;
        }

        .play-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .product-info {
            padding: 28px 24px;
            background: white;
            width: 100%;
            box-sizing: border-box;
            border-top: 1px solid #f0f0f0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-info h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #2B11DB;
            line-height: 1.4;
        }

        .product-info p {
            font-size: 15px;
            color: #666;
            line-height: 1.7;
            margin: 0;
        }

        /* Service Cards - Old Layout */
        .services-grid {
            display: flex;
            flex-direction: column;
            gap: 28px;
            width: 100%;
            max-width: 1050px;
        }

        .service-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            background: white;
            border-radius: 16px;
            padding: 48px 44px;
            border: 1px solid #E0E3FF;
            box-shadow: 0 4px 16px rgba(30, 136, 229, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(30, 136, 229, 0.15), 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .service-card.reverse {
            direction: rtl;
        }

        .service-card.reverse > * {
            direction: ltr;
        }

        .service-badge {
            display: inline-block;
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
            box-shadow: 0 4px 12px rgba(30, 136, 229, 0.25);
        }

        .service-card.teal .service-badge {
            background: linear-gradient(135deg, #00bcd4 0%, #00897b 100%);
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);
        }

        .service-content h3 {
            font-size: 26px;
            font-weight: 800;
            color: #1e88e5;
            margin-bottom: 18px;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        .service-card.teal .service-content h3 {
            color: #00bcd4;
        }

        .service-content p {
            font-size: 14px;
            color: #8B4513;
            line-height: 1.85;
            margin: 0;
        }

        .service-icon-box {
            width: 100%;
            aspect-ratio: 4 / 3;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e88e5 0%, #00bcd4 100%);
            font-size: 68px;
            color: white;
            box-shadow: 0 8px 24px rgba(30, 136, 229, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .service-card.teal .service-icon-box {
            background: linear-gradient(135deg, #00bcd4 0%, #00897b 100%);
            box-shadow: 0 8px 24px rgba(0, 188, 212, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        /* Featured Section */
        .featured-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 70px 60px;
            border-radius: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 70px;
            align-items: center;
            box-shadow: 0 4px 20px rgba(43, 17, 219, 0.08);
            overflow: hidden;
            position: relative;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #e8eef7;
        }

        .featured-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 100% 0%, rgba(255,255,255,0.4) 0%, transparent 70%);
            pointer-events: none;
        }

        .featured-content {
            position: relative;
            z-index: 2;
        }

        .featured-badge {
            display: inline-block;
            background: linear-gradient(135deg, #00D7B3 0%, #00C99A 100%);
            color: white;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.2px;
            margin-bottom: 24px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.3);
        }

        .featured-content h3 {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 12px;
            color: #2B11DB;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .featured-content h3::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #2B11DB 0%, #00d4aa 100%);
            margin-top: 16px;
            margin-bottom: 24px;
            border-radius: 2px;
        }

        .featured-meta {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            flex-wrap: wrap;
        }

        .featured-discount {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .featured-discount-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .featured-offer-text {
            color: #ff6b6b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .featured-event-info {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .featured-event-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #333;
        }

        .featured-event-detail strong {
            color: #1a1a1a;
            font-weight: 600;
        }

        .featured-event-detail i {
            color: #2B11DB;
            font-size: 16px;
        }

        .featured-content p {
            color: #555;
            margin-bottom: 32px;
            line-height: 1.9;
            font-size: 16px;
            font-weight: 500;
        }

        .featured-btn {
            background: linear-gradient(135deg, #2B11DB 0%, #1e0aa3 100%);
            color: white;
            padding: 14px 42px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(43, 17, 219, 0.3);
            letter-spacing: 0.5px;
        }

        .featured-btn:hover {
            background: linear-gradient(135deg, #3d1ffa 0%, #2B11DB 100%);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(43, 17, 219, 0.4);
        }

        .featured-btn:active {
            transform: translateY(-1px);
        }

        .featured-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 400px;
            background: #fff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            box-shadow: 0 20px 40px rgba(43, 17, 219, 0.15);
            position: relative;
            z-index: 2;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid #e8eef7;
        }

        .featured-image img {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            border-radius: 12px;
        }

        .featured-image video {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 12px;
        }

        .featured-image iframe {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            border-radius: 12px;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #2209c9 0%, #2b11db 52%, #1b0893 100%);
            color: #eef1ff;
            padding: 56px 0 0;
            margin-top: auto;
            width: 100%;
            position: relative;
            border-top: 1px solid rgba(255, 255, 255, 0.14);
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            inset: -180px -200px auto auto;
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 72%);
            pointer-events: none;
        }

        .footer-content {
            max-width: 1460px;
            margin: 0 auto;
            padding: 0 18px 20px;
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            gap: 34px;
        }

        .footer-main-grid {
            display: grid;
            grid-template-columns: minmax(240px, 1.25fr) minmax(220px, 1fr) minmax(220px, 1fr) minmax(200px, 1fr);
            gap: 90px;
            align-items: start;
        }

        .footer-brand-logo {
            display: inline-block;
            margin-bottom: 12px;
        }

        .footer-brand-logo img {
            width: 228px;
            max-width: 100%;
            height: auto;
            filter: brightness(0) invert(1);
        }

        .footer-brand-blurb {
            font-size: 10px;
            line-height: 1.58;
            margin: 0;
            color: rgba(239, 243, 255, 0.9);
            max-width: 330px;
        }

        .footer-col-title {
            margin: 4px 0 14px;
            color: #ffffff;
            font-size: 10px;
            line-height: 1.05;
            letter-spacing: 0.5px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .footer-contact-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: rgba(240, 244, 255, 0.92);
            font-size: 10px;
            line-height: 1.5;
        }

        .footer-contact-item i {
            color: #dde4ff;
            font-size: 10px;
            margin-top: 4px;
            flex-shrink: 0;
        }

        .footer-nav-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .footer-nav-links a {
            color: rgba(255, 255, 255, 0.96);
            text-decoration: none;
            font-size: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            width: fit-content;
            line-height: 1.2;
            background: transparent !important;
        }

        .footer-nav-links a::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ffffff;
            transition: width 0.3s ease;
        }

        .footer-nav-links a:hover::after {
            width: 100%;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.16);
            padding: 18px 86px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 14px;
            position: relative;
        }

        .footer-copyright {
            font-size: 10px;
            opacity: 0.95;
            font-weight: 500;
            margin: 0;
            color: rgba(243, 247, 255, 0.95);
            width: 100%;
            text-align: center;
        }

        .footer-copyright strong {
            color: #ffffff;
            font-weight: 700;
        }

        .footer-scroll-top {
            position: absolute;
            right: 26px;
            bottom: 20px;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            border: none;
            background: #f1f4ff;
            color: #2b11db;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: transform 0.24s ease, box-shadow 0.24s ease;
            z-index: 2;
        }

        .footer-scroll-top:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 26px rgba(0, 0, 0, 0.28);
        }

        @media (max-width: 1180px) {
            .footer-main-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .footer-col-title {
                font-size: 10px;
            }

            .footer-nav-links a {
                font-size: 10px;
            }

            .footer-copyright {
                font-size: 10px;
            }
        }

        @media (max-width: 768px) {
            footer {
                padding-top: 24px;
            }

            .footer-content {
                padding: 0 14px 8px;
                gap: 12px;
            }

            .footer-main-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .footer-brand-col {
                grid-column: 1 / -1;
            }

            .footer-main-grid > .footer-col:last-child {
                grid-column: 1 / -1;
            }

            .footer-brand-logo {
                margin-bottom: 6px;
            }

            .footer-brand-logo img {
                width: min(170px, 62vw);
                max-height: 56px;
                object-fit: contain;
                object-position: left center;
            }

            .footer-brand-blurb {
                font-size: 10px;
                line-height: 1.35;
                max-width: none;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .footer-col {
                background: rgba(255, 255, 255, 0.06);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 12px;
                padding: 10px 9px;
            }

            .footer-contact-list {
                gap: 6px;
            }

            .footer-contact-item {
                gap: 6px;
                font-size: 10px;
                line-height: 1.35;
            }

            .footer-contact-item span {
                overflow-wrap: anywhere;
                word-break: break-word;
            }

            .footer-contact-item i {
                font-size: 10px;
                margin-top: 3px;
            }

            .footer-col-title {
                font-size: 10px;
                margin: 0 0 8px;
            }

            .footer-nav-links {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 6px;
            }

            .footer-nav-links a {
                font-size: 10px;
                font-weight: 700;
                width: 100%;
                padding: 5px 7px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.08) !important;
                line-height: 1.25;
            }

            .footer-nav-links a::after {
                display: none;
            }

            .footer-bottom {
                align-items: center;
                padding: 10px 0 52px;
                padding-right: 0;
                padding-left: 0;
            }

            .footer-copyright {
                font-size: 10px;
            }

            .footer-scroll-top {
                width: 36px;
                height: 36px;
                right: 12px;
                bottom: 10px;
            }
        }

        @media (max-width: 420px) {
            .footer-main-grid {
                grid-template-columns: 1fr;
            }

            .footer-main-grid > .footer-col:last-child {
                grid-column: auto;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding-top: 176px;
            }

            #brand-page {
                padding-top: 18px !important;
            }

            /* Single row: logo | search | inquiry | contact */
            .header-top {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                align-items: center;
                gap: 8px;
                padding: 0 10px;
                margin-bottom: 8px;
            }

            .logo {
                flex: 0 0 auto;
            }

            .logo-box img {
                height: 36px;
            }

            .search-bar {
                position: static;
                transform: none;
                flex: 1 1 0;
                min-width: 0;
                width: auto;
                max-width: none;
                margin: 0;
            }

            .search-bar .search-field {
                width: 100%;
            }

            .search-bar input {
                width: 100%;
                height: 36px;
                font-size: 12px;
                padding: 6px 8px 6px 30px;
            }

            .search-bar .search-field i {
                font-size: 13px;
                left: 8px;
            }

            .right-actions {
                flex: 0 0 auto;
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 8px;
                margin-left: 8px;
                margin-right: 8px;
                padding-right: 8px;
            }

            .inquiry-btn,
            .cart-icon-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 6px !important;
                font-size: 28px !important;
                position: relative;
            }

            .inquiry-btn .btn-text { display: none; }
            .inquiry-btn .btn-icon { font-size: 28px; }

            .cart-badge {
                background: #2196F3 !important;
                box-shadow: 0 2px 8px rgba(33,150,243,0.5) !important;
                width: 26px !important;
                height: 26px !important;
                font-size: 13px !important;
                position: absolute !important;
                top: -4px !important;
                right: -8px !important;
                margin-left: 0 !important;
            }

            .cart-badge.hidden { display: inline-flex !important; }

            .header-contact {
                display: flex;
            }

            nav ul {
                flex-wrap: wrap;
                gap: 0;
                width: 100%;
            }

            nav li {
                margin-right: 0;
            }

            .nav-inner {
                padding-left: 0;
                padding-right: 0;
                gap: 0;
                min-height: auto;
                overflow: hidden;
                justify-content: center;
                flex-wrap: wrap;
                width: 100%;
                overscroll-behavior-x: contain;
            }

            .nav-inner::-webkit-scrollbar { display: none; }

            .nav-list {
                gap: 0;
                flex-wrap: wrap;
                flex-shrink: 1;
                justify-content: center;
                width: 100%;
                overflow: hidden;
            }

            .nav-list > li > a {
                white-space: normal;
                font-size: 11px;
                padding: 10px 8px;
            }

            .browse-toggle {
                font-size: 12px;
                padding: 6px 8px;
                gap: 4px;
            }

            .hero h1 {
                font-size: 32px;
            }
            
            .hero {
                aspect-ratio: auto;
                min-height: 260px;
                padding: 20px 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .hero-content {
                max-width: 100%;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .brand-products-title {
                margin-top: 6px;
            }

            .products-toolbar {
                margin-top: 8px;
            }

            .hero-slide {
                width: 92% !important;
                left: 50% !important;
                transform: translateX(-50%) scale(1) !important;
                filter: blur(0) !important;
                opacity: 0 !important;
            }

            .hero-slide.active {
                width: 92% !important;
                left: 50% !important;
                transform: translateX(-50%) scale(1) !important;
                filter: blur(0) !important;
                opacity: 1 !important;
            }

            .hero-slide.prev,
            .hero-slide.next {
                opacity: 0 !important;
                pointer-events: none;
            }

            .hero-thumb {
                width: 100%;
                height: auto;
                max-width: 100%;
                aspect-ratio: 16 / 9 !important;
            }
            
            .product-image {
                aspect-ratio: 4 / 3;
                min-height: 240px;
            }
            
            .featured-image {
                aspect-ratio: 4 / 3;
                min-height: 260px;
            }

            .featured-section {
                grid-template-columns: 1fr;
                padding: 40px 28px;
                gap: 40px;
                border-radius: 16px;
            }

            .featured-content h3 {
                font-size: 28px;
                font-weight: 800;
            }

            .featured-meta {
                gap: 12px;
                padding-bottom: 12px;
            }

            .featured-event-info {
                gap: 12px;
            }

            .featured-event-detail {
                font-size: 13px;
            }

            .featured-btn {
                padding: 12px 32px;
                font-size: 14px;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .services-grid {
                gap: 24px;
            }

            .service-card {
                grid-template-columns: 1fr;
                gap: 24px;
                padding: 24px;
            }

            .service-card.reverse {
                direction: ltr;
            }

            .service-badge {
                margin-bottom: 8px;
                font-size: 11px;
            }

            .service-content h3 {
                font-size: 20px;
                margin-bottom: 12px;
            }

            .service-content p {
                font-size: 14px;
                line-height: 1.7;
            }

            .service-icon-box {
                aspect-ratio: 1 / 1;
                font-size: 48px;
            }

            section h2 {
                font-size: 28px;
            }

            .section-description {
                font-size: 14px;
                margin-bottom: 28px;
            }
        }

        /* ============================================
           ANIMATIONS
           ============================================ */

        /* 1. HOVER EFFECTS */
        @keyframes hoverGlow {
            0% { box-shadow: 0 0 0px rgba(0, 212, 170, 0); }
            100% { box-shadow: 0 0 20px rgba(0, 212, 170, 0.4); }
        }

        @keyframes hoverScale {
            from { transform: scale(1); }
            to { transform: scale(1.05); }
        }

        @keyframes buttonBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .product-card {
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            opacity: 1;
            transform: translateY(0);
            will-change: transform, opacity, box-shadow;
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 25px 50px rgba(43,17,219,0.2);
            z-index: 1000;
        }

        .featured-btn:hover,
        .cta-button:hover {
            animation: buttonBounce 0.6s ease;
        }

        .nav-list a:hover {
            animation: hoverScale 0.3s ease;
        }

        .inquiry-btn:hover {
            animation: hoverGlow 0.4s ease forwards;
        }

        /* 2. SCROLLING ANIMATIONS */
        /* Use shared fadeUp keyframe for consistent reveals */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .scroll-animate { opacity: 0; transform: translateY(40px); transition: opacity 0s ease, transform 0s ease; }

        /* Match brands.php staggered reveal timings (faster) */
        .product-card { opacity: 1; transform: translateY(0); will-change: transform,opacity; }
        .product-card:nth-of-type(1){ --i:1; }
        .product-card:nth-of-type(2){ --i:2; }

        section h2 { opacity: 1; }
        .section-description { opacity: 1; }
        .featured-section { opacity: 1; }

        /* 3. PAGE TRANSITIONS */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pageExit {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        body {
            opacity: 1;
        }

        section {
            opacity: 1;
        }

        section:nth-of-type(1) { animation-delay: 0s; }
        section:nth-of-type(2) { animation-delay: 0.1s; }
        section:nth-of-type(3) { animation-delay: 0.2s; }
        section:nth-of-type(4) { animation-delay: 0.3s; }

        /* 4. SELF-DRAWING ANIMATIONS */
        @keyframes drawBorder {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(0, 212, 170, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 212, 170, 0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .featured-badge {
            animation: pulseGlow 2s infinite;
        }

        .product-image {
            position: relative;
            overflow: hidden;
        }

        .product-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        /* 5. TEXT ANIMATIONS */
        @keyframes typeWriter {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }

        @keyframes blinkCursor {
            0%, 49% {
                border-right-color: transparent;
            }
            50%, 100% {
                border-right-color: #00d4aa;
            }
        }

        @keyframes textGradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes textFadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            animation: textFadeIn 0.8s ease;
        }

        .hero p {
            animation: textFadeIn 0.8s ease 0.2s both;
        }

        .product-info h3,
        .featured-content h3 {
            animation: textFadeIn 0.6s ease;
            position: relative;
        }

        
        .footer-nav-links a {
            position: relative;
            animation: textFadeIn 0.6s ease;
        }

        /* Stagger text animations */
        .nav-list li { opacity: 1; }

        .nav-list li:nth-child(1) { animation-delay: 0.1s; }
        .nav-list li:nth-child(2) { animation-delay: 0.2s; }
        .nav-list li:nth-child(3) { animation-delay: 0.3s; }
        .nav-list li:nth-child(4) { animation-delay: 0.4s; }
        .nav-list li:nth-child(5) { animation-delay: 0.5s; }
        .nav-list li:nth-child(6) { animation-delay: 0.6s; }

        /* Smooth transitions for all interactive elements */
        a, button, input, [role="button"] {
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @media (max-width: 768px) {
            .main-wrapper {
                grid-template-columns: 1fr;
                padding: 0 12px;
            }

            html, body { overflow-x: hidden; overflow-y: auto; }
            header { position: fixed; top: 0; left: 0; right: 0; width: 100%; }
            .nav-inner {
                padding-left: 0;
                padding-right: 0;
                min-height: 40px;
                overflow: hidden;
                justify-content: center;
                flex-wrap: wrap;
                width: 100%;
                overscroll-behavior-x: contain;
            }
            .nav-list {
                position: static;
                transform: none;
                left: auto;
                width: 100%;
                flex-wrap: wrap;
                flex-shrink: 1;
                justify-content: center;
                gap: 0;
                overflow: hidden;
            }
            .browse-toggle { display: none; }
        }

        /* Global animation utilities (shared) */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

        .reveal-hidden { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease; }
        .reveal { opacity: 1; transform: none; }
        .reveal-stagger > * { opacity: 0; transform: translateY(18px); }
        .reveal-stagger.revealed > * { opacity: 1; transform: none; transition: all .48s ease; }

        h1, .page-title { opacity: 1; }
        h1 + p, .page-subtitle { opacity: 1; }
        img:not(.no-anim) { opacity: 1; }

        @media (prefers-reduced-motion: reduce) {
            .reveal, .reveal-hidden, img { animation: none !important; transition: none !important; }
        }
        /* Ensure header/navigation/footer do not animate or move */
        header, nav, footer, .header-top, .nav-inner, .browse-toggle, .nav-list, .right-actions, .footer-content {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        /* Prevent individual nav items from receiving reveal animations */
        .nav-list li { animation: none !important; opacity: 1 !important; transform: none !important; }


        /* Breadcrumb */
        .brand-breadcrumb-wrap {
            display: flex;
            justify-content: flex-start;
            padding: 22px 20px 10px;
        }
        .brand-breadcrumb {
            background: #00bfb3;
            color: #fff;
            border-radius: 30px;
            padding: 6px 18px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0,191,179,0.25);
        }
        .brand-breadcrumb a {
            color: rgba(255,255,255,0.85);
            text-decoration: none;
        }
        .brand-breadcrumb a:hover { text-decoration: underline; }
        .brand-breadcrumb .sep { opacity: 0.7; }
        .brand-breadcrumb .current { color: #fff; font-weight: 600; }
        .brand-breadcrumb #bc-brand-name:not(.current) { color: rgba(255,255,255,0.75); font-weight: 400; cursor: pointer; }
        .brand-breadcrumb #bc-brand-name:not(.current):hover { text-decoration: underline; }
        .brand-breadcrumb .bc-icon { font-size: 13px; }

        /* Brand Header Card */
        #brand-page {
            padding-top: 24px !important;
        }
        .brand-header-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 32px 36px;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 28px;
            margin: 0 0 28px;
            text-align: left;
        }
        .brand-logo-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 320px;
            min-height: 180px;
        }
        .brand-logo-wrap img {
            max-width: 82%;
            max-height: 100%;
            width: auto;
            object-fit: contain;
        }
        .brand-logo-placeholder {
            width: 100%;
            height: 100%;
            background: #f0f0f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            color: #aaa;
        }
        .brand-header-content {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
            min-width: 0;
            justify-content: center;
        }
        .brand-header-tagline {
            font-size: 14px;
            color: #888;
            margin-bottom: 6px;
        }
        .brand-description-card {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0;
            box-shadow: none;
            display: block;
            width: 100%;
            align-self: center;
            height: auto;
        }
        .brand-header-desc {
            font-size: 15px;
            line-height: 1.6;
            color: #222;
        }
        .brand-header-desc table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        .brand-header-desc th,
        .brand-header-desc td {
            border: 1px solid #d1d5db;
            padding: 7px 9px;
            vertical-align: top;
            text-align: left;
        }
        .brand-header-desc img {
            max-width: 100%;
            height: auto;
        }

        /* Products Section */
        .brand-products-section {
            margin-bottom: 40px;
        }
        .brand-products-title {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #2B11DB;
            margin-bottom: 4px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e0e0e0;
        }

        /* Products Layout */
        .brand-products-layout {
            display: flex;
            gap: 22px;
            margin-top: 20px;
        }

        /* Filter Sidebar */
        .brand-filter-sidebar {
            width: 200px;
            flex-shrink: 0;
        }
        .filter-group {
            margin-bottom: 22px;
        }
        .filter-group-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #888;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }
        .filter-group-title::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #2B11DB;
            border-radius: 50%;
            display: inline-block;
        }
        .filter-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 4px;
            cursor: pointer;
            border-radius: 4px;
            transition: background 0.15s;
        }
        .filter-item:hover { background: #f5f5f5; }
        .filter-item input[type="checkbox"] {
            accent-color: #2B11DB;
            width: 14px;
            height: 14px;
            cursor: pointer;
        }
        .filter-item label {
            font-size: 13px;
            color: #2B11DB;
            cursor: pointer;
            flex: 1;
        }
        .filter-count {
            font-size: 11px;
            color: #999;
            background: #f0f0f0;
            border-radius: 10px;
            padding: 1px 6px;
        }
        .clear-filters-btn {
            width: 100%;
            background: #e74c3c;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            margin-top: 8px;
            text-transform: uppercase;
            transition: background 0.2s;
        }
        .clear-filters-btn:hover { background: #c0392b; }

        /* Products Main Area */
        .brand-products-main {
            flex: 1;
            min-width: 0;
        }
        .products-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .products-search-wrap {
            flex: 1;
            min-width: 160px;
            position: relative;
        }
        .products-search-wrap input {
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 14px 8px 36px;
            font-size: 13px;
            outline: none;
            transition: border 0.2s;
        }
        .products-search-wrap input:focus { border-color: #2B11DB; }
        .products-search-wrap::before {
            content: '\F52A';
            font-family: 'bootstrap-icons';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 14px;
        }
        .products-sort {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #555;
        }
        .products-sort select {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 13px;
            cursor: pointer;
            outline: none;
        }
        .view-toggle {
            display: flex;
            gap: 4px;
        }
        .view-btn {
            background: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #888;
            font-size: 15px;
            transition: all 0.2s;
        }
        .view-btn.active, .view-btn:hover {
            background: #2B11DB;
            color: #fff;
            border-color: #2B11DB;
        }
        .products-count {
            font-size: 13px;
            color: #888;
            margin-left: auto;
        }

        /* Product Grid */
        .brand-product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .brand-product-grid.list-view {
            grid-template-columns: 1fr;
        }
        .brand-product-card {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .brand-product-card:hover {
            box-shadow: 0 4px 16px rgba(43,17,219,0.1);
            transform: translateY(-2px);
        }
        .brand-product-img {
            width: 100%;
            height: 140px;
            background: #fff;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .brand-product-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .brand-product-card:hover .brand-product-img img {
            /* transform: scale(1.2); Removed to prevent zooming on hover */
        }
        .brand-product-img .no-img-icon {
            font-size: 32px;
            color: #ccc;
        }
        .brand-product-name {
            font-size: 13px;
            font-weight: 600;
            color: #2B11DB;
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .brand-product-type {
            font-size: 12px;
            color: #888;
            margin-bottom: 12px;
        }
        .brand-add-inquiry {
            background: #2B11DB;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 9px 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            width: 100%;
            margin-top: auto;
            transition: background 0.2s;
            text-transform: uppercase;
        }
        .brand-add-inquiry:hover { background: #1f0aa1; }
        .brand-add-inquiry.added { background: #4caf50; }

        /* List view card */
        .brand-product-grid.list-view .brand-product-card {
            flex-direction: row;
            text-align: left;
            gap: 16px;
        }
        .brand-product-grid.list-view .brand-product-img {
            width: 100px;
            height: 80px;
            flex-shrink: 0;
            margin-bottom: 0;
        }
        .brand-product-grid.list-view .brand-product-info {
            flex: 1;
        }
        .brand-product-grid.list-view .brand-add-inquiry {
            width: auto;
            flex-shrink: 0;
        }

        /* Pagination */
        .brand-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 28px;
        }
        .page-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            transition: all 0.2s;
        }
        .page-btn:hover:not(:disabled) { border-color: #2B11DB; color: #2B11DB; }
        .page-btn.active { background: #2B11DB; color: #fff; border-color: #2B11DB; }
        .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
        .page-info {
            font-size: 13px;
            color: #888;
            padding: 0 10px;
        }

        /* No products */
        .no-brand-products {
            text-align: center;
            padding: 50px 20px;
            color: #aaa;
            font-size: 15px;
        }

        @media (max-width: 900px) {
            .brand-header-card { flex-direction: column; text-align: left; gap: 20px; padding: 24px 20px; }
            .brand-logo-wrap { flex-basis: auto; min-height: 0; align-self: center; }
            .brand-header-content { text-align: left; }
            .brand-header-tagline { text-align: left; }
            .brand-description-card { align-items: flex-start; justify-content: flex-start; }
            .brand-header-desc { text-align: left; }
            .brand-header-desc { font-size: 14px; }
            .brand-products-layout { flex-direction: column; }
            .brand-filter-sidebar { width: 100%; }
            .brand-product-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .brand-product-grid.list-view .brand-product-card {
                flex-direction: column;
                align-items: stretch;
                text-align: left;
                gap: 12px;
            }
            .brand-product-grid.list-view .brand-product-img {
                width: 100%;
                height: 150px;
                margin-bottom: 0;
            }
            .brand-product-grid.list-view .brand-product-info {
                width: 100%;
            }
            .brand-product-grid.list-view .brand-product-name {
                font-size: 12px;
                line-height: 1.35;
            }
            .brand-product-grid.list-view .brand-product-type {
                font-size: 11px;
                line-height: 1.3;
            }
            .brand-product-grid.list-view .brand-add-inquiry {
                width: 100%;
            }
            .brand-product-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<!-- GLOBAL LOADER -->
<style>
.global-page-loader { position: fixed; inset: 0; background: linear-gradient(135deg, rgba(43, 17, 219, 0.96) 0%, rgba(30, 48, 140, 0.98) 100%); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 999999; opacity: 1; visibility: visible; transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.global-page-loader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.gpl-box { width: min(320px, 88vw); padding: 32px 24px; border-radius: 24px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16); box-shadow: 0 24px 54px rgba(0, 0, 0, 0.4); display: flex; flex-direction: column; align-items: center; gap: 20px; transform: translateY(0); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.global-page-loader.is-hidden .gpl-box { transform: translateY(15px) scale(0.95); }
.gpl-logo { width: 140px; height: 140px; object-fit: contain; filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.4)); animation: gplPulse 2s ease-in-out infinite; }
.gpl-ring { width: 54px; height: 54px; border-radius: 50%; border: 4px solid rgba(255, 255, 255, 0.15); border-top-color: #00D7B3; animation: gplSpin 1s linear infinite; }
.gpl-text { margin: 0; color: #ffffff; font-size: 15px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; text-align: center; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
@keyframes gplPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.06); } }
@keyframes gplSpin { to { transform: rotate(360deg); } }
html.gpl-loading, html.gpl-loading body { overflow: hidden !important; }
</style>
<div id="globalPageLoader" class="global-page-loader" aria-hidden="false">
    <div class="gpl-box" role="status" aria-live="polite" aria-label="Loading page">
        <img class="gpl-logo" id="gplLogoImgTop" src="/ANDISON/assets/HOME/image-removebg-preview.png" alt="ANDISON Logo">
        <div class="gpl-ring" aria-hidden="true"></div>
        <p class="gpl-text">Loading...</p>
    </div>
</div>
<script>
(function() {
    var loader = document.getElementById('globalPageLoader');
    if (!loader) return;
    document.documentElement.classList.add('gpl-loading');
    var gplLogoTop = document.getElementById('gplLogoImgTop');
    if (gplLogoTop) {
        var base = window.location.pathname.split('/').slice(0, window.location.pathname.split('/').findIndex(p => p.toLowerCase() === 'andison') + 1).join('/');
        if (base && base !== '/') gplLogoTop.src = base + '/assets/HOME/image-removebg-preview.png';
    }
    
    function hideLoader() {
        if (loader.classList.contains('is-hidden')) return;
        loader.classList.add('is-hidden');
        loader.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('gpl-loading');
    }
    
    if (document.readyState === 'complete') {
        setTimeout(hideLoader, 150);
    } else {
        window.addEventListener('load', function() {
            setTimeout(hideLoader, 150);
        });
    }
    
    setTimeout(hideLoader, 5000);
    
    document.addEventListener('click', function(e) {
        var target = e.target.closest('a');
        if (!target) return;
        var href = target.getAttribute('href');
        if (!href) return;
        if (href.startsWith('javascript:') || href.startsWith('#') || target.getAttribute('target') === '_blank' || href.startsWith('tel:') || href.startsWith('mailto:') || e.ctrlKey || e.shiftKey || e.metaKey || e.button !== 0) return;
        var isInternal = false;
        try {
            var url = new URL(target.href, window.location.href);
            if (url.origin === window.location.origin) isInternal = true;
        } catch (err) {}
        if (isInternal) {
            var currentUrl = new URL(window.location.href);
            var targetUrl = new URL(target.href);
            if (currentUrl.pathname === targetUrl.pathname && currentUrl.search === targetUrl.search && targetUrl.hash) return;
            loader.classList.remove('is-hidden');
            loader.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('gpl-loading');
        }
    });
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) hideLoader();
    });
})();
</script>
<!-- /GLOBAL LOADER -->
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    
        <?php
        // Set page title
        $page_title = "Services";
        $company_name = "ANDISON INDUSTRIAL";
        
        // Contact information
        $phone = "+1(234) 567 8900";
        $phone2 = "+1(234) 567 8900";
        $phone3 = "+1(639) 977 803 7398";
        $email = "info@andison-industrial.com";
    ?>

    <!-- Header -->
    <header> 
        <div class="header-top">
            <div class="logo">
                <div class="logo-box"><a href="home.php"><img src="assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
            </div>

            <div class="search-bar">
                <form class="search-field" action="search.php" method="get">
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
            </div>
        </div>

        <!-- Navigation -->
        <nav>
            <div class="nav-inner">
                <ul class="nav-list">
                    <li>
                        <a href="home.php">Home</a>
                        <div class="nav-dropdown">
                            <h4>Welcome</h4>
                            <p>Discover our complete range of industrial welding solutions and equipment.</p>
                        </div>
                    </li>
                    <li>
                        <a href="aboutus.php">About Us</a>
                        <div class="nav-dropdown">
                            <h4>Our Company</h4>
                            <ul>
                                <li><a href="aboutus.php#mission">Our Mission</a></li>
                                <li><a href="aboutus.php#history">Company History</a></li>
                                <li><a href="aboutus.php#team">Our Team</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="brands.php" class="active">Brands</a>
                        <div class="nav-dropdown">
                            <h4>Featured Brands</h4>
                            <ul>
<?php $andisonNavBrandsPath = __DIR__ . '/includes/nav_brands_dropdown_items.php'; if (!is_file($andisonNavBrandsPath)) { $andisonNavBrandsPath = dirname(__DIR__) . '/includes/nav_brands_dropdown_items.php'; } if (!is_file($andisonNavBrandsPath)) { $andisonNavBrandsPath = dirname(dirname(__DIR__)) . '/includes/nav_brands_dropdown_items.php'; } if (is_file($andisonNavBrandsPath)) { include $andisonNavBrandsPath; } ?>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="industries.php">Industries</a>
                        <div class="nav-dropdown">
                            <h4>Industries We Serve</h4>
                            <ul>
                                <li><a href="industries.php#motor-vehicle">Motor Vehicle Industry</a></li>
                                <li><a href="industries.php#metal-fabrication">Metal Fabrication and Industrial</a></li>
                                <li><a href="industries.php#power-generation">Power Generation</a></li>
                                <li><a href="industries.php#oil-petrochemical">Oil and Petrochemical Industry</a></li>
                                <li><a href="industries.php#mining">Mining Industry</a></li>
                                <li><a href="industries.php#shipyard">Shipyard</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="services.php">Services</a>
                        <div class="nav-dropdown">
                            <h4>Our Services</h4>
                            <ul>
                                <li><a href="services.php#consultation">Technical Consultation</a></li>
                                <li><a href="services.php#training">Training Programs</a></li>
                                <li><a href="services.php#maintenance">Equipment Maintenance</a></li>
                                <li><a href="services.php#support">After-Sales Support</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="contact.php">Contact Us</a>
                        <div class="nav-dropdown">
                            <h4>Get In Touch</h4>
                            <p>Reach out to our team for inquiries, quotes, or technical support.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Brand Page Content -->
    <div class="page-content">

    <section id="brand-page">
        <div class="container">

            <!-- Brand Header -->
            <div class="brand-header-card">
                <div class="brand-logo-wrap">
                    <img src="<?php echo htmlspecialchars($brand_logo_src, ENT_QUOTES); ?>"
                         alt="<?php echo $brand_name; ?>"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="brand-logo-placeholder" style="display:none;"><?php echo $brand_name; ?></div>
                </div>
                <div class="brand-header-content">
                    <?php if (!empty($brand_info['tagline'])): ?>
                    <div class="brand-header-tagline"><?php echo htmlspecialchars($brand_info['tagline']); ?></div>
                    <?php endif; ?>
                    <?php if (trim((string)($brand_info['description'] ?? '')) !== ''): ?>
                    <div class="brand-description-card">
                        <div class="brand-header-desc"><?php echo andison_render_brand_description($brand_info['description'] ?? ''); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Breadcrumb -->
            <div class="brand-breadcrumb-wrap">
                <nav class="brand-breadcrumb">
                    <i class="bi bi-house-fill bc-icon"></i>
                    <a href="home.php">Home</a>
                    <span class="sep">/</span>
                    <a href="brands.php">Brands</a>
                    <span id="bc-sep-product" class="sep" style="display:none;">/</span>
                    <span id="bc-product-name" class="current" style="display:none;"></span>
                </nav>
            </div>

            <!-- Our Products -->
            <div class="brand-products-section">
                <h2 class="brand-products-title">Our Products</h2>

                <div class="brand-products-layout">

                    <!-- Products Main -->
                    <div class="brand-products-main">

                        <!-- Toolbar -->
                        <div class="products-toolbar">
                            <div class="products-search-wrap">
                                <input type="text" id="productSearch" placeholder="Search products...">
                            </div>
                            <div class="products-sort">
                                Sort by:
                                <select id="productSort">
                                    <option value="default">Default</option>
                                    <option value="az">A ? Z</option>
                                    <option value="za">Z ? A</option>
                                </select>
                            </div>
                            <div class="view-toggle">
                                <button class="view-btn active" id="gridViewBtn" title="Grid view"><i class="bi bi-grid-fill"></i></button>
                                <button class="view-btn" id="listViewBtn" title="List view"><i class="bi bi-list-ul"></i></button>
                            </div>
                        </div>

                        <!-- Product Grid -->
                        <?php if (!empty($brand_products)): ?>
                        <div class="brand-product-grid" id="brandProductGrid">
                            <?php foreach ($brand_products as $product):
                                $model        = is_array($product) ? (string)($product['model']          ?? '') : (string)$product;
                                $type         = is_array($product) ? (string)($product['type']           ?? '') : '';
                                $img          = is_array($product) ? (string)($product['image']          ?? '') : '';
                                // Normalize stored image paths so they resolve from /ANDISON/ root:
                                // ../assets/... → assets/...  |  andison/assets/brands... → assets/brands...
                                $img = preg_replace('/^(\.\.\/)+(?=assets\/)/i', '', $img);
                                if (preg_match('/^andison\/assets\/brands/i', $img) && !preg_match('/^andison\/assets\/uploads/i', $img)) {
                                    $img = preg_replace('/^andison\//i', '', $img);
                                }
                                $raw_badge    = is_array($product) ? ($product['badge'] ?? ($product['status'] ?? ($product['availability'] ?? ''))) : '';
                                $badge        = andison_normalize_badge_label($raw_badge);
                                $product_name = is_array($product) ? (string)($product['product_name']   ?? '') : '';
                                $description  = is_array($product) ? (string)($product['description']    ?? '') : '';
                                $card_subtitle = trim($product_name);
                                $specs_text   = is_array($product) ? (string)($product['specifications'] ?? '') : '';
                                $price        = is_array($product) ? (string)($product['price']          ?? '') : '';
                                $datasheet    = is_array($product) ? (string)($product['datasheet']      ?? '') : '';
                                $explicit_imgs = is_array($product) ? (array)($product['images'] ?? []) : [];
                                $explicit_imgs = array_map(function($p) {
                                    $p = preg_replace('/^(\.\.\/)+(?=assets\/)/i', '', $p);
                                    if (preg_match('/^andison\/assets\/brands/i', $p) && !preg_match('/^andison\/assets\/uploads/i', $p)) {
                                        $p = preg_replace('/^andison\//i', '', $p);
                                    }
                                    return $p;
                                }, $explicit_imgs);
                                $images_list   = andison_auto_images($img, $explicit_imgs, __DIR__);
                            ?>
                            <?php
                                $specs_arr  = is_array($product) ? ($product['specs'] ?? []) : [];
                                $specs_json = htmlspecialchars(json_encode($specs_arr), ENT_QUOTES);
                                $imgs_json  = htmlspecialchars(json_encode(array_values($images_list)), ENT_QUOTES);
                            ?>
                            <div class="brand-product-card"
                                 data-model="<?php echo htmlspecialchars($model, ENT_QUOTES); ?>"
                                 data-type="<?php echo htmlspecialchars($type, ENT_QUOTES); ?>"
                                   data-badge="<?php echo htmlspecialchars($badge, ENT_QUOTES); ?>"
                                 data-brand="<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>"
                                 data-image="<?php echo htmlspecialchars($img, ENT_QUOTES); ?>"
                                 data-images="<?php echo $imgs_json; ?>"
                                 data-specs="<?php echo $specs_json; ?>"
                                 data-product-name="<?php echo htmlspecialchars($product_name, ENT_QUOTES); ?>"
                                 data-description="<?php echo htmlspecialchars($description, ENT_QUOTES); ?>"
                                 data-specifications="<?php echo htmlspecialchars($specs_text, ENT_QUOTES); ?>"
                                 data-price="<?php echo htmlspecialchars($price, ENT_QUOTES); ?>"
                                 data-datasheet="<?php echo htmlspecialchars($datasheet, ENT_QUOTES); ?>"
                                 style="cursor:pointer;">
                                <div class="brand-product-img">
                                    <?php if ($img): ?>
                                        <img src="<?php echo $img; ?>"
                                             alt="<?php echo htmlspecialchars($model, ENT_QUOTES); ?>"
                                             onerror="this.style.display='none'; this.parentElement.querySelector('.no-img-icon').style.display='block';">
                                        <i class="bi bi-tools no-img-icon" style="display:none;"></i>
                                    <?php else: ?>
                                        <i class="bi bi-tools no-img-icon"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="brand-product-info">
                                    <?php if ($badge): ?>
                                        <span data-role="product-badge" style="font-size:11px;background:#2B11DB;color:#fff;padding:2px 7px;border-radius:3px;margin-bottom:6px;display:inline-block;"><?php echo htmlspecialchars($badge); ?></span>
                                    <?php endif; ?>
                                    <div class="brand-product-name"><?php echo htmlspecialchars($model); ?></div>
                                    <?php if ($card_subtitle !== ''): ?>
                                        <div class="brand-product-type"><?php echo htmlspecialchars($card_subtitle); ?></div>
                                    <?php endif; ?>
                                </div>
                                <button class="brand-add-inquiry"
                                        data-model="<?php echo htmlspecialchars($model, ENT_QUOTES); ?>"
                                        data-type="<?php echo htmlspecialchars($type, ENT_QUOTES); ?>"
                                        data-brand="<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>">
                                    ADD TO INQUIRY LIST
                                </button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="no-brand-products">
                            <i class="bi bi-box-seam" style="font-size:40px;display:block;margin-bottom:12px;"></i>
                            No products available for this brand yet.
                        </div>
                        <?php endif; ?>

                        <!-- Pagination -->
                        <div class="brand-pagination" id="brandPagination"></div>

<script>
var BRAND_NAME = '<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>';
var BRAND_LOGO = '<?php echo htmlspecialchars($brand_logo_src, ENT_QUOTES); ?>';
</script>
                    </div>
                </div>
            </div>

        </div>
    </section>
    </div><!-- /.page-content -->

<?php require_once __DIR__ . '/includes/product_modal.php'; ?>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="#sitemap">Sitemap</a>
                <a href="contact.php">Contact Us</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2026 Andison Industrial Sales Inc. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <?php require_once __DIR__ . '/includes/footer_modernize.php'; ?>

    <script>
    // ===== BRAND PAGE: PRODUCTS LOGIC =====
    (function(){
        var grid        = document.getElementById('brandProductGrid');
        var searchInput = document.getElementById('productSearch');
        var sortSel     = document.getElementById('productSort');
        var countEl     = document.getElementById('productsCount');
        var pagination  = document.getElementById('brandPagination');
        var gridBtn     = document.getElementById('gridViewBtn');
        var listBtn     = document.getElementById('listViewBtn');
        var clearBtn    = document.getElementById('clearFiltersBtn');
        var ITEMS_PER_PAGE = 9;
        var currentPage = 1;

        if (!grid) return;

        var allCards = Array.from(grid.querySelectorAll('.brand-product-card'));

        // View toggle
        if (gridBtn) gridBtn.addEventListener('click', function(){
            grid.classList.remove('list-view');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        });
        if (listBtn) listBtn.addEventListener('click', function(){
            grid.classList.add('list-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        });

        // Filter + search + sort
        function getSelectedTypes(){
            return Array.from(document.querySelectorAll('.filter-type-check:checked')).map(function(el){ return el.value.toLowerCase(); });
        }
        function getSearchQuery(){
            return searchInput ? searchInput.value.trim().toLowerCase() : '';
        }
        function getSortOrder(){
            return sortSel ? sortSel.value : 'default';
        }

        function filterAndRender(){
            currentPage = 1;
            renderPage();
        }

        function renderPage(){
            var query       = getSearchQuery();
            var types       = getSelectedTypes();
            var catAllChk   = document.getElementById('cat-all');
            var showAll     = catAllChk && catAllChk.checked;
            var sort        = getSortOrder();

            // Filter
            var visible = allCards.filter(function(card){
                var model = card.getAttribute('data-model').toLowerCase();
                var type  = card.getAttribute('data-type').toLowerCase();
                var matchSearch = !query || model.includes(query) || type.includes(query);
                var matchType   = types.length === 0 || types.some(function(t){ return type.includes(t); });
                return matchSearch && matchType;
            });

            // Sort
            if (sort === 'az') {
                visible.sort(function(a,b){ return a.getAttribute('data-model').localeCompare(b.getAttribute('data-model')); });
            } else if (sort === 'za') {
                visible.sort(function(a,b){ return b.getAttribute('data-model').localeCompare(a.getAttribute('data-model')); });
            }

            // Pagination
            var totalPages = Math.max(1, Math.ceil(visible.length / ITEMS_PER_PAGE));
            if (currentPage > totalPages) currentPage = totalPages;
            var start = (currentPage - 1) * ITEMS_PER_PAGE;
            var end   = start + ITEMS_PER_PAGE;
            var shownOnPage = Math.max(0, Math.min(end, visible.length) - start);

            // Show/hide cards
            allCards.forEach(function(c){ c.style.display = 'none'; });
            visible.forEach(function(c, i){
                c.style.display = (i >= start && i < end) ? '' : 'none';
                // Re-append to preserve DOM sort order in grid
                if (i >= start && i < end) grid.appendChild(c);
            });

            // Count
            if (countEl) countEl.textContent = 'Showing ' + shownOnPage + ' of ' + visible.length + ' product' + (visible.length !== 1 ? 's' : '');

            // Ensure badges remain visible even if any external script modifies card internals.
            ensureCardBadges(allCards);

            // Build pagination
            buildPagination(totalPages, visible.length);
        }

        function ensureCardBadges(cards){
            (cards || []).forEach(function(card){
                if (!card) return;
                var badgeVal = (card.getAttribute('data-badge') || '').trim();
                var info = card.querySelector('.brand-product-info');
                if (!info) return;

                var existing = info.querySelector('[data-role="product-badge"]');
                if (badgeVal === '') {
                    if (existing) existing.remove();
                    return;
                }

                if (!existing) {
                    existing = document.createElement('span');
                    existing.setAttribute('data-role', 'product-badge');
                    var nameNode = info.querySelector('.brand-product-name');
                    if (nameNode) {
                        info.insertBefore(existing, nameNode);
                    } else {
                        info.prepend(existing);
                    }
                }

                existing.textContent = badgeVal;
                existing.style.cssText = 'font-size:11px;background:#2B11DB;color:#fff;padding:2px 7px;border-radius:3px;margin-bottom:6px;display:inline-block;';
            });
        }

        function buildPagination(totalPages, total){
            if (!pagination) return;
            pagination.innerHTML = '';
            if (totalPages <= 1) return;

            // Prev
            var prev = document.createElement('button');
            prev.className = 'page-btn';
            prev.innerHTML = '&#8249;';
            prev.disabled = currentPage === 1;
            prev.addEventListener('click', function(){ if(currentPage > 1){ currentPage--; renderPage(); }});
            pagination.appendChild(prev);

            // Pages
            for (var i = 1; i <= totalPages; i++){
                (function(p){
                    var btn = document.createElement('button');
                    btn.className = 'page-btn' + (p === currentPage ? ' active' : '');
                    btn.textContent = p;
                    btn.addEventListener('click', function(){ currentPage = p; renderPage(); });
                    pagination.appendChild(btn);
                })(i);
            }

            // Next
            var next = document.createElement('button');
            next.className = 'page-btn';
            next.innerHTML = '&#8250;';
            next.disabled = currentPage === totalPages;
            next.addEventListener('click', function(){ if(currentPage < totalPages){ currentPage++; renderPage(); }});
            pagination.appendChild(next);

            // Page info
            var info = document.createElement('span');
            info.className = 'page-info';
            info.textContent = 'Page ' + currentPage + ' of ' + totalPages;
            pagination.appendChild(info);
        }

        // Event listeners
        if (searchInput) searchInput.addEventListener('input', filterAndRender);
        if (sortSel)     sortSel.addEventListener('change', filterAndRender);
        document.querySelectorAll('.filter-type-check, .filter-tag-check, #cat-all').forEach(function(chk){
            chk.addEventListener('change', filterAndRender);
        });
        if (clearBtn) clearBtn.addEventListener('click', function(){
            document.querySelectorAll('.filter-type-check, .filter-tag-check').forEach(function(c){ c.checked = false; });
            var catAll = document.getElementById('cat-all');
            if (catAll) catAll.checked = true;
            if (searchInput) searchInput.value = '';
            if (sortSel) sortSel.value = 'default';
            filterAndRender();
        });

        // -- Product Detail Modal ï¿½ open on product card click --
        grid.addEventListener('click', function(e){
            if (e.target.closest('.brand-add-inquiry')) return;
            var card = e.target.closest('.brand-product-card');
            if (card) openProductModal(card);
        });

        // Inquiry buttons
        grid.addEventListener('click', function(e){
            var btn = e.target.closest('.brand-add-inquiry');
            if (!btn) return;
            var modelVal = btn.getAttribute('data-model') || '';
            var typeVal  = btn.getAttribute('data-type')  || '';
            var brandVal = btn.getAttribute('data-brand') || '';
            var imageVal = btn.closest('.brand-product-card') ? (btn.closest('.brand-product-card').getAttribute('data-image') || '') : '';
            var list = [];
            try { list = JSON.parse(localStorage.getItem('inquiryItems') || '[]'); } catch(err){}
            var exists = list.some(function(x){ return x.model === modelVal && x.brand === brandVal; });
            if (!exists) {
                list.push({
                    model:       modelVal,
                    name:        modelVal,
                    description: typeVal,
                    brand:       brandVal,
                    image:       imageVal,
                    qty:         1,
                    timestamp:   new Date().getTime()
                });
                localStorage.setItem('inquiryItems', JSON.stringify(list));
                window.dispatchEvent(new Event('inquiryItemsUpdated'));
            }
            btn.textContent = exists ? 'ALREADY ADDED' : 'ADDED!';
            btn.classList.add('added');
            setTimeout(function(){ btn.textContent = 'ADD TO INQUIRY LIST'; btn.classList.remove('added'); }, 2000);
        });

        // Initial render
        renderPage();
        ensureCardBadges(allCards);
    })();
    </script>

    <script>
        // Manage aria states for contact dropdown (improves accessibility)
        (function(){
            var dropdowns = document.querySelectorAll('.contact-dropdown');
            dropdowns.forEach(function(dd){
                var pop = dd.querySelector('.contact-popover');
                var link = dd.querySelector('.contact-link');
                dd.addEventListener('keydown', function(e){
                    if(e.key === 'Escape') { link.blur(); pop.setAttribute('aria-hidden','true'); }
                });
                dd.addEventListener('focusin', function(){ pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true'); });
                dd.addEventListener('focusout', function(){ setTimeout(function(){ if(!dd.contains(document.activeElement)){ pop.setAttribute('aria-hidden','true'); dd.setAttribute('aria-expanded','false'); } }, 10); });
                dd.addEventListener('mouseenter', function(){ 
                    if(dd.classList.contains('closed')) return;
                    pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true'); 
                });
                dd.addEventListener('mouseleave', function(){ pop.setAttribute('aria-hidden','true'); dd.setAttribute('aria-expanded','false'); dd.classList.remove('closed'); });

                // Mobile: click to toggle
                dd.addEventListener('click', function(e){
                    if(window.innerWidth > 768) return;
                    e.stopPropagation();
                    var isOpen = dd.classList.contains('open');
                    document.querySelectorAll('.contact-dropdown').forEach(function(d){ d.classList.remove('open'); });
                    if(!isOpen) dd.classList.add('open');
                });

                // Close button
                var closeBtn = dd.querySelector('.contact-close');
                if(closeBtn){
                    closeBtn.addEventListener('click', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        pop.setAttribute('aria-hidden','true');
                        dd.setAttribute('aria-expanded','false');
                        dd.classList.add('closed');
                        dd.classList.remove('open');
                        document.activeElement.blur();
                    });
                }
            });

            // Mobile: click outside closes all
            document.addEventListener('click', function(){
                if(window.innerWidth > 768) return;
                document.querySelectorAll('.contact-dropdown').forEach(function(d){ d.classList.remove('open'); });
            });
        })();
    </script>
    <script>
        // Hero slider functionality
        (function(){
            var slider = document.getElementById('heroSlider');
            if (!slider) return;

            var slides = slider.querySelectorAll('.hero-slide');
            var dots = slider.querySelectorAll('.hero-dot');
            if (!slides.length || !dots.length) return;

            var currentSlide = 0;
            var autoplayInterval;

            function showSlide(n) {
                slides.forEach(function(slide) { 
                    slide.classList.remove('active', 'prev', 'next'); 
                });
                dots.forEach(function(dot) { dot.classList.remove('active'); });
                
                var prevIndex = (n - 1 + slides.length) % slides.length;
                var nextIndex = (n + 1) % slides.length;
                
                slides[prevIndex].classList.add('prev');
                slides[n].classList.add('active');
                slides[nextIndex].classList.add('next');
                
                dots[n].classList.add('active');
                currentSlide = n;
            }

            function nextSlide() {
                showSlide((currentSlide + 1) % slides.length);
            }

            function goToSlide(n) {
                showSlide(n);
                clearInterval(autoplayInterval);
                autoplayInterval = setInterval(nextSlide, 5000);
            }

            // Dot click handlers
            dots.forEach(function(dot, index) {
                dot.addEventListener('click', function() {
                    goToSlide(index);
                });
            });

            // Initialize first slide
            showSlide(0);
            
            // Auto-play
            autoplayInterval = setInterval(nextSlide, 5000);
        })();
    </script>

    <script>
        // ============================================
        // SCROLL ANIMATIONS - Trigger animations when elements come into view
        // ============================================
        (function(){
            var observerOptions = {
                threshold: 0.15,
                rootMargin: '0px 0px -100px 0px'
            };

            var observer = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.classList.add('visible');
                        // Optional: stop observing once animated
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all elements with scroll-animate class
            var animatedElements = document.querySelectorAll('.scroll-animate, .product-card, section h2, .section-description, .featured-section');
            animatedElements.forEach(function(el){
                observer.observe(el);
            });

            // Stagger animations for product cards on page load
            setTimeout(function(){
                var cards = document.querySelectorAll('.product-card');
                cards.forEach(function(card, index){
                    setTimeout(function(){
                        card.style.opacity = '1';
                    }, index * 150);
                });
            }, 300);
        })();
    </script>

    <script>
        // ============================================
        // BRAND DROPDOWN NAVIGATION (priority handler)
        // ============================================
        (function(){
            // Handle brand dropdown clicks with immediate navigation
            document.addEventListener('click', function(e){
                // Check if click is within brands dropdown
                var brandLink = e.target.closest('.nav-list li:nth-child(3) .nav-dropdown a');
                if(brandLink){
                    e.preventDefault();
                    e.stopPropagation();
                    var href = brandLink.getAttribute('href');
                    if(href){
                        window.location.href = href;
                    }
                    return;
                }
            }, true); // Use capture phase for priority
        })();
    </script>

    <script>
        // ============================================
        // PAGE ENTRY EFFECTS
        // ============================================
        (function(){
            // Intentionally no click interception here: keep native navigation behavior reliable.
        })();
    </script>

    <script>
        // ============================================
        // TEXT ANIMATIONS - Enhanced text reveal effects
        // ============================================
        (function(){
            // Add text animation to headings and descriptions
            var headings = document.querySelectorAll('h2, h3');
            headings.forEach(function(heading, index){
                heading.style.animationDelay = (index * 0.1) + 's';
            });

            // Animate footer links on hover
            var footerLinks = document.querySelectorAll('.footer-nav-links a');
            footerLinks.forEach(function(link, index){
                link.style.animationDelay = (index * 0.1) + 's';
            });

            var scrollTopBtn = document.getElementById('footerScrollTop');
            if (scrollTopBtn) {
                scrollTopBtn.addEventListener('click', function(){
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }


        })();
    </script>

    <script>
        // ============================================
        // HOVER EFFECTS - Enhanced interactive feedback
        // ============================================
        (function(){
            // Add hover effects to product cards
            var cards = document.querySelectorAll('.product-card');
            cards.forEach(function(card){
                card.addEventListener('mouseenter', function(){
                    this.style.boxShadow = '0 20px 40px rgba(0, 212, 170, 0.2)';
                });
                card.addEventListener('mouseleave', function(){
                    this.style.boxShadow = '';
                });
            });

            // Enhance button interactions
            var buttons = document.querySelectorAll('button, .cta-button, .featured-btn');
            buttons.forEach(function(btn){
                btn.addEventListener('mousedown', function(){
                    this.style.transform = 'scale(0.98)';
                });
                btn.addEventListener('mouseup', function(){
                    this.style.transform = '';
                });
                btn.addEventListener('mouseleave', function(){
                    this.style.transform = '';
                });
            });

            // Enhance navigation link hover effects
            var navLinks = document.querySelectorAll('.nav-list a');
            navLinks.forEach(function(link){
                link.addEventListener('mouseenter', function(){
                    this.style.color = '#ffffff';
                });
                link.addEventListener('mouseleave', function(){
                    if(!this.classList.contains('active')){
                        this.style.color = '';
                    }
                });
            });
        })();
    </script>

    <script>
        // ============================================
        // PARALLAX & SCROLL EFFECTS
        // ============================================
        (function(){
            var heroSlider = document.getElementById('heroSlider');
            if(!heroSlider) return;

            window.addEventListener('scroll', function(){
                var scrolled = window.pageYOffset;
                if(scrolled < 500){
                    heroSlider.style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
                    heroSlider.style.opacity = 1 - (scrolled / 800);
                }
            }, false);
        })();
    </script>


    <script>
        // ============================================
        // UPDATE CART BADGE COUNT IN REAL-TIME
        // ============================================
        (function(){
            function updateCartBadge() {
                var badge = document.getElementById('cartBadge');
                if(!badge) return;
                
                var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                var count = items.length;
                
                if(count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            
            // Update on page load
            updateCartBadge();
            
            // Update on storage change (when items added from other pages)
            window.addEventListener('storage', updateCartBadge);
            window.addEventListener('inquiryItemsUpdated', updateCartBadge);
            
            // Update frequently to catch changes
            setInterval(updateCartBadge, 500);
        })();
    </script>
</body>
</html>






