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

function andison_admin_services_extract_file(array $group, int $index): ?array
{
    if (!isset($group['error'][$index])) {
        return null;
    }

    return [
        'name' => $group['name'][$index] ?? '',
        'type' => $group['type'][$index] ?? '',
        'tmp_name' => $group['tmp_name'][$index] ?? '',
        'error' => $group['error'][$index] ?? UPLOAD_ERR_NO_FILE,
        'size' => $group['size'][$index] ?? 0,
    ];
}

$items = andison_get_services_page_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slugs = $_POST['slug'] ?? [];
    $badges = $_POST['badge'] ?? [];
    $titles = $_POST['title'] ?? [];
    $descriptions = $_POST['description'] ?? [];
    $details = $_POST['details'] ?? [];
    $icons = $_POST['icon'] ?? [];
    $sortOrders = $_POST['sort_order'] ?? [];
    $tealFlags = $_POST['is_teal'] ?? [];
    $reverseFlags = $_POST['is_reverse'] ?? [];
    $currentImages = $_POST['current_image_url'] ?? [];

    $payload = [];
    $imageGroup = $_FILES['image'] ?? [];

    foreach ($slugs as $index => $slugRaw) {
        $slug = trim((string)$slugRaw);
        $title = trim((string)($titles[$index] ?? ''));
        if ($slug === '' || $title === '') {
            continue;
        }

        $imageUrl = trim((string)($currentImages[$index] ?? ''));
        $file = andison_admin_services_extract_file($imageGroup, (int)$index);
        if (is_array($file) && andison_admin_services_is_upload($file)) {
            $stored = andison_admin_services_store_image($file);
            if ($stored === null) {
                andison_set_flash('error', 'Invalid image upload detected. Please use JPG/PNG/WebP/GIF/AVIF files only.');
                header('Location: services.php');
                exit;
            }
            $imageUrl = $stored;
        }

        $payload[] = [
            'slug' => $slug,
            'sort_order' => (int)($sortOrders[$index] ?? $index),
            'badge' => (string)($badges[$index] ?? ''),
            'title' => $title,
            'description' => (string)($descriptions[$index] ?? ''),
            'details' => (string)($details[$index] ?? ''),
            'icon' => (string)($icons[$index] ?? ''),
            'image_url' => $imageUrl,
            'is_teal' => ((string)($tealFlags[$index] ?? '0')) === '1',
            'is_reverse' => ((string)($reverseFlags[$index] ?? '0')) === '1',
        ];
    }

    $ok = andison_save_services_page_content($payload);
    if ($ok) {
        andison_set_flash('success', 'Services page content updated.');
    } else {
        andison_set_flash('error', 'Unable to save Services content. Please ensure Supabase table services_page_content exists and has proper permissions.');
    }

    header('Location: services.php');
    exit;
}

andison_admin_header('Services Page', 'services-page');
?>

