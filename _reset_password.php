<?php
declare(strict_types=1);

require_once __DIR__ . '/Andison/admin/_auth.php';
require_once __DIR__ . '/Andison/includes/supabase.php';

// The new password
$newPassword = 'admin'; // change this if you want
$newHash = password_hash($newPassword, PASSWORD_BCRYPT);
$username = 'andisonindustrial'; // using your default username

// Update in Supabase
$success = andison_sb_update('admin_users', [
    'password_hash' => $newHash,
], 'username=eq.' . rawurlencode($username));

// Update local config just in case it falls back
$cfg = require __DIR__ . '/Andison/admin/config.php';
$cfg['password_hash'] = $newHash;
file_put_contents(__DIR__ . '/Andison/admin/config.php', "<?php\n\nreturn " . var_export($cfg, true) . ";\n", LOCK_EX);

if ($success) {
    echo "<h1>Password Reset Successful!</h1>";
    echo "<p>Your password has been changed to: <strong>" . htmlspecialchars($newPassword) . "</strong></p>";
    echo "<p>Username: <strong>" . htmlspecialchars($username) . "</strong></p>";
    echo '<a href="/ANDISON/Andison/admin/login.php">Click here to log in</a>';
} else {
    echo "<h1>Error</h1>";
    echo "<p>Failed to update the password in Supabase. Check your connection or table permissions.</p>";
}
