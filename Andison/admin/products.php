<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../includes/brand_order.php';
require_once __DIR__ . '/../includes/categories_info.php';

$brands = andison_get_brands_info(true);
$brandNames = array_keys($brands);
$selectedBrandDisplay = isset($_GET['brand']) ? (string)$_GET['brand'] : ($brandNames[0] ?? '');

function andison_brand_display_label(string $brand): string
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

function andison_brand_data_candidates(string $brand): array
{
    $normalized = strtolower(trim($brand));
    if ($normalized === 'robot systems' || $normalized === 'robot system peripherals' || $normalized === 'robot systems peripherals') {
        return ['Robot Systems Peripherals', 'Robot Systems', 'Robot System Peripherals'];
    }
    if ($normalized === 'hard worker' || $normalized === 'hard workers' || $normalized === 'hardworker') {
        return ['HARDWORKER', 'Hard Worker', 'Hard Workers', 'HARD WORKER', 'HARD WORKERS'];
    }
    if ($normalized === 'dryrod. ii' || $normalized === 'dryrod ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
        return ['DryRod. II', 'DryRod II', 'Phoenix Dry Rod', 'Phoenix DryRod', 'PHOENIX DRY ROD', 'PHOENIX DRYROD'];
    }
    if ($normalized === 'ansell') {
        return ['ANSELL', 'Ansell'];
    }
    if ($normalized === 'alphatec' || $normalized === 'microgard') {
        return ['AlphaTec', 'ALPHATEC', 'MICROGARD', 'Microgard'];
    }
    if ($normalized === 'panasonic connect' || $normalized === 'panasonic') {
        return ['Panasonic Connect', 'PANASONIC'];
    }
    if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
        return ['RAE SYSTEMS', 'RAC'];
    }
    if ($normalized === 'weiler' || $normalized === 'weller') {
        return ['WEILER', 'Weller'];
    }
    return [$brand];
}

if (!function_exists('andison_brand_order_rank')) {
    function andison_brand_order_rank(string $brand): int
    {
        return 10000;
    }
}

function andison_pick_brand_bucket(array $brands, array $candidates): string
{
    foreach ($candidates as $candidate) {
        if (isset($brands[$candidate]) && !empty($brands[$candidate]['products'])) {
            return (string)$candidate;
        }
    }

    foreach ($candidates as $candidate) {
        if (isset($brands[$candidate])) {
            return (string)$candidate;
        }
    }

    foreach ($candidates as $candidate) {
        $needle = strtolower(trim((string)$candidate));
        if ($needle === '') {
            continue;
        }

        foreach ($brands as $brandKey => $brandInfo) {
            if (strtolower(trim((string)$brandKey)) === $needle && !empty($brandInfo['products'])) {
                return (string)$brandKey;
            }
        }
    }

    foreach ($candidates as $candidate) {
        $needle = strtolower(trim((string)$candidate));
        if ($needle === '') {
            continue;
        }

        foreach ($brands as $brandKey => $_brandInfo) {
            if (strtolower(trim((string)$brandKey)) === $needle) {
                return (string)$brandKey;
            }
        }
    }

    return (string)($candidates[0] ?? '');
}

// Deduplicate brand options by display label (case-insensitive).
$rawBrandNames = array_keys($brands);
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
foreach ($rawBrandNames as $brandKey) {
    $display = andison_brand_display_label((string)$brandKey);
    $displayKey = strtolower(trim($display));
    if ($displayKey === '') {
        continue;
    }

    if (isset($hiddenBrandDisplayKeys[$displayKey])) {
        continue;
    }

    if (!isset($brandDisplayToKey[$displayKey])) {
        $brandDisplayToKey[$displayKey] = (string)$brandKey;
        continue;
    }

    $currentKey = $brandDisplayToKey[$displayKey];
    $currentCount = count($brands[$currentKey]['products'] ?? []);
    $newCount = count($brands[$brandKey]['products'] ?? []);
    if ($newCount > $currentCount) {
        $brandDisplayToKey[$displayKey] = (string)$brandKey;
    }
}

$brandNames = array_values($brandDisplayToKey);
usort($brandNames, static function (string $a, string $b): int {
    $rankA = andison_brand_order_rank(andison_brand_display_label($a));
    $rankB = andison_brand_order_rank(andison_brand_display_label($b));

    if ($rankA !== $rankB) {
        return $rankA <=> $rankB;
    }

    return strcasecmp(andison_brand_display_label($a), andison_brand_display_label($b));
});

$brandOrderLabels = array_map(static function (string $brandKey): string {
    return andison_brand_display_label((string)$brandKey);
}, $brandNames);

if ($selectedBrandDisplay === '') {
    $selectedBrandDisplay = $brandNames[0] ?? '';
} else {
    $selectedDisplayKey = strtolower(trim(andison_brand_display_label($selectedBrandDisplay)));
    if (isset($brandDisplayToKey[$selectedDisplayKey])) {
        $selectedBrandDisplay = $brandDisplayToKey[$selectedDisplayKey];
    }
}

$selectedBrandKey = andison_pick_brand_bucket($brands, andison_brand_data_candidates($selectedBrandDisplay));
if ($selectedBrandKey === '' || !isset($brands[$selectedBrandKey])) {
    $selectedBrandKey = $brandNames[0] ?? '';
}
$selectedBrand = $selectedBrandDisplay;

$allCategories = andison_get_categories();
$maxProductImages = 8;

function andison_env_flag(string $key, bool $default = false): bool
{
    $raw = function_exists('andison_admin_env') ? andison_admin_env($key) : null;
    if ($raw === null) {
        $value = getenv($key);
        if ($value === false && isset($_ENV[$key])) {
            $value = $_ENV[$key];
        }
        if ($value === false && isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        }
        $raw = $value === false ? null : trim((string)$value);
    }

    if ($raw === null || $raw === '') {
        return $default;
    }

    return in_array(strtolower($raw), ['1', 'true', 'yes', 'on'], true);
}

$allowProductDelete = andison_env_flag('ANDISON_ALLOW_PRODUCT_DELETE', true);

function andison_safe_filename(string $name): string
{
    $name = strtolower($name);
    $name = preg_replace('~[^a-z0-9._-]+~', '_', $name) ?? $name;
    $name = trim($name, '._-');
    return $name !== '' ? $name : 'file';
}

function andison_is_allowed_image_upload(array $file, array $allowedExt, array $allowedMime): bool
{
    $ext = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return false;
    }

    $tmpName = (string)($file['tmp_name'] ?? '');
    if ($tmpName === '' || !is_file($tmpName)) {
        return false;
    }

    $mime = '';
    $fi = finfo_open(FILEINFO_MIME_TYPE);
    if ($fi !== false) {
        $detected = finfo_file($fi, $tmpName);
        finfo_close($fi);
        if (is_string($detected)) {
            $mime = strtolower(trim($detected));
        }
    }

    if ($mime !== '' && in_array($mime, $allowedMime, true)) {
        return true;
    }

    // Some servers return octet-stream/empty MIME for AVIF/JFIF despite valid file contents.
    if (($ext === 'avif' || $ext === 'jfif') && ($mime === '' || $mime === 'application/octet-stream' || $mime === 'image/octet-stream')) {
        return true;
    }

    if (function_exists('exif_imagetype')) {
        $type = @exif_imagetype($tmpName);
        if ($type !== false) {
            return true;
        }
    }

    return false;
}

function andison_normalize_category_assignment(array $allCategories, string $categoryId, string $subcategoryId, string $subSubcategoryId): array
{
    $categoryId = trim($categoryId);
    $subcategoryId = trim($subcategoryId);
    $subSubcategoryId = trim($subSubcategoryId);

    $normalizeKey = static function (string $value): string {
        $value = strtolower(trim($value));
        $value = preg_replace('~[^a-z0-9]+~', ' ', $value) ?? $value;
        return trim(preg_replace('~\s+~', ' ', $value) ?? $value);
    };

    if ($categoryId === '') {
        return [
            'category_id' => '',
            'subcategory_id' => '',
            'sub_subcategory_id' => '',
        ];
    }

    $category = null;
    foreach ($allCategories as $cat) {
        if ((string)($cat['id'] ?? '') === $categoryId) {
            $category = $cat;
            break;
        }
    }

    // Allow CSV to provide category name instead of category id.
    if (!is_array($category)) {
        $categoryLookup = $normalizeKey($categoryId);
        foreach ($allCategories as $cat) {
            $catName = (string)($cat['name'] ?? '');
            if ($catName !== '' && $normalizeKey($catName) === $categoryLookup) {
                $category = $cat;
                $categoryId = (string)($cat['id'] ?? '');
                break;
            }
        }

        // Fuzzy fallback for CSV values like "Power Tools & Accessories".
        if (!is_array($category) && $categoryLookup !== '') {
            foreach ($allCategories as $cat) {
                $catName = (string)($cat['name'] ?? '');
                $catNameKey = $normalizeKey($catName);
                if ($catNameKey === '') {
                    continue;
                }
                if (str_contains($categoryLookup, $catNameKey) || str_contains($catNameKey, $categoryLookup)) {
                    $category = $cat;
                    $categoryId = (string)($cat['id'] ?? '');
                    break;
                }
            }
        }
    }

    if (!is_array($category)) {
        // Invalid category should not block inserts; clear hierarchy instead.
        return [
            'category_id' => '',
            'subcategory_id' => '',
            'sub_subcategory_id' => '',
        ];
    }

    $subcategoryIds = [];
    $subcategoryNameToId = [];
    $subSubParent = [];
    $subSubNameToId = [];
    foreach (($category['subcategories'] ?? []) as $sub) {
        $subId = (string)($sub['id'] ?? '');
        $subName = (string)($sub['name'] ?? '');
        if ($subId === '') {
            continue;
        }
        $subcategoryIds[$subId] = true;
        if ($subName !== '') {
            $subcategoryNameToId[$normalizeKey($subName)] = $subId;
        }

        foreach (($sub['subcategories'] ?? []) as $subSub) {
            $subSubId = (string)($subSub['id'] ?? '');
            $subSubName = (string)($subSub['name'] ?? '');
            if ($subSubId === '') {
                continue;
            }
            $subSubParent[$subSubId] = $subId;
            if ($subSubName !== '') {
                $subSubNameToId[$normalizeKey($subSubName)] = $subSubId;
            }
        }
    }

    // Allow CSV subcategory/sub-subcategory names.
    if ($subcategoryId !== '' && !isset($subcategoryIds[$subcategoryId])) {
        $lookup = $normalizeKey($subcategoryId);
        if (isset($subcategoryNameToId[$lookup])) {
            $subcategoryId = $subcategoryNameToId[$lookup];
        }
    }
    if ($subSubcategoryId !== '' && !isset($subSubParent[$subSubcategoryId])) {
        $lookup = $normalizeKey($subSubcategoryId);
        if (isset($subSubNameToId[$lookup])) {
            $subSubcategoryId = $subSubNameToId[$lookup];
        }
    }

    if ($subSubcategoryId !== '' && isset($subSubParent[$subSubcategoryId])) {
        $subcategoryId = $subSubParent[$subSubcategoryId];
    } elseif ($subSubcategoryId !== '') {
        $subSubcategoryId = '';
    }

    if ($subcategoryId !== '' && !isset($subcategoryIds[$subcategoryId])) {
        // Backward compatibility: legacy rows may have saved sub-subcategory id into subcategory_id.
        if ($subSubcategoryId === '' && isset($subSubParent[$subcategoryId])) {
            $subSubcategoryId = $subcategoryId;
            $subcategoryId = $subSubParent[$subcategoryId];
        } else {
            $subcategoryId = '';
        }
    }

    if ($subcategoryId === '' && $subSubcategoryId !== '' && isset($subSubParent[$subSubcategoryId])) {
        $subcategoryId = $subSubParent[$subSubcategoryId];
    }

    return [
        'category_id' => $categoryId,
        'subcategory_id' => $subcategoryId,
        'sub_subcategory_id' => $subSubcategoryId,
    ];
}

/**
 * Handle up to N product images (image_file_0 … image_file_(N-1)).
 * existing_images POST param is a JSON array of current URLs to keep per slot.
 * Returns array of image URLs (empty slots are omitted).
 */
function andison_handle_multi_image_upload(): array
{
    global $maxProductImages;
    $existingJson = isset($_POST['existing_images']) ? trim((string)$_POST['existing_images']) : '[]';
    $existing     = json_decode($existingJson, true);
    if (!is_array($existing)) $existing = [];
    while (count($existing) < $maxProductImages) $existing[] = '';

    $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'jfif'];
    $allowed_mime = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'image/jfif'];
    $result  = [];

    for ($i = 0; $i < $maxProductImages; $i++) {
        $fieldName   = 'image_file_' . $i;
        $existingUrl = trim((string)($existing[$i] ?? ''));

        if (!empty($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES[$fieldName];
            if (andison_is_allowed_image_upload($f, $allowed_ext, $allowed_mime)) {
                $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
                $base     = andison_safe_filename(pathinfo((string)($f['name'] ?? ''), PATHINFO_FILENAME));
                $destName = $base . '_' . date('Ymd_His') . '_' . $i . '.' . $ext;
                $url      = andison_sb_storage_upload_tmp($f, 'product-images', $destName);
                if ($url !== null) {
                    $result[] = $url;
                    continue;
                }
            }
        }

        if ($existingUrl !== '') {
            $result[] = $existingUrl;
        }
    }

    return array_values($result);
}

/**
 * Handle optional datasheet PDF upload (field: datasheet_file).
 * Keeps existing URL from hidden input if no new file uploaded.
 */
function andison_handle_datasheet_upload(): string
{
    $existingUrl = isset($_POST['existing_datasheet']) ? trim((string)$_POST['existing_datasheet']) : '';

    if (!empty($_FILES['datasheet_file']) && $_FILES['datasheet_file']['error'] === UPLOAD_ERR_OK) {
        $f    = $_FILES['datasheet_file'];
        $ext  = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
        $fi   = finfo_open(FILEINFO_MIME_TYPE);
        $mime = (string)finfo_file($fi, $f['tmp_name']);
        finfo_close($fi);
        if ($ext === 'pdf' && $mime === 'application/pdf') {
            $base     = andison_safe_filename(pathinfo((string)($f['name'] ?? ''), PATHINFO_FILENAME));
            $destName = $base . '_' . date('Ymd_His') . '.pdf';
            $url      = andison_sb_storage_upload_tmp($f, 'datasheets', $destName);
            if ($url !== null) return $url;
        }
    }

    return $existingUrl;
}

/**
 * Handle optional specifications-side image upload (field: spec_image_file).
 * Keeps existing URL from hidden input if no new file uploaded.
 */
function andison_handle_spec_side_image_upload(): string
{
    $existingUrl = isset($_POST['existing_spec_image']) ? trim((string)$_POST['existing_spec_image']) : '';

    if (!empty($_FILES['spec_image_file']) && $_FILES['spec_image_file']['error'] === UPLOAD_ERR_OK) {
        $f = $_FILES['spec_image_file'];
        $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'jfif'];
        $allowedMime = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'image/jfif'];

        if (andison_is_allowed_image_upload($f, $allowedExt, $allowedMime)) {
            $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
            $base = andison_safe_filename(pathinfo((string)($f['name'] ?? ''), PATHINFO_FILENAME));
            $destName = 'spec-side/' . $base . '_' . date('Ymd_His') . '.'.$ext;
            // Reuse the known-working product-images bucket to avoid missing-bucket upload failures.
            $url = andison_sb_storage_upload_tmp($f, 'product-images', $destName);
            if ($url !== null) {
                return $url;
            }
        }
    }

    return $existingUrl;
}

/**
 * Persist spec-side image URL inside specifications JSON payload.
 * Uses existing structured payload when possible; wraps plain text when image is present.
 */
function andison_apply_spec_side_image_to_specifications(string $rawSpecs, string $specImageUrl): string
{
    $source = trim($rawSpecs);
    $image = trim($specImageUrl);

    if ($source !== '') {
        $parsed = json_decode($source, true);
        if (is_array($parsed) && isset($parsed['format']) && is_string($parsed['format'])) {
            if ($image !== '') {
                $parsed['specImage'] = $image;
            } else {
                unset($parsed['specImage'], $parsed['spec_image']);
            }

            $encoded = json_encode($parsed, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (is_string($encoded) && $encoded !== '') {
                return $encoded;
            }
        }
    }

    if ($image === '') {
        return $rawSpecs;
    }

    $payload = [
        'format' => 'andison_specs_v3',
        'text' => $source,
        'tables' => [],
        'specImage' => $image,
    ];

    $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return is_string($encoded) && $encoded !== '' ? $encoded : $rawSpecs;
}

/**
 * Sanitize rich HTML used for brand descriptions.
 * Allows table/image formatting while stripping dangerous markup.
 */
function andison_sanitize_brand_description_html(string $html): string
{
    $value = trim($html);
    if ($value === '') {
        return '';
    }

    $value = preg_replace('~<(script|style|iframe|object|embed|form|input|button|textarea|select)[^>]*>.*?</\\1>~is', '', $value) ?? $value;
    $value = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $value) ?? $value;

    $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><a><span><div><table><thead><tbody><tfoot><tr><th><td><img>';
    $value = strip_tags($value, $allowedTags);

    // Block javascript: and data:text/html URI payloads in links/images.
    $value = preg_replace_callback('/\s(href|src)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', static function (array $m): string {
        $attr = strtolower((string)$m[1]);
        $raw = (string)$m[2];
        $quote = '';
        if ($raw !== '' && ($raw[0] === '"' || $raw[0] === '\'')) {
            $quote = $raw[0];
            $raw = trim($raw, "\"'");
        }
        $check = strtolower(trim($raw));
        if (str_starts_with($check, 'javascript:') || str_starts_with($check, 'data:text/html')) {
            $raw = '#';
        }
        $escaped = htmlspecialchars($raw, ENT_QUOTES);
        if ($quote === '"' || $quote === '\'') {
            return ' ' . $attr . '=' . $quote . $escaped . $quote;
        }
        return ' ' . $attr . '="' . $escaped . '"';
    }, $value) ?? $value;

    return trim($value);
}

function andison_handle_brand_description_image_upload(): string
{
    if (empty($_FILES['description_image_file']) || $_FILES['description_image_file']['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No image uploaded.');
    }

    $f = $_FILES['description_image_file'];
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'jfif'];
    $allowedMime = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'image/jfif'];
    if (!andison_is_allowed_image_upload($f, $allowedExt, $allowedMime)) {
        throw new RuntimeException('Invalid image type. Use JPG, JFIF, PNG, WEBP, GIF, or AVIF.');
    }

    $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));

    if (($f['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('Image too large (max 8 MB).');
    }

    $base = andison_safe_filename(pathinfo((string)($f['name'] ?? 'description-image'), PATHINFO_FILENAME));
    $destName = 'brand-desc_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '_' . $base . '.' . $ext;
    $url = andison_sb_storage_upload_tmp($f, 'product-images', $destName);
    if ($url === '') {
        throw new RuntimeException('Image upload failed.');
    }

    return $url;
}

function andison_handle_brand_logo_upload(string $brandName, string $fileField = 'new_brand_logo', bool $required = true): string
{
    if (empty($_FILES[$fileField])) {
        if ($required) {
            throw new RuntimeException('Brand image is required.');
        }
        return '';
    }

    $uploadError = (int)($_FILES[$fileField]['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($uploadError === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            throw new RuntimeException('Brand image is required.');
        }
        return '';
    }
    if ($uploadError !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Brand image upload failed.');
    }

    $f = $_FILES[$fileField];
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif', 'jfif'];
    $allowedMime = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'image/jfif'];
    if (!andison_is_allowed_image_upload($f, $allowedExt, $allowedMime)) {
        throw new RuntimeException('Invalid brand image type. Use JPG, JFIF, PNG, WEBP, GIF, or AVIF.');
    }

    $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));

    if (($f['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('Brand image too large (max 8 MB).');
    }

    $base = andison_safe_filename($brandName !== '' ? $brandName : pathinfo((string)($f['name'] ?? 'brand-logo'), PATHINFO_FILENAME));
    $destName = 'brand-logos/' . $base . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $url = andison_sb_storage_upload_tmp($f, 'product-images', $destName);
    if (!is_string($url) || trim($url) === '') {
        throw new RuntimeException('Brand image upload failed.');
    }

    return trim($url);
}

// ── CSV Template download (GET) ──────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'download_csv_template') {
    andison_require_admin();
    $headers = ['brand','product_name','model','type','price','badge','description','specifications','category_id','subcategory_id','sub_subcategory_id','image_url','datasheet_url'];
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="products_import_template.csv"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    fclose($out);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';
    $brand = isset($_POST['brand']) ? (string)$_POST['brand'] : '';

    if ($action === 'add_brand') {
        $newBrandName = trim((string)($_POST['new_brand_name'] ?? ''));
        $newBrandDescription = trim((string)($_POST['new_brand_description'] ?? ''));

        if ($newBrandName === '') {
            andison_set_flash('error', 'Brand name is required.');
            header('Location: products.php');
            exit;
        }

        try {
            $newBrandLogoUrl = andison_handle_brand_logo_upload($newBrandName);
        } catch (Throwable $e) {
            andison_set_flash('error', $e->getMessage());
            header('Location: products.php');
            exit;
        }

        if (andison_create_brand($newBrandName, $newBrandDescription, $newBrandLogoUrl)) {
            andison_set_flash('success', 'Brand added.');
            header('Location: products.php?brand=' . urlencode(andison_canonical_brand_name($newBrandName)));
            exit;
        }

        andison_set_flash('error', 'Failed to add brand. Please try again.');
        header('Location: products.php');
        exit;
    }

    if ($action === 'delete_brand') {
        $brandToDelete = trim((string)($_POST['brand_to_delete'] ?? $brand));
        if ($brandToDelete === '') {
            andison_set_flash('error', 'Please choose a brand to delete.');
            header('Location: products.php');
            exit;
        }

        if (andison_delete_brand($brandToDelete)) {
            andison_set_flash('success', 'Brand deleted.');
            header('Location: products.php');
            exit;
        }

        andison_set_flash('error', 'Failed to delete brand.');
        header('Location: products.php?brand=' . urlencode($brandToDelete));
        exit;
    }

    if ($action === 'reorder_brands') {
        $brandOrder = json_decode((string)($_POST['brand_order'] ?? '[]'), true);
        if (!is_array($brandOrder) || empty($brandOrder)) {
            andison_set_flash('error', 'No brand order received.');
            header('Location: products.php');
            exit;
        }

        if (andison_save_brand_order($brandOrder)) {
            andison_set_flash('success', 'Brand order updated.');
        } else {
            andison_set_flash('error', 'Failed to save brand order.');
        }

        header('Location: products.php');
        exit;
    }

    if ($action === 'reorder_products') {
        $brandToReorder = trim((string)($_POST['brand'] ?? $brand));
        $brandToReorder = andison_pick_brand_bucket($brands, andison_brand_data_candidates($brandToReorder));

        if ($brandToReorder === '' || !isset($brands[$brandToReorder])) {
            andison_set_flash('error', 'Please choose a brand to reorder.');
            header('Location: products.php');
            exit;
        }

        $orderPayload = json_decode((string)($_POST['product_order'] ?? '[]'), true);
        if (!is_array($orderPayload) || empty($orderPayload)) {
            andison_set_flash('error', 'No product order received.');
            header('Location: products.php?brand=' . urlencode($brandToReorder));
            exit;
        }

        $currentProducts = isset($brands[$brandToReorder]['products']) && is_array($brands[$brandToReorder]['products'])
            ? array_values($brands[$brandToReorder]['products'])
            : [];
        if (empty($currentProducts)) {
            andison_set_flash('warning', 'No products to reorder.');
            header('Location: products.php?brand=' . urlencode($brandToReorder));
            exit;
        }

        $productMap = [];
        foreach ($currentProducts as $idx => $productRow) {
            if (!is_array($productRow)) {
                continue;
            }

            $token = isset($productRow['id']) ? ('id:' . (int)$productRow['id']) : ('idx:' . (int)$idx);
            $productMap[$token] = $productRow;
        }

        $reorderedProducts = [];
        foreach ($orderPayload as $token) {
            $token = trim((string)$token);
            if ($token === '' || !isset($productMap[$token])) {
                continue;
            }

            $reorderedProducts[] = $productMap[$token];
            unset($productMap[$token]);
        }

        foreach ($productMap as $leftoverProduct) {
            $reorderedProducts[] = $leftoverProduct;
        }

        if (empty($reorderedProducts)) {
            andison_set_flash('error', 'Failed to reorder products.');
            header('Location: products.php?brand=' . urlencode($brandToReorder));
            exit;
        }

        $brands[$brandToReorder]['products'] = array_values($reorderedProducts);
        if (andison_save_single_brand($brandToReorder, $brands[$brandToReorder], ['allowProductCountDecrease' => true])) {
            andison_set_flash('success', 'Product order updated.');
        } else {
            andison_set_flash('error', 'Failed to save product order.');
        }

        header('Location: products.php?brand=' . urlencode($brandToReorder));
        exit;
    }

    if ($action === 'edit_brand_logo') {
        $originalBrand = trim((string)($_POST['brand_original'] ?? $brand));
        $brandToEditInput = trim((string)($_POST['brand_to_edit'] ?? $originalBrand));
        if ($brandToEditInput === '') {
            andison_set_flash('error', 'Please choose a brand to edit.');
            header('Location: products.php');
            exit;
        }

        $sourceBrand = andison_pick_brand_bucket($brands, andison_brand_data_candidates($originalBrand));
        if ($sourceBrand === '') {
            $sourceBrand = andison_canonical_brand_name($originalBrand);
        }

        $sourceMeta = isset($brands[$sourceBrand]) && is_array($brands[$sourceBrand]) ? $brands[$sourceBrand] : [];
        if (empty($sourceMeta)) {
            $sourceRow = andison_sb_select('brands', 'select=name,description&name=eq.' . rawurlencode($sourceBrand) . '&limit=1');
            if (!empty($sourceRow[0]) && is_array($sourceRow[0])) {
                $sourceMeta = andison_brand_row_unpack((array)$sourceRow[0]);
            }
        }

        $targetBrand = andison_canonical_brand_name($brandToEditInput);
        if ($targetBrand === '') {
            $targetBrand = $sourceBrand;
        }

        try {
            $updatedLogoUrl = andison_handle_brand_logo_upload($targetBrand, 'edit_brand_logo', false);
        } catch (Throwable $e) {
            andison_set_flash('error', $e->getMessage());
            header('Location: products.php?brand=' . urlencode($sourceBrand));
            exit;
        }

        $descriptionToSave = isset($_POST['brand_description'])
            ? andison_sanitize_brand_description_html((string)$_POST['brand_description'])
            : trim((string)($sourceMeta['description'] ?? ''));
        $logoToSave = $updatedLogoUrl !== '' ? $updatedLogoUrl : trim((string)($sourceMeta['logo'] ?? ''));
        $shortLabelToSave = isset($_POST['brand_short_label'])
            ? trim((string)$_POST['brand_short_label'])
            : trim((string)($sourceMeta['short_label'] ?? ''));

        $saved = andison_create_brand($targetBrand, $descriptionToSave, $logoToSave, $shortLabelToSave);
        if ($saved) {
            $sourceKey = strtolower(trim($sourceBrand));
            $targetKey = strtolower(trim($targetBrand));

            if ($sourceKey !== '' && $targetKey !== '' && $sourceKey !== $targetKey) {
                andison_sb_update('products', ['brand' => $targetBrand], 'brand=ilike.' . rawurlencode($sourceBrand));
                andison_sb_delete('brands', 'name=eq.' . rawurlencode($sourceBrand));
            }

            andison_set_flash('success', $updatedLogoUrl !== '' ? 'Brand updated.' : 'Brand saved.');
            header('Location: products.php?brand=' . urlencode($targetBrand));
            exit;
        }

        andison_set_flash('error', 'Failed to update brand. Please try again.');
        header('Location: products.php?brand=' . urlencode($sourceBrand));
        exit;
    }

    $brand = andison_pick_brand_bucket($brands, andison_brand_data_candidates($brand));

    if ($brand !== '' && isset($brands[$brand])) {
        if ($action === 'upload_brand_description_image') {
            header('Content-Type: application/json; charset=UTF-8');
            try {
                $url = andison_handle_brand_description_image_upload();
                echo json_encode(['location' => $url], JSON_UNESCAPED_SLASHES);
            } catch (Throwable $e) {
                http_response_code(400);
                echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_SLASHES);
            }
            exit;
        }

        if ($action === 'import_csv') {
            $errors = [];
            $imported = 0;

            if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                andison_set_flash('error', 'No CSV file uploaded.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            $f    = $_FILES['csv_file'];
            $ext  = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
            $mime = mime_content_type($f['tmp_name']);
            if (!in_array($ext, ['csv', 'txt'], true) || !in_array($mime, ['text/plain','text/csv','application/csv','application/vnd.ms-excel'], true)) {
                andison_set_flash('error', 'Invalid file type. Please upload a .csv file.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            $handle = fopen($f['tmp_name'], 'r');
            if ($handle === false) {
                andison_set_flash('error', 'Could not read CSV file.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            $expectedHeadersV1 = ['product_name','model','type','price','badge','description','specifications','category_id','subcategory_id','image_url','datasheet_url'];
            $expectedHeadersV2 = ['product_name','model','type','price','badge','description','specifications','category_id','subcategory_id','sub_subcategory_id','image_url','datasheet_url'];
            $expectedHeadersV3 = ['brand','product_name','model','type','price','badge','description','specifications','category_id','subcategory_id','sub_subcategory_id','image_url','datasheet_url'];
            $headerRow = fgetcsv($handle);
            if (is_array($headerRow) && isset($headerRow[0])) {
                $headerRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$headerRow[0]) ?? (string)$headerRow[0];
            }
            $normalizedHeaders = $headerRow === false ? [] : array_map('strtolower', array_map('trim', $headerRow));
            $isV1 = $normalizedHeaders === $expectedHeadersV1;
            $isV2 = $normalizedHeaders === $expectedHeadersV2;
            $isV3 = $normalizedHeaders === $expectedHeadersV3;
            if (!$isV1 && !$isV2 && !$isV3) {
                fclose($handle);
                andison_set_flash('error', 'CSV headers do not match the template. Please download and use the official template.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            $affectedBrands = [];
            $rowNum = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (count($row) < 9) { $errors[] = "Row {$rowNum}: too few columns, skipped."; continue; }

                $row = array_pad($row, $isV3 ? 13 : ($isV2 ? 12 : 11), '');
                $rowBrand = $isV3 ? trim((string)$row[0]) : '';
                $targetBrand = $brand;
                if ($rowBrand !== '') {
                    $targetBrand = andison_pick_brand_bucket($brands, andison_brand_data_candidates($rowBrand));
                    if ($targetBrand === '' || !isset($brands[$targetBrand])) {
                        $targetBrand = $rowBrand;
                        if (!isset($brands[$targetBrand])) {
                            $brands[$targetBrand] = ['description' => '', 'products' => []];
                        }
                    }
                }

                if (empty($brands[$targetBrand]['products']) || !is_array($brands[$targetBrand]['products'])) {
                    $brands[$targetBrand]['products'] = [];
                }

                $model = trim($isV3 ? $row[2] : $row[1]);
                $type  = trim($isV3 ? $row[3] : $row[2]);
                if ($model === '' || $type === '') {
                    $errors[] = "Row {$rowNum}: model and type are required, skipped.";
                    continue;
                }

                $rawCategoryId = trim((string)($isV3 ? $row[8] : $row[7]));
                $rawSubcategoryId = trim((string)($isV3 ? $row[9] : $row[8]));
                $rawSubSubcategoryId = $isV2 || $isV3 ? trim((string)($isV3 ? $row[10] : $row[9])) : '';
                $normalizedAssignment = andison_normalize_category_assignment(
                    $allCategories,
                    $rawCategoryId,
                    $rawSubcategoryId,
                    $rawSubSubcategoryId
                );

                $imageIndex = $isV3 ? 11 : ($isV2 ? 10 : 9);
                $datasheetIndex = $isV3 ? 12 : ($isV2 ? 11 : 10);
                $imageUrl = filter_var(trim((string)$row[$imageIndex]), FILTER_VALIDATE_URL) ? trim((string)$row[$imageIndex]) : '';
                $datasheetUrl = filter_var(trim((string)$row[$datasheetIndex]), FILTER_VALIDATE_URL) ? trim((string)$row[$datasheetIndex]) : '';
                $images = $imageUrl !== '' ? [$imageUrl] : [];

                $brands[$targetBrand]['products'][] = [
                    'brand'          => $targetBrand,
                    'product_name'   => trim($isV3 ? $row[1] : $row[0]),
                    'model'          => $model,
                    'type'           => $type,
                    'price'          => trim($isV3 ? $row[4] : $row[3]),
                    'badge'          => trim($isV3 ? $row[5] : $row[4]),
                    'description'    => trim($isV3 ? $row[6] : $row[5]),
                    'specifications' => trim($isV3 ? $row[7] : $row[6]),
                    'category_id'    => $normalizedAssignment['category_id'],
                    'subcategory_id' => $normalizedAssignment['subcategory_id'],
                    'sub_subcategory_id' => $normalizedAssignment['sub_subcategory_id'],
                    'image'          => $imageUrl,
                    'images'         => $images,
                    'datasheet'      => $datasheetUrl,
                ];
                $imported++;
                $affectedBrands[$targetBrand] = true;
            }
            fclose($handle);

            error_log("CSV Import: Total imported: {$imported}, Total errors: " . count($errors));
            error_log("CSV Import: Affected brands: " . implode(", ", array_keys($affectedBrands)));
            foreach (array_keys($affectedBrands) as $affBrand) {
                $prodCnt = count($brands[$affBrand]['products'] ?? []);
                error_log("CSV Import: Brand '{$affBrand}' has {$prodCnt} products ready to save");
                if ($prodCnt > 0) {
                    // Log first product as sample
                    $first = $brands[$affBrand]['products'][0];
                    error_log("CSV Import: Sample product - model: '" . ($first['model'] ?? 'N/A') . "', type: '" . ($first['type'] ?? 'N/A') . "'");
                }
            }

            $saveOk = true;
            $redirectBrand = $brand;
            if ($imported > 0) {
                error_log("CSV Import: Processing " . count($affectedBrands) . " affected brand(s)");
                foreach (array_keys($affectedBrands) as $affectedBrand) {
                    error_log("CSV Import: Saving " . count($brands[$affectedBrand]['products']) . " products for brand: {$affectedBrand}");
                    $result = andison_save_single_brand($affectedBrand, $brands[$affectedBrand], [
                        'allowProductCountDecrease' => true,
                        'allowEmptyProducts' => true,
                    ]);
                    if (!$result) {
                        error_log("CSV Import: Save failed for brand {$affectedBrand}");
                    } else {
                        error_log("CSV Import: Save succeeded for brand {$affectedBrand}");
                    }
                    $saveOk = $result && $saveOk;
                }
                @unlink(__DIR__ . '/../data/_cache/brands_full.cache');
                // Redirect to the first affected brand so user sees imported products immediately
                $redirectBrand = array_key_first($affectedBrands) ?: $brand;
            }

            $msg = "Imported {$imported} product(s).";
            if (!empty($errors)) $msg .= ' Skipped rows: ' . implode(' | ', $errors);
            if ($imported > 0 && $saveOk) {
                andison_set_flash('success', $msg);
            } elseif ($imported > 0) {
                andison_set_flash('error', $msg . ' Save failed for one or more brands due to invalid category mapping. Please fix category/subcategory values in CSV and try again.');
            } else {
                andison_set_flash('error', $msg);
            }
            header('Location: products.php?brand=' . urlencode($redirectBrand));
            exit;
        }

        if ($action === 'update_brand') {
            $desc = isset($_POST['description']) ? andison_sanitize_brand_description_html((string)$_POST['description']) : '';
            $brands[$brand]['description'] = $desc;
            if (andison_save_single_brand($brand, $brands[$brand])) {
                andison_set_flash('success', 'Brand description updated.');
            } else {
                andison_set_flash('error', 'Save blocked to protect existing products. Please refresh and try again.');
            }
            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'add_product') {
            $name = isset($_POST['product_name']) ? trim((string)$_POST['product_name']) : '';
            $model = isset($_POST['model']) ? trim((string)$_POST['model']) : '';
            $type = isset($_POST['type']) ? trim((string)$_POST['type']) : '';
            $price = isset($_POST['price']) ? trim((string)$_POST['price']) : '';
            $badge = isset($_POST['badge']) ? trim((string)$_POST['badge']) : '';
            $descRaw = isset($_POST['product_description']) ? (string)$_POST['product_description'] : '';
            $descMode = isset($_POST['product_description_mode']) ? strtolower(trim((string)$_POST['product_description_mode'])) : 'plain';
            $desc = $descMode === 'table'
                ? andison_sanitize_brand_description_html($descRaw)
                : trim($descRaw);
            $specs = isset($_POST['specifications']) ? trim((string)$_POST['specifications']) : '';
            $catId  = isset($_POST['category_id'])    ? trim((string)$_POST['category_id'])    : '';
            $subId  = isset($_POST['subcategory_id']) ? trim((string)$_POST['subcategory_id']) : '';
            $subSubId = isset($_POST['sub_subcategory_id']) ? trim((string)$_POST['sub_subcategory_id']) : '';
            $normalizedAssignment = andison_normalize_category_assignment($allCategories, $catId, $subId, $subSubId);
            $catId = $normalizedAssignment['category_id'];
            $subId = $normalizedAssignment['subcategory_id'];
            $subSubId = $normalizedAssignment['sub_subcategory_id'];
            $images    = andison_handle_multi_image_upload();
            $image     = $images[0] ?? '';
            $datasheet = andison_handle_datasheet_upload();
            $specSideImage = andison_handle_spec_side_image_upload();
            $specs = andison_apply_spec_side_image_to_specifications($specs, $specSideImage);

            if ($model === '' || $type === '') {
                andison_set_flash('error', 'Model and Type are required.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            if (empty($brands[$brand]['products']) || !is_array($brands[$brand]['products'])) {
                $brands[$brand]['products'] = [];
            }

            $brands[$brand]['products'][] = [
                'brand'          => $brand,
                'product_name'   => $name,
                'model'          => $model,
                'type'           => $type,
                'price'          => $price,
                'badge'          => $badge,
                'description'    => $desc,
                'specifications' => $specs,
                'image'          => $image,
                'images'         => $images,
                'datasheet'      => $datasheet,
                'category_id'    => $catId,
                'subcategory_id' => $subId,
                'sub_subcategory_id' => $subSubId,
            ];

            if (andison_save_single_brand($brand, $brands[$brand])) {
                andison_set_flash('success', 'Product added.');
            } else {
                andison_set_flash('error', 'Save blocked to protect existing products. Please refresh and try again.');
            }

            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'update_product') {
            $idx = isset($_POST['index']) ? (int)$_POST['index'] : -1;
            if ($idx < 0 || empty($brands[$brand]['products'][$idx])) {
                andison_set_flash('error', 'Product not found.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            $name = isset($_POST['product_name']) ? trim((string)$_POST['product_name']) : '';
            $model = isset($_POST['model']) ? trim((string)$_POST['model']) : '';
            $type = isset($_POST['type']) ? trim((string)$_POST['type']) : '';
            $price = isset($_POST['price']) ? trim((string)$_POST['price']) : '';
            $badge = isset($_POST['badge']) ? trim((string)$_POST['badge']) : '';
            $descRaw = isset($_POST['product_description']) ? (string)$_POST['product_description'] : '';
            $descMode = isset($_POST['product_description_mode']) ? strtolower(trim((string)$_POST['product_description_mode'])) : 'plain';
            $desc = $descMode === 'table'
                ? andison_sanitize_brand_description_html($descRaw)
                : trim($descRaw);
            $specs = isset($_POST['specifications']) ? trim((string)$_POST['specifications']) : '';
            $catId  = isset($_POST['category_id'])    ? trim((string)$_POST['category_id'])    : '';
            $subId  = isset($_POST['subcategory_id']) ? trim((string)$_POST['subcategory_id']) : '';
            $subSubId = isset($_POST['sub_subcategory_id']) ? trim((string)$_POST['sub_subcategory_id']) : '';
            $normalizedAssignment = andison_normalize_category_assignment($allCategories, $catId, $subId, $subSubId);
            $catId = $normalizedAssignment['category_id'];
            $subId = $normalizedAssignment['subcategory_id'];
            $subSubId = $normalizedAssignment['sub_subcategory_id'];
            $images = andison_handle_multi_image_upload();
            // If no images submitted at all, fall back to existing product images
            if (empty($images)) {
                $oldImgs = $brands[$brand]['products'][$idx]['images'] ?? [];
                if (empty($oldImgs) && !empty($brands[$brand]['products'][$idx]['image'])) {
                    $oldImgs = [$brands[$brand]['products'][$idx]['image']];
                }
                $images = $oldImgs;
            }
            $image     = $images[0] ?? '';
            $datasheet = andison_handle_datasheet_upload();
            $specSideImage = andison_handle_spec_side_image_upload();
            $specs = andison_apply_spec_side_image_to_specifications($specs, $specSideImage);
            // Keep existing datasheet if none uploaded and no new URL provided
            if ($datasheet === '') {
                $datasheet = (string)($brands[$brand]['products'][$idx]['datasheet'] ?? '');
            }

            if ($model === '' || $type === '') {
                andison_set_flash('error', 'Model and Type are required.');
                header('Location: products.php?brand=' . urlencode($brand) . '&edit=' . $idx);
                exit;
            }

            $brands[$brand]['products'][$idx] = [
                'brand'          => $brand,
                'product_name'   => $name,
                'model'          => $model,
                'type'           => $type,
                'price'          => $price,
                'badge'          => $badge,
                'description'    => $desc,
                'specifications' => $specs,
                'image'          => $image,
                'images'         => $images,
                'datasheet'      => $datasheet,
                'category_id'    => $catId,
                'subcategory_id' => $subId,
                'sub_subcategory_id' => $subSubId,
            ];

            if (andison_save_single_brand($brand, $brands[$brand])) {
                andison_set_flash('success', 'Product updated.');
            } else {
                andison_set_flash('error', 'Save blocked to protect existing products. Please refresh and try again.');
            }

            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'delete_product') {
            if (!$allowProductDelete) {
                andison_set_flash('error', 'Product deletion is disabled in safe mode. Set ANDISON_ALLOW_PRODUCT_DELETE=1 to enable delete actions.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            $productToken = isset($_POST['product_id']) ? trim((string)$_POST['product_id']) : '';
            if (preg_match('/^id:(\d+)$/', $productToken, $m) === 1) {
                $productId = (int)$m[1];
                if ($productId > 0) {
                    $deleted = andison_sb_delete('products', 'id=eq.' . $productId);
                    if ($deleted) {
                        @unlink(__DIR__ . '/../data/_cache/brands_full.cache');
                        andison_set_flash('success', 'Product deleted.');
                    } else {
                        andison_set_flash('error', 'Delete failed for this product ID. Please refresh and try again.');
                    }
                    header('Location: products.php?brand=' . urlencode($brand));
                    exit;
                }
            }

            $idx = isset($_POST['index']) ? (int)$_POST['index'] : -1;
            if ($idx >= 0 && isset($brands[$brand]['products'][$idx])) {
                array_splice($brands[$brand]['products'], $idx, 1);
                if (andison_save_single_brand($brand, $brands[$brand], [
                    'allowEmptyProducts' => true,
                    'allowProductCountDecrease' => true,
                ])) {
                    andison_set_flash('success', 'Product deleted.');
                } else {
                    andison_set_flash('error', 'Failed to delete product safely. Please refresh and try again.');
                }
            }
            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'bulk_delete_products') {
            if (!$allowProductDelete) {
                andison_set_flash('error', 'Product deletion is disabled.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            $selectedValues = isset($_POST['selected_products']) ? (array)$_POST['selected_products'] : [];

            $selectedIds = [];
            $selectedIndices = [];
            foreach ($selectedValues as $value) {
                $token = trim((string)$value);
                if (preg_match('/^id:(\d+)$/', $token, $m) === 1) {
                    $id = (int)$m[1];
                    if ($id > 0) {
                        $selectedIds[] = $id;
                    }
                    continue;
                }
                if (preg_match('/^idx:(\d+)$/', $token, $m) === 1) {
                    $selectedIndices[] = (int)$m[1];
                    continue;
                }
                if (is_numeric($token)) {
                    $selectedIndices[] = (int)$token;
                }
            }

            $selectedIds = array_values(array_unique($selectedIds));
            sort($selectedIds, SORT_NUMERIC);

            $selectedIndices = array_values(array_unique($selectedIndices));
            sort($selectedIndices, SORT_NUMERIC);

            $deletedCount = 0;

            if (!empty($selectedIds)) {
                foreach ($selectedIds as $id) {
                    if (andison_sb_delete('products', 'id=eq.' . $id)) {
                        $deletedCount++;
                    }
                }
                @unlink(__DIR__ . '/../data/_cache/brands_full.cache');
            }

            if (!empty($selectedIndices)) {
                foreach (array_reverse($selectedIndices) as $idx) {
                    if ($idx >= 0 && isset($brands[$brand]['products'][$idx])) {
                        array_splice($brands[$brand]['products'], $idx, 1);
                        $deletedCount++;
                    }
                }

                if (!andison_save_single_brand($brand, $brands[$brand], [
                    'allowEmptyProducts' => true,
                    'allowProductCountDecrease' => true,
                ])) {
                    andison_set_flash('error', 'Failed to save after bulk delete.');
                    header('Location: products.php?brand=' . urlencode($brand));
                    exit;
                }
            }

            if ($deletedCount > 0) {
                andison_set_flash('success', "Deleted {$deletedCount} product(s).");
            } else {
                andison_set_flash('warning', 'No products selected for deletion.');
            }
            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }
    }

    andison_set_flash('error', 'Invalid request.');
    header('Location: products.php');
    exit;
}

$editIndex = isset($_GET['edit']) ? (int)$_GET['edit'] : -1;
$brandInfo = $selectedBrandKey !== '' ? ($brands[$selectedBrandKey] ?? ($brands[$selectedBrand] ?? [])) : [];
$selectedBrandDescription = trim((string)($brandInfo['description'] ?? ''));
$selectedBrandLogo = trim((string)($brandInfo['logo'] ?? ''));
$selectedBrandShortLabel = trim((string)($brandInfo['short_label'] ?? ''));
$products = isset($brandInfo['products']) && is_array($brandInfo['products']) ? $brandInfo['products'] : [];

// Keep presets intentionally minimal: Optional only.

// Fallback logo map for admin preview (mirrors public site logic)
$adminLogoMap = [
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
    'HARDWORKER'             => 'HARDWORKER',
];
$pngBrands = ['ROBOT SYSTEMS', 'WELDCRAFT', 'REVOLT', 'TECHNOTEX'];

// If no database logo, show fallback from assets/brands/
if ($selectedBrandLogo === '' && $selectedBrandKey !== '') {
    $fallbackKey = $selectedBrandKey;
    if (isset($adminLogoMap[$selectedBrandKey])) {
        $fallbackKey = $adminLogoMap[$selectedBrandKey];
    } elseif (isset($adminLogoMap[$selectedBrand])) {
        $fallbackKey = $adminLogoMap[$selectedBrand];
    }
    $logoExt = in_array($fallbackKey, $pngBrands) ? 'png' : 'jpg';
    $fallbackLogo = './assets/brands/' . rawurlencode($fallbackKey) . '.' . $logoExt;
    if (file_exists(__DIR__ . '/../assets/brands/' . rawurlencode($fallbackKey) . '.' . $logoExt)) {
        $selectedBrandLogo = $fallbackLogo;
    }
}

$_typeOptions = ['optional' => 'Optional'];

// Build flat ID→name lookup for display in the product table
$_catNameMap = [];
$_subParentMap = [];
foreach ($allCategories as $_cat) {
    $_catNameMap[(string)$_cat['id']] = (string)$_cat['name'];
    foreach ($_cat['subcategories'] ?? [] as $_sub) {
        $_catNameMap[(string)$_sub['id']] = (string)$_sub['name'];
        foreach ($_sub['subcategories'] ?? [] as $_ss) {
            $_catNameMap[(string)$_ss['id']] = (string)$_ss['name'];
            $_subParentMap[(string)$_ss['id']] = (string)$_sub['id'];
        }
    }
}

andison_admin_header('Products', 'products');
?>

<style>
.prod-page-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);border-radius:14px;padding:20px 24px;color:white;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap; }
.prod-brand-select { background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:8px;padding:9px 36px 9px 14px;font-size:13px;font-weight:600;appearance:none;-webkit-appearance:none;cursor:pointer;min-width:260px;backdrop-filter:blur(4px);transition:border-color 0.2s; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='white' d='M6 9L1 4h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center; }
.prod-brand-select,
.prod-brand-select option { text-transform:uppercase; }
.prod-brand-select option { color:#111;background:white; }
.prod-brand-select:focus { outline:none;border-color:rgba(255,255,255,0.8); }
.prod-load-btn { background:rgba(255,255,255,0.2);border:1.5px solid rgba(255,255,255,0.4);color:white;border-radius:8px;padding:9px 18px;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;white-space:nowrap; }
.prod-load-btn:hover { background:rgba(255,255,255,0.35); }
.prod-stat-pill { display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);border-radius:999px;padding:6px 14px;font-size:12px;font-weight:600;white-space:nowrap; }
.prod-table thead th { padding:11px 14px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.6px;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.prod-table thead th:first-child { border-radius:10px 0 0 0; }
.prod-table thead th:last-child { border-radius:0 10px 0 0;text-align:center; }
.prod-table tbody tr { border-bottom:1px solid #f3f4f6;transition:background 0.15s; }
.prod-table tbody tr:last-child { border-bottom:none; }
.prod-table tbody tr:hover { background:#fafbff; }
.prod-table td { padding:12px 14px;vertical-align:middle; }
.prod-num { width:36px;height:36px;border-radius:8px;background:#f3f4f6;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#9ca3af; }
.prod-badge-chip { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;font-size:10px;font-weight:700;border-radius:999px;white-space:nowrap; }
.prod-badge-available { background:#dcfce7;color:#16a34a; }
.prod-badge-unavailable { background:#fee2e2;color:#dc2626; }
.prod-badge-featured { background:#fef9c3;color:#b45309; }
.prod-badge-new { background:#ede9fe;color:#7c3aed; }
.prod-badge-bestseller { background:#fce7f3;color:#be185d; }
.prod-badge-limited { background:#ffedd5;color:#c2410c; }
.prod-badge-default { background:#f3f4f6;color:#374151; }
.prod-search-wrap { position:relative; }
.prod-search-wrap .search-icon { position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px;pointer-events:none; }
.prod-search-wrap input { width:100%;padding:9px 14px 9px 36px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;transition:border-color 0.2s,box-shadow 0.2s; }
.prod-search-wrap input:focus { outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(43,17,219,0.08); }
.prod-desc-textarea { width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:inherit;resize:vertical;min-height:80px;transition:border-color 0.2s; }
.prod-desc-textarea:focus { outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(43,17,219,0.08); }
.brand-reorder-item.is-dragging { opacity: 0.45; }
.brand-reorder-item.drag-over { outline: 2px dashed rgba(43,17,219,0.35); outline-offset: -2px; }
.brand-reorder-item:hover .brand-reorder-handle { color:#2b11db; background:#eef2ff; }
.product-row.is-dragging { opacity: 0.45; }
.product-row.drag-over { outline: 2px dashed rgba(43,17,219,0.35); outline-offset: -2px; }
.product-drag-handle { cursor: grab; transition: color 0.15s, background 0.15s; }
.product-drag-handle:active { cursor: grabbing; }
.product-row:hover .product-drag-handle { color: #2b11db; background: #eef2ff; border-radius: 6px; }
</style>

<div class="grid">
    <!-- Brand Selector Section -->
    <section style="grid-column:span 12;" class="prod-page-header">
        <div style="display:flex;flex-direction:column;gap:6px;">
            <div style="font-size:11px;font-weight:600;opacity:0.7;letter-spacing:0.5px;text-transform:uppercase;">Product Management</div>
            <div style="font-size:20px;font-weight:800;letter-spacing:-0.2px;"><i class="bi bi-building"></i>
                <?php echo $selectedBrand !== '' ? htmlspecialchars(andison_brand_display_label($selectedBrand)) : 'Select a Brand'; ?>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <?php if ($selectedBrand !== ''): ?>
                <span class="prod-stat-pill"><i class="bi bi-box-seam"></i> <?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?></span>
                    <button class="btn btn-outline" type="button" onclick="toggleReorderBrandPanel()" style="font-size:12px;padding:8px 14px;"><i class="bi bi-grid-3x3-gap"></i> Reorder Brands</button>
            <?php endif; ?>
            <form method="get" action="products.php" style="display:flex;gap:8px;align-items:center;">
                <select name="brand" class="prod-brand-select" onchange="this.form.submit()">
                    <?php foreach ($brandNames as $bn): ?>
                        <option value="<?php echo htmlspecialchars($bn); ?>" <?php echo ($bn === $selectedBrand || andison_brand_display_label((string)$bn) === $selectedBrand) ? 'selected' : ''; ?>><?php echo htmlspecialchars(andison_brand_display_label((string)$bn)); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <button class="btn btn-outline" type="button" onclick="toggleAddBrandPanel()" style="font-size:12px;padding:8px 14px;"><i class="bi bi-plus-circle"></i> Add Brand</button>
            <?php if ($selectedBrand !== ''): ?>
                <button class="btn btn-outline" type="button" onclick="toggleEditBrandPanel()" style="font-size:12px;padding:8px 14px;"><i class="bi bi-pencil-square"></i> Edit Brand</button>
            <?php endif; ?>
            <?php if ($selectedBrand !== ''): ?>
                <form method="post" action="products.php" onsubmit="return confirm('Delete brand <?php echo htmlspecialchars(andison_brand_display_label((string)$selectedBrand), ENT_QUOTES); ?> and all its products? This cannot be undone.');" style="margin:0;">
                    <input type="hidden" name="action" value="delete_brand">
                    <input type="hidden" name="brand_to_delete" value="<?php echo htmlspecialchars($selectedBrandKey, ENT_QUOTES); ?>">
                    <button class="btn btn-danger" type="submit" style="font-size:12px;padding:8px 14px;"><i class="bi bi-trash"></i> Delete Brand</button>
                </form>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($selectedBrand !== ''): ?>
        <section class="card" id="editBrandPanel" style="grid-column:span 12;display:none;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
                <h2 style="margin:0;font-size:16px;"><i class="bi bi-pencil-square"></i> Edit Brand</h2>
                <button class="btn btn-outline" type="button" onclick="toggleEditBrandPanel()" style="font-size:12px;padding:6px 10px;"><i class="bi bi-x-lg"></i> Close</button>
            </div>
            <form method="post" action="products.php?brand=<?php echo urlencode($selectedBrandKey); ?>" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr;gap:10px;align-items:stretch;">
                <input type="hidden" name="action" value="edit_brand_logo">
                <input type="hidden" name="brand_original" value="<?php echo htmlspecialchars($selectedBrandKey, ENT_QUOTES); ?>">
                <input type="hidden" name="brand_to_edit" value="<?php echo htmlspecialchars($selectedBrandKey, ENT_QUOTES); ?>">
                <div class="field" style="margin:0;min-width:0;">
                    <label for="editBrandDescription">Brand Description (shown on public brand page)</label>
                    <textarea id="editBrandDescription" name="brand_description" rows="4" class="prod-desc-textarea" placeholder="Write brand description here..."><?php echo htmlspecialchars($selectedBrandDescription, ENT_QUOTES); ?></textarea>
                </div>
                <div class="field" style="margin:0;min-width:0;">
                    <label for="editBrandShortLabel">Brand Label (shown on brand grid cards - max 100 chars)</label>
                    <input id="editBrandShortLabel" name="brand_short_label" type="text" maxlength="100" placeholder="e.g., Welding Robot & Machine" value="<?php echo htmlspecialchars($selectedBrandShortLabel, ENT_QUOTES); ?>" style="padding:8px;border:1px solid #d1d5db;border-radius:8px;">
                </div>
                <div class="field" style="margin:0;min-width:0;">
                    <label for="editBrandLogo">New Brand Image (optional)</label>
                    <input id="editBrandLogo" name="edit_brand_logo" type="file" accept="image/jpeg,image/pjpeg,image/png,image/webp,image/gif,image/avif,image/jfif,.jpg,.jpeg,.jfif,.png,.webp,.gif,.avif">
                </div>
                <div class="field" style="margin:0;min-width:0;">
                    <label>Brand Image Preview</label>
                    <div id="editBrandLogoPreviewWrap" style="display:flex;align-items:center;gap:12px;padding:10px 12px;border:1.5px dashed #d1d5db;border-radius:10px;background:#f9fafb;">
                        <div style="width:180px;height:72px;border-radius:10px;background:#ffffff;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                            <img id="editBrandLogoPreview" src="<?php echo htmlspecialchars($selectedBrandLogo, ENT_QUOTES); ?>" alt="Brand logo preview" style="max-width:100%;max-height:100%;object-fit:contain;<?php echo $selectedBrandLogo === '' ? 'display:none;' : ''; ?>">
                            <span id="editBrandLogoPreviewEmpty" style="font-size:11px;color:#9ca3af;<?php echo $selectedBrandLogo !== '' ? 'display:none;' : ''; ?>">No logo yet</span>
                        </div>
                        <div style="font-size:11px;color:#6b7280;line-height:1.5;">
                            <?php 
                            if ($selectedBrandLogo !== '' && strpos($selectedBrandLogo, './assets/brands/') === 0) {
                                echo '<span style="color:#ea8634;"><strong>⚠ Fallback logo</strong></span> - Using default image.<br>';
                                echo 'To customize, upload a new image below.<br>';
                            } elseif ($selectedBrandLogo !== '') {
                                echo '<strong>✓ Custom logo</strong> from admin data.<br>';
                            }
                            ?>
                            Selecting a new file will preview it before save.
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit" style="height:44px;padding:10px 16px;"><i class="bi bi-check2-circle"></i> Save Brand</button>
            </form>
        </section>
    <?php endif; ?>

    <section class="card" id="addBrandPanel" style="grid-column:span 12;display:none;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <h2 style="margin:0;font-size:16px;"><i class="bi bi-building-add"></i> Add New Brand</h2>
            <button class="btn btn-outline" type="button" onclick="toggleAddBrandPanel()" style="font-size:12px;padding:6px 10px;"><i class="bi bi-x-lg"></i> Close</button>
        </div>
        <form method="post" action="products.php" enctype="multipart/form-data" style="display:grid;grid-template-columns:2fr 3fr 2.5fr auto;gap:10px;align-items:end;">
            <input type="hidden" name="action" value="add_brand">
            <div class="field" style="margin:0;min-width:0;">
                <label for="newBrandName">Brand Name *</label>
                <input id="newBrandName" name="new_brand_name" type="text" required placeholder="e.g., New Industrial Brand">
            </div>
            <div class="field" style="margin:0;min-width:0;">
                <label for="newBrandDescription">Description (optional)</label>
                <input id="newBrandDescription" name="new_brand_description" type="text" placeholder="Short brand description">
            </div>
            <div class="field" style="margin:0;min-width:0;">
                <label for="newBrandLogo">Brand Image *</label>
                <input id="newBrandLogo" name="new_brand_logo" type="file" required accept="image/jpeg,image/pjpeg,image/png,image/webp,image/gif,image/avif,image/jfif,.jpg,.jpeg,.jfif,.png,.webp,.gif,.avif">
            </div>
            <button class="btn btn-primary" type="submit" style="height:44px;padding:10px 16px;"><i class="bi bi-check2-circle"></i> Save Brand</button>
            <div class="field" style="margin:0;min-width:0;grid-column:1 / 5;">
                <label>New Brand Image Preview</label>
                <div style="display:flex;align-items:center;gap:12px;padding:10px 12px;border:1.5px dashed #d1d5db;border-radius:10px;background:#f9fafb;">
                    <div style="width:180px;height:72px;border-radius:10px;background:#ffffff;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <img id="newBrandLogoPreview" src="" alt="New brand logo preview" style="max-width:100%;max-height:100%;object-fit:contain;display:none;">
                        <span id="newBrandLogoPreviewEmpty" style="font-size:11px;color:#9ca3af;">No file selected</span>
                    </div>
                    <div style="font-size:11px;color:#6b7280;line-height:1.5;">
                        Choose an image file to preview how the logo will look.<br>
                        Supports JPG, JFIF, PNG, WEBP, GIF, and AVIF.
                    </div>
                </div>
            </div>
        </form>
    </section>

    <section class="card" id="reorderBrandPanel" style="grid-column:span 12;display:none;">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:10px;">
            <h2 style="margin:0;font-size:16px;"><i class="bi bi-grid-3x3-gap"></i> Reorder Brands</h2>
            <button class="btn btn-outline" type="button" onclick="toggleReorderBrandPanel()" style="font-size:12px;padding:6px 10px;"><i class="bi bi-x-lg"></i> Close</button>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:12px;">
            <span style="font-size:12px;color:#6b7280;font-weight:500;"><i class="bi bi-arrows-move"></i> Drag brands to change the public order</span>
            <form id="reorderBrandsForm" method="post" action="products.php" style="margin:0;display:inline-flex;align-items:center;gap:8px;">
                <input type="hidden" name="action" value="reorder_brands">
                <input type="hidden" name="brand_order" id="brandOrderInput" value="[]">
                <button class="btn btn-primary" type="submit" style="padding:8px 14px;font-size:12px;display:flex;align-items:center;gap:6px;"><i class="bi bi-save"></i> Save Brand Order</button>
            </form>
        </div>
        <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;background:#fff;">
            <div id="brandReorderList" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;padding:12px;max-height:420px;overflow:auto;">
                <?php foreach ($brandOrderLabels as $brandLabel): ?>
                    <div class="brand-reorder-item" draggable="true" data-brand-label="<?php echo htmlspecialchars($brandLabel, ENT_QUOTES); ?>" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;background:linear-gradient(180deg,#fff 0%,#f8fafc 100%);cursor:grab;user-select:none;">
                        <span class="brand-reorder-handle" style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:6px;color:#94a3b8;"><i class="bi bi-grip-vertical"></i></span>
                        <span style="font-size:13px;font-weight:700;color:#111827;letter-spacing:0.01em;"><?php echo htmlspecialchars($brandLabel, ENT_QUOTES); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <?php if ($selectedBrand !== ''): ?>
        <section class="card" style="grid-column:span 12;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:12px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:9px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-box-seam" style="color:var(--mint);font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:15px;font-weight:700;color:#111827;">Product List</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:1px;"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?> &middot; <?php echo htmlspecialchars(andison_brand_display_label($selectedBrand)); ?></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <button class="btn btn-primary" type="button" onclick="openAddProductModal();" style="font-size:12px;padding:8px 16px;"><i class="bi bi-plus-lg"></i> Add Product</button>
                    <button class="btn btn-secondary" type="button" onclick="openImportCsvModal();" style="font-size:12px;padding:8px 16px;background:#6b7280;border-color:#6b7280;color:white;border-radius:8px;"><i class="bi bi-upload"></i> Import CSV</button>
                    <button class="btn btn-outline" type="button" onclick="toggleReorderBrandPanel()" style="font-size:12px;padding:8px 14px;"><i class="bi bi-grid-3x3-gap"></i> Reorder Brands</button>
                    <?php if (!$allowProductDelete): ?>
                        <span style="font-size:10px;font-weight:700;background:#fef3c7;color:#92400e;border:1px solid #fcd34d;border-radius:999px;padding:4px 10px;"><i class="bi bi-shield-lock"></i> Delete Disabled</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Search Bar -->
            <div style="margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                <div class="prod-search-wrap" style="flex:1;min-width:250px;">
                    <i class="bi bi-search search-icon"></i>
                    <input id="productSearch" type="text" placeholder="Search by model, name, type, or badge...">
                </div>
                <div id="bulkActionsBar" style="display:none;gap:8px;align-items:center;">
                    <span id="selectedCountText" style="font-size:12px;color:#6b7280;font-weight:500;"></span>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete selected products? This cannot be undone.');">
                        <input type="hidden" name="action" value="bulk_delete_products">
                        <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrandKey); ?>">
                        <div id="selectedProductsContainer"></div>
                        <button type="submit" class="btn" style="background:#dc2626;border-color:#dc2626;color:white;border-radius:6px;padding:6px 14px;font-size:12px;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;"><i class="bi bi-trash"></i> Delete Selected</button>
                    </form>
                </div>
                <div id="reorderActionsBar" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;justify-content:flex-end;">
                    <span style="font-size:12px;color:#6b7280;font-weight:500;"><i class="bi bi-arrows-move"></i> Drag rows by the handle to change public order</span>
                    <form id="reorderProductsForm" method="POST" action="products.php?brand=<?php echo urlencode($selectedBrandKey); ?>" style="margin:0;display:inline-flex;align-items:center;gap:8px;">
                        <input type="hidden" name="action" value="reorder_products">
                        <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrandKey, ENT_QUOTES); ?>">
                        <input type="hidden" name="product_order" id="productOrderInput" value="[]">
                        <button type="submit" class="btn btn-primary" style="padding:6px 14px;font-size:12px;border-radius:6px;font-weight:600;display:flex;align-items:center;gap:6px;"><i class="bi bi-save"></i> Save Order</button>
                    </form>
                </div>
            </div>

            <!-- Products Table -->
            <div style="overflow-x:auto;border-radius:10px;border:1px solid #e5e7eb;background:white;">
                <table class="prod-table" id="productsTable" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="width:40px;text-align:center;"><input type="checkbox" id="selectAllCheckbox" style="cursor:pointer;" title="Select all products"></th>
                            <th style="width:50px;">#</th>
                            <th>Product</th>
                            <th style="width:130px;">Type</th>
                            <th style="width:120px;">Status</th>
                            <th style="width:90px;text-align:center;">Image</th>
                            <th style="width:160px;text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7" style="padding:48px;text-align:center;">
                                    <div style="width:56px;height:56px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;"><i class="bi bi-inbox" style="font-size:24px;color:#d1d5db;"></i></div>
                                    <div style="font-weight:600;font-size:14px;color:#374151;margin-bottom:4px;">No products yet</div>
                                    <div style="font-size:12px;color:#9ca3af;margin-bottom:14px;">Add the first product for <?php echo htmlspecialchars(andison_brand_display_label($selectedBrand)); ?></div>
                                    <button class="btn btn-primary" type="button" onclick="openAddProductModal();" style="font-size:12px;padding:7px 16px;"><i class="bi bi-plus-lg"></i> Add Product</button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $i => $prod): ?>
                                <?php if (!is_array($prod)) { continue; } ?>
                                <?php
                                    $productToken = isset($prod['id']) ? ('id:' . (int)$prod['id']) : ('idx:' . (int)$i);
                                    $badge = (string)($prod['badge'] ?? '');
                                    $badgeClass = 'prod-badge-default';
                                    if ($badge === 'Available') $badgeClass = 'prod-badge-available';
                                    elseif ($badge === 'Not Available') $badgeClass = 'prod-badge-unavailable';
                                    elseif ($badge === 'Featured') $badgeClass = 'prod-badge-featured';
                                    elseif ($badge === 'New Arrival' || $badge === 'New') $badgeClass = 'prod-badge-new';
                                    elseif ($badge === 'Best Seller') $badgeClass = 'prod-badge-bestseller';
                                    elseif ($badge === 'Limited Stock') $badgeClass = 'prod-badge-limited';
                                ?>
                                <tr class="product-row" 
                                    data-order-token="<?php echo htmlspecialchars($productToken, ENT_QUOTES); ?>"
                                    data-model="<?php echo htmlspecialchars(strtolower((string)($prod['model'] ?? '')), ENT_QUOTES); ?>" 
                                    data-type="<?php echo htmlspecialchars(strtolower((string)($prod['type'] ?? '')), ENT_QUOTES); ?>" 
                                    data-badge="<?php echo htmlspecialchars(strtolower((string)($prod['badge'] ?? '')), ENT_QUOTES); ?>">
                                    <td style="text-align:center;">
                                        <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                            <span class="product-drag-handle" title="Drag to reorder" aria-label="Drag to reorder" draggable="true" style="display:inline-flex;align-items:center;justify-content:center;width:22px;height:22px;border-radius:6px;color:#94a3b8;cursor:grab;flex-shrink:0;"><i class="bi bi-grip-vertical"></i></span>
                                            <input type="checkbox" class="product-checkbox" value="<?php echo htmlspecialchars($productToken, ENT_QUOTES); ?>" style="cursor:pointer;">
                                        </div>
                                    </td>
                                    <td><span class="prod-num"><?php echo (int)$i + 1; ?></span></td>
                                    <td>
                                        <div style="font-weight:600;font-size:13px;color:#111827;"><?php echo htmlspecialchars((string)($prod['model'] ?? '')); ?></div>
                                        <?php if (!empty($prod['product_name'])): ?>
                                            <div style="font-size:11px;color:#9ca3af;margin-top:1px;"><?php echo htmlspecialchars((string)$prod['product_name']); ?></div>
                                        <?php endif; ?>
                                        <?php if (empty($prod['category_id'])): ?>
                                            <div style="margin-top:3px;"><span style="font-size:10px;font-weight:700;background:#fef3c7;color:#92400e;border:1px solid #fcd34d;border-radius:4px;padding:1px 6px;"><i class="bi bi-exclamation-triangle-fill"></i> No Category — won't show on browse pages</span></div>
                                        <?php else: ?>
                                            <?php
                                                $storedCatId = trim((string)($prod['category_id'] ?? ''));
                                                $storedSubId = trim((string)($prod['subcategory_id'] ?? ''));
                                                $storedSubSubId = trim((string)($prod['sub_subcategory_id'] ?? ''));

                                                // Backward compatibility: legacy rows may have saved sub-subcategory into subcategory_id.
                                                if ($storedSubSubId === '' && $storedSubId !== '' && isset($_subParentMap[$storedSubId])) {
                                                    $storedSubSubId = $storedSubId;
                                                    $storedSubId = $_subParentMap[$storedSubId];
                                                }

                                                $dispCat = $_catNameMap[$storedCatId] ?? $storedCatId;
                                                $dispSub = $storedSubId !== '' ? ($_catNameMap[$storedSubId] ?? $storedSubId) : '';
                                                $dispSubSub = $storedSubSubId !== '' ? ($_catNameMap[$storedSubSubId] ?? $storedSubSubId) : '';
                                                
                                                $categoryPath = $dispCat;
                                                if ($dispSub !== '') $categoryPath .= ' › ' . $dispSub;
                                                if ($dispSubSub !== '') $categoryPath .= ' › ' . $dispSubSub;
                                            ?>
                                            <div style="margin-top:3px;"><span style="font-size:11px;color:#4b5563;font-weight:500;display:flex;align-items:center;gap:6px;"><i class="bi bi-diagram-3" style="color:#2B11DB;"></i> <?php echo htmlspecialchars($categoryPath); ?></span></div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="color:#6b7280;font-size:12px;"><?php echo htmlspecialchars((string)($prod['type'] ?? '')); ?></td>
                                    <td>
                                        <?php if ($badge !== ''): ?>
                                            <span class="prod-badge-chip <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($badge); ?></span>
                                        <?php else: ?>
                                            <span style="color:#d1d5db;font-size:12px;">&mdash;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <?php 
                                            $imgPath = (string)($prod['image'] ?? '');
                                            if ($imgPath !== ''):
                                                $displayPath = $imgPath;
                                                if (strpos($imgPath, 'andison/') === 0) {
                                                    $displayPath = '../' . substr($imgPath, 8);
                                                } elseif (strpos($imgPath, 'assets/') === 0) {
                                                    $displayPath = '../' . $imgPath;
                                                } elseif (!preg_match('~^(https?://|\.\./)~i', $imgPath)) {
                                                    $displayPath = '../' . $imgPath;
                                                }
                                        ?>
                                            <img src="<?php echo htmlspecialchars($displayPath); ?>" 
                                                 alt="<?php echo htmlspecialchars((string)($prod['model'] ?? '')); ?>" 
                                                 style="width:48px;height:48px;object-fit:contain;border-radius:8px;cursor:pointer;border:1px solid #e5e7eb;background:#f9fafb;transition:all 0.2s;" 
                                                 onclick="openImagePreview('<?php echo htmlspecialchars($displayPath, ENT_QUOTES); ?>')"
                                                 onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';"
                                                 onmouseover="this.style.transform='scale(1.12)';this.style.borderColor='var(--accent)';this.style.boxShadow='0 4px 10px rgba(43,17,219,0.15)';"
                                                 onmouseout="this.style.transform='scale(1)';this.style.borderColor='#e5e7eb';this.style.boxShadow='none';"
                                                 title="Click to preview">
                                            <span style="display:none;width:48px;height:48px;background:#f3f4f6;border-radius:8px;align-items:center;justify-content:center;color:#d1d5db;font-size:18px;"><i class="bi bi-image"></i></span>
                                        <?php else: ?>
                                            <span style="width:48px;height:48px;background:#f3f4f6;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;color:#d1d5db;font-size:18px;"><i class="bi bi-image"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align:center;">
                                        <div style="display:flex;gap:5px;justify-content:center;">
                                            <button class="btn btn-outline edit-product-btn" type="button" 
                                                    data-index="<?php echo (int)$i; ?>" 
                                                    data-name="<?php echo htmlspecialchars((string)($prod['product_name'] ?? ''), ENT_QUOTES); ?>"
                                                    data-model="<?php echo htmlspecialchars((string)($prod['model'] ?? ''), ENT_QUOTES); ?>" 
                                                    data-type="<?php echo htmlspecialchars((string)($prod['type'] ?? ''), ENT_QUOTES); ?>"
                                                    data-price="<?php echo htmlspecialchars((string)($prod['price'] ?? ''), ENT_QUOTES); ?>"
                                                    data-badge="<?php echo htmlspecialchars((string)($prod['badge'] ?? ''), ENT_QUOTES); ?>" 
                                                    data-description="<?php echo htmlspecialchars((string)($prod['description'] ?? ''), ENT_QUOTES); ?>"
                                                    data-specifications="<?php echo htmlspecialchars((string)($prod['specifications'] ?? ''), ENT_QUOTES); ?>"
                                                    data-spec-image="<?php echo htmlspecialchars((string)($prod['spec_image'] ?? ''), ENT_QUOTES); ?>"
                                                    data-image="<?php echo htmlspecialchars((string)($prod['image'] ?? ''), ENT_QUOTES); ?>"
                                                    data-images="<?php
                                                        $prodImgs = isset($prod['images']) && is_array($prod['images']) ? $prod['images'] : [];
                                                        if (empty($prodImgs) && !empty($prod['image'])) $prodImgs = [$prod['image']];
                                                        echo htmlspecialchars(json_encode($prodImgs), ENT_QUOTES);
                                                    ?>"
                                                    data-category="<?php echo htmlspecialchars((string)($prod['category_id'] ?? ''), ENT_QUOTES); ?>"
                                                    data-subcategory="<?php echo htmlspecialchars((string)($prod['subcategory_id'] ?? ''), ENT_QUOTES); ?>"
                                                    data-subsubcategory="<?php echo htmlspecialchars((string)($prod['sub_subcategory_id'] ?? ''), ENT_QUOTES); ?>"
                                                    data-datasheet="<?php echo htmlspecialchars((string)($prod['datasheet'] ?? ''), ENT_QUOTES); ?>"
                                                    style="padding:5px 10px;font-size:11px;">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <?php if ($allowProductDelete): ?>
                                                <form method="post" action="products.php?brand=<?php echo urlencode($selectedBrandKey); ?>" class="delete-form" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrandKey); ?>">
                                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($productToken, ENT_QUOTES); ?>">
                                                    <input type="hidden" name="index" value="<?php echo (int)$i; ?>">
                                                    <button class="btn btn-danger" type="submit" style="padding:5px 10px;font-size:11px;"><i class="bi bi-trash"></i></button>
                                                </form>
                                            <?php else: ?>
                                                <button class="btn btn-danger" type="button" disabled title="Deletion disabled in safe mode" style="padding:5px 10px;font-size:11px;opacity:0.55;cursor:not-allowed;"><i class="bi bi-trash"></i></button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            

        </section>
    <?php endif; ?>

<!-- Edit Product Modal -->
<div id="editProductModal" class="edit-modal" style="display:none;">
    <div class="edit-modal-overlay" onclick="closeEditModal()"></div>
    <div class="edit-modal-content">
        <div class="edit-modal-header">
            <h2><i class="bi bi-pencil"></i> Edit Product</h2>
            <button class="edit-modal-close" onclick="closeEditModal()" type="button">✕</button>
        </div>
        <form method="post" enctype="multipart/form-data" action="products.php?brand=<?php echo urlencode($selectedBrandKey); ?>" class="edit-product-form">
            <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrandKey); ?>">
            <input type="hidden" name="action" value="update_product">
            <input type="hidden" name="index" id="editIndex">
            <input type="hidden" name="product_description_mode" id="editDescriptionMode" value="plain">
            
            <div class="edit-modal-body">
                <!-- Category Assignment Section -->
                <div style="margin-bottom:24px;background:linear-gradient(135deg,#eef0ff,#f5f3ff);border:1.5px solid rgba(43,17,219,0.18);border-radius:10px;padding:16px;">                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;border-bottom:2px solid rgba(43,17,219,0.12);padding-bottom:10px;"><i class="bi bi-diagram-3"></i> Category Assignment <span style="font-size:10px;font-weight:600;background:#2b11db;color:#fff;border-radius:999px;padding:2px 8px;margin-left:6px;">REQUIRED to show on site</span></h3>
                    <input type="hidden" id="finalCategoryId" name="category_id">
                    <input type="hidden" id="finalSubcategoryId" name="subcategory_id">
                        <input type="hidden" id="finalSubSubcategoryId" name="sub_subcategory_id">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px;">
                        <div class="field" style="margin:0;">
                            <label for="editCategory"><i class="bi bi-folder"></i> Category</label>
                            <select id="editCategory" onchange="populateCategorySubcategories('', '')" title="Select the product category">
                                <option value="">-- None / Brand Only --</option>
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field" style="margin:0;">
                            <label for="editSubcategory"><i class="bi bi-folder2-open"></i> Subcategory</label>
                            <select id="editSubcategory" onchange="populateSubSubcategories(this.value, '');" title="Select the product subcategory">
                                <option value="">-- Select Category First --</option>
                            </select>
                        </div>
                        <div class="field" style="margin:0;grid-column:2;">
                            <label for="editSubSubcategory"><i class="bi bi-diagram-2"></i> Sub-subcategory</label>
                            <select id="editSubSubcategory" onchange="updateFinalSubcategory();" title="Select the product sub-subcategory (optional)">
                                <option value="">-- Optional --</option>
                            </select>
                        </div>
                    </div>
                    <p style="font-size:11px;color:#9ca3af;margin:0;"><i class="bi bi-info-circle"></i> Assign a category so this product appears on the public product pages.</p>
                    <div id="categoryLivePreview" style="margin-top:7px;font-size:11px;min-height:18px;"></div>
                </div>

                <!-- Basic Info Section -->
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;"><i class="bi bi-info-circle"></i> Product Information</h3>
                    
                    <div class="field edit-description-field" style="margin:0;margin-bottom:12px;">
                        <label for="editProductName"><i class="bi bi-tag"></i> Product Description</label>
                        <input id="editProductName" name="product_name" type="text" placeholder="e.g., Panasonic TIG Welding Machine" title="Enter product name">
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div class="field" style="margin:0;">
                            <label for="editModel"><i class="bi bi-tag"></i> Product Name *</label>
                            <input id="editModel" name="model" type="text" required placeholder="e.g., YD-350KR2" title="Enter product model number or code">
                        </div>
                        
                        <div class="field" style="margin:0;">
                            <label for="editType"><i class="bi bi-folder"></i> Type *</label>
                            <select id="editTypePreset" onchange="onTypePresetChange()" title="Select a product type or choose custom" style="margin-bottom:8px;">
                                <option value="">-- Select Type --</option>
                                <?php foreach ($_typeOptions as $_typeOpt): ?>
                                    <option value="<?php echo htmlspecialchars((string)$_typeOpt, ENT_QUOTES); ?>"><?php echo htmlspecialchars((string)$_typeOpt); ?></option>
                                <?php endforeach; ?>
                                <option value="__custom__">Custom (Type Manually)</option>
                            </select>
                            <input id="editType" name="type" type="text" required placeholder="Choose from dropdown or type custom type" title="Choose from dropdown or type custom type">
                            <div style="font-size:10px;color:#9ca3af;margin-top:4px;">Select from dropdown, or choose Custom and type your own.</div>
                        </div>
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div class="field" style="margin:0;">
                            <label for="editPrice"><i class="bi bi-currency-dollar"></i> Price</label>
                            <input id="editPrice" name="price" type="text" placeholder="e.g., $999.99 or Upon Request" title="Enter product price">
                        </div>
                        
                        <div class="field" style="margin:0;">
                            <label for="editBadge"><i class="bi bi-award"></i> Badge (optional)</label>
                            <select id="editBadge" name="badge" title="Select product availability status" class="badge-select">
                                <option value="" style="color:#6b7280;">-- None --</option>
                                <option value="Available" style="color:#10b981;">✓ Available</option>
                                <option value="Not Available" style="color:#ef4444;">✗ Not Available</option>
                                <option value="Featured" style="color:#f59e0b;">★ Featured</option>
                                <option value="New" style="color:#8b5cf6;">🆕 New</option>
                                <option value="Best Seller" style="color:#ec4899;">🏆 Best Seller</option>
                                <option value="Limited Stock" style="color:#f97316;">⚠️ Limited Stock</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;"><i class="bi bi-file-text"></i> Details</h3>
                    <div class="field" style="margin:0;margin-bottom:12px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:8px;">
                            <label for="editDescription" style="margin:0;">Description</label>
                            <div class="desc-mode-toggle" role="group" aria-label="Description input mode">
                                <button type="button" id="descModePlainBtn" class="desc-mode-btn is-active" onclick="setEditDescriptionMode('plain', true)">Plain Text</button>
                                <button type="button" id="descModeTableBtn" class="desc-mode-btn" onclick="setEditDescriptionMode('table', true)">Table Type</button>
                            </div>
                        </div>
                        <textarea id="editDescription" name="product_description" rows="3" placeholder="Add product benefits and key features..." style="resize:vertical;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;"></textarea>
                        <div id="editDescriptionTablePanel" style="display:none;margin-top:10px;">
                            <div class="desc-table-toolbar">
                                <button type="button" class="desc-action-btn" onclick="descriptionTableInsertStarterTable()"><i class="bi bi-table"></i> Starter Table</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableAddRow()"><i class="bi bi-plus-square"></i> Add Row</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableAddColumn()"><i class="bi bi-plus-square-dotted"></i> Add Column</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableDeleteRow()"><i class="bi bi-dash-square"></i> Delete Row</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableDeleteColumn()"><i class="bi bi-dash-square-dotted"></i> Delete Column</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableDeleteSelectedCells()"><i class="bi bi-x-square"></i> Delete Selected Cells</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableMergeCells()"><i class="bi bi-diagram-3"></i> Merge Selected Cells</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableSplitCell()"><i class="bi bi-distribute-vertical"></i> Split Selected Cell</button>
                                <button type="button" class="desc-action-btn" onclick="descriptionTableInsertImagePrompt()"><i class="bi bi-image"></i> Insert Image</button>
                                <input type="file" id="descriptionTableImageInput" accept="image/*" style="display:none;" onchange="descriptionTableHandleImageInput(this)">
                            </div>
                            <div id="editDescriptionTableHint" style="font-size:11px;color:#6b7280;margin:8px 0 4px 0;"><i class="bi bi-info-circle"></i> Click, drag, or use the small selector in each cell to pick multiple cells. Then merge, split, or delete the selected cells.</div>
                            <div id="editDescriptionTableSelectionCount" style="font-size:11px;font-weight:700;color:#334155;margin:0 0 10px 0;">Selected cells: 0</div>
                            <div class="desc-table-shell">
                                <table id="editDescriptionTableEditor" class="desc-custom-table"><tbody></tbody></table>
                            </div>
                        </div>
                        <div id="editDescriptionModeHint" style="font-size:11px;color:#6b7280;margin-top:8px;"><i class="bi bi-info-circle"></i> Plain Text mode for normal descriptions. Switch to Table Type for merged cells and images.</div>
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;">
                            <button type="button" id="convertTextToTableBtn" class="desc-action-btn" onclick="convertEditDescriptionTextToTableRows()"><i class="bi bi-table"></i> Convert current text to table rows</button>
                            <span style="font-size:11px;color:#9ca3af;align-self:center;">Best for old descriptions with line breaks or label/value pairs.</span>
                        </div>
                    </div>

                    <div class="field" style="margin:0;margin-bottom:12px;">
                        <label for="specImageFile"><i class="bi bi-image" style="color:#2b11db;"></i> Specifications Side Image (optional)</label>
                        <input type="hidden" name="existing_spec_image" id="existingSpecImageInput" value="">
                        <div id="specImagePreview" style="display:none;margin-bottom:8px;padding:10px 12px;background:#eef2ff;border:1.5px solid #c7d2fe;border-radius:8px;align-items:center;gap:10px;">
                            <img id="specImageThumb" src="" alt="Specs side image" style="width:52px;height:52px;object-fit:cover;border-radius:8px;border:1px solid #c7d2fe;flex-shrink:0;">
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" id="specImageFileName">spec-image.jpg</div>
                                <a id="specImageLink" href="#" target="_blank" style="font-size:10px;color:#2b11db;">View Image</a>
                            </div>
                            <button type="button" onclick="removeSpecImage()" style="background:rgba(79,70,229,0.1);border:1px solid #c7d2fe;color:#3730a3;border-radius:6px;padding:4px 8px;font-size:11px;cursor:pointer;"><i class="bi bi-x-lg"></i> Remove</button>
                        </div>
                        <label for="specImageFile" style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px dashed #c7d2fe;border-radius:8px;cursor:pointer;font-size:13px;color:#4f46e5;font-weight:600;transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='#c7d2fe'">
                            <i class="bi bi-upload" style="font-size:16px;"></i> Click to upload specs-side image
                        </label>
                        <input type="file" id="specImageFile" name="spec_image_file" accept="image/*" style="display:none;" onchange="handleSpecImageSelect(this)">
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;"><i class="bi bi-info-circle"></i> Displayed at the left side of Specifications on client view.</div>
                    </div>
                    
                    <div class="field" style="margin:0;margin-bottom:12px;">
                        <label for="editSpecificationsText">Specifications (Text)</label>
                        <textarea id="editSpecificationsText" rows="3" placeholder="Technical specs, dimensions, power requirements, etc..." style="resize:vertical;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;"></textarea>
                        <input type="hidden" id="editSpecifications" name="specifications" value="">
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;"><i class="bi bi-info-circle"></i> This displays as plain text on the client side.</div>
                    </div>

                    <div class="field spec-editor-field" style="margin:0;margin-bottom:12px;">
                        <label><i class="bi bi-table"></i> Specifications Table (Optional)</label>
                        <div class="spec-toolbar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
                            <label for="specTableMode" style="margin:0;font-size:11px;font-weight:700;color:#374151;">Table Style</label>
                            <select id="specTableMode" onchange="setSpecTableMode(this.value, true)" style="min-width:220px;padding:6px 9px;border:1.5px solid #dbe1ea;border-radius:8px;font-size:11px;font-weight:600;background:#fff;">
                                <option value="standard">Spreadsheet Dark Grid (Excel-like)</option>
                                <option value="grouped-pairs">Grouped Header (like image)</option>
                            </select>
                            <button type="button" id="specToggleGroupHeaderBtn" onclick="toggleSpecGroupHeaderControls()" style="display:none;align-items:center;gap:6px;background:#f8fafc;border:1px solid #cbd5e1;color:#334155;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-sliders"></i> Show Group Settings
                            </button>
                            <button type="button" onclick="convertSpecificationsTextToTable()" style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border:1px solid #93c5fd;color:#1d4ed8;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-arrow-repeat"></i> Convert Text to Table
                            </button>
                        </div>
                        <div id="specGroupHeaderWrap" style="display:none;margin-bottom:8px;padding:8px;border:1px solid #dbeafe;border-radius:8px;background:#eff6ff;"></div>
                        <div id="specTableBuilderWrap" style="overflow:auto;border:1px solid #e5e7eb;border-radius:10px;background:#fff;">
                            <table id="specTableBuilder" style="width:max-content;border-collapse:separate;border-spacing:0;min-width:100%;table-layout:fixed;">
                                <thead id="specTableHead"></thead>
                                <tbody id="specTableBody"></tbody>
                            </table>
                        </div>
                        <div class="spec-actions" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                            <button type="button" onclick="addSpecTableDataRow()" style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;border:1px solid #c7d2fe;color:#2b11db;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-plus-lg"></i> Add Row
                            </button>
                            <button type="button" id="specAddColumnBtn" onclick="addSpecTableColumn()" style="display:inline-flex;align-items:center;gap:6px;background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-layout-three-columns"></i> Add Column
                            </button>
                            <button type="button" id="specAddSingleColumnBtn" onclick="addSpecTableSingleColumn()" style="display:inline-flex;align-items:center;gap:6px;background:#ecfeff;border:1px solid #a5f3fc;color:#155e75;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-plus-square"></i> Add 1 Column
                            </button>
                            <button type="button" onclick="pasteExcelIntoSpecTable(true)" style="display:inline-flex;align-items:center;gap:6px;background:#fff7ed;border:1px solid #fdba74;color:#9a3412;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-clipboard"></i> PASTE TABLE
                            </button>
                            <button type="button" onclick="clearSpecTableValues()" style="display:inline-flex;align-items:center;gap:6px;background:#f8fafc;border:1px solid #cbd5e1;color:#334155;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-eraser"></i> Clear Data
                            </button>
                            <button type="button" onclick="resetSpecTableFresh()" style="display:inline-flex;align-items:center;gap:6px;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-arrow-counterclockwise"></i> NEW TABLE
                            </button>
                        </div>
                        <div id="specTableHelpText" style="font-size:11px;color:#9ca3af;margin-top:6px;"><i class="bi bi-info-circle"></i> Easiest way: copy from Excel, then click "PASTE TABLE".</div>
                        <button type="button" id="specAddSecondTableBtn" onclick="showSecondTableEditor()" style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;background:#eef2ff;border:1px solid #c7d2fe;color:#2b11db;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                            <i class="bi bi-plus-square"></i> Add 2nd Table
                        </button>
                        <div class="spec-second-table-card" id="specSecondTableContainer" style="display:none;margin-top:10px;">
                            <div class="spec-toolbar" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
                                <label for="specSecondTableMode" style="margin:0;font-size:11px;font-weight:700;color:#374151;">Table Style</label>
                                <select id="specSecondTableMode" onchange="setSecondSpecTableMode(this.value, true)" style="min-width:220px;padding:6px 9px;border:1.5px solid #dbe1ea;border-radius:8px;font-size:11px;font-weight:600;background:#fff;">
                                    <option value="standard">Spreadsheet Dark Grid (Excel-like)</option>
                                </select>
                                <button type="button" onclick="convertSecondSpecificationsTextToTable()" style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border:1px solid #93c5fd;color:#1d4ed8;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                    <i class="bi bi-arrow-repeat"></i> Convert Text to Table
                                </button>
                            </div>
                            <div id="specSecondTableBuilderWrap" style="overflow:auto;border:1px solid #e5e7eb;border-radius:10px;background:#fff;">
                                <table id="specSecondTableBuilder" style="width:max-content;border-collapse:separate;border-spacing:0;min-width:100%;table-layout:fixed;">
                                    <thead id="specSecondTableHead"></thead>
                                    <tbody id="specSecondTableBody"></tbody>
                                </table>
                            </div>
                            <div class="spec-actions" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                                <button type="button" onclick="addSecondSpecTableDataRow()" style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;border:1px solid #c7d2fe;color:#2b11db;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                    <i class="bi bi-plus-lg"></i> Add Row
                                </button>
                                <button type="button" onclick="addSecondSpecTableColumn()" style="display:inline-flex;align-items:center;gap:6px;background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                    <i class="bi bi-layout-three-columns"></i> Add Column
                                </button>
                                <button type="button" onclick="pasteExcelIntoSecondSpecTable()" style="display:inline-flex;align-items:center;gap:6px;background:#fff7ed;border:1px solid #fdba74;color:#9a3412;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                    <i class="bi bi-clipboard"></i> PASTE TABLE
                                </button>
                                <button type="button" onclick="clearSecondSpecTableValues()" style="display:inline-flex;align-items:center;gap:6px;background:#f8fafc;border:1px solid #cbd5e1;color:#334155;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                    <i class="bi bi-eraser"></i> Clear Data
                                </button>
                                <button type="button" onclick="resetSecondSpecTableFresh()" style="display:inline-flex;align-items:center;gap:6px;background:#fee2e2;border:1px solid #fecaca;color:#991b1b;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                    <i class="bi bi-arrow-counterclockwise"></i> NEW TABLE
                                </button>
                            </div>
                            <div id="specSecondTableHelpText" style="font-size:11px;color:#9ca3af;margin-top:6px;"><i class="bi bi-info-circle"></i> Easiest way: copy from Excel, then click "PASTE TABLE".</div>
                            <textarea id="specSecondTableText" rows="4" style="display:none;"></textarea>
                        </div>
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="datasheetFile"><i class="bi bi-file-earmark-pdf" style="color:#dc2626;"></i> Datasheet (PDF)</label>
                        <input type="hidden" name="existing_datasheet" id="existingDatasheetInput" value="">
                        <div id="datasheetPreview" style="display:none;margin-bottom:8px;padding:10px 12px;background:#fef2f2;border:1.5px solid #fecaca;border-radius:8px;display:flex;align-items:center;gap:10px;">
                            <i class="bi bi-file-earmark-pdf-fill" style="color:#dc2626;font-size:20px;flex-shrink:0;"></i>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" id="datasheetFileName">datasheet.pdf</div>
                                <a id="datasheetLink" href="#" target="_blank" style="font-size:10px;color:#2b11db;">View / Download</a>
                            </div>
                            <button type="button" onclick="removeDatasheet()" style="background:rgba(239,68,68,0.1);border:1px solid #fecaca;color:#dc2626;border-radius:6px;padding:4px 8px;font-size:11px;cursor:pointer;"><i class="bi bi-x-lg"></i> Remove</button>
                        </div>
                        <label for="datasheetFile" style="display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px dashed #e5e7eb;border-radius:8px;cursor:pointer;font-size:13px;color:#6b7280;font-weight:400;transition:border-color 0.2s;" onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='#e5e7eb'">
                            <i class="bi bi-upload" style="font-size:16px;"></i> Click to upload a PDF datasheet
                        </label>
                        <input type="file" id="datasheetFile" name="datasheet_file" accept=".pdf,application/pdf" style="display:none;" onchange="handleDatasheetSelect(this)">
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;"><i class="bi bi-info-circle"></i> PDF only · max 10 MB</div>
                    </div>
                </div>

                <!-- Image Section -->
                <div style="margin-bottom:12px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;"><i class="bi bi-images"></i> Product Images <span style="font-size:10px;font-weight:500;color:#9ca3af;text-transform:none;letter-spacing:0;">Up to <?php echo (int)$maxProductImages; ?> — first slot is the main image</span></h3>

                    <!-- Multi-slot image grid -->
                    <input type="hidden" name="existing_images" id="existingImagesInput" value="[]">
                    <div id="imageSlotGrid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:8px;margin-bottom:6px;"></div>

                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                        <div style="font-size:11px;color:#9ca3af;"><i class="bi bi-info-circle"></i> Click a slot to upload · click × to remove</div>
                        <button type="button" onclick="document.getElementById('bulkImageFiles').click();" style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;border:1px solid #c7d2fe;color:#2b11db;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                            <i class="bi bi-images"></i> Select Multiple Images
                        </button>
                    </div>
                    <div style="font-size:10px;color:#9ca3af;margin-bottom:6px;">Tip: choose multiple files once, and they will auto-fill the <?php echo (int)$maxProductImages; ?> slots in order.</div>

                    <!-- Bulk selector for efficient multi-upload -->
                    <input type="file" id="bulkImageFiles" accept="image/jpeg,image/pjpeg,image/png,image/webp,image/gif,image/avif,image/jfif,.jpg,.jpeg,.jfif,.png,.webp,.gif,.avif" multiple style="display:none;" onchange="handleBulkImageSelect(this)">

                    <!-- Hidden file inputs, one per slot -->
                    <div style="display:none;">
                        <?php for ($s = 0; $s < $maxProductImages; $s++): ?>
                        <input type="file" id="imageFile_<?php echo $s; ?>" name="image_file_<?php echo $s; ?>" accept="image/jpeg,image/pjpeg,image/png,image/webp,image/gif,image/avif,image/jfif,.jpg,.jpeg,.jfif,.png,.webp,.gif,.avif" onchange="previewImageSlot(this, <?php echo $s; ?>)">
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            
            <div class="edit-modal-footer">
                <button class="btn btn-outline" type="button" onclick="closeEditModal()"><i class="bi bi-x-lg"></i> Cancel</button>
                <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Save Changes</button>
            </div>
        </form>
        <div class="edit-modal-resize-handle" title="Drag to resize" aria-hidden="true"></div>
    </div>
</div>

<style>
/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden !important;
}

.edit-modal {
    position: fixed;
    inset: 0;
    z-index: 120000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 14px;
    box-sizing: border-box;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.edit-modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    cursor: pointer;
}

.edit-modal-content {
    position: relative;
    background: white;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: min(96vw, 1240px);
    max-width: min(98vw, 1380px);
    height: min(94vh, 980px);
    max-height: 96vh;
    min-width: min(760px, 98vw);
    min-height: 620px;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
}

.edit-modal-resize-handle {
    position: absolute;
    right: 10px;
    bottom: 10px;
    width: 22px;
    height: 22px;
    border-radius: 6px;
    border: 1px solid rgba(99, 102, 241, 0.45);
    cursor: nwse-resize;
    z-index: 6;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.18);
    background-color: rgba(255, 255, 255, 0.95);
    background:
        linear-gradient(135deg, transparent 0%, transparent 38%, rgba(99,102,241,0.55) 38%, rgba(99,102,241,0.55) 45%, transparent 45%, transparent 58%, rgba(99,102,241,0.55) 58%, rgba(99,102,241,0.55) 65%, transparent 65%, transparent 100%);
    opacity: 1;
}

.edit-modal-content.is-resizing,
.edit-modal-content.is-resizing * {
    cursor: nwse-resize !important;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.edit-modal-header {
    padding: 24px 24px 16px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 16px 16px 0 0;
}

.edit-modal-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #2b11db;
    display: flex;
    align-items: center;
    gap: 10px;
}

.edit-modal-close {
    background: transparent;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 8px;
    line-height: 1;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.edit-modal-close:hover {
    background: rgba(43, 17, 219, 0.1);
    color: #2b11db;
    transform: scale(1.1);
}

.edit-product-form {
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
    min-height: 0;
    height: 100%;
    overflow: hidden;
}

.edit-modal-body {
    padding: 28px;
    flex: 1 1 auto;
    min-height: 0;
    height: 0;
    max-height: none;
    overflow-y: auto;
    overflow-x: hidden;
    scroll-behavior: smooth;
    scroll-padding: 16px;
    background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 2%, rgba(255,255,255,1) 98%, rgba(0,0,0,0.02) 100%);
}

@media (max-width: 900px) {
    .edit-modal {
        padding: 10px;
    }

    .edit-modal-content {
        width: 96vw;
        max-width: 96vw;
        height: 94vh;
        min-width: 0;
        min-height: 0;
    }

    .edit-modal-body {
        padding: 18px;
    }
}

@media (pointer: coarse) {
    .edit-modal-resize-handle {
        display: none;
    }
}

/* Custom scrollbar styling */
.edit-modal-body::-webkit-scrollbar {
    width: 10px;
}

.edit-modal-body::-webkit-scrollbar-track {
    background: rgba(43, 17, 219, 0.05);
    border-radius: 10px;
}

.edit-modal-body::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, rgba(43, 17, 219, 0.4), rgba(43, 17, 219, 0.6));
    border-radius: 10px;
    transition: all 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.edit-modal-body::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, rgba(43, 17, 219, 0.7), rgba(43, 17, 219, 0.9));
    border-color: rgba(255, 255, 255, 0.5);
}

/* Firefox scrollbar */
.edit-modal-body {
    scrollbar-color: rgba(43, 17, 219, 0.5) rgba(43, 17, 219, 0.05);
    scrollbar-width: thin;
}

.edit-modal-body .field {
    margin-bottom: 18px;
}

.edit-modal-body .field:last-child {
    margin-bottom: 0;
}

.edit-modal-body label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 8px;
    font-size: 14px;
}

.edit-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border-radius: 0 0 16px 16px;
    flex-shrink: 0;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.05);
}

.edit-modal-footer .btn {
    transition: all 0.2s ease;
}

.edit-modal-footer .btn:hover {
    transform: translateY(-2px);
}

/* Form input styling */
.edit-modal-body input[type="text"],
.edit-modal-body textarea,
.edit-modal-body select {
    position: relative;
    transition: all 0.2s ease;
    border-color: #e5e7eb;
}

.edit-modal-body input[type="text"]:focus,
.edit-modal-body textarea:focus,
.edit-modal-body select:focus {
    border-color: #2b11db;
    box-shadow: 0 0 0 3px rgba(43, 17, 219, 0.1);
}

.edit-modal-body input[type="text"]:hover,
.edit-modal-body textarea:hover,
.edit-modal-body select:hover {
    border-color: var(--accent);
    box-shadow: 0 2px 8px rgba(43, 17, 219, 0.1);
}

.edit-modal-body input[type="text"]:focus,
.edit-modal-body textarea:focus,
.edit-modal-body select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(43, 17, 219, 0.1);
    outline: none;
}

.edit-modal-body select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

/* Badge select styling */
.badge-select {
    background: linear-gradient(to right, #f9fafb, #f3f4f6);
    border: 2px solid #e5e7eb;
}

.badge-select:hover {
    border-color: var(--accent);
}

.badge-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(43, 17, 219, 0.1);
}

/* Upload area styling */
.upload-area {
    position: relative;
    border: 2px dashed #e5e7eb;
    border-radius: 12px;
    padding: 28px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    overflow: hidden;
    position: relative;
}

.upload-area::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at var(--mouse-x, 50%) var(--mouse-y, 50%), rgba(43, 17, 219, 0.05), transparent 50%);
    opacity: 0;
    transition: opacity 0.3s;
}

.upload-area:hover {
    border-color: var(--accent);
    background: linear-gradient(135deg, rgba(43, 17, 219, 0.02) 0%, rgba(43, 17, 219, 0.01) 100%);
    box-shadow: 0 4px 12px rgba(43, 17, 219, 0.1);
}

.upload-area:hover::before {
    opacity: 1;
}

.upload-content {
    position: relative;
    z-index: 1;
}

.upload-icon {
    font-size: 32px;
    color: var(--accent);
    display: block;
    margin-bottom: 4px;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Animation for file selected */
@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Section headers improvement */
.edit-modal-body > div > h3 {
    position: relative;
    padding-bottom: 12px;
    margin-bottom: 16px !important;
    border-bottom: 2px solid rgba(43, 17, 219, 0.1);
}

/* Scroll shadow effect at body bottom */
.edit-modal-body::after {
    content: '';
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.05), transparent);
    pointer-events: none;
}

/* Specs editor layout polish */
.spec-editor-field {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px;
}

.spec-toolbar {
    padding: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #f8fafc;
}

#specTableBuilderWrap {
    min-height: 150px;
    max-height: 420px;
}

.spec-actions {
    padding: 8px;
    border: 1px dashed #d1d5db;
    border-radius: 10px;
    background: #fcfdff;
}

.spec-actions > button,
.spec-toolbar > button,
.spec-second-table-card button {
    min-height: 32px;
}

.spec-second-table-card {
    margin-top: 12px;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #f9fafb;
}

#specSecondTableText {
    display: none;
}

#specSecondTableBuilderWrap {
    min-height: 150px;
    max-height: 420px;
}

#specSecondTableBuilderWrap:focus {
    outline: none;
}

#specSecondTableBuilder {
    outline: none;
}

#specSecondTableHelpText {
    font-size: 11px;
    color: #9ca3af;
}

.desc-mode-toggle {
    display: inline-flex;
    align-items: center;
    gap: 0;
    border: 1px solid #d6dee8;
    border-radius: 999px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.95) inset, 0 2px 8px rgba(15, 23, 42, 0.05);
}

.desc-mode-btn {
    border: 0;
    border-right: 1px solid #d8e0e6;
    background: linear-gradient(180deg, #ffffff 0%, #f2f5f8 100%);
    color: #334155;
    padding: 7px 15px;
    font-size: 11px;
    font-weight: 800;
    cursor: pointer;
    letter-spacing: 0.03em;
    transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, transform 0.08s ease;
}

.desc-mode-btn:last-child {
    border-right: 0;
}

.desc-mode-btn.is-active {
    background: linear-gradient(180deg, #2f7f42 0%, #256536 100%);
    color: #fff;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.16), 0 2px 6px rgba(20, 83, 45, 0.16);
}

.desc-mode-btn:hover {
    background: linear-gradient(180deg, #ffffff 0%, #e7edf4 100%);
}

.desc-mode-btn.is-active:hover {
    background: linear-gradient(180deg, #33914c 0%, #245f33 100%);
}

.desc-mode-btn:active {
    transform: translateY(1px);
}

.desc-action-btn {
    border: 1px solid #c9d5e1;
    background: linear-gradient(180deg, #ffffff 0%, #f3f6f9 100%);
    color: #1e293b;
    border-radius: 10px;
    padding: 9px 14px;
    font-size: 12px;
    font-weight: 800;
    cursor: pointer;
    line-height: 1;
    transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease, transform 0.08s ease;
    box-shadow: 0 1px 0 rgba(255, 255, 255, 0.96) inset, 0 2px 6px rgba(15, 23, 42, 0.04);
    min-height: 36px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.desc-action-btn:hover {
    background: linear-gradient(180deg, #ffffff 0%, #e9eff5 100%);
    border-color: #aab8c6;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}

.desc-action-btn:active {
    transform: translateY(1px);
    box-shadow: inset 0 2px 6px rgba(15, 23, 42, 0.15);
}

.desc-table-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 10px;
    padding: 12px;
    border: 1px solid #d9e0e8;
    border-radius: 14px;
    background: linear-gradient(180deg, #ffffff 0%, #f4f7fa 100%);
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05), inset 0 1px 0 rgba(255,255,255,0.96);
}

.desc-table-toolbar button i,
.desc-mode-btn i {
    font-size: 0.95em;
}

.desc-table-shell {
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    background: #ffffff;
    overflow: auto;
    resize: vertical;
    height: 420px;
    min-height: 260px;
    max-height: 78vh;
    box-shadow: 0 3px 14px rgba(15, 23, 42, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.96);
}

.desc-custom-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    min-width: 820px;
    background: #ffffff;
    color: #1f2937;
}

.desc-custom-table td,
.desc-custom-table th {
    border: 1px solid #d8e0e8;
    padding: 10px 12px;
    vertical-align: top;
    font-size: 13px;
    line-height: 1.45;
    min-width: 140px;
    background: #ffffff;
    color: #1f2937;
}

.desc-custom-table td {
    position: relative;
}

.desc-cell-editor {
    min-height: 28px;
    outline: none;
    white-space: pre-wrap;
    word-break: break-word;
    border-radius: 2px;
}

.desc-cell-editor:focus {
    background: #f8fcff;
}

.desc-custom-table th {
    background: linear-gradient(180deg, #f7fafc 0%, #ebf1f6 100%);
    font-weight: 800;
    color: #243447;
}

.desc-custom-table td[contenteditable="true"],
.desc-custom-table th[contenteditable="true"] {
    outline: none;
    cursor: text;
}

.desc-custom-table td.is-selected,
.desc-custom-table th.is-selected {
    background: linear-gradient(180deg, #eef6ff 0%, #dcecff 100%);
    box-shadow: inset 0 0 0 2px #2f7fd6;
}

.desc-custom-table img {
    max-width: 100%;
    height: auto;
    display: inline-block;
}

.desc-cell-image-wrap {
    position: relative;
    display: inline-block;
    margin: 8px 8px 0 0;
    padding: 3px;
    border: 1px solid #d0dbe6;
    border-radius: 10px;
    background: #ffffff;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
}

.desc-cell-image-wrap img {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 6px;
}

.desc-cell-image-delete {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 18px;
    height: 18px;
    border: 1px solid #e57d7d;
    border-radius: 999px;
    background: #e05252;
    color: #fff;
    font-size: 11px;
    line-height: 1;
    font-weight: 800;
    cursor: pointer;
    padding: 0;
}

.desc-cell-image-delete:hover {
    background: #bf3d3d;
}

.desc-custom-table .desc-head-letter {
    background: linear-gradient(180deg, #edf2f6 0%, #dfe6ed 100%);
    color: #334155;
    text-align: center;
    font-weight: 800;
    width: 56px;
    min-width: 56px;
    max-width: 56px;
}

.desc-custom-table .desc-head-letter {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}

.desc-custom-table .desc-head-label {
    background: linear-gradient(180deg, #f7fafc 0%, #ebf1f6 100%);
    font-weight: 800;
}

.desc-custom-table .desc-head-label input,
.desc-custom-table .desc-head-label [contenteditable="true"] {
    border: 0;
    background: transparent;
    color: #1f2937;
    width: 100%;
    font: inherit;
    outline: none;
}

#editDescriptionTableSelectionCount {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(180deg, #f3fbf5 0%, #e6f4e9 100%);
    border: 1px solid #c8dfcc;
    color: #1f5f31 !important;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.02em;
}

#editDescriptionTableHint {
    color: #5b6b7c !important;
}

#editDescriptionTableEditor thead tr:first-child th,
#editDescriptionTableEditor thead tr:nth-child(2) th {
    position: sticky;
    top: 0;
    z-index: 2;
}

#editDescriptionTableEditor thead tr:first-child th,
#editDescriptionTableEditor thead tr:nth-child(2) th {
    background-clip: padding-box;
}

#editDescriptionTableEditor thead tr:first-child th {
    top: 0;
}

#editDescriptionTableEditor thead tr:nth-child(2) th {
    top: 35px;
}

.edit-description-field textarea#editDescription {
    min-height: 220px;
    max-height: 72vh;
    resize: vertical;
    font-size: 14px;
    line-height: 1.7;
}

#editDescription {
    max-height: 72vh;
    resize: vertical;
}
</style>

<script>
// Edit product modal functionality
var _andisonCategories = <?php echo json_encode(array_map(function($c){ return ['id'=>$c['id'],'name'=>$c['name'],'subcategories'=>$c['subcategories']??[]]; }, $allCategories), JSON_HEX_TAG); ?>;
var _selectedBrandKeyForEditor = <?php echo json_encode((string)$selectedBrandKey, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var _subSubcategoryDisabledParents = ['welding-head-and-face-protection'];

function isSubSubcategoryDisabledForSubcategory(subId) {
    return _subSubcategoryDisabledParents.indexOf(String(subId || '')) !== -1;
}

// Resolve category path from normalized or legacy record values.
function resolveCategoryPath(catId, subId, subSubId) {
    var path = { subId: '', subSubId: '' };
    if (!catId) return path;

    var cat = _andisonCategories.find(function(c){ return c.id === catId; });
    if (!cat) {
        path.subId = subId;
        path.subSubId = subSubId;
        return path;
    }

    if (subSubId) {
        path.subSubId = subSubId;

        if (subId) {
            path.subId = subId;
        } else {
            for (var p = 0; p < (cat.subcategories || []).length; p++) {
                var parent = cat.subcategories[p];
                var deepBySubSub = (parent.subcategories || []).find(function(ss){ return ss.id === subSubId; });
                if (deepBySubSub) {
                    path.subId = parent.id;
                    break;
                }
            }
        }
        return path;
    }

    if (!subId) return path;

    var direct = (cat.subcategories || []).find(function(s){ return s.id === subId; });
    if (direct) {
        path.subId = subId;
        return path;
    }

    for (var i = 0; i < (cat.subcategories || []).length; i++) {
        var sub = cat.subcategories[i];
        var deep = (sub.subcategories || []).find(function(ss){ return ss.id === subId; });
        if (deep) {
            path.subId = sub.id;
            path.subSubId = deep.id;
            return path;
        }
    }

    path.subId = subId;
    return path;
}

function populateCategorySubcategories(selectedSubId, selectedSubSubId) {
    var catId   = document.getElementById('editCategory').value;
    var subSel  = document.getElementById('editSubcategory');
    var subSubSel = document.getElementById('editSubSubcategory');
    subSel.innerHTML = '<option value="">' + (catId ? '-- Select Subcategory --' : '-- Select Category First --') + '</option>';
    if (subSubSel) {
        subSubSel.innerHTML = '<option value="">-- Optional --</option>';
        subSubSel.disabled = true;
    }
    if (!catId) {
        updateFinalSubcategory();
        return;
    }

    var cat = _andisonCategories.find(function(c){ return c.id === catId; });
    if (!cat) { updateFinalSubcategory(); return; }

    (cat.subcategories || []).forEach(function(sub){
        var opt = document.createElement('option');
        opt.value = sub.id;
        opt.textContent = sub.name;
        if (selectedSubId && sub.id === selectedSubId) opt.selected = true;
        subSel.appendChild(opt);
    });

    populateSubSubcategories(selectedSubId || '', selectedSubSubId || '');
}

function populateSubSubcategories(selectedSubId, selectedSubSubId) {
    var catId = document.getElementById('editCategory').value;
    var subSel = document.getElementById('editSubcategory');
    var subSubSel = document.getElementById('editSubSubcategory');
    if (!subSubSel) {
        updateFinalSubcategory();
        return;
    }

    var activeSubId = selectedSubId || (subSel ? subSel.value : '');
    subSubSel.innerHTML = '<option value="">-- Optional --</option>';

    if (!catId || !activeSubId) {
        subSubSel.disabled = true;
        updateFinalSubcategory();
        return;
    }

    if (isSubSubcategoryDisabledForSubcategory(activeSubId)) {
        subSubSel.innerHTML = '<option value="">-- Not available for this subcategory --</option>';
        subSubSel.disabled = true;
        updateFinalSubcategory();
        return;
    }

    var cat = _andisonCategories.find(function(c){ return c.id === catId; });
    if (!cat) {
        subSubSel.disabled = true;
        updateFinalSubcategory();
        return;
    }

    var sub = (cat.subcategories || []).find(function(s){ return s.id === activeSubId; });
    var nested = sub && Array.isArray(sub.subcategories) ? sub.subcategories : [];
    if (!nested.length) {
        subSubSel.innerHTML = '<option value="">-- None available --</option>';
        subSubSel.disabled = true;
        updateFinalSubcategory();
        return;
    }

    nested.forEach(function(ss){
        var opt = document.createElement('option');
        opt.value = ss.id;
        opt.textContent = ss.name;
        if (selectedSubSubId && ss.id === selectedSubSubId) opt.selected = true;
        subSubSel.appendChild(opt);
    });
    subSubSel.disabled = false;

    updateFinalSubcategory();
}

function updateFinalSubcategory() {
    var catId = document.getElementById('editCategory').value;
    var subId = document.getElementById('editSubcategory').value;
    var subSubSel = document.getElementById('editSubSubcategory');
    var subSubId = subSubSel ? subSubSel.value : '';
    if (!catId) {
        subId = '';
        subSubId = '';
    }

    document.getElementById('finalCategoryId').value    = catId;
    document.getElementById('finalSubcategoryId').value = subId;
    var subSubHidden = document.getElementById('finalSubSubcategoryId');
    if (subSubHidden) {
        subSubHidden.value = subSubId;
    }
    refreshCategoryPreview();
}

function refreshCategoryPreview() {
    var preview = document.getElementById('categoryLivePreview');
    if (!preview) return;
    var cat = document.getElementById('finalCategoryId').value;
    var sub = document.getElementById('finalSubcategoryId').value;
    var subSub = '';
    var subSubInput = document.getElementById('finalSubSubcategoryId');
    if (subSubInput) {
        subSub = subSubInput.value;
    }
    if (!cat) {
        preview.innerHTML = '<span style="color:#b45309;"><i class="bi bi-exclamation-triangle" style="font-size:10px;"></i> No category assigned — product won\'t appear in browse pages.</span>';
        return;
    }
    // Resolve human-readable names
    var catName = cat;
    var subName = '';
    var subSubName = '';
    var catData = _andisonCategories.find(function(c){ return c.id === cat; });
    if (catData) {
        catName = catData.name || cat;

        if (sub) {
            var subData = (catData.subcategories || []).find(function(s){ return s.id === sub; });
            if (subData) {
                subName = subData.name || sub;
                if (subSub) {
                    var explicitDeep = (subData.subcategories || []).find(function(ss){ return ss.id === subSub; });
                    if (explicitDeep) {
                        subSubName = explicitDeep.name || subSub;
                    }
                }
            }
        }

        if (subSub && !subSubName) {
            for (var i = 0; i < (catData.subcategories || []).length; i++) {
                var parentSub = catData.subcategories[i];
                var deep = (parentSub.subcategories || []).find(function(ss){ return ss.id === subSub; });
                if (deep) {
                    if (!subName) {
                        subName = parentSub.name || parentSub.id;
                    }
                    subSubName = deep.name || deep.id;
                    break;
                }
            }
        }
    }

    if (!subName && sub) {
        subName = sub;
    }
    if (!subSubName && subSub) {
        subSubName = subSub;
    }

    var path = catName;
    if (subName) path += ' › ' + subName;
    if (subSubName) path += ' › ' + subSubName;

    preview.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:10px;"></i> '
        + '<span style="color:#166534;font-weight:500;">Assigned: ' + path + '</span>';
}

function onTypePresetChange() {
    var preset = document.getElementById('editTypePreset');
    var input = document.getElementById('editType');
    if (!preset || !input) return;

    if (preset.value && preset.value !== '__custom__') {
        input.value = preset.value;
    }
    if (preset.value === '__custom__') {
        input.focus();
        input.select();
    }
}

function syncTypePresetFromInput() {
    var preset = document.getElementById('editTypePreset');
    var input = document.getElementById('editType');
    if (!preset || !input) return;

    var val = input.value.trim();
    if (val === '') {
        preset.value = '';
        return;
    }

    var hasMatch = false;
    for (var i = 0; i < preset.options.length; i++) {
        if (preset.options[i].value === val) {
            hasMatch = true;
            break;
        }
    }
    preset.value = hasMatch ? val : '__custom__';
}

function normalizeSpecTableRows(rows) {
    if (!Array.isArray(rows)) return [];
    return rows.map(function(row) {
        if (Array.isArray(row)) {
            return {
                label: String(row[0] || '').trim(),
                value: String(row[1] || '').trim(),
            };
        }
        if (row && typeof row === 'object') {
            return {
                label: String(row.label || row.key || '').trim(),
                value: String(row.value || '').trim(),
            };
        }
        return { label: '', value: '' };
    }).filter(function(row) {
        return row.label !== '' || row.value !== '';
    });
}

function normalizeSpecMatrixRows(rows, columnCount) {
    if (!Array.isArray(rows)) return [];
    return rows.map(function(row) {
        var out = Array.isArray(row) ? row.slice(0, columnCount) : [];
        while (out.length < columnCount) out.push('');
        return out.map(function(cell) { return String(cell || ''); });
    });
}

function isDefaultSpecHeaderLabel(header, idx) {
    var h = String(header || '').trim().toLowerCase();
    if (h === '') return true;
    if (h === ('column ' + (idx + 1)).toLowerCase()) return true;
    if (idx === 0 && h === 'parameter') return true;
    if (idx === 1 && h === 'value') return true;
    return false;
}

function parseSpecificationsForEditor(rawSpecifications) {
    var source = String(rawSpecifications || '').trim();
    var result = {
        text: '',
        table: [],
        matrix: null,
        sourceHtml: '',
        secondTableText: '',
        specImage: '',
    };

    if (!source) return result;

    try {
        var parsed = JSON.parse(source);
        if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
            result.specImage = String(parsed.specImage || parsed.spec_image || '').trim();
            var hasTables = Array.isArray(parsed.tables);
            if (parsed.format === 'andison_specs_v3' && hasTables && parsed.tables.length === 0) {
                result.text = String(parsed.text || '').trim();
                return result;
            }
            if (parsed.format === 'andison_specs_v3' && hasTables && parsed.tables.length > 0) {
                result.text = String(parsed.text || '').trim();
                var first = parsed.tables[0] || {};
                result.sourceHtml = String(first.tableHtml || '').trim();

                for (var ti = 1; ti < parsed.tables.length; ti++) {
                    var extra = parsed.tables[ti] || {};
                    var extraMatrix = extra.tableMatrix && typeof extra.tableMatrix === 'object' ? extra.tableMatrix : null;
                    if (!extraMatrix) continue;
                    var extraText = matrixPayloadToTabText(extraMatrix);
                    if (extraText !== '') {
                        result.secondTableText = extraText;
                        break;
                    }
                }

                var matrixRawV3 = first.tableMatrix && typeof first.tableMatrix === 'object' ? first.tableMatrix : null;
                if (matrixRawV3) {
                    var modeV3 = matrixRawV3.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
                    var headersV3 = Array.isArray(matrixRawV3.headers)
                        ? matrixRawV3.headers.map(function(h) { return String(h || '').trim(); }).filter(function(h) { return h !== ''; })
                        : [];

                    if (headersV3.length > 0) {
                        var leadV3 = Math.max(0, parseInt(matrixRawV3.leadColumns, 10) || 0);
                        if (leadV3 >= headersV3.length) leadV3 = Math.max(0, headersV3.length - 1);
                        var rowsV3 = normalizeSpecMatrixRows(matrixRawV3.rows || [], headersV3.length);
                        if (rowsV3.length === 0) rowsV3 = [new Array(headersV3.length).fill('')];
                        result.matrix = {
                            mode: modeV3,
                            leadColumns: leadV3,
                            headers: headersV3,
                            rows: rowsV3,
                            groups: modeV3 === 'grouped-pairs'
                                ? normalizeGroupedGroups(matrixRawV3.groups || [], Math.max(1, headersV3.length - leadV3))
                                : [],
                            merges: modeV3 === 'standard' && Array.isArray(matrixRawV3.merges)
                                ? matrixRawV3.merges
                                : [],
                            rowMerges: modeV3 === 'grouped-pairs' && Array.isArray(matrixRawV3.rowMerges)
                                ? matrixRawV3.rowMerges
                                : [],
                        };
                        return result;
                    }
                }
            }

            var hasMatrix = parsed.tableMatrix && typeof parsed.tableMatrix === 'object';
            var looksLikeV2 = parsed.format === 'andison_specs_v2' || hasMatrix;

            if (looksLikeV2) {
                result.text = String(parsed.text || '').trim();

                var matrixRaw = parsed.tableMatrix || {};
                var mode = matrixRaw.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
                var leadColumns = parseInt(matrixRaw.leadColumns, 10);
                if (!isFinite(leadColumns) || leadColumns < 0) leadColumns = 0;
                var headers = Array.isArray(matrixRaw.headers)
                    ? matrixRaw.headers.map(function(h) { return String(h || '').trim(); }).filter(function(h) { return h !== ''; })
                    : [];

                if (mode === 'grouped-pairs') {
                    if (headers.length === 0) {
                        headers = getDefaultGroupedHeaders();
                    }

                    if (leadColumns >= headers.length) {
                        leadColumns = Math.max(0, headers.length - 1);
                    }

                    var groups = normalizeGroupedGroups(matrixRaw.groups || [], Math.max(1, headers.length - leadColumns));
                    var targetCols = leadColumns + getGroupedDataColumnCount(groups);
                    headers = headers.slice(0, targetCols);
                    while (headers.length < targetCols) {
                        headers.push(getDefaultGroupedSubHeader(headers.length));
                    }

                    headers[0] = String(headers[0] || 'Model').trim() || 'Model';
                    for (var hc = leadColumns; hc < headers.length; hc++) {
                        if (String(headers[hc] || '').trim() === '') {
                            headers[hc] = getDefaultGroupedSubHeader(hc);
                        }
                    }

                    var groupedRows = normalizeSpecMatrixRows(matrixRaw.rows || [], headers.length);
                    if (groupedRows.length === 0) groupedRows = [new Array(headers.length).fill('')];

                    result.matrix = {
                        mode: mode,
                        leadColumns: leadColumns,
                        headers: headers,
                        rows: groupedRows,
                        groups: groups,
                        rowMerges: Array.isArray(matrixRaw.rowMerges) ? matrixRaw.rowMerges : [],
                    };
                    return result;
                }

                if (headers.length > 0) {
                    var rows = normalizeSpecMatrixRows(matrixRaw.rows || [], headers.length);
                    if (rows.length === 0) rows = [new Array(headers.length).fill('')];

                    var merges = (Array.isArray(matrixRaw.merges) ? matrixRaw.merges : []).map(function(m) {
                        var row = parseInt(m && m.row, 10);
                        var col = parseInt(m && m.col, 10);
                        var span = parseInt(m && m.span, 10);
                        if (!isFinite(row) || row < 0) return null;
                        if (!isFinite(col) || col < 1 || col >= headers.length) return null;
                        if (!isFinite(span) || span < 2) return null;
                        var maxSpan = headers.length - col;
                        if (maxSpan < 2) return null;
                        if (span > maxSpan) span = maxSpan;
                        return { row: row, col: col, span: span };
                    }).filter(function(m) { return !!m; });

                    result.matrix = {
                        mode: 'standard',
                        headers: headers,
                        rows: rows,
                        groups: [],
                        merges: merges,
                    };
                    return result;
                }
            }

            var hasTable = Array.isArray(parsed.table);
            if (parsed.format === 'andison_specs_v1' || hasTable) {
                result.text = String(parsed.text || '').trim();
                result.table = normalizeSpecTableRows(parsed.table || []);
                return result;
            }
        }
    } catch (e) {
        // Legacy plain text specifications are valid.
    }

    result.text = source;
    return result;
}

function specTableRowsToMatrix(tableRows) {
    var rows = normalizeSpecTableRows(tableRows);
    if (rows.length === 0) {
        return {
            headers: ['Parameter', 'Value'],
            rows: [['', '']],
        };
    }

    var keyValueRows = rows.every(function(item) {
        return String(item.value || '').indexOf('|') === -1;
    });

    if (keyValueRows) {
        return {
            headers: ['Parameter', 'Value'],
            rows: rows.map(function(item) {
                return [String(item.label || ''), String(item.value || '')];
            }),
        };
    }

    var headers = [];
    var columns = [];
    var maxRows = 1;

    rows.forEach(function(item, idx) {
        var label = String(item.label || '').trim();
        headers.push(label !== '' ? label : ('Column ' + (idx + 1)));

        var colValues = String(item.value || '')
            .split('|')
            .map(function(v) { return v.trim(); });
        columns.push(colValues);
        if (colValues.length > maxRows) maxRows = colValues.length;
    });

    var matrixRows = [];
    for (var r = 0; r < maxRows; r++) {
        var row = [];
        for (var c = 0; c < headers.length; c++) {
            row.push(columns[c][r] || '');
        }
        matrixRows.push(row);
    }

    return {
        headers: headers,
        rows: matrixRows,
    };
}

function matrixToSpecTableRows(headers, rows) {
    if (!Array.isArray(headers) || headers.length === 0) return [];

    var normalizedRows = Array.isArray(rows) ? rows : [];
    var tableRows = [];
    var hasMeaningfulData = false;

    for (var c = 0; c < headers.length; c++) {
        var header = String(headers[c] || '').trim();
        var colValues = [];

        for (var r = 0; r < normalizedRows.length; r++) {
            var row = normalizedRows[r];
            var cell = '';
            if (Array.isArray(row) && c < row.length) {
                cell = String(row[c] || '').trim();
            }
            colValues.push(cell);
        }

        while (colValues.length > 0 && colValues[colValues.length - 1] === '') {
            colValues.pop();
        }

        var hasValues = colValues.some(function(v) { return v !== ''; });
        var defaultHeader = isDefaultSpecHeaderLabel(header, c);

        if (!hasValues && defaultHeader) {
            continue;
        }

        var finalLabel = header !== '' ? header : ('Column ' + (c + 1));
        tableRows.push({
            label: finalLabel,
            value: colValues.join('|'),
        });

        if (hasValues || !defaultHeader) {
            hasMeaningfulData = true;
        }
    }

    return hasMeaningfulData ? tableRows : [];
}

var _specTableMode = 'standard';
var _specTableHeaders = ['Parameter', 'Value'];
var _specTableRows = [['', '']];
var _specTableGroups = [];
var _specTableMerges = [];
var _specTableSourceHtml = '';
var _specTableLeadColumns = 0;
var _specTableRowMerges = [];
var _specTableHasUserInput = false;  // Track if user has actively used the table builder
var _specShowGroupHeaderControls = false;

var _specSecondTableMode = 'standard';
var _specSecondTableHeaders = ['Parameter', 'Value'];
var _specSecondTableRows = [['', '']];
var _specSecondTableHasUserInput = false;

function escapeSpecHtml(str) {
    return String(str || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function getDefaultGroupedHeaders() {
    return ['Model', 'cfm', 'm3/hr'];
}

function getDefaultGroupedGroups() {
    return [{ title: 'Free Air', span: 2, rowSpan: false }];
}

function getDefaultGroupedSubHeader(colIdx) {
    return (colIdx % 2 === 1) ? 'cfm' : 'm3/hr';
}

function normalizeGroupedGroups(groups, dataColumnCount) {
    var cols = parseInt(dataColumnCount, 10);
    if (!isFinite(cols) || cols < 1) cols = 1;

    var raw = Array.isArray(groups) ? groups : [];
    var normalized = raw.map(function(g) {
        if (g && typeof g === 'object' && !Array.isArray(g)) {
            var spanObj = parseInt(g.span, 10);
            return {
                title: String(g.title || g.label || g.name || '').trim(),
                span: (isFinite(spanObj) && spanObj > 0) ? spanObj : 1,
                rowSpan: !!g.rowSpan,
            };
        }
        return {
            title: String(g || '').trim(),
            span: 2,
            rowSpan: false,
        };
    });

    if (normalized.length === 0) {
        normalized = [{ title: 'Free Air', span: cols }];
    }

    var remaining = cols;
    for (var i = 0; i < normalized.length; i++) {
        var groupsLeft = normalized.length - i - 1;
        if (i === normalized.length - 1) {
            normalized[i].span = Math.max(1, remaining);
            remaining = 0;
            break;
        }

        var maxSpan = Math.max(1, remaining - groupsLeft);
        var nextSpan = normalized[i].span;
        if (!isFinite(nextSpan) || nextSpan < 1) nextSpan = 1;
        if (nextSpan > maxSpan) nextSpan = maxSpan;

        normalized[i].span = nextSpan;
        remaining -= nextSpan;
    }

    if (remaining > 0 && normalized.length > 0) {
        normalized[normalized.length - 1].span += remaining;
    }

    return normalized.map(function(g, idx) {
        var title = String(g.title || '').trim();
        if (title === '') {
            title = idx === 0 ? 'Free Air' : ('Group ' + (idx + 1));
        }
        return { title: title, span: g.span, rowSpan: g.span === 1 ? !!g.rowSpan : false };
    });
}

function getGroupedLeadColumnCount() {
    var lead = parseInt(_specTableLeadColumns, 10);
    if (!isFinite(lead) || lead < 0) lead = 0;
    if (Array.isArray(_specTableHeaders) && _specTableHeaders.length > 0 && lead >= _specTableHeaders.length) {
        lead = Math.max(0, _specTableHeaders.length - 1);
    }
    return lead;
}

function normalizeSpecRowMerges(rowMerges, totalRows, totalCols) {
    var maxRows = Math.max(0, parseInt(totalRows, 10) || 0);
    var maxCols = Math.max(0, parseInt(totalCols, 10) || 0);

    var parsed = (Array.isArray(rowMerges) ? rowMerges : []).map(function(m) {
        var row = parseInt(m && m.row, 10);
        var col = parseInt(m && m.col, 10);
        var rowSpan = parseInt(m && m.rowSpan, 10);
        if (!isFinite(row) || row < 0 || row >= maxRows) return null;
        if (!isFinite(col) || col < 0 || col >= maxCols) return null;
        if (!isFinite(rowSpan) || rowSpan < 2) return null;
        var maxSpan = maxRows - row;
        if (maxSpan < 2) return null;
        if (rowSpan > maxSpan) rowSpan = maxSpan;
        return { row: row, col: col, rowSpan: rowSpan };
    }).filter(function(m) { return !!m; });

    parsed.sort(function(a, b) {
        if (a.col !== b.col) return a.col - b.col;
        return a.row - b.row;
    });

    var out = [];
    var lastByCol = {};
    for (var i = 0; i < parsed.length; i++) {
        var cur = parsed[i];
        var prev = lastByCol[cur.col];
        if (prev) {
            var prevEnd = prev.row + prev.rowSpan - 1;
            if (cur.row <= prevEnd) {
                continue;
            }
        }
        out.push(cur);
        lastByCol[cur.col] = cur;
    }

    return out;
}

function getGroupedDataColumnCount(groups) {
    if (!Array.isArray(groups)) return 0;
    return groups.reduce(function(sum, g) {
        var span = parseInt(g && g.span, 10);
        if (!isFinite(span) || span < 1) span = 1;
        return sum + span;
    }, 0);
}

function getGroupedStartColumn(groupIdx, groups) {
    var gs = Array.isArray(groups) ? groups : _specTableGroups;
    var col = getGroupedLeadColumnCount();
    for (var i = 0; i < groupIdx; i++) {
        var span = parseInt(gs[i] && gs[i].span, 10);
        if (!isFinite(span) || span < 1) span = 1;
        col += span;
    }
    return col;
}

function findGroupedIndexByColumn(colIdx) {
    var lead = getGroupedLeadColumnCount();
    if (colIdx < lead) return -1;

    var cursor = lead;
    for (var i = 0; i < _specTableGroups.length; i++) {
        var span = parseInt(_specTableGroups[i] && _specTableGroups[i].span, 10);
        if (!isFinite(span) || span < 1) span = 1;
        var end = cursor + span - 1;
        if (colIdx >= cursor && colIdx <= end) return i;
        cursor = end + 1;
    }
    return _specTableGroups.length - 1;
}

function isDefaultGroupedHeader(header, colIdx) {
    var h = String(header || '').trim().toLowerCase();
    if (colIdx === 0) return h === 'model' || h === '';
    var pos = (colIdx - 1) % 2;
    return pos === 0 ? (h === 'cfm' || h === '') : (h === 'm3/hr' || h === 'm3hr' || h === '');
}

function normalizeSpecTableState() {
    if (_specTableMode !== 'grouped-pairs') _specTableMode = 'standard';

    if (!Array.isArray(_specTableHeaders)) _specTableHeaders = [];
    if (!Array.isArray(_specTableRows)) _specTableRows = [];
    if (!Array.isArray(_specTableMerges)) _specTableMerges = [];
    if (!Array.isArray(_specTableRowMerges)) _specTableRowMerges = [];
    _specTableLeadColumns = parseInt(_specTableLeadColumns, 10);
    if (!isFinite(_specTableLeadColumns) || _specTableLeadColumns < 0) _specTableLeadColumns = 0;

    if (_specTableMode === 'grouped-pairs') {
        if (_specTableHeaders.length === 0) {
            _specTableHeaders = getDefaultGroupedHeaders();
        }

        if (_specTableLeadColumns >= _specTableHeaders.length) {
            _specTableLeadColumns = Math.max(0, _specTableHeaders.length - 1);
        }

        var leadCols = getGroupedLeadColumnCount();
        var currentDataCols = Math.max(1, _specTableHeaders.length - leadCols);

        // If groups are missing, create one group that covers current data columns.
        if (!Array.isArray(_specTableGroups) || _specTableGroups.length === 0) {
            _specTableGroups = [{ title: 'Group 1', span: currentDataCols, rowSpan: false }];
        }

        // Preserve existing visible columns: never silently drop trailing columns.
        // If header/data has more columns than group spans, extend the last group.
        var groupedCols = getGroupedDataColumnCount(_specTableGroups);
        if (currentDataCols > groupedCols) {
            var lastIdx = _specTableGroups.length - 1;
            var lastSpan = parseInt(_specTableGroups[lastIdx] && _specTableGroups[lastIdx].span, 10);
            if (!isFinite(lastSpan) || lastSpan < 1) lastSpan = 1;
            _specTableGroups[lastIdx].span = lastSpan + (currentDataCols - groupedCols);
            groupedCols = currentDataCols;
        }

        _specTableGroups = normalizeGroupedGroups(_specTableGroups, Math.max(1, groupedCols));

        var normalizedDataCols = Math.max(groupedCols, getGroupedDataColumnCount(_specTableGroups));
        var targetHeaders = leadCols + normalizedDataCols;
        _specTableHeaders = _specTableHeaders.slice(0, targetHeaders);
        while (_specTableHeaders.length < targetHeaders) {
            if (_specTableHeaders.length < leadCols) {
                _specTableHeaders.push('Column ' + (_specTableHeaders.length + 1));
            } else {
                _specTableHeaders.push(getDefaultGroupedSubHeader(_specTableHeaders.length));
            }
        }

        for (var lh = 0; lh < leadCols; lh++) {
            if (String(_specTableHeaders[lh] || '').trim() === '') {
                _specTableHeaders[lh] = lh === 0 ? 'Model' : ('Column ' + (lh + 1));
            }
        }

        for (var hc = leadCols; hc < _specTableHeaders.length; hc++) {
            var val = String(_specTableHeaders[hc] || '').trim();
            if (val === '') {
                _specTableHeaders[hc] = getDefaultGroupedSubHeader(hc);
            }
        }
    } else {
        if (_specTableHeaders.length === 0) _specTableHeaders = ['Parameter', 'Value'];
        _specTableGroups = [];
        _specTableRowMerges = [];

        var maxMergeRow = Math.max(0, _specTableRows.length - 1);
        var maxMergeCol = Math.max(0, _specTableHeaders.length - 1);
        _specTableMerges = _specTableMerges.map(function(m) {
            var row = parseInt(m && m.row, 10);
            var col = parseInt(m && m.col, 10);
            var span = parseInt(m && m.span, 10);
            if (!isFinite(row) || row < 0 || row > maxMergeRow) return null;
            if (!isFinite(col) || col < 1 || col > maxMergeCol) return null;
            if (!isFinite(span) || span < 2) return null;
            var maxSpan = _specTableHeaders.length - col;
            if (maxSpan < 2) return null;
            if (span > maxSpan) span = maxSpan;
            return { row: row, col: col, span: span };
        }).filter(function(m) { return !!m; });
    }

    if (_specTableRows.length === 0) {
        _specTableRows = [new Array(_specTableHeaders.length).fill('')];
    }

    _specTableRows = _specTableRows.map(function(row) {
        var next = Array.isArray(row) ? row.slice(0, _specTableHeaders.length) : [];
        while (next.length < _specTableHeaders.length) next.push('');
        return next;
    });

    _specTableRowMerges = normalizeSpecRowMerges(_specTableRowMerges, _specTableRows.length, _specTableHeaders.length);
}

function getSpecColumnLabel(colIdx) {
    var n = parseInt(colIdx, 10);
    if (!isFinite(n) || n < 0) return '';

    var label = '';
    n += 1;
    while (n > 0) {
        var rem = (n - 1) % 26;
        label = String.fromCharCode(65 + rem) + label;
        n = Math.floor((n - 1) / 26);
    }

    return label;
}

function getSpecCellPositionFromElement(el) {
    if (!el || !el.getAttribute) return { row: 0, col: 0 };

    var row = parseInt(el.getAttribute('data-row'), 10);
    var col = parseInt(el.getAttribute('data-col'), 10);

    if (!isFinite(row) || row < 0) row = 0;
    if (!isFinite(col) || col < 0) col = 0;

    return { row: row, col: col };
}

function focusSpecTableCell(rowIdx, colIdx) {
    var selector = '.spec-table-input[data-row="' + rowIdx + '"][data-col="' + colIdx + '"]';
    var input = document.querySelector(selector);
    if (!input) return false;

    input.focus();
    input.select();
    return true;
}

function autoResizeSpecCell(el) {
    if (!el) return;
    el.style.height = 'auto';
    var nextHeight = Math.max(34, el.scrollHeight);
    el.style.height = nextHeight + 'px';
}

function ensureSpecTableGridSize(minRows, minCols) {
    normalizeSpecTableState();

    var targetRows = parseInt(minRows, 10);
    var targetCols = parseInt(minCols, 10);
    if (!isFinite(targetRows) || targetRows < 1) targetRows = 1;
    if (!isFinite(targetCols) || targetCols < 1) targetCols = 1;

    while (_specTableHeaders.length < targetCols) {
        if (_specTableMode === 'grouped-pairs') {
            _specTableHeaders.push(getDefaultGroupedSubHeader(_specTableHeaders.length));
        } else {
            _specTableHeaders.push('Column ' + (_specTableHeaders.length + 1));
        }
    }

    _specTableRows = _specTableRows.map(function(row) {
        var next = Array.isArray(row) ? row.slice(0) : [];
        while (next.length < _specTableHeaders.length) next.push('');
        return next;
    });

    while (_specTableRows.length < targetRows) {
        _specTableRows.push(new Array(_specTableHeaders.length).fill(''));
    }
}

function getNormalizedCellText(raw) {
    return String(raw || '')
        .replace(/\u00a0/g, ' ')
        .replace(/\r\n?/g, '\n')
        .replace(/[\u200B-\u200D\uFEFF]/g, '')
        .split('\n')
        .map(function(line) { return line.trim(); })
        .join('\n')
        .trim();
}

function parseClipboardHtmlTable(rawHtml) {
    var html = String(rawHtml || '').trim();
    if (html === '') return null;

    var parser = new DOMParser();
    var doc = parser.parseFromString(html, 'text/html');
    var table = doc.querySelector('table');
    if (!table) return null;

    var trNodes = Array.prototype.slice.call(table.querySelectorAll('tr'));
    if (trNodes.length === 0) return null;

    function buildCanonicalTableHtml(sourceGrid, headerRows) {
        var out = ['<table>'];
        var totalRows = sourceGrid.length;
        var headRows = Math.max(0, Math.min(totalRows, parseInt(headerRows, 10) || 0));

        if (headRows > 0) {
            out.push('<thead>');
            for (var hr = 0; hr < headRows; hr++) {
                out.push('<tr>');
                var hrow = sourceGrid[hr] || [];
                for (var hc = 0; hc < hrow.length; hc++) {
                    var hslot = hrow[hc];
                    if (!hslot || !hslot.origin || !hslot.ref) continue;
                    var hcspan = Math.max(1, parseInt(hslot.ref.colSpan, 10) || 1);
                    var hrspan = Math.max(1, parseInt(hslot.ref.rowSpan, 10) || 1);
                    out.push('<th');
                    if (hcspan > 1) out.push(' colspan="' + hcspan + '"');
                    if (hrspan > 1) out.push(' rowspan="' + hrspan + '"');
                    out.push('>' + escapeSpecHtml(hslot.ref.text) + '</th>');
                }
                out.push('</tr>');
            }
            out.push('</thead>');
        }

        out.push('<tbody>');
        for (var br = headRows; br < totalRows; br++) {
            out.push('<tr>');
            var brow = sourceGrid[br] || [];
            for (var bc = 0; bc < brow.length; bc++) {
                var bslot = brow[bc];
                if (!bslot || !bslot.origin || !bslot.ref) continue;
                var bcspan = Math.max(1, parseInt(bslot.ref.colSpan, 10) || 1);
                var brspan = Math.max(1, parseInt(bslot.ref.rowSpan, 10) || 1);
                out.push('<td');
                if (bcspan > 1) out.push(' colspan="' + bcspan + '"');
                if (brspan > 1) out.push(' rowspan="' + brspan + '"');
                out.push('>' + escapeSpecHtml(bslot.ref.text) + '</td>');
            }
            out.push('</tr>');
        }
        out.push('</tbody></table>');
        return out.join('');
    }

    var grid = [];
    var maxCols = 0;

    for (var r = 0; r < trNodes.length; r++) {
        if (!grid[r]) grid[r] = [];

        var tr = trNodes[r];
        var cells = Array.prototype.slice.call(tr.children).filter(function(el) {
            return el && (el.tagName === 'TH' || el.tagName === 'TD');
        });

        var colCursor = 0;
        for (var ci = 0; ci < cells.length; ci++) {
            while (grid[r][colCursor]) colCursor++;

            var cellEl = cells[ci];
            var colSpan = parseInt(cellEl.getAttribute('colspan') || '1', 10);
            var rowSpan = parseInt(cellEl.getAttribute('rowspan') || '1', 10);
            if (!isFinite(colSpan) || colSpan < 1) colSpan = 1;
            if (!isFinite(rowSpan) || rowSpan < 1) rowSpan = 1;

            var cellObj = {
                text: getNormalizedCellText(cellEl.innerText || cellEl.textContent || ''),
                colSpan: colSpan,
                rowSpan: rowSpan,
                isHeader: cellEl.tagName === 'TH',
            };

            for (var rr = 0; rr < rowSpan; rr++) {
                if (!grid[r + rr]) grid[r + rr] = [];
                for (var cc = 0; cc < colSpan; cc++) {
                    grid[r + rr][colCursor + cc] = {
                        ref: cellObj,
                        origin: rr === 0 && cc === 0,
                    };
                }
            }

            colCursor += colSpan;
        }

        if (grid[r].length > maxCols) maxCols = grid[r].length;
    }

    if (maxCols < 1) return null;

    for (var gr = 0; gr < grid.length; gr++) {
        if (!grid[gr]) grid[gr] = [];
        while (grid[gr].length < maxCols) grid[gr].push(null);
    }

    var headerRowCount = table.tHead ? table.tHead.rows.length : 0;
    if (!headerRowCount) {
        var detected = 0;
        for (var hr = 0; hr < Math.min(3, trNodes.length); hr++) {
            var hasTh = Array.prototype.slice.call(trNodes[hr].children).some(function(el) {
                return el && el.tagName === 'TH';
            });
            if (hasTh) {
                detected = hr + 1;
            } else {
                break;
            }
        }

        // Some copied tables use <td> for the second header row; keep 2 header rows when top row is merged.
        if (detected <= 1 && trNodes.length >= 2) {
            var topHasMerge = Array.prototype.slice.call(trNodes[0].children).some(function(el) {
                var cs = parseInt((el && el.getAttribute && el.getAttribute('colspan')) || '1', 10);
                var rs = parseInt((el && el.getAttribute && el.getAttribute('rowspan')) || '1', 10);
                return (isFinite(cs) && cs > 1) || (isFinite(rs) && rs > 1);
            });
            if (topHasMerge) {
                detected = 2;
            }
        }

        headerRowCount = detected || 1;
    }
    if (headerRowCount >= grid.length) headerRowCount = Math.max(1, grid.length - 1);

    var canonicalTableHtml = buildCanonicalTableHtml(grid, headerRowCount);

    var topRow = grid[0] || [];
    var lastHeaderRow = grid[Math.max(0, headerRowCount - 1)] || [];
    var hasMergedTopHeader = topRow.some(function(slot) {
        return !!(slot && slot.origin && slot.ref && slot.ref.colSpan > 1);
    });

    if (headerRowCount >= 2 && hasMergedTopHeader && maxCols >= 2) {
        var leadColumns = 0;
        for (var lc = 0; lc < maxCols; lc++) {
            var leadSlot = topRow[lc];
            if (!leadSlot || !leadSlot.origin || !leadSlot.ref) break;
            var leadColSpan = parseInt(leadSlot.ref.colSpan, 10);
            var leadRowSpan = parseInt(leadSlot.ref.rowSpan, 10);
            if (!isFinite(leadColSpan) || leadColSpan < 1) leadColSpan = 1;
            if (!isFinite(leadRowSpan) || leadRowSpan < 1) leadRowSpan = 1;
            if (leadColSpan === 1 && leadRowSpan > 1) {
                leadColumns += 1;
                continue;
            }
            break;
        }
        if (leadColumns < 0) leadColumns = 0;

        var groups = [];

        for (var tc = leadColumns; tc < maxCols; tc++) {
            var tSlot = topRow[tc];
            if (!tSlot || !tSlot.origin || !tSlot.ref) continue;

            var tSpan = parseInt(tSlot.ref.colSpan, 10);
            if (!isFinite(tSpan) || tSpan < 1) tSpan = 1;
            tSpan = Math.min(tSpan, maxCols - tc);

            groups.push({
                title: tSlot.ref.text || ('Group ' + (groups.length + 1)),
                span: Math.max(1, tSpan),
            });
        }

        if (groups.length === 0) {
            groups = [{ title: 'Group 1', span: Math.max(1, maxCols - leadColumns) }];
        }

        var headers = [];
        for (var lh = 0; lh < leadColumns; lh++) {
            var leadHeaderSlot = topRow[lh];
            var leadHeader = (leadHeaderSlot && leadHeaderSlot.ref) ? leadHeaderSlot.ref.text : '';
            headers.push(leadHeader || (lh === 0 ? 'Model' : ('Column ' + (lh + 1))));
        }
        for (var hc = leadColumns; hc < maxCols; hc++) {
            var hSlot = lastHeaderRow[hc];
            var label = (hSlot && hSlot.ref) ? hSlot.ref.text : '';
            if (label === '') {
                var fallbackSlot = topRow[hc];
                label = (fallbackSlot && fallbackSlot.ref) ? fallbackSlot.ref.text : '';
            }
            headers.push(label || ('Column ' + (hc + 1)));
        }

        var rows = [];
        var rowMerges = [];
        for (var dr = headerRowCount; dr < grid.length; dr++) {
            var outRow = [];
            for (var dc = 0; dc < maxCols; dc++) {
                var dSlot = grid[dr][dc];
                var dValue = '';
                if (dSlot && dSlot.ref) {
                    if (dSlot.origin) {
                        dValue = dSlot.ref.text;
                    } else if (dSlot.ref.rowSpan > 1 && dSlot.ref.colSpan === 1) {
                        // Keep vertically merged values readable in the editable grid.
                        dValue = dSlot.ref.text;
                    }
                }
                outRow.push(dValue);

                if (dSlot && dSlot.origin && dSlot.ref) {
                    var dRowSpan = parseInt(dSlot.ref.rowSpan, 10);
                    if (isFinite(dRowSpan) && dRowSpan > 1) {
                        rowMerges.push({
                            row: dr - headerRowCount,
                            col: dc,
                            rowSpan: Math.min(dRowSpan, Math.max(1, grid.length - dr)),
                        });
                    }
                }
            }
            if (outRow.some(function(v) { return String(v || '').trim() !== ''; })) {
                rows.push(outRow);
            }
        }

        if (rows.length === 0) rows = [new Array(maxCols).fill('')];

        return {
            mode: 'grouped-pairs',
            leadColumns: leadColumns,
            headers: headers,
            groups: groups,
            rows: rows,
            merges: [],
            rowMerges: rowMerges,
            tableHtml: canonicalTableHtml,
        };
    }

    var standardHeaders = [];
    for (var sc = 0; sc < maxCols; sc++) {
        var stdHeaderSlot = lastHeaderRow[sc];
        var stdHeader = (stdHeaderSlot && stdHeaderSlot.ref) ? stdHeaderSlot.ref.text : '';
        standardHeaders.push(stdHeader || ('Column ' + (sc + 1)));
    }

    var standardRows = [];
    var standardMerges = [];
    var standardRowMerges = [];

    for (var sr = headerRowCount; sr < grid.length; sr++) {
        var srcRow = grid[sr] || [];
        var out = [];
        var outRowIndex = standardRows.length;

        for (var sCol = 0; sCol < maxCols; sCol++) {
            var sSlot = srcRow[sCol];
            var isOrigin = !!(sSlot && sSlot.origin && sSlot.ref);
            var sValue = '';
            if (sSlot && sSlot.ref) {
                if (isOrigin) {
                    sValue = sSlot.ref.text;
                } else if (sSlot.ref.rowSpan > 1 && sSlot.ref.colSpan === 1) {
                    sValue = sSlot.ref.text;
                }
            }
            out.push(sValue);

            if (isOrigin && sCol > 0) {
                var span = parseInt(sSlot.ref.colSpan, 10);
                if (isFinite(span) && span > 1) {
                    standardMerges.push({
                        row: outRowIndex,
                        col: sCol,
                        span: Math.min(span, maxCols - sCol),
                    });
                }
            }

            if (isOrigin) {
                var rowSpan = parseInt(sSlot.ref.rowSpan, 10);
                if (isFinite(rowSpan) && rowSpan > 1) {
                    standardRowMerges.push({
                        row: outRowIndex,
                        col: sCol,
                        rowSpan: Math.min(rowSpan, Math.max(1, (grid.length - headerRowCount) - outRowIndex)),
                    });
                }
            }
        }

        if (out.some(function(v) { return String(v || '').trim() !== ''; })) {
            standardRows.push(out);
        }
    }

    if (standardRows.length === 0) standardRows = [new Array(maxCols).fill('')];

    return {
        mode: 'standard',
        headers: standardHeaders,
        groups: [],
        rows: standardRows,
        merges: standardMerges,
        rowMerges: standardRowMerges,
        tableHtml: canonicalTableHtml,
    };
}

function applyPastedSpecHtml(rawHtml) {
    var parsed = parseClipboardHtmlTable(rawHtml);
    if (!parsed || !Array.isArray(parsed.headers) || parsed.headers.length < 1) return false;

    _specTableMode = parsed.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
    _specTableHeaders = parsed.headers.slice(0);
    _specTableRows = Array.isArray(parsed.rows) ? parsed.rows.slice(0) : [];
    _specTableGroups = Array.isArray(parsed.groups) ? parsed.groups.slice(0) : [];
    _specTableLeadColumns = Math.max(0, parseInt(parsed.leadColumns, 10) || 0);
    _specTableMerges = Array.isArray(parsed.merges) ? parsed.merges.slice(0) : [];
    _specTableRowMerges = Array.isArray(parsed.rowMerges) ? parsed.rowMerges.slice(0) : [];
    _specTableSourceHtml = String(parsed.tableHtml || '').trim();

    normalizeSpecTableState();
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();

    window.requestAnimationFrame(function() {
        focusSpecTableCell(0, 0);
    });

    return true;
}

function applySmartFullTablePaste(matrix, rowOffset, colOffset) {
    if (!Array.isArray(matrix) || matrix.length < 2) return false;
    if (rowOffset !== 0 || colOffset !== 0) return false;

    var maxCols = matrix.reduce(function(max, row) {
        return Math.max(max, Array.isArray(row) ? row.length : 0);
    }, 0);
    if (maxCols < 2) return false;

    function normalizeRow(row, width) {
        var out = Array.isArray(row) ? row.slice(0, width) : [];
        while (out.length < width) out.push('');
        return out.map(function(cell) { return String(cell || '').trim(); });
    }

    function nonEmptyCount(row) {
        var count = 0;
        for (var i = 0; i < row.length; i++) {
            if (String(row[i] || '').trim() !== '') count++;
        }
        return count;
    }

    function looksLikeGroupedHeader(rows, width) {
        if (!Array.isArray(rows) || rows.length < 3) return false;
        var top = normalizeRow(rows[0], width);
        var sub = normalizeRow(rows[1], width);

        var topNonEmpty = nonEmptyCount(top);
        var subNonEmpty = nonEmptyCount(sub);
        var topHasBlank = top.some(function(v) { return String(v || '').trim() === ''; });
        var subMostlyFilled = subNonEmpty >= Math.max(2, width - 1);

        return topHasBlank && topNonEmpty >= 2 && subMostlyFilled;
    }

    function applyStandardFromMatrix(rows, width) {
        var headerRow = normalizeRow(rows[0], width);
        var headers = headerRow.map(function(h, idx) {
            var label = String(h || '').trim();
            return label !== '' ? label : ('Column ' + (idx + 1));
        });

        var dataRows = rows.slice(1).map(function(row) {
            return normalizeRow(row, width).map(function(cell) { return String(cell || ''); });
        });
        if (dataRows.length === 0) dataRows = [new Array(width).fill('')];

        _specTableMode = 'standard';
        _specTableHeaders = headers;
        _specTableRows = dataRows;
        _specTableGroups = [];
        _specTableLeadColumns = 0;
        _specTableMerges = [];
        _specTableRowMerges = [];
        _specTableSourceHtml = '';

        normalizeSpecTableState();
        renderSpecTableBuilder();
        syncSpecificationsHiddenField();
        return true;
    }

    function applyGroupedFromMatrix(rows, width) {
        var top = normalizeRow(rows[0], width);
        var sub = normalizeRow(rows[1], width);

        var leadColumns = 0;
        if (String(top[0] || '').trim() !== '' && String(sub[0] || '').trim() !== '') {
            leadColumns = 0;
        }

        var groups = [];
        var c = leadColumns;
        while (c < width) {
            var title = String(top[c] || '').trim();
            var span = 1;

            if (title === '') {
                title = groups.length > 0 ? groups[groups.length - 1].title : ('Group ' + (groups.length + 1));
            }

            var next = c + 1;
            while (next < width) {
                var nextTitle = String(top[next] || '').trim();
                if (nextTitle !== '') break;
                span++;
                next++;
            }

            groups.push({
                title: title,
                span: span,
                rowSpan: false,
            });

            c += span;
        }

        if (groups.length === 0) {
            return applyStandardFromMatrix(rows.slice(1), width);
        }

        var headers = sub.map(function(h, idx) {
            var label = String(h || '').trim();
            if (label === '' && idx === 0) return String(top[0] || 'Model').trim() || 'Model';
            if (label === '') return 'Column ' + (idx + 1);
            return label;
        });

        var dataRows = rows.slice(2).map(function(row) {
            return normalizeRow(row, width).map(function(cell) { return String(cell || ''); });
        });
        if (dataRows.length === 0) dataRows = [new Array(width).fill('')];

        _specTableMode = 'grouped-pairs';
        _specTableLeadColumns = leadColumns;
        _specTableHeaders = headers;
        _specTableGroups = groups;
        _specTableRows = dataRows;
        _specTableMerges = [];
        _specTableRowMerges = [];
        _specTableSourceHtml = '';

        normalizeSpecTableState();
        renderSpecTableBuilder();
        syncSpecificationsHiddenField();
        return true;
    }

    if (looksLikeGroupedHeader(matrix, maxCols)) {
        return applyGroupedFromMatrix(matrix, maxCols);
    }

    return applyStandardFromMatrix(matrix, maxCols);
}

function parseTabularTextToMatrix(rawText) {
    var normalized = String(rawText || '').replace(/\r\n?/g, '\n');
    if (normalized.trim() === '') return [];

    var lines = normalized.split('\n');
    while (lines.length > 0 && String(lines[lines.length - 1] || '').trim() === '') lines.pop();
    if (lines.length === 0) return [];

    // Analyze first few lines to detect format
    var sampleLines = lines.slice(0, Math.min(3, lines.length));
    var hasTabs = sampleLines.some(function(line) { return line.indexOf('\t') !== -1; });
    var hasMultipleSpaces = sampleLines.some(function(line) { return /\s{2,}/.test(line); });
    
    var matrix = lines.map(function(line) {
        var trimmedLine = String(line || '').trim();
        
        // Priority 1: Tab-separated (Excel default)
        if (line.indexOf('\t') !== -1) {
            return line.split('\t').map(function(cell) { return String(cell || '').trim(); });
        }
        
        // Priority 2: Multiple spaces (fallback for copied tables)
        if (hasMultipleSpaces && /\s{2,}/.test(line)) {
            var cells = trimmedLine.split(/\s{2,}/);
            if (cells.length > 1) {
                return cells.map(function(cell) { return String(cell || '').trim(); });
            }
        }
        
        // If no separators found, return single cell
        return [trimmedLine];
    });

    return matrix;
}

function buildStandardMatrixPayloadFromTabText(rawText) {
    var matrix = parseTabularTextToMatrix(rawText);
    if (!Array.isArray(matrix) || matrix.length === 0) return null;

    var maxCols = matrix.reduce(function(max, row) {
        return Math.max(max, Array.isArray(row) ? row.length : 0);
    }, 0);
    if (maxCols < 1) return null;

    var normalizeRow = function(row) {
        var out = Array.isArray(row) ? row.slice(0, maxCols) : [];
        while (out.length < maxCols) out.push('');
        return out.map(function(cell) { return String(cell || '').trim(); });
    };

    var headers = normalizeRow(matrix[0]).map(function(h, idx) {
        return h !== '' ? h : ('Column ' + (idx + 1));
    });

    var rows = matrix.slice(1).map(normalizeRow);
    if (rows.length === 0) rows = [new Array(maxCols).fill('')];

    var hasAnyData = rows.some(function(row) {
        return row.some(function(cell) { return String(cell || '').trim() !== ''; });
    });
    if (!hasAnyData) return null;

    return {
        mode: 'standard',
        headers: headers,
        rows: rows,
        merges: [],
    };
}

function matrixPayloadToTabText(matrixRaw) {
    if (!matrixRaw || typeof matrixRaw !== 'object') return '';

    var headers = Array.isArray(matrixRaw.headers)
        ? matrixRaw.headers.map(function(h) { return String(h || '').trim(); })
        : [];
    var rows = Array.isArray(matrixRaw.rows) ? matrixRaw.rows : [];

    var width = Math.max(headers.length, rows.reduce(function(max, row) {
        return Math.max(max, Array.isArray(row) ? row.length : 0);
    }, 0));

    if (width < 1) return '';

    while (headers.length < width) headers.push('');

    var lines = [headers.join('\t')];
    rows.forEach(function(row) {
        var out = Array.isArray(row) ? row.slice(0, width) : [];
        while (out.length < width) out.push('');
        lines.push(out.map(function(cell) { return String(cell || '').trim(); }).join('\t'));
    });

    return lines.join('\n').trim();
}

function renderExcelLikePreviewTable(host, matrix) {
    if (!host || !matrix || !Array.isArray(matrix.headers)) return;

    var table = document.createElement('table');
    table.style.cssText = 'width:100%;border-collapse:collapse;table-layout:fixed;font-size:11px;';

    var thead = document.createElement('thead');
    var headerRow = document.createElement('tr');
    matrix.headers.forEach(function(header) {
        var th = document.createElement('th');
        th.textContent = String(header || '').trim();
        th.style.cssText = 'padding:4px 8px;border:1px solid #4c6382;background:#9fc1f0;color:#111827;font-weight:700;text-align:center;vertical-align:middle;word-break:break-word;';
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);
    table.appendChild(thead);

    var tbody = document.createElement('tbody');
    matrix.rows.forEach(function(row, rowIdx) {
        var tr = document.createElement('tr');
        row.forEach(function(cell) {
            var td = document.createElement('td');
            td.textContent = String(cell || '').trim();
            td.style.cssText = 'padding:4px 8px;border:1px solid #4c6382;background:' + (rowIdx % 2 === 0 ? '#a9c7f3' : '#b3cef5') + ';color:#111827;vertical-align:middle;word-break:break-word;white-space:normal;';
            tr.appendChild(td);
        });
        tbody.appendChild(tr);
    });
    table.appendChild(tbody);

    host.innerHTML = '';
    host.appendChild(table);
}

function normalizeSecondSpecTableState() {
    if (_specSecondTableMode !== 'standard') _specSecondTableMode = 'standard';

    if (!Array.isArray(_specSecondTableHeaders) || _specSecondTableHeaders.length < 1) {
        _specSecondTableHeaders = ['Parameter', 'Value'];
    }

    if (!Array.isArray(_specSecondTableRows) || _specSecondTableRows.length < 1) {
        _specSecondTableRows = [['', '']];
    }

    var colCount = Math.max(1, _specSecondTableHeaders.length);
    _specSecondTableHeaders = _specSecondTableHeaders.map(function(h, idx) {
        var value = String(h || '').trim();
        return value !== '' ? value : (idx === 0 ? 'Parameter' : (idx === 1 ? 'Value' : 'Column ' + (idx + 1)));
    });

    _specSecondTableRows = _specSecondTableRows.map(function(row) {
        var next = Array.isArray(row) ? row.slice(0) : [];
        while (next.length < colCount) next.push('');
        if (next.length > colCount) next = next.slice(0, colCount);
        return next.map(function(cell) { return String(cell || ''); });
    });

    if (_specSecondTableRows.length === 0) {
        _specSecondTableRows = [new Array(colCount).fill('')];
    }
}

function secondSpecTablePayload() {
    normalizeSecondSpecTableState();
    return {
        mode: 'standard',
        headers: _specSecondTableHeaders.map(function(h) { return String(h || '').trim(); }),
        rows: _specSecondTableRows.map(function(row) {
            return row.map(function(cell) { return String(cell || '').trim(); });
        }),
        merges: [],
    };
}

function syncSecondSpecTableHiddenField() {
    var textInput = document.getElementById('specSecondTableText');
    if (!textInput) return;
    textInput.value = matrixPayloadToTabText(secondSpecTablePayload());
}

function focusSecondSpecTableCell(rowIdx, colIdx) {
    var selector = '.spec-second-table-input[data-row="' + rowIdx + '"][data-col="' + colIdx + '"]';
    var input = document.querySelector(selector);
    if (!input) return false;

    input.focus();
    input.select();
    return true;
}

function autoResizeSecondSpecCell(el) {
    if (!el) return;
    el.style.height = 'auto';
    var nextHeight = Math.max(34, el.scrollHeight);
    el.style.height = nextHeight + 'px';
}

function ensureSecondSpecTableGridSize(minRows, minCols) {
    normalizeSecondSpecTableState();

    var targetRows = parseInt(minRows, 10);
    var targetCols = parseInt(minCols, 10);
    if (!isFinite(targetRows) || targetRows < 1) targetRows = 1;
    if (!isFinite(targetCols) || targetCols < 1) targetCols = 1;

    while (_specSecondTableHeaders.length < targetCols) {
        _specSecondTableHeaders.push('Column ' + (_specSecondTableHeaders.length + 1));
    }

    _specSecondTableRows = _specSecondTableRows.map(function(row) {
        var next = Array.isArray(row) ? row.slice(0) : [];
        while (next.length < _specSecondTableHeaders.length) next.push('');
        return next;
    });

    while (_specSecondTableRows.length < targetRows) {
        _specSecondTableRows.push(new Array(_specSecondTableHeaders.length).fill(''));
    }
}

function renderSecondSpecTableBuilder() {
    normalizeSecondSpecTableState();

    var modeSel = document.getElementById('specSecondTableMode');
    if (modeSel) modeSel.value = _specSecondTableMode;

    var head = document.getElementById('specSecondTableHead');
    var body = document.getElementById('specSecondTableBody');
    var wrap = document.getElementById('specSecondTableBuilderWrap');
    var help = document.getElementById('specSecondTableHelpText');
    if (!head || !body) {
        syncSecondSpecTableHiddenField();
        return;
    }

    if (wrap) {
        wrap.style.border = '1px solid #4b5563';
        wrap.style.background = 'linear-gradient(180deg,#101217 0%,#171b24 100%)';
        wrap.style.boxShadow = 'inset 0 0 0 1px rgba(255,255,255,0.03)';
    }
    if (help) {
        help.innerHTML = '<i class="bi bi-info-circle"></i> Copy from Excel, then click "PASTE TABLE".';
    }

    head.innerHTML = '';
    body.innerHTML = '';

    var headRow = document.createElement('tr');

    var indexHead = document.createElement('th');
    indexHead.style.cssText = 'width:42px;padding:8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:#0f131a;color:#cbd5e1;text-align:center;font-size:10px;font-weight:800;';
    indexHead.textContent = '#';
    headRow.appendChild(indexHead);

    _specSecondTableHeaders.forEach(function(header, colIdx) {
        var th = document.createElement('th');
        th.style.cssText = 'padding:8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:#11151d;vertical-align:middle;';

        var wrap = document.createElement('div');
        wrap.style.cssText = 'display:grid;grid-template-columns:auto 1fr auto;gap:6px;align-items:center;';

        var colLabel = document.createElement('span');
        colLabel.textContent = getSpecColumnLabel(colIdx);
        colLabel.style.cssText = 'display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border:1px solid #4b5563;border-radius:5px;background:#1f2530;color:#e5e7eb;font-size:10px;font-weight:800;';
        wrap.appendChild(colLabel);

        var input = document.createElement('input');
        input.type = 'text';
        input.value = header;
        input.placeholder = 'Column ' + (colIdx + 1);
        input.style.cssText = 'width:100%;min-width:110px;padding:7px 9px;border:1px solid #6b7280;border-radius:7px;font-size:11px;font-weight:700;color:#f3f4f6;background:#1f2530;';
        input.addEventListener('input', function() {
            _specSecondTableHasUserInput = true;
            _specSecondTableHeaders[colIdx] = this.value;
            syncSecondSpecTableHiddenField();
        });
        wrap.appendChild(input);

        if (_specSecondTableHeaders.length > 1) {
            var removeColBtn = document.createElement('button');
            removeColBtn.type = 'button';
            removeColBtn.innerHTML = '&times;';
            removeColBtn.title = 'Remove column';
            removeColBtn.style.cssText = 'width:24px;height:24px;border-radius:6px;border:1px solid #7f1d1d;background:#2a1014;color:#fca5a5;font-size:16px;line-height:1;cursor:pointer;flex-shrink:0;';
            removeColBtn.addEventListener('click', function() {
                removeSecondSpecTableColumn(colIdx);
            });
            wrap.appendChild(removeColBtn);
        }

        th.appendChild(wrap);
        headRow.appendChild(th);
    });

    var actionHead = document.createElement('th');
    actionHead.style.cssText = 'width:42px;padding:8px;border-bottom:1px solid #6b7280;background:#11151d;';
    headRow.appendChild(actionHead);
    head.appendChild(headRow);

    _specSecondTableRows.forEach(function(row, rowIdx) {
        var tr = document.createElement('tr');
        if (rowIdx % 2 === 1) tr.style.backgroundColor = 'rgba(255,255,255,0.02)';

        var idxTd = document.createElement('td');
        idxTd.style.cssText = 'padding:6px 8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:#0f131a;color:#cbd5e1;text-align:center;font-size:11px;font-weight:700;';
        idxTd.textContent = String(rowIdx + 1);
        tr.appendChild(idxTd);

        _specSecondTableHeaders.forEach(function(header, colIdx) {
            var td = document.createElement('td');
            td.style.cssText = 'padding:6px 8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:transparent;';

            var cellInput = document.createElement('textarea');
            cellInput.value = row[colIdx] || '';
            cellInput.className = 'spec-second-table-input';
            cellInput.setAttribute('data-row', String(rowIdx));
            cellInput.setAttribute('data-col', String(colIdx));
            cellInput.rows = 1;
            cellInput.style.cssText = 'width:100%;min-height:34px;padding:7px 9px;border:1px solid #6b7280;border-radius:7px;font-size:12px;line-height:1.45;color:#f3f4f6;background:#1f2530;resize:vertical;overflow:hidden;white-space:pre-wrap;';
            (function(r, c, inputEl) {
                inputEl.addEventListener('input', function() {
                    _specSecondTableHasUserInput = true;
                    _specSecondTableRows[r][c] = this.value;
                    autoResizeSecondSpecCell(this);
                    syncSecondSpecTableHiddenField();
                });
                inputEl.addEventListener('keydown', function(evt) {
                    handleSecondSpecCellKeyDown(evt, r, c);
                });
                inputEl.addEventListener('paste', function(evt) {
                    handleSecondSpecCellPaste(evt, r, c, inputEl);
                });
            })(rowIdx, colIdx, cellInput);

            autoResizeSecondSpecCell(cellInput);

            td.appendChild(cellInput);
            tr.appendChild(td);
        });

        var actionTd = document.createElement('td');
        actionTd.style.cssText = 'padding:6px 8px;border-bottom:1px solid #6b7280;text-align:center;background:transparent;';
        var removeRowBtn = document.createElement('button');
        removeRowBtn.type = 'button';
        removeRowBtn.innerHTML = '<i class="bi bi-trash"></i>';
        removeRowBtn.title = 'Remove row';
        removeRowBtn.style.cssText = 'width:28px;height:28px;border-radius:7px;border:1px solid #7f1d1d;background:#2a1014;color:#fca5a5;cursor:pointer;';
        (function(r) {
            removeRowBtn.addEventListener('click', function() {
                removeSecondSpecTableDataRow(r);
            });
        })(rowIdx);
        actionTd.appendChild(removeRowBtn);
        tr.appendChild(actionTd);

        body.appendChild(tr);
    });

    syncSecondSpecTableHiddenField();
}

function setSecondSpecTableMode(value, forceRender) {
    _specSecondTableMode = 'standard';
    var modeSel = document.getElementById('specSecondTableMode');
    if (modeSel) modeSel.value = _specSecondTableMode;
    if (forceRender !== false) renderSecondSpecTableBuilder();
}

function addSecondSpecTableColumn(label) {
    _specSecondTableHasUserInput = true;
    normalizeSecondSpecTableState();
    _specSecondTableHeaders.push(String(label || ('Column ' + (_specSecondTableHeaders.length + 1))));
    _specSecondTableRows = _specSecondTableRows.map(function(row) {
        row.push('');
        return row;
    });
    renderSecondSpecTableBuilder();
}

function addSecondSpecTableDataRow(initialValues) {
    _specSecondTableHasUserInput = true;
    normalizeSecondSpecTableState();
    var row = new Array(_specSecondTableHeaders.length).fill('');
    if (Array.isArray(initialValues)) {
        for (var i = 0; i < Math.min(initialValues.length, row.length); i++) {
            row[i] = String(initialValues[i] || '');
        }
    }
    _specSecondTableRows.push(row);
    renderSecondSpecTableBuilder();
}

function removeSecondSpecTableColumn(colIdx) {
    _specSecondTableHasUserInput = true;
    normalizeSecondSpecTableState();
    if (_specSecondTableHeaders.length <= 1) return;
    _specSecondTableHeaders.splice(colIdx, 1);
    _specSecondTableRows = _specSecondTableRows.map(function(row) {
        row.splice(colIdx, 1);
        return row;
    });
    renderSecondSpecTableBuilder();
}

function removeSecondSpecTableDataRow(rowIdx) {
    _specSecondTableHasUserInput = true;
    normalizeSecondSpecTableState();
    if (_specSecondTableRows.length <= 1) {
        _specSecondTableRows[0] = new Array(_specSecondTableHeaders.length).fill('');
    } else {
        _specSecondTableRows.splice(rowIdx, 1);
    }
    renderSecondSpecTableBuilder();
}

function clearSecondSpecTableValues() {
    _specSecondTableHasUserInput = true;
    normalizeSecondSpecTableState();
    _specSecondTableRows = [new Array(_specSecondTableHeaders.length).fill('')];
    renderSecondSpecTableBuilder();
}

function resetSecondSpecTableFresh() {
    _specSecondTableHasUserInput = true;
    _specSecondTableMode = 'standard';
    _specSecondTableHeaders = ['Parameter', 'Value'];
    _specSecondTableRows = [['', '']];
    renderSecondSpecTableBuilder();
}

function applySecondPastedSpecText(rawText, startRow, startCol) {
    var matrix = parseTabularTextToMatrix(rawText);
    if (!Array.isArray(matrix) || matrix.length === 0) return false;

    var looksTabular = matrix.length > 1 || (Array.isArray(matrix[0]) && matrix[0].length > 1);
    if (!looksTabular) return false;

    var maxCols = matrix.reduce(function(max, row) {
        return Math.max(max, row.length);
    }, 0);
    if (maxCols < 1) return false;

    var rowOffset = Math.max(0, parseInt(startRow, 10) || 0);
    var colOffset = Math.max(0, parseInt(startCol, 10) || 0);
    ensureSecondSpecTableGridSize(rowOffset + matrix.length, colOffset + maxCols);

    for (var r = 0; r < matrix.length; r++) {
        for (var c = 0; c < maxCols; c++) {
            _specSecondTableRows[rowOffset + r][colOffset + c] = String((matrix[r] && matrix[r][c]) || '');
        }
    }

    renderSecondSpecTableBuilder();
    window.requestAnimationFrame(function() {
        focusSecondSpecTableCell(rowOffset, colOffset);
    });

    return true;
}

function applySecondPastedSpecHtml(rawHtml, startRow, startCol) {
    var parsed = parseClipboardHtmlTable(rawHtml);
    if (!parsed || !Array.isArray(parsed.headers) || parsed.headers.length < 1) return false;

    var headers = parsed.headers.map(function(h, idx) {
        var label = String(h || '').trim();
        return label !== '' ? label : ('Column ' + (idx + 1));
    });

    var rows = Array.isArray(parsed.rows) ? parsed.rows.map(function(row) {
        var out = Array.isArray(row) ? row.slice(0, headers.length) : [];
        while (out.length < headers.length) out.push('');
        return out.map(function(cell) { return String(cell || ''); });
    }) : [];

    if (rows.length === 0) rows = [new Array(headers.length).fill('')];

    var rowOffset = Math.max(0, parseInt(startRow, 10) || 0);
    var colOffset = Math.max(0, parseInt(startCol, 10) || 0);

    if (rowOffset === 0 && colOffset === 0) {
        _specSecondTableMode = 'standard';
        _specSecondTableHeaders = headers;
        _specSecondTableRows = rows;
        _specSecondTableHasUserInput = true;
        renderSecondSpecTableBuilder();
        window.requestAnimationFrame(function() {
            focusSecondSpecTableCell(0, 0);
        });
        return true;
    }

    ensureSecondSpecTableGridSize(rowOffset + rows.length, colOffset + headers.length);
    for (var r = 0; r < rows.length; r++) {
        for (var c = 0; c < headers.length; c++) {
            _specSecondTableRows[rowOffset + r][colOffset + c] = String((rows[r] && rows[r][c]) || '');
        }
    }

    _specSecondTableHasUserInput = true;
    renderSecondSpecTableBuilder();
    window.requestAnimationFrame(function() {
        focusSecondSpecTableCell(rowOffset, colOffset);
    });

    return true;
}

function handleSecondSpecCellPaste(event, rowIdx, colIdx, inputEl) {
    var clipboard = event.clipboardData || window.clipboardData;
    var beforeValue = inputEl ? String(inputEl.value || '') : '';

    if (!clipboard) {
        setTimeout(function() {
            if (!inputEl) return;
            var afterValue = String(inputEl.value || '').trim();
            if (afterValue === '' || afterValue === String(beforeValue || '').trim()) return;
            if (applySecondPastedSpecText(afterValue, rowIdx, colIdx)) return;
            syncSecondSpecTableHiddenField();
        }, 0);
        return;
    }

    var html = '';
    try {
        html = clipboard.getData('text/html');
    } catch (e) {
        html = '';
    }

    if (html && applySecondPastedSpecHtml(html, rowIdx, colIdx)) {
        event.preventDefault();
        return;
    }

    var text = clipboard.getData('text');
    if (applySecondPastedSpecText(text, rowIdx, colIdx)) {
        event.preventDefault();
        return;
    }

    setTimeout(function() {
        if (!inputEl) return;
        var afterValue = String(inputEl.value || '').trim();
        if (afterValue === '' || afterValue === String(beforeValue || '').trim()) return;
        if (applySecondPastedSpecText(afterValue, rowIdx, colIdx)) return;
        syncSecondSpecTableHiddenField();
    }, 0);
}

function handleSecondSpecCellKeyDown(event, rowIdx, colIdx) {
    var nextRow = rowIdx;
    var nextCol = colIdx;
    var handled = false;

    if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
        handled = true;
        nextRow = event.shiftKey ? rowIdx - 1 : rowIdx + 1;
    } else if (event.key === 'ArrowDown' && event.altKey) {
        handled = true;
        nextRow = rowIdx + 1;
    } else if (event.key === 'ArrowUp' && event.altKey) {
        handled = true;
        nextRow = rowIdx - 1;
    } else if (event.key === 'Tab') {
        handled = true;
        if (event.shiftKey) {
            if (colIdx > 0) {
                nextCol = colIdx - 1;
            } else {
                nextRow = Math.max(0, rowIdx - 1);
                nextCol = Math.max(0, _specSecondTableHeaders.length - 1);
            }
        } else if (colIdx < _specSecondTableHeaders.length - 1) {
            nextCol = colIdx + 1;
        } else {
            nextRow = rowIdx + 1;
            nextCol = 0;
        }
    }

    if (!handled) return;

    event.preventDefault();
    if (nextRow < 0) nextRow = 0;
    if (nextCol < 0) nextCol = 0;

    ensureSecondSpecTableGridSize(nextRow + 1, nextCol + 1);
    renderSecondSpecTableBuilder();
    window.requestAnimationFrame(function() {
        focusSecondSpecTableCell(nextRow, nextCol);
    });
}

function convertSecondSpecificationsTextToTable() {
    var input = document.getElementById('specSecondTableText');
    var raw = input ? String(input.value || '').trim() : '';

    if (raw === '') {
        renderSecondSpecTableBuilder();
        return;
    }

    var matrix = buildStandardMatrixPayloadFromTabText(raw);
    if (!matrix) {
        customAlert('No table data detected in the second table text.');
        return;
    }

    _specSecondTableMode = 'standard';
    _specSecondTableHeaders = matrix.headers.slice(0);
    _specSecondTableRows = matrix.rows.map(function(row) { return row.slice(0); });
    _specSecondTableHasUserInput = true;
    renderSecondSpecTableBuilder();
}

function setSecondSpecificationsEditor(rawText) {
    var raw = String(rawText || '').trim();
    _specSecondTableMode = 'standard';
    var addBtn = document.getElementById('specAddSecondTableBtn');
    var container = document.getElementById('specSecondTableContainer');

    if (raw === '') {
        _specSecondTableHeaders = ['Parameter', 'Value'];
        _specSecondTableRows = [['', '']];
        _specSecondTableHasUserInput = false;
        if (addBtn) addBtn.style.display = 'inline-flex';
        if (container) container.style.display = 'none';
        renderSecondSpecTableBuilder();
        return;
    }

    var matrix = buildStandardMatrixPayloadFromTabText(raw);
    if (matrix) {
        _specSecondTableHeaders = matrix.headers.slice(0);
        _specSecondTableRows = matrix.rows.map(function(row) { return row.slice(0); });
        _specSecondTableHasUserInput = true;
    } else {
        _specSecondTableHeaders = ['Parameter', 'Value'];
        _specSecondTableRows = [['', '']];
        _specSecondTableHasUserInput = false;
    }

    if (addBtn) addBtn.style.display = 'none';
    if (container) container.style.display = 'block';

    renderSecondSpecTableBuilder();
}

function showSecondTableEditor() {
    var addBtn = document.getElementById('specAddSecondTableBtn');
    var container = document.getElementById('specSecondTableContainer');

    if (addBtn) addBtn.style.display = 'none';
    if (container) container.style.display = 'block';

    renderSecondSpecTableBuilder();
    window.requestAnimationFrame(function() {
        focusSecondSpecTableCell(0, 0);
    });
}

function showSecondTableTextInput() {
    showSecondTableEditor();
}

function openSecondTablePasteDialog(startRow, startCol, initialText) {
    var existing = document.getElementById('specSecondPasteDialog');
    if (existing && existing.parentNode) existing.parentNode.removeChild(existing);

    var overlay = document.createElement('div');
    overlay.id = 'specSecondPasteDialog';
    overlay.style.cssText = 'position:fixed;inset:0;z-index:100000;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,0.45);padding:16px;';

    var card = document.createElement('div');
    card.style.cssText = 'width:min(720px,95vw);background:#fff;border:1px solid #dbe1ea;border-radius:12px;box-shadow:0 20px 40px rgba(15,23,42,0.25);padding:12px;';

    var title = document.createElement('div');
    title.style.cssText = 'font-size:13px;font-weight:800;color:#1f2937;margin-bottom:8px;';
    title.textContent = 'Paste Second Table Data';

    var note = document.createElement('div');
    note.style.cssText = 'font-size:11px;color:#64748b;margin-bottom:8px;';
    note.textContent = 'Paste from Google Docs/Excel here, then click Apply.';

    var textarea = document.createElement('textarea');
    textarea.style.cssText = 'width:100%;min-height:220px;padding:8px 10px;border:1.5px solid #dbe1ea;border-radius:8px;font-size:11px;font-family:Consolas,Courier New,monospace;resize:vertical;background:#fff;color:#111827;';
    textarea.placeholder = 'Paste tabular data here...';
    textarea.value = String(initialText || '');

    var actions = document.createElement('div');
    actions.style.cssText = 'display:flex;justify-content:flex-end;gap:8px;margin-top:10px;';

    var cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = 'padding:7px 12px;border-radius:8px;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;font-size:11px;font-weight:700;cursor:pointer;';

    var applyBtn = document.createElement('button');
    applyBtn.type = 'button';
    applyBtn.textContent = 'Apply Paste';
    applyBtn.style.cssText = 'padding:7px 12px;border-radius:8px;border:1px solid #93c5fd;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:800;cursor:pointer;';

    function closeDialog() {
        if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
    }

    cancelBtn.addEventListener('click', closeDialog);
    overlay.addEventListener('click', function(evt) {
        if (evt.target === overlay) closeDialog();
    });

    applyBtn.addEventListener('click', function() {
        var raw = String(textarea.value || '').trim();
        if (raw === '') {
            customAlert('Paste table text first.');
            textarea.focus();
            return;
        }

        if (!applySecondPastedSpecText(raw, startRow, startCol)) {
            customAlert('Could not detect tabular data. Try copying directly from the table in Docs.');
            textarea.focus();
            return;
        }

        closeDialog();
    });

    actions.appendChild(cancelBtn);
    actions.appendChild(applyBtn);
    card.appendChild(title);
    card.appendChild(note);
    card.appendChild(textarea);
    card.appendChild(actions);
    overlay.appendChild(card);
    document.body.appendChild(overlay);

    window.requestAnimationFrame(function() {
        textarea.focus();
        if (textarea.value) textarea.select();
    });
}

function pasteExcelIntoSecondSpecTable() {
    showSecondTableEditor();
    var active = document.activeElement;
    var startRow = 0;
    var startCol = 0;

    if (active && active.classList && active.classList.contains('spec-second-table-input')) {
        startRow = Math.max(0, parseInt(active.getAttribute('data-row'), 10) || 0);
        startCol = Math.max(0, parseInt(active.getAttribute('data-col'), 10) || 0);
    }

    if (navigator.clipboard && navigator.clipboard.read) {
        navigator.clipboard.read().then(function(items) {
            if (Array.isArray(items)) {
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    if (item && item.types && item.types.indexOf('text/html') !== -1) {
                        item.getType('text/html').then(function(blob) {
                            return blob.text();
                        }).then(function(html) {
                            if (applySecondPastedSpecHtml(html, startRow, startCol)) return;
                            if (navigator.clipboard && navigator.clipboard.readText) {
                                navigator.clipboard.readText().then(function(text) {
                                    if (applySecondPastedSpecText(text, startRow, startCol)) return;
                                    openSecondTablePasteDialog(startRow, startCol, text);
                                }).catch(function() {
                                    openSecondTablePasteDialog(startRow, startCol, '');
                                });
                                return;
                            }
                            openSecondTablePasteDialog(startRow, startCol, '');
                        }).catch(function() {
                            openSecondTablePasteDialog(startRow, startCol, '');
                        });
                        return;
                    }
                }
            }

            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    if (applySecondPastedSpecText(text, startRow, startCol)) return;
                    openSecondTablePasteDialog(startRow, startCol, text);
                }).catch(function() {
                    openSecondTablePasteDialog(startRow, startCol, '');
                });
                return;
            }

            openSecondTablePasteDialog(startRow, startCol, '');
        }).catch(function() {
            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    if (applySecondPastedSpecText(text, startRow, startCol)) return;
                    openSecondTablePasteDialog(startRow, startCol, text);
                }).catch(function() {
                    openSecondTablePasteDialog(startRow, startCol, '');
                });
                return;
            }

            openSecondTablePasteDialog(startRow, startCol, '');
        });
        return;
    }

    if (navigator.clipboard && navigator.clipboard.readText) {
        navigator.clipboard.readText().then(function(text) {
            if (applySecondPastedSpecText(text, startRow, startCol)) return;
            openSecondTablePasteDialog(startRow, startCol, text);
        }).catch(function() {
            openSecondTablePasteDialog(startRow, startCol, '');
        });
        return;
    }

    openSecondTablePasteDialog(startRow, startCol, '');
}

function applyPastedSpecText(rawText, startRow, startCol) {
    var matrix = parseTabularTextToMatrix(rawText);
    if (!Array.isArray(matrix) || matrix.length === 0) return false;

    var looksTabular = matrix.length > 1 || (Array.isArray(matrix[0]) && matrix[0].length > 1);
    if (!looksTabular) return false;

    var maxCols = matrix.reduce(function(max, row) {
        return Math.max(max, row.length);
    }, 0);
    if (maxCols < 1) return false;

    var rowOffset = Math.max(0, parseInt(startRow, 10) || 0);
    var colOffset = Math.max(0, parseInt(startCol, 10) || 0);

    // Smart full-table replace when pasting from top-left.
    // This lets users paste varying table formats without manual header setup.
    if (applySmartFullTablePaste(matrix, rowOffset, colOffset)) {
        window.requestAnimationFrame(function() {
            focusSpecTableCell(0, 0);
        });
        return true;
    }

    ensureSpecTableGridSize(rowOffset + matrix.length, colOffset + maxCols);
    _specTableMerges = [];
    _specTableRowMerges = [];
    _specTableSourceHtml = '';

    if (_specTableMode === 'grouped-pairs') {
        var lead = getGroupedLeadColumnCount();
        if (lead >= _specTableHeaders.length) {
            lead = Math.max(1, _specTableHeaders.length - 1);
            _specTableLeadColumns = lead;
        }

        var dataCols = Math.max(1, _specTableHeaders.length - lead);
        _specTableGroups = normalizeGroupedGroups(_specTableGroups, dataCols);
    }

    for (var r = 0; r < matrix.length; r++) {
        for (var c = 0; c < maxCols; c++) {
            _specTableRows[rowOffset + r][colOffset + c] = String((matrix[r] && matrix[r][c]) || '');
        }
    }

    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
    window.requestAnimationFrame(function() {
        focusSpecTableCell(rowOffset, colOffset);
    });

    return true;
}

function buildSpecTableFromQuickPaste() {
    _specTableHasUserInput = true;
    var input = document.getElementById('specQuickPasteInput');
    if (!input) return;

    var raw = String(input.value || '');
    if (raw.trim() === '') {
        customAlert('Paste table text first, then click Build Table.');
        return;
    }

    var matrix = parseTabularTextToMatrix(raw);
    if (!Array.isArray(matrix) || matrix.length === 0) {
        customAlert('No table data detected in the paste box.');
        return;
    }

    if (applySmartFullTablePaste(matrix, 0, 0)) {
        customAlert('Table built successfully.');
        window.requestAnimationFrame(function() {
            focusSpecTableCell(0, 0);
        });
        return;
    }

    if (applyPastedSpecText(raw, 0, 0)) {
        customAlert('Table built successfully.');
        window.requestAnimationFrame(function() {
            focusSpecTableCell(0, 0);
        });
        return;
    }

    customAlert('Could not detect a valid table. Try pasting tab-separated data from Excel.');
}

function clearSpecQuickPasteInput() {
    var input = document.getElementById('specQuickPasteInput');
    if (!input) return;
    input.value = '';
    input.focus();
}

function handleSpecCellPaste(event, rowIdx, colIdx) {
    var clipboard = event.clipboardData || window.clipboardData;
    var beforeValue = event.target ? String(event.target.value || '') : '';

    if (!clipboard) {
        setTimeout(function() {
            var inputEl = event.target;
            if (!inputEl) return;
            var afterValue = String(inputEl.value || '').trim();
            if (afterValue === '' || afterValue === String(beforeValue || '').trim()) return;
            if (applyPastedSpecText(afterValue, rowIdx, colIdx)) return;
            syncSpecificationsHiddenField();
        }, 0);
        return;
    }

    var html = '';
    try {
        html = clipboard.getData('text/html');
    } catch (e) {
        html = '';
    }

    if (html && applyPastedSpecHtml(html, rowIdx, colIdx)) {
        event.preventDefault();
        return;
    }

    var text = clipboard.getData('text');
    if (applyPastedSpecText(text, rowIdx, colIdx)) {
        event.preventDefault();
        return;
    }

    setTimeout(function() {
        var inputEl = event.target;
        if (!inputEl) return;
        var afterValue = String(inputEl.value || '').trim();
        if (afterValue === '' || afterValue === String(beforeValue || '').trim()) return;
        if (applyPastedSpecText(afterValue, rowIdx, colIdx)) return;
        syncSpecificationsHiddenField();
    }, 0);
}

function handleSpecCellKeyDown(event, rowIdx, colIdx) {
    if (_specTableMode !== 'standard') return;

    var nextRow = rowIdx;
    var nextCol = colIdx;
    var handled = false;

    // Keep Enter for newline inside multiline cells.
    // Use Ctrl+Enter (or Cmd+Enter) to move rows.
    if (event.key === 'Enter' && (event.ctrlKey || event.metaKey)) {
        handled = true;
        nextRow = event.shiftKey ? rowIdx - 1 : rowIdx + 1;
    } else if (event.key === 'ArrowDown' && event.altKey) {
        handled = true;
        nextRow = rowIdx + 1;
    } else if (event.key === 'ArrowUp' && event.altKey) {
        handled = true;
        nextRow = rowIdx - 1;
    } else if (event.key === 'Tab') {
        handled = true;
        if (event.shiftKey) {
            if (colIdx > 0) {
                nextCol = colIdx - 1;
            } else {
                nextRow = Math.max(0, rowIdx - 1);
                nextCol = Math.max(0, _specTableHeaders.length - 1);
            }
        } else if (colIdx < _specTableHeaders.length - 1) {
            nextCol = colIdx + 1;
        } else {
            nextRow = rowIdx + 1;
            nextCol = 0;
        }
    }

    if (!handled) return;

    event.preventDefault();
    if (nextRow < 0) nextRow = 0;
    if (nextCol < 0) nextCol = 0;

    ensureSpecTableGridSize(nextRow + 1, nextCol + 1);
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();

    window.requestAnimationFrame(function() {
        focusSpecTableCell(nextRow, nextCol);
    });
}

function pasteExcelIntoSpecTable(forceTopLeft) {
    _specTableHasUserInput = true;

    var active = document.activeElement;
    var startRow = 0;
    var startCol = 0;

    if (!forceTopLeft && active && active.classList && active.classList.contains('spec-table-input')) {
        startRow = Math.max(0, parseInt(active.getAttribute('data-row'), 10) || 0);
        startCol = Math.max(0, parseInt(active.getAttribute('data-col'), 10) || 0);
    }

    if (navigator.clipboard && navigator.clipboard.read) {
        navigator.clipboard.read().then(function(items) {
            if (Array.isArray(items)) {
                for (var i = 0; i < items.length; i++) {
                    var item = items[i];
                    if (item && item.types && item.types.indexOf('text/html') !== -1) {
                        item.getType('text/html').then(function(blob) {
                            return blob.text();
                        }).then(function(html) {
                            if (applyPastedSpecHtml(html, startRow, startCol)) return;
                            if (navigator.clipboard && navigator.clipboard.readText) {
                                navigator.clipboard.readText().then(function(text) {
                                    if (applyPastedSpecText(text, startRow, startCol)) return;
                                    openSpecTablePasteDialog(startRow, startCol, text);
                                }).catch(function() {
                                    openSpecTablePasteDialog(startRow, startCol, '');
                                });
                                return;
                            }
                            openSpecTablePasteDialog(startRow, startCol, '');
                        }).catch(function() {
                            openSpecTablePasteDialog(startRow, startCol, '');
                        });
                        return;
                    }
                }
            }

            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    if (applyPastedSpecText(text, startRow, startCol)) return;
                    openSpecTablePasteDialog(startRow, startCol, text);
                }).catch(function() {
                    openSpecTablePasteDialog(startRow, startCol, '');
                });
                return;
            }

            openSpecTablePasteDialog(startRow, startCol, '');
        }).catch(function() {
            if (navigator.clipboard && navigator.clipboard.readText) {
                navigator.clipboard.readText().then(function(text) {
                    if (applyPastedSpecText(text, startRow, startCol)) return;
                    openSpecTablePasteDialog(startRow, startCol, text);
                }).catch(function() {
                    openSpecTablePasteDialog(startRow, startCol, '');
                });
                return;
            }

            openSpecTablePasteDialog(startRow, startCol, '');
        });
        return;
    }

    if (navigator.clipboard && navigator.clipboard.readText) {
        navigator.clipboard.readText().then(function(text) {
            if (applyPastedSpecText(text, startRow, startCol)) return;
            openSpecTablePasteDialog(startRow, startCol, text);
        }).catch(function() {
            openSpecTablePasteDialog(startRow, startCol, '');
        });
        return;
    }

    openSpecTablePasteDialog(startRow, startCol, '');
}

function openSpecTablePasteDialog(startRow, startCol, initialText) {
    var existing = document.getElementById('specPasteDialog');
    if (existing && existing.parentNode) existing.parentNode.removeChild(existing);

    var overlay = document.createElement('div');
    overlay.id = 'specPasteDialog';
    overlay.style.cssText = 'position:fixed;inset:0;z-index:100000;display:flex;align-items:center;justify-content:center;background:rgba(15,23,42,0.45);padding:16px;';

    var card = document.createElement('div');
    card.style.cssText = 'width:min(720px,95vw);background:#fff;border:1px solid #dbe1ea;border-radius:12px;box-shadow:0 20px 40px rgba(15,23,42,0.25);padding:12px;';

    var title = document.createElement('div');
    title.style.cssText = 'font-size:13px;font-weight:800;color:#1f2937;margin-bottom:8px;';
    title.textContent = 'Paste Table Data';

    var note = document.createElement('div');
    note.style.cssText = 'font-size:11px;color:#64748b;margin-bottom:8px;';
    note.textContent = 'Paste from Google Docs/Excel here, then click Apply.';

    var textarea = document.createElement('textarea');
    textarea.style.cssText = 'width:100%;min-height:220px;padding:8px 10px;border:1.5px solid #dbe1ea;border-radius:8px;font-size:11px;font-family:Consolas,Courier New,monospace;resize:vertical;background:#fff;color:#111827;';
    textarea.placeholder = 'Paste tabular data here...';
    textarea.value = String(initialText || '');

    var actions = document.createElement('div');
    actions.style.cssText = 'display:flex;justify-content:flex-end;gap:8px;margin-top:10px;';

    var cancelBtn = document.createElement('button');
    cancelBtn.type = 'button';
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = 'padding:7px 12px;border-radius:8px;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;font-size:11px;font-weight:700;cursor:pointer;';

    var applyBtn = document.createElement('button');
    applyBtn.type = 'button';
    applyBtn.textContent = 'Apply Paste';
    applyBtn.style.cssText = 'padding:7px 12px;border-radius:8px;border:1px solid #93c5fd;background:#eff6ff;color:#1d4ed8;font-size:11px;font-weight:800;cursor:pointer;';

    function closeDialog() {
        if (overlay && overlay.parentNode) overlay.parentNode.removeChild(overlay);
    }

    cancelBtn.addEventListener('click', closeDialog);
    overlay.addEventListener('click', function(evt) {
        if (evt.target === overlay) closeDialog();
    });

    applyBtn.addEventListener('click', function() {
        var raw = String(textarea.value || '').trim();
        if (raw === '') {
            customAlert('Paste table text first.');
            textarea.focus();
            return;
        }

        if (!applyPastedSpecText(raw, startRow, startCol)) {
            customAlert('Could not detect tabular data. Try copying directly from Excel or Docs.');
            textarea.focus();
            return;
        }

        closeDialog();
    });

    actions.appendChild(cancelBtn);
    actions.appendChild(applyBtn);
    card.appendChild(title);
    card.appendChild(note);
    card.appendChild(textarea);
    card.appendChild(actions);
    overlay.appendChild(card);
    document.body.appendChild(overlay);

    window.requestAnimationFrame(function() {
        textarea.focus();
        if (textarea.value) textarea.select();
    });
}

function updateSpecTableModeUi() {
    var modeSel = document.getElementById('specTableMode');
    if (modeSel) modeSel.value = _specTableMode;

    var tableWrap = document.getElementById('specTableBuilderWrap');
    if (tableWrap) {
        if (_specTableMode === 'grouped-pairs') {
            tableWrap.style.border = '1px solid #e5e7eb';
            tableWrap.style.background = '#fff';
            tableWrap.style.boxShadow = 'none';
        } else {
            tableWrap.style.border = '1px solid #4b5563';
            tableWrap.style.background = 'linear-gradient(180deg,#101217 0%,#171b24 100%)';
            tableWrap.style.boxShadow = 'inset 0 0 0 1px rgba(255,255,255,0.03)';
        }
    }

    var addColBtn = document.getElementById('specAddColumnBtn');
    var addSingleColBtn = document.getElementById('specAddSingleColumnBtn');
    var toggleGroupBtn = document.getElementById('specToggleGroupHeaderBtn');
    if (addColBtn) {
        if (_specTableMode === 'grouped-pairs') {
            addColBtn.innerHTML = '<i class="bi bi-layout-three-columns"></i> Add Group';
            if (addSingleColBtn) addSingleColBtn.style.display = 'inline-flex';
            if (toggleGroupBtn) {
                toggleGroupBtn.style.display = 'inline-flex';
                toggleGroupBtn.innerHTML = _specShowGroupHeaderControls
                    ? '<i class="bi bi-sliders"></i> Hide Group Settings'
                    : '<i class="bi bi-sliders"></i> Show Group Settings';
            }
        } else {
            addColBtn.innerHTML = '<i class="bi bi-layout-three-columns"></i> Add Column';
            if (addSingleColBtn) addSingleColBtn.style.display = 'none';
            if (toggleGroupBtn) toggleGroupBtn.style.display = 'none';
        }
    }

    var help = document.getElementById('specTableHelpText');
    if (help) {
        if (_specTableMode === 'grouped-pairs') {
            help.innerHTML = '<i class="bi bi-info-circle"></i> Copy from Excel, then click "PASTE TABLE".';
        } else {
            help.innerHTML = '<i class="bi bi-info-circle"></i> Copy from Excel, then click "PASTE TABLE".';
        }
    }
}

function applySpecBuilderAutoSizing() {
    var table = document.getElementById('specTableBuilder');
    if (!table) return;

    var oldGroups = table.querySelectorAll('colgroup[data-spec-builder-colgroup="1"]');
    oldGroups.forEach(function(cg) {
        if (cg && cg.parentNode === table) table.removeChild(cg);
    });

    var dataColCount = Array.isArray(_specTableHeaders) ? _specTableHeaders.length : 0;
    if (dataColCount < 1) return;

    var colgroup = document.createElement('colgroup');
    colgroup.setAttribute('data-spec-builder-colgroup', '1');

    // Standard mode has row-index column at left.
    if (_specTableMode === 'standard') {
        var idxCol = document.createElement('col');
        idxCol.style.width = '46px';
        colgroup.appendChild(idxCol);
    }

    var charSizes = new Array(dataColCount).fill(10);

    for (var h = 0; h < dataColCount; h++) {
        var hText = String(_specTableHeaders[h] || '').trim();
        var hLen = hText.length;
        if (hLen > charSizes[h]) charSizes[h] = hLen;
    }

    for (var r = 0; r < _specTableRows.length; r++) {
        var row = Array.isArray(_specTableRows[r]) ? _specTableRows[r] : [];
        for (var c = 0; c < dataColCount; c++) {
            var cell = String(row[c] || '');
            var lines = cell.split(/\r?\n/);
            var longest = 0;
            for (var li = 0; li < lines.length; li++) {
                var len = String(lines[li] || '').trim().length;
                if (len > longest) longest = len;
            }
            if (longest > charSizes[c]) charSizes[c] = longest;
        }
    }

    for (var i = 0; i < dataColCount; i++) {
        var col = document.createElement('col');
        var chars = Math.max(8, Math.min(56, charSizes[i]));
        var width = Math.round(chars * 6.6 + 30);
        if (width < 88) width = 88;
        if (width > 260) width = 260;
        col.style.width = width + 'px';
        colgroup.appendChild(col);
    }

    // Right-side actions column.
    var actionCol = document.createElement('col');
    actionCol.style.width = '44px';
    colgroup.appendChild(actionCol);

    table.insertBefore(colgroup, table.firstChild);
    table.style.width = 'max-content';
    table.style.minWidth = '100%';
    table.style.tableLayout = 'fixed';
}

function renderGroupedHeaderControls() {
    var wrap = document.getElementById('specGroupHeaderWrap');
    if (!wrap) return;

    if (_specTableMode !== 'grouped-pairs' || !_specShowGroupHeaderControls) {
        wrap.style.display = 'none';
        wrap.innerHTML = '';
        return;
    }

    wrap.style.display = 'block';
    wrap.innerHTML = '';

    var title = document.createElement('div');
    title.style.cssText = 'font-size:11px;font-weight:700;color:#1e3a8a;margin-bottom:6px;';
    title.textContent = 'Group Headers';
    wrap.appendChild(title);

    var leadRow = document.createElement('div');
    leadRow.style.cssText = 'display:grid;grid-template-columns:140px 100px 1fr;gap:8px;align-items:center;margin-bottom:8px;';
    var leadLabel = document.createElement('div');
    leadLabel.style.cssText = 'font-size:10px;font-weight:700;color:#1e40af;';
    leadLabel.textContent = 'Row-merged columns';
    var leadInput = document.createElement('input');
    leadInput.type = 'number';
    leadInput.min = '0';
    leadInput.max = '12';
    leadInput.value = String(getGroupedLeadColumnCount());
    leadInput.style.cssText = 'padding:6px 8px;border:1.5px solid #bfdbfe;border-radius:7px;font-size:11px;text-align:center;';
    leadInput.addEventListener('change', function() {
        _specTableSourceHtml = '';
        var nextLead = parseInt(this.value, 10);
        if (!isFinite(nextLead) || nextLead < 0) nextLead = 0;
        _specTableLeadColumns = nextLead;
        normalizeSpecTableState();
        renderSpecTableBuilder();
        syncSpecificationsHiddenField();
    });
    var leadHelp = document.createElement('div');
    leadHelp.style.cssText = 'font-size:10px;color:#6b7280;';
    leadHelp.textContent = 'Set how many first columns stay merged vertically across 2 header rows.';
    leadRow.appendChild(leadLabel);
    leadRow.appendChild(leadInput);
    leadRow.appendChild(leadHelp);
    wrap.appendChild(leadRow);

    for (var i = 0; i < _specTableGroups.length; i++) {
        (function(groupIdx) {
            var row = document.createElement('div');
            row.style.cssText = 'display:grid;grid-template-columns:72px 1fr 84px 120px auto;gap:8px;align-items:center;margin-bottom:6px;';

            var label = document.createElement('div');
            label.style.cssText = 'font-size:10px;font-weight:700;color:#1e40af;';
            label.textContent = 'Group ' + (groupIdx + 1);

            var input = document.createElement('input');
            input.type = 'text';
            input.value = String((_specTableGroups[groupIdx] && _specTableGroups[groupIdx].title) || '');
            input.placeholder = 'Group title';
            input.style.cssText = 'padding:6px 8px;border:1.5px solid #bfdbfe;border-radius:7px;font-size:11px;';
            input.addEventListener('input', function() {
                if (!_specTableGroups[groupIdx]) return;
                _specTableGroups[groupIdx].title = this.value;
                syncSpecificationsHiddenField();
            });
            input.addEventListener('blur', function() {
                renderSpecTableBuilder();
            });

            var spanInput = document.createElement('input');
            spanInput.type = 'number';
            spanInput.min = '1';
            spanInput.max = '12';
            spanInput.value = String((_specTableGroups[groupIdx] && _specTableGroups[groupIdx].span) || 1);
            spanInput.title = 'Merged columns in this group';
            spanInput.style.cssText = 'padding:6px 8px;border:1.5px solid #bfdbfe;border-radius:7px;font-size:11px;text-align:center;';
            spanInput.addEventListener('change', function() {
                setSpecTableGroupSpan(groupIdx, this.value);
            });

            var rowSpanWrap = document.createElement('label');
            rowSpanWrap.style.cssText = 'display:flex;align-items:center;gap:6px;font-size:10px;color:#1f2937;font-weight:700;white-space:nowrap;';
            var rowSpanCheck = document.createElement('input');
            rowSpanCheck.type = 'checkbox';
            rowSpanCheck.checked = !!(_specTableGroups[groupIdx] && _specTableGroups[groupIdx].rowSpan);
            rowSpanCheck.disabled = (parseInt((_specTableGroups[groupIdx] && _specTableGroups[groupIdx].span) || 1, 10) !== 1);
            rowSpanCheck.addEventListener('change', function() {
                if (!_specTableGroups[groupIdx]) return;
                _specTableSourceHtml = '';
                _specTableGroups[groupIdx].rowSpan = !!this.checked;
                renderSpecTableBuilder();
                syncSpecificationsHiddenField();
            });
            rowSpanWrap.appendChild(rowSpanCheck);
            rowSpanWrap.appendChild(document.createTextNode('Row merge'));

            var removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '&times;';
            removeBtn.title = 'Remove group';
            removeBtn.style.cssText = 'width:24px;height:24px;border-radius:6px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:16px;line-height:1;cursor:pointer;';
            removeBtn.addEventListener('click', function() {
                removeSpecTableGroup(groupIdx);
            });

            row.appendChild(label);
            row.appendChild(input);
            row.appendChild(spanInput);
            row.appendChild(rowSpanWrap);
            row.appendChild(removeBtn);
            wrap.appendChild(row);
        })(i);
    }

    var mergeTitle = document.createElement('div');
    mergeTitle.style.cssText = 'font-size:11px;font-weight:700;color:#1e3a8a;margin:10px 0 6px;';
    mergeTitle.textContent = 'Body Row Merges';
    wrap.appendChild(mergeTitle);

    var mergeHelp = document.createElement('div');
    mergeHelp.style.cssText = 'font-size:10px;color:#6b7280;margin-bottom:6px;';
    mergeHelp.textContent = 'Use this for values like CGA that should span multiple rows (e.g. span 4 rows).';
    wrap.appendChild(mergeHelp);

    if (_specTableRowMerges.length === 0) {
        var noMerge = document.createElement('div');
        noMerge.style.cssText = 'font-size:10px;color:#9ca3af;margin-bottom:6px;';
        noMerge.textContent = 'No body row merges yet.';
        wrap.appendChild(noMerge);
    }

    _specTableRowMerges.forEach(function(m, idx) {
        var row = document.createElement('div');
        row.style.cssText = 'display:grid;grid-template-columns:130px 92px 92px auto;gap:8px;align-items:center;margin-bottom:6px;';

        var colSel = document.createElement('select');
        colSel.style.cssText = 'padding:6px 8px;border:1.5px solid #bfdbfe;border-radius:7px;font-size:11px;';
        _specTableHeaders.forEach(function(h, cIdx) {
            var opt = document.createElement('option');
            opt.value = String(cIdx);
            var name = String(h || '').trim() || ('Column ' + (cIdx + 1));
            opt.textContent = (cIdx + 1) + ' - ' + name;
            if (cIdx === parseInt(m.col, 10)) opt.selected = true;
            colSel.appendChild(opt);
        });
        colSel.addEventListener('change', function() {
            _specTableSourceHtml = '';
            if (!_specTableRowMerges[idx]) return;
            _specTableRowMerges[idx].col = parseInt(this.value, 10) || 0;
            normalizeSpecTableState();
            renderSpecTableBuilder();
            syncSpecificationsHiddenField();
        });

        var startInput = document.createElement('input');
        startInput.type = 'number';
        startInput.min = '1';
        startInput.value = String((parseInt(m.row, 10) || 0) + 1);
        startInput.title = 'Start row';
        startInput.style.cssText = 'padding:6px 8px;border:1.5px solid #bfdbfe;border-radius:7px;font-size:11px;text-align:center;';
        startInput.addEventListener('change', function() {
            _specTableSourceHtml = '';
            if (!_specTableRowMerges[idx]) return;
            var v = parseInt(this.value, 10);
            if (!isFinite(v) || v < 1) v = 1;
            _specTableRowMerges[idx].row = v - 1;
            normalizeSpecTableState();
            renderSpecTableBuilder();
            syncSpecificationsHiddenField();
        });

        var spanInput = document.createElement('input');
        spanInput.type = 'number';
        spanInput.min = '2';
        spanInput.value = String(parseInt(m.rowSpan, 10) || 2);
        spanInput.title = 'Row span';
        spanInput.style.cssText = 'padding:6px 8px;border:1.5px solid #bfdbfe;border-radius:7px;font-size:11px;text-align:center;';
        spanInput.addEventListener('change', function() {
            _specTableSourceHtml = '';
            if (!_specTableRowMerges[idx]) return;
            var v = parseInt(this.value, 10);
            if (!isFinite(v) || v < 2) v = 2;
            _specTableRowMerges[idx].rowSpan = v;
            normalizeSpecTableState();
            renderSpecTableBuilder();
            syncSpecificationsHiddenField();
        });

        var removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.innerHTML = '&times;';
        removeBtn.title = 'Remove row merge';
        removeBtn.style.cssText = 'width:24px;height:24px;border-radius:6px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:16px;line-height:1;cursor:pointer;';
        removeBtn.addEventListener('click', function() {
            _specTableSourceHtml = '';
            _specTableRowMerges.splice(idx, 1);
            normalizeSpecTableState();
            renderSpecTableBuilder();
            syncSpecificationsHiddenField();
        });

        row.appendChild(colSel);
        row.appendChild(startInput);
        row.appendChild(spanInput);
        row.appendChild(removeBtn);
        wrap.appendChild(row);
    });

    var addMergeBtn = document.createElement('button');
    addMergeBtn.type = 'button';
    addMergeBtn.style.cssText = 'display:inline-flex;align-items:center;gap:6px;background:#ecfeff;border:1px solid #a5f3fc;color:#155e75;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;';
    addMergeBtn.innerHTML = '<i class="bi bi-arrows-collapse-vertical"></i> Add Body Merge';
    addMergeBtn.addEventListener('click', function() {
        _specTableSourceHtml = '';
        var lead = getGroupedLeadColumnCount();
        var defaultCol = lead > 0 ? 0 : 0;
        _specTableRowMerges.push({ row: 0, col: defaultCol, rowSpan: 2 });
        normalizeSpecTableState();
        renderSpecTableBuilder();
        syncSpecificationsHiddenField();
    });
    wrap.appendChild(addMergeBtn);
}

function toggleSpecGroupHeaderControls(forceValue) {
    var next = typeof forceValue === 'boolean' ? forceValue : !_specShowGroupHeaderControls;
    _specShowGroupHeaderControls = !!next;
    renderSpecTableBuilder();
}

function renderSpecTableBuilder() {
    normalizeSpecTableState();
    updateSpecTableModeUi();
    renderGroupedHeaderControls();

    var head = document.getElementById('specTableHead');
    var body = document.getElementById('specTableBody');
    if (!head || !body) return;

    head.innerHTML = '';
    body.innerHTML = '';

    var isStandardDark = _specTableMode === 'standard';

    if (_specTableMode === 'grouped-pairs') {
        var groupRow = document.createElement('tr');

        var leadCols = getGroupedLeadColumnCount();

        for (var leadIdx = 0; leadIdx < leadCols; leadIdx++) {
            (function(colIdx) {
                var firstTop = document.createElement('th');
                firstTop.rowSpan = 2;
                firstTop.style.cssText = 'padding:6px 7px;border-bottom:1px solid #93b4dc;border-right:1px solid #93b4dc;background:linear-gradient(180deg,#2f5f9d 0%,#1f4a82 55%,#183a67 100%);color:#fff;vertical-align:middle;';
                var firstTopInput = document.createElement('input');
                firstTopInput.type = 'text';
                firstTopInput.value = _specTableHeaders[colIdx] || (colIdx === 0 ? 'Model' : ('Column ' + (colIdx + 1)));
                firstTopInput.style.cssText = 'width:100%;padding:6px 8px;border:1px solid #9ec0ea;border-radius:7px;font-size:11px;font-weight:700;background:rgba(255,255,255,0.95);color:#183153;';
                firstTopInput.addEventListener('input', function() {
                    _specTableSourceHtml = '';
                    _specTableHeaders[colIdx] = this.value;
                    syncSpecificationsHiddenField();
                });
                firstTop.appendChild(firstTopInput);
                groupRow.appendChild(firstTop);
            })(leadIdx);
        }

        for (var g = 0; g < _specTableGroups.length; g++) {
            var gTh = document.createElement('th');
            var gInfo = _specTableGroups[g] || { title: '', span: 1 };
            var gSpan = parseInt(gInfo.span, 10);
            if (!isFinite(gSpan) || gSpan < 1) gSpan = 1;

            if (gSpan === 1 && gInfo.rowSpan) {
                gTh.rowSpan = 2;
            } else {
                gTh.colSpan = gSpan;
            }
            gTh.style.cssText = 'padding:7px 8px;border-bottom:1px solid #93b4dc;border-right:1px solid #93b4dc;background:linear-gradient(180deg,#2f5f9d 0%,#1f4a82 55%,#183a67 100%);color:#fff;font-size:11px;font-weight:800;text-align:center;';
            gTh.textContent = String(gInfo.title || ('Group ' + (g + 1)));
            groupRow.appendChild(gTh);
        }

        var actionHeadTop = document.createElement('th');
        actionHeadTop.rowSpan = 2;
        actionHeadTop.style.cssText = 'width:40px;padding:6px;border-bottom:1px solid #93b4dc;background:linear-gradient(180deg,#2f5f9d 0%,#1f4a82 55%,#183a67 100%);';
        groupRow.appendChild(actionHeadTop);
        head.appendChild(groupRow);

        var subHeaderRow = document.createElement('tr');
        var headerCursor = leadCols;
        var subCellCount = 0;
        for (var sg = 0; sg < _specTableGroups.length; sg++) {
            var sgInfo = _specTableGroups[sg] || { span: 1, rowSpan: false };
            var sgSpan = parseInt(sgInfo.span, 10);
            if (!isFinite(sgSpan) || sgSpan < 1) sgSpan = 1;

            if (sgSpan === 1 && sgInfo.rowSpan) {
                headerCursor += 1;
                continue;
            }

            for (var si = 0; si < sgSpan; si++) {
                (function(colIdx) {
                    var th = document.createElement('th');
                    th.style.cssText = 'padding:6px;border-bottom:1px solid #c7d7ed;border-right:1px solid #c7d7ed;background:#eef4fb;vertical-align:middle;';
                    var input = document.createElement('input');
                    input.type = 'text';
                    input.value = _specTableHeaders[colIdx] || '';
                    input.placeholder = (colIdx % 2 === 1) ? 'cfm' : 'm3/hr';
                    input.style.cssText = 'width:100%;padding:6px 8px;border:1.5px solid #bfd4ef;border-radius:7px;font-size:11px;font-weight:700;color:#1f2937;background:#fff;text-align:center;';
                    input.addEventListener('input', function() {
                        _specTableSourceHtml = '';
                        _specTableHeaders[colIdx] = this.value;
                        syncSpecificationsHiddenField();
                    });
                    th.appendChild(input);
                    subHeaderRow.appendChild(th);
                    subCellCount += 1;
                })(headerCursor);
                headerCursor += 1;
            }
        }

        if (subCellCount > 0) {
            head.appendChild(subHeaderRow);
        }
    } else {
        var headRow = document.createElement('tr');

        var indexHead = document.createElement('th');
        indexHead.style.cssText = isStandardDark
            ? 'width:42px;padding:8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:#0f131a;color:#cbd5e1;text-align:center;font-size:10px;font-weight:800;'
            : 'width:42px;padding:8px;border-bottom:1px solid #e5e7eb;background:#f8fafc;color:#6b7280;text-align:center;font-size:10px;font-weight:800;';
        indexHead.textContent = '#';
        headRow.appendChild(indexHead);

        _specTableHeaders.forEach(function(header, colIdx) {
            var th = document.createElement('th');
            th.style.cssText = isStandardDark
                ? 'padding:8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:#11151d;vertical-align:middle;'
                : 'padding:8px;border-bottom:1px solid #e5e7eb;background:#f8fafc;vertical-align:middle;';

            var wrap = document.createElement('div');
            wrap.style.cssText = 'display:grid;grid-template-columns:auto 1fr auto;gap:6px;align-items:center;';

            var colLabel = document.createElement('span');
            colLabel.textContent = getSpecColumnLabel(colIdx);
            colLabel.style.cssText = isStandardDark
                ? 'display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border:1px solid #4b5563;border-radius:5px;background:#1f2530;color:#e5e7eb;font-size:10px;font-weight:800;'
                : 'display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 6px;border:1px solid #d1d5db;border-radius:5px;background:#fff;color:#4b5563;font-size:10px;font-weight:800;';
            wrap.appendChild(colLabel);

            var input = document.createElement('input');
            input.type = 'text';
            input.value = header;
            input.placeholder = 'Column ' + (colIdx + 1);
            input.style.cssText = isStandardDark
                ? 'width:100%;min-width:110px;padding:7px 9px;border:1px solid #6b7280;border-radius:7px;font-size:11px;font-weight:700;color:#f3f4f6;background:#1f2530;'
                : 'width:100%;min-width:110px;padding:7px 9px;border:1.5px solid #dbe1ea;border-radius:7px;font-size:11px;font-weight:700;color:#1f2937;background:#fff;';
            input.addEventListener('input', function() {
                _specTableSourceHtml = '';
                _specTableHeaders[colIdx] = this.value;
                syncSpecificationsHiddenField();
            });
            wrap.appendChild(input);

            if (_specTableHeaders.length > 1) {
                var removeColBtn = document.createElement('button');
                removeColBtn.type = 'button';
                removeColBtn.innerHTML = '&times;';
                removeColBtn.title = 'Remove column';
                removeColBtn.style.cssText = isStandardDark
                    ? 'width:24px;height:24px;border-radius:6px;border:1px solid #7f1d1d;background:#2a1014;color:#fca5a5;font-size:16px;line-height:1;cursor:pointer;flex-shrink:0;'
                    : 'width:24px;height:24px;border-radius:6px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:16px;line-height:1;cursor:pointer;flex-shrink:0;';
                removeColBtn.addEventListener('click', function() {
                    removeSpecTableColumn(colIdx);
                });
                wrap.appendChild(removeColBtn);
            }

            th.appendChild(wrap);
            headRow.appendChild(th);
        });

        var actionHead = document.createElement('th');
        actionHead.style.cssText = isStandardDark
            ? 'width:42px;padding:8px;border-bottom:1px solid #6b7280;background:#11151d;'
            : 'width:42px;padding:8px;border-bottom:1px solid #e5e7eb;background:#f8fafc;';
        headRow.appendChild(actionHead);
        head.appendChild(headRow);
    }

    var rowMergeStarts = {};
    var rowMergeCovered = {};
    _specTableRowMerges.forEach(function(m) {
        var startRow = parseInt(m && m.row, 10);
        var col = parseInt(m && m.col, 10);
        var rowSpan = parseInt(m && m.rowSpan, 10);
        if (!isFinite(startRow) || !isFinite(col) || !isFinite(rowSpan)) return;
        if (rowSpan < 2) return;
        var startKey = startRow + ':' + col;
        rowMergeStarts[startKey] = rowSpan;
        for (var rr = startRow + 1; rr < startRow + rowSpan; rr++) {
            rowMergeCovered[rr + ':' + col] = true;
        }
    });

    _specTableRows.forEach(function(row, rowIdx) {
        var tr = document.createElement('tr');
        if (rowIdx % 2 === 1) tr.style.backgroundColor = isStandardDark ? 'rgba(255,255,255,0.02)' : '#fcfdff';
            var isGroupedMode = _specTableMode === 'grouped-pairs';

        if (_specTableMode === 'standard') {
            var idxTd = document.createElement('td');
            idxTd.style.cssText = isStandardDark
                ? 'padding:6px 8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:#0f131a;color:#cbd5e1;text-align:center;font-size:11px;font-weight:700;'
                : 'padding:6px 8px;border-bottom:1px solid #eef2f6;background:#f8fafc;color:#6b7280;text-align:center;font-size:11px;font-weight:700;';
            idxTd.textContent = String(rowIdx + 1);
            tr.appendChild(idxTd);
        }

        for (var colIdx = 0; colIdx < _specTableHeaders.length; colIdx++) {
            if (rowMergeCovered[rowIdx + ':' + colIdx]) {
                continue;
            }

            var td = document.createElement('td');
            td.style.cssText = isStandardDark
                ? 'padding:6px 8px;border-bottom:1px solid #6b7280;border-right:1px solid #6b7280;background:transparent;'
                : (isGroupedMode
                    ? 'padding:5px 6px;border-bottom:1px solid #d7deea;border-right:1px solid #d7deea;background:#ffffff;'
                    : 'padding:6px 8px;border-bottom:1px solid #d7deea;border-right:1px solid #d7deea;background:#ffffff;');

            var rowMergeSpan = parseInt(rowMergeStarts[rowIdx + ':' + colIdx], 10);
            if (isFinite(rowMergeSpan) && rowMergeSpan > 1) {
                td.rowSpan = rowMergeSpan;
                td.style.verticalAlign = 'middle';
            }

            var cellInput = document.createElement('textarea');
            cellInput.value = row[colIdx] || '';
            cellInput.className = 'spec-table-input';
            cellInput.setAttribute('data-row', String(rowIdx));
            cellInput.setAttribute('data-col', String(colIdx));
            cellInput.rows = 1;
            cellInput.style.cssText = isStandardDark
                ? 'width:100%;min-height:34px;padding:7px 9px;border:1px solid #6b7280;border-radius:7px;font-size:12px;line-height:1.45;color:#f3f4f6;background:#1f2530;resize:vertical;overflow:hidden;white-space:pre-wrap;'
                : (isGroupedMode
                    ? 'width:100%;min-height:30px;padding:6px 8px;border:1.5px solid #d6dfec;border-radius:7px;font-size:12px;line-height:1.35;color:#2d3a4f;background:#fdfefe;resize:vertical;overflow:hidden;white-space:pre-wrap;'
                    : 'width:100%;min-height:34px;padding:7px 9px;border:1.5px solid #d6dfec;border-radius:7px;font-size:12px;line-height:1.45;color:#2d3a4f;background:#fdfefe;resize:vertical;overflow:hidden;white-space:pre-wrap;');
            (function(r, c, inputEl) {
                inputEl.addEventListener('input', function() {
                    _specTableHasUserInput = true;
                    _specTableSourceHtml = '';
                    _specTableRows[r][c] = this.value;
                    autoResizeSpecCell(this);
                    syncSpecificationsHiddenField();
                });
                inputEl.addEventListener('keydown', function(evt) {
                    handleSpecCellKeyDown(evt, r, c);
                });
                inputEl.addEventListener('paste', function(evt) {
                    handleSpecCellPaste(evt, r, c);
                });
            })(rowIdx, colIdx, cellInput);

            autoResizeSpecCell(cellInput);

            td.appendChild(cellInput);
            tr.appendChild(td);
        }

        var actionTd = document.createElement('td');
        actionTd.style.cssText = isStandardDark
            ? 'padding:6px 8px;border-bottom:1px solid #6b7280;text-align:center;background:transparent;'
            : (isGroupedMode
                ? 'padding:5px 6px;border-bottom:1px solid #d7deea;text-align:center;background:#ffffff;'
                : 'padding:6px 8px;border-bottom:1px solid #d7deea;text-align:center;background:#ffffff;');
        var removeRowBtn = document.createElement('button');
        removeRowBtn.type = 'button';
        removeRowBtn.innerHTML = '<i class="bi bi-trash"></i>';
        removeRowBtn.title = 'Remove row';
        removeRowBtn.style.cssText = isStandardDark
            ? 'width:28px;height:28px;border-radius:7px;border:1px solid #7f1d1d;background:#2a1014;color:#fca5a5;cursor:pointer;'
            : (isGroupedMode
                ? 'width:26px;height:26px;border-radius:7px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;cursor:pointer;'
                : 'width:28px;height:28px;border-radius:7px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;cursor:pointer;');
        (function(r) {
            removeRowBtn.addEventListener('click', function() {
                removeSpecTableDataRow(r);
            });
        })(rowIdx);
        actionTd.appendChild(removeRowBtn);
        tr.appendChild(actionTd);

        body.appendChild(tr);
    });

    applySpecBuilderAutoSizing();
}

function addSpecTableColumn(label) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    _specTableRowMerges = [];
    if (_specTableMode === 'grouped-pairs') {
        addSpecTableGroup(label);
        return;
    }
    _specTableHeaders.push(String(label || ('Column ' + (_specTableHeaders.length + 1))));
    _specTableRows = _specTableRows.map(function(row) {
        row.push('');
        return row;
    });
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function addSpecTableSingleColumn(label) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    _specTableRowMerges = [];

    if (_specTableMode === 'grouped-pairs') {
        if (!Array.isArray(_specTableGroups) || _specTableGroups.length === 0) {
            _specTableGroups = [{ title: 'Group 1', span: 1, rowSpan: false }];
        } else {
            var lastIdx = _specTableGroups.length - 1;
            var lastSpan = parseInt(_specTableGroups[lastIdx] && _specTableGroups[lastIdx].span, 10);
            if (!isFinite(lastSpan) || lastSpan < 1) lastSpan = 1;
            _specTableGroups[lastIdx].span = lastSpan + 1;
        }

        _specTableHeaders.push(String(label || getDefaultGroupedSubHeader(_specTableHeaders.length)));
        _specTableRows = _specTableRows.map(function(row) {
            row.push('');
            return row;
        });

        renderSpecTableBuilder();
        syncSpecificationsHiddenField();
        return;
    }

    addSpecTableColumn(label);
}

function removeSpecTableColumn(colIdx) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    _specTableRowMerges = [];
    if (_specTableMode === 'grouped-pairs') {
        if (colIdx < getGroupedLeadColumnCount()) return;
        removeSpecTableGroup(findGroupedIndexByColumn(colIdx));
        return;
    }
    if (_specTableHeaders.length <= 1) return;
    _specTableHeaders.splice(colIdx, 1);
    _specTableRows = _specTableRows.map(function(row) {
        row.splice(colIdx, 1);
        return row;
    });
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function addSpecTableDataRow(initialValues) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    var row = new Array(_specTableHeaders.length).fill('');
    if (Array.isArray(initialValues)) {
        for (var i = 0; i < Math.min(initialValues.length, row.length); i++) {
            row[i] = String(initialValues[i] || '');
        }
    }
    _specTableRows.push(row);
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function removeSpecTableDataRow(rowIdx) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    if (_specTableRows.length <= 1) {
        _specTableRows[0] = new Array(_specTableHeaders.length).fill('');
        _specTableRowMerges = [];
    } else {
        _specTableRows.splice(rowIdx, 1);

        _specTableRowMerges = _specTableRowMerges.map(function(m) {
            var start = parseInt(m && m.row, 10);
            var span = parseInt(m && m.rowSpan, 10);
            var col = parseInt(m && m.col, 10);
            if (!isFinite(start) || !isFinite(span) || !isFinite(col)) return null;
            if (span < 2) return null;

            var end = start + span - 1;
            if (rowIdx < start) {
                return { row: start - 1, col: col, rowSpan: span };
            }

            if (rowIdx > end) {
                return { row: start, col: col, rowSpan: span };
            }

            var nextSpan = span - 1;
            if (nextSpan < 2) return null;
            return { row: start, col: col, rowSpan: nextSpan };
        }).filter(function(m) { return !!m; });
    }
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function clearSpecTableValues() {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    _specTableMerges = [];
    _specTableRowMerges = [];

    _specTableRows = [new Array(_specTableHeaders.length).fill('')];

    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function resetSpecTableFresh() {
    _specTableHasUserInput = true;
    _specTableMode = 'standard';
    _specTableLeadColumns = 0;
    _specTableHeaders = ['Parameter', 'Value'];
    _specTableRows = [['', '']];
    _specTableGroups = [];
    _specTableMerges = [];
    _specTableRowMerges = [];
    _specTableSourceHtml = '';

    normalizeSpecTableState();
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function addSpecTableGroup(groupLabel) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    _specTableRowMerges = [];
    var parsedSpan = parseInt(arguments[1], 10);
    if (!isFinite(parsedSpan) || parsedSpan < 1) parsedSpan = 2;

    var nextTitle = String(groupLabel || ('Group ' + (_specTableGroups.length + 1))).trim();
    if (nextTitle === '') nextTitle = 'Group ' + (_specTableGroups.length + 1);
    _specTableGroups.push({ title: nextTitle, span: parsedSpan });

    for (var i = 0; i < parsedSpan; i++) {
        _specTableHeaders.push(getDefaultGroupedSubHeader(_specTableHeaders.length));
    }

    _specTableRows = _specTableRows.map(function(row) {
        for (var i = 0; i < parsedSpan; i++) row.push('');
        return row;
    });
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function setSpecTableGroupSpan(groupIdx, spanValue) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    _specTableRowMerges = [];
    if (!_specTableGroups[groupIdx]) return;

    var nextSpan = parseInt(spanValue, 10);
    if (!isFinite(nextSpan) || nextSpan < 1) nextSpan = 1;

    var oldSpan = parseInt(_specTableGroups[groupIdx].span, 10);
    if (!isFinite(oldSpan) || oldSpan < 1) oldSpan = 1;
    if (nextSpan === oldSpan) return;

    var startCol = getGroupedStartColumn(groupIdx, _specTableGroups);

    if (nextSpan > oldSpan) {
        var addCount = nextSpan - oldSpan;
        for (var a = 0; a < addCount; a++) {
            var insertCol = startCol + oldSpan + a;
            _specTableHeaders.splice(insertCol, 0, getDefaultGroupedSubHeader(insertCol));
            _specTableRows = _specTableRows.map(function(row) {
                row.splice(insertCol, 0, '');
                return row;
            });
        }
    } else {
        var removeCount = oldSpan - nextSpan;
        var removeStart = startCol + nextSpan;
        _specTableHeaders.splice(removeStart, removeCount);
        _specTableRows = _specTableRows.map(function(row) {
            row.splice(removeStart, removeCount);
            return row;
        });
    }

    _specTableGroups[groupIdx].span = nextSpan;

    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function removeSpecTableGroup(groupIdx) {
    _specTableHasUserInput = true;
    normalizeSpecTableState();
    _specTableSourceHtml = '';
    _specTableRowMerges = [];
    if (_specTableGroups.length <= 1) {
        _specTableGroups = getDefaultGroupedGroups();
        _specTableLeadColumns = 0;
        var minHeaders = _specTableLeadColumns + getGroupedDataColumnCount(_specTableGroups);
        var firstHeader = String(_specTableHeaders[0] || 'Model').trim() || 'Model';
        _specTableHeaders = [firstHeader];
        while (_specTableHeaders.length < minHeaders) {
            _specTableHeaders.push(getDefaultGroupedSubHeader(_specTableHeaders.length));
        }

        if (_specTableRows.length === 0) {
            _specTableRows = [new Array(_specTableHeaders.length).fill('')];
        }
        for (var r = 0; r < _specTableRows.length; r++) {
            _specTableRows[r] = _specTableRows[r].slice(0, _specTableHeaders.length);
            while (_specTableRows[r].length < _specTableHeaders.length) _specTableRows[r].push('');
            for (var c = 1; c < _specTableRows[r].length; c++) {
                _specTableRows[r][c] = '';
            }
        }
        renderSpecTableBuilder();
        syncSpecificationsHiddenField();
        return;
    }

    if (groupIdx < 0 || groupIdx >= _specTableGroups.length) return;

    var startCol = getGroupedStartColumn(groupIdx, _specTableGroups);
    var span = parseInt(_specTableGroups[groupIdx].span, 10);
    if (!isFinite(span) || span < 1) span = 1;

    _specTableGroups.splice(groupIdx, 1);
    _specTableHeaders.splice(startCol, span);
    _specTableRows = _specTableRows.map(function(row) {
        row.splice(startCol, span);
        return row;
    });

    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function setSpecTableMode(mode, keepData) {
    _specTableHasUserInput = true;
    var nextMode = mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
    var prevMode = _specTableMode;

    _specTableMode = nextMode;
    _specTableSourceHtml = '';
    _specTableRowMerges = [];

    if (prevMode !== nextMode && keepData) {
        if (nextMode === 'grouped-pairs') {
            _specTableMerges = [];
            _specShowGroupHeaderControls = false;
            if (_specTableHeaders.length === 0) {
                _specTableHeaders = getDefaultGroupedHeaders();
            }

            var targetCols = getGroupedLeadColumnCount() + getGroupedDataColumnCount(_specTableGroups.length > 0 ? _specTableGroups : getDefaultGroupedGroups());

            while (_specTableHeaders.length < targetCols) {
                _specTableHeaders.push(getDefaultGroupedSubHeader(_specTableHeaders.length));
            }
        } else {
            _specTableGroups = [];
            _specShowGroupHeaderControls = false;
        }
    }

    normalizeSpecTableState();
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function applyAirflowSpecTemplate() {
    _specTableHasUserInput = true;
    _specTableMode = 'grouped-pairs';
    _specTableMerges = [];
    _specTableRowMerges = [];
    _specTableSourceHtml = '';
    _specTableLeadColumns = 0;
    _specTableHeaders = ['Model', 'cfm', 'm3/hr', 'cfm', 'm3/hr', 'cfm', 'm3/hr', 'cfm', 'm3/hr'];
    _specTableGroups = [
        { title: 'Free Air', span: 2, rowSpan: false },
        { title: '10 ft. 3.05 M', span: 2, rowSpan: false },
        { title: '20 ft. 6.10 M', span: 2, rowSpan: false },
        { title: '30 ft. 9.15 M', span: 2, rowSpan: false },
    ];
    _specTableRows = [[
        'Air Max', '2200', '3740', '2120', '3602', '2025', '3440', '1890', '3211'
    ]];

    normalizeSpecTableState();
    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function convertSpecificationsTextToTable() {
    var textArea = document.getElementById('editSpecificationsText');
    if (!textArea) return;

    var raw = String(textArea.value || '')
        .replace(/\r\n?/g, '\n')
        .replace(/\u00A0/g, ' ')
        .replace(/[\u200B-\u200D\uFEFF]/g, '');

    var lines = raw.split('\n');
    var rows = [];

    function appendToLastValue(text) {
        if (rows.length === 0) {
            rows.push(['', text]);
            return;
        }
        var prev = String(rows[rows.length - 1][1] || '');
        rows[rows.length - 1][1] = prev !== '' ? (prev + '\n' + text) : text;
    }

    for (var i = 0; i < lines.length; i++) {
        var line = lines[i].replace(/\s+$/g, '');
        var trimmed = line.trim();
        if (trimmed === '') continue;
        if (/^specifications?:?$/i.test(trimmed)) continue;

        var numbered = trimmed.match(/^(\d+\.)\s*(.+)$/);
        if (numbered) {
            appendToLastValue(numbered[1] + ' ' + numbered[2]);
            continue;
        }

        var sepIdx = trimmed.indexOf(':');
        if (sepIdx >= 0) {
            var label = trimmed.slice(0, sepIdx).replace(/\s+/g, ' ').trim();
            var value = trimmed.slice(sepIdx + 1).replace(/\s+/g, ' ').trim();

            if (label === '' && value !== '') {
                appendToLastValue(value);
            } else {
                rows.push([label, value]);
            }
            continue;
        }

        appendToLastValue(trimmed.replace(/\s+/g, ' '));
    }

    if (rows.length === 0) {
        customAlert('No specs text found to convert.');
        return;
    }

    _specTableHasUserInput = true;
    _specTableMode = 'standard';
    _specTableLeadColumns = 0;
    _specTableGroups = [];
    _specTableMerges = [];
    _specTableRowMerges = [];
    _specTableSourceHtml = '';
    _specTableHeaders = ['Parameter', 'Value'];
    _specTableRows = rows.map(function(pair) {
        return [String(pair[0] || ''), String(pair[1] || '')];
    });

    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

function hasGroupedTableContent() {
    var leadCols = getGroupedLeadColumnCount();
    var hasCellData = _specTableRows.some(function(row) {
        return row.some(function(cell) { return String(cell || '').trim() !== ''; });
    });

    var hasCustomHeader = _specTableHeaders.some(function(h, idx) {
        if (idx < leadCols && idx > 0) return String(h || '').trim() !== '';
        return !isDefaultGroupedHeader(h, idx);
    });

    var hasCustomGroup = _specTableGroups.some(function(g, idx) {
        var title = String((g && g.title) || '').trim().toLowerCase();
        var span = parseInt(g && g.span, 10);
        if (!isFinite(span) || span < 1) span = 1;

        var defaultTitle = (idx === 0) ? 'free air' : ('group ' + (idx + 1));
        return (title !== '' && title !== defaultTitle) || span !== 2 || !!(g && g.rowSpan);
    });

    var hasCustomLead = leadCols !== 1;

    var hasBodyRowMerge = Array.isArray(_specTableRowMerges) && _specTableRowMerges.length > 0;

    return hasCellData || hasCustomHeader || hasCustomGroup || hasCustomLead || hasBodyRowMerge;
}

function syncSpecificationsHiddenField() {
    var hiddenInput = document.getElementById('editSpecifications');
    var textArea = document.getElementById('editSpecificationsText');
    var secondTableInput = document.getElementById('specSecondTableText');
    if (!hiddenInput || !textArea) return;

    var textValue = String(textArea.value || '').trim();
    var secondTableText = secondTableInput ? String(secondTableInput.value || '').trim() : '';
    var secondTableMatrix = buildStandardMatrixPayloadFromTabText(secondTableText);
    var hasSecondTable = !!secondTableMatrix;

    var currentMatrixPayload = (_specTableMode === 'grouped-pairs')
        ? {
            mode: 'grouped-pairs',
            leadColumns: getGroupedLeadColumnCount(),
            headers: _specTableHeaders.map(function(h){ return String(h || '').trim(); }),
            groups: _specTableGroups.map(function(g){
                return {
                    title: String((g && g.title) || '').trim(),
                    span: Math.max(1, parseInt(g && g.span, 10) || 1),
                    rowSpan: !!(g && g.rowSpan),
                };
            }),
            rows: _specTableRows.map(function(row) {
                return row.map(function(cell) { return String(cell || '').trim(); });
            }),
            rowMerges: _specTableRowMerges.map(function(m) {
                return {
                    row: Math.max(0, parseInt(m && m.row, 10) || 0),
                    col: Math.max(0, parseInt(m && m.col, 10) || 0),
                    rowSpan: Math.max(2, parseInt(m && m.rowSpan, 10) || 2),
                };
            }),
        }
        : {
            mode: 'standard',
            headers: _specTableHeaders.map(function(h){ return String(h || '').trim(); }),
            rows: _specTableRows.map(function(row) {
                return row.map(function(cell) { return String(cell || '').trim(); });
            }),
            merges: _specTableMerges.map(function(m) {
                return {
                    row: Math.max(0, parseInt(m && m.row, 10) || 0),
                    col: Math.max(1, parseInt(m && m.col, 10) || 1),
                    span: Math.max(2, parseInt(m && m.span, 10) || 2),
                };
            }),
        };

    // If user hasn't actively used the table builder and there's only text, save as plain text
    if (!hasSecondTable && !_specTableHasUserInput && _specTableRows.length === 1 && _specTableRows[0].every(function(cell) { return String(cell).trim() === ''; })) {
        hiddenInput.value = textValue;
        return;
    }

    if (_specTableMode === 'grouped-pairs') {
        if (!hasGroupedTableContent()) {
            if (!hasSecondTable) {
                hiddenInput.value = textValue;
                return;
            }

            hiddenInput.value = JSON.stringify({
                format: 'andison_specs_v3',
                text: textValue,
                tables: [{
                    tableHtml: '',
                    tableMatrix: secondTableMatrix,
                }],
            });
            return;
        }

        if (hasSecondTable) {
            hiddenInput.value = JSON.stringify({
                format: 'andison_specs_v3',
                text: textValue,
                tables: [{
                    tableHtml: '',
                    tableMatrix: currentMatrixPayload,
                }, {
                    tableHtml: '',
                    tableMatrix: secondTableMatrix,
                }],
            });
            return;
        }

        hiddenInput.value = JSON.stringify({
            format: 'andison_specs_v2',
            text: textValue,
            tableMatrix: currentMatrixPayload,
        });
        return;
    }

    if (_specTableSourceHtml !== '') {
        var sourceTables = [{
            tableHtml: _specTableSourceHtml,
            tableMatrix: currentMatrixPayload,
        }];
        if (hasSecondTable) {
            sourceTables.push({
                tableHtml: '',
                tableMatrix: secondTableMatrix,
            });
        }

        hiddenInput.value = JSON.stringify({
            format: 'andison_specs_v3',
            text: textValue,
            tables: sourceTables,
        });
        return;
    }

    var hasStandardMerge = _specTableMerges.some(function(m) {
        return m && isFinite(parseInt(m.row, 10)) && isFinite(parseInt(m.col, 10)) && (parseInt(m.span, 10) > 1);
    });

    if (hasStandardMerge) {
        if (hasSecondTable) {
            hiddenInput.value = JSON.stringify({
                format: 'andison_specs_v3',
                text: textValue,
                tables: [{
                    tableHtml: '',
                    tableMatrix: currentMatrixPayload,
                }, {
                    tableHtml: '',
                    tableMatrix: secondTableMatrix,
                }],
            });
            return;
        }

        hiddenInput.value = JSON.stringify({
            format: 'andison_specs_v2',
            text: textValue,
            tableMatrix: currentMatrixPayload,
        });
        return;
    }

    var tableRows = matrixToSpecTableRows(_specTableHeaders, _specTableRows);

    // Only convert to JSON if user explicitly used table builder or there's actual table data
    if (!_specTableHasUserInput || tableRows.length === 0) {
        if (hasSecondTable) {
            hiddenInput.value = JSON.stringify({
                format: 'andison_specs_v3',
                text: textValue,
                tables: [{
                    tableHtml: '',
                    tableMatrix: secondTableMatrix,
                }],
            });
            return;
        }

        hiddenInput.value = textValue;
        return;
    }

    if (hasSecondTable) {
        hiddenInput.value = JSON.stringify({
            format: 'andison_specs_v3',
            text: textValue,
            tables: [{
                tableHtml: '',
                tableMatrix: currentMatrixPayload,
            }, {
                tableHtml: '',
                tableMatrix: secondTableMatrix,
            }],
        });
        return;
    }

    hiddenInput.value = JSON.stringify({
        format: 'andison_specs_v1',
        text: textValue,
        table: tableRows,
    });
}

function setSpecificationsEditor(rawSpecifications) {
    var parsed = parseSpecificationsForEditor(rawSpecifications);
    var textArea = document.getElementById('editSpecificationsText');
    if (!textArea) return;

    textArea.value = parsed.text;
    setSpecImagePreview(parsed.specImage || '');
    setSecondSpecificationsEditor(parsed.secondTableText || '');

    // Track whether the original format had table data
    _specTableHasUserInput = !!(parsed.matrix || (parsed.table && parsed.table.length > 0));

    if (parsed.matrix) {
        _specTableMode = parsed.matrix.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
        _specTableHeaders = parsed.matrix.headers;
        _specTableRows = parsed.matrix.rows;
        _specTableSourceHtml = String(parsed.sourceHtml || '').trim();
        if (_specTableMode === 'grouped-pairs') {
            _specTableLeadColumns = parseInt(parsed.matrix.leadColumns, 10);
            if (!isFinite(_specTableLeadColumns) || _specTableLeadColumns < 0) _specTableLeadColumns = 0;
            _specTableGroups = normalizeGroupedGroups(parsed.matrix.groups || [], Math.max(1, _specTableHeaders.length - getGroupedLeadColumnCount()));
            _specTableMerges = [];
            _specTableRowMerges = Array.isArray(parsed.matrix.rowMerges) ? parsed.matrix.rowMerges : [];
        } else {
            _specTableLeadColumns = 0;
            _specTableGroups = [];
            _specTableMerges = Array.isArray(parsed.matrix.merges) ? parsed.matrix.merges : [];
            _specTableRowMerges = [];
        }
    } else if (parsed.table && parsed.table.length > 0) {
        // User had table data, so populate table builder
        _specTableMode = 'standard';
        _specTableLeadColumns = 0;
        _specTableGroups = [];
        _specTableMerges = [];
        _specTableRowMerges = [];
        _specTableSourceHtml = '';

        var matrix = specTableRowsToMatrix(parsed.table);
        _specTableHeaders = matrix.headers;
        _specTableRows = matrix.rows;
    } else {
        // Plain text only - don't populate table builder with defaults
        _specTableMode = 'standard';
        _specTableLeadColumns = 0;
        _specTableGroups = [];
        _specTableMerges = [];
        _specTableRowMerges = [];
        _specTableSourceHtml = '';
        _specTableHeaders = ['Parameter', 'Value'];
        _specTableRows = [['', '']];
    }

    renderSpecTableBuilder();
    syncSpecificationsHiddenField();
}

// ── Multi-image slot state ──────────────────────────────────────────────────
var MAX_PRODUCT_IMAGES = <?php echo (int)$maxProductImages; ?>;
var _existingUrls = Array(MAX_PRODUCT_IMAGES).fill('');
var _previewUrls  = Array(MAX_PRODUCT_IMAGES).fill(null);
var _allowedImageExts = ['jpg', 'jpeg', 'jfif', 'png', 'webp', 'gif', 'avif'];
var _allowedImageMimes = ['image/jpeg', 'image/pjpeg', 'image/jfif', 'image/png', 'image/webp', 'image/gif', 'image/avif'];

function _esc(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

function isAllowedImageFile(file) {
    if (!file) return false;

    var name = String(file.name || '').toLowerCase();
    var ext = '';
    if (name.indexOf('.') !== -1) {
        ext = name.split('.').pop();
    }

    var mime = String(file.type || '').toLowerCase();
    if (mime && _allowedImageMimes.indexOf(mime) !== -1) {
        return true;
    }

    // Fallback by extension for browsers/files where MIME can be empty.
    return _allowedImageExts.indexOf(ext) !== -1;
}

function renderImageSlots() {
    var grid = document.getElementById('imageSlotGrid');
    if (!grid) return;
    grid.innerHTML = '';
    for (var i = 0; i < MAX_PRODUCT_IMAGES; i++) {
        (function(idx) {
            var slot = document.createElement('div');
            var preview = _previewUrls[idx];
            slot.style.cssText = 'background:#f9fafb;border:2px dashed #e5e7eb;border-radius:10px;height:84px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;overflow:hidden;transition:border-color 0.2s;flex-shrink:0;';
            if (preview) {
                slot.style.border = '2px solid ' + (idx === 0 ? 'var(--accent)' : '#d1d5db');
                var img = document.createElement('img');
                img.src = preview;
                img.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;padding:5px;';
                img.onerror = function() { this.style.display='none'; };
                slot.appendChild(img);
                if (idx === 0) {
                    var badge = document.createElement('span');
                    badge.textContent = 'MAIN';
                    badge.style.cssText = 'position:absolute;bottom:0;left:0;right:0;text-align:center;font-size:9px;font-weight:800;color:#fff;background:var(--accent);padding:2px 0;pointer-events:none;';
                    slot.appendChild(badge);
                }
                var rm = document.createElement('button');
                rm.type = 'button';
                rm.innerHTML = '&times;';
                rm.style.cssText = 'position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:50%;background:rgba(239,68,68,0.85);color:#fff;border:none;cursor:pointer;font-size:14px;line-height:1;display:flex;align-items:center;justify-content:center;padding:0;font-weight:700;';
                rm.onclick = function(e) { e.stopPropagation(); removeImageSlot(idx); };
                slot.appendChild(rm);
            } else {
                var inner = document.createElement('div');
                inner.style.cssText = 'text-align:center;pointer-events:none;';
                inner.innerHTML = '<i class="bi bi-plus" style="font-size:22px;color:#d1d5db;display:block;"></i>'
                    + '<span style="font-size:9px;color:#d1d5db;">' + (idx === 0 ? 'Main' : 'Img '+(idx+1)) + '</span>';
                slot.appendChild(inner);
                slot.addEventListener('mouseenter', function() { this.style.borderColor='var(--accent)'; });
                slot.addEventListener('mouseleave', function() { this.style.borderColor='#e5e7eb'; });
            }
            slot.addEventListener('click', function(e) {
                if (e.target.tagName !== 'BUTTON') {
                    document.getElementById('imageFile_' + idx).click();
                }
            });
            grid.appendChild(slot);
        })(i);
    }
    var ei = document.getElementById('existingImagesInput');
    if (ei) ei.value = JSON.stringify(_existingUrls);
}

function removeImageSlot(idx) {
    _existingUrls[idx] = '';
    _previewUrls[idx]  = null;
    var fi = document.getElementById('imageFile_' + idx);
    if (fi) fi.value = '';
    renderImageSlots();
}

function previewImageSlot(input, idx) {
    if (!input.files || !input.files.length) return;
    var file = input.files[0];
    if (!isAllowedImageFile(file)) {
        customAlert('Please select a valid image file.'); input.value = ''; return;
    }
    if (file.size > 100 * 1024 * 1024) {
        customAlert('File size must be less than 100MB.'); input.value = ''; return;
    }
    _existingUrls[idx] = '';
    var reader = new FileReader();
    reader.onload = function(e) { _previewUrls[idx] = e.target.result; renderImageSlots(); };
    reader.readAsDataURL(file);
}

function assignFileToSlotInput(file, idx) {
    var input = document.getElementById('imageFile_' + idx);
    if (!input || typeof DataTransfer === 'undefined') return false;
    try {
        var dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        return true;
    } catch (e) {
        return false;
    }
}

function handleBulkImageSelect(input) {
    if (!input.files || !input.files.length) return;

    var files = Array.prototype.slice.call(input.files);
    var validFiles = [];

    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (!isAllowedImageFile(file)) continue;
        if (file.size > 100 * 1024 * 1024) continue;
        validFiles.push(file);
    }

    if (!validFiles.length) {
        customAlert('Please select valid image files (max 100MB each).');
        input.value = '';
        return;
    }

    if (validFiles.length > MAX_PRODUCT_IMAGES) {
        customAlert('You can upload up to ' + MAX_PRODUCT_IMAGES + ' images only. First ' + MAX_PRODUCT_IMAGES + ' files will be used.');
        validFiles = validFiles.slice(0, MAX_PRODUCT_IMAGES);
    }

    var emptySlots = [];
    for (var s = 0; s < MAX_PRODUCT_IMAGES; s++) {
        if (!_previewUrls[s]) emptySlots.push(s);
    }

    var targetSlots = emptySlots.slice(0, validFiles.length);
    if (targetSlots.length < validFiles.length) {
        for (var t = 0; t < MAX_PRODUCT_IMAGES && targetSlots.length < validFiles.length; t++) {
            if (targetSlots.indexOf(t) === -1) targetSlots.push(t);
        }
    }

    var assignFailed = false;
    for (var k = 0; k < validFiles.length; k++) {
        var slotIdx = targetSlots[k];
        if (typeof slotIdx === 'undefined') break;

        if (!assignFileToSlotInput(validFiles[k], slotIdx)) {
            assignFailed = true;
            break;
        }

        _existingUrls[slotIdx] = '';
        (function(idx, fileObj) {
            var reader = new FileReader();
            reader.onload = function(e) {
                _previewUrls[idx] = e.target.result;
                renderImageSlots();
            };
            reader.readAsDataURL(fileObj);
        })(slotIdx, validFiles[k]);
    }

    if (assignFailed) {
        customAlert('Multi-image assignment is not supported by this browser. Please upload per slot.');
    } else {
        renderImageSlots();
    }

    input.value = '';
}
// ─────────────────────────────────────────────────────────────────────────────

function ensureGlobalProductModal() {
    var modal = document.getElementById('editProductModal');
    if (!modal) return null;

    // Keep modal outside the dashboard content container so it behaves as a true global popup.
    if (modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    return modal;
}

var _editDescriptionStarterSeeded = false;

function isLikelyRichDescription(content) {
    var value = String(content || '').trim();
    if (value === '') return false;
    return /<\s*(table|tr|td|th|thead|tbody|tfoot|img)\b/i.test(value);
}

function getEditDescriptionEditor() {
    return null;
}

function getEditDescriptionContent() {
    var textarea = document.getElementById('editDescription');
    return textarea ? textarea.value : '';
}

function setEditDescriptionContent(content) {
    var html = String(content || '');
    var textarea = document.getElementById('editDescription');
    if (textarea) textarea.value = html;
}

function isEditDescriptionEmpty(content) {
    var html = String(content || '').trim();
    if (html === '') return true;

    var stripped = html
        .replace(/<\s*br\s*\/?\s*>/gi, '\n')
        .replace(/<\/(p|div|li|h[1-6]|tr|table)\s*>/gi, '\n')
        .replace(/<(p|div|li|h[1-6]|tr|table)\b[^>]*>/gi, '')
        .replace(/&nbsp;/gi, ' ')
        .replace(/<[^>]+>/g, '')
        .trim();

    return stripped === '';
}

function escapeEditDescriptionHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function buildEditDescriptionStarterTable() {
    return [
        '<table style="width:100%;border-collapse:collapse;">',
        '<thead>',
        '<tr>',
        '<th>Section</th>',
        '<th>Details</th>',
        '</tr>',
        '</thead>',
        '<tbody>',
        '<tr>',
        '<td>Feature 1</td>',
        '<td>Add your description here.</td>',
        '</tr>',
        '<tr>',
        '<td>Feature 2</td>',
        '<td>Use merged cells or upload images if needed.</td>',
        '</tr>',
        '<tr>',
        '<td>Feature 3</td>',
        '<td>Edit this starter table as your template.</td>',
        '</tr>',
        '</tbody>',
        '</table>'
    ].join('');
}

function normalizeEditDescriptionLines(content) {
    var html = String(content || '');
    if (!html.trim()) return [];

    var temp = document.createElement('div');
    temp.innerHTML = html
        .replace(/<\s*br\s*\/?\s*>/gi, '\n')
        .replace(/<\/(p|div|li|h[1-6]|tr)\s*>/gi, '\n')
        .replace(/<(p|div|li|h[1-6]|tr)\b[^>]*>/gi, '');

    var text = (temp.textContent || temp.innerText || '')
        .replace(/\u00a0/g, ' ')
        .replace(/\n{3,}/g, '\n\n')
        .trim();

    if (!text) return [];

    return text
        .split(/\r?\n+/)
        .map(function(line) {
            return line.trim();
        })
        .filter(function(line) {
            return line !== '';
        });
}

function buildEditDescriptionTableFromText(content) {
    var lines = normalizeEditDescriptionLines(content);
    if (!lines.length) {
        return buildEditDescriptionStarterTable();
    }

    var rowsHtml = lines.map(function(line) {
        var label = line;
        var value = '';

        if (/\t/.test(line)) {
            var tabParts = line.split(/\t+/);
            label = tabParts.shift() || '';
            value = tabParts.join(' ').trim();
        } else if (line.indexOf(':') !== -1) {
            var colonIndex = line.indexOf(':');
            label = line.slice(0, colonIndex);
            value = line.slice(colonIndex + 1);
        } else if (/\s{2,}/.test(line)) {
            var spaceParts = line.split(/\s{2,}/);
            label = spaceParts.shift() || '';
            value = spaceParts.join(' ').trim();
        }

        label = label.trim();
        value = value.trim();

        if (!label && !value) return '';

        if (!value) {
            value = 'Add details here.';
        }

        return '<tr><td>' + escapeEditDescriptionHtml(label) + '</td><td>' + escapeEditDescriptionHtml(value) + '</td></tr>';
    }).filter(function(row) {
        return row !== '';
    }).join('');

    if (!rowsHtml) {
        rowsHtml = '<tr><td>Item 1</td><td>Add details here.</td></tr>';
    }

    return [
        '<table style="width:100%;border-collapse:collapse;">',
        '<thead><tr><th>Label</th><th>Details</th></tr></thead>',
        '<tbody>',
        rowsHtml,
        '</tbody>',
        '</table>'
    ].join('');
}

var _editDescriptionMode = 'plain';
var _editDescriptionStarterSeeded = false;
var _editDescriptionTableHtmlCache = '';
var _editDescriptionCellIdSeq = 0;
var _editDescriptionPendingImageCell = null;
var _editDescriptionSelectionAnchor = null;
var _editDescriptionSelectionDragActive = false;
var _editDescriptionSelectionDragAnchor = null;

function descriptionTableGetTextarea() {
    return document.getElementById('editDescription');
}

function descriptionTableGetPanel() {
    return document.getElementById('editDescriptionTablePanel');
}

function descriptionTableGetEditor() {
    return document.getElementById('editDescriptionTableEditor');
}

function descriptionTableHasTableMarkup(content) {
    return /<\s*table\b/i.test(String(content || ''));
}

function descriptionTableExtractTableHtml(content) {
    var wrapper = document.createElement('div');
    wrapper.innerHTML = String(content || '');
    var table = wrapper.querySelector('table');
    return table ? table.outerHTML : '';
}

function descriptionTableEnsureCellIds(table) {
    if (!table) return;
    table.querySelectorAll('tbody td, thead .desc-head-label').forEach(function(cell) {
        if (!cell.getAttribute('data-desc-cell-id')) {
            _editDescriptionCellIdSeq += 1;
            cell.setAttribute('data-desc-cell-id', 'desc-cell-' + _editDescriptionCellIdSeq);
        }
        if (cell.classList.contains('desc-head-label')) {
            cell.setAttribute('contenteditable', 'true');
            cell.spellcheck = true;
        } else {
            cell.removeAttribute('contenteditable');
            var editor = cell.querySelector('.desc-cell-editor');
            if (editor) {
                editor.setAttribute('contenteditable', 'true');
                editor.spellcheck = true;
            }
        }
    });
}

function descriptionTableBuildImageWrapper(url, altText) {
    var safeUrl = escapeEditDescriptionHtml(url || '');
    var safeAlt = escapeEditDescriptionHtml(altText || 'Description image');
    return '<span class="desc-cell-image-wrap" contenteditable="false">' +
        '<button type="button" class="desc-cell-image-delete" data-desc-image-delete aria-label="Remove image">x</button>' +
        '<img src="' + safeUrl + '" alt="' + safeAlt + '">' +
        '</span>';
}

function descriptionTableClearSelection() {
    var table = descriptionTableGetEditor();
    if (!table) return;
    table.querySelectorAll('tbody .is-selected').forEach(function(cell) {
        cell.classList.remove('is-selected');
    });
    descriptionTableRefreshSelectionUi();
}

function descriptionTableGetSelectedCells() {
    var table = descriptionTableGetEditor();
    if (!table) return [];
    return Array.prototype.slice.call(table.querySelectorAll('tbody .is-selected'));
}

function descriptionTableUpdateSelectionCount() {
    var counter = document.getElementById('editDescriptionTableSelectionCount');
    if (!counter) return;
    var selected = descriptionTableGetSelectedCells().length;
    counter.textContent = 'Selected cells: ' + selected;
}

function descriptionTableSelectCell(cell, additive) {
    var table = descriptionTableGetEditor();
    if (!table || !cell) return;

    if (!additive) {
        descriptionTableClearSelection();
    }

    cell.classList.add('is-selected');
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
}

function descriptionTableGetCellInfo(cell) {
    if (!cell) return null;
    var map = descriptionTableBuildGrid();
    return map.positions.get(cell) || null;
}

function descriptionTableSelectCellRange(startCell, endCell) {
    var startInfo = descriptionTableGetCellInfo(startCell);
    var endInfo = descriptionTableGetCellInfo(endCell);
    if (!startInfo || !endInfo) return;

    var map = descriptionTableBuildGrid();
    var minRow = Math.min(startInfo.row, endInfo.row);
    var maxRow = Math.max(startInfo.row, endInfo.row);
    var minCol = Math.min(startInfo.col, endInfo.col);
    var maxCol = Math.max(startInfo.col, endInfo.col);

    descriptionTableClearSelection();
    for (var r = minRow; r <= maxRow; r++) {
        for (var c = minCol; c <= maxCol; c++) {
            var target = map.grid[r] && map.grid[r][c];
            if (target) {
                target.classList.add('is-selected');
            }
        }
    }
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
}

function descriptionTableToggleCellSelection(cell) {
    if (!cell) return;
    cell.classList.toggle('is-selected');
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
}

function descriptionTableStartDragSelection(cell) {
    if (!cell) return;
    _editDescriptionSelectionDragActive = true;
    _editDescriptionSelectionDragAnchor = cell;
    descriptionTableClearSelection();
    descriptionTableSelectCell(cell, false);
    _editDescriptionSelectionAnchor = cell;
}

function descriptionTableExtendDragSelection(cell) {
    if (!_editDescriptionSelectionDragActive || !_editDescriptionSelectionDragAnchor || !cell) return;
    descriptionTableSelectCellRange(_editDescriptionSelectionDragAnchor, cell);
}

function descriptionTableStopDragSelection() {
    _editDescriptionSelectionDragActive = false;
    _editDescriptionSelectionDragAnchor = null;
}

function descriptionTableIsCellSelectionTarget(node) {
    return !!(node && !node.closest('.desc-cell-editor'));
}

function descriptionTableRefreshSelectionUi() {
    var table = descriptionTableGetEditor();
    if (!table) return;
}

function descriptionTableDeleteSelectedCells() {
    var selected = descriptionTableGetSelectedCells();
    if (!selected.length) return;

    selected.forEach(function(cell) {
        cell.removeAttribute('rowspan');
        cell.removeAttribute('colspan');
        cell.innerHTML = '&nbsp;';
        cell.classList.remove('is-selected');
    });

    descriptionTableClearSelection();
    if (selected[0]) {
        descriptionTableSelectCell(selected[0], false);
    }

    descriptionTableUpdateSelectionCount();

    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableRemoveImage(button) {
    if (!button) return;
    var wrap = button.closest('.desc-cell-image-wrap');
    if (!wrap) return;
    var cell = wrap.closest('td');
    wrap.remove();
    if (cell) {
        var editor = cell.querySelector('.desc-cell-editor');
        if (editor && editor.textContent.trim() === '') {
            editor.innerHTML = '&nbsp;';
        }
    }
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableEnsureBindings() {
    var table = descriptionTableGetEditor();
    if (!table || table._andisonBound) return;

    table._andisonBound = true;
    table.addEventListener('click', function(event) {
        var selectBtn = event.target.closest('[data-desc-cell-select]');
        if (selectBtn) {
            var selectCell = selectBtn.closest('td');
            if (!selectCell) return;

            if (event.shiftKey && _editDescriptionSelectionAnchor) {
                descriptionTableSelectCellRange(_editDescriptionSelectionAnchor, selectCell);
            } else if (event.ctrlKey || event.metaKey) {
                descriptionTableToggleCellSelection(selectCell);
                _editDescriptionSelectionAnchor = selectCell;
            } else {
                descriptionTableToggleCellSelection(selectCell);
                _editDescriptionSelectionAnchor = selectCell;
            }

            _editDescriptionTableHtmlCache = descriptionTableSerialize();
            event.preventDefault();
            event.stopPropagation();
            return;
        }

        var imageDeleteBtn = event.target.closest('[data-desc-image-delete]');
        if (imageDeleteBtn) {
            descriptionTableRemoveImage(imageDeleteBtn);
            event.preventDefault();
            event.stopPropagation();
            return;
        }

        var cell = event.target.closest('tbody td');
        if (!cell || !table.contains(cell)) return;
        if (!descriptionTableIsCellSelectionTarget(event.target)) {
            return;
        }

        if (event.shiftKey && _editDescriptionSelectionAnchor) {
            descriptionTableSelectCellRange(_editDescriptionSelectionAnchor, cell);
            return;
        }

        if (event.ctrlKey || event.metaKey) {
            descriptionTableToggleCellSelection(cell);
            _editDescriptionSelectionAnchor = cell;
            _editDescriptionTableHtmlCache = descriptionTableSerialize();
            return;
        }

        descriptionTableClearSelection();
        descriptionTableSelectCell(cell, false);
        _editDescriptionSelectionAnchor = cell;
    });

    table.addEventListener('mousedown', function(event) {
        if (!descriptionTableIsCellSelectionTarget(event.target)) return;

        var cell = event.target.closest('tbody td');
        if (!cell || !table.contains(cell)) return;

        event.preventDefault();
        descriptionTableStartDragSelection(cell);
    });

    table.addEventListener('mousemove', function(event) {
        if (!_editDescriptionSelectionDragActive) return;
        if (!descriptionTableIsCellSelectionTarget(event.target)) return;

        var cell = event.target.closest('tbody td');
        if (!cell || !table.contains(cell)) return;
        descriptionTableExtendDragSelection(cell);
    });

    document.addEventListener('mouseup', function() {
        if (_editDescriptionSelectionDragActive) {
            descriptionTableStopDragSelection();
        }
    });

    table.addEventListener('keydown', function(event) {
        if (event.key === 'Backspace' || event.key === 'Delete') {
            var selectedCells = descriptionTableGetSelectedCells();
            if (selectedCells.length) {
                event.preventDefault();
                descriptionTableDeleteSelectedCells();
            }
        }
    });

    table.addEventListener('input', function() {
        if (_editDescriptionMode === 'table') {
            _editDescriptionTableHtmlCache = descriptionTableSerialize();
        }
    });
}

function descriptionTableBuildStarterHtml() {
    return [
        '<table class="desc-custom-table">',
        '<thead>',
        '<tr>',
        '<th class="desc-head-letter">A</th>',
        '<th class="desc-head-letter">B</th>',
        '</tr>',
        '<tr>',
        '<th class="desc-head-label" contenteditable="true">Parameter</th>',
        '<th class="desc-head-label" contenteditable="true">Value</th>',
        '</tr>',
        '</thead>',
        '<tbody>',
        '<tr>',
        '<td><div class="desc-cell-editor" contenteditable="true">Add your description here.</div></td>',
        '<td><div class="desc-cell-editor" contenteditable="true">Use merged cells or upload images if needed.</div></td>',
        '</tr>',
        '</tbody>',
        '</table>'
    ].join('');
}

function descriptionTableBuildTextTable(content) {
    var lines = normalizeEditDescriptionLines(content);
    if (!lines.length) {
        return descriptionTableBuildStarterHtml();
    }

    var rows = lines.map(function(line) {
        var label = line;
        var value = '';

        if (/\t/.test(line)) {
            var tabParts = line.split(/\t+/);
            label = tabParts.shift() || '';
            value = tabParts.join(' ').trim();
        } else if (line.indexOf(':') !== -1) {
            var colonIndex = line.indexOf(':');
            label = line.slice(0, colonIndex);
            value = line.slice(colonIndex + 1);
        } else if (/\s{2,}/.test(line)) {
            var spaceParts = line.split(/\s{2,}/);
            label = spaceParts.shift() || '';
            value = spaceParts.join(' ').trim();
        }

        label = label.trim();
        value = value.trim();

        if (!label && !value) return '';
        if (!value) value = 'Add details here.';

        return '<tr><td>' + escapeEditDescriptionHtml(label) + '</td><td>' + escapeEditDescriptionHtml(value) + '</td></tr>';
    }).filter(function(row) {
        return row !== '';
    }).join('');

    if (!rows) {
        rows = '<tr><td>Item 1</td><td>Add details here.</td></tr>';
    }

    return [
        '<table style="width:100%;border-collapse:collapse;">',
        '<tbody>',
        rows,
        '</tbody>',
        '</table>'
    ].join('');
}

function descriptionTableStripControlGlyphs(value) {
    return String(value || '').replace(/[☐☑□]/g, '');
}

function descriptionTableRender(html) {
    var table = descriptionTableGetEditor();
    if (!table) return;

    var sourceHtml = String(html || '').trim();
    if (!sourceHtml) {
        sourceHtml = descriptionTableBuildStarterHtml();
    }

    var wrapper = document.createElement('div');
    wrapper.innerHTML = sourceHtml;
    var sourceTable = wrapper.querySelector('table');
    if (sourceTable && sourceTable.querySelector('thead') && sourceTable.querySelector('tbody')) {
        table.outerHTML = sourceTable.outerHTML;
        table = descriptionTableGetEditor();
    } else {
        table.innerHTML = descriptionTableBuildStarterHtml();
        var body = table.querySelector('tbody');
        var rows = sourceTable ? Array.prototype.slice.call(sourceTable.querySelectorAll('tr')) : [];

        if (rows.length) {
            body.innerHTML = '';
            rows.forEach(function(row) {
                var cells = Array.prototype.slice.call(row.querySelectorAll('th,td')).map(function(cell) {
                    return descriptionTableStripControlGlyphs(String(cell.innerHTML || '')).trim();
                }).filter(function(value) { return value !== ''; });
                if (!cells.length) return;
                var tr = document.createElement('tr');
                tr.innerHTML = '<td><div class="desc-cell-editor" contenteditable="true">' + (cells[0] || '&nbsp;') + '</div></td>' +
                    '<td><div class="desc-cell-editor" contenteditable="true">' + (cells[1] || '&nbsp;') + '</div></td>';
                body.appendChild(tr);
            });
        }
    }

    descriptionTableInjectCellSelectors(table);
    descriptionTableEnsureCellIds(table);
    descriptionTableEnsureBindings();
    descriptionTableClearSelection();
    _editDescriptionSelectionAnchor = null;
    descriptionTableSelectFirstCell();
    descriptionTableUpdateSelectionCount();
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableSelectFirstCell() {
    var table = descriptionTableGetEditor();
    if (!table) return;
    var firstCell = table.querySelector('tbody td');
    if (firstCell) {
        descriptionTableSelectCell(firstCell, false);
    }
}

function descriptionTableSerialize() {
    var table = descriptionTableGetEditor();
    if (!table) return '';

    var clone = table.cloneNode(true);
    clone.querySelectorAll('.desc-cell-image-delete').forEach(function(btn) {
        btn.remove();
    });
    clone.querySelectorAll('.desc-cell-editor').forEach(function(editor) {
        editor.innerHTML = descriptionTableStripControlGlyphs(editor.innerHTML);
        editor.removeAttribute('contenteditable');
    });
    clone.querySelectorAll('td,th').forEach(function(cell) {
        cell.removeAttribute('data-desc-cell-id');
        cell.classList.remove('is-selected');
    });

    return clone.outerHTML;
}

function descriptionTableExtractPlainText() {
    var table = descriptionTableGetEditor();
    if (!table) return '';

    var lines = Array.prototype.slice.call(table.tBodies[0].rows).map(function(row) {
        var cellTexts = Array.prototype.slice.call(row.querySelectorAll('td .desc-cell-editor')).map(function(cell) {
            return descriptionTableStripControlGlyphs(String(cell.textContent || '')).replace(/\s+/g, ' ').trim();
        }).filter(function(value) {
            return value !== '';
        });

        if (!cellTexts.length) return '';
        if (cellTexts.length === 1) return cellTexts[0];
        return cellTexts[0] + ': ' + cellTexts.slice(1).join(' | ');
    }).filter(function(line) {
        return line !== '';
    });

    return lines.join('\n');
}

function descriptionTableUpdateTextFallback() {
    var textarea = descriptionTableGetTextarea();
    if (!textarea) return;
    textarea.value = descriptionTableExtractPlainText();
}

function descriptionTableInsertStarterTable() {
    _editDescriptionStarterSeeded = true;
    _editDescriptionTableHtmlCache = descriptionTableBuildStarterHtml();
    descriptionTableRender(_editDescriptionTableHtmlCache);
}

function descriptionTableInjectCellSelectors(table) {
    if (!table) return;

    Array.prototype.slice.call(table.querySelectorAll('.desc-cell-select-toggle')).forEach(function(btn) {
        btn.remove();
    });

    Array.prototype.slice.call(table.querySelectorAll('tbody td')).forEach(function(cell) {
        cell.style.position = 'relative';
    });

    Array.prototype.slice.call(table.querySelectorAll('tbody td')).forEach(function(cell) {
        var editor = cell.querySelector('.desc-cell-editor');
        if (!editor) {
            editor = document.createElement('div');
            editor.className = 'desc-cell-editor';
            editor.setAttribute('contenteditable', 'true');
            editor.innerHTML = descriptionTableStripControlGlyphs(cell.textContent || '&nbsp;');

            Array.prototype.slice.call(cell.childNodes).forEach(function(node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    node.remove();
                }
            });

            cell.appendChild(editor);
        } else {
            editor.innerHTML = descriptionTableStripControlGlyphs(editor.innerHTML);
        }
    });

    Array.prototype.slice.call(table.querySelectorAll('.desc-cell-image-wrap')).forEach(function(wrap) {
        if (wrap.querySelector('.desc-cell-image-delete')) return;
        var deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'desc-cell-image-delete';
        deleteBtn.setAttribute('data-desc-image-delete', '1');
        deleteBtn.setAttribute('aria-label', 'Remove image');
        deleteBtn.textContent = 'x';
        wrap.insertBefore(deleteBtn, wrap.firstChild);
    });

    descriptionTableRefreshSelectionUi();
}

function descriptionTableLoadFromCurrentContent() {
    var content = String(descriptionTableGetTextarea() ? descriptionTableGetTextarea().value : '').trim();
    if (descriptionTableHasTableMarkup(content)) {
        _editDescriptionTableHtmlCache = descriptionTableExtractTableHtml(content) || descriptionTableBuildStarterHtml();
        descriptionTableRender(_editDescriptionTableHtmlCache);
        return;
    }

    if (content !== '') {
        _editDescriptionTableHtmlCache = descriptionTableBuildTextTable(content);
        descriptionTableRender(_editDescriptionTableHtmlCache);
        return;
    }

    descriptionTableInsertStarterTable();
}

function descriptionTableBuildGrid() {
    var table = descriptionTableGetEditor();
    var grid = [];
    var positions = new Map();
    if (!table) return { grid: grid, positions: positions, rowCount: 0, colCount: 0 };

    var rows = Array.prototype.slice.call((table.tBodies && table.tBodies[0]) ? table.tBodies[0].rows : []);
    var maxCols = 0;

    for (var r = 0; r < rows.length; r++) {
        if (!grid[r]) grid[r] = [];
        var col = 0;
        Array.prototype.slice.call(rows[r].querySelectorAll('td')).forEach(function(cell) {
            while (grid[r][col]) col += 1;

            var rowSpan = parseInt(cell.getAttribute('rowspan') || '1', 10);
            var colSpan = parseInt(cell.getAttribute('colspan') || '1', 10);
            if (!isFinite(rowSpan) || rowSpan < 1) rowSpan = 1;
            if (!isFinite(colSpan) || colSpan < 1) colSpan = 1;

            positions.set(cell, { row: r, col: col, rowSpan: rowSpan, colSpan: colSpan });

            for (var rr = 0; rr < rowSpan; rr++) {
                if (!grid[r + rr]) grid[r + rr] = [];
                for (var cc = 0; cc < colSpan; cc++) {
                    grid[r + rr][col + cc] = cell;
                }
            }

            col += colSpan;
            if (col > maxCols) maxCols = col;
        });
    }

    return { grid: grid, positions: positions, rowCount: rows.length, colCount: maxCols };
}

function descriptionTableRenderSimpleRows(rows, useHeaderRow) {
    var html = ['<table class="desc-custom-table">', '<thead>',
        '<tr><th class="desc-head-letter">A</th><th class="desc-head-letter">B</th></tr>',
        '<tr><th class="desc-head-label" contenteditable="true">Parameter</th><th class="desc-head-label" contenteditable="true">Value</th></tr>',
        '</thead>', '<tbody>'];
    rows.forEach(function(row, rowIndex) {
        html.push('<tr>');
        html.push('<td contenteditable="true">' + (row[0] || '&nbsp;') + '</td>');
        html.push('<td contenteditable="true">' + (row[1] || '&nbsp;') + '</td>');
        html.push('</tr>');
    });
    html.push('</tbody></table>');
    descriptionTableRender(html.join(''));
}

function descriptionTableGetSelectedGridInfo() {
    var table = descriptionTableGetEditor();
    var selected = descriptionTableGetSelectedCells();
    if (!table || !selected.length) return null;

    var map = descriptionTableBuildGrid();
    var cells = [];
    selected.forEach(function(cell) {
        var info = map.positions.get(cell);
        if (info) cells.push({ cell: cell, info: info });
    });

    if (!cells.length) return null;
    return { map: map, cells: cells };
}

function descriptionTableAddRow() {
    var table = descriptionTableGetEditor();
    if (!table) {
        descriptionTableInsertStarterTable();
        return;
    }

    var info = descriptionTableGetSelectedGridInfo();
    var body = table.tBodies[0];
    var insertAfter = body.rows.length - 1;
    var colCount = Math.max(2, descriptionTableBuildGrid().colCount || 2);

    if (info && info.cells.length) {
        insertAfter = info.cells[0].info.row;
        colCount = Math.max(colCount, info.map.colCount || colCount);
    }

    var row = body.insertRow(insertAfter + 1);
    for (var c = 0; c < colCount; c++) {
        var cell = row.insertCell(-1);
        cell.setAttribute('contenteditable', 'true');
        cell.textContent = 'New cell';
    }

    descriptionTableInjectCellSelectors(table);
    descriptionTableEnsureCellIds(table);
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableAddColumn() {
    var table = descriptionTableGetEditor();
    if (!table) {
        descriptionTableInsertStarterTable();
        return;
    }

    Array.prototype.slice.call(table.tBodies[0].rows).forEach(function(row) {
        var cell = document.createElement('td');
        cell.setAttribute('contenteditable', 'true');
        cell.textContent = 'New cell';
        row.appendChild(cell);
    });

    var headTop = table.tHead ? table.tHead.rows[0] : null;
    var headLabels = table.tHead ? table.tHead.rows[1] : null;
    if (headTop && headLabels) {
        var newLetterIndex = Math.max(2, headTop.cells.length);
        var newLetter = String.fromCharCode(64 + newLetterIndex + 1);
        var topLetter = document.createElement('th');
        topLetter.className = 'desc-head-letter';
        topLetter.textContent = newLetter;
        headTop.appendChild(topLetter);

        var labelCell = document.createElement('th');
        labelCell.className = 'desc-head-label';
        labelCell.setAttribute('contenteditable', 'true');
        labelCell.textContent = 'Column ' + newLetter;
        headLabels.appendChild(labelCell);
    }

    descriptionTableInjectCellSelectors(table);
    descriptionTableEnsureCellIds(table);
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableDeleteRow() {
    var table = descriptionTableGetEditor();
    if (!table || !table.tBodies[0].rows.length) return;

    var info = descriptionTableGetSelectedGridInfo();
    var body = table.tBodies[0];
    var rowsToDelete = [];

    if (info && info.cells.length) {
        info.cells.forEach(function(item) {
            if (rowsToDelete.indexOf(item.info.row) === -1) {
                rowsToDelete.push(item.info.row);
            }
        });
    } else {
        rowsToDelete.push(body.rows.length - 1);
    }

    rowsToDelete.sort(function(a, b) { return b - a; });
    rowsToDelete.forEach(function(rowIndex) {
        if (body.rows[rowIndex]) {
            body.deleteRow(rowIndex);
        }
    });

    if (!body.rows.length) {
        descriptionTableInsertStarterTable();
        return;
    }

    descriptionTableInjectCellSelectors(table);
    descriptionTableEnsureCellIds(table);
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableDeleteColumn() {
    var table = descriptionTableGetEditor();
    if (!table || !table.tBodies[0].rows.length) return;

    var gridInfo = descriptionTableBuildGrid();
    var colIndex = gridInfo.colCount > 0 ? gridInfo.colCount - 1 : 0;
    var selected = descriptionTableGetSelectedGridInfo();
    if (selected && selected.cells.length) {
        colIndex = selected.cells[0].info.col;
    }

    Array.prototype.slice.call(table.tBodies[0].rows).forEach(function(row, rowIdx) {
        var cells = Array.prototype.slice.call(row.querySelectorAll('td'));
        for (var i = 0; i < cells.length; i++) {
            var cell = cells[i];
            var info = gridInfo.positions.get(cell);
            if (info && info.col <= colIndex && colIndex < (info.col + info.colSpan)) {
                if (info.colSpan > 1) {
                    cell.setAttribute('colspan', String(info.colSpan - 1));
                } else {
                    cell.remove();
                }
                break;
            }
        }
    });

        var headerRows = table.tHead ? Array.prototype.slice.call(table.tHead.rows) : [];
        headerRows.forEach(function(headerRow) {
            var headerCells = Array.prototype.slice.call(headerRow.children).filter(function(cell) {
                return !cell.classList.contains('desc-head-actions');
            });
            var headerCell = headerCells[colIndex];
            if (headerCell) headerCell.remove();
        });

    if (!table.tBodies[0].rows.length || !table.tBodies[0].rows[0].querySelectorAll('td').length) {
        descriptionTableInsertStarterTable();
        return;
    }

    descriptionTableInjectCellSelectors(table);
    descriptionTableEnsureCellIds(table);
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableMergeCells() {
    var selected = descriptionTableGetSelectedGridInfo();
    if (!selected || selected.cells.length < 2) return;

    var minRow = Infinity;
    var minCol = Infinity;
    var maxRow = -1;
    var maxCol = -1;
    var map = selected.map;
    var selectedSet = new Set(selected.cells.map(function(item) { return item.cell; }));

    selected.cells.forEach(function(item) {
        var info = item.info;
        if (info.row < minRow) minRow = info.row;
        if (info.col < minCol) minCol = info.col;
        if ((info.row + info.rowSpan - 1) > maxRow) maxRow = info.row + info.rowSpan - 1;
        if ((info.col + info.colSpan - 1) > maxCol) maxCol = info.col + info.colSpan - 1;
    });

    for (var r = minRow; r <= maxRow; r++) {
        for (var c = minCol; c <= maxCol; c++) {
            var cell = map.grid[r] && map.grid[r][c];
            if (!cell || !selectedSet.has(cell)) {
                customAlert('Selected cells must form one continuous rectangle.');
                return;
            }
        }
    }

    var anchor = null;
    selected.cells.forEach(function(item) {
        if (!anchor || item.info.row < anchor.info.row || (item.info.row === anchor.info.row && item.info.col < anchor.info.col)) {
            anchor = item;
        }
    });

    selected.cells.forEach(function(item) {
        if (item.cell !== anchor.cell) {
            item.cell.remove();
        }
    });

    anchor.cell.setAttribute('rowspan', String((maxRow - minRow) + 1));
    anchor.cell.setAttribute('colspan', String((maxCol - minCol) + 1));
    descriptionTableEnsureCellIds(descriptionTableGetEditor());
    descriptionTableClearSelection();
    descriptionTableSelectCell(anchor.cell, false);
    _editDescriptionSelectionAnchor = anchor.cell;
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableSplitCell() {
    var selected = descriptionTableGetSelectedGridInfo();
    if (!selected || !selected.cells.length) return;

    var anchor = selected.cells[0];
    var cell = anchor.cell;
    var rowSpan = parseInt(cell.getAttribute('rowspan') || '1', 10);
    var colSpan = parseInt(cell.getAttribute('colspan') || '1', 10);
    if (!isFinite(rowSpan) || rowSpan < 2) rowSpan = 1;
    if (!isFinite(colSpan) || colSpan < 2) colSpan = 1;
    if (rowSpan === 1 && colSpan === 1) return;

    var map = descriptionTableBuildGrid();
    var matrix = [];
    for (var r = 0; r < map.rowCount; r++) {
        matrix[r] = [];
        for (var c = 0; c < map.colCount; c++) {
            var source = map.grid[r] && map.grid[r][c];
            if (!source) {
                matrix[r][c] = '';
                continue;
            }

            var info = map.positions.get(source);
            if (info && info.row === r && info.col === c) {
                matrix[r][c] = String(source.innerHTML || '').trim() || '&nbsp;';
            } else {
                matrix[r][c] = '';
            }
        }
    }

    descriptionTableRenderSimpleRows(matrix, false);
    descriptionTableInjectCellSelectors(descriptionTableGetEditor());
    descriptionTableRefreshSelectionUi();
    descriptionTableUpdateSelectionCount();
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableInsertImageIntoCell(cell, url) {
    if (!cell || !url) return;
    var editor = cell.querySelector('.desc-cell-editor') || cell;
    editor.insertAdjacentHTML('beforeend', descriptionTableBuildImageWrapper(url, 'Description image'));
    _editDescriptionTableHtmlCache = descriptionTableSerialize();
}

function descriptionTableUploadImage(file) {
    return new Promise(function(resolve, reject) {
        var formData = new FormData();
        formData.append('action', 'upload_brand_description_image');
        formData.append('brand', _selectedBrandKeyForEditor || '');
        formData.append('description_image_file', file, file.name || 'description-image');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'products.php?brand=' + encodeURIComponent(_selectedBrandKeyForEditor || ''));
        xhr.withCredentials = true;

        xhr.onload = function() {
            if (xhr.status < 200 || xhr.status >= 300) {
                reject('Upload failed (' + xhr.status + ')');
                return;
            }

            var json;
            try {
                json = JSON.parse(xhr.responseText);
            } catch (err) {
                reject('Invalid upload response.');
                return;
            }

            if (!json || typeof json.location !== 'string' || json.location === '') {
                reject((json && json.error) ? json.error : 'Upload failed.');
                return;
            }

            resolve(json.location);
        };

        xhr.onerror = function() {
            reject('Image upload request failed.');
        };

        xhr.send(formData);
    });
}

function descriptionTableInsertImagePrompt() {
    var fileInput = document.getElementById('descriptionTableImageInput');
    if (!fileInput) return;
    _editDescriptionPendingImageCell = descriptionTableGetSelectedCells()[0] || (descriptionTableGetEditor() ? descriptionTableGetEditor().querySelector('tbody td') : null);
    fileInput.value = '';
    fileInput.click();
}

function descriptionTableHandleImageInput(input) {
    var file = input && input.files && input.files[0] ? input.files[0] : null;
    if (!file) return;

    var targetCell = _editDescriptionPendingImageCell || descriptionTableGetSelectedCells()[0] || (descriptionTableGetEditor() ? descriptionTableGetEditor().querySelector('tbody td') : null);
    _editDescriptionPendingImageCell = null;

    descriptionTableUploadImage(file).then(function(url) {
        descriptionTableInsertImageIntoCell(targetCell, url);
    }).catch(function(err) {
        customAlert(err || 'Image upload failed.');
    }).finally(function() {
        input.value = '';
    });
}

function descriptionTableSwitchToTableMode(userTriggered, options) {
    var panel = descriptionTableGetPanel();
    var textarea = descriptionTableGetTextarea();
    if (panel) panel.style.display = 'block';
    if (textarea) textarea.style.display = 'none';

    if (_editDescriptionTableHtmlCache && descriptionTableHasTableMarkup(_editDescriptionTableHtmlCache)) {
        descriptionTableRender(_editDescriptionTableHtmlCache);
    } else {
        descriptionTableLoadFromCurrentContent();
    }

    if (userTriggered && (!options || !options.skipFocus)) {
        setTimeout(function() {
            var cell = descriptionTableGetEditor() ? descriptionTableGetEditor().querySelector('tbody td') : null;
            if (cell) cell.focus();
        }, 60);
    }
}

function descriptionTableSwitchToPlainMode() {
    var panel = descriptionTableGetPanel();
    var textarea = descriptionTableGetTextarea();
    if (_editDescriptionMode === 'table') {
        _editDescriptionTableHtmlCache = descriptionTableSerialize();
        if (textarea) textarea.value = descriptionTableExtractPlainText();
    }
    if (panel) panel.style.display = 'none';
    if (textarea) textarea.style.display = 'block';
}

function setEditDescriptionMode(mode, userTriggered, options) {
    options = options || {};
    var normalizedMode = mode === 'table' ? 'table' : 'plain';
    var previousMode = _editDescriptionMode;
    _editDescriptionMode = normalizedMode;

    var modeInput = document.getElementById('editDescriptionMode');
    var plainBtn = document.getElementById('descModePlainBtn');
    var tableBtn = document.getElementById('descModeTableBtn');
    var hint = document.getElementById('editDescriptionModeHint');

    if (modeInput) modeInput.value = normalizedMode;
    if (plainBtn) plainBtn.classList.toggle('is-active', normalizedMode === 'plain');
    if (tableBtn) tableBtn.classList.toggle('is-active', normalizedMode === 'table');

    if (hint) {
        hint.innerHTML = normalizedMode === 'table'
            ? '<i class="bi bi-info-circle"></i> Custom Table Maker mode: select cells, merge them, add rows/columns, and upload images directly into cells.'
            : '<i class="bi bi-info-circle"></i> Plain Text mode for normal descriptions.';
    }

    if (normalizedMode === 'table') {
        descriptionTableSwitchToTableMode(userTriggered, options);
        _editDescriptionStarterSeeded = true;
    } else {
        if (previousMode === 'table') {
            descriptionTableSwitchToPlainMode();
        } else {
            var panel = descriptionTableGetPanel();
            var textarea = descriptionTableGetTextarea();
            if (panel) panel.style.display = 'none';
            if (textarea) textarea.style.display = 'block';
        }
    }
}

function convertEditDescriptionTextToTableRows() {
    var content = String(descriptionTableGetTextarea() ? descriptionTableGetTextarea().value : '');
    var html = descriptionTableBuildTextTable(content);
    _editDescriptionTableHtmlCache = html;
    setEditDescriptionMode('table', true, { skipFocus: false });
    descriptionTableRender(html);
}

function descriptionTableSyncBeforeSubmit() {
    if (_editDescriptionMode === 'table') {
        _editDescriptionTableHtmlCache = descriptionTableSerialize();
        if (descriptionTableGetTextarea()) {
            descriptionTableGetTextarea().value = _editDescriptionTableHtmlCache;
        }
    }
}

function openEditModal(index, name, model, type, price, badge, description, specifications, image, catId, subId, subSubId, imagesJson, datasheet) {
    var modal = ensureGlobalProductModal();
    if (!modal) return;
    _editDescriptionStarterSeeded = false;
    _editDescriptionTableHtmlCache = '';
    _editDescriptionMode = 'plain';
    document.getElementById('editIndex').value = index;
    document.getElementById('editProductName').value = name;
    document.getElementById('editModel').value = model;
    document.getElementById('editType').value = type;
    syncTypePresetFromInput();
    document.getElementById('editPrice').value = price;
    document.getElementById('editBadge').value = (badge === 'New Arrival') ? 'New' : badge;
    document.getElementById('editDescription').value = description;
    setEditDescriptionMode(isLikelyRichDescription(description) ? 'table' : 'plain', false);
    setSpecificationsEditor(specifications);

    // Populate datasheet
    setDatasheetPreview(datasheet || '');

    // Populate image slots
    _existingUrls = Array(MAX_PRODUCT_IMAGES).fill('');
    _previewUrls  = Array(MAX_PRODUCT_IMAGES).fill(null);
    var imgArr = [];
    try { imgArr = JSON.parse(imagesJson || '[]'); } catch(e) {}
    if (!Array.isArray(imgArr) || imgArr.length === 0) { imgArr = image ? [image] : []; }
    for (var ii = 0; ii < Math.min(imgArr.length, MAX_PRODUCT_IMAGES); ii++) {
        if (imgArr[ii]) { _existingUrls[ii] = imgArr[ii]; _previewUrls[ii] = imgArr[ii]; }
    }
    for (var jj = 0; jj < MAX_PRODUCT_IMAGES; jj++) {
        var fi = document.getElementById('imageFile_' + jj);
        if (fi) fi.value = '';
    }
    var bulkInput = document.getElementById('bulkImageFiles');
    if (bulkInput) bulkInput.value = '';
    renderImageSlots();

    // Resolve existing category path (supports records saved at sub-subcategory level).
    var resolvedPath = resolveCategoryPath(catId || '', subId || '', subSubId || '');
    var resolvedSubId = resolvedPath.subId || '';
    var resolvedSubSubId = resolvedPath.subSubId || '';

    // Preserve/initialize hidden values.
    document.getElementById('finalCategoryId').value    = catId || '';
    document.getElementById('finalSubcategoryId').value = resolvedSubId || subId || '';
    var finalSubSubInput = document.getElementById('finalSubSubcategoryId');
    if (finalSubSubInput) {
        finalSubSubInput.value = resolvedSubSubId || subSubId || '';
    }

    // Populate category/subcategory/sub-subcategory dropdowns.
    var catSel = document.getElementById('editCategory');
    catSel.value = catId || '';
    populateCategorySubcategories(resolvedSubId || '', resolvedSubSubId || '');

    // If dropdowns couldn't pre-fill, keep current hidden value.
    if (!document.getElementById('finalCategoryId').value && catId) {
        document.getElementById('finalCategoryId').value = catId;
    }
    if (!document.getElementById('finalSubcategoryId').value && (subId || resolvedSubSubId || resolvedSubId)) {
        document.getElementById('finalSubcategoryId').value = resolvedSubId || subId || '';
    }
    if (finalSubSubInput && !finalSubSubInput.value && (subSubId || resolvedSubSubId)) {
        finalSubSubInput.value = resolvedSubSubId || subSubId || '';
    }
    refreshCategoryPreview();

    modal.style.display = 'flex';
    // Scroll modal body back to top so category dropdowns are visible
    setTimeout(function(){ var b = modal.querySelector('.edit-modal-body'); if(b) b.scrollTop = 0; }, 10);
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '10px';
    document.body.classList.add('modal-open');
}

function closeEditModal() {
    var modal = document.getElementById('editProductModal');
    setEditDescriptionMode('plain', false);
    _editDescriptionStarterSeeded = false;
    _editDescriptionTableHtmlCache = '';
    _editDescriptionMode = 'plain';
    modal.style.display = 'none';
    // Restore background scrolling
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.classList.remove('modal-open');
}

// Attach click handlers to edit buttons
document.querySelectorAll('.edit-product-btn').forEach(function(btn){
    btn.addEventListener('click', function(){
        var index = this.getAttribute('data-index');
        var name = this.getAttribute('data-name');
        var model = this.getAttribute('data-model');
        var type = this.getAttribute('data-type');
        var price = this.getAttribute('data-price');
        var badge = this.getAttribute('data-badge');
        var description = this.getAttribute('data-description');
        var specifications = this.getAttribute('data-specifications');
        var specImage = this.getAttribute('data-spec-image') || '';
        var image  = this.getAttribute('data-image');
        var images = this.getAttribute('data-images') || '[]';
        var catId     = this.getAttribute('data-category');
        var subId     = this.getAttribute('data-subcategory');
        var subSubId  = this.getAttribute('data-subsubcategory');
        var datasheet = this.getAttribute('data-datasheet') || '';
        openEditModal(index, name, model, type, price, badge, description, specifications, image, catId, subId, subSubId, images, datasheet);
        if (specImage && !document.getElementById('existingSpecImageInput').value) {
            setSpecImagePreview(specImage);
        }
    });
});

// Close modal on Escape key
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') {
        var modal = document.getElementById('editProductModal');
        if (modal && modal.style.display === 'flex') {
            closeEditModal();
        }
    }
});

// Handle edit form submission with confirmation
document.querySelector('.edit-product-form').addEventListener('submit', function(e){
    e.preventDefault();
    var f = this;
    var action = f.querySelector('[name="action"]').value;
    var message = action === 'add_product' 
        ? 'Are you sure you want to add this product?' 
        : 'Are you sure you want to save changes to this product?';
    
    customConfirm(message).then(function(confirmed){
        if (confirmed) {
            // Pre-submit: ensure hidden category inputs match current dropdown state
            var catSel = document.getElementById('editCategory');
            if (catSel) document.getElementById('finalCategoryId').value = catSel.value || '';
            updateFinalSubcategory();
            descriptionTableSyncBeforeSubmit();
            syncSpecificationsHiddenField();
            f.submit();
        } else if (action !== 'add_product') {
            // Reset modal state after cancel
            var modal = document.getElementById('editProductModal');
            var modalHeader = modal.querySelector('.edit-modal-header h2');
            modalHeader.innerHTML = '<i class="bi bi-pencil"></i> Edit Product';
            f.querySelector('[name="action"]').value = 'update_product';
            f.querySelector('button[type="submit"]').innerHTML = '<i class="bi bi-save"></i> Save Changes';
        }
    });
});

// Brand description form
document.querySelectorAll('.brand-desc-form').forEach(function(form){
    form.addEventListener('submit', function(e){
        e.preventDefault();
        var f = this;
        customConfirm('Are you sure you want to save the brand description?').then(function(confirmed){
            if (confirmed) f.submit();
        });
    });
});

// Add button to open modal for new product removed - using top button instead

function openAddProductModal() {
    var modal = ensureGlobalProductModal();
    if (!modal) return;
    var modalHeader = modal.querySelector('.edit-modal-header h2');
    var form = modal.querySelector('.edit-product-form');
    
    // Change modal to "Add Product" mode
    modalHeader.innerHTML = '<i class="bi bi-plus-circle"></i> Add New Product';
    form.action = 'products.php?brand=<?php echo urlencode($selectedBrandKey); ?>';
    form.querySelector('[name="action"]').value = 'add_product';
    
    // Clear all fields
    document.getElementById('editIndex').value = '';
    document.getElementById('editProductName').value = '';
    document.getElementById('editModel').value = '';
    document.getElementById('editType').value = '';
    syncTypePresetFromInput();
    document.getElementById('editPrice').value = '';
    document.getElementById('editBadge').value = '';
    document.getElementById('editDescription').value = '';
    setEditDescriptionMode('plain', false);
    setSpecificationsEditor('');
    setSpecImagePreview('');
    // Clear datasheet
    setDatasheetPreview('');
    // Clear image slots
    _existingUrls = Array(MAX_PRODUCT_IMAGES).fill('');
    _previewUrls  = Array(MAX_PRODUCT_IMAGES).fill(null);
    for (var _s = 0; _s < MAX_PRODUCT_IMAGES; _s++) { var _fi = document.getElementById('imageFile_' + _s); if (_fi) _fi.value = ''; }
    var _bulkInput = document.getElementById('bulkImageFiles');
    if (_bulkInput) _bulkInput.value = '';
    renderImageSlots();
    document.getElementById('editCategory').value = '';
    document.getElementById('finalCategoryId').value    = '';
    document.getElementById('finalSubcategoryId').value = '';
    var finalSubSubInput = document.getElementById('finalSubSubcategoryId');
    if (finalSubSubInput) finalSubSubInput.value = '';
    populateCategorySubcategories('', '');
    
    // Change submit button text
    form.querySelector('button[type="submit"]').innerHTML = '<i class="bi bi-save"></i> Add Product';
    
    modal.style.display = 'flex';
    // Scroll modal body back to top so category dropdowns are visible
    setTimeout(function(){ var b = modal.querySelector('.edit-modal-body'); if(b) b.scrollTop = 0; }, 10);
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '10px';
    document.body.classList.add('modal-open');
}

// Datasheet helpers
function setDatasheetPreview(url) {
    document.getElementById('existingDatasheetInput').value = url || '';
    var preview = document.getElementById('datasheetPreview');
    var fi = document.getElementById('datasheetFile');
    if (fi) fi.value = '';
    if (url) {
        var parts = url.split('/');
        document.getElementById('datasheetFileName').textContent = decodeURIComponent(parts[parts.length - 1]) || 'datasheet.pdf';
        document.getElementById('datasheetLink').href = url;
        preview.style.display = 'flex';
    } else {
        preview.style.display = 'none';
    }
}
function removeDatasheet() {
    setDatasheetPreview('');
}
function handleDatasheetSelect(input) {
    if (!input.files || !input.files.length) return;
    var file = input.files[0];
    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
        customAlert('Please select a PDF file.'); input.value = ''; return;
    }
    if (file.size > 10 * 1024 * 1024) {
        customAlert('File size must be less than 10 MB.'); input.value = ''; return;
    }
    // Show selected file name in preview (actual upload happens on form submit)
    document.getElementById('existingDatasheetInput').value = '';
    document.getElementById('datasheetFileName').textContent = file.name;
    document.getElementById('datasheetLink').href = '#';
    document.getElementById('datasheetPreview').style.display = 'flex';
}

function setSpecImagePreview(url) {
    var hidden = document.getElementById('existingSpecImageInput');
    var preview = document.getElementById('specImagePreview');
    var input = document.getElementById('specImageFile');
    var thumb = document.getElementById('specImageThumb');
    var fileNameEl = document.getElementById('specImageFileName');
    var link = document.getElementById('specImageLink');

    if (!hidden || !preview || !thumb || !fileNameEl || !link) return;

    hidden.value = url || '';
    if (input) input.value = '';

    if (url) {
        var parts = String(url).split('/');
        fileNameEl.textContent = decodeURIComponent(parts[parts.length - 1]) || 'spec-image.jpg';
        thumb.src = url;
        link.href = url;
        preview.style.display = 'flex';
    } else {
        thumb.src = '';
        link.href = '#';
        preview.style.display = 'none';
    }
}

function removeSpecImage() {
    setSpecImagePreview('');
}

function handleSpecImageSelect(input) {
    if (!input || !input.files || !input.files.length) return;
    var file = input.files[0];
    if (!file.type || file.type.indexOf('image/') !== 0) {
        customAlert('Please select an image file.');
        input.value = '';
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        customAlert('Image size must be less than 10 MB.');
        input.value = '';
        return;
    }

    var preview = document.getElementById('specImagePreview');
    var thumb = document.getElementById('specImageThumb');
    var fileNameEl = document.getElementById('specImageFileName');
    var link = document.getElementById('specImageLink');
    var hidden = document.getElementById('existingSpecImageInput');

    if (!preview || !thumb || !fileNameEl || !link || !hidden) return;

    hidden.value = '';
    fileNameEl.textContent = file.name;
    link.href = '#';
    preview.style.display = 'flex';

    var reader = new FileReader();
    reader.onload = function(e) {
        thumb.src = String((e && e.target && e.target.result) || '');
    };
    reader.readAsDataURL(file);
}

// Image preview modal
function openImagePreview(src) {
    var modal = document.createElement('div');
    modal.style.cssText = 'position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.8);cursor:pointer;';
    
    var img = document.createElement('img');
    img.src = src;
    img.style.cssText = 'max-width:90%;max-height:90%;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,0.5);';
    
    modal.appendChild(img);
    document.body.appendChild(modal);
    
    modal.addEventListener('click', function(){
        document.body.removeChild(modal);
    });
}

// Product add/edit form
document.querySelectorAll('.product-form').forEach(function(form){
    form.addEventListener('submit', function(e){
        e.preventDefault();
        var f = this;
        customConfirm('Are you sure you want to save this product?').then(function(confirmed){
            if (confirmed) f.submit();
        });
    });
});

var _typeInput = document.getElementById('editType');
if (_typeInput) {
    _typeInput.addEventListener('input', syncTypePresetFromInput);
}

var _specTextInput = document.getElementById('editSpecificationsText');
if (_specTextInput) {
    _specTextInput.addEventListener('input', syncSpecificationsHiddenField);
}

setSpecificationsEditor('');

// Delete forms
document.querySelectorAll('.delete-form').forEach(function(form){
    form.addEventListener('submit', function(e){
        e.preventDefault();
        var f = this;
        customConfirm('Are you sure you want to delete this product? This action cannot be undone.').then(function(confirmed){
            if (confirmed) f.submit();
        });
    });
});

// Custom alert function
function customAlert(message) {
    alert(message);
}

// Modal resize handle (more reliable than CSS native resize on some browsers)
var _editModalResizeState = null;

function initEditModalResizer() {
    var modal = document.getElementById('editProductModal');
    if (!modal) return;

    var content = modal.querySelector('.edit-modal-content');
    var handle = modal.querySelector('.edit-modal-resize-handle');
    if (!content || !handle || content.getAttribute('data-resizer-init') === '1') return;

    content.setAttribute('data-resizer-init', '1');

    function stopResize() {
        if (!_editModalResizeState) return;
        _editModalResizeState = null;
        content.classList.remove('is-resizing');
        document.body.style.userSelect = '';
        document.removeEventListener('mousemove', onResizeMove);
        document.removeEventListener('mouseup', stopResize);
    }

    function onResizeMove(e) {
        if (!_editModalResizeState) return;

        var dx = e.clientX - _editModalResizeState.startX;
        var dy = e.clientY - _editModalResizeState.startY;

        var viewportMaxW = Math.max(420, window.innerWidth - 24);
        var viewportMaxH = Math.max(420, window.innerHeight - 24);
        var minW = Math.min(520, viewportMaxW);
        var minH = Math.min(520, viewportMaxH);

        var nextW = _editModalResizeState.startW + dx;
        var nextH = _editModalResizeState.startH + dy;

        nextW = Math.max(minW, Math.min(viewportMaxW, nextW));
        nextH = Math.max(minH, Math.min(viewportMaxH, nextH));

        content.style.width = nextW + 'px';
        content.style.height = nextH + 'px';
    }

    handle.addEventListener('mousedown', function(e) {
        if (window.matchMedia('(pointer: coarse)').matches) return;

        e.preventDefault();
        e.stopPropagation();

        var rect = content.getBoundingClientRect();
        _editModalResizeState = {
            startX: e.clientX,
            startY: e.clientY,
            startW: rect.width,
            startH: rect.height,
        };

        content.classList.add('is-resizing');
        document.body.style.userSelect = 'none';
        document.addEventListener('mousemove', onResizeMove);
        document.addEventListener('mouseup', stopResize);
    });
}

initEditModalResizer();
</script>

<script>
// Product search functionality
(function(){
    var searchInput = document.getElementById('productSearch');
    var productRows = document.querySelectorAll('.product-row');
    
    if (searchInput && productRows.length > 0) {
        searchInput.addEventListener('input', function(){
            var searchTerm = this.value.toLowerCase().trim();
            var visibleCount = 0;
            
            productRows.forEach(function(row){
                var model = row.getAttribute('data-model') || '';
                var type = row.getAttribute('data-type') || '';
                var badge = row.getAttribute('data-badge') || '';
                
                var matches = model.includes(searchTerm) || 
                             type.includes(searchTerm) || 
                             badge.includes(searchTerm);
                
                if (matches || searchTerm === '') {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show message if no results
            var noResultsRow = document.getElementById('noSearchResults');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsRow) {
                    var tbody = document.querySelector('#productsTable tbody');
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noSearchResults';
                    noResultsRow.innerHTML = '<td colspan="7" style="background:#fff;border-radius:12px;padding:14px;border:1px dashed rgba(43,17,219,0.25);color:#374151;text-align:center;"><i class="bi bi-search"></i> No products found matching "' + searchTerm + '"</td>';
                    tbody.appendChild(noResultsRow);
                } else {
                    noResultsRow.querySelector('td').innerHTML = '<i class="bi bi-search"></i> No products found matching "' + searchTerm + '"';
                    noResultsRow.style.display = '';
                }
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });
    }
})();

// Bulk selection functionality
(function(){
    var selectAllCheckbox = document.getElementById('selectAllCheckbox');
    var productCheckboxes = document.querySelectorAll('.product-checkbox');
    var bulkActionsBar = document.getElementById('bulkActionsBar');
    var selectedCountText = document.getElementById('selectedCountText');
    var selectedProductsContainer = document.getElementById('selectedProductsContainer');
    
    if (!selectAllCheckbox || productCheckboxes.length === 0) return;
    
    function updateBulkActionsBar() {
        var checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        
        if (checkedCount > 0) {
            bulkActionsBar.style.display = 'flex';
            selectedCountText.textContent = checkedCount + ' product' + (checkedCount !== 1 ? 's' : '') + ' selected';
            
            // Update hidden inputs with selected product indices
            selectedProductsContainer.innerHTML = '';
            document.querySelectorAll('.product-checkbox:checked').forEach(function(checkbox) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_products[]';
                input.value = checkbox.value;
                selectedProductsContainer.appendChild(input);
            });
        } else {
            bulkActionsBar.style.display = 'none';
            selectedProductsContainer.innerHTML = '';
        }
        
        // Update select all checkbox state
        var totalVisible = document.querySelectorAll('.product-row:not([style*="display: none"]) .product-checkbox').length;
        var checkedVisible = document.querySelectorAll('.product-row:not([style*="display: none"]) .product-checkbox:checked').length;
        selectAllCheckbox.checked = totalVisible > 0 && checkedVisible === totalVisible;
        selectAllCheckbox.indeterminate = checkedVisible > 0 && checkedVisible < totalVisible;
    }
    
    // Handle individual checkboxes
    productCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateBulkActionsBar);
    });
    
    // Handle select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        var isChecked = this.checked;
        document.querySelectorAll('.product-row:not([style*="display: none"]) .product-checkbox').forEach(function(checkbox) {
            checkbox.checked = isChecked;
        });
        updateBulkActionsBar();
    });
})();
</script>

<!-- ── CSV Import Modal ──────────────────────────────────────────────────── -->
<div id="importCsvModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:14px;width:100%;max-width:480px;margin:20px;box-shadow:0 20px 60px rgba(0,0,0,0.25);overflow:hidden;">
        <div style="padding:20px 24px 16px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;">
            <div style="font-size:16px;font-weight:700;color:#111827;"><i class="bi bi-upload" style="margin-right:8px;color:#6b7280;"></i>Import Products from CSV</div>
            <button type="button" onclick="closeImportCsvModal();" style="background:none;border:none;font-size:20px;color:#9ca3af;cursor:pointer;line-height:1;">&times;</button>
        </div>
        <div style="padding:20px 24px;">
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:12px 14px;margin-bottom:18px;font-size:12px;color:#0369a1;line-height:1.6;">
                <strong>Instructions:</strong><br>
                1. Download the template CSV below.<br>
                2. Fill in the brand and product details — the brand column decides where each row is imported and can create a new brand if needed.<br>
                3. <code>model</code> and <code>type</code> columns are required.<br>
                4. Use image/datasheet URLs (not file uploads).<br>
                5. Save as CSV (UTF-8) and import here.
            </div>
            <a href="products.php?action=download_csv_template" download style="display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:600;color:#2563eb;text-decoration:none;background:#eff6ff;border:1px solid #bfdbfe;padding:7px 14px;border-radius:7px;margin-bottom:18px;">
                <i class="bi bi-file-earmark-spreadsheet"></i> Download Template (CSV)
            </a>
            <form id="importCsvForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import_csv">
                <input type="hidden" name="brand" id="importCsvBrand" value="<?php echo htmlspecialchars($selectedBrandKey, ENT_QUOTES); ?>">
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Select CSV File</label>
                    <input type="file" name="csv_file" id="csvFileInput" accept=".csv,text/csv" required
                        style="width:100%;font-size:12px;padding:8px 10px;border:1px solid #d1d5db;border-radius:7px;cursor:pointer;box-sizing:border-box;">
                    <div id="csvFileInfo" style="font-size:11px;color:#6b7280;margin-top:4px;"></div>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;">
                    <button type="button" onclick="closeImportCsvModal();" style="padding:8px 18px;font-size:12px;border-radius:7px;border:1px solid #d1d5db;background:white;color:#374151;cursor:pointer;font-weight:500;">Cancel</button>
                    <button type="submit" id="importCsvSubmit" style="padding:8px 18px;font-size:12px;border-radius:7px;border:none;background:#2563eb;color:white;cursor:pointer;font-weight:600;"><i class="bi bi-upload"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openImportCsvModal() {
    var modal = document.getElementById('importCsvModal');
    modal.style.display = 'flex';
    document.getElementById('csvFileInput').value = '';
    document.getElementById('csvFileInfo').textContent = '';
}
function closeImportCsvModal() {
    document.getElementById('importCsvModal').style.display = 'none';
}
document.getElementById('csvFileInput').addEventListener('change', function() {
    var info = document.getElementById('csvFileInfo');
    if (this.files && this.files[0]) {
        var size = (this.files[0].size / 1024).toFixed(1);
        info.textContent = this.files[0].name + ' (' + size + ' KB)';
    } else {
        info.textContent = '';
    }
});
document.getElementById('importCsvModal').addEventListener('click', function(e) {
    if (e.target === this) closeImportCsvModal();
});
document.getElementById('importCsvForm').addEventListener('submit', function() {
    var btn = document.getElementById('importCsvSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Importing...';
});

function toggleAddBrandPanel() {
    var panel = document.getElementById('addBrandPanel');
    if (!panel) {
        return;
    }
    var nextDisplay = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
    panel.style.display = nextDisplay;
    if (nextDisplay === 'block') {
        var input = document.getElementById('newBrandName');
        if (input) {
            setTimeout(function(){ input.focus(); }, 20);
        }
    }
}

function toggleReorderBrandPanel() {
    var panel = document.getElementById('reorderBrandPanel');
    if (!panel) {
        return;
    }
    var nextDisplay = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
    panel.style.display = nextDisplay;
}

(function(){
    var brandList = document.getElementById('brandReorderList');
    var reorderForm = document.getElementById('reorderBrandsForm');
    var brandOrderInput = document.getElementById('brandOrderInput');

    if (!brandList || !reorderForm || !brandOrderInput) {
        return;
    }

    var draggedItem = null;

    function collectBrandOrder() {
        return Array.from(brandList.querySelectorAll('.brand-reorder-item')).map(function(item) {
            return item.getAttribute('data-brand-label') || '';
        }).filter(function(label) {
            return label !== '';
        });
    }

    function syncBrandOrderInput() {
        brandOrderInput.value = JSON.stringify(collectBrandOrder());
    }

    function clearStates() {
        brandList.querySelectorAll('.brand-reorder-item').forEach(function(item) {
            item.classList.remove('drag-over');
            item.classList.remove('is-dragging');
        });
    }

    function getInsertBeforeItem(container, clientY) {
        var items = Array.from(container.querySelectorAll('.brand-reorder-item:not(.is-dragging)'));
        return items.reduce(function(closest, item) {
            var box = item.getBoundingClientRect();
            var offset = clientY - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: item };
            }
            return closest;
        }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
    }

    brandList.querySelectorAll('.brand-reorder-item').forEach(function(item) {
        item.addEventListener('dragstart', function(event) {
            draggedItem = item;
            item.classList.add('is-dragging');
            if (event.dataTransfer) {
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', item.getAttribute('data-brand-label') || '');
            }
        });

        item.addEventListener('dragend', function() {
            draggedItem = null;
            clearStates();
            syncBrandOrderInput();
        });

        item.addEventListener('dragover', function(event) {
            if (!draggedItem || draggedItem === item) {
                return;
            }
            event.preventDefault();
            var insertBeforeItem = getInsertBeforeItem(brandList, event.clientY);
            clearStates();
            item.classList.add('drag-over');
            if (insertBeforeItem == null) {
                brandList.appendChild(draggedItem);
            } else {
                brandList.insertBefore(draggedItem, insertBeforeItem);
            }
            syncBrandOrderInput();
        });

        item.addEventListener('drop', function(event) {
            event.preventDefault();
            syncBrandOrderInput();
        });
    });

    reorderForm.addEventListener('submit', function() {
        syncBrandOrderInput();
    });

    syncBrandOrderInput();
})();

// ── Product Reordering (Drag & Drop) ──
(function(){
    var productTable = document.querySelector('.prod-table tbody');
    if (!productTable) return;

    var draggedRow = null;

    function syncProductOrderInput() {
        var input = document.getElementById('productOrderInput');
        if (!input) return;

        var rows = productTable.querySelectorAll('.product-row');
        var orderTokens = [];
        rows.forEach(function(row) {
            var token = row.getAttribute('data-order-token');
            if (token) {
                orderTokens.push(token);
            }
        });

        input.value = JSON.stringify(orderTokens);
    }

    function clearStates() {
        productTable.querySelectorAll('.product-row').forEach(function(item) {
            item.classList.remove('drag-over');
        });
    }

    function getInsertBeforeItem(container, clientY) {
        var items = container.querySelectorAll('.product-row');
        var closest = { offset: Number.NEGATIVE_INFINITY, element: null };

        items.forEach(function(item) {
            var box = item.getBoundingClientRect();
            var offset = clientY - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                closest = { offset: offset, element: item };
            }
        });

        return closest.element;
    }

    productTable.querySelectorAll('.product-drag-handle').forEach(function(handle) {
        handle.addEventListener('dragstart', function(event) {
            draggedRow = handle.closest('.product-row');
            if (draggedRow) {
                draggedRow.classList.add('is-dragging');
                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    var model = draggedRow.getAttribute('data-model') || 'Product';
                    event.dataTransfer.setData('text/plain', model);
                }
            }
        });

        handle.addEventListener('dragend', function() {
            if (draggedRow) {
                draggedRow.classList.remove('is-dragging');
                draggedRow = null;
            }
            clearStates();
            syncProductOrderInput();
        });
    });

    productTable.querySelectorAll('.product-row').forEach(function(row) {
        row.addEventListener('dragover', function(event) {
            if (!draggedRow || draggedRow === row) {
                return;
            }
            event.preventDefault();
            var insertBeforeRow = getInsertBeforeItem(productTable, event.clientY);
            clearStates();
            row.classList.add('drag-over');
            if (insertBeforeRow == null) {
                productTable.appendChild(draggedRow);
            } else {
                productTable.insertBefore(draggedRow, insertBeforeRow);
            }
            syncProductOrderInput();
        });

        row.addEventListener('drop', function(event) {
            event.preventDefault();
            syncProductOrderInput();
        });
    });

    var reorderProductsForm = document.querySelector('form[action*="products.php"]');
    if (reorderProductsForm) {
        reorderProductsForm.addEventListener('submit', function() {
            syncProductOrderInput();
        });
    }

    syncProductOrderInput();
})();

function toggleEditBrandPanel() {
    var panel = document.getElementById('editBrandPanel');
    if (!panel) {
        return;
    }
    var nextDisplay = panel.style.display === 'none' || panel.style.display === '' ? 'block' : 'none';
    panel.style.display = nextDisplay;
    if (nextDisplay === 'block') {
        var input = document.getElementById('editBrandLogo');
        if (input) {
            setTimeout(function(){ input.focus(); }, 20);
        }
    }
}

function previewBrandLogoFile(input, previewImgId, emptyLabelId, fallbackSrc) {
    var img = document.getElementById(previewImgId);
    var empty = document.getElementById(emptyLabelId);
    if (!img || !empty) return;

    var file = input && input.files && input.files[0] ? input.files[0] : null;
    if (!file) {
        if (fallbackSrc) {
            img.src = fallbackSrc;
            img.style.display = 'block';
            empty.style.display = 'none';
        } else {
            img.src = '';
            img.style.display = 'none';
            empty.style.display = 'inline';
        }
        return;
    }

    if (!file.type || file.type.indexOf('image/') !== 0) {
        img.src = '';
        img.style.display = 'none';
        empty.style.display = 'inline';
        empty.textContent = 'Invalid image file';
        return;
    }

    var reader = new FileReader();
    reader.onload = function(e) {
        img.src = String((e && e.target && e.target.result) || '');
        img.style.display = 'block';
        empty.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

(function(){
    var editInput = document.getElementById('editBrandLogo');
    var editPreview = document.getElementById('editBrandLogoPreview');
    var editFallbackSrc = editPreview ? (editPreview.getAttribute('src') || '') : '';

    if (editInput) {
        editInput.addEventListener('change', function(){
            previewBrandLogoFile(editInput, 'editBrandLogoPreview', 'editBrandLogoPreviewEmpty', editFallbackSrc);
        });
    }

    var newInput = document.getElementById('newBrandLogo');
    if (newInput) {
        newInput.addEventListener('change', function(){
            var empty = document.getElementById('newBrandLogoPreviewEmpty');
            if (empty) empty.textContent = 'No file selected';
            previewBrandLogoFile(newInput, 'newBrandLogoPreview', 'newBrandLogoPreviewEmpty', '');
        });
    }
})();
</script>

<?php
andison_admin_footer();



