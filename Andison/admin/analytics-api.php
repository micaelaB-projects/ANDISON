<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
 
require_once __DIR__ . '/../includes/analytics.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

$analytics = andison_get_analytics();
$chartData   = andison_get_daily_chart(7);
$chartData30 = andison_get_daily_chart(30);

// Brands sorted descending
$brands = $analytics['brands'] ?? [];
arsort($brands);

// Categories sorted descending
$categories = $analytics['categories'] ?? [];
arsort($categories);

// Sort top pages descending (kept for legacy, unused on dashboard now)
$pages = $analytics['pages'] ?? [];
arsort($pages);
$topPages = array_slice($pages, 0, 6, true);

echo json_encode([
    'total_pageviews'  => $analytics['total_pageviews'],
    'unique_sessions'  => $analytics['unique_sessions'],
    'today_pageviews'  => $analytics['today_pageviews'],
    'today_unique'     => $analytics['today_unique'],
    'week_pageviews'   => $analytics['week_pageviews'],
    'week_unique'      => $analytics['week_unique'],
    'month_pageviews'  => $analytics['month_pageviews'],
    'month_unique'     => $analytics['month_unique'],
    'day_label'        => date('l, F j'),
    'week_label'       => date('M j', strtotime('monday this week')),
    'month_label'      => date('F Y'),
    'chart'            => $chartData,
    'chart30'          => $chartData30,
    'brands'           => $brands,
    'categories'       => $categories,
], JSON_UNESCAPED_SLASHES);
