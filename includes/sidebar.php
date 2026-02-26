<?php
/**
 * Sidebar Include File
 * Contains all CSS, HTML, and JavaScript for:
 *  - Overlay Backdrop
 *  - Sidebar Navigation (slide-out panel)
 *  - Mini Sidebar (icon bar)
 *  - Mobile FAB button
 *  - Mini Popover (subcategory flyout)
 *
 * Usage: <?php require_once 'includes/sidebar.php'; ?>
 *        Place after <body> and after your <header>.
 */

// Define the site base path for all sidebar links
$_sidebar_base = '/ANDISON/';

// ================================================================
// ACTIVE CATEGORY DETECTION
// ================================================================
// Detect current page and extract category information
$current_page = basename($_SERVER['PHP_SELF']);
$current_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['PHP_SELF']);
$current_path = str_replace('\\', '/', $current_path); // Normalize backslashes

// Extract category and subcategory from current path
// Examples: /ANDISON/arc-welding-machine/arc-welding-machine.php => arc-welding-machine
// Also handles deep nesting: /ANDISON/arc-welding-machine/accessories-and-consumables/welding-torch-gun.php
$path_parts = explode('/', trim($current_path, '/'));
$current_category = '';
$current_subcategory = '';
$current_nested_subcategory = '';

// Find ANDISON index and extract category/subcategory/nested
if (($andison_idx = array_search('ANDISON', $path_parts)) !== false && isset($path_parts[$andison_idx + 1])) {
    $current_category = $path_parts[$andison_idx + 1];
    
    // Handle deep nesting (e.g., drilling-and-lifting/magnetic-drill/b-line-series.php)
    if (isset($path_parts[$andison_idx + 2]) && $path_parts[$andison_idx + 2] !== $current_page) {
        $current_subcategory = $path_parts[$andison_idx + 2];
        
        // Check for 4th level (nested subcategory)
        if (isset($path_parts[$andison_idx + 3]) && $path_parts[$andison_idx + 3] !== $current_page) {
            $current_nested_subcategory = $path_parts[$andison_idx + 3];
        } else {
            // The page file is at the 3rd level
            $current_nested_subcategory = $current_page;
        }
    } else {
        // Main category page (e.g., arc-welding-machine.php)
        $current_subcategory = $current_page;
    }
}
?>

<!-- Bootstrap Icons (ensures icons load regardless of parent page) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- ============================================================
     SIDEBAR CSS
     ============================================================ -->
