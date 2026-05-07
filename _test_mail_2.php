<?php
require 'C:/xamppp/htdocs/ANDISON/Andison/includes/mailer.php';

$t = microtime(true);
$success = andison_send_mail('johncedricreyes14@gmail.com', 'Test', 'TestBody');
$elapsed = microtime(true) - $t;

echo "Success: " . ($success ? "Yes" : "No") . "\n";
echo "Time: " . round($elapsed, 2) . "s\n";
