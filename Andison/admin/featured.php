<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/home_featured.php';
require_once __DIR__ . '/../includes/youtube_links.php';

$featured = andison_get_home_featured();
$featuredRows = andison_sb_select('home_featured', 'limit=1');
$featuredRaw = !empty($featuredRows[0]) && is_array($featuredRows[0]) ? $featuredRows[0] : [];
$fallbackDefaultImage = '';
$fallbackDefaultImageAlt = '';
$fallbackDefaultTitle = '';
$fallbackDefaultDescription = '';
if (!empty($featuredRaw)) {
    $fallbackStoredImage = trim((string)($featuredRaw['youtube_url'] ?? ''));
    $fallbackMeta = andison_home_featured_read_fallback_meta($featuredRaw);
    if (andison_home_featured_looks_like_image_reference($fallbackStoredImage)) {
        $fallbackDefaultImage = $fallbackStoredImage;
    }
    $fallbackDefaultImageAlt = $fallbackMeta['image_alt'] ?? '';
    $fallbackDefaultTitle = $fallbackMeta['title'] ?? '';
    $fallbackDefaultDescription = $fallbackMeta['description'] ?? '';
}
 
function andison_admin_is_upload(array $f): bool
{
    if (!isset($f['error']) || $f['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if (!isset($f['tmp_name']) || !is_file($f['tmp_name'])) {
        return false;
    }
    return true;
}

function andison_admin_store_featured_image(array $f, string $targetDir): ?string
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

    $safe = 'featured_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    return andison_sb_storage_upload_tmp($f, 'home-images', 'featured/' . $safe);
}

