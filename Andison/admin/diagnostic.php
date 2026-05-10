<?php

declare(strict_types=1);

require_once __DIR__ . '/../Andison/includes/_auth.php';
andison_require_admin();

?><!DOCTYPE html>
<html>
<head>
    <title>Supabase Diagnostic</title>
    <style>
        body { font-family: monospace; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .status { padding: 15px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f9f9f9; font-weight: bold; }
        tr:hover { background: #f9f9f9; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h1>🔧 Supabase Diagnostic Tool</h1>

    <?php
    require_once __DIR__ . '/../Andison/includes/supabase.php';
    require_once __DIR__ . '/../Andison/includes/aboutus_settings.php';

    // Check 1: Environment variables
    echo '<h2>1. Environment Variables</h2>';
    $isEnabled = andison_supabase_is_enabled();
    if ($isEnabled) {
        echo '<div class="status success">✓ Supabase is ENABLED</div>';
        echo '<p><strong>URL:</strong> ' . htmlspecialchars(ANDISON_SUPABASE_URL) . '</p>';
        echo '<p><strong>Key:</strong> ' . (strlen(ANDISON_SUPABASE_KEY) > 10 ? substr(ANDISON_SUPABASE_KEY, 0, 10) . '...' : 'SHORT') . '</p>';
    } else {
        echo '<div class="status error">✗ Supabase is DISABLED - Missing env variables</div>';
        echo '<p>Need to set: <code>ANDISON_SUPABASE_URL</code> and <code>ANDISON_SUPABASE_KEY</code></p>';
    }

    // Check 2: Table connectivity
    echo '<h2>2. Table Connectivity Test</h2>';
    $testSelect = andison_sb_select('youtube_links', 'section=eq.banner_settings&limit=1');
    if ($testSelect !== false && is_array($testSelect)) {
        echo '<div class="status success">✓ Can query youtube_links table</div>';
        echo '<p>Found ' . count($testSelect) . ' records with section=banner_settings</p>';
    } else {
        echo '<div class="status error">✗ Cannot query youtube_links table</div>';
    }

    // Check 3: Insert/Delete permissions
    echo '<h2>3. Write Permissions Test</h2>';
    echo '<form method="post" style="margin-bottom: 20px;">';
    echo '<button type="submit" name="test_insert" value="1">Test Insert Permission</button> ';
    echo '<button type="submit" name="test_delete" value="1">Test Delete Permission</button>';
    echo '</form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['test_insert'])) {
            echo '<div class="status info">Testing INSERT...</div>';
            $testData = [
                'section' => 'test_aboutus_' . time(),
                'title' => 'Test Record',
                'url' => json_encode(['test' => 'value']),
                'sort_order' => 99,
            ];
            $result = andison_sb_insert('youtube_links', [$testData]);
            if ($result) {
                echo '<div class="status success">✓ INSERT successful</div>';
            } else {
                echo '<div class="status error">✗ INSERT failed - Check error logs</div>';
                echo '<p><small>Check PHP error log or <code>$_SERVER["APACHE_LOG_DIR"]/error.log</code></small></p>';
            }
        }

        if (isset($_POST['test_delete'])) {
            echo '<div class="status info">Testing DELETE...</div>';
            $result = @andison_sb_delete('youtube_links', 'section=like.test_aboutus_%');
            if ($result) {
                echo '<div class="status success">✓ DELETE successful</div>';
            } else {
                echo '<div class="status warning">⚠ DELETE returned false (might be OK if no records)</div>';
            }
        }
    }

    // Check 4: Current About Us data
    echo '<h2>4. Current About Us Settings</h2>';
    $current = andison_get_aboutus_settings();
    echo '<table>';
    echo '<tr><th>Key</th><th>Value Preview</th></tr>';
    foreach ($current as $key => $value) {
        $preview = is_string($value) ? (strlen($value) > 50 ? substr($value, 0, 50) . '...' : $value) : (is_array($value) ? 'Array' : var_export($value, true));
        echo '<tr><td>' . htmlspecialchars($key) . '</td><td><code>' . htmlspecialchars($preview) . '</code></td></tr>';
    }
    echo '</table>';

    // Check 5: Error log tail
    echo '<h2>5. Recent Error Log</h2>';
    $errorLogFile = ini_get('error_log');
    if ($errorLogFile && is_file($errorLogFile)) {
        $lines = file($errorLogFile);
        $recent = array_slice($lines, -20); // Last 20 lines
        echo '<pre style="background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; max-height: 300px;">';
        foreach ($recent as $line) {
            if (stripos($line, 'supabase') !== false || stripos($line, 'aboutus') !== false) {
                echo '<strong>' . htmlspecialchars($line) . '</strong>';
            } else {
                echo htmlspecialchars($line);
            }
        }
        echo '</pre>';
    } else {
        echo '<div class="status warning">Error log not found at: ' . htmlspecialchars($errorLogFile) . '</div>';
    }
    ?>

    <hr style="margin: 30px 0;">
    <p><small><a href="index.php">← Back to Dashboard</a></small></p>
</div>
</body>
</html>
