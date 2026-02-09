<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/brands_info.php';

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

function andison_handle_product_upload(string $fieldName = 'image_file'): string
{
    if (empty($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
        return '';
    }

    $f = $_FILES[$fieldName];
    if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return '';
    }

    $tmp = (string)($f['tmp_name'] ?? '');
    $orig = (string)($f['name'] ?? '');
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    if (!in_array($ext, $allowed, true)) {
        return '';
    }

    $base = andison_safe_filename(pathinfo($orig, PATHINFO_FILENAME));
    $destDir = dirname(__DIR__) . '/assets/uploads/products';
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }

    $destName = $base . '_' . date('Ymd_His') . '.' . $ext;
    $destPath = $destDir . '/' . $destName;

    if (!@move_uploaded_file($tmp, $destPath)) {
        return '';
    }

    return 'andison/assets/uploads/products/' . $destName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';
    $brand = isset($_POST['brand']) ? (string)$_POST['brand'] : '';

    if ($brand !== '' && isset($brands[$brand])) {
        if ($action === 'update_brand') {
            $desc = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
            $brands[$brand]['description'] = $desc;
            if (andison_save_brands_info($brands)) {
                andison_set_flash('success', 'Brand description updated.');
            } else {
                andison_set_flash('error', 'Failed to save changes. Check file permissions for /data.');
            }
            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'add_product') {
            $model = isset($_POST['model']) ? trim((string)$_POST['model']) : '';
            $type = isset($_POST['type']) ? trim((string)$_POST['type']) : '';
            $badge = isset($_POST['badge']) ? trim((string)$_POST['badge']) : '';
            $desc = isset($_POST['product_description']) ? trim((string)$_POST['product_description']) : '';
            $image = andison_handle_product_upload('image_file');

            if ($model === '' || $type === '') {
                andison_set_flash('error', 'Model and Type are required.');
                header('Location: products.php?brand=' . urlencode($brand));
                exit;
            }

            if (empty($brands[$brand]['products']) || !is_array($brands[$brand]['products'])) {
                $brands[$brand]['products'] = [];
            }

            $brands[$brand]['products'][] = [
                'model' => $model,
                'type' => $type,
                'badge' => $badge,
                'description' => $desc,
                'image' => $image,
            ];

            if (andison_save_brands_info($brands)) {
                andison_set_flash('success', 'Product added.');
            } else {
                andison_set_flash('error', 'Failed to save changes. Check file permissions for /data.');
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

            $model = isset($_POST['model']) ? trim((string)$_POST['model']) : '';
            $type = isset($_POST['type']) ? trim((string)$_POST['type']) : '';
            $badge = isset($_POST['badge']) ? trim((string)$_POST['badge']) : '';
            $desc = isset($_POST['product_description']) ? trim((string)$_POST['product_description']) : '';
            $existingImage = (string)($brands[$brand]['products'][$idx]['image'] ?? '');
            $newImage = andison_handle_product_upload('image_file');

            if ($model === '' || $type === '') {
                andison_set_flash('error', 'Model and Type are required.');
                header('Location: products.php?brand=' . urlencode($brand) . '&edit=' . $idx);
                exit;
            }

            $brands[$brand]['products'][$idx] = [
                'model' => $model,
                'type' => $type,
                'badge' => $badge,
                'description' => $desc,
                'image' => $newImage !== '' ? $newImage : $existingImage,
            ];

            if (andison_save_brands_info($brands)) {
                andison_set_flash('success', 'Product updated.');
            } else {
                andison_set_flash('error', 'Failed to save changes. Check file permissions for /data.');
            }

            header('Location: products.php?brand=' . urlencode($brand));
            exit;
        }

        if ($action === 'delete_product') {
            $idx = isset($_POST['index']) ? (int)$_POST['index'] : -1;
            if ($idx >= 0 && isset($brands[$brand]['products'][$idx])) {
                array_splice($brands[$brand]['products'], $idx, 1);
                if (andison_save_brands_info($brands)) {
                    andison_set_flash('success', 'Product deleted.');
                } else {
                    andison_set_flash('error', 'Failed to save changes. Check file permissions for /data.');
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

andison_admin_header('Products', 'products');
?>

<div class="grid">
    <!-- Brand Selector Section -->
    <section class="card" style="grid-column:span 12;background:linear-gradient(135deg,rgba(43,17,219,0.05),rgba(16,185,129,0.05));">
        <div style="display:grid;grid-template-columns:1fr auto;gap:16px;align-items:flex-end;">
            <div>
                <h2 style="font-size:20px;margin-bottom:8px;"><i class="bi bi-building" style="color:var(--accent);"></i> Select Brand</h2>
                <p style="font-size:13px;color:var(--muted);margin-bottom:12px;">Choose a brand to manage its products and description</p>
                <form method="get" action="products.php" style="display:flex;gap:8px;">
                    <select id="brand" name="brand" style="flex:1;min-width:300px;">
                        <?php foreach ($brandNames as $bn): ?>
                            <option value="<?php echo htmlspecialchars($bn); ?>" <?php echo $bn === $selectedBrand ? 'selected' : ''; ?>><?php echo htmlspecialchars($bn); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-arrow-repeat"></i> Load</button>
                </form>
            </div>
            <?php if ($selectedBrand !== ''): ?>
                <div style="text-align:right;">
                    <div style="font-size:12px;color:var(--muted);margin-bottom:8px;">Products:</div>
                    <div style="font-size:32px;font-weight:700;color:var(--accent);"><?php echo count($products); ?></div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Brand Description Section -->
    <?php if ($selectedBrand !== ''): ?>
        <section class="card" style="grid-column:span 12;">
            <h3 style="font-size:18px;margin-bottom:12px;"><i class="bi bi-file-text" style="color:#f59e0b;"></i> Brand Description</h3>
            <form method="post" action="products.php?brand=<?php echo urlencode($selectedBrand); ?>" class="brand-desc-form">
                <input type="hidden" name="action" value="update_brand">
                <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrand); ?>">
                <div style="display:grid;grid-template-columns:1fr auto;gap:16px;">
                    <textarea id="description" name="description" style="font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;min-height:100px;padding:12px;border:1px solid #e5e7eb;border-radius:8px;"><?php echo htmlspecialchars((string)($brandInfo['description'] ?? '')); ?></textarea>
                    <button class="btn btn-primary" type="submit" style="height:fit-content;white-space:nowrap;"><i class="bi bi-save"></i> Save Description</button>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <!-- Products Section -->
    <?php if ($selectedBrand !== ''): ?>
        <section class="card" style="grid-column:span 12;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;padding-bottom:16px;border-bottom:2px solid #f0f0f0;">
                <div>
                    <h2 style="font-size:22px;"><i class="bi bi-box-seam" style="color:var(--mint);"></i> Products in <?php echo htmlspecialchars($selectedBrand); ?></h2>
                    <p style="font-size:13px;color:var(--muted);margin-top:4px;"><?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?> listed</p>
                </div>
                <button class="btn btn-primary" type="button" onclick="openAddProductModal();"><i class="bi bi-plus-lg"></i> Add New Product</button>
            </div>

            <!-- Search Bar -->
            <div style="margin-bottom:16px;display:flex;gap:8px;align-items:center;">
                <div class="field" style="flex:1;margin:0;">
                    <input id="productSearch" type="text" placeholder="🔍 Search by model, type, or badge..." style="width:100%;padding:10px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;">
                </div>
            </div>

            <!-- Products Table -->
            <div style="overflow-x:auto;border-radius:12px;border:1px solid #e5e7eb;background:white;">
                <table class="table" id="productsTable" style="width:100%;border-collapse:collapse;">
                    <thead style="background:linear-gradient(135deg,#f9fafb,#f0f0f0);border-bottom:2px solid #e5e7eb;">
                        <tr>
                            <th style="padding:14px;text-align:left;font-weight:600;color:#374151;width:60px;">#</th>
                            <th style="padding:14px;text-align:left;font-weight:600;color:#374151;">Model</th>
                            <th style="padding:14px;text-align:left;font-weight:600;color:#374151;width:140px;">Type</th>
                            <th style="padding:14px;text-align:left;font-weight:600;color:#374151;width:120px;">Badge</th>
                            <th style="padding:14px;text-align:center;font-weight:600;color:#374151;width:120px;">Image</th>
                            <th style="padding:14px;text-align:center;font-weight:600;color:#374151;width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" style="padding:40px;text-align:center;color:var(--muted);">
                                    <i class="bi bi-inbox" style="font-size:32px;opacity:0.3;display:block;margin-bottom:8px;"></i>
                                    No products found for this brand. <a href="#addProductForm" style="color:var(--accent);text-decoration:underline;">Add one now</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                                $totalProducts = count($products);
                                $displayLimit = 10;
                            ?>
                            <?php foreach ($products as $i => $prod): ?>
                                <?php if (!is_array($prod)) { continue; } ?>
                                <tr class="product-row <?php echo $i >= $displayLimit ? 'hidden-row' : ''; ?>" 
                                    data-model="<?php echo htmlspecialchars(strtolower((string)($prod['model'] ?? '')), ENT_QUOTES); ?>" 
                                    data-type="<?php echo htmlspecialchars(strtolower((string)($prod['type'] ?? '')), ENT_QUOTES); ?>" 
                                    data-badge="<?php echo htmlspecialchars(strtolower((string)($prod['badge'] ?? '')), ENT_QUOTES); ?>" 
                                    style="<?php echo $i >= $displayLimit ? 'display:none;' : ''; ?>;border-bottom:1px solid #f0f0f0;transition:background 0.2s ease;"
                                    onmouseover="this.style.background='#f9fafb';"
                                    onmouseout="this.style.background='white';">
                                    <td style="padding:14px;color:#6b7280;font-weight:500;"><?php echo (int)$i + 1; ?></td>
                                    <td style="padding:14px;"><strong><?php echo htmlspecialchars((string)($prod['model'] ?? '')); ?></strong></td>
                                    <td style="padding:14px;color:#6b7280;font-size:13px;"><?php echo htmlspecialchars((string)($prod['type'] ?? '')); ?></td>
                                    <td style="padding:14px;">
                                        <?php if (!empty($prod['badge'])): ?>
                                            <span style="display:inline-block;padding:4px 10px;background:var(--accent);color:white;font-size:11px;font-weight:600;border-radius:20px;"><?php echo htmlspecialchars((string)($prod['badge'] ?? '')); ?></span>
                                        <?php else: ?>
                                            <span style="color:var(--muted);font-size:12px;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:14px;text-align:center;">
                                        <?php 
                                            $imgPath = (string)($prod['image'] ?? '');
                                            if ($imgPath !== ''):
                                                // Convert andison/assets/... to ../assets/... for admin display
                                                $displayPath = $imgPath;
                                                if (strpos($imgPath, 'andison/') === 0) {
                                                    $displayPath = '../' . substr($imgPath, 8);
                                                } elseif (!preg_match('~^(https?://|\.\./)~i', $imgPath)) {
                                                    $displayPath = '../' . $imgPath;
                                                }
                                        ?>
                                            <img src="<?php echo htmlspecialchars($displayPath); ?>" 
                                                 alt="<?php echo htmlspecialchars((string)($prod['model'] ?? '')); ?>" 
                                                 style="width:60px;height:60px;object-fit:contain;border-radius:6px;cursor:pointer;border:1px solid #e5e7eb;transition:all 0.2s ease;background:#f9fafb;" 
                                                 onclick="openImagePreview('<?php echo htmlspecialchars($displayPath, ENT_QUOTES); ?>')"
                                                 onmouseover="this.style.transform='scale(1.1)';this.style.borderColor='var(--accent)';this.style.boxShadow='0 4px 12px rgba(43,17,219,0.15)';"
                                                 onmouseout="this.style.transform='scale(1)';this.style.borderColor='#e5e7eb';this.style.boxShadow='none';"
                                                 title="Click to view full image">
                                        <?php else: ?>
                                            <span style="color:var(--muted);font-size:12px;">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding:14px;text-align:center;">
                                        <div style="display:flex;gap:6px;justify-content:center;">
                                            <button class="btn btn-outline edit-product-btn" type="button" 
                                                    data-index="<?php echo (int)$i; ?>" 
                                                    data-model="<?php echo htmlspecialchars((string)($prod['model'] ?? ''), ENT_QUOTES); ?>" 
                                                    data-type="<?php echo htmlspecialchars((string)($prod['type'] ?? ''), ENT_QUOTES); ?>" 
                                                    data-badge="<?php echo htmlspecialchars((string)($prod['badge'] ?? ''), ENT_QUOTES); ?>" 
                                                    data-description="<?php echo htmlspecialchars((string)($prod['description'] ?? ''), ENT_QUOTES); ?>" 
                                                    data-image="<?php echo htmlspecialchars((string)($prod['image'] ?? ''), ENT_QUOTES); ?>"
                                                    style="padding:6px 12px;font-size:12px;">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form method="post" action="products.php?brand=<?php echo urlencode($selectedBrand); ?>" class="delete-form" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="brand" value="<?php echo htmlspecialchars($selectedBrand); ?>">
                                                <input type="hidden" name="index" value="<?php echo (int)$i; ?>">
                                                <button class="btn btn-danger" type="submit" style="padding:6px 12px;font-size:12px;"><i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($products) && count($products) > 10): ?>
                <div style="text-align:center;margin-top:16px;padding-top:16px;border-top:1px solid #f0f0f0;">
                    <button id="seeMoreBtn" class="btn btn-outline" type="button" onclick="toggleSeeMore()">
                        <i class="bi bi-chevron-down"></i> Show More (<?php echo count($products) - 10; ?> hidden)
                    </button>
                </div>
            <?php endif; ?>
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
                <!-- Basic Info Section -->
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;"><i class="bi bi-info-circle"></i> Product Information</h3>
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

                <!-- Description Section -->
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;"><i class="bi bi-file-text"></i> Description</h3>
                    <div class="field" style="margin:0;">
                        <label for="editDescription">Product Description</label>
                        <textarea id="editDescription" name="product_description" rows="4" placeholder="Add details about features, specifications, and benefits..." style="resize:vertical;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;"></textarea>
                        <div class="hint">Optional but recommended for better product presentation</div>
                    </div>
                </div>

                <!-- Image Section -->
                <div style="margin-bottom:12px;">
                    <h3 style="font-size:13px;font-weight:700;color:var(--accent);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:12px;"><i class="bi bi-image"></i> Product Image</h3>
                    
                    <!-- Current Image Preview -->
                    <div id="currentImageInfo" style="display:none;margin-bottom:14px;padding:12px;background:linear-gradient(135deg,#f0f9ff,#f0fdf4);border-radius:8px;border:1px solid rgba(16,185,129,0.2);">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div id="currentImagePreview" style="width:80px;height:80px;border-radius:6px;background:#fff;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                                <img id="currentImageThumbnail" src="" alt="Current image" style="width:100%;height:100%;object-fit:contain;">
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:12px;font-weight:600;color:#059669;margin-bottom:4px;">Current Image</div>
                                <div style="font-size:12px;color:#6b7280;word-break:break-word;" id="currentImagePath"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload New Image -->
                    <div class="field" style="margin:0;">
                        <label for="editImageFile"><i class="bi bi-upload"></i> Upload New Image</label>
                        <div class="upload-area" 
                             onmouseover="this.style.borderColor='var(--accent)';this.style.backgroundColor='rgba(43,17,219,0.02)';this.querySelector('.upload-icon').style.transform='scale(1.1)';"
                             onmouseout="this.style.borderColor='#e5e7eb';this.style.backgroundColor='#f9fafb';this.querySelector('.upload-icon').style.transform='scale(1)';"
                             onclick="document.getElementById('editImageFile').click();">
                            <div class="upload-content">
                                <i class="bi bi-cloud-upload upload-icon"></i>
                                <div style="font-weight:600;color:#374151;margin-top:8px;">Click to upload or drag & drop</div>
                                <div style="font-size:12px;color:#9ca3af;margin-top:4px;">JPG, PNG, WEBP, GIF or AVIF (max 5MB)</div>
                            </div>
                            <input id="editImageFile" name="image_file" type="file" accept="image/*" style="display:none;" onchange="handleImagePreview(this)">
                        </div>
                        <div id="selectedFileName" style="margin-top:8px;font-size:12px;color:var(--accent);display:none;animation:slideInDown 0.3s ease;"><i class="bi bi-check-circle"></i> <span id="fileName"></span></div>
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
function openEditModal(index, model, type, badge, description, image) {
    var modal = document.getElementById('editProductModal');
    document.getElementById('editIndex').value = index;
    document.getElementById('editModel').value = model;
    document.getElementById('editType').value = type;
    document.getElementById('editBadge').value = badge;
    document.getElementById('editDescription').value = description;
    
    var currentImageInfo = document.getElementById('currentImageInfo');
    var currentImagePath = document.getElementById('currentImagePath');
    if (image) {
        currentImagePath.textContent = image;
        currentImageInfo.style.display = 'block';
    } else {
        currentImageInfo.style.display = 'none';
    }
    
    modal.style.display = 'flex';
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
        var model = this.getAttribute('data-model');
        var type = this.getAttribute('data-type');
        var badge = this.getAttribute('data-badge');
        var description = this.getAttribute('data-description');
        var image = this.getAttribute('data-image');
        openEditModal(index, model, type, badge, description, image);
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
    document.getElementById('editModel').value = '';
    document.getElementById('editType').value = '';
    document.getElementById('editBadge').value = '';
    document.getElementById('editDescription').value = '';
    document.getElementById('editImageFile').value = '';
    document.getElementById('currentImageInfo').style.display = 'none';
    
    // Change submit button text
    form.querySelector('button[type="submit"]').innerHTML = '<i class="bi bi-save"></i> Add Product';
    
    modal.style.display = 'flex';
    // Prevent background scrolling
    document.body.style.overflow = 'hidden';
    document.body.style.paddingRight = '10px';
    document.body.classList.add('modal-open');
}

// See More functionality
function toggleSeeMore() {
    var hiddenRows = document.querySelectorAll('.product-row.hidden-row');
    var btn = document.getElementById('seeMoreBtn');
    var isExpanded = btn.getAttribute('data-expanded') === 'true';
    
    if (isExpanded) {
        // Collapse - hide rows again
        hiddenRows.forEach(function(row){
            row.style.display = 'none';
        });
        btn.innerHTML = '<i class="bi bi-chevron-down"></i> See More (' + hiddenRows.length + ' hidden)';
        btn.setAttribute('data-expanded', 'false');
    } else {
        // Expand - show all rows
        hiddenRows.forEach(function(row){
            row.style.display = '';
        });
        btn.innerHTML = '<i class="bi bi-chevron-up"></i> See Less';
        btn.setAttribute('data-expanded', 'true');
    }
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

// Handle image preview and drag-and-drop
function handleImagePreview(input) {
    var fileInput = document.getElementById('editImageFile');
    var selectedFileNameDiv = document.getElementById('selectedFileName');
    var fileNameSpan = document.getElementById('fileName');
    var uploadArea = fileInput.parentElement;
    
    if (input && input.files && input.files.length > 0) {
        var file = input.files[0];
        
        // Validate file type
        if (!file.type.startsWith('image/')) {
            customAlert('Please select a valid image file');
            return;
        }
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            customAlert('File size must be less than 5MB');
            return;
        }
        
        // Show file name with animation
        fileNameSpan.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        selectedFileNameDiv.style.display = 'block';
        
        // Animate the upload area
        uploadArea.style.borderColor = 'var(--accent)';
        uploadArea.style.backgroundColor = 'rgba(43, 17, 219, 0.05)';
        uploadArea.style.boxShadow = '0 4px 12px rgba(43, 17, 219, 0.2)';
        
        // Show preview if possible
        var reader = new FileReader();
        reader.onload = function(e) {
            var currentImageThumbnail = document.getElementById('currentImageThumbnail');
            var currentImageInfo = document.getElementById('currentImageInfo');
            currentImageThumbnail.src = e.target.result;
            currentImageInfo.style.display = 'block';
            
            // Add animation class
            currentImageInfo.style.animation = 'none';
            setTimeout(function() {
                currentImageInfo.style.animation = 'slideInDown 0.3s ease';
            }, 10);
            
            document.querySelector('#currentImageInfo div:last-child div:last-child').textContent = 'Selected: ' + file.name;
        };
        reader.readAsDataURL(file);
    }
}

// Drag and drop handling with visual feedback
document.addEventListener('DOMContentLoaded', function() {
    var uploadArea = document.querySelector('.upload-area');
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = 'var(--accent)';
            this.style.backgroundColor = 'rgba(43, 17, 219, 0.08)';
            this.style.transform = 'scale(1.01)';
            this.style.boxShadow = '0 8px 24px rgba(43, 17, 219, 0.2)';
            this.querySelector('.upload-icon').style.transform = 'scale(1.2) rotate(-5deg)';
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = '#e5e7eb';
            this.style.backgroundColor = 'linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%)';
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
            this.querySelector('.upload-icon').style.transform = 'scale(1)';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.style.borderColor = '#e5e7eb';
            this.style.backgroundColor = 'linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%)';
            this.style.transform = 'scale(1)';
            this.style.boxShadow = 'none';
            this.querySelector('.upload-icon').style.transform = 'scale(1)';
            
            var files = e.dataTransfer.files;
            if (files && files.length > 0) {
                var fileInput = document.getElementById('editImageFile');
                fileInput.files = files;
                handleImagePreview(fileInput);
            }
        });
    }
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

<?php
andison_admin_footer();