<style>
    /* Overlay Backdrop */
    .overlay-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.3);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s;
        z-index: 60;
    }
    .overlay-backdrop.active {
        opacity: 1;
        visibility: visible;
    }

    /* ── Sidebar Overlay (slide-out panel) ── */
    .sidebar-overlay {
        position: fixed;
        left: 0;
        top: calc(14px + 50px + 14px + 12px + 52px);
        bottom: 0;
        width: 320px;
        max-width: 80%;
        background: #fff;
        box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        transform: translateX(-100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 70;
        padding: 20px 12px;
        overflow-y: auto;
    }
    .sidebar-overlay.active {
        transform: translateX(0);
    }
    .sidebar-overlay h3 {
        font-size: 18px;
        margin-bottom: 24px;
        color: #222;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* ── Sidebar List ── */
    .sidebar-list { list-style: none; padding: 0; margin: 0; }
    .sidebar-list li { border-bottom: 1px solid #e5e7eb; }
    .sidebar-list li:last-child { border-bottom: none; }
    .sidebar-list a {
        display: flex;
        gap: 12px;
        padding: 16px 12px;
        color: #1f2937;
        text-decoration: none;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        font-size: 15px;
        min-height: 48px;
    }
    .sidebar-list a:hover {
        background: #f3f4f6;
        color: #2B11DB;
        padding-left: 16px;
    }
    .sidebar-list a:active {
        background: rgba(43, 17, 219, 0.08);
    }
    .sidebar-list li a.active {
        background: #f3f4f6;
        color: #2B11DB;
        font-weight: 600;
        border-left: 4px solid #2B11DB;
        padding-left: 12px;
    }
    .sidebar-list li a.active .sidebar-icon {
        color: #2B11DB;
    }
    
    /* Active parent category highlight */
    .sidebar-list li.active-parent > a {
        background: rgba(43, 17, 219, 0.06);
        color: #2B11DB;
        font-weight: 500;
        border-left: 4px solid #2B11DB;
        padding-left: 12px;
    }
    .sidebar-list li.active-parent > a .sidebar-icon {
        color: #2B11DB;
    }
    
    /* Active parent category in mini sidebar */
    .mini-sidebar-icon.active-parent {
        background: rgba(0, 215, 179, 0.2);
    }
    .mini-sidebar-icon.active-parent .sub-indicator {
        background: #00D7B3;
        border-color: #2B11DB;
    }
    
    /* Highlight subcategory items */
    .sidebar-sublist a.active-subcategory {
        background: rgba(43, 17, 219, 0.12);
        color: #2B11DB;
        font-weight: 600;
        border-left: 3px solid #2B11DB;
        padding-left: 13px;
    }
    .sidebar-sublist a.active-subcategory:hover {
        background: rgba(43, 17, 219, 0.18);
    }
    
    /* Highlight nested subcategory items */
    .sidebar-nested-sublist a.active-nested {
        background: rgba(43, 17, 219, 0.15);
        color: #2B11DB;
        font-weight: 600;
        border-left: 2px solid #2B11DB;
        padding-left: 10px;
    }
    .sidebar-icon {
        color: #5b21b6;
        width: 24px;
        height: 24px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .sidebar-list a .sidebar-label { flex: 1; }
    .sidebar-list a .sidebar-arrow {
        width: 20px;
        height: 20px;
        display: none;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 14px;
        flex-shrink: 0;
        margin-left: 8px;
    }
    .sidebar-list li.has-sub a .sidebar-arrow { display: flex; }

    /* ── Sub-list ── */
    .sidebar-sublist {
        list-style: none;
        margin: 2px 0;
        padding: 4px 0 4px 12px;
        background: #f8f9fa;
        border-left: 3px solid #2B11DB;
        max-height: 500px;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        opacity: 1;
        display: block;
    }
    .sidebar-sublist.collapsed {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
    }
    .sidebar-sublist li { padding: 0; border: none; }
    .sidebar-sublist a {
        color: #4b5563;
        font-size: 13px;
        padding: 9px 16px 9px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        min-height: 38px;
        transition: all 0.2s ease;
    }
    .sidebar-sublist a:hover {
        color: #2B11DB;
        background: rgba(43, 17, 219, 0.08);
        padding-left: 16px;
    }

    /* ── Nested Sub-list ── */
    .sidebar-sublist li.has-nested-sub { position: relative; }
    .sidebar-sublist li.has-nested-sub > a { padding-right: 24px; }
    .nested-toggle {
        position: absolute;
        right: 0;
        top: 6px;
        background: transparent;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0;
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }
    .nested-toggle:focus { outline: none; }
    .nested-toggle .bi { transition: transform 200ms ease; }
    .nested-toggle[aria-expanded="true"] .bi { transform: rotate(90deg); }

    .sidebar-nested-sublist {
        list-style: none;
        margin: 2px 0;
        padding: 4px 0 4px 12px;
        max-height: 500px;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        opacity: 1;
        background: rgba(43, 17, 219, 0.05);
        border-left: 2px solid rgba(43, 17, 219, 0.3);
    }
    .sidebar-nested-sublist.collapsed {
        max-height: 0;
        opacity: 0;
        overflow: hidden;
    }
    .sidebar-nested-sublist li { padding: 0; border: none; }
    .sidebar-nested-sublist a {
        color: #5a6b7d;
        font-size: 11px;
        padding: 7px 10px 7px 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.25s ease;
        border-radius: 4px;
        margin: 0;
        min-height: 28px;
    }
    .sidebar-nested-sublist a:hover {
        color: #2B11DB;
        background: rgba(43, 17, 219, 0.12);
        padding-left: 12px;
        transform: none;
    }

    /* ── Sub-toggle button ── */
    .sidebar-list li.has-sub { position: relative; }
    .has-sub > a { padding-right: 40px; }
    .sub-toggle {
        position: absolute;
        right: 8px;
        top: 12px;
        background: transparent;
        border: 2px solid #d1d5db;
        color: #2B11DB;
        cursor: pointer;
        padding: 4px;
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 0;
        z-index: 10;
    }
    .sub-toggle:hover {
        background: rgba(43, 17, 219, 0.1);
        border-color: #2B11DB;
        transform: scale(1.1);
    }
    .sub-toggle:active { transform: scale(0.95); }
    .sub-toggle:focus { outline: none; }
    .sub-toggle .bi {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 14px;
        display: inline-flex;
    }
    .sub-toggle[aria-expanded="true"] .bi { transform: rotate(180deg); }

    /* ── Sidebar Close Button ── */
    .sidebar-close {
        background: transparent;
        border: none;
        color: #9ca3af;
        font-weight: 700;
        cursor: pointer;
        position: static;
        font-size: 16px;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
        flex-shrink: 0;
    }
    .sidebar-close:hover { color: #374151; }

    /* ── Sub-item icons ── */
    .sidebar-sublist a > i.bi {
        font-size: 14px;
        color: #2B11DB;
        opacity: 0.6;
        flex-shrink: 0;
        width: 18px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .sidebar-sublist a:hover > i.bi { opacity: 1; }
    .sidebar-nested-sublist a > i.bi {
        font-size: 11px;
        color: #2B11DB;
        opacity: 0.5;
        flex-shrink: 0;
        width: 14px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .sidebar-nested-sublist a:hover > i.bi { opacity: 1; }

    /* ================================================================
       MINI SIDEBAR (always-visible icon bar)
       ================================================================ */
    .mini-sidebar {
        position: fixed;
        left: 0;
        top: calc(14px + 50px + 14px + 52px);
        bottom: 0;
        width: 80px;
        background: linear-gradient(180deg, #2B11DB 0%, #1a0a7f 100%);
        box-shadow: 2px 0 16px rgba(0,0,0,0.2);
        z-index: 65;
        padding: 24px 12px;
        overflow-y: auto;
        overflow-x: hidden;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.1) transparent;
    }
    .mini-sidebar::-webkit-scrollbar { width: 6px; }
    .mini-sidebar::-webkit-scrollbar-track { background: transparent; }
    .mini-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    .mini-sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }

    .mini-sidebar.expanded {
        width: 280px;
        overflow-y: auto;
        padding: 24px 16px;
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,0.1) transparent;
        align-items: stretch;
    }
    .mini-sidebar.expanded::-webkit-scrollbar { width: 6px; }
    .mini-sidebar.expanded::-webkit-scrollbar-track { background: transparent; }
    .mini-sidebar.expanded::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 3px; }
    .mini-sidebar.expanded::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }

    .mini-sidebar.active {
        display: flex !important;
        flex-direction: column;
        align-items: center;
    }
    .mini-sidebar.active.expanded { align-items: stretch; }
    
    /* Ensure mini-sidebar is visible by default (treat as always active for desktop) */
    @media (min-width: 769px) {
        .mini-sidebar {
            display: flex !important;
        }
    }

    /* ── Mini Sidebar Icon ── */
    .mini-sidebar-icon {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #00FFE0;
        font-size: 24px;
        cursor: pointer;
        position: relative;
        border-radius: 8px;
        margin-bottom: 16px;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                    justify-content 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                    padding 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                    background 0.3s ease,
                    transform 0.2s ease;
        gap: 12px;
        padding: 0;
        flex-shrink: 0;
        min-width: 56px;
    }
    .mini-sidebar-icon .label {
        display: none;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
        flex: 1;
        text-align: left;
        opacity: 0;
        transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1) 0.1s;
    }
    .mini-sidebar.expanded .mini-sidebar-icon {
        width: 100%;
        justify-content: flex-start;
        padding: 14px;
        min-width: auto;
        margin-bottom: 12px;
        border-radius: 8px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(0, 215, 179, 0.3);
    }
    .mini-sidebar.expanded .mini-sidebar-icon:hover {
        background: rgba(255,255,255,0.22);
        border-color: rgba(0, 215, 179, 0.6);
        transform: translateX(4px);
    }
    .mini-sidebar.expanded .mini-sidebar-icon .label {
        display: block;
        opacity: 1;
        color: #ffffff;
        font-weight: 500;
        font-size: 14px;
    }
    .mini-sidebar-icon:hover {
        background: rgba(0, 215, 179, 0.2);
        transform: scale(1.08);
    }
    .mini-sidebar.expanded .mini-sidebar-icon:hover {
        transform: translateX(6px);
        background: rgba(0, 215, 179, 0.2);
    }
    .mini-sidebar-icon.active-icon {
        background: #00D7B3;
        color: #2B11DB;
        font-weight: 600;
    }
    .mini-sidebar-icon.active-icon .label {
        color: #2B11DB;
        font-weight: 600;
    }
    
    /* Also highlight active-parent class (used for category highlighting) */
    .mini-sidebar-icon.active-parent {
        background: #00D7B3;
        color: #2B11DB;
        font-weight: 600;
    }
    .mini-sidebar-icon.active-parent .label {
        color: #2B11DB;
        font-weight: 600;
    }
    .mini-sidebar.expanded .mini-sidebar-icon.active-parent {
        background: rgba(0, 215, 179, 0.25);
        color: #00FFE0;
        border: 1.5px solid #00D7B3;
        box-shadow: 0 0 12px rgba(0, 215, 179, 0.2);
    }
    .mini-sidebar.expanded .mini-sidebar-icon.active-parent .label {
        color: #00FFE0;
        font-weight: 600;
    }

    /* ── Sub-indicator dot ── */
    .mini-sidebar-icon .sub-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background: rgba(0, 215, 179, 0.9);
        color: #ffffff;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
        opacity: 0.9;
        transition: background 0.15s ease, color 0.15s ease, transform 0.2s ease;
        z-index: 999;
        cursor: pointer;
        pointer-events: auto;
        border: 1px solid #2B11DB;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .mini-sidebar-icon:hover .sub-indicator {
        opacity: 1;
        background: #00D7B3;
        color: #2B11DB;
        transform: scale(1.15);
    }
    .mini-sidebar-icon .sub-indicator:active { transform: translateY(0); }
    .mini-sidebar.expanded .mini-sidebar-icon .sub-indicator {
        position: static;
        background: #00D7B3;
        color: #2B11DB;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        margin-left: auto;
        opacity: 0.9;
        border: 1px solid #2B11DB;
        cursor: pointer;
        pointer-events: auto;
        z-index: 100;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    /* ── Mini Sidebar Menu Bar ── */
    #miniSidebarMenuBar {
        justify-content: center;
        width: 56px;
        height: 56px;
        margin-bottom: 8px;
        margin-top: 0;
        flex-shrink: 0;
    }
    .mini-sidebar.expanded #miniSidebarMenuBar {
        justify-content: flex-start;
        width: 100%;
        height: auto;
        padding: 14px;
        margin-bottom: 8px;
        background: rgba(0, 215, 179, 0.15);
        border: 1px solid rgba(0, 215, 179, 0.3);
        border-radius: 8px;
    }
    .mini-sidebar.expanded .browse-label { display: inline-block !important; }

    /* ── Mini Sidebar Toggle Button ── */
    .mini-sidebar-toggle {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 215, 179, 0.2);
        border: 1px solid rgba(0, 215, 179, 0.4);
        color: #00D7B3;
        cursor: pointer;
        border-radius: 8px;
        font-size: 20px;
        margin-top: auto;
        transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                    padding 0.5s cubic-bezier(0.4, 0, 0.2, 1),
                    background 0.3s ease,
                    transform 0.2s ease,
                    border-color 0.3s ease;
        flex-shrink: 0;
        z-index: 100;
    }
    .mini-sidebar-toggle:hover {
        background: rgba(0, 215, 179, 0.3);
        border-color: rgba(0, 215, 179, 0.6);
        transform: scale(1.08);
    }
    .mini-sidebar-toggle:active { transform: scale(0.95); }
    .mini-sidebar-toggle i {
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-block;
    }
    .mini-sidebar.expanded .mini-sidebar-toggle i { transform: rotate(180deg); }
    .mini-sidebar.expanded .mini-sidebar-toggle {
        width: 100%;
        padding: 14px;
        min-width: auto;
        margin-bottom: 12px;
        background: rgba(0, 215, 179, 0.15);
        border: 1px solid rgba(0, 215, 179, 0.3);
    }

    /* ── Adjust main content for mini sidebar (footer stays fixed) ── */
    section,
    .page-content,
    .main-content,
    .category-container {
        margin-left: 0px;
        transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .mini-sidebar.expanded ~ section,
    .mini-sidebar.expanded ~ .page-content,
    .mini-sidebar.expanded ~ .main-content,
    .mini-sidebar.expanded ~ .category-container {
        margin-left: 280px;
    }
    
    /* Footer remains at the bottom and scrolls with content */
    footer {
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 40px !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        width: 100% !important;
        position: relative !important;
        bottom: auto !important;
        left: auto !important;
        right: auto !important;
        z-index: 50 !important;
        transition: none !important;
        box-sizing: border-box !important;
    }
    /* Remove body padding since footer is no longer fixed */
    body {
        padding-bottom: 0 !important;
    }

    @media (max-width: 768px) {
        section, footer, .page-content, .main-content, .category-container {
            margin-left: 0 !important;
        }
        .mini-sidebar { display: none !important; }
        .mini-sidebar.mobile-visible { display: flex !important; }
    }

    /* ── Sidebar overlay expanded state ── */
    .sidebar-overlay.expanded { width: 380px; }
    .overlay-backdrop.expanded { display: none !important; }

    /* ── Responsive sidebar for 768px and below ── */
    @media (max-width: 768px) {
        .mini-sidebar {
            top: calc(14px + 36px + 14px + 40px);
            width: 56px !important;
            transform: translateX(-100%);
            transition: transform 0.3s ease, width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .mini-sidebar.mobile-visible { transform: translateX(0); }
        .mini-sidebar.expanded { width: 240px !important; }
        .browse-toggle { display: inline-flex !important; }
        .browse-toggle .browse-text { display: inline !important; }
        .main-content, .category-container { margin-left: 0 !important; }
        .mobile-sidebar-fab { display: flex !important; }

        /* Sidebar list (mobile tweaks) */
        .sidebar-overlay {
            top: calc(14px + 36px + 14px + 40px);
            width: 380px;
            max-width: 90%;
            padding: 12px 8px;
        }
        .sidebar-list { padding: 8px 8px 0 8px; }
        .sidebar-list a {
            padding: 10px 10px;
            font-size: 13px;
            min-height: 36px;
        }
        .sidebar-list a:hover { padding-left: 16px; }
        .sidebar-sublist a {
            font-size: 12px;
            padding: 8px 8px 8px 8px;
            min-height: 32px;
        }
        .sidebar-sublist a:hover { padding-left: 12px; }
        .sub-toggle { top: 8px; }

        .sidebar-nested-sublist a:active {
            background: rgba(43, 17, 219, 0.14);
            color: #2B11DB;
            padding-left: 44px;
        }
    }

    /* ================================================================
       MOBILE FAB BUTTON
       ================================================================ */
    .mobile-sidebar-fab {
        display: none;
        position: fixed;
        left: 0;
        top: 50%;
        transform: translateY(-50%) translateX(0);
        z-index: 70;
        width: 16px;
        height: 36px;
        background: #2B11DB;
        color: #fff;
        border: none;
        border-radius: 0 6px 6px 0;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        box-shadow: 2px 0 8px rgba(0,0,0,0.25);
        transition: transform 0.3s ease, background 0.2s;
    }
    .mobile-sidebar-fab:hover { background: #1a0aa8; }
    .mobile-sidebar-fab.open { transform: translateY(-50%) translateX(56px); }
    .mobile-sidebar-fab.open.wide { transform: translateY(-50%) translateX(240px); }

    /* ================================================================
       MINI POPOVER (subcategory flyout)
       ================================================================ */
    .mini-popover {
        position: fixed;
        top: -9999px;
        left: -9999px;
        width: 380px;
        max-width: calc(100vw - 40px);
        background: linear-gradient(135deg, #1E88E5 0%, #00BCD4 100%);
        color: #fff;
        border-radius: 16px;
        box-shadow: 0 16px 40px rgba(30,136,229,0.3), 0 2px 8px rgba(0,0,0,0.2);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-8px) scale(0.95);
        transition: opacity 180ms cubic-bezier(0.34,1.56,0.64,1),
                    transform 180ms cubic-bezier(0.34,1.56,0.64,1),
                    visibility 180ms ease;
        z-index: 1300;
        display: flex;
        flex-direction: column;
        height: auto;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.15);
    }
    .mini-popover.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0) scale(1);
    }
    .mini-popover::before {
        content: '';
        position: absolute;
        left: -10px;
        top: calc(26px + var(--arrow-offset, 0px));
        width: 0; height: 0;
        border-top: 10px solid transparent;
        border-bottom: 10px solid transparent;
        border-right: 10px solid #1976D2;
        filter: drop-shadow(-2px 2px 2px rgba(0,0,0,0.12));
    }
    .mini-popover-header {
        background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
        color: #ffffff;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 16px 20px;
        font-weight: 700;
        font-size: 16px;
        letter-spacing: 0.4px;
        line-height: 1.3;
    }
    .mini-popover-title { color: #ffffff; }
    .mini-popover-body {
        padding: 14px 16px 18px 16px;
        overflow: visible;
        flex: 1;
        background: linear-gradient(180deg, rgba(255,255,255,0.04) 0%, transparent 100%);
    }
    .mini-popover-list {
        list-style: none;
        margin: 0;
        padding: 0;
        position: relative;
    }
    .mini-popover-list::before {
        content: '';
        position: absolute;
        left: 24px;
        top: 6px;
        bottom: 6px;
        width: 2px;
        background: rgba(255,255,255,0.35);
        border-radius: 2px;
        display: none;
    }
    .mini-popover-item {
        position: relative;
        padding-left: 0;
        margin: 3px 0;
        display: flex;
        align-items: stretch;
        min-height: auto;
    }
    .mini-popover-item .square {
        position: absolute;
        left: 16px;
        top: 0;
        bottom: 0;
        margin: auto;
        width: 14px; height: 14px;
        border-radius: 3px;
        background: #7aa7ff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.18), inset 0 -1px 0 rgba(0,0,0,0.08);
        flex-shrink: 0;
        pointer-events: none;
        display: none;
    }
    .mini-popover-item a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 600;
        display: block;
        padding: 12px 14px;
        border-radius: 8px;
        transition: all 160ms cubic-bezier(0.34,1.56,0.64,1);
        width: 100%;
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        line-height: 1.4;
        font-size: 14px;
        background: rgba(255,255,255,0.06);
        border-left: 3px solid transparent;
    }
    .mini-popover-item a:hover {
        background: rgba(255,255,255,0.16);
        transform: translateX(4px);
        border-left-color: rgba(255,255,255,0.5);
    }

    /* ── Expandable popover items ── */
    .mini-popover-item.has-subitems {
        flex-wrap: wrap;
        padding-right: 36px;
    }
    .popover-expand-btn {
        position: absolute;
        right: 8px;
        top: 0; bottom: 0;
        height: 32px; width: 32px;
        margin: auto;
        background: rgba(255,255,255,0.1);
        border: none;
        color: #ffffff;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 160ms cubic-bezier(0.34,1.56,0.64,1);
        flex-shrink: 0;
        border-radius: 8px;
    }
    .popover-expand-btn:hover { background: rgba(255,255,255,0.22); transform: scale(1.08); }
    .popover-expand-btn:active { background: rgba(255,255,255,0.3); transform: scale(0.95); }
    .popover-expand-btn .bi {
        font-size: 18px;
        transition: transform 200ms cubic-bezier(0.34,1.56,0.64,1);
    }
    .popover-expand-btn[aria-expanded="true"] .bi { transform: rotate(90deg); }

    .popover-subitems {
        width: 100%;
        margin-top: 8px;
        max-height: none;
        overflow: visible;
        transition: opacity 250ms ease;
        opacity: 1;
        padding-left: 0;
    }
    .popover-subitems.collapsed { display: none; }

    .popover-subitem {
        color: rgba(255,255,255,0.85) !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        padding: 6px 10px 6px 28px !important;
        display: block !important;
        text-decoration: none !important;
        border-radius: 6px !important;
        transition: all 120ms ease !important;
        position: relative;
    }
    .popover-subitem::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #ffffff;
        opacity: 0.6;
    }
    .popover-subitem:hover {
        background: rgba(255,255,255,0.12) !important;
        transform: translateX(2px) !important;
        color: #ffffff !important;
    }
    
    /* ── Active Popover Items ── */
    .mini-popover-item a.active-popover-item {
        background: rgba(0, 215, 179, 0.3);
        color: #00FFE0;
        font-weight: 600;
        border-left: 3px solid #00D7B3;
        padding-left: 17px !important;
    }
    .mini-popover-item a.active-popover-item:hover {
        background: rgba(0, 215, 179, 0.4);
    }
    .popover-subitem.active-popover-subitem {
        background: rgba(0, 215, 179, 0.25) !important;
        color: #00FFE0 !important;
        font-weight: 600 !important;
        border-left: 2px solid #00D7B3 !important;
        padding-left: 20px !important;
    }
    .popover-subitem.active-popover-subitem:hover {
        background: rgba(0, 215, 179, 0.35) !important;
    }

    /* ── Mini Popover — Mobile overrides ── */
    @media (max-width: 768px) {
        .mini-popover {
            border-radius: 0 12px 12px 0 !important;
            box-shadow: 4px 8px 24px rgba(0,0,0,0.28) !important;
            overflow: hidden !important;
        }
        .mini-popover::before { display: none !important; }
        .mini-popover-header {
            border-radius: 0 !important;
            padding: 10px 14px !important;
            font-size: 13px !important;
            letter-spacing: 0.5px !important;
        }
        .mini-popover-body { padding: 6px 8px 8px 8px !important; }
        .mini-popover-list { padding: 0 !important; }
        .mini-popover-list::before { display: none !important; }
        .mini-popover-item {
            margin: 2px 0 !important;
            min-height: auto !important;
            padding-left: 4px !important;
            align-items: center !important;
        }
        .mini-popover-item .square {
            width: 6px !important;
            height: 6px !important;
            min-width: 6px !important;
            border-radius: 2px !important;
        }
        .mini-popover-item a {
            font-size: 12px !important;
            padding: 8px 10px !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            font-weight: 600 !important;
        }
        .mini-popover-item a:active { background: rgba(255,255,255,0.18) !important; }
        .mini-popover-item.has-subitems { padding-right: 30px !important; }
        .popover-expand-btn { height: 28px !important; width: 28px !important; right: 4px !important; }
        .popover-expand-btn .bi { font-size: 14px !important; }
        .popover-subitem {
            padding: 5px 10px 5px 18px !important;
            font-size: 11px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
    }
</style>

<!-- ============================================================
     SIDEBAR HTML
     ============================================================ -->

<!-- Overlay Backdrop -->
<div class="overlay-backdrop" id="overlayBackdrop"></div>

<!-- Sidebar Navigation (slide-out panel) -->
<aside id="sidebar" class="sidebar-overlay" aria-hidden="true">
    <div style="padding: 14px 20px; background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); display: flex; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 8px; color: white; flex: 1;">
            <i class="bi bi-list" style="font-size: 20px; font-weight: 700;"></i>
            <span style="font-size: 14px; font-weight: 700; letter-spacing: 0.5px;">BROWSE</span>
        </div>
        <button id="closeSidebar" style="background: transparent; border: none; color: white; cursor: pointer; font-size: 24px; padding: 0; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; line-height: 1;">×</button>
    </div>
    <ul class="sidebar-list">
        <li class="has-sub<?php echo ($current_category === 'arc-welding-machine') ? ' active-parent' : ''; ?>">
            <a href="<?php echo $_sidebar_base; ?>arc-welding-machine/arc-welding-machine.php"<?php echo ($current_category === 'arc-welding-machine' && $current_subcategory === 'arc-welding-machine.php') ? ' class="active"' : ''; ?>><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
            <button class="sub-toggle" aria-controls="sub-arc-welding" aria-expanded="<?php echo ($current_category === 'arc-welding-machine') ? 'true' : 'false'; ?>"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-arc-welding" class="sidebar-sublist<?php echo ($current_category === 'arc-welding-machine') ? '' : ' collapsed'; ?>">
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/mig-welding-machine.php"<?php echo ($current_category === 'arc-welding-machine' && $current_page === 'mig-welding-machine.php') ? ' class="active-subcategory"' : ''; ?>><i class="bi bi-lightning-charge"></i>MIG Welding Machine</a></li>
                <li class="has-nested-sub">
                    <a href="<?php echo $_sidebar_base; ?>arc-welding-machine/accessories-and-consumables.php"<?php echo ($current_category === 'arc-welding-machine' && ($current_page === 'accessories-and-consumables.php' || $current_subcategory === 'accessories-and-consumables')) ? ' class="active-subcategory"' : ''; ?>><i class="bi bi-bag2"></i>Accessories and Consumables</a>
                    <button class="nested-toggle" aria-controls="nested-accessories-consumables" aria-expanded="<?php echo ($current_category === 'arc-welding-machine' && ($current_page === 'welding-torch-gun.php' || $current_page === 'torch-consumables.php' || $current_page === 'accessories.php' || $current_subcategory === 'accessories-and-consumables')) ? 'true' : 'false'; ?>"><i class="bi bi-chevron-right"></i></button>
                    <ul id="nested-accessories-consumables" class="sidebar-nested-sublist<?php echo ($current_category === 'arc-welding-machine' && ($current_page === 'welding-torch-gun.php' || $current_page === 'torch-consumables.php' || $current_page === 'accessories.php')) ? '' : ' collapsed'; ?>">
                        <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/welding-torch-gun.php"<?php echo ($current_category === 'arc-welding-machine' && $current_page === 'welding-torch-gun.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>Welding Torch / Gun</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/torch-consumables.php"<?php echo ($current_category === 'arc-welding-machine' && $current_page === 'torch-consumables.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>Torch Consumables</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/accessories.php"<?php echo ($current_category === 'arc-welding-machine' && $current_page === 'accessories.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>Accessories</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/co1-mag-welding-machine.php"><i class="bi bi-cloud-fill"></i>CO2/MAG Welding Machine</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/stud-welding-machine.php"><i class="bi bi-record-circle"></i>STUD Welding Machine</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/tig-welding-machine.php"><i class="bi bi-activity"></i>TIG Welding Machine</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-machine/plasma-cutting-machine.php"><i class="bi bi-fire"></i>Plasma Cutting Machine</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
            <button class="sub-toggle" aria-controls="sub-arc-robots" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-robots/g3-controller-series.php"><i class="bi bi-cpu"></i>G3 Controller Series</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-robots/g4-controller-series.php"><i class="bi bi-cpu-fill"></i>G4 Controller Series</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-robots/featured-products-and-solution.php"><i class="bi bi-stars"></i>Featured Products and Solutions</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>arc-welding-robots/robot-system-peripherals.php"><i class="bi bi-puzzle"></i>Robot System Peripherals</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
            <button class="sub-toggle" aria-controls="sub-batteries" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-batteries" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>batteries/maintenance-free.php"><i class="bi bi-battery-full"></i>Maintenance Free</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>batteries/low-maintenance.php"><i class="bi bi-battery-half"></i>Low Maintenance</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>batteries/special-batteries.php"><i class="bi bi-battery-charging"></i>Special Batteries</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
            <button class="sub-toggle" aria-controls="sub-drilling-lifting" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/lifting.php"><i class="bi bi-arrow-up-circle-fill"></i>Lifting</a></li>
                <li class="has-nested-sub">
                    <a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/magnetic-drill.php"<?php echo ($current_category === 'drilling-and-lifting' && ($current_page === 'magnetic-drill.php' || $current_subcategory === 'magnetic-drill')) ? ' class="active-subcategory"' : ''; ?>><i class="bi bi-tools"></i>Magnetic Drill</a>
                    <button class="nested-toggle" aria-controls="nested-magnetic-drill" aria-expanded="<?php echo ($current_category === 'drilling-and-lifting' && ($current_page === 'b-line-series.php' || $current_page === 'rl-e-line-series.php' || $current_page === 'rbx-line-series.php' || $current_page === 'sp-line-series.php' || $current_page === 'v-line-series.php')) ? 'true' : 'false'; ?>"><i class="bi bi-chevron-right"></i></button>
                    <ul id="nested-magnetic-drill" class="sidebar-nested-sublist<?php echo ($current_category === 'drilling-and-lifting' && ($current_page === 'b-line-series.php' || $current_page === 'rl-e-line-series.php' || $current_page === 'rbx-line-series.php' || $current_page === 'sp-line-series.php' || $current_page === 'v-line-series.php')) ? '' : ' collapsed'; ?>">
                        <li><a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/magnetic-drill/b-line-series.php"<?php echo ($current_category === 'drilling-and-lifting' && $current_page === 'b-line-series.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>B-Line Series</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/magnetic-drill/rl-e-line-series.php"<?php echo ($current_category === 'drilling-and-lifting' && $current_page === 'rl-e-line-series.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>RL-E Line Series</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/magnetic-drill/rbx-line-series.php"<?php echo ($current_category === 'drilling-and-lifting' && $current_page === 'rbx-line-series.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>RBX-Line Series</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/magnetic-drill/sp-line-series.php"<?php echo ($current_category === 'drilling-and-lifting' && $current_page === 'sp-line-series.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>SP-Line Series</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/magnetic-drill/v-line-series.php"<?php echo ($current_category === 'drilling-and-lifting' && $current_page === 'v-line-series.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-dash-lg"></i>V-Line Series</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $_sidebar_base; ?>drilling-and-lifting/cutters.php"><i class="bi bi-scissors"></i>Cutters</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
            <button class="sub-toggle" aria-controls="sub-gas-detectors" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>gas-detectors/single-gas-detector.php"><i class="bi bi-search"></i>Single Gas Detector</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>gas-detectors/multi-gas-detector.php"><i class="bi bi-grid"></i>Multi Gas Detector</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>gas-detectors/portable-gas-detectors.php"><i class="bi bi-bag"></i>Portable Gas Detectors</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>gas-detectors/docking-data-management.php"><i class="bi bi-hdd-network"></i>Docking and Data Management</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>gas-detectors/calibration-gas-regulators.php"><i class="bi bi-sliders"></i>Calibration Gas and Regulators</a></li>
            </ul>
        </li>
        <li class="">
            <a href="<?php echo $_sidebar_base; ?>portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
            <button class="sub-toggle" aria-controls="sub-power-tool" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>power-tools/grinder.php"><i class="bi bi-circle-fill"></i>Grinder</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>power-tools/saw.php"><i class="bi bi-app"></i>Saw</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>power-tools/drill-and-wrench.php"><i class="bi bi-wrench"></i>Drill and Wrench</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>power-tools/rotary-and-demolition-hammer.php"><i class="bi bi-lightning-fill"></i>Rotary and Demolition Hammer</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>power-tools/accessories.php"><i class="bi bi-bag-fill"></i>Accessories</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
            <button class="sub-toggle" aria-controls="sub-protection-safety" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>protection/eye-protection.php"><i class="bi bi-eye-fill"></i>Eye Protection</a></li>
                <li class="has-nested-sub">
                    <a href="<?php echo $_sidebar_base; ?>protection/hand-protection.php"<?php echo ($current_category === 'protection' && ($current_page === 'hand-protection.php' || $current_subcategory === 'hand-protection')) ? ' class="active-subcategory"' : ''; ?>><i class="bi bi-hand-index"></i>Hand Protection</a>
                    <button class="nested-toggle" aria-controls="nested-hand-protection" aria-expanded="<?php echo ($current_category === 'protection' && ($current_page === 'working-gloves.php' || $current_page === 'chemical-liquid-protection-gloves.php' || $current_page === 'disposable-gloves.php' || $current_page === 'welding-gloves.php')) ? 'true' : 'false'; ?>"><i class="bi bi-chevron-right"></i></button>
                    <ul id="nested-hand-protection" class="sidebar-nested-sublist<?php echo ($current_category === 'protection' && ($current_page === 'working-gloves.php' || $current_page === 'chemical-liquid-protection-gloves.php' || $current_page === 'disposable-gloves.php' || $current_page === 'welding-gloves.php')) ? '' : ' collapsed'; ?>">
                        <li><a href="<?php echo $_sidebar_base; ?>protection/working-gloves.php"<?php echo ($current_category === 'protection' && $current_page === 'working-gloves.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-hand-index"></i>Working Gloves</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>protection/chemical-liquid-protection-gloves.php"<?php echo ($current_category === 'protection' && $current_page === 'chemical-liquid-protection-gloves.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-shield-fill"></i>Chemical and Liquid Protection Gloves</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>protection/disposable-gloves.php"<?php echo ($current_category === 'protection' && $current_page === 'disposable-gloves.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-hand-thumbs-up"></i>Disposable Gloves</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>protection/welding-gloves.php"<?php echo ($current_category === 'protection' && $current_page === 'welding-gloves.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-hand-index-thumb"></i>Welding Gloves</a></li>
                    </ul>
                </li>
                <li><a href="<?php echo $_sidebar_base; ?>protection/hearing-respiratory-protection.php"><i class="bi bi-headphones"></i>Hearing &amp; Respiratory Protection</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>protection/welding-head-and-face-protection.php"><i class="bi bi-person-bounding-box"></i>Welding Head and Face Protection</a></li>
                <li class="has-nested-sub">
                    <a href="<?php echo $_sidebar_base; ?>protection/body-protection.php"<?php echo ($current_category === 'protection' && ($current_page === 'body-protection.php' || $current_subcategory === 'body-protection')) ? ' class="active-subcategory"' : ''; ?>><i class="bi bi-person-fill"></i>Body Protection</a>
                    <button class="nested-toggle" aria-controls="nested-body-protection" aria-expanded="<?php echo ($current_category === 'protection' && ($current_page === 'chemical-flame-retardant.php' || $current_page === 'liquid-spray-splash.php' || $current_page === 'particulate-low-hazard.php')) ? 'true' : 'false'; ?>"><i class="bi bi-chevron-right"></i></button>
                    <ul id="nested-body-protection" class="sidebar-nested-sublist<?php echo ($current_category === 'protection' && ($current_page === 'chemical-flame-retardant.php' || $current_page === 'liquid-spray-splash.php' || $current_page === 'particulate-low-hazard.php')) ? '' : ' collapsed'; ?>">
                        <li><a href="<?php echo $_sidebar_base; ?>protection/chemical-flame-retardant.php"<?php echo ($current_category === 'protection' && $current_page === 'chemical-flame-retardant.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-fire"></i>Chemical and Flame Retardant</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>protection/liquid-spray-splash.php"<?php echo ($current_category === 'protection' && $current_page === 'liquid-spray-splash.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-droplet-fill"></i>Liquid Spray and Splash</a></li>
                        <li><a href="<?php echo $_sidebar_base; ?>protection/particulate-low-hazard.php"<?php echo ($current_category === 'protection' && $current_page === 'particulate-low-hazard.php') ? ' class="active-nested"' : ''; ?>><i class="bi bi-wind"></i>Particulate and Low Hazard</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
            <button class="sub-toggle" aria-controls="sub-welding-accessories" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-welding-accessories" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>welding-accessories/welding-electrode-oven.php"><i class="bi bi-thermometer"></i>Welding Electrode Oven</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>welding-accessories/non-destructive-crack-detection.php"><i class="bi bi-search"></i>Non-Destructive Crack Detection</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>welding-accessories/gas-saving-regulator.php"><i class="bi bi-droplet"></i>Gas Saving Regulator</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>welding-accessories/gas-cutting-equipment.php"><i class="bi bi-scissors"></i>Gas Cutting Equipment</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>welding-accessories/industrial-markers.php"><i class="bi bi-pen-fill"></i>Industrial Markers</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>welding-accessories/measuring-gauge.php"><i class="bi bi-rulers"></i>Measuring Gauge</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>welding-accessories/others.php"><i class="bi bi-three-dots"></i>Others</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="<?php echo $_sidebar_base; ?>welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
            <button class="sub-toggle" aria-controls="sub-welding-consumables" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                <li><a href="<?php echo $_sidebar_base; ?>welding-consumables/kobelco.php"><i class="bi bi-award"></i>Kobelco</a></li>
                <li><a href="<?php echo $_sidebar_base; ?>welding-consumables/metrode.php"><i class="bi bi-award-fill"></i>Metrode</a></li>
            </ul>
        </li>
    </ul>
</aside>

<!-- Mini Sidebar (Icon Bar) -->
<div class="mini-sidebar active" id="miniSidebar">
    <div id="miniSidebarMenuBar" style="background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); border-radius: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
        <i class="bi bi-list" style="font-size: 18px; font-weight: 700; color: white;"></i>
        <span style="font-size: 13px; font-weight: 700; color: white; letter-spacing: 0.5px; display: none;" class="browse-label">BROWSE CATEGORIES</span>
    </div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>arc-welding-machine/arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>arc-welding-robots/arc-welding-robot.php" title="Arc Welding Robots"><i class="bi bi-robot"></i><span class="label">Arc Welding Robots</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>batteries/batteries.php" title="Batteries"><i class="bi bi-lightning-fill"></i><span class="label">Batteries</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>drilling-and-lifting/drilling-and-lifting.php" title="Drilling and Lifting"><i class="bi bi-hammer"></i><span class="label">Drilling and Lifting</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>gas-detectors/gas-detectors.php" title="Gas Detectors"><i class="bi bi-bullseye"></i><span class="label">Gas Detectors</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>portable-ventilators/portable-ventilators.php" title="Portable Ventilators"><i class="bi bi-fan"></i><span class="label">Portable Ventilators</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>power-tools/power-tools.php" title="Power Tools"><i class="bi bi-tools"></i><span class="label">Power Tools</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>protection/protection.php" title="Personal Protective Equipment"><i class="bi bi-shield-check"></i><span class="label">PPE</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>welding-accessories/welding-accessories.php" title="Welding Accessories"><i class="bi bi-gear"></i><span class="label">Welding Accessories</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="<?php echo $_sidebar_base; ?>welding-consumables/welding-consumables.php" title="Welding Consumables"><i class="bi bi-box"></i><span class="label">Welding Consumables</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <button class="mini-sidebar-toggle" id="expandSidebar" title="Toggle Sidebar"><i class="bi bi-chevron-right"></i></button>
</div>

<!-- Mobile FAB to show/hide mini sidebar -->
<button class="mobile-sidebar-fab" id="mobileSidebarFab"><i class="bi bi-chevron-right" id="mobileFabIcon"></i></button>

<!-- Floating popover for mini sidebar subcategories -->
<div id="miniPopover" class="mini-popover" aria-hidden="true">
    <div class="mini-popover-header">
        <div class="mini-popover-title"></div>
    </div>
    <div class="mini-popover-body">
        <ul class="mini-popover-list"></ul>
    </div>
</div>

<!-- ============================================================
     SIDEBAR JAVASCRIPT
     ============================================================ -->
<script>
    // ── Sidebar open / close (browse-toggle & close button) ──
    (function(){
        var browseToggle = document.getElementById('browseToggle');
        var sidebar      = document.getElementById('sidebar');
        var overlay      = document.getElementById('overlayBackdrop');
        var closeBtn     = document.getElementById('closeSidebar');

        function openSidebar(){
            if(!sidebar) return;
            sidebar.classList.add('active');
            if(overlay) overlay.classList.add('active');
            sidebar.setAttribute('aria-hidden','false');
        }
        function closeSidebar(){
            if(!sidebar) return;
            sidebar.classList.remove('active');
            if(overlay) overlay.classList.remove('active');
            sidebar.setAttribute('aria-hidden','true');
        }

        if(browseToggle) browseToggle.addEventListener('click', function(e){ e.preventDefault(); openSidebar(); });
        if(closeBtn)     closeBtn.addEventListener('click', closeSidebar);
        if(overlay)      overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeSidebar(); });
    })();

    // ── Sidebar sublist toggle with localStorage persistence ──
    (function(){
        var toggles = document.querySelectorAll('.sub-toggle');
        toggles.forEach(function(btn){
            var targetId   = btn.getAttribute('aria-controls');
            var list       = document.getElementById(targetId);
            if(!list) return;
            var storageKey = 'sidebar_sub_' + targetId;
            try {
                var stored = localStorage.getItem(storageKey);
                if(stored === 'true'){
                    btn.setAttribute('aria-expanded','true');
                    list.classList.remove('collapsed');
                } else {
                    btn.setAttribute('aria-expanded','false');
                    list.classList.add('collapsed');
                }
            } catch(e){
                btn.setAttribute('aria-expanded','false');
                list.classList.add('collapsed');
            }
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var expanded = btn.getAttribute('aria-expanded') === 'true';
                if(expanded){
                    btn.setAttribute('aria-expanded','false');
                    list.classList.add('collapsed');
                    try { localStorage.setItem(storageKey,'false'); } catch(ex){}
                } else {
                    btn.setAttribute('aria-expanded','true');
                    list.classList.remove('collapsed');
                    try { localStorage.setItem(storageKey,'true'); } catch(ex){}
                }
            });
        });
    })();

    // ── Nested sublist toggle with localStorage persistence ──
    (function(){
        var nestedToggles = document.querySelectorAll('.nested-toggle');
        nestedToggles.forEach(function(btn){
            var targetId   = btn.getAttribute('aria-controls');
            var list       = document.getElementById(targetId);
            if(!list) return;
            var storageKey = 'sidebar_nested_' + targetId;
            try {
                var stored = localStorage.getItem(storageKey);
                if(stored === 'true'){
                    btn.setAttribute('aria-expanded','true');
                    list.classList.remove('collapsed');
                } else {
                    btn.setAttribute('aria-expanded','false');
                    list.classList.add('collapsed');
                }
            } catch(e){
                btn.setAttribute('aria-expanded','false');
                list.classList.add('collapsed');
            }
            btn.addEventListener('click', function(e){
                e.preventDefault();
                var expanded = btn.getAttribute('aria-expanded') === 'true';
                if(expanded){
                    btn.setAttribute('aria-expanded','false');
                    list.classList.add('collapsed');
                    try { localStorage.setItem(storageKey,'false'); } catch(ex){}
                } else {
                    btn.setAttribute('aria-expanded','true');
                    list.classList.remove('collapsed');
                    try { localStorage.setItem(storageKey,'true'); } catch(ex){}
                }
            });
        });
    })();

    // ── Sidebar overlay backdrop close ──
    (function(){
        var overlayBackdrop = document.querySelector('.overlay-backdrop');
        var sidebar = document.getElementById('sidebar');
        if(overlayBackdrop){
            overlayBackdrop.addEventListener('click', function(){
                if(sidebar) sidebar.classList.remove('active');
                overlayBackdrop.classList.remove('active');
            });
        }

        // Sub-toggle popover (from main sidebar)
        var mainSidebarPopover  = document.getElementById('miniPopover');
        var popoverTitle        = mainSidebarPopover ? mainSidebarPopover.querySelector('.mini-popover-title') : null;
        var popoverList         = mainSidebarPopover ? mainSidebarPopover.querySelector('.mini-popover-list')  : null;
        var currentMainSidebarKey = null;

        function showMainSidebarPopover(toggle){
            if(!mainSidebarPopover || !popoverList || !popoverTitle) return;
            var sublistId = toggle.getAttribute('aria-controls');
            var sublist   = document.getElementById(sublistId);
            if(!sublist) return;

            var parentLi = toggle.closest('.has-sub');
            var mainLink = parentLi ? parentLi.querySelector(':scope > a') : null;
            var title    = mainLink ? mainLink.textContent.trim() : 'Items';

            var items = [];
            sublist.querySelectorAll('li > a').forEach(function(link){
                items.push({ text: link.textContent.trim(), href: link.getAttribute('href') || '#' });
            });

            popoverTitle.textContent = title;
            popoverList.innerHTML    = '';
            items.forEach(function(item){
                var li = document.createElement('li');
                li.className = 'mini-popover-item';
                li.innerHTML = '<span class="square"></span><a href="' + item.href + '">' + item.text + '</a>';
                popoverList.appendChild(li);
            });

            setTimeout(function(){
                mainSidebarPopover.style.left = '-9999px';
                mainSidebarPopover.style.top  = '-9999px';
                mainSidebarPopover.classList.add('show');

                var toggleRect    = toggle.getBoundingClientRect();
                var ph            = mainSidebarPopover.offsetHeight;
                var pw            = mainSidebarPopover.offsetWidth;
                var toggleCenterY = toggleRect.top + toggleRect.height / 2;
                var left          = Math.round(toggleRect.right + 14);
                var top           = Math.round(toggleCenterY - ph / 2);

                if(left + pw + 12 > window.innerWidth) left = Math.round(toggleRect.left - pw - 14);
                var minTop = 112, maxTop = window.innerHeight - ph - 12;
                if(top < minTop) top = minTop;
                if(top > maxTop) top = maxTop;

                mainSidebarPopover.style.setProperty('--arrow-offset', (toggleCenterY - top - 26) + 'px');
                mainSidebarPopover.style.left = left + 'px';
                mainSidebarPopover.style.top  = top  + 'px';
                mainSidebarPopover.setAttribute('aria-hidden', 'false');
                currentMainSidebarKey = sublistId;
            }, 5);
        }

        function hideMainSidebarPopover(){
            if(!mainSidebarPopover) return;
            mainSidebarPopover.classList.remove('show');
            mainSidebarPopover.setAttribute('aria-hidden','true');
            currentMainSidebarKey = null;
        }

        document.querySelectorAll('.sub-toggle').forEach(function(toggle){
            toggle.addEventListener('click', function(e){
                e.preventDefault(); e.stopPropagation();
                if(currentMainSidebarKey === toggle.getAttribute('aria-controls') && mainSidebarPopover.classList.contains('show')){
                    hideMainSidebarPopover();
                } else {
                    showMainSidebarPopover(toggle);
                }
            });
        });

        document.addEventListener('click', function(e){
            if(!mainSidebarPopover || !mainSidebarPopover.classList.contains('show')) return;
            if(e.target.closest('.mini-popover') || e.target.closest('.sub-toggle')) return;
            hideMainSidebarPopover();
        });
        document.addEventListener('keydown', function(e){ if(e.key === 'Escape') hideMainSidebarPopover(); });

        document.querySelectorAll('.nested-toggle').forEach(function(toggle){
            toggle.addEventListener('click', function(e){
                e.preventDefault(); e.stopPropagation();
                var nested = document.getElementById(toggle.getAttribute('aria-controls'));
                if(nested){
                    nested.classList.toggle('collapsed');
                    toggle.setAttribute('aria-expanded', nested.classList.contains('collapsed') ? 'false' : 'true');
                }
            });
        });
    })();

    // ── Active sidebar category highlighting ──
    setTimeout(function(){
        var currentPath = window.location.pathname.toLowerCase();
        var currentPathClean = currentPath.replace(/\/andison\//i, '').replace(/\.php$/, '');
        var sidebar = document.getElementById('sidebar');
        var miniSidebar = document.getElementById('miniSidebar');
        if(!sidebar) return;
        
        var pathParts = currentPath.split('/').filter(function(p){ return p && p !== 'andison-1'; });
        var categoryList = [
            'arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting',
            'gas-detectors','portable-ventilators','power-tools','protection',
            'welding-accessories','welding-consumables'
        ];
        
        // Find current category, subcategory, and nested level
        var currentCategory = null, currentSubcategory = null, currentNested = null;
        for(var i = 0; i < pathParts.length; i++){
            if(categoryList.indexOf(pathParts[i]) !== -1){ 
                currentCategory = pathParts[i]; 
                // Next part could be subcategory
                if(i + 1 < pathParts.length && pathParts[i + 1].indexOf('.php') === -1) {
                    currentSubcategory = pathParts[i + 1];
                    // Check for nested level
                    if(i + 2 < pathParts.length && pathParts[i + 2].indexOf('.php') === -1) {
                        currentNested = pathParts[i + 2];
                    }
                }
                break; 
            }
        }
        
        if(!currentCategory) return;
        
        // 1. Highlight and expand main sidebar category parent
        sidebar.querySelectorAll('.sidebar-list > li').forEach(function(li){
            var link = li.querySelector(':scope > a');
            if(link && link.getAttribute('href').toLowerCase().includes('/' + currentCategory + '/')) {
                li.classList.add('active-parent');
                // Auto-expand
                var toggle = li.querySelector('.sub-toggle');
                var sublist = li.querySelector('.sidebar-sublist');
                if(toggle && sublist) {
                    toggle.setAttribute('aria-expanded', 'true');
                    sublist.classList.remove('collapsed');
                }
            }
        });
        
        // 2. Highlight direct subcategory/main page links
        sidebar.querySelectorAll('.sidebar-sublist > li > a').forEach(function(link){
            var href = link.getAttribute('href').toLowerCase();
            var hrefClean = href.replace(/\/andison\//i, '').replace(/\.php$/, '');
            // Match if the entire path matches or if current path contains this href
            if(currentPathClean === hrefClean || currentPath.includes(href)) {
                link.classList.add('active-subcategory');
            }
        });
        
        // 3. Highlight nested subcategory links and auto-expand ALL parent lists
        sidebar.querySelectorAll('.sidebar-nested-sublist a').forEach(function(link){
            var href = link.getAttribute('href').toLowerCase();
            var hrefClean = href.replace(/\/andison\//i, '').replace(/\.php$/, '');
            
            if(currentPathClean === hrefClean || currentPath.includes(href)) {
                link.classList.add('active-nested');
                
                // Get the nested list
                var nestedList = link.closest('.sidebar-nested-sublist');
                if(nestedList) {
                    // Expand nested list
                    nestedList.classList.remove('collapsed');
                    
                    // Expand nested toggle
                    var nestedToggle = nestedList.previousElementSibling;
                    if(nestedToggle && nestedToggle.classList.contains('nested-toggle')) {
                        nestedToggle.setAttribute('aria-expanded', 'true');
                    }
                    
                    // Mark parent subcategory as active
                    var hasNestedSubLi = nestedList.closest('.has-nested-sub');
                    if(hasNestedSubLi) {
                        var parentSublink = hasNestedSubLi.querySelector(':scope > a');
                        if(parentSublink) {
                            parentSublink.classList.add('active-subcategory');
                        }
                        
                        // Also ensure the parent sublist is visible
                        var parentSublist = hasNestedSubLi.closest('.sidebar-sublist');
                        if(parentSublist && parentSublist.classList.contains('collapsed')) {
                            parentSublist.classList.remove('collapsed');
                            var parentToggle = parentSublist.previousElementSibling;
                            if(parentToggle && parentToggle.classList.contains('sub-toggle')) {
                                parentToggle.setAttribute('aria-expanded', 'true');
                            }
                        }
                    }
                }
            }
        });
        
        // 4. Also highlight subcategory links that are parents of nested items
        sidebar.querySelectorAll('.sidebar-sublist > li.has-nested-sub > a').forEach(function(link){
            var href = link.getAttribute('href').toLowerCase();
            if(currentPathClean.indexOf(href.replace(/\/andison\//i, '').replace(/\.php$/, '')) === 0) {
                link.classList.add('active-subcategory');
            }
        });
        
        // 5. Highlight mini-sidebar icon
        if(miniSidebar) {
            miniSidebar.querySelectorAll('.mini-sidebar-icon').forEach(function(icon){
                var target = icon.getAttribute('data-target') || '';
                if(target.toLowerCase().includes('/' + currentCategory + '/')) {
                    icon.classList.add('active-parent');
                }
            });
        }
    }, 500);

    // ── Mini Sidebar & Browse Toggle ──
    (function(){
        var miniSidebar  = document.getElementById('miniSidebar');
        var mainSidebar  = document.getElementById('sidebar');
        var backdrop     = document.getElementById('overlayBackdrop');
        var expandBtn    = document.getElementById('expandSidebar');
        var browseToggle = document.getElementById('browseToggle');
        var miniIcons    = document.querySelectorAll('.mini-sidebar-icon');
        var miniPopover  = document.getElementById('miniPopover');
        var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;
        var popoverList  = miniPopover ? miniPopover.querySelector('.mini-popover-list')  : null;
        var currentPopoverKey = null;
        var lastIconCenterY = 0, lastIconRight = 0, lastIconTop = 0;

        // ── Visibility helper for browse toggle ──
        function updateBrowseToggleVisibility(){
            if(!browseToggle) return;
            if(window.innerWidth <= 1024) browseToggle.classList.add('active');
            else                          browseToggle.classList.remove('active');
        }
        if(browseToggle) updateBrowseToggleVisibility();
        window.addEventListener('resize', updateBrowseToggleVisibility);

        // ── Category helpers ──
        function getCategoryKeyFromTarget(dataTarget){
            if(!dataTarget) return null;
            var keys = ['arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting',
                        'gas-detectors','portable-ventilators','power-tools','protection',
                        'welding-accessories','welding-consumables'];
            for(var i = 0; i < keys.length; i++){
                if(dataTarget.indexOf('/'+keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'/') !== -1) return keys[i];
            }
            return null;
        }
        function getCategoryTitle(key){
            var map = {
                'arc-welding-machine':  'Arc Welding Machines',
                'arc-welding-robots':   'Arc Welding Robots',
                'batteries':            'Batteries',
                'drilling-and-lifting': 'Drilling and Lifting',
                'gas-detectors':        'Gas Detectors',
                'portable-ventilators': 'Portable Ventilators',
                'power-tools':          'Power Tools',
                'protection':           'Personal Protective Equipment',
                'welding-accessories':  'Welding Accessories',
                'welding-consumables':  'Welding Consumables'
            };
            return map[key] || 'Categories';
        }
        function getPopoverItems(key){
            var base = '<?php echo rtrim($_sidebar_base,"/"); ?>';
            var maps = {
                'arc-welding-robots': [
                    { label:'G3 Controller Series',              href: base+'/arc-welding-robots/g3-controller-series.php' },
                    { label:'G4 Controller Series',              href: base+'/arc-welding-robots/g4-controller-series.php' },
                    { label:'Featured Products and Solutions',   href: base+'/arc-welding-robots/featured-products-and-solution.php' },
                    { label:'Robot System Peripherals',          href: base+'/arc-welding-robots/robot-system-peripherals.php' }
                ],
                'arc-welding-machine': [
                    { label:'MIG Welding Machine',        href: base+'/arc-welding-machine/mig-welding-machine.php' },
                    { label:'Accessories and Consumables', href: base+'/arc-welding-machine/accessories-and-consumables.php', subitems:[
                        { label:'Welding Torch / Gun', href: base+'/arc-welding-machine/welding-torch-gun.php' },
                        { label:'Torch Consumables',   href: base+'/arc-welding-machine/torch-consumables.php' },
                        { label:'Accessories',         href: base+'/arc-welding-machine/accessories.php' }
                    ]},
                    { label:'CO2/MAG Welding Machine',    href: base+'/arc-welding-machine/co1-mag-welding-machine.php' },
                    { label:'STUD Welding Machine',       href: base+'/arc-welding-machine/stud-welding-machine.php' },
                    { label:'TIG Welding Machine',        href: base+'/arc-welding-machine/tig-welding-machine.php' },
                    { label:'Plasma Cutting Machine',     href: base+'/arc-welding-machine/plasma-cutting-machine.php' }
                ],
                'batteries': [
                    { label:'Maintenance Free',  href: base+'/batteries/maintenance-free.php' },
                    { label:'Low Maintenance',   href: base+'/batteries/low-maintenance.php' },
                    { label:'Special Batteries', href: base+'/batteries/special-batteries.php' }
                ],
                'drilling-and-lifting': [
                    { label:'Lifting',        href: base+'/drilling-and-lifting/lifting.php' },
                    { label:'Magnetic Drill', href: base+'/drilling-and-lifting/magnetic-drill.php', subitems:[
                        { label:'B-Line Series',     href: base+'/drilling-and-lifting/b-line-series.php' },
                        { label:'RL-E Line Series',  href: base+'/drilling-and-lifting/rl-e-line-series.php' },
                        { label:'RBX-Line Series',   href: base+'/drilling-and-lifting/rbx-line-series.php' },
                        { label:'SP-Line Series',    href: base+'/drilling-and-lifting/sp-line-series.php' },
                        { label:'V-Line Series',     href: base+'/drilling-and-lifting/v-line-series.php' }
                    ]},
                    { label:'Cutters', href: base+'/drilling-and-lifting/cutters.php' }
                ],
                'gas-detectors': [
                    { label:'Single Gas Detector',          href: base+'/gas-detectors/single-gas-detector.php' },
                    { label:'Multi Gas Detector',           href: base+'/gas-detectors/multi-gas-detector.php' },
                    { label:'Portable Gas Detectors',       href: base+'/gas-detectors/portable-gas-detectors.php' },
                    { label:'Docking and Data Management',  href: base+'/gas-detectors/docking-data-management.php' },
                    { label:'Calibration Gas and Regulators', href: base+'/gas-detectors/calibration-gas-regulators.php' }
                ],
                'portable-ventilators': [
                    { label:'Electric Driven',  href: base+'/portable-ventilators/electric-driven.php' },
                    { label:'Pneumatic Driven', href: base+'/portable-ventilators/pneumatic-driven.php' }
                ],
                'power-tools': [
                    { label:'Grinder',                          href: base+'/power-tools/grinder.php' },
                    { label:'Saw',                              href: base+'/power-tools/saw.php' },
                    { label:'Drill and Wrench',                 href: base+'/power-tools/drill-and-wrench.php' },
                    { label:'Rotary and Demolition Hammer',     href: base+'/power-tools/rotary-and-demolition-hammer.php' },
                    { label:'Accessories',                      href: base+'/power-tools/accessories.php' }
                ],
                'protection': [
                    { label:'Eye Protection',  href: base+'/protection/eye-protection.php' },
                    { label:'Hand Protection', href: base+'/protection/hand-protection.php', subitems:[
                        { label:'Welding Gloves',                         href: base+'/protection/welding-gloves.php' },
                        { label:'Working Gloves',                         href: base+'/protection/working-gloves.php' },
                        { label:'Chemical and Liquid Protection Gloves',  href: base+'/protection/chemical-liquid-protection-gloves.php' },
                        { label:'Disposable Gloves',                      href: base+'/protection/disposable-gloves.php' }
                    ]},
                    { label:'Hearing & Respiratory Protection',       href: base+'/protection/hearing-respiratory-protection.php' },
                    { label:'Welding Head and Face Protection',       href: base+'/protection/welding-head-and-face-protection.php' },
                    { label:'Body Protection', href: base+'/protection/body-protection.php', subitems:[
                        { label:'Particulate and Low Hazard',           href: base+'/protection/particulate-low-hazard.php' },
                        { label:'Liquid Spray and Splash',              href: base+'/protection/liquid-spray-splash.php' },
                        { label:'Chemical and Flame Retardant',         href: base+'/protection/chemical-flame-retardant.php' }
                    ]}
                ],
                'welding-accessories': [
                    { label:'Welding Electrode Oven',           href: base+'/welding-accessories/welding-electrode-oven.php' },
                    { label:'Non-Destructive Crack Detection',  href: base+'/welding-accessories/non-destructive-crack-detection.php' },
                    { label:'Gas Saving Regulator',             href: base+'/welding-accessories/gas-saving-regulator.php' },
                    { label:'Gas Cutting Equipment',            href: base+'/welding-accessories/gas-cutting-equipment.php' },
                    { label:'Industrial Markers',               href: base+'/welding-accessories/industrial-markers.php' },
                    { label:'Measuring Gauge',                  href: base+'/welding-accessories/measuring-gauge.php' },
                    { label:'Others',                           href: base+'/welding-accessories/others.php' }
                ],
                'welding-consumables': [
                    { label:'Kobelco', href: base+'/welding-consumables/kobelco.php' },
                    { label:'Metrode', href: base+'/welding-consumables/metrode.php' }
                ]
            };
            return maps[key] || [];
        }

        // ── Popover render ──
        function renderPopover(key){
            if(!miniPopover || !popoverList) return;
            
            // Get current page for highlighting
            var currentPath = window.location.pathname.toLowerCase();
            var currentPathClean = currentPath.replace(/\/andison\//i, '').replace(/\.php$/, '');
            
            popoverList.innerHTML = '';
            getPopoverItems(key).forEach(function(it){
                var li = document.createElement('li');
                li.className = 'mini-popover-item';
                
                // Check if this item is active
                var itemHrefClean = it.href.toLowerCase().replace(/\/andison\//i, '').replace(/\.php$/, '');
                var isActive = currentPathClean === itemHrefClean || currentPath.includes(it.href.toLowerCase());
                
                if(it.subitems && it.subitems.length > 0){
                    var activeClass = isActive ? ' active-popover-item' : '';
                    li.innerHTML = '<span class="square"></span><a href="'+it.href+'" class="'+activeClass+'">'+it.label+'</a><button class="popover-expand-btn" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>';
                    li.className += ' has-subitems';
                    var subContainer = document.createElement('div');
                    subContainer.className = 'popover-subitems collapsed';
                    
                    // Check if any subitems are active
                    var hasActiveSubitem = false;
                    subContainer.innerHTML = it.subitems.map(function(sub){
                        var subHrefClean = sub.href.toLowerCase().replace(/\/andison\//i, '').replace(/\.php$/, '');
                        var isActiveSub = currentPathClean === subHrefClean || currentPath.includes(sub.href.toLowerCase());
                        if(isActiveSub) hasActiveSubitem = true;
                        return '<a href="'+sub.href+'" class="popover-subitem' + (isActiveSub ? ' active-popover-subitem' : '') + '">'+sub.label+'</a>';
                    }).join('');
                    
                    li.appendChild(subContainer);
                    
                    // Auto-expand if has active subitem
                    if(hasActiveSubitem) {
                        subContainer.classList.remove('collapsed');
                        var btn = li.querySelector('.popover-expand-btn');
                        btn.setAttribute('aria-expanded', 'true');
                    }
                    
                    var btn = li.querySelector('.popover-expand-btn');
                    btn.addEventListener('click', function(e){
                        e.preventDefault(); e.stopPropagation();
                        var isExp = btn.getAttribute('aria-expanded') === 'true';
                        btn.setAttribute('aria-expanded', isExp ? 'false' : 'true');
                        subContainer.classList.toggle('collapsed', isExp);
                        setTimeout(adjustPopoverHeight, 10);
                    });
                } else {
                    var activeClass = isActive ? ' active-popover-item' : '';
                    li.innerHTML = '<span class="square"></span><a href="'+it.href+'" class="'+activeClass+'">'+it.label+'</a>';
                }
                popoverList.appendChild(li);
            });
            if(popoverTitle) popoverTitle.textContent = getCategoryTitle(key);
        }

        // ── Popover positioning ──
        function _applyPosition(finalHeight){
            var vh = window.innerHeight;
            var isMobile = window.innerWidth <= 768;
            if(isMobile){
                var popLeft  = Math.round(lastIconRight);
                var headerMin = 90, bottomPad = 8;
                var popTop   = Math.round(lastIconTop);
                var contentH = Math.min(finalHeight, vh - headerMin - bottomPad);
                if(popTop + contentH > vh - bottomPad) popTop = vh - contentH - bottomPad;
                if(popTop < headerMin) popTop = headerMin;
                miniPopover.style.left   = popLeft + 'px';
                miniPopover.style.right  = '0px';
                miniPopover.style.width  = 'auto';
                miniPopover.style.top    = popTop + 'px';
                miniPopover.style.height = contentH + 'px';
                miniPopover.style.setProperty('--arrow-offset', '-9999px');
            } else {
                var headerBottom = 140;
                var top = Math.round(lastIconCenterY - finalHeight / 2);
                if(top < headerBottom) top = headerBottom;
                if(top + finalHeight > vh - 8) top = vh - finalHeight - 8;
                if(top < headerBottom) top = headerBottom;
                miniPopover.style.right = '';
                miniPopover.style.width = '';
                var arrowOffset = Math.max(8, Math.min(finalHeight - 44, Math.round(lastIconCenterY - top - 26)));
                miniPopover.style.top    = top + 'px';
                miniPopover.style.height = finalHeight + 'px';
                miniPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');
            }
        }
        function adjustPopoverHeight(){
            if(!miniPopover) return;
            miniPopover.style.height = 'auto';
            _applyPosition(Math.min(miniPopover.offsetHeight, window.innerHeight * 0.88));
        }
        function positionPopoverForIcon(icon){
            if(!miniPopover || !icon) return;
            miniPopover.style.right = '';
            miniPopover.style.width = '';
            var rect = icon.getBoundingClientRect();
            lastIconCenterY = rect.top + rect.height / 2;
            lastIconRight   = rect.right;
            lastIconTop     = rect.top;
            if(window.innerWidth > 768){
                var pw   = miniPopover.offsetWidth;
                var left = Math.round(rect.right + 14);
                if(left + pw + 12 > window.innerWidth) left = Math.round(rect.left - pw - 14);
                miniPopover.style.left = left + 'px';
            }
            miniPopover.style.height = 'auto';
            miniPopover.style.top    = '-9999px';
            miniPopover.classList.add('show');
            _applyPosition(Math.min(miniPopover.offsetHeight, window.innerHeight * 0.88));
        }
        function hidePopover(){
            if(!miniPopover) return;
            miniPopover.classList.remove('show');
            miniPopover.setAttribute('aria-hidden','true');
            miniPopover.style.right  = '';
            miniPopover.style.width  = '';
            miniPopover.style.height = '';
            currentPopoverKey = null;
        }
        function showPopoverForKey(key, icon){
            if(!miniPopover) return;
            if(currentPopoverKey === key && miniPopover.classList.contains('show')){ hidePopover(); return; }
            miniPopover.classList.remove('show');
            miniPopover.style.left   = '-9999px';
            miniPopover.style.top    = '-9999px';
            miniPopover.style.height = 'auto';
            renderPopover(key);
            currentPopoverKey = key;
            positionPopoverForIcon(icon);
            miniPopover.setAttribute('aria-hidden','false');
        }

        // Close on outside click / Escape / resize
        document.addEventListener('click', function(e){
            if(!miniPopover || !miniPopover.classList.contains('show')) return;
            if(e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;
            hidePopover();
        });
        document.addEventListener('keydown', function(e){ if(e.key === 'Escape') hidePopover(); });
        var _resizeTimer;
        window.addEventListener('resize', function(){
            clearTimeout(_resizeTimer);
            _resizeTimer = setTimeout(function(){
                if(!miniPopover) return;
                miniPopover.classList.remove('show');
                miniPopover.setAttribute('aria-hidden','true');
                miniPopover.style.right = miniPopover.style.width = miniPopover.style.height = miniPopover.style.top = miniPopover.style.left = '';
                currentPopoverKey = null;
            }, 150);
        });

        // ── Browse toggle click ──
        if(browseToggle){
            browseToggle.addEventListener('click', function(e){
                e.preventDefault(); e.stopPropagation();
                var isMiniVisible = window.getComputedStyle(miniSidebar).display !== 'none';
                if(window.innerWidth > 1024 && isMiniVisible){
                    miniSidebar.classList.toggle('expanded');
                    browseToggle.classList.toggle('expanded');
                } else {
                    if(mainSidebar.classList.contains('active')){
                        mainSidebar.classList.remove('active');
                        backdrop.classList.remove('active');
                    } else {
                        mainSidebar.classList.add('active');
                        backdrop.classList.add('active');
                    }
                }
            });
        }

        // ── Expand / collapse button ──
        if(expandBtn){
            expandBtn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                if(miniSidebar) miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }
        
        // Fallback: Direct selector if ID method fails
        document.addEventListener('click', function(e){
            if(e.target.closest('.mini-sidebar-toggle')){
                e.preventDefault();
                e.stopPropagation();
                var sidebar = document.getElementById('miniSidebar');
                if(sidebar) sidebar.classList.toggle('expanded');
                var browse = document.getElementById('browseToggle');
                if(browse) browse.classList.toggle('expanded');
            }
        }, true);

        // Mobile: collapse by default
        if(window.innerWidth <= 768){
            miniSidebar.classList.remove('expanded');
            if(browseToggle) browseToggle.classList.remove('expanded');
        }

        // Menu-bar click handler
        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar){
            menuBar.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                if(miniSidebar) miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }
        
        // Fallback: Direct selector for menu bar
        document.addEventListener('click', function(e){
            if(e.target.closest('#miniSidebarMenuBar')){
                e.preventDefault();
                e.stopPropagation();
                var sidebar = document.getElementById('miniSidebar');
                if(sidebar) sidebar.classList.toggle('expanded');
                var browse = document.getElementById('browseToggle');
                if(browse) browse.classList.toggle('expanded');
            }
        }, true);

        // ── Arrow (sub-indicator) click handler ──
        var arrowHandler = function(e){
            e.stopPropagation(); e.preventDefault();
            var arrow = (e.target && e.target.closest('.sub-indicator')) || e.currentTarget;
            var icon  = arrow ? arrow.closest('.mini-sidebar-icon') : null;
            if(!icon) return;
            var categoryKey = getCategoryKeyFromTarget(icon.getAttribute('data-target') || '');
            if(!categoryKey) return;
            showPopoverForKey(categoryKey, icon);
        };
        document.querySelectorAll('.sub-indicator').forEach(function(arrow){
            arrow.addEventListener('click', arrowHandler, true);
        });
        document.addEventListener('click', function(e){
            if(e.target.closest('.sub-indicator')) arrowHandler(e);
        }, true);

        // ── Mini icon navigation ──
        miniIcons.forEach(function(icon){
            icon.addEventListener('click', function(e){
                if(e.target.closest('.sub-indicator')){ e.stopPropagation(); return; }
                var target = this.getAttribute('data-target');
                if(target) window.location.href = target;
            }, true);
        });

        // ── Backdrop & close button ──
        backdrop.addEventListener('click', function(){
            if(mainSidebar.classList.contains('active')){
                mainSidebar.classList.remove('active');
                backdrop.classList.remove('active');
            }
        });
        var closeSidebarBtn = document.getElementById('closeSidebar');
        if(closeSidebarBtn){
            closeSidebarBtn.addEventListener('click', function(){
                if(mainSidebar.classList.contains('active')){
                    mainSidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                }
            });
        }
    })();

    // ── Mobile FAB toggle for mini sidebar ──
    (function(){
        var fab      = document.getElementById('mobileSidebarFab');
        var sidebar  = document.getElementById('miniSidebar');
        var fabIcon  = document.getElementById('mobileFabIcon');
        if(!fab || !sidebar) return;

        function isMobile(){ return window.innerWidth <= 768; }
        function syncFab(){
            if(!isMobile()){ fab.classList.remove('open','wide'); return; }
            var isOpen     = sidebar.classList.contains('mobile-visible');
            var isExpanded = sidebar.classList.contains('expanded');
            fab.classList.toggle('open',  isOpen);
            fab.classList.toggle('wide', isOpen && isExpanded);
            fabIcon.className = isOpen ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
        }
        fab.addEventListener('click', function(e){
            e.stopPropagation();
            if(!isMobile()) return;
            sidebar.classList.toggle('mobile-visible');
            syncFab();
        });
        new MutationObserver(function(){ syncFab(); }).observe(sidebar, { attributes: true, attributeFilter: ['class'] });
        window.addEventListener('resize', syncFab);
    })();

    // Direct expand button initialization (runs immediately)
    (function(){
        document.addEventListener('DOMContentLoaded', initExpandButton);
        if(document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initExpandButton);
        } else {
            initExpandButton();
        }
        
        function initExpandButton(){
            var expandBtn = document.getElementById('expandSidebar');
            var miniSidebar = document.getElementById('miniSidebar');
            if(!expandBtn || !miniSidebar) return;
            
            expandBtn.onclick = function(e){
                e.preventDefault();
                e.stopPropagation();
                miniSidebar.classList.toggle('expanded');
                var browseToggle = document.getElementById('browseToggle');
                if(browseToggle) browseToggle.classList.toggle('expanded');
                return false;
            };
        }
    })();
</script>