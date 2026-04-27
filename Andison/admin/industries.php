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

function andison_admin_industries_extract_file(array $group, int $index): ?array
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

$items = andison_get_industries_page_content();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slugs = $_POST['slug'] ?? [];
    $titles = $_POST['title'] ?? [];
    $summaries = $_POST['summary'] ?? [];
    $details = $_POST['details'] ?? [];
    $productsLists = $_POST['products_list'] ?? [];
    $sortOrders = $_POST['sort_order'] ?? [];
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
        $file = andison_admin_industries_extract_file($imageGroup, (int)$index);
        if (is_array($file) && andison_admin_industries_is_upload($file)) {
            $stored = andison_admin_industries_store_image($file);
            if ($stored === null) {
                andison_set_flash('error', 'Invalid image upload detected. Please use JPG/PNG/WebP/GIF/AVIF files only.');
                header('Location: industries.php');
                exit;
            }
            $imageUrl = $stored;
        }

        $payload[] = [
            'slug' => $slug,
            'sort_order' => (int)($sortOrders[$index] ?? $index),
            'title' => $title,
            'summary' => (string)($summaries[$index] ?? ''),
            'details' => (string)($details[$index] ?? ''),
            'products_list' => (string)($productsLists[$index] ?? ''),
            'image_url' => $imageUrl,
        ];
    }

    $ok = andison_save_industries_page_content($payload);
    if ($ok) {
        andison_set_flash('success', 'Industries page content updated.');
    } else {
        andison_set_flash('error', 'Unable to save Industries content. Please ensure Supabase table industries_page_content exists and has proper permissions.');
    }

    header('Location: industries.php');
    exit;
}

andison_admin_header('Industries Page', 'industries-page');
?>

