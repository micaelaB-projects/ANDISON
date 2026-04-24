<?php
declare(strict_types=1);

require_once __DIR__ . '/brands_info.php';
require_once __DIR__ . '/brand_order.php';
include_once __DIR__ . '/brand_logo_map.php';

if (!defined('ANDISON_NAV_BRANDS_DROPDOWN_STYLE_PRINTED')) {
    define('ANDISON_NAV_BRANDS_DROPDOWN_STYLE_PRINTED', true);
    ?>
    <style>
        nav li:nth-child(3) .nav-dropdown {
            min-width: 860px !important;
            max-width: 860px !important;
            padding: 18px 22px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 7px 12px !important;
            margin-top: 12px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: auto !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 4px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
            width: 152px !important;
            height: 76px !important;
            min-height: 76px !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            transition: background-color 180ms ease, transform 180ms ease !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:hover {
            background: #f0f5ff !important;
            transform: translateY(-1px) !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link img.andison-nav-brand-logo {
            --nav-logo-scale: 1.55;
            --nav-logo-scale-hover: 1.7;
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            max-height: none !important;
            object-fit: contain !important;
            transform: scale(var(--nav-logo-scale)) !important;
            transform-origin: center center !important;
            transition: transform 180ms ease !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:hover img.andison-nav-brand-logo {
            transform: scale(var(--nav-logo-scale-hover)) !important;
        }

        /* Reduce only the logos that appear too large visually. */
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-soyer img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-skandgalgage img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-magnaflux img.andison-nav-brand-logo {
            --nav-logo-scale: 1.2;
            --nav-logo-scale-hover: 1.3;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-dryrodii img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-bw img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-alphatec img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-revogard img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-robotsystemsperipherals img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-weldas img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-aquasol img.andison-nav-brand-logo,
        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link.brand-key-alfra img.andison-nav-brand-logo {
            --nav-logo-scale: 1.03;
            --nav-logo-scale-hover: 1.12;
        }

        nav li:nth-child(3) .nav-dropdown ul li > span {
            font-size: 9px !important;
            color: #333 !important;
            text-align: center !important;
            font-weight: 600 !important;
            max-width: 140px !important;
            word-break: break-word !important;
            line-height: 1.3 !important;
        }

        @media (max-width: 1200px) {
            nav li:nth-child(3) .nav-dropdown {
                min-width: 760px !important;
                max-width: 760px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
                width: 132px !important;
                height: 68px !important;
                min-height: 68px !important;
            }
        }

        @media (max-width: 980px) {
            nav li:nth-child(3) .nav-dropdown {
                min-width: 640px !important;
                max-width: 640px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
                width: 114px !important;
                height: 62px !important;
                min-height: 62px !important;
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
        ?>
        <li style="display:flex;flex-direction:column;align-items:center;gap:4px;">
            <a class="andison-nav-brand-link brand-key-<?php echo htmlspecialchars($brandCssKey, ENT_QUOTES); ?>" href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
                <img
                    class="andison-nav-brand-logo"
                    src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES); ?>"
                    alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                    title="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                >
            </a>
            <?php if ($brandShortLabel !== ''): ?>
            <span><?php echo htmlspecialchars($brandShortLabel, ENT_QUOTES); ?></span>
            <?php endif; ?>
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
        ?>
        <li>
            <a class="andison-nav-brand-link brand-key-<?php echo htmlspecialchars($brandCssKey, ENT_QUOTES); ?>" href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
                <img
                    class="andison-nav-brand-logo"
                    src="<?php echo htmlspecialchars($resolvedLogoPath, ENT_QUOTES); ?>"
                    alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                    title="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                >
            </a>
        </li>
        <?php
    }
}
