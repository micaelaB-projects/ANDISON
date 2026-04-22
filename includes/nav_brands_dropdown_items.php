<?php
declare(strict_types=1);

require_once __DIR__ . '/brands_info.php';
include_once __DIR__ . '/brand_logo_map.php';

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

$andisonPreferredOrder = [
    'Panasonic Connect',
    'Robot Systems',
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
    'RAC',
    'WELDAS',
    'UVEX',
    'ACES',
    'MICROGARD',
    'ANSELL',
    'Alfra',
    'BOSCH',
    'Makita',
    'Weller',
    'Garryson',
    'REVOLT',
    'Technotex',
    'Spilfyter',
    'Dalo',
    'MOTOLITE',
];

$andisonBrandAliases = [
    'robot system peripherals' => 'Robot Systems',
    'hard worker' => 'HARDWORKER',
    'hard workers' => 'HARDWORKER',
    'bw technologies' => 'BW',
    'rae systems' => 'RAC',
    'rae' => 'RAC',
    'sk and gal gage' => 'SK And GAL GAGE',
    'sk/gal gage' => 'SK And GAL GAGE',
    'dryrod ii' => 'DryRod. II',
];

$andisonOrderIndex = [];
foreach ($andisonPreferredOrder as $andisonOrderPos => $andisonOrderName) {
    $andisonOrderIndex[strtolower($andisonOrderName)] = $andisonOrderPos;
}

$andisonCanonicalName = static function (string $name) use ($andisonBrandAliases): string {
    $trimmed = trim($name);
    $lower = strtolower($trimmed);
    return $andisonBrandAliases[$lower] ?? $trimmed;
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
            <a href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
                <img
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
            <a href="<?php echo htmlspecialchars($brandUrl, ENT_QUOTES); ?>">
                <img
                    src="<?php echo htmlspecialchars($resolvedLogoPath, ENT_QUOTES); ?>"
                    alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                    title="<?php echo htmlspecialchars($displayName, ENT_QUOTES); ?>"
                >
            </a>
        </li>
        <?php
    }
}
