<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/home_slider.php';

$slides = andison_get_home_slider();
 
function andison_admin_is_image_upload(array $f): bool
{
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!isset($f['tmp_name']) || !is_file($f['tmp_name'])) {
        return false;
    }
    return true;
}

function andison_admin_store_slider_image(array $f, string $targetDir, int $index): ?string
{
    $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $info = @getimagesize($f['tmp_name']);
    if ($info === false) {
        return null;
    }

    $safe = 'slide_' . ($index + 1) . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    return andison_sb_storage_upload_tmp($f, 'home-images', 'slider/' . $safe);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = $slides;

    for ($i = 0; $i < 4; $i++) {
        $k = 'slide_' . ($i + 1);
        if (!isset($_FILES[$k]) || !andison_admin_is_image_upload($_FILES[$k])) {
            continue;
        }

        $stored = andison_admin_store_slider_image(
            $_FILES[$k],
            dirname(__DIR__) . '/assets/uploads/home/slider',
            $i
        );

        if ($stored === null) {
            andison_set_flash('error', 'Invalid image upload for Slide ' . ($i + 1) . '. Please use JPG/PNG/WebP/GIF/AVIF.');
            header('Location: slider.php');
            exit;
        }

        $updated[$i] = $stored;
    }

    $ok = andison_save_home_slider($updated);
    if ($ok) {
        andison_set_flash('success', 'Slider images updated.');
    } else {
        andison_set_flash('error', 'Failed to save changes. Check file permissions for /data.');
    }

    header('Location: slider.php');
    exit;
}

andison_admin_header('Homepage Slider', 'slider');
?>

