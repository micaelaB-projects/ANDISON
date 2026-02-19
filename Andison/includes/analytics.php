<?php

declare(strict_types=1);

require_once __DIR__ . '/storage.php';

define('ANDISON_ANALYTICS_FILE', __DIR__ . '/../data/analytics.json');

/**
 * Record a page visit. Call once per page load on public-facing pages.
 *
 * Deduplication strategy:
 *   - Uses PHP sessions (no cookies set manually, avoids header issues).
 *   - Session ID is unique per browser tab/window open.
 *   - A per-session-per-day key ensures only the FIRST page load counts.
 *   - Navigating to other pages = NOT counted again.
 *   - Opening a new browser session = new count.
 */
function andison_track_visit(string $page = 'unknown'): void
{
    $today     = date('Y-m-d');
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $month     = date('Y-m');

    // Use the admin's dedicated session name only if already active,
    // otherwise use the default public session.
    if (session_status() === PHP_SESSION_NONE) {
        // Only start a session if headers haven't been sent yet
        if (!headers_sent()) {
            session_start();
        }
    }

    // Key is fixed (no date) — counted ONCE per browser session.
    // Closing the browser clears the session, so reopening counts again.
    $sessionKey = 'av';

    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!empty($_SESSION[$sessionKey])) {
            return; // already counted this browser session
        }
        $_SESSION[$sessionKey] = 1;
    } else {
        // Sessions unavailable (headers already sent) — deduplicate within same request only
        static $counted = false;
        if ($counted) {
            return;
        }
        $counted = true;
    }

    $data = andison_read_json_file(ANDISON_ANALYTICS_FILE, []);

    // ── Structural defaults ────────────────────────────────────────
    $data += [
        'total_pageviews'   => 0,
        'unique_sessions'   => 0,
        'today_pageviews'   => 0,
        'today_unique'      => 0,
        'today_date'        => $today,
        'week_pageviews'    => 0,
        'week_unique'       => 0,
        'week_start'        => $weekStart,
        'month_pageviews'   => 0,
        'month_unique'      => 0,
        'month_key'         => $month,
        'daily'             => [],
        'pages'             => [],
    ];

    // ── Reset today counters if date changed ──────────────────────
    if ($data['today_date'] !== $today) {
        $data['today_pageviews'] = 0;
        $data['today_unique']    = 0;
        $data['today_date']      = $today;
    }

    // ── Reset week counters if new week ───────────────────────────
    if ($data['week_start'] !== $weekStart) {
        $data['week_pageviews'] = 0;
        $data['week_unique']    = 0;
        $data['week_start']     = $weekStart;
    }

    // ── Reset month counters if new month ─────────────────────────
    if ($data['month_key'] !== $month) {
        $data['month_pageviews'] = 0;
        $data['month_unique']    = 0;
        $data['month_key']       = $month;
    }

    // ── Count this visit ONCE per browser session per day ─────────
    $data['total_pageviews']++;
    $data['today_pageviews']++;
    $data['week_pageviews']++;
    $data['month_pageviews']++;
    $data['unique_sessions']++;
    $data['today_unique']++;
    $data['week_unique']++;
    $data['month_unique']++;

    // Daily visitor breakdown (keep last 30 days)
    $data['daily'][$today] = ($data['daily'][$today] ?? 0) + 1;
    if (count($data['daily']) > 30) {
        ksort($data['daily']);
        $data['daily'] = array_slice($data['daily'], -30, 30, true);
    }

    // Entry-page tracking (which page the visitor first landed on)
    $data['pages'][$page] = ($data['pages'][$page] ?? 0) + 1;

    andison_write_json_file(ANDISON_ANALYTICS_FILE, $data);
}

/**
 * Return the current analytics data array.
 */
function andison_get_analytics(): array
{
    $data = andison_read_json_file(ANDISON_ANALYTICS_FILE, []);

    $today     = date('Y-m-d');
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $month     = date('Y-m');

    return array_merge([
        'total_pageviews'  => 0,
        'unique_sessions'  => 0,
        'today_pageviews'  => 0,
        'today_unique'     => 0,
        'today_date'       => $today,
        'week_pageviews'   => 0,
        'week_unique'      => 0,
        'week_start'       => $weekStart,
        'month_pageviews'  => 0,
        'month_unique'     => 0,
        'month_key'        => $month,
        'daily'            => [],
        'pages'            => [],
    ], $data);
}

/**
 * Return last N days of daily page-view data as [['date'=>..., 'views'=>...], ...]
 */
function andison_get_daily_chart(int $days = 7): array
{
    $analytics = andison_get_analytics();
    $result    = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $d          = date('Y-m-d', strtotime("-{$i} days"));
        $result[]   = [
            'date'  => date('M j', strtotime($d)),
            'views' => $analytics['daily'][$d] ?? 0,
        ];
    }
    return $result;
}

/**
 * Internal helper — increment a named entity counter (brand/category).
 * Uses PHP session so each brand/category is counted once per browser session per day.
 */
function _andison_track_entity(string $type, string $name): void
{
    if (empty($name)) return;

    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }

    $sessionKey = 'ae_' . $type . '_' . md5($name) . '_' . date('Y-m-d');

    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!empty($_SESSION[$sessionKey])) return;
        $_SESSION[$sessionKey] = 1;
    }

    $data = andison_read_json_file(ANDISON_ANALYTICS_FILE, []);
    $data[$type]        = $data[$type] ?? [];
    $data[$type][$name] = ($data[$type][$name] ?? 0) + 1;
    andison_write_json_file(ANDISON_ANALYTICS_FILE, $data);
}

/**
 * Record a brand page visit. Call with the brand's display name.
 */
function andison_track_brand_visit(string $brand): void
{
    _andison_track_entity('brands', trim($brand));
}

/**
 * Record a category page visit. Call with the category's display name.
 */
function andison_track_category_visit(string $category): void
{
    _andison_track_entity('categories', trim($category));
}
