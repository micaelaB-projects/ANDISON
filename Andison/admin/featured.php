<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/home_featured.php';
require_once __DIR__ . '/../includes/youtube_links.php';

$featured = andison_get_home_featured();

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

    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    $safe = 'featured_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $abs = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $safe;

    if (!@move_uploaded_file($f['tmp_name'], $abs)) {
        return null;
    }

    return 'andison/assets/uploads/home/featured/' . $safe;
}

function andison_admin_store_featured_video(array $f, string $targetDir): ?string
{
    $ext = strtolower(pathinfo((string)($f['name'] ?? ''), PATHINFO_EXTENSION));
    $allowed = ['mp4', 'webm', 'ogg', 'mov'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    $safe = 'featured_video_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $abs = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $safe;

    if (!@move_uploaded_file($f['tmp_name'], $abs)) {
        return null;
    }

    return 'andison/assets/uploads/home/featured/' . $safe;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $badge = (string)($_POST['badge'] ?? '');
    $title = (string)($_POST['title'] ?? '');
    $description = (string)($_POST['description'] ?? '');
    $buttonText = (string)($_POST['button_text'] ?? '');
    $buttonUrl = (string)($_POST['button_url'] ?? '');
    $imageAlt = (string)($_POST['image_alt'] ?? '');
    $mediaType = (string)($_POST['media_type'] ?? 'picture');
    $youtubeUrl = (string)($_POST['youtube_url'] ?? '');
    $eventDate = (string)($_POST['event_date'] ?? '');
    $eventLocation = (string)($_POST['event_location'] ?? '');
    $discount = (string)($_POST['discount'] ?? '');
    $offerText = (string)($_POST['offer_text'] ?? '');

    $newImagePath = $featured['image'] ?? '';
    $newVideoPath = $featured['video_file'] ?? '';
    $newYoutubeEmbed = '';

    if ($mediaType === 'picture' && isset($_FILES['image']) && andison_admin_is_upload($_FILES['image'])) {
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

    if ($mediaType === 'video' && isset($_FILES['video']) && andison_admin_is_upload($_FILES['video'])) {
        $stored = andison_admin_store_featured_video(
            $_FILES['video'],
            dirname(__DIR__) . '/assets/uploads/home/featured'
        );
        if ($stored === null) {
            andison_set_flash('error', 'Invalid video upload. Please use MP4/WebM/OGG/MOV.');
            header('Location: featured.php');
            exit;
        }
        $newVideoPath = $stored;
    }

    if ($mediaType === 'youtube' && trim($youtubeUrl) !== '') {
        $normalized = andison_normalize_youtube_to_embed(trim($youtubeUrl));
        if ($normalized === '') {
            andison_set_flash('error', 'Invalid YouTube URL/ID.');
            header('Location: featured.php');
            exit;
        }
        $newYoutubeEmbed = $normalized;
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
        'media_type' => $mediaType,
        'image' => $newImagePath,
        'image_alt' => $imageAlt,
        'youtube_url' => $newYoutubeEmbed,
        'video_file' => $newVideoPath,
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

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid #f0f0f0;">
            <div>
                <h2 style="font-size:24px;font-weight:700;color:#2b11db;"><i class="bi bi-star-fill" style="color:#f59e0b;margin-right:8px;"></i>Featured Section</h2>
                <p style="font-size:13px;color:#6b7280;margin-top:4px;">Manage the featured content displayed on your homepage</p>
            </div>
        </div>

        <form method="post" action="featured.php" enctype="multipart/form-data" id="featuredForm">
            <!-- Content Information Section -->
            <div style="margin-bottom:28px;">
                <h3 style="font-size:14px;font-weight:700;color:#2b11db;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid rgba(43,17,219,0.1);"><i class="bi bi-info-circle" style="margin-right:8px;"></i>Content Information</h3>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div class="field" style="margin:0;">
                        <label for="badge"><i class="bi bi-tag" style="margin-right:6px;"></i>Badge (e.g., EVENTS, NEW, FEATURED)</label>
                        <input id="badge" name="badge" type="text" value="<?php echo htmlspecialchars((string)($featured['badge'] ?? '')); ?>" placeholder="e.g., EVENTS" style="border:1px solid #e5e7eb;">
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="media_type"><i class="bi bi-image" style="margin-right:6px;"></i>Media Type</label>
                        <select id="media_type" name="media_type" style="border:1px solid #e5e7eb;background:linear-gradient(to right, #f9fafb, #f3f4f6);">
                            <option value="picture" <?php echo ($featured['media_type'] ?? 'picture') === 'picture' ? 'selected' : ''; ?>>🖼️ Picture</option>
                            <option value="youtube" <?php echo ($featured['media_type'] ?? '') === 'youtube' ? 'selected' : ''; ?>>🎥 YouTube Video</option>
                            <option value="video" <?php echo ($featured['media_type'] ?? '') === 'video' ? 'selected' : ''; ?>>📹 Video File</option>
                        </select>
                    </div>
                </div>

                <div class="field" style="margin:0;margin-bottom:16px;">
                    <label for="title"><i class="bi bi-pencil-square" style="margin-right:6px;"></i>Title *</label>
                    <input id="title" name="title" type="text" value="<?php echo htmlspecialchars((string)($featured['title'] ?? '')); ?>" placeholder="Enter featured section title" required style="border:1px solid #e5e7eb;font-weight:500;">
                </div>

                <div class="field" style="margin:0;">
                    <label for="description"><i class="bi bi-file-text" style="margin-right:6px;"></i>Description</label>
                    <textarea id="description" name="description" placeholder="Describe the featured content, benefits, and key features..." rows="4" style="font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;border:1px solid #e5e7eb;resize:vertical;"><?php echo htmlspecialchars((string)($featured['description'] ?? '')); ?></textarea>
                    <small style="color:#6b7280;font-size:12px;margin-top:6px;display:block;">Make it engaging and compelling for your visitors</small>
                </div>
            </div>

            <!-- Event Information Section -->
            <div style="margin-bottom:28px;">
                <h3 style="font-size:14px;font-weight:700;color:#2b11db;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid rgba(43,17,219,0.1);"><i class="bi bi-calendar-event" style="margin-right:8px;"></i>Event & Sales Information</h3>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                    <div class="field" style="margin:0;">
                        <label for="event_date"><i class="bi bi-calendar" style="margin-right:6px;"></i>Event Date</label>
                        <input id="event_date" name="event_date" type="text" value="<?php echo htmlspecialchars((string)($featured['event_date'] ?? '')); ?>" placeholder="e.g., February 10, 2026" style="border:1px solid #e5e7eb;">
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="event_location"><i class="bi bi-geo-alt" style="margin-right:6px;"></i>Event Location</label>
                        <input id="event_location" name="event_location" type="text" value="<?php echo htmlspecialchars((string)($featured['event_location'] ?? '')); ?>" placeholder="e.g., Industrial Expo Center" style="border:1px solid #e5e7eb;">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="field" style="margin:0;">
                        <label for="discount"><i class="bi bi-percent" style="margin-right:6px;"></i>Discount / Offer</label>
                        <input id="discount" name="discount" type="text" value="<?php echo htmlspecialchars((string)($featured['discount'] ?? '')); ?>" placeholder="e.g., 20% OFF" style="border:1px solid #e5e7eb;">
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="offer_text"><i class="bi bi-bookmark" style="margin-right:6px;"></i>Offer Text</label>
                        <input id="offer_text" name="offer_text" type="text" value="<?php echo htmlspecialchars((string)($featured['offer_text'] ?? '')); ?>" placeholder="e.g., Limited Time Offer" style="border:1px solid #e5e7eb;">
                    </div>
                </div>
            </div>

            <!-- Call-to-Action Section -->
            <div style="margin-bottom:28px;">
                <h3 style="font-size:14px;font-weight:700;color:#2b11db;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid rgba(43,17,219,0.1);"><i class="bi bi-hand-index-fill" style="margin-right:8px;"></i>Call-to-Action Button</h3>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="field" style="margin:0;">
                        <label for="button_text"><i class="bi bi-type" style="margin-right:6px;"></i>Button Text</label>
                        <input id="button_text" name="button_text" type="text" value="<?php echo htmlspecialchars((string)($featured['button_text'] ?? '')); ?>" placeholder="e.g., Learn More, Shop Now" style="border:1px solid #e5e7eb;">
                    </div>

                    <div class="field" style="margin:0;">
                        <label for="button_url"><i class="bi bi-link-45deg" style="margin-right:6px;"></i>Button URL</label>
                        <input id="button_url" name="button_url" type="text" value="<?php echo htmlspecialchars((string)($featured['button_url'] ?? '')); ?>" placeholder="e.g., #products or https://..." style="border:1px solid #e5e7eb;">
                    </div>
                </div>
            </div>

            <!-- Media Section -->
            <div style="margin-bottom:28px;">
                <h3 style="font-size:14px;font-weight:700;color:#2b11db;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid rgba(43,17,219,0.1);"><i class="bi bi-camera" style="margin-right:8px;"></i>Media Content</h3>
                
                <!-- Preview -->
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;margin-bottom:8px;display:block;">Preview</label>
                    <div id="preview_container" style="border:2px solid #e5e7eb;border-radius:12px;padding:20px;background:linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);min-height:280px;cursor:pointer;transition:all 0.3s ease;display:flex;align-items:center;justify-content:center;" title="Click to view full size">
                        <?php
                        $mType = $featured['media_type'] ?? 'picture';
                        if ($mType === 'picture'):
                            $imgPath = (string)($featured['image'] ?? '');
                            $displayImgPath = $imgPath;
                            // Convert andison/assets/... to ../assets/... for admin display
                            if (strpos($imgPath, 'andison/') === 0) {
                                $displayImgPath = '../' . substr($imgPath, 8);
                            } elseif ($imgPath !== '' && !preg_match('~^(https?://|\\.\\./|/)~i', $imgPath)) {
                                $displayImgPath = '../' . $imgPath;
                            }
                            $img = htmlspecialchars($displayImgPath, ENT_QUOTES);
                        ?>
                            <img id="image_preview" src="<?php echo $img; ?>" alt="preview" style="width:100%;max-height:280px;object-fit:contain;border-radius:8px;box-shadow:0 4px 12px rgba(43,17,219,0.1);" data-src="<?php echo $img; ?>">
                        <?php elseif ($mType === 'youtube'): 
                            $ytUrl = htmlspecialchars((string)($featured['youtube_url'] ?? ''), ENT_QUOTES);
                        ?>
                            <iframe id="youtube_preview" src="<?php echo $ytUrl; ?>" style="width:100%;height:280px;border:0;border-radius:8px;box-shadow:0 4px 12px rgba(43,17,219,0.1);" allowfullscreen data-src="<?php echo $ytUrl; ?>"></iframe>
                        <?php elseif ($mType === 'video'):
                            $vidPath = (string)($featured['video_file'] ?? '');
                            $displayVidPath = $vidPath;
                            // Convert andison/assets/... to ../assets/... for admin display
                            if (strpos($vidPath, 'andison/') === 0) {
                                $displayVidPath = '../' . substr($vidPath, 8);
                            } elseif ($vidPath !== '' && !preg_match('~^(https?://|\\.\\./|/)~i', $vidPath)) {
                                $displayVidPath = '../' . $vidPath;
                            }
                        ?>
                            <video id="video_preview" controls style="width:100%;max-height:280px;border-radius:8px;box-shadow:0 4px 12px rgba(43,17,219,0.1);" data-src="<?php echo htmlspecialchars($displayVidPath, ENT_QUOTES); ?>">
                                <source src="<?php echo htmlspecialchars($displayVidPath); ?>" type="video/mp4">
                            </video>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upload Fields -->
                <div id="field_picture" class="field" style="margin:0;display:<?php echo ($mType === 'picture') ? 'block' : 'none'; ?>;">
                    <label for="image" style="margin-bottom:12px;"><i class="bi bi-upload" style="margin-right:6px;"></i>Upload Picture</label>
                    <div style="border:2px dashed #e5e7eb;border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:all 0.3s ease;background:#f9fafb;hover-effect:" onmouseover="this.style.borderColor='#2b11db';this.style.backgroundColor='rgba(43,17,219,0.02)';" onmouseout="this.style.borderColor='#e5e7eb';this.style.backgroundColor='#f9fafb';" onclick="document.getElementById('image').click();">
                        <i class="bi bi-cloud-upload" style="font-size:32px;color:#2b11db;display:block;margin-bottom:8px;"></i>
                        <div style="font-weight:600;color:#374151;margin-bottom:4px;">Click to upload or drag & drop</div>
                        <div style="font-size:12px;color:#9ca3af;">JPG, PNG, WebP, GIF or AVIF (max 5MB)</div>
                    </div>
                    <input id="image" name="image" type="file" accept="image/*" style="display:none;">
                    <div class="field" style="margin:0;margin-top:12px;">
                        <label for="image_alt"><i class="bi bi-chat" style="margin-right:6px;"></i>Image Alt Text</label>
                        <input id="image_alt" name="image_alt" type="text" value="<?php echo htmlspecialchars((string)($featured['image_alt'] ?? '')); ?>" placeholder="Describe the image for accessibility" style="border:1px solid #e5e7eb;">
                        <small style="color:#6b7280;font-size:12px;margin-top:6px;display:block;">Used for accessibility and SEO</small>
                    </div>
                </div>

                <div id="field_youtube" class="field" style="margin:0;display:<?php echo ($mType === 'youtube') ? 'block' : 'none'; ?>;">
                    <label for="youtube_url" style="margin-bottom:8px;"><i class="bi bi-youtube" style="margin-right:6px;"></i>YouTube URL or Video ID</label>
                    <input id="youtube_url" name="youtube_url" type="text" value="<?php echo htmlspecialchars((string)($featured['youtube_url'] ?? '')); ?>" placeholder="Paste: https://www.youtube.com/watch?v=... or video ID" style="border:1px solid #e5e7eb;">
                    <small style="color:#6b7280;font-size:12px;margin-top:6px;display:block;">Supports full URLs or YouTube video IDs</small>
                </div>

                <div id="field_video" class="field" style="margin:0;display:<?php echo ($mType === 'video') ? 'block' : 'none'; ?>;">
                    <label for="video" style="margin-bottom:12px;"><i class="bi bi-upload" style="margin-right:6px;"></i>Upload Video</label>
                    <div style="border:2px dashed #e5e7eb;border-radius:10px;padding:24px;text-align:center;cursor:pointer;transition:all 0.3s ease;background:#f9fafb;" onmouseover="this.style.borderColor='#2b11db';this.style.backgroundColor='rgba(43,17,219,0.02)';" onmouseout="this.style.borderColor='#e5e7eb';this.style.backgroundColor='#f9fafb';" onclick="document.getElementById('video').click();">
                        <i class="bi bi-cloud-upload" style="font-size:32px;color:#2b11db;display:block;margin-bottom:8px;"></i>
                        <div style="font-weight:600;color:#374151;margin-bottom:4px;">Click to upload or drag & drop</div>
                        <div style="font-size:12px;color:#9ca3af;">MP4, WebM, OGG or MOV (max 50MB)</div>
                    </div>
                    <input id="video" name="video" type="file" accept="video/*" style="display:none;">
                </div>
            </div>

            <!-- Save Button -->
            <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:20px;border-top:1px solid #f0f0f0;">
                <button class="btn btn-primary" type="submit" style="padding:12px 24px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-check-circle"></i> Save Featured Section</button>
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

#preview_container:hover {
    border-color: #2b11db;
    background: linear-gradient(135deg, rgba(43,17,219,0.02) 0%, rgba(43,17,219,0.01) 100%);
}
</style>

<script>
(function(){
    var mediaTypeSelect = document.getElementById('media_type');
    var fieldPicture = document.getElementById('field_picture');
    var fieldYoutube = document.getElementById('field_youtube');
    var fieldVideo = document.getElementById('field_video');
    var previewContainer = document.getElementById('preview_container');

    var imageInput = document.getElementById('image');
    var videoInput = document.getElementById('video');
    var youtubeInput = document.getElementById('youtube_url');

    function updateMediaFields() {
        var type = mediaTypeSelect.value;
        fieldPicture.style.display = type === 'picture' ? 'flex' : 'none';
        fieldYoutube.style.display = type === 'youtube' ? 'flex' : 'none';
        fieldVideo.style.display = type === 'video' ? 'flex' : 'none';
    }

    mediaTypeSelect.addEventListener('change', updateMediaFields);

    // Live preview for image upload
    imageInput.addEventListener('change', function(e){
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(ev){
                previewContainer.innerHTML = '<img id="image_preview" src="' + ev.target.result + '" alt="preview" style="width:100%;max-height:240px;object-fit:cover;border-radius:12px;" data-src="' + ev.target.result + '">';
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    // Live preview for video upload
    videoInput.addEventListener('change', function(e){
        if (e.target.files && e.target.files[0]) {
            var url = URL.createObjectURL(e.target.files[0]);
            previewContainer.innerHTML = '<video id="video_preview" controls style="width:100%;max-height:240px;border-radius:12px;" data-src="' + url + '"><source src="' + url + '" type="video/mp4"></video>';
        }
    });

    // Live preview for YouTube URL
    youtubeInput.addEventListener('input', function(e){
        var val = e.target.value.trim();
        if (val === '') return;
        
        // Simple YouTube ID extraction
        var videoId = '';
        if (val.match(/^[A-Za-z0-9_-]{6,}$/)) {
            videoId = val;
        } else if (val.indexOf('youtu.be/') > -1) {
            videoId = val.split('youtu.be/')[1].split(/[?&]/)[0];
        } else if (val.indexOf('youtube.com/watch') > -1) {
            var match = val.match(/[?&]v=([^&]+)/);
            if (match) videoId = match[1];
        } else if (val.indexOf('youtube.com/embed/') > -1) {
            videoId = val.split('youtube.com/embed/')[1].split(/[?&]/)[0];
        } else if (val.indexOf('youtube.com/shorts/') > -1) {
            videoId = val.split('youtube.com/shorts/')[1].split(/[?&]/)[0];
        }

        if (videoId) {
            var embedUrl = 'https://www.youtube.com/embed/' + videoId;
            previewContainer.innerHTML = '<iframe src="' + embedUrl + '" style="width:100%;height:240px;border:0;border-radius:12px;" allowfullscreen data-src="' + embedUrl + '"></iframe>';
        }
    });

    // Make preview container clickable
    previewContainer.addEventListener('click', function(e){
        var target = e.target;
        var src = target.getAttribute('data-src');
        
        if (!src && target.tagName === 'VIDEO') {
            src = target.querySelector('source') ? target.querySelector('source').src : null;
        }
        
        if (src && target.tagName !== 'VIDEO') {
            openPreviewModal(src, target.tagName);
        }
    });

    function openPreviewModal(src, type) {
        var modal = document.getElementById('previewModal');
        var content = document.getElementById('previewModalContent');
        
        if (type === 'IMG') {
            content.innerHTML = '<img src="' + src + '" style="max-width:95%;max-height:95%;border-radius:12px;box-shadow:0 10px 50px rgba(0,0,0,0.5);" onclick="event.stopPropagation()">';
        } else if (type === 'IFRAME') {
            content.innerHTML = '<iframe src="' + src + '" style="width:95%;max-width:1600px;height:90%;border:0;border-radius:12px;box-shadow:0 10px 50px rgba(0,0,0,0.5);" allowfullscreen onclick="event.stopPropagation()"></iframe>';
        }
        
        modal.style.display = 'block';
    }
})();
</script>

<?php
andison_admin_footer();
