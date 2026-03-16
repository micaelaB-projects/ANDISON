<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/categories_info.php';
require_once __DIR__ . '/../includes/products_management.php';

$categories = andison_get_categories();
$categoryList = [];
foreach ($categories as $cat) {
    $categoryList[$cat['id']] = $cat;
}

$selectedCategory = isset($_GET['cat']) ? (string)$_GET['cat'] : '';
$selectedSubcategory = isset($_GET['sub']) ? (string)$_GET['sub'] : '';

// Validate selections
if ($selectedCategory && !isset($categoryList[$selectedCategory])) {
    $selectedCategory = '';
    $selectedSubcategory = '';
}

// This page is now READ-ONLY for products.
// All product add/edit/delete is handled exclusively in products.php (Brands admin).
// This prevents duplicate rows and conflicting writes to the Supabase products table.

// Sync canonical categories → Supabase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'sync_categories') {
    $canonical = andison_get_categories();
    if (!empty($canonical)) {
        if (andison_save_categories($canonical)) {
            andison_set_flash('success', 'Categories synced to database successfully! The product form dropdowns will now show the correct paths.');
        } else {
            andison_set_flash('error', 'Sync failed — check your Supabase connection.');
        }
    } else {
        andison_set_flash('error', 'No categories found in database to sync.');
    }
    header('Location: categories.php');
    exit;
}

andison_admin_header('Categories & Products', 'categories');
?>

