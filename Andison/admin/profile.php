<?php
declare(strict_types=1);
require_once __DIR__ . '/_layout.php';

// Handle POST actions: update profile or change password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cfg = andison_admin_config();

    // Load existing values
    $username = (string)($cfg['username'] ?? 'andisonindustrial');
    $passwordHash = (string)($cfg['password_hash'] ?? '');

    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $first = isset($_POST['first_name']) ? trim((string)$_POST['first_name']) : '';
        $last = isset($_POST['last_name']) ? trim((string)$_POST['last_name']) : '';
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $newUser = isset($_POST['username']) ? trim((string)$_POST['username']) : $username;

        // Persist into config.php — merge with existing to preserve timestamps/image
        $newCfg = array_merge($cfg, [
            'username' => $newUser,
            'password_hash' => $passwordHash,
            'first_name' => $first,
            'last_name' => $last,
            'email' => $email,
            'last_updated' => time(),
        ]);
        unset($newCfg['password']);

        $out = "<?php\n\nreturn ".var_export($newCfg, true).";\n";
        $written = @file_put_contents(__DIR__ . '/config.php', $out, LOCK_EX);
        if ($written === false) {
            andison_set_flash('error', 'Unable to save profile. Check permissions.');
        } else {
            // Sync profile to Supabase
            require_once __DIR__ . '/../includes/supabase.php';
            andison_sb_update('admin_users', [
                'username'   => $newUser,
                'first_name' => $first,
                'last_name'  => $last,
                'email'      => $email,
            ], 'username=eq.' . rawurlencode($username));
            andison_set_flash('success', 'Profile updated.');
        }

        header('Location: profile.php');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
        $current = isset($_POST['current_password']) ? (string)$_POST['current_password'] : '';
        $new = isset($_POST['new_password']) ? (string)$_POST['new_password'] : '';
        $confirm = isset($_POST['confirm_password']) ? (string)$_POST['confirm_password'] : '';

        if ($current === '' || $new === '' || $confirm === '') {
            andison_set_flash('error', 'Please fill in all password fields.');
            header('Location: profile.php');
            exit;
        }

        $currentOk = andison_admin_verify_password($cfg, $current);
        if (!$currentOk) {
            andison_set_flash('error', 'Current password is incorrect.');
            header('Location: profile.php');
            exit;
        }

        if ($new !== $confirm) {
            andison_set_flash('error', 'New passwords do not match.');
            header('Location: profile.php');
            exit;
        }

        // Update password in config (store as bcrypt hash)
        $cfg['password_hash'] = password_hash($new, PASSWORD_BCRYPT);
        unset($cfg['password']);
        $out = "<?php\n\nreturn ".var_export($cfg, true).";\n";
        $written = @file_put_contents(__DIR__ . '/config.php', $out, LOCK_EX);
        if ($written === false) {
            andison_set_flash('error', 'Unable to update password. Check permissions.');
        } else {
            // Record last_updated timestamp
            $cfgTs = andison_admin_config();
            $cfgTs['last_updated'] = time();
            @file_put_contents(__DIR__ . '/config.php', "<?php\n\nreturn " . var_export($cfgTs, true) . ";\n", LOCK_EX);
            // Sync bcrypt hash to Supabase
            require_once __DIR__ . '/../includes/supabase.php';
            andison_sb_update('admin_users', [
                'password_hash' => $cfg['password_hash'],
            ], 'username=eq.' . rawurlencode($username));
            andison_set_flash('success', 'Password updated successfully.');
        }

        header('Location: profile.php');
        exit;
    }

    // Handle profile image upload
    if (isset($_POST['action']) && $_POST['action'] === 'upload_image') {
        if (empty($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK) {
            andison_set_flash('error', 'No image uploaded or upload error.');
            header('Location: profile.php');
            exit;
        }

        $file = $_FILES['profile_image'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
        $maxBytes = 5 * 1024 * 1024; // 5MB

        if ($file['size'] > $maxBytes) {
            andison_set_flash('error', 'Image exceeds 5MB limit.');
            header('Location: profile.php');
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowed[$mime])) {
            andison_set_flash('error', 'Unsupported image type. Use JPG, PNG or GIF.');
            header('Location: profile.php');
            exit;
        }

        $ext = $allowed[$mime];
        require_once __DIR__ . '/../includes/supabase.php';
        $storageName = 'profile_' . time() . '.' . $ext;
        $webPath = andison_sb_storage_upload_tmp($file, 'profile-images', $storageName);

        if ($webPath === null) {
            andison_set_flash('error', 'Failed to upload profile image.');
            header('Location: profile.php');
            exit;
        }

        // Update config
        $cfg['profile_image'] = $webPath;
        $out = "<?php\n\nreturn " . var_export($cfg, true) . ";\n";
        $written = @file_put_contents(__DIR__ . '/config.php', $out, LOCK_EX);
        if ($written === false) {
            andison_set_flash('error', 'Uploaded but failed to save config. Check permissions.');
        } else {
            // Record last_updated timestamp
            $cfgTs = andison_admin_config();
            $cfgTs['last_updated'] = time();
            @file_put_contents(__DIR__ . '/config.php', "<?php\n\nreturn " . var_export($cfgTs, true) . ";\n", LOCK_EX);
            andison_set_flash('success', 'Profile image updated.');
        }

        header('Location: profile.php');
        exit;
    }

    // Remove profile image
    if (isset($_POST['action']) && $_POST['action'] === 'remove_image') {
        $old = isset($cfg['profile_image']) ? (string)$cfg['profile_image'] : '';
        if ($old && strpos($old, 'assets/uploads/') === 0) {
            $oldPath = __DIR__ . '/../' . $old;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        unset($cfg['profile_image']);
        $out = "<?php\n\nreturn " . var_export($cfg, true) . ";\n";
        $written = @file_put_contents(__DIR__ . '/config.php', $out, LOCK_EX);
        if ($written !== false) {
            $cfgTs = andison_admin_config();
            $cfgTs['last_updated'] = time();
            @file_put_contents(__DIR__ . '/config.php', "<?php\n\nreturn " . var_export($cfgTs, true) . ";\n", LOCK_EX);
        }
        andison_set_flash('success', 'Profile image removed.');
        header('Location: profile.php');
        exit;
    }
}

andison_admin_header('Profile Management', 'profile');
$cfg = andison_admin_config();
$firstVal = htmlspecialchars((string)($cfg['first_name'] ?? 'Andison'));
$lastVal = htmlspecialchars((string)($cfg['last_name'] ?? 'Industrial'));
$emailVal = htmlspecialchars((string)($cfg['email'] ?? 'andisonindustrial@gmail.com'));
$usernameVal = htmlspecialchars((string)($cfg['username'] ?? 'andisonindustrial'));
$profileImage = htmlspecialchars((string)($cfg['profile_image'] ?? 'assets/HOME/profile-placeholder.png'));
$createdAt = !empty($cfg['created_at']) ? date('M d, Y', (int)$cfg['created_at']) : 'Not recorded';
$lastLogin  = !empty($cfg['last_login'])  ? date('M d, Y g:i A', (int)$cfg['last_login'])  : 'Not recorded';
$flash = andison_get_flash();
?>

<style>
    .alert {display:none;padding:16px 20px;border-radius:14px;margin-bottom:20px;display:flex;align-items:center;gap:12px;animation:slideIn 0.3s ease-out}
    .alert.show {display:flex}
    .alert-success {background:rgba(16,185,129,0.1);color:#065f46;border:1px solid rgba(16,185,129,0.3)}
    .alert-error {background:rgba(239,68,68,0.1);color:#7f1d1d;border:1px solid rgba(239,68,68,0.3)}
    .alert i {font-size:20px}
    .form-group {margin-bottom:16px}
    .password-strength {display:none;margin-top:8px;height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden}
    .password-strength.show {display:block}
    .strength-bar {height:100%;width:0;transition:all 0.3s ease}
    .strength-weak {background:#ef4444}
    .strength-fair {background:#f59e0b}
    .strength-good {background:#3b82f6}
    .strength-strong {background:#10b981}
    .strength-text {font-size:12px;margin-top:4px;font-weight:600}
    .password-match {display:none;font-size:12px;margin-top:6px;color:#10b981}
    .password-match.show {display:block}
    .error-message {font-size:12px;color:var(--danger);margin-top:6px;display:none;align-items:center;gap:4px}
    .error-message.show {display:flex}
    .badge-icon {width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px}
    @keyframes slideIn {from {transform:translateY(-10px);opacity:0} to {transform:translateY(0);opacity:1}}
    @keyframes pulse {0%, 100% {opacity:1} 50% {opacity:0.7}}
    input:invalid:not(:placeholder-shown), input.error {border-color:var(--danger) !important;box-shadow:0 0 0 4px rgba(239,68,68,0.12) !important}
    input:valid:not(:placeholder-shown), input.success {border-color:#10b981 !important}
    #copyEmailBtn:hover {background:rgba(43,17,219,0.08) !important;color:var(--accent)}
    #copyEmailBtn.copied {animation:pulse 0.3s ease;color:#10b981}
    @media (max-width:768px) {
        .grid {grid-template-columns:1fr !important}
        .card {grid-column:span 1 !important}
        .row {flex-direction:column}
        .field {min-width:auto}
    }
</style>

<div id="alertContainer" style="position:fixed;top:28px;right:28px;width:100%;max-width:400px;z-index:10000"></div>

<?php if ($flash && isset($flash['type']) && isset($flash['message'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const container = document.getElementById('alertContainer');
        const type = '<?php echo htmlspecialchars($flash['type']); ?>';
        const message = '<?php echo htmlspecialchars($flash['message']); ?>';
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} show`;
        alertDiv.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;
        container.appendChild(alertDiv);
        setTimeout(() => alertDiv.style.opacity = '0', 4000);
    });
</script>
<?php endif; ?>

<style>
.prof-page-header { background:linear-gradient(135deg,#2B11DB 0%,#3d22ef 60%,#4f35e8 100%);border-radius:14px;padding:18px 22px;color:white;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }
.prof-section-hd { display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:9px;margin-bottom:14px; }
.prof-hd-icon { width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0; }
.prof-hd-title { font-size:13px;font-weight:800;line-height:1.2; }
.prof-input { border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;padding:9px 12px;width:100%;transition:border-color 0.2s; }
.prof-input:focus { outline:none;border-color:#2B11DB;box-shadow:0 0 0 3px rgba(43,17,219,0.08); }
</style>

<div class="grid" style="grid-template-columns:repeat(12,1fr);gap:18px">
    <div style="grid-column:span 12;" class="prof-page-header">
        <div>
            <div style="font-size:11px;font-weight:700;opacity:0.7;letter-spacing:0.6px;text-transform:uppercase;margin-bottom:4px;">Admin Settings</div>
            <div style="font-size:20px;font-weight:800;letter-spacing:-0.2px;display:flex;align-items:center;gap:8px;"><i class="bi bi-person-circle" style="color:#fbbf24;"></i> Profile Management</div>
        </div>
        <span style="font-size:12px;opacity:0.75;">Manage your account details and security</span>
    </div>

    <div class="card" style="grid-column:span 8">
        <div class="card" style="margin-bottom:18px;padding:18px;border-radius:12px">
            <div class="prof-section-hd" style="background:rgba(43,17,219,0.05);">
                <div class="prof-hd-icon" style="background:rgba(43,17,219,0.1);color:#2B11DB;"><i class="bi bi-person"></i></div>
                <div class="prof-hd-title" style="color:#2B11DB;">Account Information</div>
            </div>
            <form id="updateProfileForm" method="post" action="profile.php" style="margin-top:12px">
                <input type="hidden" name="action" value="update_profile">
                <div class="row">
                    <div class="field form-group">
                        <label style="display:flex;align-items:center;gap:6px"><i class="bi bi-type"></i>First Name</label>
                        <input type="text" name="first_name" value="<?php echo $firstVal; ?>" placeholder="Enter your first name" required minlength="2" maxlength="50" style="padding:12px 16px;font-size:15px">
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i><span>First name is required (2-50 characters)</span></div>
                    </div>
                    <div class="field form-group">
                        <label style="display:flex;align-items:center;gap:6px"><i class="bi bi-type"></i>Last Name</label>
                        <input type="text" name="last_name" value="<?php echo $lastVal; ?>" placeholder="Enter your last name" required minlength="2" maxlength="50" style="padding:12px 16px;font-size:15px">
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i><span>Last name is required (2-50 characters)</span></div>
                    </div>
                </div>

                <div class="row" style="margin-top:12px">
                    <div class="field form-group">
                        <label style="display:flex;align-items:center;gap:6px"><i class="bi bi-envelope"></i>Email Address <span style="color:#dc2626;margin-left:2px">*</span></label>
                        <div style="position:relative;display:flex;align-items:center">
                            <input type="email" id="emailInput" name="email" value="<?php echo $emailVal; ?>" placeholder="Enter your email" required style="padding:14px 48px 14px 16px;font-size:14px;width:100%;border-radius:14px;border:2px solid var(--border);background:#fff;transition:all 0.2s ease">
                            <button type="button" id="copyEmailBtn" title="Copy email" style="position:absolute;right:12px;background:transparent;border:none;color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;transition:all 0.2s ease;padding:0">
                                <i class="bi bi-clipboard" style="font-size:16px"></i>
                            </button>
                        </div>
                        <div style="font-size:11px;color:#6b7280;margin-top:6px;display:flex;align-items:center;gap:4px"><i class="bi bi-info-circle"></i> Used for login • All your content remains safe when changed</div>
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i><span>Please enter a valid email address</span></div>
                    </div>
                    <div class="field form-group">
                        <label style="display:flex;align-items:center;gap:6px"><i class="bi bi-person-badge"></i>Username</label>
                        <input type="text" name="username" value="<?php echo $usernameVal; ?>" placeholder="Enter your username" required minlength="3" maxlength="30" style="padding:12px 16px;font-size:15px">
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i><span>Username is required (3-30 characters)</span></div>
                    </div>
                </div>

                <div style="margin-top:16px;text-align:right">
                    <button class="btn btn-primary" id="updateProfileBtn" type="submit" style="font-size:13px;padding:10px 22px;display:inline-flex;align-items:center;gap:6px;">
                        <i class="bi bi-check-circle"></i>Update Profile
                    </button>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="prof-section-hd" style="background:rgba(239,68,68,0.05);">
                <div class="prof-hd-icon" style="background:rgba(239,68,68,0.1);color:#dc2626;"><i class="bi bi-shield-lock"></i></div>
                <div class="prof-hd-title" style="color:#dc2626;">Change Password</div>
            </div>
            <form id="changePasswordForm" method="post" action="profile.php" style="margin-top:12px">
                <input type="hidden" name="action" value="change_password">
                <div class="row">
                    <div class="field form-group" style="width:100%">
                        <label><i class="bi bi-key" style="margin-right:4px"></i>Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" required>
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i><span>Current password is required</span></div>
                    </div>
                </div>
                <div class="row" style="margin-top:8px">
                    <div class="field form-group">
                        <label><i class="bi bi-lock" style="margin-right:4px"></i>New Password</label>
                        <input type="password" id="newPasswordInput" name="new_password" placeholder="Enter new password" required minlength="8">
                        <div class="password-strength" id="strengthBar">
                            <div class="strength-bar" id="strengthFill"></div>
                        </div>
                        <div class="strength-text" id="strengthText"></div>
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i><span>Password must be at least 8 characters</span></div>
                    </div>
                    <div class="field form-group">
                        <label><i class="bi bi-lock-check" style="margin-right:4px"></i>Confirm New Password</label>
                        <input type="password" id="confirmPasswordInput" name="confirm_password" placeholder="Confirm new password" required>
                        <div class="password-match" id="passwordMatch">✓ Passwords match</div>
                        <div class="error-message"><i class="bi bi-exclamation-circle"></i><span>Passwords do not match</span></div>
                    </div>
                </div>
                <div style="margin-top:12px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                    <label style="font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;margin:0">
                        <input type="checkbox" id="showPasswords" style="width:16px;height:16px;cursor:pointer">
                        <span>Show Passwords</span>
                    </label>
                    <div style="margin-left:auto;display:flex;gap:8px">
                        <button class="btn btn-danger" id="changePasswordBtn" type="submit" style="font-size:13px;padding:10px 22px;display:inline-flex;align-items:center;gap:6px;">
                            <i class="bi bi-shield-check"></i>Change Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div style="grid-column:span 4;display:flex;flex-direction:column;gap:18px">
        <div class="card">
            <div class="prof-section-hd" style="background:rgba(16,185,129,0.05);">
                <div class="prof-hd-icon" style="background:rgba(16,185,129,0.1);color:#059669;"><i class="bi bi-person-circle"></i></div>
                <div class="prof-hd-title" style="color:#059669;">Profile Image</div>
            </div>

            <?php
                $initials = strtoupper(substr((string)($cfg['first_name'] ?? 'A'), 0, 1) . substr((string)($cfg['last_name'] ?? 'I'), 0, 1));
                $displayName = trim((string)($cfg['first_name'] ?? '') . ' ' . (string)($cfg['last_name'] ?? '')) ?: 'Admin';
            ?>

            <!-- Avatar -->
            <div style="display:flex;flex-direction:column;align-items:center;padding:4px 12px 16px;">
                <div style="position:relative;margin-bottom:14px;">
                    <div id="profileCircle" style="width:110px;height:110px;border-radius:999px;overflow:hidden;border:4px solid #2B11DB;box-shadow:0 4px 20px rgba(43,17,219,0.2);background:linear-gradient(135deg,#2B11DB,#4f35e8);display:flex;align-items:center;justify-content:center;position:relative;cursor:pointer;" onclick="document.getElementById('fileInput').click();" title="Click to change photo">
                        <img id="profileImg" src="<?php echo str_starts_with($profileImage, 'http') ? $profileImage : '../' . $profileImage; ?>" alt="Profile"
                             onerror="this.style.display='none';document.getElementById('profileInitials').style.display='flex';"
                             onload="this.style.display='block';document.getElementById('profileInitials').style.display='none';"
                             style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;">
                        <span id="profileInitials" style="display:none;font-size:32px;font-weight:900;color:#fff;letter-spacing:-1px;z-index:1;"><?php echo htmlspecialchars($initials); ?></span>
                        <!-- Hover overlay -->
                        <div style="position:absolute;inset:0;background:rgba(43,17,219,0.55);display:flex;align-items:center;justify-content:center;opacity:0;transition:opacity 0.2s;border-radius:999px;z-index:2;"
                             onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0'">
                            <i class="bi bi-camera" style="color:#fff;font-size:22px;"></i>
                        </div>
                    </div>
                    <!-- Online indicator dot -->
                    <span style="position:absolute;bottom:6px;right:6px;width:14px;height:14px;background:#10b981;border-radius:999px;border:2px solid #fff;display:block;"></span>
                </div>

                <div style="font-size:14px;font-weight:800;color:#111827;margin-bottom:2px;"><?php echo htmlspecialchars($displayName); ?></div>
                <div style="font-size:11px;color:#6b7280;margin-bottom:14px;"><?php echo htmlspecialchars($usernameVal); ?></div>

                <form id="uploadForm" method="post" action="profile.php" enctype="multipart/form-data" style="width:100%;">
                    <input type="hidden" name="action" value="upload_image">

                    <div id="dropZone" style="border:2px dashed #e5e7eb;border-radius:10px;padding:14px 10px;text-align:center;cursor:pointer;transition:border-color 0.2s,background 0.2s;background:#fafafa;margin-bottom:10px;">
                        <i class="bi bi-cloud-arrow-up" style="font-size:22px;color:#2B11DB;display:block;margin-bottom:4px;"></i>
                        <div id="dropText" style="font-size:12px;font-weight:600;color:#374151;">Click or drag to upload</div>
                        <div id="dropFileName" style="font-size:11px;color:#9ca3af;margin-top:2px;">JPG, PNG, GIF · max 5MB</div>
                        <input id="fileInput" type="file" name="profile_image" accept="image/*" style="display:none">
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                        <button class="btn btn-primary" id="uploadImageBtn" type="submit" style="font-size:12px;padding:8px 10px;display:inline-flex;align-items:center;justify-content:center;gap:5px;">
                            <i class="bi bi-upload"></i>Upload
                        </button>
                        <button class="btn btn-outline" id="removeImageBtn" type="button" style="font-size:12px;padding:8px 10px;display:inline-flex;align-items:center;justify-content:center;gap:5px;color:#dc2626;border-color:#fecaca;">
                            <i class="bi bi-trash3"></i>Remove
                        </button>
                    </div>
                </form>
            </div>
        </div>
  
        <div class="card">
            <div class="prof-section-hd" style="background:rgba(245,158,11,0.05);">
                <div class="prof-hd-icon" style="background:rgba(245,158,11,0.1);color:#d97706;"><i class="bi bi-bar-chart-line"></i></div>
                <div class="prof-hd-title" style="color:#d97706;">Account Statistics</div>
            </div>
                <div style="display:flex;flex-direction:column;gap:12px;padding:6px">
                <div style="display:flex;gap:12px;align-items:center;padding:12px;background:rgba(0,215,179,0.06);border-radius:12px;transition:all 0.3s ease">
                    <div class="badge-icon" style="background:rgba(0,215,179,0.12);color:#065f46"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <div style="font-weight:900;font-size:14px">Account Created</div>
                        <div style="color:var(--muted);font-size:12px"><?php echo htmlspecialchars($createdAt); ?></div>
                    </div>
                </div>

                <div style="display:flex;gap:12px;align-items:center;padding:12px;background:rgba(43,17,219,0.06);border-radius:12px;transition:all 0.3s ease">
                    <div class="badge-icon" style="background:rgba(43,17,219,0.12);color:var(--accent)"><i class="bi bi-box-arrow-in-right"></i></div>
                    <div>
                        <div style="font-weight:900;font-size:14px">Last Login</div>
                        <div id="lastLoginTime" data-ts="<?php echo (int)($cfg['last_login'] ?? 0); ?>" style="color:var(--muted);font-size:12px"><?php echo htmlspecialchars($lastLogin); ?></div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>

<?php
andison_admin_footer();
?>

<script>
// Real-time last login display
(function(){
    const el = document.getElementById('lastLoginTime');
    if (!el) return;
    const ts = parseInt(el.getAttribute('data-ts'), 10);
    if (!ts) return;

    function timeAgo(ts) {
        const now = Math.floor(Date.now() / 1000);
        const diff = now - ts;
        if (diff < 5)   return 'Just now';
        if (diff < 60)  return diff + ' seconds ago';
        if (diff < 120) return '1 minute ago';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 7200) return '1 hour ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        if (diff < 172800) return 'Yesterday at ' + new Date(ts * 1000).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
        const d = new Date(ts * 1000);
        return d.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) + ' ' + d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    }

    function update() { el.textContent = timeAgo(ts); }
    update();
    setInterval(update, 10000);
})();
</script>

<script>
// Email copy button
(function(){
    const copyBtn = document.getElementById('copyEmailBtn');
    const emailInput = document.getElementById('emailInput');
    
    if (!copyBtn || !emailInput) return;
    
    copyBtn.addEventListener('click', function(e){
        e.preventDefault();
        
        const email = emailInput.value.trim();
        if (!email) return;
        
        // Copy to clipboard
        navigator.clipboard.writeText(email).then(() => {
            const originalIcon = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="bi bi-check" style="font-size:16px"></i>';
            copyBtn.classList.add('copied');
            
            setTimeout(() => {
                copyBtn.innerHTML = originalIcon;
                copyBtn.classList.remove('copied');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    });
})();

// Password strength calculator
function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
    return Math.min(strength, 4);
}

function getStrengthInfo(strength) {
    const info = [
        {level: 'Weak', width: '25%', color: '#ef4444', text: 'Weak - Add uppercase, numbers, or symbols'},
        {level: 'Fair', width: '50%', color: '#f59e0b', text: 'Fair - Add numbers and special characters'},
        {level: 'Good', width: '75%', color: '#3b82f6', text: 'Good - Consider adding special characters'},
        {level: 'Strong', width: '100%', color: '#10b981', text: 'Strong - Great password!'}
    ];
    return info[strength] || info[0];
}

// Toggle password visibility
(function(){
    const cb = document.getElementById('showPasswords');
    if (!cb) return;
    
    const passwords = document.querySelectorAll('input[type="password"][name*="password"]');
    cb.addEventListener('change', function(){
        const type = cb.checked ? 'text' : 'password';
        passwords.forEach(p => p.type = type);
    });
})();

// Password strength indicator
(function(){
    const newPwInput = document.getElementById('newPasswordInput');
    const confirmPwInput = document.getElementById('confirmPasswordInput');
    const strengthBar = document.getElementById('strengthBar');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');
    const passwordMatch = document.getElementById('passwordMatch');
    
    if (!newPwInput) return;
    
    newPwInput.addEventListener('input', function(){
        const password = newPwInput.value;
        if (password.length < 1) {
            strengthBar.classList.remove('show');
            strengthText.textContent = '';
            return;
        }
        
        strengthBar.classList.add('show');
        const strength = calculatePasswordStrength(password);
        const info = getStrengthInfo(strength);
        
        strengthFill.className = `strength-bar strength-${info.level.toLowerCase()}`;
        strengthFill.style.width = info.width;
        strengthText.textContent = info.text;
    });
    
    // Password match indicator
    const checkMatch = () => {
        if (confirmPwInput.value && newPwInput.value) {
            if (newPwInput.value === confirmPwInput.value) {
                passwordMatch.classList.add('show');
            } else {
                passwordMatch.classList.remove('show');
            }
        } else {
            passwordMatch.classList.remove('show');
        }
    };
    
    newPwInput.addEventListener('input', checkMatch);
    confirmPwInput.addEventListener('input', checkMatch);
})();

// Form validation
(function(){
    const updateForm = document.getElementById('updateProfileForm');
    const changeForm = document.getElementById('changePasswordForm');
    
    function validateEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function showError(input) {
        input.classList.add('error');
        const errorMsg = input.parentElement.querySelector('.error-message');
        if (errorMsg) errorMsg.classList.add('show');
    }
    
    function clearError(input) {
        input.classList.remove('error');
        const errorMsg = input.parentElement.querySelector('.error-message');
        if (errorMsg) errorMsg.classList.remove('show');
    }
    
    if (updateForm) {
        updateForm.addEventListener('submit', function(e){
            const firstName = updateForm.querySelector('input[name="first_name"]');
            const lastName = updateForm.querySelector('input[name="last_name"]');
            const email = updateForm.querySelector('input[name="email"]');
            const username = updateForm.querySelector('input[name="username"]');
            
            let valid = true;
            
            if (!firstName.value.trim() || firstName.value.length < 2) {
                showError(firstName);
                valid = false;
            } else {
                clearError(firstName);
            }
            
            if (!lastName.value.trim() || lastName.value.length < 2) {
                showError(lastName);
                valid = false;
            } else {
                clearError(lastName);
            }
            
            if (!validateEmail(email.value)) {
                showError(email);
                valid = false;
            } else {
                clearError(email);
            }
            
            if (!username.value.trim() || username.value.length < 3) {
                showError(username);
                valid = false;
            } else {
                clearError(username);
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    }
    
    if (changeForm) {
        changeForm.addEventListener('submit', function(e){
            const current = changeForm.querySelector('input[name="current_password"]');
            const newPw = changeForm.querySelector('input[name="new_password"]');
            const confirm = changeForm.querySelector('input[name="confirm_password"]');
            
            let valid = true;
            
            if (!current.value) {
                showError(current);
                valid = false;
            } else {
                clearError(current);
            }
            
            if (newPw.value.length < 8) {
                showError(newPw);
                valid = false;
            } else {
                clearError(newPw);
            }
            
            if (newPw.value !== confirm.value) {
                showError(confirm);
                valid = false;
            } else {
                clearError(confirm);
            }
            
            if (!valid) {
                e.preventDefault();
            }
        });
    }
})();

// Profile image preview & drag/drop
(function(){
    const fileInput = document.getElementById('fileInput');
    const dropZone = document.getElementById('dropZone');
    const profileImg = document.getElementById('profileImg');
    const profileInitials = document.getElementById('profileInitials');
    const dropFileName = document.getElementById('dropFileName');
    const dropText = document.getElementById('dropText');
    const removeBtn = document.getElementById('removeImageBtn');

    function previewFile(file) {
        if (!file) return;
        if (!file.type.startsWith('image/')) { alert('Please select an image file'); return; }
        if (file.size > 5 * 1024 * 1024) { alert('File size exceeds 5MB limit'); return; }

        // Show filename in drop zone
        const kb = (file.size / 1024).toFixed(0);
        dropText.textContent = file.name;
        dropFileName.textContent = (file.size > 1024 * 1024 ? (file.size / 1024 / 1024).toFixed(1) + ' MB' : kb + ' KB');
        dropZone.style.borderColor = '#2B11DB';
        dropZone.style.background = 'rgba(43,17,219,0.04)';

        const reader = new FileReader();
        reader.onload = function(e){
            profileImg.src = e.target.result;
            profileImg.style.display = 'block';
            if (profileInitials) profileInitials.style.display = 'none';
            document.getElementById('profileCircle').style.boxShadow = '0 4px 20px rgba(16,185,129,0.35)';
        };
        reader.readAsDataURL(file);
    }

    dropZone.addEventListener('click', function(){ fileInput.click(); });

    fileInput.addEventListener('change', function(e){
        const f = e.target.files && e.target.files[0];
        if (f) previewFile(f);
    });

    dropZone.addEventListener('dragover', function(e){
        e.preventDefault();
        dropZone.style.borderColor = '#2B11DB';
        dropZone.style.background = 'rgba(43,17,219,0.06)';
    });

    dropZone.addEventListener('dragleave', function(){
        dropZone.style.borderColor = '#e5e7eb';
        dropZone.style.background = '#fafafa';
    });

    dropZone.addEventListener('drop', function(e){
        e.preventDefault();
        dropZone.style.borderColor = '#e5e7eb';
        dropZone.style.background = '#fafafa';
        const dt = e.dataTransfer;
        if (!dt || !dt.files || !dt.files[0]) return;
        const f = dt.files[0];
        try { fileInput.files = dt.files; } catch(err) {}
        previewFile(f);
    });

    removeBtn.addEventListener('click', function(){
        if (!confirm('Are you sure you want to remove your profile image?')) return;
        const f = document.createElement('form');
        f.method = 'post'; f.action = 'profile.php';
        const i = document.createElement('input');
        i.type = 'hidden'; i.name = 'action'; i.value = 'remove_image';
        f.appendChild(i); document.body.appendChild(f); f.submit();
    });
})();
</script>



