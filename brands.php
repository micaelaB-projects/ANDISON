<?php
require_once __DIR__ . '/Andison/includes/analytics.php';
andison_track_visit('brands');
require_once __DIR__ . '/Andison/includes/home_featured.php';
require_once __DIR__ . '/Andison/includes/home_slider.php';
require_once __DIR__ . '/Andison/includes/youtube_links.php';
require_once __DIR__ . '/Andison/includes/brands_info.php';
require_once __DIR__ . '/Andison/includes/brand_order.php';
require_once __DIR__ . '/includes/brand_logo_map.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$featured = andison_get_home_featured();
$slides = andison_get_home_slider();
$ytLinks = andison_get_youtube_links();
$brandsData = andison_get_brands_info(true);

// ── Clean up orphaned brand aliases (e.g., old MICROGARD after rename to AlphaTec) ────
if (function_exists('andison_sb_delete') && function_exists('andison_sb_select')) {
    $orphanedAliases = ['MICROGARD', 'Microgard', 'Hard Worker', 'Hard Workers'];
    foreach ($orphanedAliases as $oldName) {
        $canonical = strtolower(trim($oldName));
        if ($canonical === 'microgard' && isset($brandsData['AlphaTec'])) {
            // MICROGARD was renamed to AlphaTec; delete any stray MICROGARD record
            @andison_sb_delete('brands', 'name=eq.' . rawurlencode($oldName));
            @unlink(__DIR__ . '/Andison/data/_cache/brands_full.cache');
        }
    }
}

if (!function_exists('andison_brands_display_label')) {
    function andison_brands_display_label(string $brand): string
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
        return $normalized === 'weller' ? 'WEILER' : $brand;
    }
}

if (!function_exists('andison_brands_logo_path')) {
    function andison_brands_logo_path(string $brandKey, string $displayName, array $logoMap, array $brandInfo = []): string
    {
        $brandLogo = trim((string)($brandInfo['logo'] ?? ''));
        if ($brandLogo !== '') {
            return $brandLogo;
        }

        $candidates = [$brandKey, $displayName];
        $normalized = strtolower(trim($brandKey));

        if ($normalized === 'robot systems peripherals') {
            $candidates[] = 'Robot Systems';
        } elseif ($normalized === 'rae systems' || $normalized === 'rae' || $normalized === 'rac') {
            $candidates[] = 'RAC';
        } elseif ($normalized === 'magnaflux') {
            $candidates[] = 'MAGNAFLUX';
        } elseif ($normalized === 'weldas') {
            $candidates[] = 'WELDAS';
        } elseif ($normalized === 'uvex') {
            $candidates[] = 'UVEX';
        } elseif ($normalized === 'microgard') {
            $candidates[] = 'MICROGARD';
        } elseif ($normalized === 'ansell') {
            $candidates[] = 'ANSELL';
        } elseif ($normalized === 'bosch') {
            $candidates[] = 'BOSCH';
        } elseif ($normalized === 'revolt') {
            $candidates[] = 'REVOLT';
        } elseif ($normalized === 'motolite') {
            $candidates[] = 'MOTOLITE';
        } elseif ($normalized === 'sk and gal gage') {
            $candidates[] = 'SK And GAL GAGE';
        }

        foreach ($candidates as $candidate) {
            if (isset($logoMap[$candidate])) {
                return (string)$logoMap[$candidate];
            }
        }

        foreach ($logoMap as $mapName => $mapPath) {
            if (strcasecmp((string)$mapName, $brandKey) === 0 || strcasecmp((string)$mapName, $displayName) === 0) {
                return (string)$mapPath;
            }
        }

        return '';
    }
}

if (!function_exists('andison_brands_order_rank')) {
    function andison_brands_order_rank(string $brand): int
    {
        return andison_brand_order_rank(andison_brands_display_label($brand));
    }
}