<style>
    /* ── Category Tabs ────────────────────────────────── */
    .cat-section-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);border-radius:14px;padding:18px 22px;color:white;margin-bottom:0; }
    .cat-section-header h2 { font-size:11px;font-weight:700;opacity:0.7;letter-spacing:0.6px;text-transform:uppercase;margin:0 0 12px; }
    .category-tabs { display:flex;gap:8px;flex-wrap:wrap; }
    .category-tab { padding:8px 14px;border-radius:999px;border:1.5px solid rgba(255,255,255,0.25);background:rgba(255,255,255,0.1);color:white;cursor:pointer;font-weight:600;font-size:12px;transition:all 0.2s;display:flex;align-items:center;gap:6px; }
    .category-tab:hover { background:rgba(255,255,255,0.2);border-color:rgba(255,255,255,0.5); }
    .category-tab.active { background:white;border-color:white;color:#2B11DB; }
    .category-tab i { font-size:13px; }

    /* ── Subcategory Tabs ─────────────────────────────── */
    .sub-section-title { display:flex;align-items:center;gap:8px;margin-bottom:12px; }
    .sub-section-title .sub-label { font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px; }
    .sub-section-title .sub-cat-name { font-size:13px;font-weight:700;color:#111827; }
    .subcategory-tabs { display:flex;gap:6px;flex-wrap:wrap; }
    .subcategory-tab { display:inline-flex;align-items:center;gap:6px;padding:7px 13px;border-radius:8px;border:1.5px solid #e5e7eb;background:#fff;cursor:pointer;font-weight:600;font-size:12px;color:#374151;transition:all 0.2s; }
    .subcategory-tab:hover { border-color:#10b981;background:rgba(16,185,129,0.05);color:#059669; }
    .subcategory-tab.active { background:#10b981;border-color:#10b981;color:#fff; }
    .subcategory-tab.active .sub-count { background:rgba(255,255,255,0.25);color:white; }
    .sub-count { display:inline-flex;align-items:center;justify-content:center;padding:1px 6px;border-radius:999px;font-size:10px;font-weight:700;background:#f3f4f6;color:#6b7280;min-width:18px;line-height:1.4; }

    /* ── Products Table ───────────────────────────────── */
    .products-table { width:100%;border-collapse:collapse; }
    .products-table thead th { padding:10px 13px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.6px;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
    .products-table thead th:first-child { border-radius:9px 0 0 0; }
    .products-table thead th:last-child { border-radius:0 9px 0 0;text-align:center; }
    .products-table td { padding:12px 13px;border-bottom:1px solid #f3f4f6;vertical-align:middle;font-size:13px; }
    .products-table tbody tr:last-child td { border-bottom:none; }
    .products-table tbody tr:hover { background:#fafbff; }

    /* ── Product Image thumb ──────────────────────────── */
    .product-image-thumb { width:46px;height:46px;border-radius:8px;background:#f3f4f6;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0; }
    .product-image-thumb img { width:100%;height:100%;object-fit:contain; }
    .product-image-thumb i { font-size:18px;color:#d1d5db; }

    /* ── Badge chips ──────────────────────────────────── */
    .cat-badge-chip { display:inline-flex;align-items:center;padding:3px 9px;font-size:10px;font-weight:700;border-radius:999px;white-space:nowrap; }
    .cat-badge-available { background:#dcfce7;color:#16a34a; }
    .cat-badge-unavailable { background:#fee2e2;color:#dc2626; }
    .cat-badge-featured { background:#fef9c3;color:#b45309; }
    .cat-badge-new { background:#ede9fe;color:#7c3aed; }
    .cat-badge-bestseller { background:#fce7f3;color:#be185d; }
    .cat-badge-limited { background:#ffedd5;color:#c2410c; }
    .cat-badge-default { background:#f3f4f6;color:#374151; }

    /* ── Product row actions ──────────────────────────── */
    .product-actions { display:flex;gap:5px;justify-content:center; }
    .product-actions .btn { padding:5px 10px;font-size:11px;white-space:nowrap; }

    /* ── Breadcrumb ───────────────────────────────────── */
    .cat-breadcrumb { display:flex;align-items:center;gap:6px;font-size:12px;color:#9ca3af;margin-bottom:14px; }
    .cat-breadcrumb strong { color:#374151; }
    .cat-breadcrumb i { font-size:10px; }
    
    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 50000;
        justify-content: center;
        align-items: center;
    }
    
    .modal-backdrop.show {
        display: flex;
    }
    
    .modal-content {
        background: #fff;
        border-radius: 20px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 28px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .modal-header h2 {
        margin: 0;
        font-size: 20px;
        color: var(--accent);
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        transition: color 0.2s;
    }
    
    .modal-close:hover {
        color: #333;
    }

    .confirmation-dialog {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 50001;
        justify-content: center;
        align-items: center;
    }

    .confirmation-dialog.show {
        display: flex;
    }

    .confirmation-dialog-content {
        background: #fff;
        border-radius: 20px;
        padding: 40px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }

    .confirmation-icon {
        font-size: 60px;
        color: #4B5CFF;
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 80px;
        height: 80px;
        background: #f0f1ff;
        border-radius: 50%;
        margin-left: auto;
        margin-right: auto;
    }

    .confirmation-dialog-content h3 {
        font-size: 20px;
        color: #333;
        margin: 0 0 16px 0;
    }

    .confirmation-dialog-content p {
        font-size: 14px;
        color: #666;
        margin: 0 0 28px 0;
    }

    .confirmation-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-confirm {
        background: #00D4AA;
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-confirm:hover {
        background: #00b895;
    }

    .btn-confirm-cancel {
        background: transparent;
        color: #4B5CFF;
        border: 2px solid #4B5CFF;
        padding: 10px 28px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-confirm-cancel:hover {
        background: #f0f1ff;
    }
    
    .image-upload-area {
        border: 2px dashed #e5e7eb;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        background: #f9fafb;
        margin-bottom: 16px;
    }
    
    .image-preview {
        max-width: 100%;
        max-height: 200px;
        border-radius: 10px;
        margin-bottom: 12px;
    }
</style>

<div class="grid">
    <!-- Category Selector Header -->
    <div style="grid-column:span 12;" class="cat-section-header">
        <h2>Categories</h2>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
            <form method="post" action="categories.php" onsubmit="return confirm('Re-sync all categories and subcategories from the canonical list to the database? This will update the product form dropdowns.');">
                <input type="hidden" name="action" value="sync_categories">
                <button type="submit" style="background:rgba(255,255,255,0.2);border:1.5px solid rgba(255,255,255,0.4);color:white;border-radius:8px;padding:8px 16px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                    <i class="bi bi-arrow-repeat"></i> Sync Categories to Database
                </button>
            </form>
        </div>
        <div class="category-tabs">
            <?php foreach ($categoryList as $cat): ?>
                <button class="category-tab <?php echo $selectedCategory === $cat['id'] ? 'active' : ''; ?>" onclick="selectCategory('<?php echo htmlspecialchars($cat['id']); ?>')">
                    <i class="bi <?php echo htmlspecialchars($cat['icon']); ?>"></i>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($selectedCategory && isset($categoryList[$selectedCategory])): ?>
        <?php $currentCat = $categoryList[$selectedCategory]; ?>

        <!-- Subcategory Tabs -->
        <div class="card" style="grid-column:span 12;padding:16px 20px;">
            <div class="sub-section-title">
                <div style="width:26px;height:26px;border-radius:7px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-list-check" style="color:#10b981;font-size:12px;"></i>
                </div>
                <span class="sub-label">Subcategories</span>
                <i class="bi bi-chevron-right" style="font-size:10px;color:#d1d5db;"></i>
                <span class="sub-cat-name"><?php echo htmlspecialchars($currentCat['name']); ?></span>
            </div>
            <div class="subcategory-tabs">
                <?php foreach ($currentCat['subcategories'] ?? [] as $sub): ?>
                    <?php $subCount = count(andison_get_products_for_subcategory($selectedCategory, $sub['id'])); ?>
                    <button class="subcategory-tab <?php echo $selectedSubcategory === $sub['id'] ? 'active' : ''; ?>" onclick="selectSubcategory('<?php echo htmlspecialchars($selectedCategory); ?>', '<?php echo htmlspecialchars($sub['id']); ?>')">
                        <?php echo htmlspecialchars($sub['name']); ?>
                        <span class="sub-count"><?php echo $subCount; ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($selectedSubcategory): ?>
            <?php 
                $products = andison_get_products_for_subcategory($selectedCategory, $selectedSubcategory);
                $subName = '';
                foreach ($currentCat['subcategories'] as $s) {
                    if ($s['id'] === $selectedSubcategory) {
                        $subName = $s['name'];
                        break;
                    }
                }
            ?>

            <!-- Products Section -->
            <div class="card" style="grid-column:span 12;">
                <!-- Header -->
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:34px;height:34px;border-radius:9px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-boxes" style="color:#10b981;font-size:15px;"></i>
                        </div>
                        <div>
                            <div style="font-size:15px;font-weight:700;color:#111827;">Product List</div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:1px;"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?></div>
                        </div>
                    </div>
                    <a href="products.php" class="btn btn-primary" style="font-size:12px;padding:8px 16px;text-decoration:none;">
                        <i class="bi bi-box-arrow-up-right"></i> Manage in Products
                    </a>
                </div>

                <!-- Breadcrumb -->
                <div class="cat-breadcrumb">
                    <i class="bi bi-house-door"></i>
                    <strong><?php echo htmlspecialchars($currentCat['name']); ?></strong>
                    <i class="bi bi-chevron-right"></i>
                    <strong><?php echo htmlspecialchars($subName); ?></strong>
                </div>

                <?php if (empty($products)): ?>
                    <div style="text-align:center;padding:48px 20px;">
                        <div style="width:56px;height:56px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;"><i class="bi bi-inbox" style="font-size:24px;color:#d1d5db;"></i></div>
                        <div style="font-weight:600;font-size:14px;color:#374151;margin-bottom:4px;">No products yet</div>
                        <div style="font-size:12px;color:#9ca3af;margin-bottom:14px;">Go to <strong>Products</strong> admin, add a product and assign it to <strong><?php echo htmlspecialchars($subName); ?></strong>.</div>
                        <a href="products.php" class="btn btn-primary" style="font-size:12px;padding:7px 16px;text-decoration:none;"><i class="bi bi-box-arrow-up-right"></i> Go to Products</a>
                    </div>
                <?php else: ?>
                    <div style="overflow-x:auto;border-radius:10px;border:1px solid #e5e7eb;background:white;">
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th style="width:62px;">Image</th>
                                    <th>Name</th>
                                    <th style="width:140px;">Model</th>
                                    <th style="width:110px;">Price</th>
                                    <th style="width:120px;">Badge</th>
                                    <th style="width:130px;text-align:center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $prod): ?>
                                    <?php
                                        $badge = (string)($prod['badge'] ?? '');
                                        $bClass = 'cat-badge-default';
                                        if ($badge === 'Available') $bClass = 'cat-badge-available';
                                        elseif ($badge === 'Not Available') $bClass = 'cat-badge-unavailable';
                                        elseif ($badge === 'Featured') $bClass = 'cat-badge-featured';
                                        elseif ($badge === 'New Arrival') $bClass = 'cat-badge-new';
                                        elseif ($badge === 'Best Seller') $bClass = 'cat-badge-bestseller';
                                        elseif ($badge === 'Limited Stock') $bClass = 'cat-badge-limited';
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="product-image-thumb">
                                                <?php if (!empty($prod['image'])): ?>
                                                    <img src="<?php echo htmlspecialchars('../../' . str_replace('andison/', '', $prod['image'])); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                                                <?php else: ?>
                                                    <i class="bi bi-image"></i>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight:600;font-size:13px;color:#111827;"><?php echo htmlspecialchars($prod['name']); ?></div>
                                        </td>
                                        <td style="color:#6b7280;font-size:12px;"><?php echo htmlspecialchars($prod['model'] ?? $prod['id']); ?></td>
                                        <td style="color:#374151;font-size:12px;"><?php echo htmlspecialchars($prod['price'] ?? '—'); ?></td>
                                        <td>
                                            <?php if ($badge !== '' && $badge !== '-'): ?>
                                                <span class="cat-badge-chip <?php echo $bClass; ?>"><?php echo htmlspecialchars($badge); ?></span>
                                            <?php else: ?>
                                                <span style="color:#d1d5db;font-size:12px;">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="product-actions">
                                                <a href="products.php" class="btn btn-outline" style="padding:5px 10px;font-size:11px;text-decoration:none;" title="Edit in Products admin">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function selectCategory(catId) {
    window.location = 'categories.php' + (catId ? '?cat=' + encodeURIComponent(catId) : '');
}
function selectSubcategory(catId, subId) {
    window.location = 'categories.php?cat=' + encodeURIComponent(catId) + '&sub=' + encodeURIComponent(subId);
}
</script>

<?php andison_admin_footer(); ?>



