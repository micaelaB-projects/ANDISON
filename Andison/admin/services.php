<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/services_page_content.php';

function andison_admin_services_is_upload(array $f): bool
{
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!isset($f['tmp_name']) || !is_file($f['tmp_name'])) {
        return false;
    }
    return true;
}

function andison_admin_services_store_image(array $f): ?string
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

    $safeName = 'service_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    return andison_sb_storage_upload_tmp($f, 'home-images', 'services/' . $safeName);
}

function andison_admin_services_extract_file(array $group): ?array
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

// Bootstrap Icons list for quick selection
$BOOTSTRAP_ICONS = [
    'bi-gear' => 'Gear',
    'bi-lightning-charge' => 'Lightning',
    'bi-tools' => 'Tools',
    'bi-wrench' => 'Wrench',
    'bi-hammer' => 'Hammer',
    'bi-cpu' => 'CPU',
    'bi-server' => 'Server',
    'bi-network' => 'Network',
    'bi-cloud' => 'Cloud',
    'bi-database' => 'Database',
    'bi-graph-up' => 'Analytics',
    'bi-speedometer2' => 'Performance',
    'bi-shield-check' => 'Security',
    'bi-star' => 'Star',
    'bi-rocket' => 'Rocket',
    'bi-puzzle' => 'Integration',
    'bi-gear-wide-connected' => 'Connected',
    'bi-arrow-repeat' => 'Process',
    'bi-boxes' => 'Boxes',
    'bi-layers' => 'Layers',
];

