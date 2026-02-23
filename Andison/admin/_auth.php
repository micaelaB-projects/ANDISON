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

function andison_admin_config(): array
{
    $cfg = require __DIR__ . '/config.php';
    return is_array($cfg) ? $cfg : ['username' => 'andisonindustrial', 'password' => 'ais.inc'];
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



