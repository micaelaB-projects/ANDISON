<?php
$errorLog = 'C:\\xampp\\apache\\logs\\error.log';

if (!file_exists($errorLog)) {
    die("Error log not found at: " . $errorLog);
}

// Read last 100 lines
$lines = array_slice(file($errorLog), -100);

echo "Last 100 lines of error log:\n";
echo "=".str_repeat("=", 70)."\n";
foreach ($lines as $line) {
    echo $line;
}
echo "\n" . str_repeat("=", 72);
?>