if (!function_exists('andison_brands_preferred_key')) {
    function andison_brands_preferred_key(string $displayName, string $fallbackKey, array $brandsData): string
    {
        $displayKey = strtolower(trim($displayName));
        if ($displayKey === '') {
            return $fallbackKey;
        }

        $bestKey = $fallbackKey;
        $bestScore = -1;

        foreach ($brandsData as $candidateKey => $candidateInfo) {
            if (!is_array($candidateInfo)) {
                continue;
            }

            $candidateDisplay = strtolower(trim(andison_brands_display_label((string)$candidateKey)));
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

$brandDisplayToKey = [];
$hiddenBrandDisplayKeys = [
    'aer service' => true,
    'wire wizard' => true,
    'tokin arc' => true,
    'bw technologies' => true,
    'hard worker' => true,
    'phoenix dryrod' => true,
    'phoenix dry rod' => true,
    'sk' => true,
    'gal gage' => true,
];

foreach (array_keys($brandsData) as $brandKey) {
    $display = andison_brands_display_label((string)$brandKey);
    $displayKey = strtolower(trim($display));
    if ($displayKey === '' || isset($hiddenBrandDisplayKeys[$displayKey])) {
        continue;
    }

    if (!isset($brandDisplayToKey[$displayKey])) {
        $brandDisplayToKey[$displayKey] = (string)$brandKey;
        continue;
    }

    $currentKey = $brandDisplayToKey[$displayKey];
    $currentCount = count($brandsData[$currentKey]['products'] ?? []);
    $newCount = count($brandsData[$brandKey]['products'] ?? []);
    if ($newCount > $currentCount) {
        $brandDisplayToKey[$displayKey] = (string)$brandKey;
    }
}

$largeRectangularBrands = ['revolt', 'weldcraft', 'truweld', 'tempilstik', 'chiyoda', 'tanaka', 'yutaka', 'coppus', 'spilfyter', 'uvex', 'aces'];

$brandCards = [];
foreach ($brandDisplayToKey as $displayKey => $brandKey) {
    $displayName = andison_brands_display_label($brandKey);
    $resolvedBrandKey = andison_brands_preferred_key($displayName, (string)$brandKey, $brandsData);
    $brandInfo = isset($brandsData[$resolvedBrandKey]) && is_array($brandsData[$resolvedBrandKey]) ? $brandsData[$resolvedBrandKey] : [];
    $displayLower = strtolower(trim($displayName));
    $logoMaxScale = 1.55;
    if (str_contains($displayLower, 'bw technologies') || str_contains($displayLower, 'bw ') || $displayLower === 'alfra') {
        $logoMaxScale = 1.02;
    } elseif ($displayLower === 'revolt') {
        $logoMaxScale = 1.70;
    }
    $compactHover = str_contains($displayLower, 'alphatec') || str_contains($displayLower, 'revogard');
    $isLargeRect = in_array($displayLower, $largeRectangularBrands, true);
    $brandCards[] = [
        'key' => $resolvedBrandKey,
        'display' => $displayName,
        'logo' => andison_brands_logo_path($resolvedBrandKey, $displayName, isset($brand_logo_map) && is_array($brand_logo_map) ? $brand_logo_map : [], $brandInfo),
        'logo_max_scale' => $logoMaxScale,
        'compact_hover' => $compactHover,
        'large_rect' => $isLargeRect,
        'short_label' => trim((string)($brandInfo['short_label'] ?? '')),
    ];
}

usort($brandCards, static function (array $a, array $b): int {
    $rankA = andison_brands_order_rank((string)$a['display']);
    $rankB = andison_brands_order_rank((string)$b['display']);

    if ($rankA !== $rankB) {
        return $rankA <=> $rankB;
    }

    return strcasecmp((string)$a['display'], (string)$b['display']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Premium Brands - ANDISON INDUSTRIAL</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            padding-top: 142px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            height: 50px;
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
            transform: translateX(-50%) translateY(-6px) scale(0.98);
            transition: opacity 180ms ease, transform 180ms ease, visibility 180ms;
            z-index: 120;
        }

        .contact-dropdown:hover:not(.closed) .contact-popover,
        .contact-dropdown:focus-within:not(.closed) .contact-popover {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0) scale(1);
        }

        /* mobile: click-to-open; .open class used instead of hover */
        @media (max-width: 768px) {
            .contact-dropdown:hover:not(.closed) .contact-popover,
            .contact-dropdown:focus-within:not(.closed) .contact-popover {
                opacity: 0;
                visibility: hidden;
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

        .nav-list > li > a:hover::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
        }

        .nav-list > li > a:hover {
            background: rgba(0,0,0,0.10);
            border-radius: 6px;
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

        .nav-dropdown::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid white;
            filter: drop-shadow(0 -2px 2px rgba(0,0,0,0.05));
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
            color: #333;
            font-size: 14px;
            padding: 8px 12px;
            display: block;
            border-radius: 4px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-dropdown ul a:hover {
            background: #f0f5ff;
            color: #2B11DB;
        }

        .nav-dropdown p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        nav li:nth-child(3) .nav-dropdown {
            min-width: 576px;
            max-width: 576px;
            padding: 20px 22px;
        }

        .nav-dropdown ul a {
            display: block;
            border-radius: 4px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-dropdown ul a:hover {
            background: #f0f5ff;
            color: #2B11DB;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 12px !important;
            margin-top: 16px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 60px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a img {
            max-width: 96px;
            max-height: 52px;
            object-fit: contain;
            display: block;
            pointer-events: all;
            cursor: pointer;
        }

        nav li:nth-child(3) .nav-dropdown ul a .nav-brand-fallback {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 96px;
            min-height: 52px;
            font-size: 12px;
            font-weight: 800;
            color: #2B11DB;
            border: 1px dashed #cfd8ff;
            border-radius: 6px;
            padding: 6px 10px;
            text-align: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            cursor: pointer;
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
            background: linear-gradient(135deg, #2B11DB 0%, #00D7B3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
    
        .section-description {
            text-align: center;
            max-width: 750px;
            margin: 0 auto 60px;
            color: #555;
            line-height: 1.9;
            width: 100%;
            box-sizing: border-box;
            padding: 0 20px;
            font-size: 16px;
            font-weight: 500;
        }

        /* Brands Section */
        .brands-hero {
            text-align: center;
            padding: 60px 20px 50px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .brands-hero h2 {
            margin-bottom: 16px;
        }

        .brands-hero h2 span {
            background: linear-gradient(135deg, #2B11DB 0%, #00D7B3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brands-hero p {
            color: #666;
            font-size: 16px;
            line-height: 1.8;
            margin: 0;
        }

        .brands-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            max-width: 1500px;
            margin: 28px auto;
            padding: 0 20px 36px;
        }

        .brand-card {
            background: #fff;
            border: 1px solid #efefef;
            border-radius: 16px;
            padding: 8px 8px 10px;
            text-align: center;
            transition: border-color 0.24s ease, box-shadow 0.24s ease, transform 0.24s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 320px;
        }

        .brand-card-meta {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-top: auto;
            min-height: 45px;
            padding-top: 8px;
        }

        .brand-card-name {
            font-size: 18px;
            font-weight: 800;
            color: #1f2937;
            line-height: 1.3;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .brand-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 215, 179, 0.22);
            border-color: #00D7B3;
        }

        .brand-logo {
            width: 100%;\n            background: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0;
            overflow: hidden;
            flex: 1;
            min-height: 0;
        }

        .brand-logo img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 95%;
            object-fit: contain;
            padding: 0;
            transform: scale(1);
            transform-origin: center;
            filter: grayscale(25%);
            transition: filter 0.24s ease, transform 0.24s ease;
        }

        .brand-card:hover .brand-logo img {
            filter: grayscale(0%);
            transform: scale(1.04);
        }

        .brand-card.brand-card--compact-hover .brand-logo img {
            max-width: 78%;
            max-height: 74%;
        }

        .brand-card.brand-card--compact-hover:hover .brand-logo img {
            transform: scale(0.82) !important;
        }

        /* Large rectangular brand cards */
        .brand-card--large .brand-logo img {
            max-width: 96%;
            max-height: 96%;
        }

        @media (max-width: 1024px) {
            .brand-card {
                height: 280px;
            }

            .brands-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
            }
        }

        @media (max-width: 768px) {
            .brand-card {
                height: 250px;
            }

            .brands-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .brands-hero {
                padding: 40px 16px 30px;
            }
            .brands-hero h2 {
                font-size: 32px;
            }
        }

        @media (max-width: 480px) {
            .brand-card {
                height: 220px;
            }

            .brands-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .brand-card {
                padding: 8px;
            }
        }

        .brand-logo-fallback {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2B11DB 0%, #00BCD4 100%);
            color: #fff;
            font-size: 28px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            letter-spacing: 0.5px;
        }

        .brand-label {
            padding: 0;
            text-align: center;
            font-size: 16px;
            color: #333;
            font-weight: 700;
            line-height: 1.35;
            margin-top: 0;
            word-wrap: break-word;
            overflow-wrap: break-word;
            flex-shrink: 0;
        }

        @media (max-width: 1024px) {
            .brands-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
            }

            .brand-logo {
                height: 200px;
            }
        }

        @media (max-width: 768px) {
            .brands-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .brands-hero {
                padding: 40px 16px 30px;
            }
            .brands-hero h2 {
                font-size: 32px;
            }

            .brand-logo {
                height: 170px;
            }
        }

        @media (max-width: 480px) {
            .brands-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .brand-card {
                padding: 8px;
            }
            .brand-logo {
                height: 130px;
            }
            .brand-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        }

        /* Product Highlights */
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin: 34px auto 50px;
            width: 100%;
            max-width: 1500px;
            padding: 14px 12px 12px;
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
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
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
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
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
            background: linear-gradient(135deg, #1a0d7a 0%, #2B11DB 100%);
            color: white;
            padding: 60px 0 40px;
            text-align: center;
            margin-top: auto;
            width: 100%;
            position: relative;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            width: 100%;
            margin: 0;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.95);
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 4px;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #00D7B3;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-copyright {
            font-size: 14px;
            opacity: 0.85;
            font-weight: 500;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 24px;
        }

        /* Responsive */
        @media (max-width: 768px) {
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
                flex-wrap: nowrap;
                gap: 0;
            }

            nav li {
                margin-right: 0;
            }

            .nav-inner {
                padding-left: 0;
                padding-right: 0;
                gap: 0;
                min-height: auto;
                overflow-x: hidden;
                overflow-y: visible;
                justify-content: center;
                flex-wrap: wrap;
            }

            .nav-inner::-webkit-scrollbar { display: none; }

            .nav-list {
                gap: 0;
                flex-wrap: wrap;
                flex-shrink: 1;
                justify-content: center;
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

        
        .footer-links a {
            position: relative;
            animation: textFadeIn 0.6s ease;
        }

        .footer-links a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #00d4aa;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::before {
            width: 100%;
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

            .nav-inner {
                padding-left: 0;
                padding-right: 0;
                gap: 0;
                min-height: auto;
                overflow-x: hidden;
                overflow-y: visible;
                justify-content: center;
                flex-wrap: wrap;
            }

            .nav-inner::-webkit-scrollbar { display: none; }

            .nav-list {
                gap: 0;
                flex-wrap: wrap;
                flex-shrink: 1;
                justify-content: center;
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
        <?php
        // Set page title
        $page_title = "Brands";
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

    <!-- Sidebar (loaded from includes/sidebar.php) -->
     <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <!-- Brands Hero -->
    <div class="page-content">
    <section id="brands-overview" style="padding: 0; background: #fff;">
        <div class="brands-hero">
            <h2>Our <span>Premium Brands</span></h2>
            <p>We partner with leading international brands to provide you with the highest quality industrial solutions and equipment.</p>
        </div>

        <div class="brands-grid">
            <?php foreach ($brandCards as $brandCard): ?>
                <div class="brand-card<?php echo !empty($brandCard['compact_hover']) ? ' brand-card--compact-hover' : ''; ?><?php echo !empty($brandCard['large_rect']) ? ' brand-card--large' : ''; ?>" data-brand="<?php echo htmlspecialchars((string)$brandCard['key'], ENT_QUOTES); ?>" data-logo-max-scale="<?php echo htmlspecialchars((string)$brandCard['logo_max_scale'], ENT_QUOTES); ?>">
                    <div class="brand-logo">
                        <?php if ((string)($brandCard['logo'] ?? '') !== ''): ?>
                            <img src="<?php echo htmlspecialchars((string)$brandCard['logo'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars((string)$brandCard['display'], ENT_QUOTES); ?>">
                        <?php else: ?>
                            <span class="brand-logo-fallback"><?php echo htmlspecialchars(strtoupper(substr((string)$brandCard['display'], 0, 1)), ENT_QUOTES); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($brandCard['short_label'])): ?>
                    <div class="brand-label">
                        <?php echo htmlspecialchars((string)$brandCard['short_label'], ENT_QUOTES); ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    </div><!-- /.page-content -->

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
                <p>&copy; 2026 <?php echo $company_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <?php require_once __DIR__ . '/includes/footer_modernize.php'; ?>
    <script>
        // Auto-fit logos with large internal padding (generic, no brand hardcoding).
        (function(){
            function clamp(value, min, max) {
                return Math.max(min, Math.min(max, value));
            }

            function estimateLogoScale(img, maxScale) {
                var w = img.naturalWidth || 0;
                var h = img.naturalHeight || 0;
                if (w < 8 || h < 8) return 1;

                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d', { willReadFrequently: true });
                if (!ctx) return 1;

                var targetW = Math.min(220, w);
                var targetH = Math.max(1, Math.round((targetW / w) * h));
                canvas.width = targetW;
                canvas.height = targetH;

                try {
                    ctx.drawImage(img, 0, 0, targetW, targetH);
                    var data = ctx.getImageData(0, 0, targetW, targetH).data;

                    var minX = targetW, minY = targetH, maxX = -1, maxY = -1;
                    for (var y = 0; y < targetH; y++) {
                        for (var x = 0; x < targetW; x++) {
                            var idx = (y * targetW + x) * 4;
                            var a = data[idx + 3];
                            if (a < 20) continue;

                            var r = data[idx], g = data[idx + 1], b = data[idx + 2];
                            var isNearlyWhite = (r > 245 && g > 245 && b > 245);
                            if (isNearlyWhite) continue;

                            if (x < minX) minX = x;
                            if (y < minY) minY = y;
                            if (x > maxX) maxX = x;
                            if (y > maxY) maxY = y;
                        }
                    }

                    if (maxX < minX || maxY < minY) return 1;

                    var boxW = maxX - minX + 1;
                    var boxH = maxY - minY + 1;
                    var fillRatio = (boxW * boxH) / (targetW * targetH);

                    if (fillRatio >= 0.7) return 1;

                    var desired = 1 + ((0.7 - fillRatio) * 1.4);
                    return clamp(desired, 1, maxScale);
                } catch (err) {
                    return 1;
                }
            }

            function applyAutoFit(img, maxScale) {
                if (!img) return;
                var scale = estimateLogoScale(img, maxScale);
                if (scale > 1.02) {
                    img.style.transform = 'scale(' + scale.toFixed(2) + ')';
                    img.style.transformOrigin = 'center';
                }
            }

            function initLogoAutoFit() {
                var cardLogos = document.querySelectorAll('.brand-logo img');
                cardLogos.forEach(function(img){
                    var card = img.closest('.brand-card');
                    var maxScale = 1.28;
                    if (card) {
                        var parsed = parseFloat(card.getAttribute('data-logo-max-scale') || '');
                        if (Number.isFinite(parsed)) {
                            maxScale = parsed;
                        }
                    }

                    if (img.complete) {
                        applyAutoFit(img, maxScale);
                    } else {
                        img.addEventListener('load', function(){ applyAutoFit(img, maxScale); }, { once: true });
                    }
                });

                var navLogos = document.querySelectorAll('nav li:nth-child(3) .nav-dropdown ul a img');
                navLogos.forEach(function(img){
                    if (img.complete) {
                        applyAutoFit(img, 1.9);
                    } else {
                        img.addEventListener('load', function(){ applyAutoFit(img, 1.9); }, { once: true });
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initLogoAutoFit);
            } else {
                initLogoAutoFit();
            }
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
            var slides = slider.querySelectorAll('.hero-slide');
            var dots = slider.querySelectorAll('.hero-dot');
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
        // PAGE TRANSITION EFFECTS
        // ============================================
        (function(){
            // Smooth page transitions on link clicks
            document.addEventListener('click', function(e){
                var link = e.target.closest('a[href*=".php"], a[href^="#"]');
                if(!link) return;
                
                var href = link.getAttribute('href');
                
                // Skip if it's an anchor link or javascript link
                if(href.startsWith('#') || href.startsWith('javascript:')) return;
                
                // Check if it's an internal PHP file
                if(!href.includes('.php')) return;
                
                // Prevent default and add exit animation
                e.preventDefault();
                
                var body = document.body;
                body.style.animation = 'none';

                setTimeout(function(){
                    window.location.href = href;
                }, 0);
            });

            // Add page entry animation on load
            window.addEventListener('load', function(){
                document.body.style.animation = 'none';
            });
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
            var footerLinks = document.querySelectorAll('.footer-links a');
            footerLinks.forEach(function(link, index){
                link.style.animationDelay = (index * 0.1) + 's';
            });


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


    <script>
        // Brand cards - navigate to brand.php with brand name parameter
        document.querySelectorAll('.brand-card').forEach(function(card) {
            card.addEventListener('click', function() {
                var brandName = card.getAttribute('data-brand');
                if (brandName) {
                    window.location.href = 'brand.php?name=' + encodeURIComponent(brandName);
                }
            });
        });
    </script>
</body>
</html>





