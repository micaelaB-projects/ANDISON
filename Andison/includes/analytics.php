<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

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

    // Write visit to Supabase (async — does not block page load)
    andison_sb_insert_async('analytics', [
        'session_key' => session_id() ?: uniqid('av_', true),
        'page'        => $page,
        'visited_at'  => date('c'),
        'date_key'    => $today,
    ]);
}

/**
 * Return the current analytics data array.
 */
function andison_get_analytics(): array
{
    $today     = date('Y-m-d');
    $weekStart = date('Y-m-d', strtotime('monday this week'));
    $month     = date('Y-m');

    $defaults = [
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
    ];

    // Aggregate all data directly from Supabase.
    $rows = andison_sb_select('analytics', 'limit=10000&order=visited_at.asc');
    if (!empty($rows)) {
        $allSk = $todaySk = $weekSk = $monthSk = [];
        $daily = $brands = $categories = $pages = [];
        foreach ($rows as $row) {
            $sk = $row['session_key'] ?? '';
            $dk = $row['date_key'] ?? substr((string)($row['visited_at'] ?? ''), 0, 10);
            $bv = $row['brand_viewed'] ?? null;
            $pg = $row['page'] ?? null;
            if ($bv !== null && $bv !== '') {
                $brands[$bv] = ($brands[$bv] ?? 0) + 1;
                continue;
            }
            if (is_string($pg) && str_starts_with($pg, '~cat:')) {
                $cat = substr($pg, 5);
                $categories[$cat] = ($categories[$cat] ?? 0) + 1;
                continue;
            }
            if ($sk !== '') $allSk[$sk] = true;
            $daily[$dk] = ($daily[$dk] ?? 0) + 1;
            if ($pg !== null && $pg !== '') $pages[$pg] = ($pages[$pg] ?? 0) + 1;
            if ($dk === $today && $sk !== '') $todaySk[$sk] = true;
            if ($dk >= $weekStart && $sk !== '') $weekSk[$sk] = true;
            if (str_starts_with($dk, $month) && $sk !== '') $monthSk[$sk] = true;
        }
        $weekPv = 0;
        foreach ($daily as $dk => $v) { if ($dk >= $weekStart) $weekPv += $v; }
        $monthPv = 0;
        foreach ($daily as $dk => $v) { if (str_starts_with($dk, $month)) $monthPv += $v; }
        return [
            'total_pageviews'  => array_sum($daily),
            'unique_sessions'  => count($allSk),
            'today_pageviews'  => $daily[$today] ?? 0,
            'today_unique'     => count($todaySk),
            'today_date'       => $today,
            'week_pageviews'   => $weekPv,
            'week_unique'      => count($weekSk),
            'week_start'       => $weekStart,
            'month_pageviews'  => $monthPv,
            'month_unique'     => count($monthSk),
            'month_key'        => $month,
            'daily'            => $daily,
            'pages'            => $pages,
            'brands'           => $brands,
            'categories'       => $categories,
        ];
    }

    return $defaults;
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

    // Deduplicate per browser session (no date — the browser session closes and reopens naturally).
    // Old key included a date suffix which caused stuck dedup across tests on the same day.
    $sessionKey = 'ae_' . $type . '_' . md5($name);

    if (session_status() === PHP_SESSION_ACTIVE) {
        if (!empty($_SESSION[$sessionKey])) return;
        $_SESSION[$sessionKey] = 1;
    }

    // Write to Supabase (async — does not block page load)
    // Use entity-specific session key so it never collides with the generic page-visit
    // row (which uses bare session_id()). Without this, ignore-duplicates silently drops it.
    $entitySessionKey = (session_id() ?: uniqid('ae_', true)) . '_' . $type[0] . '_' . substr(md5($name), 0, 8);
    $sbRow = [
        'session_key' => $entitySessionKey,
        'visited_at'  => date('c'),
        'date_key'    => date('Y-m-d'),
    ];
    if ($type === 'brands') {
        $sbRow['brand_viewed'] = $name;
        $sbRow['page'] = '';
    } else {
        // categories — stored in page field with ~cat: prefix
        $sbRow['page'] = '~cat:' . $name;
    }
    andison_sb_insert_async('analytics', $sbRow); // async — does not block page load
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
