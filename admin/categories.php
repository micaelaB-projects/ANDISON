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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? (string)$_POST['action'] : '';

    if ($action === 'add_product' && $selectedCategory && $selectedSubcategory) {
        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $model = isset($_POST['model']) ? trim((string)$_POST['model']) : '';
        $badge = isset($_POST['badge']) ? trim((string)$_POST['badge']) : '';
        $desc = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
        $price = isset($_POST['price']) ? trim((string)$_POST['price']) : '';
        $specs = isset($_POST['specs']) ? trim((string)$_POST['specs']) : '';

        if ($name && $model) {
            $products = andison_get_products_for_subcategory($selectedCategory, $selectedSubcategory);

            $image = '';
            if (!empty($_FILES['image']) && andison_admin_is_product_image_upload($_FILES['image'])) {
                $uploaded = andison_admin_store_product_image($_FILES['image'], $selectedCategory, $selectedSubcategory);
                if ($uploaded) {
                    $image = $uploaded;
                }
            }

            $products[] = [
                'id' => $model,
                'name' => $name,
                'model' => $model,
                'type' => '',
                'badge' => $badge,
                'description' => $desc,
                'price' => $price,
                'specs' => $specs,
                'image' => $image,
            ];

            if (andison_save_products_for_subcategory($selectedCategory, $selectedSubcategory, $products)) {
                andison_set_flash('success', 'Product added successfully!');
                header('Location: categories.php?cat=' . urlencode($selectedCategory) . '&sub=' . urlencode($selectedSubcategory));
                exit;
            }
        } else {
            andison_set_flash('error', 'Name and Model are required!');
        }
    } elseif ($action === 'update_product' && $selectedCategory && $selectedSubcategory) {
        $productId = isset($_POST['product_id']) ? (string)$_POST['product_id'] : '';
        $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
        $badge = isset($_POST['badge']) ? trim((string)$_POST['badge']) : '';
        $desc = isset($_POST['description']) ? trim((string)$_POST['description']) : '';
        $price = isset($_POST['price']) ? trim((string)$_POST['price']) : '';
        $specs = isset($_POST['specs']) ? trim((string)$_POST['specs']) : '';

        if ($productId && $name) {
            $products = andison_get_products_for_subcategory($selectedCategory, $selectedSubcategory);

            foreach ($products as &$product) {
                if (($product['id'] ?? '') === $productId) {
                    $product['name'] = $name;
                    $product['badge'] = $badge;
                    $product['description'] = $desc;
                    $product['price'] = $price;
                    $product['specs'] = $specs;

                    if (!empty($_FILES['image']) && andison_admin_is_product_image_upload($_FILES['image'])) {
                        $uploaded = andison_admin_store_product_image($_FILES['image'], $selectedCategory, $selectedSubcategory);
                        if ($uploaded) {
                            $product['image'] = $uploaded;
                        }
                    }

                    break;
                }
            }

            if (andison_save_products_for_subcategory($selectedCategory, $selectedSubcategory, $products)) {
                andison_set_flash('success', 'Product updated successfully!');
                header('Location: categories.php?cat=' . urlencode($selectedCategory) . '&sub=' . urlencode($selectedSubcategory));
                exit;
            }
        }
    } elseif ($action === 'delete_product' && $selectedCategory && $selectedSubcategory) {
        $productId = isset($_POST['product_id']) ? (string)$_POST['product_id'] : '';

        $products = andison_get_products_for_subcategory($selectedCategory, $selectedSubcategory);
        $products = array_filter($products, function($p) use ($productId) {
            return ($p['id'] ?? '') !== $productId;
        });

        if (andison_save_products_for_subcategory($selectedCategory, $selectedSubcategory, array_values($products))) {
            andison_set_flash('success', 'Product deleted successfully!');
            header('Location: categories.php?cat=' . urlencode($selectedCategory) . '&sub=' . urlencode($selectedSubcategory));
            exit;
        }
    }
}

andison_admin_header('Categories & Products', 'categories');
?>

