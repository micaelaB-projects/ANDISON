<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/aboutus_settings.php';

$settings = andison_get_aboutus_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buildingImageUrl = $settings['building_image_url'] ?? '';
    $missionImageUrl = $settings['mission_image_url'] ?? '';
    $visionImageUrl = $settings['vision_image_url'] ?? '';

    // Handle building image upload
    if (isset($_FILES['building_image']) && $_FILES['building_image']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/../includes/supabase.php';
        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-]/', '_', $_FILES['building_image']['name']);
        $url = andison_sb_storage_upload_tmp($_FILES['building_image'], 'home-images', 'aboutus/' . $safeName);
        if ($url) {
            $buildingImageUrl = $url;
        }
    }

    // Handle mission image upload
    if (isset($_FILES['mission_image']) && $_FILES['mission_image']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/../includes/supabase.php';
        $safeName = time() . '_mission_' . preg_replace('/[^a-zA-Z0-9.\-]/', '_', $_FILES['mission_image']['name']);
        $url = andison_sb_storage_upload_tmp($_FILES['mission_image'], 'home-images', 'aboutus/' . $safeName);
        if ($url) {
            $missionImageUrl = $url;
        }
    }

    // Handle vision image upload
    if (isset($_FILES['vision_image']) && $_FILES['vision_image']['error'] === UPLOAD_ERR_OK) {
        require_once __DIR__ . '/../includes/supabase.php';
        $safeName = time() . '_vision_' . preg_replace('/[^a-zA-Z0-9.\-]/', '_', $_FILES['vision_image']['name']);
        $url = andison_sb_storage_upload_tmp($_FILES['vision_image'], 'home-images', 'aboutus/' . $safeName);
        if ($url) {
            $visionImageUrl = $url;
        }
    }

    $payload = [
        'company_text' => (string)($_POST['company_text'] ?? ''),
        'mission_text' => (string)($_POST['mission_text'] ?? ''),
        'vision_text' => (string)($_POST['vision_text'] ?? ''),
        'building_image_url' => $buildingImageUrl,
        'mission_image_url' => $missionImageUrl,
        'vision_image_url' => $visionImageUrl,
    ];

    $saved = andison_save_aboutus_settings($payload);
    if ($saved) {
        andison_set_flash('success', 'About Us page content updated.');
    } else {
        andison_set_flash('error', 'Failed to save About Us content to Supabase. Please verify Supabase connection and table permissions.');
    }

    header('Location: aboutus.php');
    exit;
}

andison_admin_header('About Us Settings', 'aboutus');
?>

<div class="grid">
    <section class="card" style="grid-column:span 12;">
        <h2><i class="bi bi-info-circle"></i> About Us Page Content</h2>
        <form method="post" action="aboutus.php" enctype="multipart/form-data">
            <div class="field" style="margin-bottom:14px;">
                <label for="company_text">Company Description</label>
                <textarea id="company_text" name="company_text" rows="8" placeholder="Enter company description text" style="font-family:monospace;font-size:12px;"><?php echo htmlspecialchars((string)($settings['company_text'] ?? ''), ENT_QUOTES); ?></textarea>
                <small style="display:block; margin-top:8px; color:#666;">This is the main company description on the About Us page. You can use HTML tags like &lt;strong&gt;, &lt;a&gt;, &lt;span&gt;.</small>
            </div>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
            <h3 style="margin: 15px 0; font-size: 16px; font-weight: 600;">Images</h3>

            <div class="field" style="margin-bottom:14px;">
                <label for="building_image"><i class="bi bi-image"></i> Building/Hero Image</label>
                <?php if (!empty($settings['building_image_url'])): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="<?php echo htmlspecialchars($settings['building_image_url'], ENT_QUOTES); ?>" alt="Building Image" style="max-height: 100px; display: block; border-radius: 4px; border: 1px solid #ccc; padding: 4px;">
                    </div>
                <?php endif; ?>
                <input type="file" id="building_image" name="building_image" accept="image/*" class="file-input">
                <small style="color: #666; display: block; margin-top: 4px;">Upload a new building/hero image (Andison office photo)</small>
            </div>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
            <h3 style="margin: 15px 0; font-size: 16px; font-weight: 600;">Mission, Vision & Values</h3>

            <div class="two-col" style="margin-bottom:14px;">
                <div class="field">
                    <label for="mission_text">Mission Statement</label>
                    <textarea id="mission_text" name="mission_text" rows="4" placeholder="Enter mission text"><?php echo htmlspecialchars((string)($settings['mission_text'] ?? ''), ENT_QUOTES); ?></textarea>
                </div>
                <div class="field">
                    <label for="vision_text">Vision Statement</label>
                    <textarea id="vision_text" name="vision_text" rows="4" placeholder="Enter vision text"><?php echo htmlspecialchars((string)($settings['vision_text'] ?? ''), ENT_QUOTES); ?></textarea>
                </div>
            </div>

            <div class="two-col" style="margin-bottom:14px;">
                <div class="field">
                    <label for="mission_image"><i class="bi bi-image"></i> Mission Image</label>
                    <?php if (!empty($settings['mission_image_url'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?php echo htmlspecialchars($settings['mission_image_url'], ENT_QUOTES); ?>" alt="Mission Image" style="max-height: 80px; display: block; border-radius: 4px; border: 1px solid #ccc; padding: 4px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="mission_image" name="mission_image" accept="image/*" class="file-input">
                    <small style="color: #666; display: block; margin-top: 4px;">Welding Machines image for Mission card</small>
                </div>
                <div class="field">
                    <label for="vision_image"><i class="bi bi-image"></i> Vision Image</label>
                    <?php if (!empty($settings['vision_image_url'])): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?php echo htmlspecialchars($settings['vision_image_url'], ENT_QUOTES); ?>" alt="Vision Image" style="max-height: 80px; display: block; border-radius: 4px; border: 1px solid #ccc; padding: 4px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="vision_image" name="vision_image" accept="image/*" class="file-input">
                    <small style="color: #666; display: block; margin-top: 4px;">Welding Robots image for Vision card</small>
                </div>
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
