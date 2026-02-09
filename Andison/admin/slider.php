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

    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    $safe = 'slide_' . ($index + 1) . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $abs = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $safe;

    if (!@move_uploaded_file($f['tmp_name'], $abs)) {
        return null;
    }

    return 'andison/assets/uploads/home/slider/' . $safe;
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

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid #f0f0f0;">
            <div>
                <h2 style="font-size:24px;font-weight:700;color:#2b11db;"><i class="bi bi-images" style="color:#f59e0b;margin-right:8px;"></i>Homepage Slider</h2>
                <p style="font-size:13px;color:#6b7280;margin-top:4px;">Manage the images displayed in your homepage slider carousel</p>
            </div>
        </div>

        <form method="post" action="slider.php" enctype="multipart/form-data" id="sliderForm">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:24px;margin-bottom:28px;">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div style="background:linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);border:1px solid #e5e7eb;border-radius:12px;padding:20px;transition:all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 24px rgba(43,17,219,0.15)';this.style.borderColor='#2b11db';" onmouseout="this.style.boxShadow='none';this.style.borderColor='#e5e7eb';">
                        <h3 style="font-size:16px;font-weight:700;color:#2b11db;margin-bottom:12px;display:flex;align-items:center;gap:8px;"><i class="bi bi-image" style="color:#f59e0b;"></i>Slide <?php echo $i + 1; ?></h3>
                        
                        <!-- Preview -->
                        <div style="margin-bottom:16px;">
                            <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:8px;display:block;">Current Image</label>
                            <?php 
                                $slidePath = (string)($slides[$i] ?? '');
                                $displayPath = $slidePath;
                                // Convert andison/assets/... to ../assets/... for admin display
                                if (strpos($slidePath, 'andison/') === 0) {
                                    $displayPath = '../' . substr($slidePath, 8);
                                } elseif ($slidePath !== '' && !preg_match('~^(https?://|\.\./|/)~i', $slidePath)) {
                                    $displayPath = '../' . $slidePath;
                                }
                            ?>
                            <div style="border:2px solid #e5e7eb;border-radius:10px;padding:12px;background:linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);cursor:pointer;transition:all 0.3s ease;aspect-ratio:16/9;max-height:200px;display:flex;align-items:center;justify-content:center;overflow:hidden;" title="Click to preview" data-slide-src="<?php echo htmlspecialchars($displayPath, ENT_QUOTES); ?>" onclick="openSlidePreview(this.getAttribute('data-slide-src'))" onmouseover="this.style.borderColor='#2b11db';this.style.backgroundColor='rgba(43,17,219,0.02)';" onmouseout="this.style.borderColor='#e5e7eb';this.style.backgroundColor='linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%)';">
                                <img id="preview_<?php echo $i; ?>" src="<?php echo htmlspecialchars($displayPath); ?>" alt="Slide <?php echo $i + 1; ?>" style="width:100%;height:100%;object-fit:contain;border-radius:8px;pointer-events:none;">
                            </div>
                        </div>

                        <!-- Upload Field -->
                        <div class="field" style="margin:0;">
                            <label for="slide_<?php echo $i + 1; ?>" style="margin-bottom:12px;"><i class="bi bi-upload" style="margin-right:6px;"></i>Replace Image</label>
                            <div style="border:2px dashed #e5e7eb;border-radius:10px;padding:16px;text-align:center;cursor:pointer;transition:all 0.3s ease;background:#f9fafb;" onmouseover="this.style.borderColor='#2b11db';this.style.backgroundColor='rgba(43,17,219,0.02)';" onmouseout="this.style.borderColor='#e5e7eb';this.style.backgroundColor='#f9fafb';" onclick="document.getElementById('slide_<?php echo $i + 1; ?>').click();">
                                <i class="bi bi-cloud-upload" style="font-size:24px;color:#2b11db;display:block;margin-bottom:6px;"></i>
                                <div style="font-weight:600;color:#374151;font-size:13px;margin-bottom:2px;">Click to upload</div>
                                <div style="font-size:11px;color:#9ca3af;">JPG, PNG, WebP, GIF or AVIF</div>
                            </div>
                            <input id="slide_<?php echo $i + 1; ?>" name="slide_<?php echo $i + 1; ?>" type="file" accept="image/*" data-preview-id="preview_<?php echo $i; ?>" style="display:none;">
                        </div>
                        <small style="color:#6b7280;font-size:11px;margin-top:8px;display:block;"><i class="bi bi-info-circle" style="margin-right:4px;"></i>Updates homepage slider background & thumbnail</small>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Save Button -->
            <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:20px;border-top:1px solid #f0f0f0;">
                <button class="btn btn-primary" type="submit" style="padding:12px 24px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-check-circle"></i> Save All Slides</button>
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