<style>
.svc-shell { display:flex;flex-direction:column;gap:16px; }
.svc-hero { background:linear-gradient(135deg,#0f766e 0%,#0d9488 45%,#14b8a6 100%);color:#fff;border-radius:16px;padding:18px 20px;display:flex;justify-content:space-between;align-items:flex-end;gap:14px;flex-wrap:wrap; }
.svc-hero h2 { margin:0;font-size:22px;font-weight:900;display:flex;align-items:center;gap:9px; }
.svc-hero p { margin:6px 0 0;font-size:13px;opacity:0.92;max-width:720px; }
.svc-toolbar { display:flex;justify-content:flex-end;align-items:center;gap:10px;padding:8px 0 0; }
.svc-list { display:flex;flex-direction:column;gap:14px; }
.svc-item { border:1px solid rgba(15,118,110,0.2);border-radius:14px;background:#fff;box-shadow:0 8px 22px rgba(2,6,23,0.07);overflow:hidden; }
.svc-item-head { display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 14px;border-bottom:1px solid #ecfeff;background:linear-gradient(180deg,#ffffff 0%,#f7fdfa 100%); }
.svc-item-left { display:flex;align-items:center;gap:12px; }
.svc-item-num { width:34px;height:34px;border-radius:10px;background:rgba(20,184,166,0.16);color:#0f766e;font-weight:900;display:grid;place-items:center;font-size:13px; }
.svc-item-title { margin:0;font-size:14px;font-weight:800;color:#0f172a; }
.svc-item-meta { margin:2px 0 0;font-size:11px;color:#64748b; }
.svc-tags { display:flex;gap:8px;flex-wrap:wrap; }
.svc-tag { background:#f0fdfa;color:#0f766e;border:1px solid #99f6e4;border-radius:999px;padding:3px 9px;font-size:11px;font-weight:800; }
.svc-head-right { display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end; }
.svc-item-body { display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:14px;padding:14px; }
.svc-item.is-collapsed .svc-item-body { display:none; }
.svc-item.is-collapsed .svc-item-head { border-bottom:none; }
.svc-toggle {
    border:1px solid #99f6e4;background:#ecfeff;color:#115e59;border-radius:999px;
    padding:6px 12px;font-size:11px;font-weight:800;letter-spacing:0.3px;text-transform:uppercase;
    display:inline-flex;align-items:center;gap:6px;cursor:pointer;
}
.svc-toggle:hover { background:#ccfbf1; }
.svc-main { display:flex;flex-direction:column;gap:10px; }
.svc-row { display:grid;grid-template-columns:95px minmax(0,1fr) minmax(0,1fr);gap:10px; }
.svc-side { border-left:1px solid #ecfeff;padding-left:14px;display:flex;flex-direction:column;gap:10px; }
.svc-field { display:flex;flex-direction:column;gap:6px;min-width:0; }
.svc-label { font-size:11px;font-weight:900;letter-spacing:0.4px;text-transform:uppercase;color:#334155; }
.svc-input, .svc-textarea, .svc-file, .svc-select {
    width:100%;border:1px solid #ccfbf1;border-radius:10px;padding:10px 11px;font-size:13px;background:#fff;color:#0f172a;
}
.svc-input:focus, .svc-textarea:focus, .svc-file:focus, .svc-select:focus { outline:none;border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,0.12); }
.svc-textarea { resize:vertical;line-height:1.55;min-height:96px; }
.svc-help { font-size:11px;color:#64748b; }
.svc-thumb-wrap { margin-top:2px;border:1px dashed #99f6e4;border-radius:10px;padding:7px;background:#f7fdfa; }
.svc-thumb { width:100%;height:150px;object-fit:cover;border-radius:8px;border:1px solid #ccfbf1;display:block;background:#e2e8f0; }
.svc-controls { display:grid;grid-template-columns:1fr 1fr;gap:10px; }
@media (max-width: 1080px) {
    .svc-item-body { grid-template-columns:minmax(0,1fr); }
    .svc-side { border-left:none;border-top:1px solid #ecfeff;padding-left:0;padding-top:12px; }
}
@media (max-width: 760px) {
    .svc-row { grid-template-columns:minmax(0,1fr); }
    .svc-controls { grid-template-columns:minmax(0,1fr); }
}
</style>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <form method="post" action="services.php" enctype="multipart/form-data" id="servicesPageForm">
            <div class="svc-shell">
                <div class="svc-hero">
                    <div>
                        <h2><i class="bi bi-gear-wide-connected"></i> Services Content</h2>
                        <p>Refine each service card content, visual style flags, icon, and optional image used on the client-side Services page.</p>
                    </div>
                    <span class="badge"><i class="bi bi-lightning-charge"></i> <?php echo count($items); ?> Services</span>
                </div>

                <div class="svc-toolbar">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Save Services Page</button>
                </div>

                <div class="svc-list">
                    <?php foreach ($items as $i => $item): ?>
                    <article class="svc-item" data-svc-item-key="<?php echo htmlspecialchars((string)($item['slug'] ?? (string)$i), ENT_QUOTES); ?>">
                        <div class="svc-item-head">
                            <div class="svc-item-left">
                                <div class="svc-item-num"><?php echo $i + 1; ?></div>
                                <div>
                                    <h3 class="svc-item-title"><?php echo htmlspecialchars((string)($item['title'] ?? 'Service Item'), ENT_QUOTES); ?></h3>
                                    <p class="svc-item-meta">Slug: <?php echo htmlspecialchars((string)($item['slug'] ?? ''), ENT_QUOTES); ?></p>
                                </div>
                            </div>
                            <div class="svc-head-right">
                                <div class="svc-tags">
                                    <?php if (!empty($item['is_teal'])): ?><span class="svc-tag">Teal</span><?php endif; ?>
                                    <?php if (!empty($item['is_reverse'])): ?><span class="svc-tag">Reverse</span><?php endif; ?>
                                </div>
                                <button type="button" class="svc-toggle" data-svc-toggle><i class="bi bi-chevron-up"></i><span>Minimize</span></button>
                            </div>
                        </div>

                        <div class="svc-item-body">
                            <div class="svc-main">
                                <div class="svc-row">
                                    <div class="svc-field">
                                        <label class="svc-label">Order</label>
                                        <input class="svc-input" type="text" name="sort_order[]" value="<?php echo (int)($item['sort_order'] ?? $i); ?>">
                                    </div>
                                    <div class="svc-field">
                                        <label class="svc-label">Slug</label>
                                        <input class="svc-input" type="text" name="slug[]" value="<?php echo htmlspecialchars((string)($item['slug'] ?? ''), ENT_QUOTES); ?>">
                                        <span class="svc-help">Used as section link on the client page.</span>
                                    </div>
                                    <div class="svc-field">
                                        <label class="svc-label">Badge</label>
                                        <input class="svc-input" type="text" name="badge[]" value="<?php echo htmlspecialchars((string)($item['badge'] ?? ''), ENT_QUOTES); ?>">
                                    </div>
                                </div>

                                <div class="svc-field">
                                    <label class="svc-label">Title</label>
                                    <input class="svc-input" type="text" name="title[]" value="<?php echo htmlspecialchars((string)($item['title'] ?? ''), ENT_QUOTES); ?>" required>
                                </div>

                                <div class="svc-field">
                                    <label class="svc-label">Description</label>
                                    <textarea class="svc-textarea" name="description[]" rows="4"><?php echo htmlspecialchars((string)($item['description'] ?? ''), ENT_QUOTES); ?></textarea>
                                </div>

                                <div class="svc-field">
                                    <label class="svc-label">Details</label>
                                    <textarea class="svc-textarea" name="details[]" rows="5"><?php echo htmlspecialchars((string)($item['details'] ?? ''), ENT_QUOTES); ?></textarea>
                                </div>
                            </div>

                            <aside class="svc-side">
                                <input type="hidden" name="current_image_url[]" value="<?php echo htmlspecialchars((string)($item['image_url'] ?? ''), ENT_QUOTES); ?>">

                                <div class="svc-field">
                                    <label class="svc-label">Icon Class</label>
                                    <input class="svc-input" type="text" name="icon[]" value="<?php echo htmlspecialchars((string)($item['icon'] ?? 'bi-gear'), ENT_QUOTES); ?>" placeholder="bi-gear">
                                </div>

                                <div class="svc-field">
                                    <label class="svc-label">Upload Image</label>
                                    <input class="svc-file" type="file" name="image[]" accept="image/*">
                                </div>

                                <?php if (!empty($item['image_url'])): ?>
                                    <div class="svc-thumb-wrap">
                                        <img class="svc-thumb" src="<?php echo htmlspecialchars((string)$item['image_url'], ENT_QUOTES); ?>" alt="Service image">
                                    </div>
                                <?php endif; ?>

                                <div class="svc-controls">
                                    <div class="svc-field">
                                        <label class="svc-label">Teal Style</label>
                                        <select class="svc-select" name="is_teal[]">
                                            <option value="0" <?php echo !empty($item['is_teal']) ? '' : 'selected'; ?>>No</option>
                                            <option value="1" <?php echo !empty($item['is_teal']) ? 'selected' : ''; ?>>Yes</option>
                                        </select>
                                    </div>
                                    <div class="svc-field">
                                        <label class="svc-label">Reverse Layout</label>
                                        <select class="svc-select" name="is_reverse[]">
                                            <option value="0" <?php echo !empty($item['is_reverse']) ? '' : 'selected'; ?>>No</option>
                                            <option value="1" <?php echo !empty($item['is_reverse']) ? 'selected' : ''; ?>>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </aside>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <div class="svc-toolbar">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Save Services Page</button>
                </div>
            </div>
        </form>
    </section>
</div>

<script>
document.getElementById('servicesPageForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var form = this;
    customConfirm('Save all Services page changes?').then(function (confirmed) {
        if (confirmed) form.submit();
    });
});

(function () {
    var storageKey = 'andison.admin.services.collapsed';
    var map = {};
    try {
        map = JSON.parse(localStorage.getItem(storageKey) || '{}') || {};
    } catch (err) {
        map = {};
    }

    function setState(card, collapsed) {
        var btn = card.querySelector('[data-svc-toggle]');
        var icon = btn ? btn.querySelector('i') : null;
        var text = btn ? btn.querySelector('span') : null;
        card.classList.toggle('is-collapsed', collapsed);
        if (icon) {
            icon.className = collapsed ? 'bi bi-chevron-down' : 'bi bi-chevron-up';
        }
        if (text) {
            text.textContent = collapsed ? 'Expand' : 'Minimize';
        }
    }

    document.querySelectorAll('.svc-item').forEach(function (card) {
        var key = card.getAttribute('data-svc-item-key') || '';
        var btn = card.querySelector('[data-svc-toggle]');
        if (!btn) return;

        if (key && map[key] === true) {
            setState(card, true);
        }

        btn.addEventListener('click', function () {
            var collapsed = !card.classList.contains('is-collapsed');
            setState(card, collapsed);
            if (key) {
                map[key] = collapsed;
                localStorage.setItem(storageKey, JSON.stringify(map));
            }
        });
    });
})();
</script>

<?php andison_admin_footer();
