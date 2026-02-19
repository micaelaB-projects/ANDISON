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

<style>
.yt-page-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);border-radius:14px;padding:18px 22px;color:white;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
.yt-card { background:#fff;border:1.5px solid #e5e7eb;border-radius:12px;padding:16px;transition:border-color 0.2s,box-shadow 0.2s; }
.yt-card:hover { border-color:#2B11DB;box-shadow:0 4px 18px rgba(43,17,219,0.1); }
.yt-input { border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;padding:9px 12px;width:100%;box-sizing:border-box;transition:border-color 0.2s; }
.yt-input:focus { outline:none;border-color:#2B11DB;box-shadow:0 0 0 3px rgba(43,17,219,0.08); }
</style>

<div class="grid">
    <div style="grid-column:span 12;" class="yt-page-header">
        <div>
            <div style="font-size:11px;font-weight:700;opacity:0.7;letter-spacing:0.6px;text-transform:uppercase;margin-bottom:4px;">Homepage Management</div>
            <div style="font-size:20px;font-weight:800;letter-spacing:-0.2px;display:flex;align-items:center;gap:8px;"><i class="bi bi-youtube" style="color:#ff6b6b;"></i> YouTube Highlights</div>
        </div>
        <span style="font-size:12px;opacity:0.75;">2 featured videos displayed on the homepage</span>
    </div>

    <section class="card" style="grid-column:span 12;">

        <form method="post" action="youtube.php" id="youtubeForm">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:18px;">
                <!-- Video 1 -->
                <div class="yt-card">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                        <span style="width:26px;height:26px;border-radius:7px;background:rgba(255,0,0,0.08);color:#ff0000;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;">1</span>
                        <span style="font-size:13px;font-weight:700;color:#111827;">Featured Video 1</span>
                    </div>
                    <div style="margin-bottom:10px;">
                        <label for="home_1" style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;display:block;margin-bottom:5px;">YouTube URL or Video ID</label>
                        <input id="home_1" name="home_1" type="text" class="yt-input" value="<?php echo htmlspecialchars((string)($links['home_highlights'][0] ?? '')); ?>" placeholder="https://www.youtube.com/watch?v=... or video ID">
                        <div style="font-size:10px;color:#9ca3af;margin-top:4px;">Accepts full YouTube URLs or video IDs</div>
                    </div>
                    <?php if (!empty($links['home_highlights'][0])): 
                        $embedUrl1 = andison_normalize_youtube_to_embed($links['home_highlights'][0]);
                    ?>
                    <div style="background:#000;border-radius:8px;overflow:hidden;aspect-ratio:16/9;">
                        <iframe width="100%" height="100%" src="<?php echo htmlspecialchars($embedUrl1); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="display:block;border:none;"></iframe>
                    </div>
                    <?php else: ?>
                    <div style="border:2px dashed #e5e7eb;border-radius:8px;aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;background:#f9fafb;">
                        <div style="text-align:center;color:#9ca3af;">
                            <i class="bi bi-youtube" style="font-size:36px;display:block;margin-bottom:6px;opacity:0.4;"></i>
                            <div style="font-size:12px;font-weight:500;">Paste a YouTube URL above</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Video 2 -->
                <div class="yt-card">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                        <span style="width:26px;height:26px;border-radius:7px;background:rgba(255,0,0,0.08);color:#ff0000;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;flex-shrink:0;">2</span>
                        <span style="font-size:13px;font-weight:700;color:#111827;">Featured Video 2</span>
                    </div>
                    <div style="margin-bottom:10px;">
                        <label for="home_2" style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;display:block;margin-bottom:5px;">YouTube URL or Video ID</label>
                        <input id="home_2" name="home_2" type="text" class="yt-input" value="<?php echo htmlspecialchars((string)($links['home_highlights'][1] ?? '')); ?>" placeholder="https://youtu.be/... or video ID">
                        <div style="font-size:10px;color:#9ca3af;margin-top:4px;">Accepts full YouTube URLs or video IDs</div>
                    </div>
                    <?php if (!empty($links['home_highlights'][1])): 
                        $embedUrl2 = andison_normalize_youtube_to_embed($links['home_highlights'][1]);
                    ?>
                    <div style="background:#000;border-radius:8px;overflow:hidden;aspect-ratio:16/9;">
                        <iframe width="100%" height="100%" src="<?php echo htmlspecialchars($embedUrl2); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="display:block;border:none;"></iframe>
                    </div>
                    <?php else: ?>
                    <div style="border:2px dashed #e5e7eb;border-radius:8px;aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;background:#f9fafb;">
                        <div style="text-align:center;color:#9ca3af;">
                            <i class="bi bi-youtube" style="font-size:36px;display:block;margin-bottom:6px;opacity:0.4;"></i>
                            <div style="font-size:12px;font-weight:500;">Paste a YouTube URL above</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;padding-top:16px;border-top:1px solid #f3f4f6;">
                <button class="btn btn-primary" type="submit" style="font-size:13px;padding:10px 22px;display:flex;align-items:center;gap:6px;"><i class="bi bi-check-circle"></i> Save Video Links</button>
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



