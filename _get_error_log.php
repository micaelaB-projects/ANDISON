<?php
$errorLog = 'C:\\xampp\\apache\\logs\\error.log';

if (!file_exists($errorLog)) {
    die("Error log not found");
}

// Read last 200 lines
$lines = array_slice(file($errorLog), -200);
echo implode('', $lines);
?>
