<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

require_once __DIR__ . '/../includes/youtube_links.php';

$links = andison_get_youtube_links();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $home1 = (string)($_POST['home_1'] ?? '');
    $home2 = (string)($_POST['home_2'] ?? '');

    $trim1 = trim($home1);
    $trim2 = trim($home2);
    $norm1 = $trim1 === '' ? '' : andison_normalize_youtube_to_embed($trim1);
    $norm2 = $trim2 === '' ? '' : andison_normalize_youtube_to_embed($trim2);

    if ($trim1 !== '' && $norm1 === '') {
        andison_set_flash('error', 'Invalid YouTube URL/ID for Video 1. Please paste a valid YouTube link or video ID.');
        header('Location: youtube.php');
        exit;
    }
    if ($trim2 !== '' && $norm2 === '') {
        andison_set_flash('error', 'Invalid YouTube URL/ID for Video 2. Please paste a valid YouTube link or video ID.');
        header('Location: youtube.php');
        exit;
    }

    $ok = andison_save_youtube_links([
        'home_highlights' => [$norm1, $norm2],
    ]);

    if ($ok) {
        andison_set_flash('success', 'YouTube links updated.');
    } else {
        andison_set_flash('error', 'Failed to save changes. Check file permissions for /data.');
    }

    header('Location: youtube.php');
    exit;
}

andison_admin_header('YouTube Links', 'youtube');
?>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid #f0f0f0;">
            <div>
                <h2 style="font-size:24px;font-weight:700;color:#2b11db;"><i class="bi bi-youtube" style="color:#ff0000;margin-right:8px;"></i>YouTube Highlights</h2>
                <p style="font-size:13px;color:#6b7280;margin-top:4px;">Manage the featured YouTube videos displayed on your homepage</p>
            </div>
        </div>

        <form method="post" action="youtube.php" id="youtubeForm">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:28px;margin-bottom:28px;">
                <!-- Video 1 -->
                <div style="background:linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);border:1px solid #e5e7eb;border-radius:12px;padding:20px;transition:all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 24px rgba(43,17,219,0.15)';this.style.borderColor='#2b11db';" onmouseout="this.style.boxShadow='none';this.style.borderColor='#e5e7eb';">
                    <h3 style="font-size:16px;font-weight:700;color:#2b11db;margin-bottom:12px;display:flex;align-items:center;gap:8px;"><i class="bi bi-1-circle-fill" style="color:#ff0000;"></i>Featured Video 1</h3>
                    
                    <!-- Input Field -->
                    <div class="field" style="margin:0;margin-bottom:16px;">
                        <label for="home_1" style="margin-bottom:8px;"><i class="bi bi-link" style="margin-right:6px;"></i>YouTube URL or Video ID</label>
                        <input id="home_1" name="home_1" type="text" value="<?php echo htmlspecialchars((string)($links['home_highlights'][0] ?? '')); ?>" placeholder="Paste: https://www.youtube.com/watch?v=... or video ID" style="border:1px solid #e5e7eb;font-size:13px;">
                        <small style="color:#6b7280;font-size:11px;margin-top:6px;display:block;"><i class="bi bi-info-circle" style="margin-right:4px;"></i>Accepts full YouTube URLs or video IDs</small>
                    </div>
                    
                    <!-- Preview -->
                    <div style="margin-bottom:0;">
                        <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:8px;display:block;">Preview</label>
                        <?php if (!empty($links['home_highlights'][0])): 
                            $embedUrl1 = andison_normalize_youtube_to_embed($links['home_highlights'][0]);
                        ?>
                        <div style="background:#000;border-radius:10px;overflow:hidden;aspect-ratio:16/9;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                            <iframe width="100%" height="100%" src="<?php echo htmlspecialchars($embedUrl1); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="display:block;border:none;"></iframe>
                        </div>
                        <?php else: ?>
                        <div style="background:linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);border:2px dashed #e5e7eb;border-radius:10px;aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;color:#9ca3af;">
                            <div style="text-align:center;">
                                <i class="bi bi-youtube" style="font-size:48px;opacity:0.5;display:block;margin-bottom:8px;"></i>
                                <div style="font-size:13px;font-weight:500;">Paste a YouTube URL above</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Video 2 -->
                <div style="background:linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);border:1px solid #e5e7eb;border-radius:12px;padding:20px;transition:all 0.3s ease;" onmouseover="this.style.boxShadow='0 8px 24px rgba(43,17,219,0.15)';this.style.borderColor='#2b11db';" onmouseout="this.style.boxShadow='none';this.style.borderColor='#e5e7eb';">
                    <h3 style="font-size:16px;font-weight:700;color:#2b11db;margin-bottom:12px;display:flex;align-items:center;gap:8px;"><i class="bi bi-2-circle-fill" style="color:#ff0000;"></i>Featured Video 2</h3>
                    
                    <!-- Input Field -->
                    <div class="field" style="margin:0;margin-bottom:16px;">
                        <label for="home_2" style="margin-bottom:8px;"><i class="bi bi-link" style="margin-right:6px;"></i>YouTube URL or Video ID</label>
                        <input id="home_2" name="home_2" type="text" value="<?php echo htmlspecialchars((string)($links['home_highlights'][1] ?? '')); ?>" placeholder="Paste: https://youtu.be/... or video ID" style="border:1px solid #e5e7eb;font-size:13px;">
                        <small style="color:#6b7280;font-size:11px;margin-top:6px;display:block;"><i class="bi bi-info-circle" style="margin-right:4px;"></i>Accepts full YouTube URLs or video IDs</small>
                    </div>
                    
                    <!-- Preview -->
                    <div style="margin-bottom:0;">
                        <label style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:8px;display:block;">Preview</label>
                        <?php if (!empty($links['home_highlights'][1])): 
                            $embedUrl2 = andison_normalize_youtube_to_embed($links['home_highlights'][1]);
                        ?>
                        <div style="background:#000;border-radius:10px;overflow:hidden;aspect-ratio:16/9;box-shadow:0 4px 12px rgba(0,0,0,0.15);">
                            <iframe width="100%" height="100%" src="<?php echo htmlspecialchars($embedUrl2); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="display:block;border:none;"></iframe>
                        </div>
                        <?php else: ?>
                        <div style="background:linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);border:2px dashed #e5e7eb;border-radius:10px;aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;color:#9ca3af;">
                            <div style="text-align:center;">
                                <i class="bi bi-youtube" style="font-size:48px;opacity:0.5;display:block;margin-bottom:8px;"></i>
                                <div style="font-size:13px;font-weight:500;">Paste a YouTube URL above</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div style="display:flex;gap:12px;justify-content:flex-end;padding-top:20px;border-top:1px solid #f0f0f0;">
                <button class="btn btn-primary" type="submit" style="padding:12px 24px;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="bi bi-check-circle"></i> Save Video Links</button>
            </div>
        </form>
    </section>
</div>

<script>
document.getElementById('youtubeForm').addEventListener('submit', function(e){
    e.preventDefault();
    var form = this;
    customConfirm('Are you sure you want to save the YouTube link changes?').then(function(confirmed){
        if (confirmed) form.submit();
    });
});
</script>

<?php
andison_admin_footer();



