<?php

declare(strict_types=1);
 
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Use a dedicated session name for the admin panel so it never
    // shares the PHPSESSID cookie with the public website. This prevents
    // the admin's own browser visit from consuming the per-session
    // analytics deduplication slot and blocking new visitor counts.
    session_name('ANDISON_ADMIN');
    session_start();
}

require_once __DIR__ . '/../includes/env_loader.php';

if (!function_exists('andison_admin_env')) {
    function andison_admin_env(string $key): ?string
    {
        $value = getenv($key);
        if ($value === false && isset($_ENV[$key])) {
            $value = $_ENV[$key];
        }
        if ($value === false && isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        }
        if ($value === false) {
            return null;
        }
        $value = trim((string)$value);
        return $value === '' ? null : $value;
    }
}

if (!function_exists('andison_admin_is_password_hash')) {
    function andison_admin_is_password_hash(string $value): bool
    {
        if ($value === '') {
            return false;
        }
        $info = password_get_info($value);
        return !empty($info['algo']);
    }
}

if (!function_exists('andison_admin_default_password_hash')) {
    function andison_admin_default_password_hash(): string
    {
        return '$2y$10$nWLOATfGJIyL//lHaL7YCej3HqP8pnfA1PhgcVRw0agzV0i3LrDwu';
    }
}

function andison_admin_config(): array
{
    require_once __DIR__ . '/../includes/supabase.php';

    // Try to load auth configuration directly from Supabase first
    if (function_exists('andison_sb_select')) {
        $sbData = andison_sb_select('admin_users', 'limit=1');
        if (is_array($sbData) && !empty($sbData[0])) {
            $user = $sbData[0];
            $cfg = [
                'username'      => trim((string)($user['username'] ?? 'andisonindustrial')),
                'password_hash' => trim((string)($user['password_hash'] ?? '')),
                'first_name'    => trim((string)($user['first_name'] ?? '')),
                'last_name'     => trim((string)($user['last_name'] ?? '')),
                'email'         => trim((string)($user['email'] ?? '')),
            ];

            // If Supabase has a valid hash, return the config immediately
            if (andison_admin_is_password_hash($cfg['password_hash'])) {
                return $cfg;
            }
        }
    }

    // Fallback to local config if Supabase fails or doesn't have a valid user
    if (file_exists(__DIR__ . '/config.php')) {
        $cfg = require __DIR__ . '/config.php';
    }
    if (!isset($cfg) || !is_array($cfg)) {
        $cfg = [];
    }

    $cfg['username'] = trim((string)($cfg['username'] ?? 'andisonindustrial'));
    if ($cfg['username'] === '') {
        $cfg['username'] = 'andisonindustrial';
    }

    $hash = trim((string)($cfg['password_hash'] ?? ''));
    if (!andison_admin_is_password_hash($hash)) {
        $legacy = trim((string)($cfg['password'] ?? ''));
        if (andison_admin_is_password_hash($legacy)) {
            $hash = $legacy;
        } elseif ($legacy !== '') {
            $hash = password_hash($legacy, PASSWORD_BCRYPT);
        } else {
            $hash = andison_admin_default_password_hash();
        }
    }
    $cfg['password_hash'] = $hash;
    unset($cfg['password']);

    $envUsername = andison_admin_env('ANDISON_ADMIN_USERNAME');
    if ($envUsername !== null) {
        $cfg['username'] = $envUsername;
    }

    $envHash = andison_admin_env('ANDISON_ADMIN_PASSWORD_HASH');
    if ($envHash !== null && andison_admin_is_password_hash($envHash)) {
        $cfg['password_hash'] = $envHash;
    } else {
        $envPlainPassword = andison_admin_env('ANDISON_ADMIN_PASSWORD');
        if ($envPlainPassword !== null) {
            $cfg['password_hash'] = password_hash($envPlainPassword, PASSWORD_BCRYPT);
        }
    }

    return $cfg;
}

if (!function_exists('andison_admin_verify_password')) {
    function andison_admin_verify_password(array $cfg, string $password): bool
    {
        if ($password === '') {
            return false;
        }

        $hash = (string)($cfg['password_hash'] ?? '');
        if (andison_admin_is_password_hash($hash)) {
            return password_verify($password, $hash);
        }

        $legacy = (string)($cfg['password'] ?? '');
        if ($legacy === '') {
            return false;
        }

        if (andison_admin_is_password_hash($legacy)) {
            return password_verify($password, $legacy);
        }

        return hash_equals($legacy, $password);
    }
}

if (!function_exists('andison_admin_save_config')) {
    function andison_admin_save_config(array $cfg): bool
    {
        unset($cfg['password']);

        $hash = (string)($cfg['password_hash'] ?? '');
        if (!andison_admin_is_password_hash($hash)) {
            return false;
        }

        $out = "<?php\n\nreturn " . var_export($cfg, true) . ";\n";
        return @file_put_contents(__DIR__ . '/config.php', $out, LOCK_EX) !== false;
    }
}

function andison_require_admin(): void
{
    if (!empty($_SESSION['andison_admin']) && $_SESSION['andison_admin'] === true) {
        return;
    }

    $next = $_SERVER['REQUEST_URI'] ?? '/andison_industrial/admin/index.php';
    header('Location: login.php?next=' . urlencode($next));
    exit;
}

function andison_set_flash(string $type, string $message): void
{
    $_SESSION['andison_flash'] = ['type' => $type, 'message' => $message];
}

function andison_get_flash(): ?array
{
    if (empty($_SESSION['andison_flash']) || !is_array($_SESSION['andison_flash'])) {
        return null;
    }
    $v = $_SESSION['andison_flash'];
    unset($_SESSION['andison_flash']);
    return $v;
}



