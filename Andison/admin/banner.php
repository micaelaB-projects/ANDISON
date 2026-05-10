<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/banner_settings.php';

$settings = andison_get_banner_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'inquiry_banner_message' => (string)($_POST['inquiry_banner_message'] ?? ''),
    ];

    $saved = andison_save_banner_settings($payload);
    if ($saved) {
        andison_set_flash('success', 'Banner message updated.');
    } else {
        andison_set_flash('error', 'Failed to save banner message to Supabase. Please verify Supabase connection and table permissions.');
    }

    header('Location: banner.php');
    exit;
}

andison_admin_header('Banner Settings', 'banner');
?>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <h2><i class="bi bi-megaphone"></i> Inquiry Banner Message</h2>
        <form method="post" action="banner.php">
            <div class="field" style="margin-bottom:14px;">
                <label for="inquiry_banner_message">Banner Message</label>
                <textarea id="inquiry_banner_message" name="inquiry_banner_message" rows="3" placeholder="Enter the banner message that appears at the top of the header"><?php echo htmlspecialchars((string)($settings['inquiry_banner_message'] ?? ''), ENT_QUOTES); ?></textarea>
                <small style="display:block; margin-top:8px; color:#666;">This message will appear at the top of the header on all pages.</small>
            </div>

            <div style="display:flex; gap:12px; margin-top:20px;">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
                <a href="index.php" class="btn" style="background:#fff;border-color:rgba(43,17,219,0.20);color:#2B11DB;">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </form>
    </section>
</div>

<?php andison_admin_footer(); ?>
