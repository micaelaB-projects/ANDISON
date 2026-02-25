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

    /* ── Mini Sidebar Icon ── */
    .mini-sidebar-icon {
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
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
        background: rgba(255,255,255,0.08);
    }
    .mini-sidebar.expanded .mini-sidebar-icon:hover {
        background: rgba(255,255,255,0.15);
        transform: translateX(4px);
    }
    .mini-sidebar.expanded .mini-sidebar-icon .label {
        display: block;
        opacity: 1;
        color: #ffffff;
    }
    .mini-sidebar-icon:hover {
        background: rgba(0, 215, 179, 0.15);
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
        padding: 12px;
        margin-bottom: 8px;
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
    }

    /* ── Adjust main content for mini sidebar ── */
    section,
    footer,
    .page-content,
    .main-content,
    .category-container {
        margin-left: 0px;
        transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .mini-sidebar.expanded ~ section,
    .mini-sidebar.expanded ~ footer,
    .mini-sidebar.expanded ~ .page-content,
    .mini-sidebar.expanded ~ .main-content,
    .mini-sidebar.expanded ~ .category-container {
        margin-left: 280px;
    }

    @media (max-width: 992px) {
        section, footer, .page-content, .main-content, .category-container {
            margin-left: 0 !important;
        }
        .mini-sidebar { display: none !important; }
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
        <li class="has-sub">
            <a href="arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
            <button class="sub-toggle" aria-controls="sub-arc-welding" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                <li><a href="arc-welding-machine/mig-welding-machine.php"><i class="bi bi-lightning-charge"></i>MIG Welding Machine</a></li>
                <li><a href="arc-welding-machine/accessories-and-consumables.php"><i class="bi bi-bag2"></i>Accessories and Consumables</a></li>
                <li><a href="arc-welding-machine/co1-mag-welding-machine.php"><i class="bi bi-cloud-fill"></i>CO2/MAG Welding Machine</a></li>
                <li><a href="arc-welding-machine/stud-welding-machine.php"><i class="bi bi-record-circle"></i>STUD Welding Machine</a></li>
                <li><a href="arc-welding-machine/tig-welding-machine.php"><i class="bi bi-activity"></i>TIG Welding Machine</a></li>
                <li><a href="arc-welding-machine/plasma-cutting-machine.php"><i class="bi bi-fire"></i>Plasma Cutting Machine</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
            <button class="sub-toggle" aria-controls="sub-arc-robots" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                <li><a href="arc-welding-robots/g3-controller-series.php"><i class="bi bi-cpu"></i>G3 Controller Series</a></li>
                <li><a href="arc-welding-robots/g4-controller-series.php"><i class="bi bi-cpu-fill"></i>G4 Controller Series</a></li>
                <li><a href="arc-welding-robots/featured-products-and-solution.php"><i class="bi bi-stars"></i>Featured Products and Solutions</a></li>
                <li><a href="arc-welding-robots/robot-system-peripherals.php"><i class="bi bi-puzzle"></i>Robot System Peripherals</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
            <button class="sub-toggle" aria-controls="sub-batteries" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-batteries" class="sidebar-sublist collapsed">
                <li><a href="batteries/maintenance-free.php"><i class="bi bi-battery-full"></i>Maintenance Free</a></li>
                <li><a href="batteries/low-maintenance.php"><i class="bi bi-battery-half"></i>Low Maintenance</a></li>
                <li><a href="batteries/special-batteries.php"><i class="bi bi-battery-charging"></i>Special Batteries</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
            <button class="sub-toggle" aria-controls="sub-drilling-lifting" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                <li><a href="drilling-and-lifting/lifting.php"><i class="bi bi-arrow-up-circle-fill"></i>Lifting</a></li>
                <li class="has-nested-sub">
                    <a href="drilling-and-lifting/magnetic-drill.php"><i class="bi bi-tools"></i>Magnetic Drill</a>
                    <button class="nested-toggle" aria-controls="nested-magnetic-drill" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                    <ul id="nested-magnetic-drill" class="sidebar-nested-sublist collapsed">
                        <li><a href="drilling-and-lifting/magnetic-drill/b-line-series.php"><i class="bi bi-dash-lg"></i>B-Line Series</a></li>
                        <li><a href="drilling-and-lifting/magnetic-drill/rl-e-line-series.php"><i class="bi bi-dash-lg"></i>RL-E Line Series</a></li>
                        <li><a href="drilling-and-lifting/magnetic-drill/rbx-line-series.php"><i class="bi bi-dash-lg"></i>RBX-Line Series</a></li>
                        <li><a href="drilling-and-lifting/magnetic-drill/sp-line-series.php"><i class="bi bi-dash-lg"></i>SP-Line Series</a></li>
                        <li><a href="drilling-and-lifting/magnetic-drill/v-line-series.php"><i class="bi bi-dash-lg"></i>V-Line Series</a></li>
                    </ul>
                </li>
                <li><a href="drilling-and-lifting/cutters.php"><i class="bi bi-scissors"></i>Cutters</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
            <button class="sub-toggle" aria-controls="sub-gas-detectors" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                <li><a href="gas-detectors/single-gas-detector.php"><i class="bi bi-search"></i>Single Gas Detector</a></li>
                <li><a href="gas-detectors/multi-gas-detector.php"><i class="bi bi-grid"></i>Multi Gas Detector</a></li>
                <li><a href="gas-detectors/portable-gas-detectors.php"><i class="bi bi-bag"></i>Portable Gas Detectors</a></li>
                <li><a href="gas-detectors/docking-data-management.php"><i class="bi bi-hdd-network"></i>Docking and Data Management</a></li>
                <li><a href="gas-detectors/calibration-gas-regulators.php"><i class="bi bi-sliders"></i>Calibration Gas and Regulators</a></li>
            </ul>
        </li>
        <li class="">
            <a href="portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
        </li>
        <li class="has-sub">
            <a href="power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
            <button class="sub-toggle" aria-controls="sub-power-tool" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                <li><a href="power-tools/grinder.php"><i class="bi bi-circle-fill"></i>Grinder</a></li>
                <li><a href="power-tools/saw.php"><i class="bi bi-app"></i>Saw</a></li>
                <li><a href="power-tools/drill-and-wrench.php"><i class="bi bi-wrench"></i>Drill and Wrench</a></li>
                <li><a href="power-tools/rotary-and-demolition-hammer.php"><i class="bi bi-lightning-fill"></i>Rotary and Demolition Hammer</a></li>
                <li><a href="power-tools/accessories.php"><i class="bi bi-bag-fill"></i>Accessories</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
            <button class="sub-toggle" aria-controls="sub-protection-safety" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                <li><a href="protection/eye-protection.php"><i class="bi bi-eye-fill"></i>Eye Protection</a></li>
                <li class="has-nested-sub">
                    <a href="protection/hand-protection.php"><i class="bi bi-hand-index"></i>Hand Protection</a>
                    <button class="nested-toggle" aria-controls="nested-hand-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                    <ul id="nested-hand-protection" class="sidebar-nested-sublist collapsed">
                        <li><a href="protection/working-gloves.php"><i class="bi bi-hand-index"></i>Working Gloves</a></li>
                        <li><a href="protection/chemical-liquid-protection-gloves.php"><i class="bi bi-shield-fill"></i>Chemical and Liquid Protection Gloves</a></li>
                        <li><a href="protection/disposable-gloves.php"><i class="bi bi-hand-thumbs-up"></i>Disposable Gloves</a></li>
                        <li><a href="protection/welding-gloves.php"><i class="bi bi-hand-index-thumb"></i>Welding Gloves</a></li>
                    </ul>
                </li>
                <li><a href="protection/hearing-respiratory-protection.php"><i class="bi bi-headphones"></i>Hearing &amp; Respiratory Protection</a></li>
                <li><a href="protection/welding-head-and-face-protection.php"><i class="bi bi-person-bounding-box"></i>Welding Head and Face Protection</a></li>
                <li class="has-nested-sub">
                    <a href="protection/body-protection.php"><i class="bi bi-person-fill"></i>Body Protection</a>
                    <button class="nested-toggle" aria-controls="nested-body-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                    <ul id="nested-body-protection" class="sidebar-nested-sublist collapsed">
                        <li><a href="protection/chemical-flame-retardant.php"><i class="bi bi-fire"></i>Chemical and Flame Retardant</a></li>
                        <li><a href="protection/liquid-spray-splash.php"><i class="bi bi-droplet-fill"></i>Liquid Spray and Splash</a></li>
                        <li><a href="protection/particulate-low-hazard.php"><i class="bi bi-wind"></i>Particulate and Low Hazard</a></li>
                    </ul>
                </li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
            <button class="sub-toggle" aria-controls="sub-welding-accessories" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-welding-accessories" class="sidebar-sublist collapsed">
                <li><a href="welding-accessories/welding-electrode-oven.php"><i class="bi bi-thermometer"></i>Welding Electrode Oven</a></li>
                <li><a href="welding-accessories/non-destructive-crack-detection.php"><i class="bi bi-search"></i>Non-Destructive Crack Detection</a></li>
                <li><a href="welding-accessories/gas-saving-regulator.php"><i class="bi bi-droplet"></i>Gas Saving Regulator</a></li>
                <li><a href="welding-accessories/gas-cutting-equipment.php"><i class="bi bi-scissors"></i>Gas Cutting Equipment</a></li>
                <li><a href="welding-accessories/industrial-markers.php"><i class="bi bi-pen-fill"></i>Industrial Markers</a></li>
                <li><a href="welding-accessories/measuring-gauge.php"><i class="bi bi-rulers"></i>Measuring Gauge</a></li>
                <li><a href="welding-accessories/others.php"><i class="bi bi-three-dots"></i>Others</a></li>
            </ul>
        </li>
        <li class="has-sub">
            <a href="welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
            <button class="sub-toggle" aria-controls="sub-welding-consumables" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
            <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                <li><a href="welding-consumables/kobelco.php"><i class="bi bi-award"></i>Kobelco</a></li>
                <li><a href="welding-consumables/metrode.php"><i class="bi bi-award-fill"></i>Metrode</a></li>
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
    <div class="mini-sidebar-icon has-sub" data-target="arc-welding-machine/arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="arc-welding-robots/arc-welding-robot.php" title="Arc Welding Robots"><i class="bi bi-robot"></i><span class="label">Arc Welding Robots</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="batteries/batteries.php" title="Batteries"><i class="bi bi-lightning-fill"></i><span class="label">Batteries</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="drilling-and-lifting/drilling-and-lifting.php" title="Drilling and Lifting"><i class="bi bi-hammer"></i><span class="label">Drilling and Lifting</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="gas-detectors/gas-detectors.php" title="Gas Detectors"><i class="bi bi-bullseye"></i><span class="label">Gas Detectors</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="portable-ventilators/portable-ventilators.php" title="Portable Ventilators"><i class="bi bi-fan"></i><span class="label">Portable Ventilators</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="power-tools/power-tools.php" title="Power Tools"><i class="bi bi-tools"></i><span class="label">Power Tools</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="protection/protection.php" title="Personal Protective Equipment"><i class="bi bi-shield-check"></i><span class="label">PPE</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="welding-accessories/welding-accessories.php" title="Welding Accessories"><i class="bi bi-gear"></i><span class="label">Welding Accessories</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
    <div class="mini-sidebar-icon has-sub" data-target="welding-consumables/welding-consumables.php" title="Welding Consumables"><i class="bi bi-box"></i><span class="label">Welding Consumables</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
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
        var sidebar = document.getElementById('sidebar');
        if(!sidebar) return;
        var pathParts = currentPath.split('/').filter(function(p){ return p && p !== 'andison-1'; });
        var categoryList = [
            'arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting',
            'gas-detectors','portable-ventilators','power-tools','protection',
            'welding-accessories','welding-consumables'
        ];
        var currentCategory = null;
        for(var i = 0; i < pathParts.length; i++){
            if(categoryList.indexOf(pathParts[i]) !== -1){ currentCategory = pathParts[i]; break; }
        }
        if(currentCategory){
            sidebar.querySelectorAll('.sidebar-list > li > a').forEach(function(link){
                if(link.getAttribute('href').toLowerCase().includes(currentCategory)) link.classList.add('active');
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
            var base = '.';
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
                        { label:'Welding Torch / Gun', href: base+'/arc-welding-machine/accessories-and-consumables/welding-torch-gun.php' },
                        { label:'Torch Consumables',   href: base+'/arc-welding-machine/accessories-and-consumables/torch-consumables.php' },
                        { label:'Accessories',         href: base+'/arc-welding-machine/accessories-and-consumables/accessories.php' }
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
                        { label:'B-Line Series',     href: base+'/drilling-and-lifting/magnetic-drill/b-line-series.php' },
                        { label:'RL-E Line Series',  href: base+'/drilling-and-lifting/magnetic-drill/rl-e-line-series.php' },
                        { label:'RBX-Line Series',   href: base+'/drilling-and-lifting/magnetic-drill/rbx-line-series.php' },
                        { label:'SP-Line Series',    href: base+'/drilling-and-lifting/magnetic-drill/sp-line-series.php' },
                        { label:'V-Line Series',     href: base+'/drilling-and-lifting/magnetic-drill/v-line-series.php' }
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
            popoverList.innerHTML = '';
            getPopoverItems(key).forEach(function(it){
                var li = document.createElement('li');
                li.className = 'mini-popover-item';
                if(it.subitems && it.subitems.length > 0){
                    li.innerHTML = '<span class="square"></span><a href="'+it.href+'">'+it.label+'</a><button class="popover-expand-btn" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>';
                    li.className += ' has-subitems';
                    var subContainer = document.createElement('div');
                    subContainer.className = 'popover-subitems collapsed';
                    subContainer.innerHTML = it.subitems.map(function(sub){
                        return '<a href="'+sub.href+'" class="popover-subitem">'+sub.label+'</a>';
                    }).join('');
                    li.appendChild(subContainer);
                    var btn = li.querySelector('.popover-expand-btn');
                    btn.addEventListener('click', function(e){
                        e.preventDefault(); e.stopPropagation();
                        var isExp = btn.getAttribute('aria-expanded') === 'true';
                        btn.setAttribute('aria-expanded', isExp ? 'false' : 'true');
                        subContainer.classList.toggle('collapsed', isExp);
                        setTimeout(adjustPopoverHeight, 10);
                    });
                } else {
                    li.innerHTML = '<span class="square"></span><a href="'+it.href+'">'+it.label+'</a>';
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
            expandBtn.addEventListener('click', function(){
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        // Mobile: collapse by default
        if(window.innerWidth <= 768){
            miniSidebar.classList.remove('expanded');
            if(browseToggle) browseToggle.classList.remove('expanded');
        }

        // Menu-bar click handler
        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar){
            menuBar.addEventListener('click', function(){
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

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
</script>