<style>
.slider-page-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);border-radius:14px;padding:18px 22px;color:white;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
.slide-card { background:#fff;border:1.5px solid #e5e7eb;border-radius:12px;padding:16px;transition:border-color 0.2s,box-shadow 0.2s; }
.slide-card:hover { border-color:#2B11DB;box-shadow:0 4px 18px rgba(43,17,219,0.1); }
.slide-num-badge { width:26px;height:26px;border-radius:7px;background:rgba(43,17,219,0.08);color:#2B11DB;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;flex-shrink:0; }
.slide-preview-box { border:1.5px solid #e5e7eb;border-radius:8px;background:#f9fafb;overflow:hidden;aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;margin-bottom:10px;cursor:pointer;transition:border-color 0.2s; }
.slide-preview-box:hover { border-color:#2B11DB; }
.slide-preview-box img { width:100%;height:100%;object-fit:contain; }
.slide-upload-zone { border:2px dashed #e5e7eb;border-radius:8px;padding:12px;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s;background:#f9fafb; }
.slide-upload-zone:hover { border-color:#2B11DB;background:rgba(43,17,219,0.02); }
</style>

<div class="grid">
    <div style="grid-column:span 12;" class="slider-page-header">
        <div>
            <div style="font-size:11px;font-weight:700;opacity:0.7;letter-spacing:0.6px;text-transform:uppercase;margin-bottom:4px;">Homepage Management</div>
            <div style="font-size:20px;font-weight:800;letter-spacing:-0.2px;display:flex;align-items:center;gap:8px;"><i class="bi bi-images" style="color:#fbbf24;"></i> Homepage Slider</div>
        </div>
        <span style="font-size:12px;opacity:0.75;">4-image carousel displayed on the homepage</span>
    </div>

    <section class="card" style="grid-column:span 12;">
        <form method="post" action="slider.php" enctype="multipart/form-data" id="sliderForm">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:18px;">
                <?php for ($i = 0; $i < 4; $i++):
                    $slidePath = (string)($slides[$i] ?? '');
                    $displayPath = $slidePath;
                    if (strpos($slidePath, 'andison/') === 0) {
                        $displayPath = '../' . substr($slidePath, 8);
                    } elseif ($slidePath !== '' && !preg_match('~^(https?://|\.\./|/)~i', $slidePath)) {
                        $displayPath = '../' . $slidePath;
                    }
                ?>
                    <div class="slide-card">
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                            <span class="slide-num-badge"><?php echo $i + 1; ?></span>
                            <span style="font-size:13px;font-weight:700;color:#111827;">Slide <?php echo $i + 1; ?></span>
                        </div>
                        <div class="slide-preview-box" title="Click to preview" data-slide-src="<?php echo htmlspecialchars($displayPath, ENT_QUOTES); ?>" onclick="openSlidePreview(this.getAttribute('data-slide-src'))">
                            <img id="preview_<?php echo $i; ?>" src="<?php echo htmlspecialchars($displayPath); ?>" alt="Slide <?php echo $i + 1; ?>">
                        </div>
                        <div class="slide-upload-zone" onclick="document.getElementById('slide_<?php echo $i + 1; ?>').click();">
                            <i class="bi bi-cloud-upload" style="font-size:20px;color:#2B11DB;display:block;margin-bottom:4px;"></i>
                            <div style="font-size:12px;font-weight:600;color:#374151;margin-bottom:2px;">Click to replace</div>
                            <div style="font-size:10px;color:#9ca3af;">JPG, PNG, WebP, GIF or AVIF</div>
                        </div>
                        <input id="slide_<?php echo $i + 1; ?>" name="slide_<?php echo $i + 1; ?>" type="file" accept="image/*" data-preview-id="preview_<?php echo $i; ?>" style="display:none;">
                    </div>
                <?php endfor; ?>
            </div>
            <div style="display:flex;justify-content:flex-end;padding-top:16px;border-top:1px solid #f3f4f6;">
                <button class="btn btn-primary" type="submit" style="font-size:13px;padding:10px 22px;display:flex;align-items:center;gap:6px;"><i class="bi bi-check-circle"></i> Save All Slides</button>
            </div>
        </form>
    </section>
</div>

<script>
document.getElementById('sliderForm').addEventListener('submit', function(e){
    e.preventDefault();
    var form = this;
    customConfirm('Are you sure you want to update the slider images?').then(function(confirmed){
        if (confirmed) form.submit();
    });
});
</script>

<!-- Preview Modal -->
<div id="previewModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.95);backdrop-filter:blur(4px);z-index:99999;padding:20px;cursor:pointer;animation:fadeIn 0.2s ease;" onclick="this.style.display='none'">
    <div style="position:absolute;top:24px;right:32px;color:#fff;font-size:32px;font-weight:300;cursor:pointer;z-index:100000;transition:all 0.2s;width:40px;height:40px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:rgba(255,255,255,0.1);" onmouseover="this.style.background='rgba(255,255,255,0.2)';this.style.transform='scale(1.1)';" onmouseout="this.style.background='rgba(255,255,255,0.1)';this.style.transform='scale(1)';" onclick="document.getElementById('previewModal').style.display='none'">&times;</div>
    <div id="previewModalContent" style="display:flex;align-items:center;justify-content:center;height:100%;width:100%;"></div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
</style>

<script>
function openSlidePreview(src) {
    var modal = document.getElementById('previewModal');
    var content = document.getElementById('previewModalContent');
    content.innerHTML = '<img src="' + src + '" style="max-width:95%;max-height:95%;border-radius:12px;box-shadow:0 10px 50px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">';
    modal.style.display = 'block';
}

(function(){
    var inputs = document.querySelectorAll('input[type="file"][data-preview-id]');
    inputs.forEach(function(input){
        input.addEventListener('change', function(e){
            if (e.target.files && e.target.files[0]) {
                var previewId = e.target.getAttribute('data-preview-id');
                var preview = document.getElementById(previewId);
                if (preview) {
                    var reader = new FileReader();
                    reader.onload = function(ev){
                        preview.src = ev.target.result;
                        // Update parent container data-slide-src
                        var container = preview.closest('div[data-slide-src]');
                        if (container) {
                            container.setAttribute('data-slide-src', ev.target.result);
                        }
                    };
                    reader.readAsDataURL(e.target.files[0]);
                }
            }
        });
    });
})();
</script>

<?php
andison_admin_footer();



