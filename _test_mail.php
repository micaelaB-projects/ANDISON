<?php
require 'C:/xamppp/htdocs/ANDISON/Andison/admin/_auth.php';
require 'C:/xamppp/htdocs/ANDISON/Andison/includes/supabase.php';

$cfg = andison_admin_config();
print_r($cfg);

$email = 'johncedricreyes14@gmail.com';
$sbData = andison_sb_select('admin_users', 'email=eq.' . rawurlencode($email) . '&limit=1');
print_r($sbData);
