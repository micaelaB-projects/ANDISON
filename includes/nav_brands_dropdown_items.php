<?php
declare(strict_types=1);

require_once __DIR__ . '/brands_info.php';
require_once __DIR__ . '/brand_order.php';
include_once __DIR__ . '/brand_logo_map.php';

if (!defined('ANDISON_NAV_BRANDS_DROPDOWN_STYLE_PRINTED')) {
    define('ANDISON_NAV_BRANDS_DROPDOWN_STYLE_PRINTED', true);
    ?>
    <style>
        /* Brand sizing v9 - SK+GalGage reduced to 1.65 - cache bust: 2026-04-29-019 */
        
        nav li:nth-child(3) .nav-dropdown {
            min-width: 860px !important;
            max-width: 860px !important;
            padding: 18px 22px 36px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 12px 10px !important;
            margin-top: 14px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: auto !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: stretch !important;
            gap: 4px !important;
            min-width: 0 !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
            width: 100% !important;
            max-width: 132px !important;
            height: 104px !important;
            min-height: 104px !important;
            padding: 3px 5px 4px !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 4px !important;
            position: relative !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
            transition: background-color 160ms ease !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:hover {
            background: transparent !important;
            border-color: transparent !important;
            box-shadow: none !important;
            transform: none !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:focus,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:focus-visible,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:active {
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            border-color: transparent !important;
            transform: none !important;
            -webkit-tap-highlight-color: transparent !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link .andison-nav-brand-caption {
            pointer-events: none !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link img.andison-nav-brand-logo {
            --nav-logo-scale: 1.5;
            --nav-logo-scale-hover: 1.38;
            width: 100% !important;
            height: 58px !important;
            margin-top: 1px !important;
            flex: 0 0 58px !important;
            position: relative !important;
            z-index: 1 !important;
            max-width: none !important;
            max-height: none !important;
            object-fit: contain !important;
            transform: scale(var(--nav-logo-scale)) !important;
            transform-origin: center center !important;
            transition: transform 180ms ease !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link .andison-nav-brand-caption {
            position: relative !important;
            z-index: 2 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 24px !important;
            font-size: 9px !important;
            color: #333 !important;
            text-align: center !important;
            font-weight: 600 !important;
            line-height: 1.15 !important;
            max-width: 124px !important;
            overflow: hidden !important;
            white-space: normal !important;
            word-break: normal !important;
            overflow-wrap: anywhere !important;
            display: -webkit-box !important;
            -webkit-box-orient: vertical !important;
            -webkit-line-clamp: 2 !important;
            background: rgba(255, 255, 255, 0.95) !important;
            border-radius: 6px !important;
            padding: 2px 4px !important;
            margin-top: 0 !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:hover img.andison-nav-brand-logo {
            transform: scale(var(--nav-logo-scale-hover)) !important;
        }

        /* Reduce only the logos that appear too large visually. */
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-skandgalgage img.andison-nav-brand-logo {
            --nav-logo-scale: 1.65 !important;
            --nav-logo-scale-hover: 1.8 !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-magnaflux img.andison-nav-brand-logo {
            --nav-logo-scale: 1.1;
            --nav-logo-scale-hover: 1.2;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-alphatec img.andison-nav-brand-logo {
            --nav-logo-scale: 0.95;
            --nav-logo-scale-hover: 1.05;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-revogard img.andison-nav-brand-logo {
            --nav-logo-scale: 1.65;
            --nav-logo-scale-hover: 1.8;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-yutaka img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-aces img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-bosch img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-weiler img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-coppus img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-panasonicconnect img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-thermafield img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-makita img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-tanaka img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-chiyoda img.andison-nav-brand-logo {
            --nav-logo-scale: 1.85;
            --nav-logo-scale-hover: 2.01;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-technotex img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-weldcraft img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-revolt img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-truweld img.andison-nav-brand-logo {
            --nav-logo-scale: 2.4 !important;
            --nav-logo-scale-hover: 2.6 !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-robotsystemsperipherals img.andison-nav-brand-logo {
            --nav-logo-scale: 1.5;
            --nav-logo-scale-hover: 1.65;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-alfra img.andison-nav-brand-logo {
            --nav-logo-scale: 1.85;
            --nav-logo-scale-hover: 2.01;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-revolt .andison-nav-brand-caption,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-thermafield .andison-nav-brand-caption,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-makita .andison-nav-brand-caption,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-weldcraft .andison-nav-brand-caption {
            font-size: 10px !important;
            min-height: 26px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-dryrodii img.andison-nav-brand-logo {
            --nav-logo-scale: 2.4 !important;
            --nav-logo-scale-hover: 2.6 !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-bw img.andison-nav-brand-logo {
            --nav-logo-scale: 1.7;
            --nav-logo-scale-hover: 1.85;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-aquasol img.andison-nav-brand-logo {
            --nav-logo-scale: 1.72;
            --nav-logo-scale-hover: 1.88;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-weldas img.andison-nav-brand-logo {
            --nav-logo-scale: 1.78;
            --nav-logo-scale-hover: 1.94;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-magnaflux img.andison-nav-brand-logo {
            --nav-logo-scale: 1.92;
            --nav-logo-scale-hover: 2.08;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-tempilstik img.andison-nav-brand-logo {
            --nav-logo-scale: 2.4 !important;
            --nav-logo-scale-hover: 2.6 !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-soyer img.andison-nav-brand-logo {
            --nav-logo-scale: 1.46;
            --nav-logo-scale-hover: 1.6;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-spilfyter img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-splitfyter img.andison-nav-brand-logo {
            --nav-logo-scale: 1.98;
            --nav-logo-scale-hover: 2.14;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-ansell img.andison-nav-brand-logo {
            --nav-logo-scale: 1.96;
            --nav-logo-scale-hover: 2.12;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-garryson img.andison-nav-brand-logo {
            --nav-logo-scale: 2.04;
            --nav-logo-scale-hover: 2.2;
        }

        @media (max-width: 1200px) {
            nav li:nth-child(3) .nav-dropdown {
                min-width: 760px !important;
                max-width: 760px !important;
                padding-bottom: 32px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
                max-width: 116px !important;
                height: 96px !important;
                min-height: 96px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link img.andison-nav-brand-logo {
                height: 44px !important;
            }
        }

        @media (max-width: 980px) {
            nav li:nth-child(3) .nav-dropdown {
                min-width: 640px !important;
                max-width: 640px !important;
                padding-bottom: 28px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
                max-width: 100px !important;
                height: 88px !important;
                min-height: 88px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link img.andison-nav-brand-logo {
                height: 38px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link .andison-nav-brand-caption {
                font-size: 8px !important;
                max-width: 94px !important;
                min-height: 20px !important;
            }
        }
    </style>
    <?php
}

if (!function_exists('andison_nav_brand_css_key')) {
    function andison_nav_brand_css_key(string $brandName): string
    {
        $key = strtolower(trim($brandName));
        $key = preg_replace('/[^a-z0-9]+/', '', $key) ?? $key;
        return $key;
    }
}

if (!function_exists('andison_nav_dropdown_resolve_logo')) {
    /**
     * Resolve brand logo path preferring Supabase value, then static map.
     */
    function andison_nav_dropdown_resolve_logo(string $brandName, array $brandInfo, array $logoMap, string $basePath): string
    {
        $logo = trim((string)($brandInfo['logo'] ?? ''));

        if ($logo === '' && isset($logoMap[$brandName])) {
            $logo = trim((string)$logoMap[$brandName]);
        }

        if ($logo === '') {
            $lookup = strtolower($brandName);
            foreach ($logoMap as $mapName => $mapLogo) {
                if (strtolower((string)$mapName) === $lookup) {
                    $logo = trim((string)$mapLogo);
                    break;
                }
            }
        }

        if ($logo === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#i', $logo) === 1) {
            return $logo;
        }

        if (strpos($logo, '/') === 0) {
            return str_replace(' ', '%20', $logo);
        }

        return str_replace(' ', '%20', $basePath . ltrim($logo, '/'));
    }
}

$andisonNavBasePath = '';
if (isset($base_path) && is_string($base_path)) {
    $andisonNavBasePath = $base_path;
} else {
    // Auto-detect relative base path when page-specific files include this renderer directly.
    $projectRoot = realpath(dirname(__DIR__));
    $scriptDir = realpath(dirname((string)($_SERVER['SCRIPT_FILENAME'] ?? '')));
    if (is_string($projectRoot) && $projectRoot !== '' && is_string($scriptDir) && $scriptDir !== '') {
        $tmp = $scriptDir;
        while ($tmp !== '' && realpath($tmp) !== false && realpath($tmp) !== $projectRoot && strlen($tmp) > 3) {
            $andisonNavBasePath .= '../';
            $tmp = dirname($tmp);
        }
    }
}

$andisonBrandMap = (isset($brand_logo_map) && is_array($brand_logo_map)) ? $brand_logo_map : [];
$andisonBrandsInfo = function_exists('andison_get_brands_info') ? andison_get_brands_info(true) : [];

$andisonPreferredOrder = andison_load_brand_order();

$andisonBrandAliases = [
    'robot systems peripherals' => 'Robot Systems Peripherals',
    'robot system peripherals' => 'Robot Systems Peripherals',
    'hard worker' => 'HARDWORKER',
    'hard workers' => 'HARDWORKER',
    'microgard' => 'AlphaTec',
    'alphatec' => 'AlphaTec',
    'bw technologies' => 'BW',
    'bw' => 'BW',
    'rae systems' => 'RAE SYSTEMS',
    'rae' => 'RAE SYSTEMS',
    'are' => 'RAE SYSTEMS',
    'weller' => 'Weiler',
    'weiler' => 'Weiler',
    'sk and gal gage' => 'SK And GAL GAGE',
    'sk/gal gage' => 'SK And GAL GAGE',
    'dryrod ii' => 'DryRod. II',
];

$andisonOrderIndex = [];
foreach ($andisonPreferredOrder as $andisonOrderPos => $andisonOrderName) {
    $andisonOrderIndex[strtolower(andison_brand_order_label((string)$andisonOrderName))] = $andisonOrderPos;
}

$andisonCanonicalName = static function (string $name) use ($andisonBrandAliases): string {
    $trimmed = trim($name);
    $lower = strtolower($trimmed);
    return $andisonBrandAliases[$lower] ?? andison_brand_order_label($trimmed);
};

$andisonSortBrands = static function (array &$brands) use ($andisonOrderIndex, $andisonCanonicalName): void {
    uksort($brands, static function ($a, $b) use ($andisonOrderIndex, $andisonCanonicalName): int {
        $nameA = $andisonCanonicalName((string)$a);
        $nameB = $andisonCanonicalName((string)$b);

        $indexA = $andisonOrderIndex[strtolower($nameA)] ?? 9999;
        $indexB = $andisonOrderIndex[strtolower($nameB)] ?? 9999;

        if ($indexA !== $indexB) {
            return $indexA <=> $indexB;
        }

        return strcasecmp($nameA, $nameB);
    });
};

if (is_array($andisonBrandsInfo) && !empty($andisonBrandsInfo)) {
    $andisonSortBrands($andisonBrandsInfo);
    $andisonRenderedCanonical = [];
    foreach ($andisonBrandsInfo as $brandName => $brandInfo) {
        $displayName = $andisonCanonicalName((string)$brandName);
        if ($displayName === '') {
            continue;
        }

        // Deduplicate: skip if this canonical name already rendered
        $lowerDisplayName = strtolower($displayName);
        if (isset($andisonRenderedCanonical[$lowerDisplayName])) {
            continue;
        }
        $andisonRenderedCanonical[$lowerDisplayName] = true;

        $logoPath = andison_nav_dropdown_resolve_logo(
            $displayName,
            is_array($brandInfo) ? $brandInfo : [],
            $andisonBrandMap,
            $andisonNavBasePath
        );

        if ($logoPath === '') {
            continue;
        }

        $brandUrl = $andisonNavBasePath . 'brand.php?name=' . rawurlencode($displayName);
        $brandCssKey = andison_nav_brand_css_key($displayName);
        $brandShortLabel = trim((string)($brandInfo['short_label'] ?? ''));
        
        // Override short labels for specific brands to match admin display
        if (strtolower($displayName) === 'alphatec' && $brandShortLabel === '') {
            $brandShortLabel = 'Chemical Protective Clothing';
        }
        ?>
        <li>
            <a class="andison-nav-brand-link brand-key-<?php echo htmlspecialchars($brandCssKey, ENT_QUOTES); ?>" href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
                <img
                    class="andison-nav-brand-logo"
                    src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES); ?>"
                    alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                    title="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                >
                <span class="andison-nav-brand-caption"><?php echo htmlspecialchars($brandShortLabel !== '' ? $brandShortLabel : $displayName, ENT_QUOTES); ?></span>
            </a>
        </li>
        <?php
    }
} else {
    $andisonSortBrands($andisonBrandMap);
    $andisonRenderedCanonical = [];
    foreach ($andisonBrandMap as $brandName => $logoPath) {
        $displayName = $andisonCanonicalName((string)$brandName);
        
        // Deduplicate: skip if this canonical name already rendered
        $lowerDisplayName = strtolower($displayName);
        if (isset($andisonRenderedCanonical[$lowerDisplayName])) {
            continue;
        }
        $andisonRenderedCanonical[$lowerDisplayName] = true;
        
        $resolvedLogoPath = andison_nav_dropdown_resolve_logo($displayName, [], $andisonBrandMap, $andisonNavBasePath);
        if ($displayName === '' || $resolvedLogoPath === '') {
            continue;
        }
        $brandUrl = $andisonNavBasePath . 'brand.php?name=' . rawurlencode($displayName);
        $brandCssKey = andison_nav_brand_css_key($displayName);
        $brandLabel = $displayName;
        
        // Override labels for specific brands to match admin display
        if (strtolower($displayName) === 'alphatec') {
            $brandLabel = 'Chemical Protective Clothing';
        }
        ?>
        <li>
            <a class="andison-nav-brand-link brand-key-<?php echo htmlspecialchars($brandCssKey, ENT_QUOTES); ?>" href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
                <img
                    class="andison-nav-brand-logo"
                    src="<?php echo htmlspecialchars($resolvedLogoPath, ENT_QUOTES); ?>"
                    alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                    title="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                >
                <span class="andison-nav-brand-caption"><?php echo htmlspecialchars($brandLabel, ENT_QUOTES); ?></span>
            </a>
        </li>
        <?php
    }
}
