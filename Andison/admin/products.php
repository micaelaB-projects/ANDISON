<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../includes/categories_info.php';

$brands = andison_get_brands_info(true);
$brandNames = array_keys($brands);
$selectedBrand = isset($_GET['brand']) ? (string)$_GET['brand'] : ($brandNames[0] ?? '');
if ($selectedBrand === '' || !isset($brands[$selectedBrand])) {
    $selectedBrand = $brandNames[0] ?? '';
}

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

$allowProductDelete = andison_env_flag('ANDISON_ALLOW_PRODUCT_DELETE', false);

function andison_safe_filename(string $name): string
{
    $name = strtolower($name);
    $name = preg_replace('~[^a-z0-9._-]+~', '_', $name) ?? $name;
    $name = trim($name, '._-');
    return $name !== '' ? $name : 'file';
}

function andison_normalize_category_assignment(array $allCategories, string $categoryId, string $subcategoryId, string $subSubcategoryId): array
{
    $categoryId = trim($categoryId);
    $subcategoryId = trim($subcategoryId);
    $subSubcategoryId = trim($subSubcategoryId);

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

    if (!is_array($category)) {
        return [
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'sub_subcategory_id' => $subSubcategoryId,
        ];
    }

    $subcategoryIds = [];
    $subSubParent = [];
    foreach (($category['subcategories'] ?? []) as $sub) {
        $subId = (string)($sub['id'] ?? '');
        if ($subId === '') {
            continue;
        }
        $subcategoryIds[$subId] = true;

        foreach (($sub['subcategories'] ?? []) as $subSub) {
            $subSubId = (string)($subSub['id'] ?? '');
            if ($subSubId === '') {
                continue;
            }
            $subSubParent[$subSubId] = $subId;
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

    $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'];
    $result  = [];

    for ($i = 0; $i < $maxProductImages; $i++) {
        $fieldName   = 'image_file_' . $i;
        $existingUrl = trim((string)($existing[$i] ?? ''));

        if (!empty($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $f   = $_FILES[$fieldName];
            $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
            $fi   = finfo_open(FILEINFO_MIME_TYPE);
            $mime = (string)finfo_file($fi, $f['tmp_name']);
            finfo_close($fi);
            if (in_array($ext, $allowed_ext, true) && in_array($mime, $allowed_mime, true)) {
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

// ── CSV Template download (GET) ──────────────────────────────────────────────
if (isset($_GET['action']) && $_GET['action'] === 'download_csv_template') {
    andison_require_admin();
    $headers = ['product_name','model','type','price','badge','description','specifications','category_id','subcategory_id','sub_subcategory_id','image_url','datasheet_url'];
    $example = [
        'Welding Machine XYZ',
        'WM-1000',
        'MIG Welding Machine',
        '',
        'New',
        'High-performance MIG welder suitable for industrial use.',
        "Input Voltage: 220V\nOutput Current: 50-200A\nDuty Cycle: 60%",
        'arc-welding-machine',
        'mig-welding-machine',
        '',
        'https://example.com/image.jpg',
        '',
    ];
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="products_import_template.csv"');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);
    fputcsv($out, $example);
    fclose($out);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';
    $brand = isset($_POST['brand']) ? (string)$_POST['brand'] : '';

    if ($brand !== '' && isset($brands[$brand])) {
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
            $headerRow = fgetcsv($handle);
            $normalizedHeaders = $headerRow === false ? [] : array_map('strtolower', array_map('trim', $headerRow));
            $isV1 = $normalizedHeaders === $expectedHeadersV1;
            $isV2 = $normalizedHeaders === $expectedHeadersV2;
            if (!$isV1 && !$isV2) {
                fclose($handle);
                andison_set_flash('error', 'CSV headers do not match the template. Please download and use the official template.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            if (empty($brands[$brand]['products']) || !is_array($brands[$brand]['products'])) {
                $brands[$brand]['products'] = [];
            }

            $rowNum = 1;
            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                if (count($row) < 9) { $errors[] = "Row {$rowNum}: too few columns, skipped."; continue; }

                $row = array_pad($row, $isV2 ? 12 : 11, '');
                $model = trim($row[1]);
                $type  = trim($row[2]);
                if ($model === '' || $type === '') {
                    $errors[] = "Row {$rowNum}: model and type are required, skipped.";
                    continue;
                }

                $rawCategoryId = trim((string)$row[7]);
                $rawSubcategoryId = trim((string)$row[8]);
                $rawSubSubcategoryId = $isV2 ? trim((string)$row[9]) : '';
                $normalizedAssignment = andison_normalize_category_assignment(
                    $allCategories,
                    $rawCategoryId,
                    $rawSubcategoryId,
                    $rawSubSubcategoryId
                );

                $imageIndex = $isV2 ? 10 : 9;
                $datasheetIndex = $isV2 ? 11 : 10;
                $imageUrl = filter_var(trim((string)$row[$imageIndex]), FILTER_VALIDATE_URL) ? trim((string)$row[$imageIndex]) : '';
                $datasheetUrl = filter_var(trim((string)$row[$datasheetIndex]), FILTER_VALIDATE_URL) ? trim((string)$row[$datasheetIndex]) : '';
                $images = $imageUrl !== '' ? [$imageUrl] : [];

                $brands[$brand]['products'][] = [
                    'product_name'   => trim($row[0]),
                    'model'          => $model,
                    'type'           => $type,
                    'price'          => trim($row[3]),
                    'badge'          => trim($row[4]),
                    'description'    => trim($row[5]),
                    'specifications' => trim($row[6]),
                    'category_id'    => $normalizedAssignment['category_id'],
                    'subcategory_id' => $normalizedAssignment['subcategory_id'],
                    'sub_subcategory_id' => $normalizedAssignment['sub_subcategory_id'],
                    'image'          => $imageUrl,
                    'images'         => $images,
                    'datasheet'      => $datasheetUrl,
                ];
                $imported++;
            }
            fclose($handle);

            $saveOk = true;
            if ($imported > 0) {
                $saveOk = andison_save_single_brand($brand, $brands[$brand]);
                @unlink(__DIR__ . '/../data/_cache/brands_full.cache');
            }

            $msg = "Imported {$imported} product(s).";
            if (!empty($errors)) $msg .= ' Skipped rows: ' . implode(' | ', $errors);
            if ($imported > 0 && !$saveOk) {
                andison_set_flash('error', 'Import blocked to protect existing products. Please refresh the page and try again.');
            } else {
                andison_set_flash($imported > 0 ? 'success' : 'error', $msg);
            }
            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'update_brand') {
            $desc = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
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
            $desc = isset($_POST['product_description']) ? trim((string)$_POST['product_description']) : '';
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

            if ($model === '' || $type === '') {
                andison_set_flash('error', 'Model and Type are required.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            if (empty($brands[$brand]['products']) || !is_array($brands[$brand]['products'])) {
                $brands[$brand]['products'] = [];
            }

            $brands[$brand]['products'][] = [
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
            $desc = isset($_POST['product_description']) ? trim((string)$_POST['product_description']) : '';
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
    }

    andison_set_flash('error', 'Invalid request.');
    header('Location: products.php');
    exit;
}

$editIndex = isset($_GET['edit']) ? (int)$_GET['edit'] : -1;
$brandInfo = $selectedBrand !== '' ? ($brands[$selectedBrand] ?? []) : [];
$products = isset($brandInfo['products']) && is_array($brandInfo['products']) ? $brandInfo['products'] : [];

// Keep presets intentionally minimal: Optional only.
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
</style>

<div class="grid">
    <!-- Brand Selector Section -->
    <section style="grid-column:span 12;" class="prod-page-header">
        <div style="display:flex;flex-direction:column;gap:6px;">
            <div style="font-size:11px;font-weight:600;opacity:0.7;letter-spacing:0.5px;text-transform:uppercase;">Product Management</div>
            <div style="font-size:20px;font-weight:800;letter-spacing:-0.2px;"><i class="bi bi-building"></i>
                <?php echo $selectedBrand !== '' ? htmlspecialchars($selectedBrand) : 'Select a Brand'; ?>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <?php if ($selectedBrand !== ''): ?>
                <span class="prod-stat-pill"><i class="bi bi-box-seam"></i> <?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?></span>
            <?php endif; ?>
            <form method="get" action="products.php" style="display:flex;gap:8px;align-items:center;">
                <select name="brand" class="prod-brand-select" onchange="this.form.submit()">
                    <?php foreach ($brandNames as $bn): ?>
                        <option value="<?php echo htmlspecialchars($bn); ?>" <?php echo $bn === $selectedBrand ? 'selected' : ''; ?>><?php echo htmlspecialchars(strtoupper((string)$bn)); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </section>

    <!-- Brand Description Section -->
    <?php if ($selectedBrand !== ''): ?>
        <section class="card" style="grid-column:span 12;padding:16px 20px;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                <div style="width:28px;height:28px;border-radius:7px;background:rgba(245,158,11,0.12);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-file-text" style="color:#f59e0b;font-size:13px;"></i>
                </div>
                <span style="font-size:13px;font-weight:700;color:#374151;">Brand Description</span>
                <span style="font-size:11px;color:#9ca3af;margin-left:4px;">Shown on the brand page</span>
            </div>
            <form method="post" action="products.php?brand=<?php echo urlencode($selectedBrand); ?>" class="brand-desc-form">
                <input type="hidden" name="action" value="update_brand">
                <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrand); ?>">
                <div style="display:flex;gap:10px;align-items:flex-end;">
                    <textarea id="description" name="description" class="prod-desc-textarea" style="flex:1;"><?php echo htmlspecialchars((string)($brandInfo['description'] ?? '')); ?></textarea>
                    <button class="btn btn-primary" type="submit" style="white-space:nowrap;height:fit-content;font-size:12px;padding:9px 16px;"><i class="bi bi-save"></i> Save</button>
                </div>
            </form>
        </section>
    <?php endif; ?>

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
                        <div style="font-size:11px;color:#9ca3af;margin-top:1px;"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?> &middot; <?php echo htmlspecialchars($selectedBrand); ?></div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <button class="btn btn-primary" type="button" onclick="openAddProductModal();" style="font-size:12px;padding:8px 16px;"><i class="bi bi-plus-lg"></i> Add Product</button>
                    <button class="btn btn-secondary" type="button" onclick="openImportCsvModal();" style="font-size:12px;padding:8px 16px;background:#6b7280;border-color:#6b7280;color:white;border-radius:8px;"><i class="bi bi-upload"></i> Import CSV</button>
                    <?php if (!$allowProductDelete): ?>
                        <span style="font-size:10px;font-weight:700;background:#fef3c7;color:#92400e;border:1px solid #fcd34d;border-radius:999px;padding:4px 10px;"><i class="bi bi-shield-lock"></i> Delete Disabled</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Search Bar -->
            <div style="margin-bottom:14px;">
                <div class="prod-search-wrap">
                    <i class="bi bi-search search-icon"></i>
                    <input id="productSearch" type="text" placeholder="Search by model, name, type, or badge...">
                </div>
            </div>

            <!-- Products Table -->
            <div style="overflow-x:auto;border-radius:10px;border:1px solid #e5e7eb;background:white;">
                <table class="prod-table" id="productsTable" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
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
                                <td colspan="6" style="padding:48px;text-align:center;">
                                    <div style="width:56px;height:56px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;"><i class="bi bi-inbox" style="font-size:24px;color:#d1d5db;"></i></div>
                                    <div style="font-weight:600;font-size:14px;color:#374151;margin-bottom:4px;">No products yet</div>
                                    <div style="font-size:12px;color:#9ca3af;margin-bottom:14px;">Add the first product for <?php echo htmlspecialchars($selectedBrand); ?></div>
                                    <button class="btn btn-primary" type="button" onclick="openAddProductModal();" style="font-size:12px;padding:7px 16px;"><i class="bi bi-plus-lg"></i> Add Product</button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $i => $prod): ?>
                                <?php if (!is_array($prod)) { continue; } ?>
                                <?php
                                    $badge = (string)($prod['badge'] ?? '');
                                    $badgeClass = 'prod-badge-default';
                                    if ($badge === 'Available') $badgeClass = 'prod-badge-available';
                                    elseif ($badge === 'Not Available') $badgeClass = 'prod-badge-unavailable';
                                    elseif ($badge === 'Featured') $badgeClass = 'prod-badge-featured';
                                    elseif ($badge === 'New Arrival') $badgeClass = 'prod-badge-new';
                                    elseif ($badge === 'Best Seller') $badgeClass = 'prod-badge-bestseller';
                                    elseif ($badge === 'Limited Stock') $badgeClass = 'prod-badge-limited';
                                ?>
                                <tr class="product-row" 
                                    data-model="<?php echo htmlspecialchars(strtolower((string)($prod['model'] ?? '')), ENT_QUOTES); ?>" 
                                    data-type="<?php echo htmlspecialchars(strtolower((string)($prod['type'] ?? '')), ENT_QUOTES); ?>" 
                                    data-badge="<?php echo htmlspecialchars(strtolower((string)($prod['badge'] ?? '')), ENT_QUOTES); ?>">
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
                                            ?>
                                            <div style="margin-top:3px;"><span style="font-size:10px;color:#6b7280;"><i class="bi bi-folder-check"></i> <?php echo htmlspecialchars($dispCat); ?><?php if ($dispSub !== ''): ?> › <?php echo htmlspecialchars($dispSub); ?><?php endif; ?><?php if ($dispSubSub !== ''): ?> › <?php echo htmlspecialchars($dispSubSub); ?><?php endif; ?></span></div>
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
                                                <form method="post" action="products.php?brand=<?php echo urlencode($selectedBrand); ?>" class="delete-form" style="display:inline;">
                                                    <input type="hidden" name="action" value="delete_product">
                                                    <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrand); ?>">
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
        <form method="post" enctype="multipart/form-data" action="products.php?brand=<?php echo urlencode($selectedBrand); ?>" class="edit-product-form">
            <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrand); ?>">
            <input type="hidden" name="action" value="update_product">
            <input type="hidden" name="index" id="editIndex">
            
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
                    
                    <div class="field" style="margin:0;margin-bottom:12px;">
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
                                <option value="New Arrival" style="color:#8b5cf6;">🆕 New Arrival</option>
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
                        <label for="editDescription">Description</label>
                        <textarea id="editDescription" name="product_description" rows="3" placeholder="Add product benefits and key features..." style="resize:vertical;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;"></textarea>
                    </div>
                    
                    <div class="field" style="margin:0;margin-bottom:12px;">
                        <label for="editSpecificationsText">Specifications (Text)</label>
                        <textarea id="editSpecificationsText" rows="3" placeholder="Technical specs, dimensions, power requirements, etc..." style="resize:vertical;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;"></textarea>
                        <input type="hidden" id="editSpecifications" name="specifications" value="">
                        <div style="font-size:11px;color:#9ca3af;margin-top:4px;"><i class="bi bi-info-circle"></i> This displays as plain text on the client side.</div>
                    </div>

                    <div class="field" style="margin:0;margin-bottom:12px;">
                        <label><i class="bi bi-table"></i> Specifications Table (Optional)</label>
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
                            <label for="specTableMode" style="margin:0;font-size:11px;font-weight:700;color:#374151;">Table Style</label>
                            <select id="specTableMode" onchange="setSpecTableMode(this.value, true)" style="min-width:220px;padding:6px 9px;border:1.5px solid #dbe1ea;border-radius:8px;font-size:11px;font-weight:600;background:#fff;">
                                <option value="standard">Spreadsheet Dark Grid (Excel-like)</option>
                                <option value="grouped-pairs">Grouped Header (like image)</option>
                            </select>
                            <button type="button" onclick="convertSpecificationsTextToTable()" style="display:inline-flex;align-items:center;gap:6px;background:#eff6ff;border:1px solid #93c5fd;color:#1d4ed8;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-arrow-repeat"></i> Convert Text to Table
                            </button>
                            <button type="button" onclick="applyAirflowSpecTemplate()" style="display:inline-flex;align-items:center;gap:6px;background:#fefce8;border:1px solid #fde68a;color:#854d0e;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-grid-3x3-gap"></i> Use Airflow Template
                            </button>
                        </div>
                        <div id="specGroupHeaderWrap" style="display:none;margin-bottom:8px;padding:8px;border:1px solid #dbeafe;border-radius:8px;background:#eff6ff;"></div>
                        <div id="specTableBuilderWrap" style="overflow:auto;border:1px solid #e5e7eb;border-radius:10px;background:#fff;">
                            <table id="specTableBuilder" style="width:max-content;border-collapse:separate;border-spacing:0;min-width:100%;table-layout:fixed;">
                                <thead id="specTableHead"></thead>
                                <tbody id="specTableBody"></tbody>
                            </table>
                        </div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
                            <button type="button" onclick="addSpecTableDataRow()" style="display:inline-flex;align-items:center;gap:6px;background:#eef2ff;border:1px solid #c7d2fe;color:#2b11db;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-plus-lg"></i> Add Row
                            </button>
                            <button type="button" id="specAddColumnBtn" onclick="addSpecTableColumn()" style="display:inline-flex;align-items:center;gap:6px;background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-layout-three-columns"></i> Add Column
                            </button>
                            <button type="button" onclick="pasteExcelIntoSpecTable()" style="display:inline-flex;align-items:center;gap:6px;background:#fff7ed;border:1px solid #fdba74;color:#9a3412;border-radius:8px;padding:6px 10px;font-size:11px;font-weight:700;cursor:pointer;">
                                <i class="bi bi-clipboard"></i> Paste Excel
                            </button>
                        </div>
                        <div id="specTableHelpText" style="font-size:11px;color:#9ca3af;margin-top:6px;"><i class="bi bi-info-circle"></i> Excel-like mode: use Tab/Enter/Arrow Up/Arrow Down and paste multi-cell data from Excel.</div>
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
                    <input type="file" id="bulkImageFiles" accept="image/*" multiple style="display:none;" onchange="handleBulkImageSelect(this)">

                    <!-- Hidden file inputs, one per slot -->
                    <div style="display:none;">
                        <?php for ($s = 0; $s < $maxProductImages; $s++): ?>
                        <input type="file" id="imageFile_<?php echo $s; ?>" name="image_file_<?php echo $s; ?>" accept="image/*" onchange="previewImageSlot(this, <?php echo $s; ?>)">
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
    width: min(92vw, 980px);
    max-width: min(96vw, 1100px);
    height: min(88vh, 860px);
    max-height: 94vh;
    min-width: min(520px, 96vw);
    min-height: 520px;
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
    padding: 24px;
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
        height: 92vh;
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
</style>

<script>
// Edit product modal functionality
var _andisonCategories = <?php echo json_encode(array_map(function($c){ return ['id'=>$c['id'],'name'=>$c['name'],'subcategories'=>$c['subcategories']??[]]; }, $allCategories), JSON_HEX_TAG); ?>;
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
    };

    if (!source) return result;

    try {
        var parsed = JSON.parse(source);
        if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
            var hasTables = Array.isArray(parsed.tables);
            if (parsed.format === 'andison_specs_v3' && hasTables && parsed.tables.length > 0) {
                result.text = String(parsed.text || '').trim();
                var first = parsed.tables[0] || {};
                result.sourceHtml = String(first.tableHtml || '').trim();
                var matrixRawV3 = first.tableMatrix && typeof first.tableMatrix === 'object' ? first.tableMatrix : null;
                if (matrixRawV3) {
                    var modeV3 = matrixRawV3.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
                    var headersV3 = Array.isArray(matrixRawV3.headers)
                        ? matrixRawV3.headers.map(function(h) { return String(h || '').trim(); }).filter(function(h) { return h !== ''; })
                        : [];

                    if (headersV3.length > 0) {
                        var leadV3 = Math.max(1, parseInt(matrixRawV3.leadColumns, 10) || 1);
                        if (leadV3 >= headersV3.length) leadV3 = Math.max(1, headersV3.length - 1);
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
                if (!isFinite(leadColumns) || leadColumns < 1) leadColumns = 1;
                var headers = Array.isArray(matrixRaw.headers)
                    ? matrixRaw.headers.map(function(h) { return String(h || '').trim(); }).filter(function(h) { return h !== ''; })
                    : [];

                if (mode === 'grouped-pairs') {
                    if (headers.length === 0) {
                        headers = getDefaultGroupedHeaders();
                    }

                    if (leadColumns >= headers.length) {
                        leadColumns = Math.max(1, headers.length - 1);
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
var _specTableLeadColumns = 1;
var _specTableRowMerges = [];
var _specTableHasUserInput = false;  // Track if user has actively used the table builder

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
    if (!isFinite(lead) || lead < 1) lead = 1;
    if (Array.isArray(_specTableHeaders) && _specTableHeaders.length > 0 && lead >= _specTableHeaders.length) {
        lead = Math.max(1, _specTableHeaders.length - 1);
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
    if (!isFinite(_specTableLeadColumns) || _specTableLeadColumns < 1) _specTableLeadColumns = 1;

    if (_specTableMode === 'grouped-pairs') {
        if (_specTableHeaders.length === 0) {
            _specTableHeaders = getDefaultGroupedHeaders();
        }

        if (_specTableLeadColumns >= _specTableHeaders.length) {
            _specTableLeadColumns = Math.max(1, _specTableHeaders.length - 1);
        }

        _specTableHeaders[0] = String(_specTableHeaders[0] || 'Model').trim() || 'Model';

        var leadCols = getGroupedLeadColumnCount();
        var dataCols = Math.max(1, _specTableHeaders.length - leadCols);
        _specTableGroups = normalizeGroupedGroups(_specTableGroups, dataCols);

        var normalizedDataCols = getGroupedDataColumnCount(_specTableGroups);
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
        _specTableHeaders.push('Column ' + (_specTableHeaders.length + 1));
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
        if (leadColumns < 1) leadColumns = 1;

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
    _specTableLeadColumns = Math.max(1, parseInt(parsed.leadColumns, 10) || 1);
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

function applyPastedSpecText(rawText, startRow, startCol) {
    if (_specTableMode !== 'standard') return false;

    var normalized = String(rawText || '').replace(/\r\n?/g, '\n');
    if (normalized.trim() === '') return false;

    var looksTabular = normalized.indexOf('\t') !== -1 || normalized.indexOf('\n') !== -1;
    if (!looksTabular) return false;

    var lines = normalized.split('\n');
    while (lines.length > 0 && lines[lines.length - 1] === '') lines.pop();
    if (lines.length === 0) return false;

    var matrix = lines.map(function(line) {
        return line.split('\t');
    });

    var maxCols = matrix.reduce(function(max, row) {
        return Math.max(max, row.length);
    }, 0);
    if (maxCols < 1) return false;

    var rowOffset = Math.max(0, parseInt(startRow, 10) || 0);
    var colOffset = Math.max(0, parseInt(startCol, 10) || 0);

    ensureSpecTableGridSize(rowOffset + matrix.length, colOffset + maxCols);
    _specTableMerges = [];
    _specTableRowMerges = [];
    _specTableSourceHtml = '';

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

function handleSpecCellPaste(event, rowIdx, colIdx) {
    if (_specTableMode !== 'standard') return;
    var clipboard = event.clipboardData || window.clipboardData;
    if (!clipboard) return;

    var html = '';
    try {
        html = clipboard.getData('text/html');
    } catch (e) {
        html = '';
    }

    if (html && applyPastedSpecHtml(html)) {
        event.preventDefault();
        return;
    }

    var text = clipboard.getData('text');
    if (applyPastedSpecText(text, rowIdx, colIdx)) {
        event.preventDefault();
    }
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
        } else {
            if (colIdx < _specTableHeaders.length - 1) {
            } else {
                nextRow = rowIdx + 1;
                nextCol = 0;
            }
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

function pasteExcelIntoSpecTable() {
    _specTableHasUserInput = true;
    var pos = getSpecCellPositionFromElement(document.activeElement);

    if (!navigator.clipboard || (!navigator.clipboard.readText && !navigator.clipboard.read)) {
        customAlert('Clipboard API is not available. Click a cell then use Ctrl+V to paste from Excel.');
        return;
    }

    function fallbackPasteText() {
        if (!navigator.clipboard.readText) {
            customAlert('Clipboard permission denied. Click a cell then use Ctrl+V to paste from Excel.');
            return;
        }
        navigator.clipboard.readText().then(function(text) {
            if (!applyPastedSpecText(text, pos.row, pos.col)) {
                customAlert('No Excel-style table data found in clipboard.');
            }
        }).catch(function() {
            customAlert('Clipboard permission denied. Click a cell then use Ctrl+V to paste from Excel.');
        });
    }

    if (!navigator.clipboard.read) {
        fallbackPasteText();
        return;
    }

    navigator.clipboard.read().then(function(items) {
        if (!Array.isArray(items) || items.length === 0) {
            fallbackPasteText();
            return;
        }

        var htmlReader = null;
        for (var i = 0; i < items.length; i++) {
            var it = items[i];
            if (it && Array.isArray(it.types) && it.types.indexOf('text/html') !== -1) {
                htmlReader = it;
                break;
            }
        }

        if (!htmlReader) {
            fallbackPasteText();
            return;
        }

        htmlReader.getType('text/html').then(function(blob) {
            return blob.text();
        }).then(function(html) {
            if (!applyPastedSpecHtml(html)) {
                fallbackPasteText();
            }
        }).catch(function() {
            fallbackPasteText();
        });
    }).catch(function() {
        fallbackPasteText();
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
    if (addColBtn) {
        if (_specTableMode === 'grouped-pairs') {
            addColBtn.innerHTML = '<i class="bi bi-layout-three-columns"></i> Add Group';
        } else {
            addColBtn.innerHTML = '<i class="bi bi-layout-three-columns"></i> Add Column';
        }
    }

    var help = document.getElementById('specTableHelpText');
    if (help) {
        if (_specTableMode === 'grouped-pairs') {
            help.innerHTML = '<i class="bi bi-info-circle"></i> Grouped mode supports merged headers: set each group title and span (number of merged columns).';
        } else {
            help.innerHTML = '<i class="bi bi-info-circle"></i> Spreadsheet mode: use Tab/Enter/Arrow Up/Arrow Down and paste multi-cell data directly from Excel.';
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
        var width = Math.round(chars * 8.2 + 48);
        if (width < 140) width = 140;
        if (width > 520) width = 520;
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

    if (_specTableMode !== 'grouped-pairs') {
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
    leadInput.min = '1';
    leadInput.max = '12';
    leadInput.value = String(getGroupedLeadColumnCount());
    leadInput.style.cssText = 'padding:6px 8px;border:1.5px solid #bfdbfe;border-radius:7px;font-size:11px;text-align:center;';
    leadInput.addEventListener('change', function() {
        _specTableSourceHtml = '';
        var nextLead = parseInt(this.value, 10);
        if (!isFinite(nextLead) || nextLead < 1) nextLead = 1;
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
                renderSpecTableBuilder();
                syncSpecificationsHiddenField();
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
                firstTop.style.cssText = 'padding:8px;border-bottom:1px solid #cbd5e1;background:#111827;color:#fff;vertical-align:middle;';
                var firstTopInput = document.createElement('input');
                firstTopInput.type = 'text';
                firstTopInput.value = _specTableHeaders[colIdx] || (colIdx === 0 ? 'Model' : ('Column ' + (colIdx + 1)));
                firstTopInput.style.cssText = 'width:100%;padding:7px 9px;border:1px solid #4b5563;border-radius:7px;font-size:11px;font-weight:700;background:#1f2937;color:#fff;';
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
            gTh.style.cssText = 'padding:9px 8px;border-bottom:1px solid #374151;background:#111827;color:#fff;font-size:11px;font-weight:800;text-align:center;';
            gTh.textContent = String(gInfo.title || ('Group ' + (g + 1)));
            groupRow.appendChild(gTh);
        }

        var actionHeadTop = document.createElement('th');
        actionHeadTop.rowSpan = 2;
        actionHeadTop.style.cssText = 'width:42px;padding:8px;border-bottom:1px solid #cbd5e1;background:#111827;';
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
                    th.style.cssText = 'padding:8px;border-bottom:1px solid #e5e7eb;background:#f8fafc;vertical-align:middle;';
                    var input = document.createElement('input');
                    input.type = 'text';
                    input.value = _specTableHeaders[colIdx] || '';
                    input.placeholder = (colIdx % 2 === 1) ? 'cfm' : 'm3/hr';
                    input.style.cssText = 'width:100%;padding:7px 9px;border:1.5px solid #dbe1ea;border-radius:7px;font-size:11px;font-weight:700;color:#1f2937;background:#fff;';
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
                : 'padding:6px 8px;border-bottom:1px solid #eef2f6;';

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
                : 'width:100%;min-height:34px;padding:7px 9px;border:1.5px solid #e5e7eb;border-radius:7px;font-size:12px;line-height:1.45;resize:vertical;overflow:hidden;white-space:pre-wrap;';
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
            : 'padding:6px 8px;border-bottom:1px solid #eef2f6;text-align:center;';
        var removeRowBtn = document.createElement('button');
        removeRowBtn.type = 'button';
        removeRowBtn.innerHTML = '<i class="bi bi-trash"></i>';
        removeRowBtn.title = 'Remove row';
        removeRowBtn.style.cssText = isStandardDark
            ? 'width:28px;height:28px;border-radius:7px;border:1px solid #7f1d1d;background:#2a1014;color:#fca5a5;cursor:pointer;'
            : 'width:28px;height:28px;border-radius:7px;border:1px solid #fecaca;background:#fef2f2;color:#dc2626;cursor:pointer;';
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
        _specTableLeadColumns = 1;
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
            if (_specTableHeaders.length === 0) {
                _specTableHeaders = getDefaultGroupedHeaders();
            }

            var targetCols = getGroupedLeadColumnCount() + getGroupedDataColumnCount(_specTableGroups.length > 0 ? _specTableGroups : getDefaultGroupedGroups());

            while (_specTableHeaders.length < targetCols) {
                _specTableHeaders.push(getDefaultGroupedSubHeader(_specTableHeaders.length));
            }
        } else {
            _specTableGroups = [];
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
    _specTableLeadColumns = 1;
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
    _specTableLeadColumns = 1;
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
    if (!hiddenInput || !textArea) return;

    var textValue = String(textArea.value || '').trim();

    // If user hasn't actively used the table builder and there's only text, save as plain text
    if (!_specTableHasUserInput && _specTableRows.length === 1 && _specTableRows[0].every(function(cell) { return String(cell).trim() === ''; })) {
        hiddenInput.value = textValue;
        return;
    }

    if (_specTableMode === 'grouped-pairs') {
        if (!hasGroupedTableContent()) {
            hiddenInput.value = textValue;
            return;
        }

        hiddenInput.value = JSON.stringify({
            format: 'andison_specs_v2',
            text: textValue,
            tableMatrix: {
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
            },
        });
        return;
    }

    if (_specTableSourceHtml !== '') {
        hiddenInput.value = JSON.stringify({
            format: 'andison_specs_v3',
            text: textValue,
            tables: [{
                tableHtml: _specTableSourceHtml,
                tableMatrix: {
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
                },
            }],
        });
        return;
    }

    var hasStandardMerge = _specTableMerges.some(function(m) {
        return m && isFinite(parseInt(m.row, 10)) && isFinite(parseInt(m.col, 10)) && (parseInt(m.span, 10) > 1);
    });

    if (hasStandardMerge) {
        hiddenInput.value = JSON.stringify({
            format: 'andison_specs_v2',
            text: textValue,
            tableMatrix: {
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
            },
        });
        return;
    }

    var tableRows = matrixToSpecTableRows(_specTableHeaders, _specTableRows);

    // Only convert to JSON if user explicitly used table builder or there's actual table data
    if (!_specTableHasUserInput || tableRows.length === 0) {
        hiddenInput.value = textValue;
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

    // Track whether the original format had table data
    _specTableHasUserInput = !!(parsed.matrix || (parsed.table && parsed.table.length > 0));

    if (parsed.matrix) {
        _specTableMode = parsed.matrix.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
        _specTableHeaders = parsed.matrix.headers;
        _specTableRows = parsed.matrix.rows;
        _specTableSourceHtml = String(parsed.sourceHtml || '').trim();
        if (_specTableMode === 'grouped-pairs') {
            _specTableLeadColumns = parseInt(parsed.matrix.leadColumns, 10);
            if (!isFinite(_specTableLeadColumns) || _specTableLeadColumns < 1) _specTableLeadColumns = 1;
            _specTableGroups = normalizeGroupedGroups(parsed.matrix.groups || [], Math.max(1, _specTableHeaders.length - getGroupedLeadColumnCount()));
            _specTableMerges = [];
            _specTableRowMerges = Array.isArray(parsed.matrix.rowMerges) ? parsed.matrix.rowMerges : [];
        } else {
            _specTableLeadColumns = 1;
            _specTableGroups = [];
            _specTableMerges = Array.isArray(parsed.matrix.merges) ? parsed.matrix.merges : [];
            _specTableRowMerges = [];
        }
    } else if (parsed.table && parsed.table.length > 0) {
        // User had table data, so populate table builder
        _specTableMode = 'standard';
        _specTableLeadColumns = 1;
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
        _specTableLeadColumns = 1;
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

function _esc(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
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
    if (!file.type.startsWith('image/')) {
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
        if (!file.type || !file.type.startsWith('image/')) continue;
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

function openEditModal(index, name, model, type, price, badge, description, specifications, image, catId, subId, subSubId, imagesJson, datasheet) {
    var modal = ensureGlobalProductModal();
    if (!modal) return;
    document.getElementById('editIndex').value = index;
    document.getElementById('editProductName').value = name;
    document.getElementById('editModel').value = model;
    document.getElementById('editType').value = type;
    syncTypePresetFromInput();
    document.getElementById('editPrice').value = price;
    document.getElementById('editBadge').value = badge;
    document.getElementById('editDescription').value = description;
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
        var image  = this.getAttribute('data-image');
        var images = this.getAttribute('data-images') || '[]';
        var catId     = this.getAttribute('data-category');
        var subId     = this.getAttribute('data-subcategory');
        var subSubId  = this.getAttribute('data-subsubcategory');
        var datasheet = this.getAttribute('data-datasheet') || '';
        openEditModal(index, name, model, type, price, badge, description, specifications, image, catId, subId, subSubId, images, datasheet);
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
    form.action = 'products.php?brand=<?php echo urlencode($selectedBrand); ?>';
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
    setSpecificationsEditor('');
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
                    noResultsRow.innerHTML = '<td colspan="6" style="background:#fff;border-radius:12px;padding:14px;border:1px dashed rgba(43,17,219,0.25);color:#374151;text-align:center;"><i class="bi bi-search"></i> No products found matching "' + searchTerm + '"</td>';
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
                2. Fill in your products — one row per product.<br>
                3. <code>model</code> and <code>type</code> columns are required.<br>
                4. Use image/datasheet URLs (not file uploads).<br>
                5. Save as CSV (UTF-8) and import here.
            </div>
            <a href="products.php?action=download_csv_template" download style="display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:600;color:#2563eb;text-decoration:none;background:#eff6ff;border:1px solid #bfdbfe;padding:7px 14px;border-radius:7px;margin-bottom:18px;">
                <i class="bi bi-file-earmark-spreadsheet"></i> Download Template (CSV)
            </a>
            <form id="importCsvForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import_csv">
                <input type="hidden" name="brand" id="importCsvBrand" value="<?php echo htmlspecialchars($selectedBrand, ENT_QUOTES); ?>">
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
</script>

<?php
andison_admin_footer();



