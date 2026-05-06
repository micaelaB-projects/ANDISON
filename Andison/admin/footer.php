<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/footer_settings.php';

$settings = andison_get_footer_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $socialImageUrl = $settings['social_image_url'] ?? '';
    if (isset($_FILES['social_image']) && $_FILES['social_image']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/../includes/supabase.php';
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-]/', '_', $_FILES['social_image']['name']);
        $url = andison_sb_storage_upload_tmp($_FILES['social_image'], 'home-images', 'footer/' . $safeName);
        if ($url) {
            $socialImageUrl = $url;
        }
    } elseif (isset($_POST['remove_social_image']) && $_POST['remove_social_image'] === '1') {
        $socialImageUrl = '';
    }

    $payload = [
        'brand_blurb' => (string)($_POST['brand_blurb'] ?? ''),
        'manila_title' => (string)($_POST['manila_title'] ?? ''),
        'manila_address' => (string)($_POST['manila_address'] ?? ''),
        'manila_phone_1' => (string)($_POST['manila_phone_1'] ?? ''),
        'manila_phone_2' => (string)($_POST['manila_phone_2'] ?? ''),
        'calabarzon_title' => (string)($_POST['calabarzon_title'] ?? ''),
        'calabarzon_address' => (string)($_POST['calabarzon_address'] ?? ''),
        'calabarzon_phone' => (string)($_POST['calabarzon_phone'] ?? ''),
        'contact_email' => (string)($_POST['contact_email'] ?? ''),
        'facebook_url' => (string)($_POST['facebook_url'] ?? ''),
        'linkedin_url' => (string)($_POST['linkedin_url'] ?? ''),
        'navigation_title' => (string)($_POST['navigation_title'] ?? ''),
        'copyright' => (string)($_POST['copyright'] ?? ''),
        'social_image_url' => $socialImageUrl,
        'qr_code_url' => (string)($_POST['qr_code_url'] ?? ''),
    ];

    $saved = andison_save_footer_settings($payload);
    if ($saved) {
        andison_set_flash('success', 'Footer details updated.');
    } else {
        andison_set_flash('error', 'Failed to save footer details to Supabase. Please verify Supabase connection and table permissions.');
    }

    header('Location: footer.php');
    exit;
}

