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
            min-width: 770px !important;
            max-width: 770px !important;
            padding: 18px 20px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 6px 10px !important;
            margin-top: 12px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 82px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
            width: 140px !important;
            height: 74px !important;
            min-height: 74px !important;
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
            width: 100% !important;
            height: 100% !important;
            max-width: none !important;
            max-height: none !important;
            object-fit: contain !important;
            transform: scale(1.55) !important;
            transform-origin: center center !important;
            transition: transform 180ms ease !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link:hover img.andison-nav-brand-logo {
            transform: scale(1.7) !important;
        }

        @media (max-width: 1200px) {
            nav li:nth-child(3) .nav-dropdown {
                min-width: 640px !important;
                max-width: 640px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a.andison-nav-brand-link {
                width: 116px !important;
                height: 62px !important;
                min-height: 62px !important;
            }
        }
    </style>
    <?php
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
$andisonBrandsInfo = function_exists('andison_get_brands_info') ? andison_get_brands_info() : [];

$andisonPreferredOrder = andison_load_brand_order();

$andisonBrandAliases = [
    'robot systems peripherals' => 'Robot Systems Peripherals',
    'robot system peripherals' => 'Robot Systems Peripherals',
    'hard worker' => 'HARDWORKER',
    'hard workers' => 'HARDWORKER',
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
    foreach ($andisonBrandsInfo as $brandName => $brandInfo) {
        $displayName = $andisonCanonicalName((string)$brandName);
        if ($displayName === '') {
            continue;
        }

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
        ?>
        <li>
            <a class="andison-nav-brand-link" href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
                <img
                    class="andison-nav-brand-logo"
                    src="<?php echo htmlspecialchars($logoPath, ENT_QUOTES); ?>"
                    alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                    title="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                >
            </a>
        </li>
        <?php
    }
} else {
    $andisonSortBrands($andisonBrandMap);
    foreach ($andisonBrandMap as $brandName => $logoPath) {
        $displayName = $andisonCanonicalName((string)$brandName);
        $resolvedLogoPath = andison_nav_dropdown_resolve_logo($displayName, [], $andisonBrandMap, $andisonNavBasePath);
        if ($displayName === '' || $resolvedLogoPath === '') {
            continue;
        }
        $brandUrl = $andisonNavBasePath . 'brand.php?name=' . rawurlencode($displayName);
        ?>
        <li>
            <a class="andison-nav-brand-link" href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
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
