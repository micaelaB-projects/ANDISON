<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;