$items = andison_get_services_page_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'delete') {
        $deleteIndex = (int)($_POST['delete_index'] ?? -1);
        if ($deleteIndex >= 0 && isset($items[$deleteIndex])) {
            array_splice($items, $deleteIndex, 1);
            andison_save_services_page_content($items);
        }
        header('Location: services.php');
        exit;
    }
    
    if ($action === 'save') {
        $isNew = (int)($_POST['is_new_service'] ?? 0) === 1;
        $index = $isNew ? null : (int)($_POST['service_index'] ?? -1);
        
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
        $file = $_FILES['image'] ?? null;
        if (is_array($file) && andison_admin_services_is_upload($file)) {
            $stored = andison_admin_services_store_image($file);
            if ($stored === null) {
                http_response_code(400);
                exit('Invalid image. Use JPG/PNG/WebP/GIF/AVIF only.');
            }
            $imageUrl = $stored;
        }
        
        $serviceData = [
            'slug' => $slug,
            'sort_order' => (int)($_POST['sort_order'] ?? ($isNew ? count($items) : $index)),
            'badge' => (string)($_POST['badge'] ?? ''),
            'title' => $title,
            'description' => (string)($_POST['description'] ?? ''),
            'details' => (string)($_POST['details'] ?? ''),
            'icon' => (string)($_POST['icon'] ?? 'bi-gear'),
            'image_url' => $imageUrl,
            'is_teal' => isset($_POST['is_teal']),
            'is_reverse' => isset($_POST['is_reverse']),
        ];
        
        if ($isNew) {
            $items[] = $serviceData;
        } elseif ($index >= 0 && isset($items[$index])) {
            $items[$index] = $serviceData;
        }
        
        $ok = andison_save_services_page_content($items);
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

andison_admin_header('Services Page', 'services-page');
?>

<style>
.svc-shell { display:flex;flex-direction:column;gap:16px; }
.svc-hero { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);color:#fff;border-radius:16px;padding:18px 20px;display:flex;justify-content:space-between;align-items:flex-end;gap:14px;flex-wrap:wrap; }
.svc-hero h2 { margin:0;font-size:22px;font-weight:900;display:flex;align-items:center;gap:9px;color:#fff;text-shadow:0 2px 4px rgba(0,0,0,0.3); }
.svc-hero p { margin:6px 0 0;font-size:13px;opacity:1;color:#fff;max-width:720px;text-shadow:0 1px 2px rgba(0,0,0,0.2); }
.svc-toolbar { display:flex;justify-content:space-between;align-items:center;gap:10px;padding:8px 0 0;flex-wrap:wrap; }
.svc-table-wrapper { overflow-x:auto;border-radius:12px;border:1px solid #e2e8f0;background:#fff; }
.svc-table { width:100%;border-collapse:collapse;font-size:13px; }
.svc-table thead { background:#f8fafc; }
.svc-table th { padding:12px 14px;text-align:left;font-weight:900;color:#334155;border-bottom:2px solid #e2e8f0;text-transform:uppercase;font-size:11px;letter-spacing:0.3px; }
.svc-table td { padding:12px 14px;border-bottom:1px solid #e2e8f0; }
.svc-table tbody tr:hover { background:#f5f3ff; }
.svc-table-order { width:60px;text-align:center;font-weight:800;color:#4338ca; }
.svc-table-icon { width:50px;text-align:center;font-size:20px; }
.svc-table-title { font-weight:700;color:#0f172a; }
.svc-table-badge { font-size:11px;display:inline-block;background:#f3e8ff;color:#5b21b6;border:1px solid #d8b4fe;border-radius:999px;padding:3px 8px; }
.svc-table-tags { display:flex;gap:6px; }
.svc-table-tag { font-size:10px;background:#f3e8ff;color:#5b21b6;border:1px solid #d8b4fe;border-radius:999px;padding:2px 7px;font-weight:600; }
.svc-table-actions { display:flex;gap:6px;align-items:center; }
.svc-btn-edit, .svc-btn-delete { border:none;background:none;cursor:pointer;padding:4px 8px;border-radius:6px;font-size:13px;font-weight:600;transition:all 0.2s; }
.svc-btn-edit { color:#4338ca;background:rgba(67,56,202,0.08);border:1px solid rgba(67,56,202,0.2); }
.svc-btn-edit:hover { background:rgba(67,56,202,0.14);border-color:rgba(67,56,202,0.4); }
.svc-btn-delete { color:#dc2626;background:rgba(220,38,38,0.08);border:1px solid rgba(220,38,38,0.2); }
.svc-btn-delete:hover { background:rgba(220,38,38,0.14);border-color:rgba(220,38,38,0.4); }

.svc-modal { display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:20px; }
.svc-modal.active { display:flex; }
.svc-modal-content { background:#fff;border-radius:16px;width:100%;max-width:700px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.3); }
.svc-modal-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 100%);color:#fff;padding:18px 20px;border-radius:16px 16px 0 0;display:flex;justify-content:space-between;align-items:center;gap:10px;position:sticky;top:0;z-index:1; }
.svc-modal-header h2 { margin:0;font-size:18px;font-weight:900;text-shadow:0 2px 4px rgba(0,0,0,0.3); }
.svc-modal-close { background:none;border:none;color:#fff;font-size:24px;cursor:pointer;width:30px;height:30px;display:grid;place-items:center;border-radius:6px;opacity:0.8;transition:opacity 0.2s; }
.svc-modal-close:hover { opacity:1;background:rgba(255,255,255,0.2); }
.svc-modal-body { padding:20px; }
.svc-modal-section { display:flex;flex-direction:column;gap:14px;margin-bottom:14px; }
.svc-modal-section:last-child { margin-bottom:0; }
.svc-field { display:flex;flex-direction:column;gap:6px; }
.svc-field > label { font-size:12px;font-weight:900;letter-spacing:0.4px;text-transform:uppercase;color:#334155; }
.svc-input, .svc-textarea, .svc-select, .svc-file { width:100%;border:1px solid #e9d5ff;border-radius:8px;padding:10px 12px;font-size:13px;background:#fff;color:#0f172a;font-family:inherit; }
.svc-input:focus, .svc-textarea:focus, .svc-select:focus, .svc-file:focus { outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,0.12); }
.svc-textarea { resize:vertical;line-height:1.5;min-height:90px; }
.svc-help { font-size:11px;color:#64748b; }
.svc-row { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
.svc-row.full { grid-template-columns:1fr; }
.svc-icon-grid { display:grid;grid-template-columns:repeat(5, 1fr);gap:8px;max-height:200px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;padding:8px;background:#f8fafc; }
.svc-icon-btn { border:1px solid #e2e8f0;background:#fff;border-radius:8px;padding:10px;cursor:pointer;text-align:center;transition:all 0.2s;font-size:20px; }
.svc-icon-btn:hover { border-color:#d8b4fe;background:#f3e8ff; }
.svc-icon-btn.active { border-color:#7c3aed;background:#f3e8ff;box-shadow:0 0 0 2px rgba(124,58,237,0.2); }
.svc-thumb-wrap { border:1px dashed #d8b4fe;border-radius:8px;padding:8px;background:#faf5ff;text-align:center; }
.svc-thumb { max-width:100%;height:150px;object-fit:contain;border-radius:6px;display:inline-block; }
.svc-checkbox-group { display:flex;gap:20px; }
.svc-checkbox { display:flex;align-items:center;gap:8px;cursor:pointer; }
.svc-checkbox input { cursor:pointer; }
.svc-modal-footer { padding:14px 20px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;background:#f8fafc;border-radius:0 0 16px 16px;position:sticky;bottom:0; }
.svc-modal-footer button { padding:10px 16px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#0f172a;font-weight:600;font-size:13px;cursor:pointer;transition:all 0.2s; }
.svc-modal-footer button:hover { background:#f3e8ff;border-color:#d8b4fe; }
.svc-modal-footer .btn-primary { background:#7c3aed;color:#fff;border-color:#7c3aed; }
.svc-modal-footer .btn-primary:hover { background:#6d28d9;border-color:#6d28d9; }

.svc-empty { text-align:center;padding:40px 20px;color:#64748b; }
.svc-empty p { margin:0;font-size:14px; }

@media (max-width: 760px) {
    .svc-row { grid-template-columns:1fr; }
    .svc-icon-grid { grid-template-columns:repeat(4, 1fr); }
    .svc-table { font-size:12px; }
    .svc-table th, .svc-table td { padding:8px 10px; }
}
</style>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <div class="svc-shell">
            <div class="svc-hero">
                <div>
                    <h2><i class="bi bi-gear-wide-connected"></i> Services Content</h2>
                    <p>Click any service to edit. Changes are saved immediately.</p>
                </div>
                <span class="badge"><i class="bi bi-lightning-charge"></i> <?php echo count($items); ?> Services</span>
            </div>

            <div class="svc-toolbar">
                <button id="svcAddBtn" class="btn btn-primary" type="button"><i class="bi bi-plus-circle"></i> Add Service</button>
            </div>

            <?php if (empty($items)): ?>
                <div class="svc-empty">
                    <p><i class="bi bi-inbox" style="font-size:24px;display:block;margin-bottom:10px;"></i>No services yet. Click "Add Service" to create one.</p>
                </div>
            <?php else: ?>
                <div class="svc-table-wrapper">
                    <table class="svc-table">
                        <thead>
                            <tr>
                                <th class="svc-table-order">#</th>
                                <th class="svc-table-icon">Icon</th>
                                <th>Title</th>
                                <th style="width:80px;">Badge</th>
                                <th style="width:140px;">Style</th>
                                <th style="width:80px;" style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $i => $item): ?>
                            <tr data-svc-index="<?php echo $i; ?>">
                                <td class="svc-table-order"><?php echo (int)($item['sort_order'] ?? $i + 1); ?></td>
                                <td class="svc-table-icon"><i class="bi <?php echo htmlspecialchars($item['icon'] ?? 'bi-gear', ENT_QUOTES); ?>"></i></td>
                                <td class="svc-table-title"><?php echo htmlspecialchars($item['title'] ?? '', ENT_QUOTES); ?></td>
                                <td><span class="svc-table-badge"><?php echo htmlspecialchars($item['badge'] ?? '', ENT_QUOTES); ?></span></td>
                                <td>
                                    <div class="svc-table-tags">
                                        <?php if (!empty($item['is_teal'])): ?><span class="svc-table-tag">Teal</span><?php endif; ?>
                                        <?php if (!empty($item['is_reverse'])): ?><span class="svc-table-tag">Reverse</span><?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="svc-table-actions">
                                        <button type="button" class="svc-btn-edit" data-svc-edit data-svc-index="<?php echo $i; ?>"><i class="bi bi-pencil"></i> Edit</button>
                                        <button type="button" class="svc-btn-delete" data-svc-delete data-svc-index="<?php echo $i; ?>"><i class="bi bi-trash"></i></button>
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
<div id="svcModal" class="svc-modal">
    <div class="svc-modal-content">
        <div class="svc-modal-header">
            <h2 id="svcModalTitle">Edit Service</h2>
            <button type="button" class="svc-modal-close" data-svc-modal-close>&times;</button>
        </div>
        <div class="svc-modal-body">
            <form id="svcEditForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="service_index" id="svcServiceIndex" value="">
                <input type="hidden" name="current_image_url" id="svcCurrentImageUrl" value="">
                <input type="hidden" name="is_new_service" id="svcIsNewService" value="0">

                <div class="svc-modal-section">
                    <div class="svc-row">
                        <div class="svc-field">
                            <label>Order #</label>
                            <input class="svc-input" type="number" name="sort_order" id="svcSortOrder" value="0" required>
                        </div>
                        <div class="svc-field">
                            <label>Badge</label>
                            <input class="svc-input" type="text" name="badge" id="svcBadge" placeholder="e.g., New, Popular">
                        </div>
                    </div>
                </div>

                <div class="svc-modal-section">
                    <div class="svc-field full">
                        <label>Title *</label>
                        <input class="svc-input" type="text" name="title" id="svcTitle" required placeholder="Service title">
                    </div>
                </div>

                <div class="svc-modal-section">
                    <div class="svc-field full">
                        <label>Description</label>
                        <textarea class="svc-textarea" name="description" id="svcDescription" placeholder="Brief description of the service"></textarea>
                    </div>
                </div>

                <div class="svc-modal-section">
                    <div class="svc-field full">
                        <label>Details</label>
                        <textarea class="svc-textarea" name="details" id="svcDetails" placeholder="Detailed information about the service"></textarea>
                    </div>
                </div>

                <div class="svc-modal-section">
                    <div class="svc-field full">
                        <label>Icon (Click to select)</label>
                        <div class="svc-icon-grid" id="svcIconGrid"></div>
                        <input class="svc-input" type="hidden" name="icon" id="svcIcon" value="bi-gear" required>
                        <span class="svc-help">Selected: <strong id="svcIconDisplay">bi-gear</strong></span>
                    </div>
                </div>

                <div class="svc-modal-section">
                    <div class="svc-field full">
                        <label>Image</label>
                        <input class="svc-file" type="file" name="image" id="svcImage" accept="image/*">
                        <span class="svc-help">JPG, PNG, WebP, GIF, or AVIF</span>
                    </div>
                    <div id="svcThumbWrap" class="svc-thumb-wrap" style="display:none;">
                        <img id="svcThumb" class="svc-thumb" alt="Preview">
                    </div>
                </div>

                <div class="svc-modal-section">
                    <div class="svc-checkbox-group">
                        <label class="svc-checkbox">
                            <input type="checkbox" name="is_teal" id="svcIsTeal" value="1">
                            <span>Teal Style</span>
                        </label>
                        <label class="svc-checkbox">
                            <input type="checkbox" name="is_reverse" id="svcIsReverse" value="1">
                            <span>Reverse Layout</span>
                        </label>
                    </div>
                </div>
            </form>
        </div>
        <div class="svc-modal-footer">
            <button type="button" class="btn" data-svc-modal-close>Cancel</button>
            <button type="submit" form="svcEditForm" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Service</button>
        </div>
    </div>
</div>

<script>
var BOOTSTRAP_ICONS = <?php echo json_encode($BOOTSTRAP_ICONS); ?>;
var CURRENT_ITEMS = <?php echo json_encode($items); ?>;

function generateSlug(title) {
    return title
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-');
}

function populateIconGrid() {
    var grid = document.getElementById('svcIconGrid');
    grid.innerHTML = '';
    Object.keys(BOOTSTRAP_ICONS).forEach(function(icon) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'svc-icon-btn';
        btn.innerHTML = '<i class="bi ' + icon + '"></i>';
        btn.title = BOOTSTRAP_ICONS[icon];
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            selectIcon(icon);
        });
        grid.appendChild(btn);
    });
}

function selectIcon(icon) {
    document.getElementById('svcIcon').value = icon;
    document.getElementById('svcIconDisplay').textContent = icon;
    document.querySelectorAll('.svc-icon-btn').forEach(function(btn) {
        btn.classList.remove('active');
    });
    document.querySelectorAll('.svc-icon-btn').forEach(function(btn) {
        if (btn.innerHTML.includes(icon)) {
            btn.classList.add('active');
        }
    });
}

function openModal(index) {
    var modal = document.getElementById('svcModal');
    var isNew = (index === undefined || index === null || index === '');
    
    if (isNew) {
        document.getElementById('svcModalTitle').textContent = 'Add New Service';
        document.getElementById('svcIsNewService').value = '1';
        document.getElementById('svcServiceIndex').value = '';
        document.getElementById('svcTitle').value = '';
        document.getElementById('svcBadge').value = '';
        document.getElementById('svcDescription').value = '';
        document.getElementById('svcDetails').value = '';
        document.getElementById('svcIcon').value = 'bi-gear';
        document.getElementById('svcIsTeal').checked = false;
        document.getElementById('svcIsReverse').checked = false;
        document.getElementById('svcCurrentImageUrl').value = '';
        document.getElementById('svcSortOrder').value = CURRENT_ITEMS.length;
        document.getElementById('svcThumbWrap').style.display = 'none';
    } else {
        var item = CURRENT_ITEMS[index];
        if (!item) return;
        document.getElementById('svcModalTitle').textContent = 'Edit Service';
        document.getElementById('svcIsNewService').value = '0';
        document.getElementById('svcServiceIndex').value = index;
        document.getElementById('svcTitle').value = item.title || '';
        document.getElementById('svcBadge').value = item.badge || '';
        document.getElementById('svcDescription').value = item.description || '';
        document.getElementById('svcDetails').value = item.details || '';
        document.getElementById('svcIcon').value = item.icon || 'bi-gear';
        document.getElementById('svcIsTeal').checked = item.is_teal ? true : false;
        document.getElementById('svcIsReverse').checked = item.is_reverse ? true : false;
        document.getElementById('svcCurrentImageUrl').value = item.image_url || '';
        document.getElementById('svcSortOrder').value = parseInt(item.sort_order || (index + 1));
        
        if (item.image_url) {
            document.getElementById('svcThumb').src = item.image_url;
            document.getElementById('svcThumbWrap').style.display = 'block';
        } else {
            document.getElementById('svcThumbWrap').style.display = 'none';
        }
    }
    
    selectIcon(document.getElementById('svcIcon').value);
    modal.classList.add('active');
}

function closeModal() {
    document.getElementById('svcModal').classList.remove('active');
}

// Event Listeners
var addBtn = document.getElementById('svcAddBtn');
if (addBtn) {
    addBtn.addEventListener('click', function() {
        openModal();
    });
}

var closeButtons = document.querySelectorAll('[data-svc-modal-close]');
closeButtons.forEach(function(btn) {
    btn.addEventListener('click', closeModal);
});

var modal = document.getElementById('svcModal');
if (modal) {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
}

var editButtons = document.querySelectorAll('[data-svc-edit]');
editButtons.forEach(function(btn) {
    btn.addEventListener('click', function() {
        var index = parseInt(this.getAttribute('data-svc-index'));
        openModal(index);
    });
});

var deleteButtons = document.querySelectorAll('[data-svc-delete]');
deleteButtons.forEach(function(btn) {
    btn.addEventListener('click', function() {
        var index = parseInt(this.getAttribute('data-svc-index'));
        if (confirm('Delete this service? This cannot be undone.')) {
            // Submit a delete request
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'services.php';
            form.innerHTML = '<input type="hidden" name="action" value="delete"><input type="hidden" name="delete_index" value="' + index + '">';
            document.body.appendChild(form);
            form.submit();
        }
    });
});

var titleField = document.getElementById('svcTitle');
if (titleField) {
    // Title field ready for input
}

var imageField = document.getElementById('svcImage');
if (imageField) {
    imageField.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('svcThumb').src = e.target.result;
                document.getElementById('svcThumbWrap').style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
}

var editForm = document.getElementById('svcEditForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'save');
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'services.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                location.reload();
            } else {
                alert('Error saving service. Please try again.');
            }
        };
        xhr.send(formData);
    });
}

populateIconGrid();
</script>

<?php andison_admin_footer();
