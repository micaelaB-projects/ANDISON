<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/industries_page_content.php';

function andison_admin_industries_is_upload(array $f): bool
{
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!isset($f['tmp_name']) || !is_file($f['tmp_name'])) {
        return false;
    }
    return true;
}

function andison_admin_industries_store_image(array $f): ?string
{
    $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $info = @getimagesize((string)$f['tmp_name']);
    if ($info === false) {
        return null;
    }

    $safeName = 'industry_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    return andison_sb_storage_upload_tmp($f, 'home-images', 'industries/' . $safeName);
}

function andison_admin_industries_extract_file(array $group): ?array
{
    if (!isset($group['error'])) {
        return null;
    }
    
    if (is_array($group['error'])) {
        return [
            'name' => $group['name'][0] ?? '',
            'type' => $group['type'][0] ?? '',
            'tmp_name' => $group['tmp_name'][0] ?? '',
            'error' => $group['error'][0] ?? UPLOAD_ERR_NO_FILE,
            'size' => $group['size'][0] ?? 0,
        ];
    }

    return [
        'name' => $group['name'] ?? '',
        'type' => $group['type'] ?? '',
        'tmp_name' => $group['tmp_name'] ?? '',
        'error' => $group['error'] ?? UPLOAD_ERR_NO_FILE,
        'size' => $group['size'] ?? 0,
    ];
}

$items = andison_get_industries_page_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $deleteIndex = (int)($_POST['delete_index'] ?? -1);
        if ($deleteIndex >= 0 && isset($items[$deleteIndex])) {
            array_splice($items, $deleteIndex, 1);
            andison_save_industries_page_content($items);
        }
        header('Location: industries.php');
        exit;
    }
    
    if ($action === 'save') {
        $isNew = (int)($_POST['is_new_industry'] ?? 0) === 1;
        $index = $isNew ? null : (int)($_POST['industry_index'] ?? -1);
        
        $slug = trim((string)($_POST['slug'] ?? ''));
        $title = trim((string)($_POST['title'] ?? ''));
        
        if ($slug === '') {
            $slug = generateSlug($title);
        }
        if ($title === '') {
            http_response_code(400);
            exit('Title is required');
        }
        
        $imageUrl = trim((string)($_POST['current_image_url'] ?? ''));
        $file = andison_admin_industries_extract_file($_FILES['image'] ?? []);
        if (is_array($file) && andison_admin_industries_is_upload($file)) {
            $stored = andison_admin_industries_store_image($file);
            if ($stored === null) {
                http_response_code(400);
                exit('Invalid image. Use JPG/PNG/WebP/GIF/AVIF only.');
            }
            $imageUrl = $stored;
        }
        
        $industryData = [
            'slug' => $slug,
            'sort_order' => (int)($_POST['sort_order'] ?? ($isNew ? count($items) : $index)),
            'title' => $title,
            'summary' => (string)($_POST['summary'] ?? ''),
            'details' => (string)($_POST['details'] ?? ''),
            'products_list' => (string)($_POST['products_list'] ?? ''),
            'image_url' => $imageUrl,
        ];
        
        if ($isNew) {
            $items[] = $industryData;
        } elseif ($index >= 0 && isset($items[$index])) {
            $items[$index] = $industryData;
        }
        
        $ok = andison_save_industries_page_content($items);
        if ($ok) {
            http_response_code(200);
            exit('OK');
        } else {
            http_response_code(500);
            exit('Failed to save');
        }
    }
}

function generateSlug($text) {
    return strtolower(trim(preg_replace('/[^\w\s-]/', '', $text)));
}

andison_admin_header('Industries Page', 'industries-page');
?>