function andison_admin_store_featured_video(array $f, string $targetDir): ?string
{
    $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
    $allowed = ['mp4', 'webm', 'ogg', 'mov'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    $safe = 'featured_video_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    return andison_sb_storage_upload_tmp($f, 'home-images', 'featured/' . $safe);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $badge = (string)($_POST['badge'] ?? '');
    $title = (string)($_POST['title'] ?? '');
    $description = (string)($_POST['description'] ?? '');
    $buttonText = (string)($_POST['button_text'] ?? '');
    $buttonUrl = (string)($_POST['button_url'] ?? '');
    $imageAlt = (string)($_POST['image_alt'] ?? '');
    $mediaType = 'picture';
    $eventDate = (string)($_POST['event_date'] ?? '');
    $eventLocation = (string)($_POST['event_location'] ?? '');
    $discount = (string)($_POST['discount'] ?? '');
    $offerText = (string)($_POST['offer_text'] ?? '');
    $defaultImageAlt = (string)($_POST['default_image_alt'] ?? '');
    $defaultTitle = (string)($_POST['default_title'] ?? '');
    $defaultDescription = (string)($_POST['default_description'] ?? '');

    $newImagePath = $featured['image'] ?? '';
    $newDefaultImagePath = $fallbackDefaultImage;

    if (in_array($mediaType, ['picture', 'promo'], true) && isset($_FILES['image']) && andison_admin_is_upload($_FILES['image'])) {
        $stored = andison_admin_store_featured_image(
            $_FILES['image'],
            dirname(__DIR__) . '/assets/uploads/home/featured'
        );
        if ($stored === null) {
            andison_set_flash('error', 'Invalid image upload. Please use JPG/PNG/WebP/GIF/AVIF.');
            header('Location: featured.php');
            exit;
        }
        $newImagePath = $stored;
    }

    if (isset($_FILES['default_image']) && andison_admin_is_upload($_FILES['default_image'])) {
        $storedDefault = andison_admin_store_featured_image(
            $_FILES['default_image'],
            dirname(__DIR__) . '/assets/uploads/home/featured'
        );
        if ($storedDefault === null) {
            andison_set_flash('error', 'Invalid default image upload. Please use JPG/PNG/WebP/GIF/AVIF.');
            header('Location: featured.php');
            exit;
        }
        $newDefaultImagePath = $storedDefault;
    }

    $fallbackMetaPayload = json_encode([
        'image_alt' => trim($defaultImageAlt),
        'title' => trim($defaultTitle),
        'description' => trim($defaultDescription),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (!is_string($fallbackMetaPayload) || $fallbackMetaPayload === '') {
        $fallbackMetaPayload = '';
    }

    $ok = andison_save_home_featured([
        'badge' => $badge,
        'title' => $title,
        'description' => $description,
        'button_text' => $buttonText,
        'button_url' => $buttonUrl,
        'event_date' => $eventDate,
        'event_location' => $eventLocation,
        'discount' => $discount,
        'offer_text' => $offerText,
        'media_type' => 'picture',
        'image' => $newImagePath,
        'image_alt' => $imageAlt,
        'youtube_url' => $newDefaultImagePath,
        'video_file' => $fallbackMetaPayload,
    ]);

    if ($ok) {
        andison_set_flash('success', 'Featured section updated.');
    } else {
        andison_set_flash('error', 'Failed to save changes. Check file permissions for /data.');
    }

    header('Location: featured.php');
    exit;
}

andison_admin_header('Homepage Featured', 'featured');
?>

<style>
.feat-page-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);border-radius:14px;padding:18px 22px;color:white;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
.feat-section-hd { display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid #f3f4f6; }
.feat-hd-icon { width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.feat-hd-title { font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#374151; }
.feat-upload-zone { border:2px dashed #e5e7eb;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s;background:#f9fafb;margin-bottom:4px; }
.feat-upload-zone:hover { border-color:#2B11DB;background:rgba(43,17,219,0.02); }
.feat-upload-zone .uz-icon { font-size:26px;color:#2B11DB;display:block;margin-bottom:6px; }
.feat-upload-zone .uz-title { font-weight:600;color:#374151;font-size:13px;margin-bottom:2px; }
.feat-upload-zone .uz-hint { font-size:11px;color:#9ca3af; }
.feat-input { border:1.5px solid #e5e7eb !important;border-radius:8px !important;transition:border-color 0.2s,box-shadow 0.2s !important; }
.feat-input:focus { outline:none !important;border-color:#2B11DB !important;box-shadow:0 0 0 3px rgba(43,17,219,0.08) !important; }
.feat-preview-box { border:1.5px solid #e5e7eb;border-radius:10px;background:#f9fafb;min-height:200px;display:flex;align-items:center;justify-content:center;overflow:hidden;margin-bottom:14px;cursor:pointer;transition:border-color 0.2s; }
.feat-preview-box:hover { border-color:#2B11DB; }
</style>

<div class="grid">
    <div style="grid-column:span 12;" class="feat-page-header">
        <div>
            <div class="feat-page-kicker">Homepage Management</div>
            <div class="feat-page-title"><i class="bi bi-star-fill" style="color:#fbbf24;"></i> Featured Section</div>
        </div>
        <span class="feat-page-subtitle">Controls the featured banner on your homepage</span>
    </div>

    <section class="card" style="grid-column:span 12;">
        <form method="post" action="featured.php" enctype="multipart/form-data" id="featuredForm" class="feat-form">
            <div class="feat-main-col">
            <div class="feat-panel">
                <div class="feat-section-hd">
                    <div class="feat-hd-icon" style="background:rgba(43,17,219,0.1);"><i class="bi bi-info-circle" style="color:#2B11DB;font-size:13px;"></i></div>
                    <span class="feat-hd-title">Content Information</span>
                </div>
                
                <div class="feat-grid-2" style="margin-bottom:14px;">
                    <div class="field" style="margin:0;">
                        <label for="badge">Badge <small style="color:#9ca3af;font-weight:400;">(e.g. EVENTS, NEW)</small></label>
                        <input id="badge" name="badge" type="text" value="<?php echo htmlspecialchars((string)($featured['badge'] ?? '')); ?>" placeholder="e.g., EVENTS" class="feat-input">
                    </div>

                    <div class="field" style="margin:0;">
                        <label>Media Type</label>
                        <input type="text" value="Picture Only" class="feat-input" readonly>
                        <input type="hidden" id="media_type" name="media_type" value="picture">
                    </div>
                </div>

                <div class="field" style="margin:0 0 14px;">
                    <label for="title">Title <span style="color:#ef4444;">*</span></label>
                    <input id="title" name="title" type="text" value="<?php echo htmlspecialchars((string)($featured['title'] ?? '')); ?>" placeholder="Enter featured section title" required class="feat-input" style="font-weight:600;">
                </div>

                <div class="field" style="margin:0;">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Describe the featured content..." rows="3" class="feat-input" style="resize:vertical;font-family:inherit;"><?php echo htmlspecialchars((string)($featured['description'] ?? '')); ?></textarea>
                    <small style="color:#9ca3af;font-size:11px;margin-top:4px;display:block;">Make it engaging and compelling for your visitors</small>
                </div>
            </div>

            <div class="feat-panel">
                <div class="feat-section-hd">
                    <div class="feat-hd-icon" style="background:rgba(245,158,11,0.1);"><i class="bi bi-calendar-event" style="color:#f59e0b;font-size:13px;"></i></div>
                    <span class="feat-hd-title">Event &amp; Sales Information</span>
                    <span class="feat-section-note">Optional</span>
                </div>
                
                <div class="feat-grid-2" style="margin-bottom:14px;">
                    <div class="field" style="margin:0;">
                        <label for="event_date">Event Date</label>
                        <input id="event_date" name="event_date" type="text" value="<?php echo htmlspecialchars((string)($featured['event_date'] ?? '')); ?>" placeholder="e.g., February 10, 2026" class="feat-input">
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="event_location">Event Location</label>
                        <input id="event_location" name="event_location" type="text" value="<?php echo htmlspecialchars((string)($featured['event_location'] ?? '')); ?>" placeholder="e.g., Industrial Expo Center" class="feat-input">
                    </div>
                </div>

                <div class="feat-grid-2">
                    <div class="field" style="margin:0;">
                        <label for="discount">Discount / Offer</label>
                        <input id="discount" name="discount" type="text" value="<?php echo htmlspecialchars((string)($featured['discount'] ?? '')); ?>" placeholder="e.g., 20% OFF" class="feat-input">
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="offer_text">Offer Text</label>
                        <input id="offer_text" name="offer_text" type="text" value="<?php echo htmlspecialchars((string)($featured['offer_text'] ?? '')); ?>" placeholder="e.g., Limited Time Offer" class="feat-input">
                    </div>
                </div>
            </div>

            <div class="feat-panel">
                <div class="feat-section-hd">
                    <div class="feat-hd-icon" style="background:rgba(16,185,129,0.1);"><i class="bi bi-hand-index-fill" style="color:#10b981;font-size:12px;"></i></div>
                    <span class="feat-hd-title">Call-to-Action Button</span>
                </div>

                <div class="feat-grid-2">
                    <div class="field" style="margin:0;">
                        <label for="button_text">Button Text</label>
                        <input id="button_text" name="button_text" type="text" value="<?php echo htmlspecialchars((string)($featured['button_text'] ?? '')); ?>" placeholder="e.g., Learn More, Shop Now" class="feat-input">
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="button_url">Button URL</label>
                        <input id="button_url" name="button_url" type="text" value="<?php echo htmlspecialchars((string)($featured['button_url'] ?? '')); ?>" placeholder="e.g., #products or https://..." class="feat-input">
                    </div>
                </div>
            </div>
            </div>

            <div class="feat-side-col">
            <div class="feat-panel feat-panel--sticky">
                <div class="feat-section-hd">
                    <div class="feat-hd-icon" style="background:rgba(139,92,246,0.1);"><i class="bi bi-camera" style="color:#8b5cf6;font-size:13px;"></i></div>
                    <span class="feat-hd-title">Media Content</span>
                </div>
                
                <!-- Preview -->
                <div class="feat-preview-box" id="preview_container" title="Click to view full size">
                    <?php
                    $imgPath = (string)($featured['image'] ?? '');
                    $displayImgPath = $imgPath;
                    // Convert andison/assets/... to ../assets/... for admin display
                    if (strpos($imgPath, 'andison/') === 0) {
                        $displayImgPath = '../' . substr($imgPath, 8);
                    } elseif ($imgPath !== '' && !preg_match('~^(https?://|\.\./|/)~i', $imgPath)) {
                        $displayImgPath = '../' . $imgPath;
                    }
                    if ($displayImgPath !== ''):
                        $img = htmlspecialchars($displayImgPath, ENT_QUOTES);
                    ?>
                        <img id="image_preview" src="<?php echo $img; ?>" alt="preview" style="width:100%;max-height:280px;object-fit:contain;border-radius:8px;box-shadow:0 4px 12px rgba(43,17,219,0.1);" data-src="<?php echo $img; ?>">
                    <?php else: ?>
                        <div style="color:#94a3b8;font-size:13px;">No image uploaded yet</div>
                    <?php endif; ?>
                    </div>
                </div>

                <div class="feat-upload-stack">
                <!-- Upload Fields -->
                <div id="field_picture" style="display:block;">
                    <div class="feat-upload-zone" onclick="document.getElementById('image').click();">
                        <i class="bi bi-cloud-upload uz-icon"></i>
                        <div class="uz-title">Click to upload picture</div>
                        <div class="uz-hint">JPG, PNG, WebP, GIF or AVIF</div>
                    </div>
                    <input id="image" name="image" type="file" accept="image/*" style="display:none;">
                    <div class="field" style="margin:10px 0 0;">
                        <label for="image_alt">Image Alt Text</label>
                        <input id="image_alt" name="image_alt" type="text" value="<?php echo htmlspecialchars((string)($featured['image_alt'] ?? '')); ?>" placeholder="Describe the image for accessibility" class="feat-input">
                        <small style="color:#9ca3af;font-size:11px;margin-top:4px;display:block;">Used for accessibility and SEO</small>
                    </div>

                    <div style="margin:14px 0;border-top:1px solid #eef2f7;"></div>

                    <?php
                    $defaultDisplayImgPath = '';
                    if ($fallbackDefaultImage !== '') {
                        $defaultDisplayImgPath = $fallbackDefaultImage;
                        if (strpos($defaultDisplayImgPath, 'andison/') === 0) {
                            $defaultDisplayImgPath = '../' . substr($defaultDisplayImgPath, 8);
                        } elseif (!preg_match('~^(https?://|\.\./|/)~i', $defaultDisplayImgPath)) {
                            $defaultDisplayImgPath = '../' . $defaultDisplayImgPath;
                        }
                    }
                    ?>

                    <label style="display:block;font-size:11px;font-weight:700;color:#374151;letter-spacing:0.5px;text-transform:uppercase;margin:0 0 8px;">Default Fallback Image</label>
                    <div class="feat-upload-zone" onclick="document.getElementById('default_image').click();">
                        <i class="bi bi-image uz-icon"></i>
                        <div class="uz-title">Click to upload default fallback image</div>
                        <div class="uz-hint">Shown automatically after promo date expires</div>
                    </div>
                    <input id="default_image" name="default_image" type="file" accept="image/*" style="display:none;">

                    <div id="default_image_preview_wrap" style="margin-top:10px;<?php echo $defaultDisplayImgPath === '' ? 'display:none;' : 'display:block;'; ?>">
                        <img id="default_image_preview" src="<?php echo htmlspecialchars($defaultDisplayImgPath, ENT_QUOTES); ?>" alt="default fallback preview" style="width:100%;max-height:200px;object-fit:contain;border-radius:10px;border:1px solid #e5e7eb;background:#f8fafc;padding:8px;">
                    </div>

                    <div class="field" style="margin:10px 0 0;">
                        <label for="default_image_alt">Default Image Alt Text</label>
                        <input id="default_image_alt" name="default_image_alt" type="text" value="<?php echo htmlspecialchars((string)$fallbackDefaultImageAlt); ?>" placeholder="Describe the default fallback image" class="feat-input">
                    </div>

                    <div class="field" style="margin:10px 0 0;">
                        <label for="default_title">Default Fallback Title</label>
                        <input id="default_title" name="default_title" type="text" value="<?php echo htmlspecialchars((string)$fallbackDefaultTitle); ?>" placeholder="Shown after promo date expires" class="feat-input">
                    </div>

                    <div class="field" style="margin:10px 0 0;">
                        <label for="default_description">Default Fallback Description</label>
                        <textarea id="default_description" name="default_description" rows="3" class="feat-input" style="resize:vertical;font-family:inherit;" placeholder="Default description shown after promo expiry"><?php echo htmlspecialchars((string)$fallbackDefaultDescription); ?></textarea>
                    </div>
                </div>
                </div>

                <div class="feat-help-card">
                    Tip: featured section now supports <strong>picture upload only</strong>. The front-end display automatically adjusts to the uploaded image dimensions.
                </div>
            </div>
            </div>

            <div class="feat-save-row">
                <button class="btn btn-primary" type="submit" style="font-size:13px;padding:10px 22px;"><i class="bi bi-check-circle"></i> Save Featured Section</button>
            </div>
        </form>
    </section>
</div>

<script>
document.getElementById('featuredForm').addEventListener('submit', function(e){
    e.preventDefault();
    var form = this;
    customConfirm('Are you sure you want to save these changes to the Featured section?').then(function(confirmed){
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

    .feat-page-header {
        background: linear-gradient(135deg, #2B11DB 0%, #4f35e8 100%);
        border-radius: 18px;
        padding: 20px 24px;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        box-shadow: 0 16px 36px rgba(43, 17, 219, 0.16);
    }

    .feat-page-kicker {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        opacity: 0.75;
        margin-bottom: 4px;
    }

    .feat-page-title {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .feat-page-subtitle {
        font-size: 12px;
        opacity: 0.78;
        max-width: 280px;
        text-align: right;
    }

    .feat-form {
        display: grid;
        grid-template-columns: minmax(0, 1.15fr) minmax(320px, 0.85fr);
        gap: 18px;
        align-items: start;
    }

    .feat-main-col,
    .feat-side-col {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .feat-panel {
        background: #fff;
        border: 1px solid #e8edf5;
        border-radius: 16px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
    }

    .feat-panel--sticky {
        position: static;
        top: auto;
    }

    .feat-section-hd {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f3f4f6;
    }

    .feat-hd-icon {
        width: 30px;
        height: 30px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .feat-hd-title {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #374151;
    }

    .feat-section-note {
        font-size: 11px;
        color: #9ca3af;
        margin-left: 4px;
    }

    .feat-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .feat-upload-zone {
        border: 1.5px dashed #d6dbe6;
        border-radius: 14px;
        padding: 18px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s, background 0.2s, transform 0.2s;
        background: linear-gradient(180deg, #fbfcff 0%, #f7f9fc 100%);
        margin-bottom: 14px;
    }

    .feat-upload-zone:hover {
        border-color: #2B11DB;
        background: rgba(43, 17, 219, 0.03);
        transform: translateY(-1px);
    }

    .feat-upload-zone .uz-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #2B11DB;
        background: rgba(43, 17, 219, 0.08);
        margin-bottom: 8px;
    }

    .feat-upload-zone .uz-title {
        font-weight: 700;
        color: #374151;
        font-size: 13px;
        margin-bottom: 4px;
    }

    .feat-upload-zone .uz-hint {
        font-size: 11px;
        color: #6b7280;
    }

    .feat-input {
        border: 1.5px solid #d9deea !important;
        border-radius: 10px !important;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s !important;
        background: #fff !important;
    }

    .feat-input:focus {
        outline: none !important;
        border-color: #2B11DB !important;
        box-shadow: 0 0 0 4px rgba(43, 17, 219, 0.09) !important;
    }

    .feat-preview-box {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: linear-gradient(180deg, #fbfcff 0%, #f4f7fb 100%);
        min-height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-bottom: 14px;
        cursor: pointer;
        transition: border-color 0.2s, transform 0.2s;
    }

    .feat-preview-box:hover {
        border-color: #2B11DB;
        transform: translateY(-1px);
    }

    #preview_container img,
    #preview_container video,
    #preview_container iframe {
        max-height: 320px;
    }

    .feat-upload-stack {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .feat-help-card {
        padding: 14px 16px;
        background: #f8faff;
        border: 1px solid #e7ecf7;
        border-radius: 14px;
        color: #475569;
        font-size: 13px;
        line-height: 1.65;
    }

    .feat-save-row {
        display: flex;
        justify-content: flex-end;
        padding-top: 16px;
        border-top: 1px solid #f3f4f6;
        margin-top: 4px;
        grid-column: 1 / -1;
    }

    @media (max-width: 1100px) {
        .feat-form {
            grid-template-columns: 1fr;
        }

        .feat-panel--sticky {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .feat-grid-2 {
            grid-template-columns: 1fr;
        }

        .feat-page-header {
            padding: 18px 16px;
        }

        .feat-page-subtitle {
            text-align: left;
            max-width: none;
        }

        .feat-panel {
            padding: 16px;
        }
    }
</style>

<script>
(function(){
    var previewContainer = document.getElementById('preview_container');

    var imageInput = document.getElementById('image');
    var defaultImageInput = document.getElementById('default_image');
    var defaultImagePreview = document.getElementById('default_image_preview');
    var defaultImagePreviewWrap = document.getElementById('default_image_preview_wrap');

    // Live preview for image upload
    if (imageInput) {
        imageInput.addEventListener('change', function(e){
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(ev){
                    previewContainer.innerHTML = '<img id="image_preview" src="' + ev.target.result + '" alt="preview" style="width:100%;max-height:320px;object-fit:contain;border-radius:12px;" data-src="' + ev.target.result + '">';
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    if (defaultImageInput) {
        defaultImageInput.addEventListener('change', function(e){
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(ev){
                    if (defaultImagePreview) {
                        defaultImagePreview.src = ev.target.result;
                    }
                    if (defaultImagePreviewWrap) {
                        defaultImagePreviewWrap.style.display = 'block';
                    }
                };
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }

    // Make preview container clickable
    if (previewContainer) {
        previewContainer.addEventListener('click', function(e){
            var target = e.target;
            var src = target.getAttribute('data-src');

            if (src && target.tagName === 'IMG') {
                openPreviewModal(src, target.tagName);
            }
        });
    }

    function openPreviewModal(src, type) {
        var modal = document.getElementById('previewModal');
        var content = document.getElementById('previewModalContent');
        
        if (type === 'IMG') {
            content.innerHTML = '<img src="' + src + '" style="max-width:95%;max-height:95%;border-radius:12px;box-shadow:0 10px 50px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">';
        }
        
        modal.style.display = 'block';
    }
})();
</script>

<?php
andison_admin_footer();



