<?php
if (PHP_SAPI !== 'cli') {
	$configuredToken = getenv('ANDISON_DEBUG_TOKEN');
	$configuredToken = $configuredToken === false ? '' : trim((string)$configuredToken);
	$providedToken = isset($_GET['token']) ? trim((string)$_GET['token']) : '';
	if ($configuredToken === '' || !hash_equals($configuredToken, $providedToken)) {
		http_response_code(403);
		exit('Debug endpoint disabled.');
	}
}

require "C:/xampp/htdocs/ANDISON-1/Andison/includes/storage.php";
define("ANDISON_ANALYTICS_FILE", "C:/xampp/htdocs/ANDISON-1/Andison/data/analytics.json");
require "C:/xampp/htdocs/ANDISON-1/Andison/includes/analytics.php";
$_COOKIE = [];
$before = json_decode(file_get_contents("C:/xampp/htdocs/ANDISON-1/Andison/data/analytics.json"), true);
echo "BEFORE total: " . $before["total_pageviews"] . " today: " . $before["today_pageviews"] . "\n";
andison_track_visit("test");
$after = json_decode(file_get_contents("C:/xampp/htdocs/ANDISON-1/Andison/data/analytics.json"), true);
echo "AFTER  total: " . $after["total_pageviews"] . " today: " . $after["today_pageviews"] . "\n";