<style>
    .category-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    
    .category-tab {
        padding: 12px 16px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        background: #fff;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .category-tab:hover {
        border-color: var(--accent);
        background: rgba(43,17,219,0.04);
    }
    
    .category-tab.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }
    
    .category-tab i {
        font-size: 16px;
    }
    
    .subcategory-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 24px;
        padding: 16px;
        background: #f9fafb;
        border-radius: 12px;
    }
    
    .subcategory-tab {
        padding: 10px 14px;
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        background: #fff;
        cursor: pointer;
        font-weight: 600;
        font-size: 12px;
        transition: all 0.2s ease;
    }
    
    .subcategory-tab:hover {
        border-color: var(--mint);
        background: rgba(0,215,179,0.04);
    }
    
    .subcategory-tab.active {
        background: var(--mint);
        border-color: var(--mint);
        color: #0b1b16;
    }
    
    .products-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .btn-add-product {
        padding: 12px 20px;
        background: var(--mint);
        color: #0b1b16;
    }
    
    .products-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .products-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .products-table th {
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        color: #6b7280;
        letter-spacing: 0.5px;
    }
    
    .products-table td {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
    }
    
    .products-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .products-table tbody tr:hover {
        background: #f9fafb;
    }
    
    .product-image-thumb {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .product-image-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-image-thumb i {
        font-size: 24px;
        color: #ccc;
    }
    
    .product-name-cell {
        font-weight: 600;
        color: var(--accent);
    }
    
    .product-actions {
        display: flex;
        gap: 8px;
    }
    
    .product-actions .btn {
        padding: 8px 12px;
        font-size: 12px;
        white-space: nowrap;
    }
    
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
    <div class="card">
        <h2><i class="bi bi-shop"></i> Categories</h2>
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

        <div class="card">
            <h2><i class="bi bi-list-check"></i> Subcategories - <?php echo htmlspecialchars($currentCat['name']); ?></h2>
            <div class="subcategory-tabs">
                <?php foreach ($currentCat['subcategories'] ?? [] as $sub): ?>
                    <button class="subcategory-tab <?php echo $selectedSubcategory === $sub['id'] ? 'active' : ''; ?>" onclick="selectSubcategory('<?php echo htmlspecialchars($selectedCategory); ?>', '<?php echo htmlspecialchars($sub['id']); ?>')">
                        <?php echo htmlspecialchars($sub['name']); ?>
                        <span style="font-size: 11px; opacity: 0.7;">(<?php echo count(andison_get_products_for_subcategory($selectedCategory, $sub['id'])); ?>)</span>
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

            <div class="card">
                <div class="products-header">
                    <h2 style="margin: 0;"><i class="bi bi-boxes"></i> Products (<?php echo count($products); ?>)</h2>
                    <button class="btn btn-primary btn-add-product" onclick="openAddProductModal()">
                        <i class="bi bi-plus-circle"></i> Add Product
                    </button>
                </div>

                <div style="font-size: 13px; color: #666; margin-bottom: 20px;">
                    <strong><?php echo htmlspecialchars($currentCat['name']); ?></strong> → <strong><?php echo htmlspecialchars($subName); ?></strong>
                </div>

                <?php if (empty($products)): ?>
                    <p style="text-align: center; color: #999; padding: 40px;">No products yet. Add your first product!</p>
                <?php else: ?>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Image</th>
                                <th>Name</th>
                                <th>Model</th>
                                <th>Price</th>
                                <th>Badge</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $prod): ?>
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
                                    <td class="product-name-cell"><?php echo htmlspecialchars($prod['name']); ?></td>
                                    <td><?php echo htmlspecialchars($prod['model'] ?? $prod['id']); ?></td>
                                    <td><?php echo htmlspecialchars($prod['price'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($prod['badge'] ?? '-'); ?></td>
                                    <td>
                                        <div class="product-actions">
                                            <button class="btn btn-outline" onclick="openEditProductModal(<?php echo htmlspecialchars(json_encode($prod)); ?>)">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="handleDeleteProduct(event);">
                                                <input type="hidden" name="action" value="delete_product">
                                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($prod['id']); ?>">
                                                <button type="submit" class="btn btn-danger" style="padding: 8px 12px; font-size: 12px;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Product Modal -->
<div class="modal-backdrop" id="productModal" onclick="if(event.target===this) closeProductModal();">
    <div class="modal-content" onclick="event.stopPropagation();">
        <div class="modal-header">
            <h2 id="modalTitle"><i class="bi bi-plus-circle"></i> Add Product</h2>
            <button class="modal-close" onclick="closeProductModal()">✕</button>
        </div>

        <form method="POST" enctype="multipart/form-data" id="productForm" onsubmit="return handleProductFormSubmit(event)">
            <input type="hidden" name="action" id="formAction" value="add_product">
            <input type="hidden" name="product_id" id="editProductId">

            <div class="field">
                <label>Product Name *</label>
                <input type="text" name="name" id="productName" required>
            </div>

            <div class="field">
                <label>Model / ID *</label>
                <input type="text" name="model" id="productModel" required>
            </div>

            <div class="field">
                <label>Image</label>
                <div class="image-upload-area">
                    <div id="imagePreview"></div>
                    <input type="file" name="image" id="productImage" accept="image/*" onchange="previewImage(event)">
                    <small style="color: #999;">Select an image (JPG, PNG, WebP, GIF)</small>
                </div>
            </div>

            <div class="field">
                <label>Price</label>
                <input type="text" name="price" id="productPrice" placeholder="e.g., $999.99">
            </div>

            <div class="field">
                <label>Badge</label>
                <input type="text" name="badge" id="productBadge" placeholder="e.g., NEW, FEATURED">
            </div>

            <div class="field">
                <label>Description</label>
                <textarea name="description" id="productDesc" rows="3" placeholder="Product description..."></textarea>
            </div>

            <div class="field">
                <label>Specifications</label>
                <textarea name="specs" id="productSpecs" rows="3" placeholder="Product specifications..."></textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="bi bi-check-circle"></i> Save Product
                </button>
                <button type="button" class="btn btn-outline" onclick="closeProductModal()" style="flex: 1;">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Dialog -->
<div class="confirmation-dialog" id="confirmDialog">
    <div class="confirmation-dialog-content">
        <div class="confirmation-icon">
            <i class="bi bi-question-circle-fill"></i>
        </div>
        <h3>Confirm Action</h3>
        <p id="confirmMessage">Are you sure you want to add this product?</p>
        <div class="confirmation-buttons">
            <button type="button" class="btn-confirm" onclick="confirmProductAction()">
                Yes, Proceed
            </button>
            <button type="button" class="btn-confirm-cancel" onclick="cancelProductAction()">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
let pendingForm = null;
let pendingFormType = null;

function selectCategory(catId) {
    window.location = 'categories.php' + (catId ? '?cat=' + encodeURIComponent(catId) : '');
}

function selectSubcategory(catId, subId) {
    window.location = 'categories.php?cat=' + encodeURIComponent(catId) + '&sub=' + encodeURIComponent(subId);
}

function handleProductFormSubmit(event) {
    event.preventDefault();
    
    const action = document.getElementById('formAction').value;
    const actionText = action === 'add_product' ? 'add this product' : 'update this product';
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to ' + actionText + '?';
    
    pendingForm = document.getElementById('productForm');
    pendingFormType = 'product';
    document.getElementById('confirmDialog').classList.add('show');
    return false;
}

function handleDeleteProduct(event) {
    event.preventDefault();
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to delete this product? This action cannot be undone.';
    
    pendingForm = event.target;
    pendingFormType = 'delete';
    document.getElementById('confirmDialog').classList.add('show');
}

function confirmProductAction() {
    document.getElementById('confirmDialog').classList.remove('show');
    if (pendingForm) {
        pendingForm.submit();
        pendingForm = null;
        pendingFormType = null;
    }
}

function cancelProductAction() {
    document.getElementById('confirmDialog').classList.remove('show');
    pendingForm = null;
    pendingFormType = null;
}

function openAddProductModal() {
    document.getElementById('formAction').value = 'add_product';
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Add Product';
    document.getElementById('editProductId').value = '';
    document.getElementById('productName').value = '';
    document.getElementById('productModel').value = '';
    document.getElementById('productModel').disabled = false;
    document.getElementById('productPrice').value = '';
    document.getElementById('productBadge').value = '';
    document.getElementById('productDesc').value = '';
    document.getElementById('productSpecs').value = '';
    document.getElementById('imagePreview').innerHTML = '';
    document.getElementById('productImage').value = '';
    document.getElementById('productModal').classList.add('show');
}

function openEditProductModal(product) {
    document.getElementById('formAction').value = 'update_product';
    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil-square"></i> Edit Product';
    document.getElementById('editProductId').value = product.id;
    document.getElementById('productName').value = product.name;
    document.getElementById('productModel').value = product.model || product.id;
    document.getElementById('productModel').disabled = true;
    document.getElementById('productPrice').value = product.price || '';
    document.getElementById('productBadge').value = product.badge || '';
    document.getElementById('productDesc').value = product.description || '';
    document.getElementById('productSpecs').value = product.specs || '';
    document.getElementById('productImage').value = '';
    
    if (product.image) {
        const imgPath = product.image.replace('andison/', '');
        document.getElementById('imagePreview').innerHTML = '<img src="../../' + imgPath + '" class="image-preview" alt="Current">';
    } else {
        document.getElementById('imagePreview').innerHTML = '';
    }
    
    document.getElementById('productModal').classList.add('show');
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('show');
}

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '" class="image-preview" alt="Preview">';
        };
        reader.readAsDataURL(file);
    }
}
</script>

<?php andison_admin_footer(); ?>
