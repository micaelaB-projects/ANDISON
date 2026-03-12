<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../includes/categories_info.php';

$brands = andison_get_brands_info();
$brandNames = array_keys($brands);
$selectedBrand = isset($_GET['brand']) ? (string)$_GET['brand'] : ($brandNames[0] ?? '');
if ($selectedBrand === '' || !isset($brands[$selectedBrand])) {
    $selectedBrand = $brandNames[0] ?? '';
}

function andison_safe_filename(string $name): string
{
    $name = strtolower($name);
    $name = preg_replace('~[^a-z0-9._-]+~', '_', $name) ?? $name;
    $name = trim($name, '._-');
    return $name !== '' ? $name : 'file';
}

/**
 * Handle up to 5 product images (image_file_0 … image_file_4).
 * existing_images POST param is a JSON array of current URLs to keep per slot.
 * Returns array of image URLs (empty slots are omitted).
 */
function andison_handle_multi_image_upload(): array
{
    $existingJson = isset($_POST['existing_images']) ? trim((string)$_POST['existing_images']) : '[]';
    $existing     = json_decode($existingJson, true);
    if (!is_array($existing)) $existing = [];
    while (count($existing) < 5) $existing[] = '';

    $allowed_ext  = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    $allowed_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif'];
    $result  = [];

    for ($i = 0; $i < 5; $i++) {
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
    $headers = ['product_name','model','type','price','badge','description','specifications','category_id','subcategory_id','image_url','datasheet_url'];
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

    // ── Dedup: remove duplicate rows from Supabase products table ──────────────
    if ($action === 'dedup_products') {
        require_once __DIR__ . '/../includes/supabase.php';
        $all = andison_sb_select('products', 'select=id,brand,product_name,model,category_id,subcategory_id,image&limit=10000&order=id');

        $groups = [];
        foreach ($all as $row) {
            $key = strtolower(trim((string)($row['model'] ?? '')));
            if ($key === '') continue;
            $groups[$key][] = $row;
        }

        $toDelete = [];
        foreach ($groups as $rows) {
            if (count($rows) <= 1) continue;
            // Score each row — keep the most complete one
            $best      = null;
            $bestScore = -1;
            foreach ($rows as $row) {
                $score = 0;
                if (!empty($row['brand']))          $score += 4;
                if (!empty($row['category_id']))    $score += 3;
                if (!empty($row['subcategory_id'])) $score += 2;
                if (!empty($row['product_name']))   $score += 1;
                if (!empty($row['image']))          $score += 1;
                if ($score > $bestScore) { $bestScore = $score; $best = $row; }
            }
            foreach ($rows as $row) {
                if ((int)$row['id'] !== (int)$best['id']) {
                    $toDelete[] = (int)$row['id'];
                }
            }
        }

        $deleted = 0;
        if (!empty($toDelete)) {
            foreach (array_chunk($toDelete, 100) as $chunk) {
                if (andison_sb_delete('products', 'id=in.(' . implode(',', $chunk) . ')')) {
                    $deleted += count($chunk);
                }
            }
        }

        $msg = $deleted > 0 ? "Removed {$deleted} duplicate product row(s)." : 'No duplicates found.';
        @unlink(__DIR__ . '/../data/_cache/brands_full.cache');
        andison_set_flash($deleted > 0 ? 'success' : 'info', $msg);
        header('Location: products.php' . ($brand !== '' ? '?brand=' . urlencode($brand) : ''));
        exit;
    }

    // ── Delete ALL products from Supabase ─────────────────────────────────────
    if ($action === 'delete_all_products') {
        require_once __DIR__ . '/../includes/supabase.php';
        $all = andison_sb_select('products', 'select=id&limit=10000');
        $deleted = 0;
        if (!empty($all)) {
            $ids = array_filter(array_map(fn($r) => isset($r['id']) ? (int)$r['id'] : null, $all));
            foreach (array_chunk($ids, 100) as $chunk) {
                if (andison_sb_delete('products', 'id=in.(' . implode(',', $chunk) . ')')) {
                    $deleted += count($chunk);
                }
            }
        }
        @unlink(__DIR__ . '/../data/_cache/brands_full.cache');
        andison_set_flash('success', "Deleted {$deleted} product row(s). You can now add products fresh.");
        header('Location: products.php' . ($brand !== '' ? '?brand=' . urlencode($brand) : ''));
        exit;
    }

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

            $expectedHeaders = ['product_name','model','type','price','badge','description','specifications','category_id','subcategory_id','image_url','datasheet_url'];
            $headerRow = fgetcsv($handle);
            if ($headerRow === false || array_map('strtolower', array_map('trim', $headerRow)) !== $expectedHeaders) {
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

                $row = array_pad($row, 11, '');
                $model = trim($row[1]);
                $type  = trim($row[2]);
                if ($model === '' || $type === '') {
                    $errors[] = "Row {$rowNum}: model and type are required, skipped.";
                    continue;
                }

                $imageUrl = filter_var(trim($row[9]), FILTER_VALIDATE_URL) ? trim($row[9]) : '';
                $datasheetUrl = filter_var(trim($row[10]), FILTER_VALIDATE_URL) ? trim($row[10]) : '';
                $images = $imageUrl !== '' ? [$imageUrl] : [];

                $brands[$brand]['products'][] = [
                    'product_name'   => trim($row[0]),
                    'model'          => $model,
                    'type'           => $type,
                    'price'          => trim($row[3]),
                    'badge'          => trim($row[4]),
                    'description'    => trim($row[5]),
                    'specifications' => trim($row[6]),
                    'category_id'    => trim($row[7]),
                    'subcategory_id' => trim($row[8]),
                    'image'          => $imageUrl,
                    'images'         => $images,
                    'datasheet'      => $datasheetUrl,
                ];
                $imported++;
            }
            fclose($handle);

            if ($imported > 0) {
                andison_save_single_brand($brand, $brands[$brand]);
                @unlink(__DIR__ . '/../data/_cache/brands_full.cache');
            }

            $msg = "Imported {$imported} product(s).";
            if (!empty($errors)) $msg .= ' Skipped rows: ' . implode(' | ', $errors);
            andison_set_flash($imported > 0 ? 'success' : 'error', $msg);
            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'update_brand') {
            $desc = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
            $brands[$brand]['description'] = $desc;
            if (andison_save_single_brand($brand, $brands[$brand])) {
                andison_set_flash('success', 'Brand description updated.');
            } else {
                andison_set_flash('error', 'Failed to save changes.');
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
            ];

            if (andison_save_single_brand($brand, $brands[$brand])) {
                andison_set_flash('success', 'Product added.');
            } else {
                andison_set_flash('error', 'Failed to save changes.');
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
            ];

            if (andison_save_single_brand($brand, $brands[$brand])) {
                andison_set_flash('success', 'Product updated.');
            } else {
                andison_set_flash('error', 'Failed to save changes.');
            }

            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'delete_product') {
            $idx = isset($_POST['index']) ? (int)$_POST['index'] : -1;
            if ($idx >= 0 && isset($brands[$brand]['products'][$idx])) {
                array_splice($brands[$brand]['products'], $idx, 1);
                if (andison_save_single_brand($brand, $brands[$brand])) {
                    andison_set_flash('success', 'Product deleted.');
                } else {
                    andison_set_flash('error', 'Failed to save changes.');
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
$allCategories = andison_get_categories(); // for category/subcategory dropdowns

// Build flat ID→name lookup for display in the product table
$_catNameMap = [];
foreach ($allCategories as $_cat) {
    $_catNameMap[(string)$_cat['id']] = (string)$_cat['name'];
    foreach ($_cat['subcategories'] ?? [] as $_sub) {
        $_catNameMap[(string)$_sub['id']] = (string)$_sub['name'];
        foreach ($_sub['subcategories'] ?? [] as $_ss) {
            $_catNameMap[(string)$_ss['id']] = (string)$_ss['name'];
        }
    }
}

andison_admin_header('Products', 'products');
?>

<style>
.prod-page-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);border-radius:14px;padding:20px 24px;color:white;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap; }
.prod-brand-select { background:rgba(255,255,255,0.15);border:1.5px solid rgba(255,255,255,0.3);color:white;border-radius:8px;padding:9px 36px 9px 14px;font-size:13px;font-weight:600;appearance:none;-webkit-appearance:none;cursor:pointer;min-width:260px;backdrop-filter:blur(4px);transition:border-color 0.2s; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='white' d='M6 9L1 4h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 12px center; }
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
                        <option value="<?php echo htmlspecialchars($bn); ?>" <?php echo $bn === $selectedBrand ? 'selected' : ''; ?>><?php echo htmlspecialchars($bn); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <form method="post" action="products.php<?php echo $selectedBrand !== '' ? '?brand=' . urlencode($selectedBrand) : ''; ?>" onsubmit="return confirm('Scan all products and remove duplicates?');">
                <input type="hidden" name="action" value="dedup_products">
                <?php if ($selectedBrand !== ''): ?><input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrand); ?>"><?php endif; ?>
                <button type="submit" class="prod-load-btn" title="Remove duplicate product rows from database" style="background:rgba(239,68,68,0.18);border-color:rgba(239,68,68,0.4);">
                    <i class="bi bi-scissors"></i> Remove Duplicates
                </button>
            </form>
            <form method="post" action="products.php" onsubmit="return confirm('DELETE ALL products from the database? This cannot be undone!');">
                <input type="hidden" name="action" value="delete_all_products">
                <button type="submit" class="prod-load-btn" title="Wipe all products from Supabase" style="background:rgba(239,68,68,0.35);border-color:rgba(239,68,68,0.7);font-weight:700;">
                    <i class="bi bi-trash3"></i> Delete All Products
                </button>
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
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:9px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-box-seam" style="color:var(--mint);font-size:15px;"></i>
                    </div>
                    <div>
                        <div style="font-size:15px;font-weight:700;color:#111827;">Product List</div>
                        <div style="font-size:11px;color:#9ca3af;margin-top:1px;"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?> &middot; <?php echo htmlspecialchars($selectedBrand); ?></div>
                    </div>
                </div>
                <button class="btn btn-primary" type="button" onclick="openAddProductModal();" style="font-size:12px;padding:8px 16px;"><i class="bi bi-plus-lg"></i> Add Product</button>
                <button class="btn btn-secondary" type="button" onclick="openImportCsvModal();" style="font-size:12px;padding:8px 16px;margin-left:8px;background:#6b7280;border-color:#6b7280;color:white;border-radius:8px;"><i class="bi bi-upload"></i> Import CSV</button>
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
                                                $dispCat = $_catNameMap[(string)$prod['category_id']] ?? (string)$prod['category_id'];
                                                $dispSub = !empty($prod['subcategory_id']) ? ($_catNameMap[(string)$prod['subcategory_id']] ?? (string)$prod['subcategory_id']) : '';
                                            ?>
                                            <div style="margin-top:3px;"><span style="font-size:10px;color:#6b7280;"><i class="bi bi-folder-check"></i> <?php echo htmlspecialchars($dispCat); ?><?php if ($dispSub !== ''): ?> › <?php echo htmlspecialchars($dispSub); ?><?php endif; ?></span></div>
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
                                                    data-datasheet="<?php echo htmlspecialchars((string)($prod['datasheet'] ?? ''), ENT_QUOTES); ?>"
                                                    style="padding:5px 10px;font-size:11px;">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form method="post" action="products.php?brand=<?php echo urlencode($selectedBrand); ?>" class="delete-form" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrand); ?>">
                                                <input type="hidden" name="index" value="<?php echo (int)$i; ?>">
                                                <button class="btn btn-danger" type="submit" style="padding:5px 10px;font-size:11px;"><i class="bi bi-trash"></i></button>
                                            </form>
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
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:8px;">
                        <div class="field" style="margin:0;">
                            <label for="editCategory"><i class="bi bi-folder"></i> Category</label>
                            <select id="editCategory" onchange="populateCategorySubcategories('','')" title="Select the product category">
                                <option value="">-- None / Brand Only --</option>
                                <?php foreach ($allCategories as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['id'], ENT_QUOTES); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field" style="margin:0;">
                            <label for="editSubcategory"><i class="bi bi-folder2-open"></i> Subcategory</label>
                            <select id="editSubcategory" onchange="populateSubSubcategories('');updateFinalSubcategory();" title="Select the product subcategory">
                                <option value="">-- Select Category First --</option>
                            </select>
                        </div>
                    </div>
                    <div id="editSubSubcategoryWrap" style="display:none;margin-bottom:8px;">
                        <div class="field" style="margin:0;">
                            <label for="editSubSubcategory"><i class="bi bi-folder2"></i> Sub-subcategory <span style="font-size:10px;color:#9ca3af;font-weight:400;">(optional — assign to a specific sub-page)</span></label>
                            <select id="editSubSubcategory" onchange="updateFinalSubcategory();" title="Optional: assign to a sub-subcategory page">
                                <option value="">-- Use Subcategory level --</option>
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
                        <label for="editProductName"><i class="bi bi-tag"></i> Product Name</label>
                        <input id="editProductName" name="product_name" type="text" placeholder="e.g., Panasonic TIG Welding Machine" title="Enter product name">
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div class="field" style="margin:0;">
                            <label for="editModel"><i class="bi bi-tag"></i> Model *</label>
                            <input id="editModel" name="model" type="text" required placeholder="e.g., YD-350KR2" title="Enter product model number or code">
                        </div>
                        
                        <div class="field" style="margin:0;">
                            <label for="editType"><i class="bi bi-folder"></i> Type *</label>
                            <input id="editType" name="type" type="text" required placeholder="e.g., Welding Robot" title="Enter product type or category">
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
                        <label for="editSpecifications">Specifications</label>
                        <textarea id="editSpecifications" name="specifications" rows="3" placeholder="Technical specs, dimensions, power requirements, etc..." style="resize:vertical;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;"></textarea>
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
                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;"><i class="bi bi-images"></i> Product Images <span style="font-size:10px;font-weight:500;color:#9ca3af;text-transform:none;letter-spacing:0;">Up to 5 — first slot is the main image</span></h3>

                    <!-- 5-slot image grid -->
                    <input type="hidden" name="existing_images" id="existingImagesInput" value="[]">
                    <div id="imageSlotGrid" style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:6px;"></div>
                    <div style="font-size:11px;color:#9ca3af;margin-bottom:4px;"><i class="bi bi-info-circle"></i> Click a slot to upload · click × to remove</div>

                    <!-- Hidden file inputs, one per slot -->
                    <div style="display:none;">
                        <?php for ($s = 0; $s < 5; $s++): ?>
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
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
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
    max-width: 600px;
    width: 90%;
    max-height: 95vh;
    height: auto;
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    overflow: hidden;
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

.edit-modal-body {
    padding: 24px;
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1;
    min-height: 150px;
    max-height: calc(95vh - 200px);
    scroll-behavior: smooth;
    scroll-padding: 16px;
    background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 2%, rgba(255,255,255,1) 98%, rgba(0,0,0,0.02) 100%);
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

// Resolve whether subId is a direct subcategory or a sub-subcategory.
// Returns { parentSubId, subSubId } so dropdowns can be set at both levels.
function resolveSubcategoryLevel(catId, subId) {
    if (!catId || !subId) return { parentSubId: '', subSubId: '' };
    var cat = _andisonCategories.find(function(c){ return c.id === catId; });
    if (!cat) return { parentSubId: subId, subSubId: '' };
    // Direct subcategory?
    var direct = (cat.subcategories || []).find(function(s){ return s.id === subId; });
    if (direct) return { parentSubId: subId, subSubId: '' };
    // Sub-subcategory?
    for (var i = 0; i < (cat.subcategories || []).length; i++) {
        var sub = cat.subcategories[i];
        var deep = (sub.subcategories || []).find(function(ss){ return ss.id === subId; });
        if (deep) return { parentSubId: sub.id, subSubId: subId };
    }
    return { parentSubId: subId, subSubId: '' };
}

function populateCategorySubcategories(selectedSubId, selectedSubSubId) {
    var catId   = document.getElementById('editCategory').value;
    var subSel  = document.getElementById('editSubcategory');
    subSel.innerHTML = '<option value="">' + (catId ? '-- Select Subcategory --' : '-- Select Category First --') + '</option>';
    if (!catId) {
        document.getElementById('editSubSubcategoryWrap').style.display = 'none';
        document.getElementById('editSubSubcategory').innerHTML = '<option value="">-- Use Subcategory level --</option>';
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
    populateSubSubcategories(selectedSubSubId || '');
}

function populateSubSubcategories(selectedSubSubId) {
    var catId  = document.getElementById('editCategory').value;
    var subId  = document.getElementById('editSubcategory').value;
    var wrap   = document.getElementById('editSubSubcategoryWrap');
    var subSubSel = document.getElementById('editSubSubcategory');
    if (!catId || !subId) {
        wrap.style.display = 'none';
        subSubSel.innerHTML = '<option value="">-- Use Subcategory level --</option>';
        updateFinalSubcategory();
        return;
    }
    var cat = _andisonCategories.find(function(c){ return c.id === catId; });
    if (!cat) { wrap.style.display = 'none'; updateFinalSubcategory(); return; }
    var sub = (cat.subcategories || []).find(function(s){ return s.id === subId; });
    if (!sub || !sub.subcategories || sub.subcategories.length === 0) {
        wrap.style.display = 'none';
        subSubSel.innerHTML = '<option value="">-- Use Subcategory level --</option>';
        updateFinalSubcategory();
        return;
    }
    subSubSel.innerHTML = '<option value="">-- Use Subcategory level --</option>';
    (sub.subcategories || []).forEach(function(ss){
        var opt = document.createElement('option');
        opt.value = ss.id;
        opt.textContent = ss.name;
        if (selectedSubSubId && ss.id === selectedSubSubId) opt.selected = true;
        subSubSel.appendChild(opt);
    });
    wrap.style.display = 'block';
    updateFinalSubcategory();
}

function updateFinalSubcategory() {
    var subId    = document.getElementById('editSubcategory').value;
    var subSubId = document.getElementById('editSubSubcategory').value;
    var wrap     = document.getElementById('editSubSubcategoryWrap');
    // Use sub-subcategory when it's visible and selected; otherwise use subcategory
    var finalSub = (wrap.style.display !== 'none' && subSubId) ? subSubId : subId;
    // Only update hidden fields when the user has made an actual selection
    var catId = document.getElementById('editCategory').value;
    document.getElementById('finalCategoryId').value    = catId;
    document.getElementById('finalSubcategoryId').value = finalSub;
    refreshCategoryPreview();
}

function refreshCategoryPreview() {
    var preview = document.getElementById('categoryLivePreview');
    if (!preview) return;
    var cat = document.getElementById('finalCategoryId').value;
    var sub = document.getElementById('finalSubcategoryId').value;
    if (!cat) {
        preview.innerHTML = '<span style="color:#b45309;"><i class="bi bi-exclamation-triangle" style="font-size:10px;"></i> No category assigned — product won\'t appear in browse pages.</span>';
        return;
    }
    // Resolve human-readable names
    var catName = cat, subName = sub;
    var catData = _andisonCategories.find(function(c){ return c.id === cat; });
    if (catData) {
        catName = catData.name || cat;
        if (sub) {
            var subData = (catData.subcategories || []).find(function(s){ return s.id === sub; });
            if (subData) {
                subName = subData.name || sub;
            } else {
                (catData.subcategories || []).forEach(function(s) {
                    var ss = (s.subcategories || []).find(function(ss){ return ss.id === sub; });
                    if (ss) subName = (s.name || s.id) + ' › ' + (ss.name || sub);
                });
            }
        }
    }
    preview.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#16a34a;font-size:10px;"></i> '
        + '<span style="color:#166534;font-weight:500;">Assigned: ' + catName
        + (sub ? ' › ' + subName : '') + '</span>';
}

// ── Multi-image slot state ──────────────────────────────────────────────────
var _existingUrls = ['','','','',''];
var _previewUrls  = [null,null,null,null,null];

function _esc(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str));
    return d.innerHTML;
}