<style>
.indx-shell { display:flex;flex-direction:column;gap:16px; }
.indx-hero { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);color:#fff;border-radius:16px;padding:18px 20px;display:flex;justify-content:space-between;align-items:flex-end;gap:14px;flex-wrap:wrap; }
.indx-hero h2 { margin:0;font-size:22px;font-weight:900;display:flex;align-items:center;gap:9px;color:#fff;text-shadow:0 2px 4px rgba(0,0,0,0.3); }
.indx-hero p { margin:6px 0 0;font-size:13px;opacity:1;color:#fff;max-width:720px;text-shadow:0 1px 2px rgba(0,0,0,0.2); }
.indx-toolbar { display:flex;justify-content:space-between;align-items:center;gap:10px;padding:8px 0 0;flex-wrap:wrap; }
.indx-table-wrapper { overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;background:#fff; }
.indx-table { width:100%;border-collapse:collapse;font-size:13px; }
.indx-table thead { background:#f8fafc; }
.indx-table th { padding:12px 14px;text-align:left;font-weight:900;color:#334155;border-bottom:2px solid #e2e8f0;text-transform:uppercase;font-size:11px;letter-spacing:0.3px; }
.indx-table td { padding:12px 14px;border-bottom:1px solid #e2e8f0; }
.indx-table tbody tr:hover { background:#f5f3ff; }
.indx-table-order { width:60px;text-align:center;font-weight:800;color:#4338ca; }
.indx-table-title { font-weight:700;color:#0f172a; }
.indx-table-products { font-size:12px;color:#64748b; }
.indx-table-actions { display:flex;gap:6px;align-items:center; }
.indx-btn-edit, .indx-btn-delete { border:none;background:none;cursor:pointer;padding:4px 8px;border-radius:6px;font-size:13px;font-weight:600;transition:all 0.2s; }
.indx-btn-edit { color:#4338ca;background:rgba(67,56,202,0.08);border:1px solid rgba(67,56,202,0.2); }
.indx-btn-edit:hover { background:rgba(67,56,202,0.14);border-color:rgba(67,56,202,0.4); }
.indx-btn-delete { color:#dc2626;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2); }
.indx-btn-delete:hover { background:rgba(220,38,38,0.14);border-color:rgba(220,38,38,0.4); }

.indx-modal { display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:20px; }
.indx-modal.active { display:flex; }
.indx-modal-content { background:#fff;border-radius:16px;width:100%;max-width:700px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3); }
.indx-modal-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 100%);color:#fff;padding:18px 20px;border-radius:16px 16px 0 0;display:flex;justify-content:space-between;align-items:center;gap:10px;position:sticky;top:0;z-index:1; }
.indx-modal-header h2 { margin:0;font-size:18px;font-weight:900;text-shadow:0 2px 4px rgba(0,0,0,0.3); }
.indx-modal-close { background:none;border:none;color:#fff;font-size:24px;cursor:pointer;width:30px;height:30px;display:grid;place-items:center;border-radius:6px;opacity:0.8;transition:opacity 0.2s; }
.indx-modal-close:hover { opacity:1;background:rgba(255,255,255,0.2); }
.indx-modal-body { padding:20px; }
.indx-modal-section { display:flex;flex-direction:column;gap:14px;margin-bottom:14px; }
.indx-modal-section:last-child { margin-bottom:0; }
.indx-field { display:flex;flex-direction:column;gap:6px; }
.indx-field > label { font-size:12px;font-weight:900;letter-spacing:0.4px;text-transform:uppercase;color:#334155; }
.indx-input, .indx-textarea, .indx-file { width:100%;border:1px solid #e9d5ff;border-radius:8px;padding:10px 12px;font-size:13px;background:#fff;color:#0f172a;font-family:inherit; }
.indx-input:focus, .indx-textarea:focus, .indx-file:focus { outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,0.12); }
.indx-textarea { resize:vertical;line-height:1.5;min-height:90px; }
.indx-textarea.products { min-height:110px; }
.indx-help { font-size:11px;color:#64748b; }
.indx-row { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
.indx-row.full { grid-template-columns:1fr; }
.indx-thumb-wrap { border:1px dashed #d8b4fe;border-radius:8px;padding:8px;background:#faf5ff;text-align:center; }
.indx-thumb { max-width:100%;height:150px;object-fit:contain;border-radius:6px;display:inline-block; }
.indx-modal-footer { padding:14px 20px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;background:#f8fafc;border-radius:0 0 16px 16px;position:sticky;bottom:0; }
.indx-modal-footer button { padding:10px 16px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#0f172a;font-weight:600;font-size:13px;cursor:pointer;transition:all 0.2s; }
.indx-modal-footer button:hover { background:#f3e8ff;border-color:#d8b4fe; }
.indx-modal-footer .btn-primary { background:#7c3aed;color:#fff;border-color:#7c3aed; }
.indx-modal-footer .btn-primary:hover { background:#6d28d9;border-color:#6d28d9; }

.indx-empty { text-align:center;padding:40px 20px;color:#64748b; }
.indx-empty p { margin:0;font-size:14px; }

@media (max-width: 760px) {
    .indx-row { grid-template-columns:1fr; }
    .indx-table { font-size:12px; }
    .indx-table th, .indx-table td { padding:8px 10px; }
}
</style>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <div class="indx-shell">
            <div class="indx-hero">
                <div>
                    <h2><i class="bi bi-buildings"></i> Industries Content</h2>
                    <p>Click any industry to edit. Changes are saved immediately.</p>
                </div>
                <span class="badge"><i class="bi bi-collection"></i> <?php echo count($items); ?> Industries</span>
            </div>

            <div class="indx-toolbar">
                <button id="indxAddBtn" class="btn btn-primary" type="button"><i class="bi bi-plus-circle"></i> Add Industry</button>
            </div>

            <?php if (empty($items)): ?>
                <div class="indx-empty">
                    <p><i class="bi bi-inbox" style="font-size:24px;display:block;margin-bottom:10px;"></i>No industries yet. Click "Add Industry" to create one.</p>
                </div>
            <?php else: ?>
                <div class="indx-table-wrapper">
                    <table class="indx-table">
                        <thead>
                            <tr>
                                <th class="indx-table-order">#</th>
                                <th>Title</th>
                                <th style="width:220px;">Products</th>
                                <th style="width:80px;" style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $i => $item): ?>
                            <tr data-indx-index="<?php echo $i; ?>">
                                <td class="indx-table-order"><?php echo (int)($item['sort_order'] ?? $i + 1); ?></td>
                                <td class="indx-table-title"><?php echo htmlspecialchars($item['title'] ?? '', ENT_QUOTES); ?></td>
                                <td class="indx-table-products">
                                    <?php 
                                    $products = trim($item['products_list'] ?? '');
                                    $productLines = array_filter(array_map('trim', explode("\n", $products)));
                                    $count = count($productLines);
                                    echo $count > 0 ? $count . ' items' : 'No items';
                                    ?>
                                </td>
                                <td>
                                    <div class="indx-table-actions">
                                        <button type="button" class="indx-btn-edit" data-indx-edit data-indx-index="<?php echo $i; ?>"><i class="bi bi-pencil"></i> Edit</button>
                                        <button type="button" class="indx-btn-delete" data-indx-delete data-indx-index="<?php echo $i; ?>"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Edit/Add Modal -->
<div id="indxModal" class="indx-modal">
    <div class="indx-modal-content">
        <div class="indx-modal-header">
            <h2 id="indxModalTitle">Edit Industry</h2>
            <button type="button" class="indx-modal-close" data-indx-modal-close>&times;</button>
        </div>
        <div class="indx-modal-body">
            <form id="indxEditForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="industry_index" id="indxIndustryIndex" value="">
                <input type="hidden" name="current_image_url" id="indxCurrentImageUrl" value="">
                <input type="hidden" name="is_new_industry" id="indxIsNewIndustry" value="0">

                <div class="indx-modal-section">
                    <div class="indx-field full">
                        <label>Order #</label>
                        <input class="indx-input" type="number" name="sort_order" id="indxSortOrder" value="0" required>
                    </div>
                </div>

                <div class="indx-modal-section">
                    <div class="indx-field full">
                        <label>Title *</label>
                        <input class="indx-input" type="text" name="title" id="indxTitle" required placeholder="Industry title">
                    </div>
                </div>

                <div class="indx-modal-section">
                    <div class="indx-field full">
                        <label>Slug (Auto-generated)</label>
                        <input class="indx-input" type="text" name="slug" id="indxSlug" placeholder="Auto-generated from title" readonly>
                    </div>
                </div>

                <div class="indx-modal-section">
                    <div class="indx-field full">
                        <label>Summary (Collapsed View)</label>
                        <textarea class="indx-textarea" name="summary" id="indxSummary" placeholder="Brief description shown initially"></textarea>
                    </div>
                </div>

                <div class="indx-modal-section">
                    <div class="indx-field full">
                        <label>Details (Expanded View)</label>
                        <textarea class="indx-textarea" name="details" id="indxDetails" placeholder="Full description shown when expanded"></textarea>
                    </div>
                </div>

                <div class="indx-modal-section">
                    <div class="indx-field full">
                        <label>Products List (One Per Line)</label>
                        <textarea class="indx-textarea products" name="products_list" id="indxProductsList" placeholder="Arc Welding Equipment&#10;Gas Welding Equipment&#10;Plasma Cutting Equipment"></textarea>
                        <span class="indx-help">Each product on a new line</span>
                    </div>
                </div>

                <div class="indx-modal-section">
                    <div class="indx-field full">
                        <label>Image</label>
                        <input class="indx-file" type="file" name="image" id="indxImage" accept="image/*">
                        <span class="indx-help">JPG, PNG, WebP, GIF, or AVIF</span>
                    </div>
                    <div id="indxThumbWrap" class="indx-thumb-wrap" style="display:none;">
                        <img id="indxThumb" class="indx-thumb" alt="Preview">
                    </div>
                </div>
            </form>
        </div>
        <div class="indx-modal-footer">
            <button type="button" class="btn" data-indx-modal-close>Cancel</button>
            <button type="submit" form="indxEditForm" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Industry</button>
        </div>
    </div>
</div>

<script>
var CURRENT_ITEMS = <?php echo json_encode($items); ?>;

function generateSlug(title) {
    return title
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function openModal(index) {
    var modal = document.getElementById('indxModal');
    var isNew = (index === undefined || index === null || index === '');
    
    if (isNew) {
        document.getElementById('indxModalTitle').textContent = 'Add New Industry';
        document.getElementById('indxIsNewIndustry').value = '1';
        document.getElementById('indxIndustryIndex').value = '';
        document.getElementById('indxTitle').value = '';
        document.getElementById('indxSlug').value = '';
        document.getElementById('indxSummary').value = '';
        document.getElementById('indxDetails').value = '';
        document.getElementById('indxProductsList').value = '';
        document.getElementById('indxCurrentImageUrl').value = '';
        document.getElementById('indxSortOrder').value = CURRENT_ITEMS.length;
        document.getElementById('indxThumbWrap').style.display = 'none';
    } else {
        var item = CURRENT_ITEMS[index];
        if (!item) return;
        document.getElementById('indxModalTitle').textContent = 'Edit Industry';
        document.getElementById('indxIsNewIndustry').value = '0';
        document.getElementById('indxIndustryIndex').value = index;
        document.getElementById('indxTitle').value = item.title || '';
        document.getElementById('indxSlug').value = item.slug || '';
        document.getElementById('indxSummary').value = item.summary || '';
        document.getElementById('indxDetails').value = item.details || '';
        document.getElementById('indxProductsList').value = item.products_list || '';
        document.getElementById('indxCurrentImageUrl').value = item.image_url || '';
        document.getElementById('indxSortOrder').value = parseInt(item.sort_order || (index + 1));
        
        if (item.image_url) {
            document.getElementById('indxThumb').src = item.image_url;
            document.getElementById('indxThumbWrap').style.display = 'block';
        } else {
            document.getElementById('indxThumbWrap').style.display = 'none';
        }
    }
    
    updateSlugPreview();
    modal.classList.add('active');
}

function closeModal() {
    document.getElementById('indxModal').classList.remove('active');
}

function updateSlugPreview() {
    var title = document.getElementById('indxTitle').value;
    var slug = generateSlug(title);
    document.getElementById('indxSlug').value = slug;
}

// Event Listeners
var addBtn = document.getElementById('indxAddBtn');
if (addBtn) {
    addBtn.addEventListener('click', function() {
        openModal();
    });
}

var closeButtons = document.querySelectorAll('[data-indx-modal-close]');
closeButtons.forEach(function(btn) {
    btn.addEventListener('click', closeModal);
});

var modal = document.getElementById('indxModal');
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
}

var editButtons = document.querySelectorAll('[data-indx-edit]');
editButtons.forEach(function(btn) {
    btn.addEventListener('click', function() {
        var index = parseInt(this.getAttribute('data-indx-index'));
        openModal(index);
    });
});

var deleteButtons = document.querySelectorAll('[data-indx-delete]');
deleteButtons.forEach(function(btn) {
    btn.addEventListener('click', function() {
        var index = parseInt(this.getAttribute('data-indx-index'));
        if (confirm('Delete this industry? This cannot be undone.')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'industries.php';
            form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="delete_index" value="' + index + '">';
            document.body.appendChild(form);
            form.submit();
        }
    });
});

var titleField = document.getElementById('indxTitle');
if (titleField) {
    titleField.addEventListener('input', updateSlugPreview);
}

var imageField = document.getElementById('indxImage');
if (imageField) {
    imageField.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('indxThumb').src = e.target.result;
                document.getElementById('indxThumbWrap').style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
}

var editForm = document.getElementById('indxEditForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'save');
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'industries.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                location.reload();
            } else {
                alert('Error saving industry. Please try again.');
            }
        };
        xhr.send(formData);
    });
}
</script>

<?php andison_admin_footer();