andison_admin_header('Footer Details', 'footer');
?>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <h2><i class="bi bi-layout-text-window-reverse"></i> Footer Content</h2>
        <form method="post" action="footer.php" enctype="multipart/form-data">
            <div class="field" style="margin-bottom:14px;">
                <label for="brand_blurb">Company Description</label>
                <textarea id="brand_blurb" name="brand_blurb" rows="4" placeholder="Short company description for the footer"><?php echo htmlspecialchars((string)($settings['brand_blurb'] ?? ''), ENT_QUOTES); ?></textarea>
            </div>

            <div class="two-col" style="margin-bottom:14px;">
                <div class="field">
                    <label for="manila_title">Manila Section Title</label>
                    <input id="manila_title" name="manila_title" type="text" value="<?php echo htmlspecialchars((string)($settings['manila_title'] ?? ''), ENT_QUOTES); ?>">
                </div>
                <div class="field">
                    <label for="calabarzon_title">Calabarzon Section Title</label>
                    <input id="calabarzon_title" name="calabarzon_title" type="text" value="<?php echo htmlspecialchars((string)($settings['calabarzon_title'] ?? ''), ENT_QUOTES); ?>">
                </div>
            </div>

            <div class="field" style="margin-bottom:14px;">
                <label for="manila_address">Manila Address</label>
                <textarea id="manila_address" name="manila_address" rows="3"><?php echo htmlspecialchars((string)($settings['manila_address'] ?? ''), ENT_QUOTES); ?></textarea>
            </div>

            <div class="two-col" style="margin-bottom:14px;">
                <div class="field">
                    <label for="manila_phone_1">Manila Phone 1</label>
                    <input id="manila_phone_1" name="manila_phone_1" type="text" value="<?php echo htmlspecialchars((string)($settings['manila_phone_1'] ?? ''), ENT_QUOTES); ?>">
                </div>
                <div class="field">
                    <label for="manila_phone_2">Manila Phone 2</label>
                    <input id="manila_phone_2" name="manila_phone_2" type="text" value="<?php echo htmlspecialchars((string)($settings['manila_phone_2'] ?? ''), ENT_QUOTES); ?>">
                </div>
            </div>

            <div class="field" style="margin-bottom:14px;">
                <label for="calabarzon_address">Calabarzon Address</label>
                <textarea id="calabarzon_address" name="calabarzon_address" rows="3"><?php echo htmlspecialchars((string)($settings['calabarzon_address'] ?? ''), ENT_QUOTES); ?></textarea>
            </div>

            <div class="field" style="margin-bottom:14px;">
                <label for="calabarzon_phone">Calabarzon Phone</label>
                <input id="calabarzon_phone" name="calabarzon_phone" type="text" value="<?php echo htmlspecialchars((string)($settings['calabarzon_phone'] ?? ''), ENT_QUOTES); ?>">
            </div>

            <div class="field" style="margin-bottom:14px;">
                <label for="contact_email">Contact Email</label>
                <input id="contact_email" name="contact_email" type="text" value="<?php echo htmlspecialchars((string)($settings['contact_email'] ?? ''), ENT_QUOTES); ?>" placeholder="e.g., info@andison-industrial.com">
            </div>

            <div class="two-col" style="margin-bottom:14px;">
                <div class="field">
                    <label for="facebook_url">Facebook Link</label>
                    <input id="facebook_url" name="facebook_url" type="url" value="<?php echo htmlspecialchars((string)($settings['facebook_url'] ?? ''), ENT_QUOTES); ?>" placeholder="https://facebook.com/your-page">
                </div>
                <div class="field">
                    <label for="linkedin_url">LinkedIn Link</label>
                    <input id="linkedin_url" name="linkedin_url" type="url" value="<?php echo htmlspecialchars((string)($settings['linkedin_url'] ?? ''), ENT_QUOTES); ?>" placeholder="https://linkedin.com/company/your-company">
                </div>
            </div>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
            <h3 style="margin: 15px 0; font-size: 16px; font-weight: 600;">Social Links Section</h3>

            <div class="field" style="margin-bottom:14px;">
                <label for="social_image"><i class="bi bi-image"></i> Image Below Socials</label>
                <?php if (!empty($settings['social_image_url'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo htmlspecialchars($settings['social_image_url'], ENT_QUOTES); ?>" alt="Social Image" style="max-height: 100px; display: block; border-radius: 4px; border: 1px solid #ccc; padding: 4px;">
                        <label style="display:inline-block; margin-top:5px; font-weight:normal; font-size: 13px;">
                            <input type="checkbox" name="remove_social_image" value="1"> Remove this image
                        </label>
                    </div>
                <?php endif; ?>
                <input type="file" id="social_image" name="social_image" accept="image/*" class="file-input">
                <small style="color: #666; display: block; margin-top: 4px;">Upload an image (e.g. ISO certification, partner logo) to appear below the social links.</small>
            </div>

            <div class="field" style="margin-bottom:14px;">
                <label for="qr_code_url"><i class="bi bi-qr-code"></i> QR Code Image</label>
                <input id="qr_code_url" name="qr_code_url" type="url" value="<?php echo htmlspecialchars((string)($settings['qr_code_url'] ?? ''), ENT_QUOTES); ?>" placeholder="https://example.com/qr-code.png">
                <small style="color: #666; display: block; margin-top: 4px;">URL to QR code image. Will display below social links with "Browse our full product catalog" / "SCAN HERE" text.</small>
                <?php if (!empty($settings['qr_code_url'])): ?>
                    <div style="margin-top: 10px;">
                        <strong style="font-size: 12px;">Preview:</strong>
                        <img src="<?php echo htmlspecialchars($settings['qr_code_url'], ENT_QUOTES); ?>" alt="QR Code Preview" style="max-width: 120px; max-height: 120px; display: block; border-radius: 8px; border: 2px solid #0ea5e9; padding: 4px; margin-top: 8px;">
                    </div>
                <?php endif; ?>
            </div>

            <div class="two-col" style="margin-bottom:14px;">
                <div class="field">
                    <label for="navigation_title">Navigation Section Title</label>
                    <input id="navigation_title" name="navigation_title" type="text" value="<?php echo htmlspecialchars((string)($settings['navigation_title'] ?? ''), ENT_QUOTES); ?>">
                </div>
                <div class="field">
                    <label for="copyright">Copyright Text</label>
                    <input id="copyright" name="copyright" type="text" value="<?php echo htmlspecialchars((string)($settings['copyright'] ?? ''), ENT_QUOTES); ?>">
                </div>
            </div>

            <div class="row" style="justify-content:flex-end;">
                <button class="btn btn-primary" type="submit"><i class="bi bi-check-circle"></i> Save Footer Details</button>
            </div>
        </form>
    </section>
</div>

<?php andison_admin_footer();