function renderImageSlots() {
    var grid = document.getElementById('imageSlotGrid');
    if (!grid) return;
    grid.innerHTML = '';
    for (var i = 0; i < 5; i++) {
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
// ─────────────────────────────────────────────────────────────────────────────

function openEditModal(index, name, model, type, price, badge, description, specifications, image, catId, subId, imagesJson, datasheet) {
    var modal = document.getElementById('editProductModal');
    document.getElementById('editIndex').value = index;
    document.getElementById('editProductName').value = name;
    document.getElementById('editModel').value = model;
    document.getElementById('editType').value = type;
    document.getElementById('editPrice').value = price;
    document.getElementById('editBadge').value = badge;
    document.getElementById('editDescription').value = description;
    document.getElementById('editSpecifications').value = specifications;

    // Populate datasheet
    setDatasheetPreview(datasheet || '');

    // Populate image slots
    _existingUrls = ['','','','',''];
    _previewUrls  = [null,null,null,null,null];
    var imgArr = [];
    try { imgArr = JSON.parse(imagesJson || '[]'); } catch(e) {}
    if (!Array.isArray(imgArr) || imgArr.length === 0) { imgArr = image ? [image] : []; }
    for (var ii = 0; ii < Math.min(imgArr.length, 5); ii++) {
        if (imgArr[ii]) { _existingUrls[ii] = imgArr[ii]; _previewUrls[ii] = imgArr[ii]; }
    }
    for (var jj = 0; jj < 5; jj++) {
        var fi = document.getElementById('imageFile_' + jj);
        if (fi) fi.value = '';
    }
    renderImageSlots();

    // Preserve originals immediately — so saving without touching dropdowns keeps existing values
    document.getElementById('finalCategoryId').value    = catId || '';
    document.getElementById('finalSubcategoryId').value = subId || '';

    // Populate category/subcategory — resolve whether subId is 2nd or 3rd level
    var catSel = document.getElementById('editCategory');
    catSel.value = catId || '';
    var resolved = resolveSubcategoryLevel(catId || '', subId || '');
    populateCategorySubcategories(resolved.parentSubId, resolved.subSubId);

    // If dropdowns couldn't pre-fill (e.g. subcategories not in JS data), keep originals
    if (!document.getElementById('finalCategoryId').value && catId) {
        document.getElementById('finalCategoryId').value = catId;
    }
    if (!document.getElementById('finalSubcategoryId').value && subId) {
        document.getElementById('finalSubcategoryId').value = subId;
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
        var datasheet = this.getAttribute('data-datasheet') || '';
        openEditModal(index, name, model, type, price, badge, description, specifications, image, catId, subId, images, datasheet);
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
            var subSel = document.getElementById('editSubcategory');
            var subSubSel = document.getElementById('editSubSubcategory');
            var wrap = document.getElementById('editSubSubcategoryWrap');
            var activeSubSub = (subSubSel && wrap && wrap.style.display !== 'none') ? subSubSel.value : '';
            if (catSel && catSel.value) document.getElementById('finalCategoryId').value = catSel.value;
            var latestSub = activeSubSub || (subSel ? subSel.value : '');
            if (latestSub) document.getElementById('finalSubcategoryId').value = latestSub;
            f.submit();
        } else {
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
    var modal = document.getElementById('editProductModal');
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
    document.getElementById('editPrice').value = '';
    document.getElementById('editBadge').value = '';
    document.getElementById('editDescription').value = '';
    document.getElementById('editSpecifications').value = '';
    // Clear datasheet
    setDatasheetPreview('');
    // Clear image slots
    _existingUrls = ['','','','',''];
    _previewUrls  = [null,null,null,null,null];
    for (var _s = 0; _s < 5; _s++) { var _fi = document.getElementById('imageFile_' + _s); if (_fi) _fi.value = ''; }
    renderImageSlots();
    document.getElementById('editCategory').value = '';
    document.getElementById('finalCategoryId').value    = '';
    document.getElementById('finalSubcategoryId').value = '';
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



