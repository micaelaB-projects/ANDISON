<?php require __DIR__.'/admin/_auth.php'; require __DIR__.'/includes/brands_info.php'; print_r(count(andison_sb_select('products', 'select=id&limit=10000')));