<style>
.indx-shell { display:flex;flex-direction:column;gap:16px; }
.indx-hero { background:linear-gradient(135deg,#1d4ed8 0%,#2563eb 45%,#0ea5e9 100%);color:#fff;border-radius:16px;padding:18px 20px;display:flex;justify-content:space-between;align-items:flex-end;gap:14px;flex-wrap:wrap; }
.indx-hero h2 { margin:0;font-size:22px;font-weight:900;display:flex;align-items:center;gap:9px; }
.indx-hero p { margin:6px 0 0;font-size:13px;opacity:0.92;max-width:700px; }
.indx-toolbar { display:flex;justify-content:flex-end;align-items:center;gap:10px;padding:8px 0 0; }
.indx-list { display:flex;flex-direction:column;gap:14px; }
.indx-item { border:1px solid rgba(29,78,216,0.16);border-radius:14px;background:#fff;box-shadow:0 8px 22px rgba(2,6,23,0.07);overflow:hidden; }
.indx-item-head { display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;border-bottom:1px solid #eef2ff;background:linear-gradient(180deg,#ffffff 0%,#f8fbff 100%); }
.indx-item-head-main { display:flex;align-items:center;gap:12px;min-width:0; }
.indx-item-num { width:34px;height:34px;border-radius:10px;background:rgba(37,99,235,0.14);color:#1d4ed8;font-weight:900;display:grid;place-items:center;font-size:13px; }
.indx-item-title { margin:0;font-size:14px;font-weight:800;color:#0f172a; }
.indx-item-meta { margin:2px 0 0;font-size:11px;color:#64748b; }
.indx-item-body { display:grid;grid-template-columns:minmax(0,1fr) 290px;gap:14px;padding:14px; }
.indx-item.is-collapsed .indx-item-body { display:none; }
.indx-item.is-collapsed .indx-item-head { border-bottom:none; }
.indx-toggle {
    border:1px solid #bfdbfe;background:#eff6ff;color:#1e3a8a;border-radius:999px;
    padding:6px 12px;font-size:11px;font-weight:800;letter-spacing:0.3px;text-transform:uppercase;
    display:inline-flex;align-items:center;gap:6px;cursor:pointer;
}
.indx-toggle:hover { background:#dbeafe; }
.indx-main { display:flex;flex-direction:column;gap:10px; }
.indx-row { display:grid;grid-template-columns:130px minmax(0,1fr);gap:10px; }
.indx-side { border-left:1px solid #eef2ff;padding-left:14px;display:flex;flex-direction:column;gap:10px; }
.indx-field { display:flex;flex-direction:column;gap:6px;min-width:0; }
.indx-label { font-size:11px;font-weight:900;letter-spacing:0.4px;text-transform:uppercase;color:#334155; }
.indx-input, .indx-textarea, .indx-file {
    width:100%;border:1px solid #dbe4ff;border-radius:10px;padding:10px 11px;font-size:13px;background:#fff;color:#0f172a;
}
.indx-input:focus, .indx-textarea:focus, .indx-file:focus { outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
.indx-textarea { resize:vertical;line-height:1.55;min-height:94px; }
.indx-textarea.products { min-height:128px; }
.indx-help { font-size:11px;color:#64748b; }
.indx-thumb-wrap { margin-top:2px;border:1px dashed #c7d2fe;border-radius:10px;padding:7px;background:#f8fbff; }
.indx-thumb { width:100%;height:160px;object-fit:cover;border-radius:8px;border:1px solid #dbeafe;display:block;background:#e2e8f0; }
@media (max-width: 1080px) {
    .indx-item-body { grid-template-columns:minmax(0,1fr); }
    .indx-side { border-left:none;border-top:1px solid #eef2ff;padding-left:0;padding-top:12px; }
}
@media (max-width: 760px) {
    .indx-row { grid-template-columns:minmax(0,1fr); }
}
</style>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <form method="post" action="industries.php" enctype="multipart/form-data" id="industriesPageForm">
            <div class="indx-shell">
                <div class="indx-hero">
                    <div>
                        <h2><i class="bi bi-buildings"></i> Industries Content</h2>
                        <p>Manage each industry card shown on the client-side page, including summaries, expanded descriptions, product bullets, and featured images.</p>
                    </div>
                    <span class="badge"><i class="bi bi-collection"></i> <?php echo count($items); ?> Items</span>
                </div>

                <div class="indx-toolbar">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Save Industries Page</button>
                </div>

                <div class="indx-list">
                    <?php foreach ($items as $i => $item): ?>
                    <article class="indx-item" data-indx-item-key="<?php echo htmlspecialchars((string)($item['slug'] ?? (string)$i), ENT_QUOTES); ?>">
                        <div class="indx-item-head">
                            <div class="indx-item-head-main">
                                <div class="indx-item-num"><?php echo $i + 1; ?></div>
                                <div>
                                    <h3 class="indx-item-title"><?php echo htmlspecialchars((string)($item['title'] ?? 'Industry Item'), ENT_QUOTES); ?></h3>
                                    <p class="indx-item-meta">Slug: <?php echo htmlspecialchars((string)($item['slug'] ?? ''), ENT_QUOTES); ?></p>
                                </div>
                            </div>
                            <button type="button" class="indx-toggle" data-indx-toggle><i class="bi bi-chevron-up"></i><span>Minimize</span></button>
                        </div>

                        <div class="indx-item-body">
                            <div class="indx-main">
                                <div class="indx-row">
                                    <div class="indx-field">
                                        <label class="indx-label">Order</label>
                                        <input class="indx-input" type="text" name="sort_order[]" value="<?php echo (int)($item['sort_order'] ?? $i); ?>">
                                    </div>
                                    <div class="indx-field">
                                        <label class="indx-label">Slug</label>
                                        <input class="indx-input" type="text" name="slug[]" value="<?php echo htmlspecialchars((string)($item['slug'] ?? ''), ENT_QUOTES); ?>">
                                        <span class="indx-help">Used as section anchor on the client page.</span>
                                    </div>
                                </div>

                                <div class="indx-field">
                                    <label class="indx-label">Title</label>
                                    <input class="indx-input" type="text" name="title[]" value="<?php echo htmlspecialchars((string)($item['title'] ?? ''), ENT_QUOTES); ?>" required>
                                </div>

                                <div class="indx-field">
                                    <label class="indx-label">Summary (Collapsed)</label>
                                    <textarea class="indx-textarea" name="summary[]" rows="4"><?php echo htmlspecialchars((string)($item['summary'] ?? ''), ENT_QUOTES); ?></textarea>
                                </div>

                                <div class="indx-field">
                                    <label class="indx-label">Details (Expanded)</label>
                                    <textarea class="indx-textarea" name="details[]" rows="5"><?php echo htmlspecialchars((string)($item['details'] ?? ''), ENT_QUOTES); ?></textarea>
                                </div>

                                <div class="indx-field">
                                    <label class="indx-label">Products List (One Per Line)</label>
                                    <textarea class="indx-textarea products" name="products_list[]" rows="6"><?php echo htmlspecialchars((string)($item['products_list'] ?? ''), ENT_QUOTES); ?></textarea>
                                </div>
                            </div>

                            <aside class="indx-side">
                                <input type="hidden" name="current_image_url[]" value="<?php echo htmlspecialchars((string)($item['image_url'] ?? ''), ENT_QUOTES); ?>">
                                <div class="indx-field">
                                    <label class="indx-label">Upload Image</label>
                                    <input class="indx-file" type="file" name="image[]" accept="image/*">
                                </div>
                                <?php if (!empty($item['image_url'])): ?>
                                    <div class="indx-thumb-wrap">
                                        <img class="indx-thumb" src="<?php echo htmlspecialchars((string)$item['image_url'], ENT_QUOTES); ?>" alt="Industry image">
                                    </div>
                                <?php endif; ?>
                            </aside>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <div class="indx-toolbar">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Save Industries Page</button>
                </div>
            </div>
        </form>
    </section>
</div>

<script>
document.getElementById('industriesPageForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var form = this;
    customConfirm('Save all Industries page changes?').then(function (confirmed) {
        if (confirmed) form.submit();
    });
});

(function () {
    var storageKey = 'andison.admin.industries.collapsed';
    var map = {};
    try {
        map = JSON.parse(localStorage.getItem(storageKey) || '{}') || {};
    } catch (err) {
        map = {};
    }

    function setState(card, collapsed) {
        var btn = card.querySelector('[data-indx-toggle]');
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

    document.querySelectorAll('.indx-item').forEach(function (card) {
        var key = card.getAttribute('data-indx-item-key') || '';
        var btn = card.querySelector('[data-indx-toggle]');
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
