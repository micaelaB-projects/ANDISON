<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
            padding-top: 140px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%);
            color: white;
            padding: 14px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-top {
            display: flex;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            gap: 20px;
            margin-bottom: 12px;
        }

        .logo {
            font-size: 16px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .logo-box {
            background: transparent;
            color: #2b00d9;
            padding: 0;
            border-radius: 0;
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .logo-box img {
            height: 50px;
            width: auto;
            display: block;
        }

        .header-contact {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 13px;
            flex: 0 0 auto;
        }

        .contact-link {
            color: rgba(255,255,255,0.95);
            text-decoration: none;
            font-weight: 600;
            padding-bottom: 8px;
            white-space: nowrap;
            position: relative;
            display: inline-block;
        }

        .contact-link::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 64px;
            height: 3px;
            background: rgba(255,255,255,0.18);
            bottom: -6px;
            border-radius: 2px;
            transition: transform 220ms ease;
        }

        .contact-link:hover::after,
        .contact-link:focus-visible::after {
            transform: translateX(-50%) scaleX(1);
        }

        .contact-dropdown {
            position: relative;
            display: inline-block;
        }

        .contact-popover {
            position: absolute;
            left: 50%;
            top: calc(100% + 12px);
            width: 320px;
            background: #fff;
            color: #111;
            border-radius: 8px;
            padding: 14px 16px;
            box-shadow: 0 10px 30px rgba(10,10,20,0.12);
            opacity: 0;
            visibility: hidden;
            transform: translateX(-50%) translateY(-6px) scale(0.98);
            transition: opacity 180ms ease, transform 180ms ease, visibility 180ms;
            z-index: 120;
        }

        .contact-popover::before {
            content: '';
            position: absolute;
            left: 20%;
            transform: translateX(50%);
            top: 8px;
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #fff;
            filter: drop-shadow(0 -1px 0 rgba(0,0,0,0.03));
        }

        .contact-dropdown:hover:not(.closed) .contact-popover,
        .contact-dropdown:focus-within:not(.closed) .contact-popover {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0) scale(1);
        }

        .contact-close {
            position: absolute;
            top: 8px;
            right: 8px;
            background: transparent;
            border: none;
            color: #666;
            font-weight: 700;
            font-size: 24px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            line-height: 1;
        }

        .contact-close:hover { background: rgba(0,0,0,0.06); color: #333; }

        .contact-dropdown.closed .contact-popover {
            opacity: 0 !important;
            visibility: hidden !important;
            transform: translateX(-50%) translateY(-6px) scale(0.98) !important;
        }

        .contact-list { list-style: none; margin: 0; padding: 6px 0; }
        .contact-list li { display:flex; gap:12px; align-items:center; padding:10px 6px; }
        .contact-list .icon { font-size:18px; width:28px; text-align:center; color:#2B11DB; }
        .contact-list a { color: #111; text-decoration:none; font-weight:600; }
        .contact-list a:hover { text-decoration:underline; }

        .search-bar {
            flex: 1 1 auto;
            display: flex;
            justify-content: center;
            max-width: 600px;
            margin: 0 auto;
        }

        .search-bar .search-field {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            height: 46px;
            padding: 10px 16px 10px 40px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            font-size: 15px;
            background: rgba(255,255,255,0.95);
            color: #333;
        }

        .search-bar input::placeholder {
            color: #999;
        }

        .search-bar .search-field::before {
            content: '🔍';
            position: absolute;
            left: 12px;
            font-size: 16px;
            pointer-events: none;
            color: #666;
        }

        .inquiry-btn,
        .cart-icon-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 6px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00E5C8  0%, #347aec 100%);
        }

        .inquiry-btn:hover,
        .cart-icon-wrapper:hover {
            background: linear-gradient(135deg, #00FFD1 0%, #00FFD1 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 255, 209, 0.4);
            color: #333;
        }

        .cart-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(255, 102, 102, 0.4);
        }

        .cart-badge.hidden {
            display: none;
        }
        
        .right-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Navigation */
        nav {
            position: relative;
            background: rgba(0, 215, 179, 0.85);
            backdrop-filter: blur(10px);
            overflow: visible;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            min-height: 52px;
            gap: 18px;
            justify-content: flex-start;
            padding-left: 100px;
        }

        .browse-toggle {
            position: absolute;
            left: 12px;
            top: 20%;
            transform: translateY(39%);
            z-index: 80;
            background: transparent;
            border: none;
            color: white;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            cursor: pointer;
            font-size: 15px;
            line-height: 6px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            gap: 28px;
            margin: 0;
            padding: 0;
        }

        .nav-list > li {
            position: relative;
        }

        .nav-list li {
            position: relative;
        }

        .nav-list a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            padding: 12px 6px;
            display: block;
            transition: color 0.2s;
            position: relative;
        }

        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        /* Glowing underline + dark active background for top-level nav links */
        .nav-list > li > a {
            position: relative;
            padding: 10px 14px;
            color: white;
            transition: color 180ms ease, background 180ms ease;
        }

        .nav-list > li > a.active {
            background: rgba(0,0,0,0.14);
            color: #fff;
            font-weight: 700;
            border-radius: 6px;
            box-shadow: inset 0 -6px 18px rgba(0,0,0,0.06);
        }

        .nav-list > li > a.active::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -8px;
            transform: translateX(-50%);
            width: 44px;
            height: 6px;
            border-radius: 6px;
            background: linear-gradient(90deg, #00ffd1 0%, #00d4aa 50%, #2B11DB 100%);
            box-shadow: 0 8px 28px rgba(0,212,170,0.18), 0 0 40px rgba(43,17,219,0.08);
            pointer-events: none;
        }

        .nav-list > li > a:hover::after {
            width: 56px;
        }

        .nav-dropdown {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            background: white;
            min-width: 280px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, transform 0.25s ease, visibility 0.25s;
            z-index: 110;
            padding: 16px;
            margin-top: 8px;
        }

        .nav-dropdown::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid white;
            filter: drop-shadow(0 -2px 2px rgba(0,0,0,0.05));
        }

        .nav-list > li:hover .nav-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        /* Keep dropdown visible when hovering over it */
        .nav-dropdown:hover {
            opacity: 1;
            visibility: visible;
        }

        .nav-dropdown h4 {
            color: #2b00d9;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .nav-dropdown ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-dropdown ul li {
            margin: 0;
        }

        .nav-dropdown ul a {
            color: #333;
            font-size: 14px;
            padding: 8px 12px;
            display: block;
            border-radius: 4px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-dropdown ul a:hover {
            background: #f0f5ff;
            color: #2b00d9;
        }

        .nav-dropdown p {
            color: #666;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }

        nav li:nth-child(3) .nav-dropdown {
            min-width: 650px;
            max-width: 650px;
            padding: 24px 28px;
        }

        .nav-dropdown ul a {
            display: block;
            border-radius: 4px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .nav-dropdown ul a:hover {
            background: #f0f5ff;
            color: #2B11DB;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 12px 20px !important;
            margin-top: 16px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 60px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a img {
            max-width: 85px;
            max-height: 45px;
            object-fit: contain;
            display: block;
            pointer-events: all;
            cursor: pointer;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            cursor: pointer;
        }

        /* Overlay sidebar */
        .overlay-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.08);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s;
            z-index: 60;
        }

        .overlay-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-overlay {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            width: 300px;
            max-width: 88%;
            background: #fff;
            box-shadow: 6px 0 30px rgba(2,6,23,0.08);
            transform: translateX(-100%);
            transition: transform 0.28s ease;
            z-index: 70;
            padding: 28px 20px;
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
        }
        .sidebar-list a:hover { 
            background: #f3f4f6; 
            color: #2B11DB;
            padding-left: 16px;
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

        .sidebar-list a .sidebar-label {
            flex: 1;
        }

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

        .sidebar-list li.has-sub a .sidebar-arrow {
            display: flex;
        }

        .sidebar-sublist { 
            list-style: none; 
            margin: 0; 
            padding: 8px 0 8px 44px; 
            display: none;
            background: #fafafa;
            margin-left: 12px;
            margin-right: 12px;
            padding-left: 16px;
            border-left: 2px solid #e5e7eb;
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .sidebar-sublist li { 
            padding: 4px 0; 
            border: none;
        }
        .sidebar-sublist a { 
            color: #4b5563; 
            font-size: 14px; 
            padding: 6px 8px; 
            display: block; 
            text-decoration: none;
            justify-content: flex-start;
        }
        .sidebar-sublist a:hover { 
            color: #2B11DB; 
            background: transparent;
            padding-left: 12px;
        }

        /* Nested sublists */
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
            margin: 10px 0 10px -12px; 
            padding: 0; 
            display: none;
        }
        .sidebar-nested-sublist li { 
            padding: 0;
            border: none;
        }
        .sidebar-nested-sublist a { 
            color: #5a6b7d; 
            font-size: 13px; 
            padding: 10px 12px 10px 28px; 
            display: block; 
            text-decoration: none;
            position: relative;
            transition: all 0.25s ease;
            border-radius: 6px;
            margin: 2px 0;
        }
        .sidebar-nested-sublist a::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, #2B11DB 0%, #6d28d9 100%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(43, 17, 219, 0.2);
        }
        .sidebar-nested-sublist a:hover { 
            color: #2B11DB;
            background: rgba(43, 17, 219, 0.08);
            padding-left: 32px;
            transform: translateX(4px);
        }

        .sidebar-nested-sublist.collapsed { display: none; }
        .sidebar-nested-sublist:not(.collapsed) { display: block; }
        .sidebar-list li.has-sub { position: relative; }
        .has-sub > a { padding-right: 40px; }
        .sub-toggle {
            position: absolute;
            right: 12px;
            top: 16px;
            transform: none;
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            box-shadow: none;
        }
        .sub-toggle:focus { outline: none; }
        .sub-toggle .bi { transition: transform 200ms ease; font-size: 16px; }
        .sub-toggle[aria-expanded="true"] .bi { transform: rotate(90deg); }
        .sidebar-sublist.collapsed { display: none; }
        .sidebar-sublist:not(.collapsed) { display: block; }

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

        .sidebar-close:hover { color: #333; }

        /* Main Content */
        .main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .page-title {
            text-align: center;
            font-size: 35px;
            font-weight: 800;
            color: #2B11DB;
            margin-bottom: 50px;
        }

        /* Location Cards */
        .locations-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 60px;
        }

        .location-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .location-map {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border: none;
        }

        .location-info {
            padding: 30px;
        }

        .location-name {
            font-size: 28px;
            font-weight: bold;
            color: #2b00d9;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .info-value a {
            color: #2b00d9;
            text-decoration: none;
        }

        .info-value a:hover {
            text-decoration: underline;
        }

        .contact-note {
            background: #f0f5ff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        /* Footer */
        footer {
            background: #2B11DB;
            color: white;
            padding: 30px 0;
            margin-top: auto;
            width: 100vw;
            position: relative;
            left: 0;
            right: 0;
            margin-left: 0;
            margin-right: 0;
        }

        .footer-content {
            width: 100%;
            margin: 0;
            padding: 0 20px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-copyright {
            font-size: 13px;
        }

        .footer-links {
            display: flex;
            gap: 25px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 10px;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #00d4aa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 32px;
            }

            .location-name {
                font-size: 24px;
            }

            .location-info {
                padding: 20px;
            }

            .nav-inner {
                padding-left: 50px;
                padding-right: 6px;
                min-height: 40px;
                overflow-x: auto;
                overflow-y: visible;
                justify-content: flex-start;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
            }
            .nav-inner::-webkit-scrollbar { display: none; }
            .nav-list { position: static; transform: none; left: auto; flex-wrap: nowrap; flex-shrink: 0; gap: 0; }
            .nav-list > li > a { white-space: nowrap; font-size: 12px; padding: 8px 10px; }
        }
        /* Shared animations and utilities (standardized) */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes mapZoom { from { opacity:0; transform: scale(.98); filter: blur(3px);} to { opacity:1; transform: scale(1); filter: blur(0);} }
        @keyframes slideFromRight { from { opacity:0; transform: translateX(18px);} to { opacity:1; transform: translateX(0);} }

        .page-title { opacity: 1; transform: translateY(0); }

        .location-card { display:grid; grid-template-columns: 1fr 1fr; gap:18px; align-items:center; }
        .location-card .map { opacity:0; transform: scale(.98); animation: mapZoom 0s forwards; animation-delay: calc(var(--i,1) * 0ms); }
        .location-card .info { opacity:0; transform: translateX(18px); animation: slideFromRight 0s forwards; animation-delay: calc(var(--i,1) * 0ms); }

        .location-card a.tel, .location-card a.address { transition: transform .22s ease, color .18s ease; }
        .location-card a.tel:hover { transform: translateX(6px); color:#00d4aa; }
        .location-card a.address:hover { transform: translateX(4px); box-shadow: 0 10px 24px rgba(43,17,219,0.06); }

        .contact-note { opacity: 1; transform: translateY(0); }

        /* Reveal helpers (use existing fadeUp keyframes) */
        .reveal-hidden { opacity: 0; transform: translateY(18px); transition: opacity 0s ease, transform 0s ease; }
        .reveal { opacity: 1; transform: none; }
        .reveal-stagger > * { opacity: 0; transform: translateY(18px); }
        .reveal-stagger.revealed > * { opacity: 1; transform: none; transition: all .48s ease; }

        h1, .page-title { opacity: 1; }
        h1 + p, .page-subtitle { opacity: 1; }
        img:not(.no-anim) { opacity: 1; }


        @media (prefers-reduced-motion: reduce) {
            .reveal, .reveal-hidden, img { animation: none !important; transition: none !important; }
        }

        /* Page transition keyframes */
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes pageExit { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(20px); } }

        /* Ensure header/navigation/footer do not animate or move */
        header, nav, footer, .header-top, .nav-inner, .browse-toggle, .nav-list, .right-actions, .footer-content {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        .nav-list li { animation: none !important; opacity: 1 !important; transform: none !important; }

        /* Sidebar Overlay */
        .overlay-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 63;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            display: none;
        }
        .overlay-backdrop.active {
            opacity: 1;
            visibility: visible;
            display: block;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 380px;
            background: #fff;
            box-shadow: 2px 0 15px rgba(0,0,0,0.15);
            z-index: 64;
            padding-top: 142px;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: none;
        }
        .sidebar-overlay.active {
            transform: translateX(0);
            display: block;
        }
        .sidebar-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-list > li {
            border-bottom: 1px solid #e5e7eb;
            padding: 0;
        }
        .sidebar-list > li:last-child { border-bottom: none; }
        .sidebar-list > li > a {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            color: #1f2937;
            text-decoration: none;
            font-weight: 500;
            gap: 12px;
            transition: all 0.25s ease;
            position: relative;
        }
        .sidebar-list > li > a:hover,
        .sidebar-list > li > a.active {
            background: rgba(43, 17, 219, 0.06);
            color: #2B11DB;
            padding-left: 28px;
        }
        .sidebar-list > li > a.active {
            border-right: 3px solid #2B11DB;
        }
        .sidebar-icon { font-size: 20px; }
        .sidebar-label { font-size: 14px; }
        .sidebar-sublist {
            list-style: none;
            margin: 0;
            padding: 0;
            max-height: 500px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 1;
            background: #f9fafb;
        }
        .sidebar-sublist.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .sidebar-sublist li {
            padding: 0;
            border: none;
        }
        .sidebar-sublist a {
            color: #5a6b7d;
            font-size: 13px;
            padding: 12px 20px 12px 52px;
            display: block;
            text-decoration: none;
            position: relative;
            transition: all 0.25s ease;
            border-radius: 0;
            margin: 0;
        }
        .sidebar-sublist a::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, #2B11DB 0%, #6d28d9 100%);
            border-radius: 50%;
            box-shadow: 0 1px 2px rgba(43, 17, 219, 0.15);
        }
        .sidebar-sublist a:hover {
            color: #2B11DB;
            background: rgba(43, 17, 219, 0.08);
            padding-left: 56px;
        }
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
            margin: 10px 0 10px -12px; 
            padding: 0;
            max-height: 500px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 1;
        }
        
        .sidebar-nested-sublist.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
        .sidebar-nested-sublist li { 
            padding: 0;
            border: none;
        }
        .sidebar-nested-sublist a { 
            color: #5a6b7d; 
            font-size: 13px; 
            padding: 10px 12px 10px 28px; 
            display: block; 
            text-decoration: none;
            position: relative;
            transition: all 0.25s ease;
            border-radius: 6px;
            margin: 2px 0;
        }
        .sidebar-nested-sublist a::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 6px;
            height: 6px;
            background: linear-gradient(135deg, #2B11DB 0%, #6d28d9 100%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(43, 17, 219, 0.2);
        }
        .sidebar-nested-sublist a:hover { 
            color: #2B11DB;
            background: rgba(43, 17, 219, 0.08);
            padding-left: 32px;
            transform: translateX(4px);
        }

        .sidebar-list li.has-sub { position: relative; }
        .has-sub > a { padding-right: 40px; }
        .sub-toggle {
            position: absolute;
            right: 8px;
            top: 12px;
            transform: none;
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
            box-shadow: none;
            transition: all 0.2s ease;
            font-size: 0;
            z-index: 10;
        }
        .sub-toggle:hover {
            background: rgba(43, 17, 219, 0.1);
            border-color: #2B11DB;
            transform: scale(1.1);
        }
        .sub-toggle:active {
            transform: scale(0.95);
        }
        .sub-toggle:focus { outline: none; }
        .sub-toggle .bi { 
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-size: 14px;
            display: inline-flex;
        }
        .sub-toggle[aria-expanded="true"] .bi { transform: rotate(180deg); }

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
        .sidebar-close:hover {
            color: #374151;
        }

        /* Mini Sidebar (always visible icon bar) */
        .mini-sidebar {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            width: 80px;
            background: #2B11DB;
            box-shadow: 2px 0 16px rgba(0,0,0,0.1);
            z-index: 65;
            padding: 20px 12px;
            overflow: hidden;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .mini-sidebar.expanded {
            width: 280px;
            overflow-y: auto;
            padding: 20px 12px;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .mini-sidebar.expanded::-webkit-scrollbar {
            display: none;
        }

        .mini-sidebar.active {
            display: flex !important;
            flex-direction: column;
            align-items: center;
        }

        .mini-sidebar.active.expanded {
            align-items: stretch;
        }

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
            margin-bottom: 8px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), justify-content 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease;
            gap: 12px;
            padding: 0;
            flex-shrink: 0;
            min-width: 56px;
        }

        .mini-sidebar-icon .label {
            display: none;
            font-size: 13px;
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
            padding: 12px;
            min-width: auto;
        }

        .mini-sidebar.expanded .mini-sidebar-icon .label {
            display: block;
            opacity: 1;
        }

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

        .mini-sidebar.expanded .browse-label {
            display: inline-block !important;
        }

        .mini-sidebar-icon:hover {
            background: rgba(255,255,255,0.2);
            transform: scale(1.05);
        }

        .mini-sidebar.expanded .mini-sidebar-icon:hover {
            transform: translateX(4px);
        }

        .mini-sidebar-icon.active-icon {
            background: #00D7B3;
            color: #2B11DB;
        }

        .mini-sidebar-icon .sub-indicator {
            position: absolute;
            bottom: -1px;
            right: -1px;
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            opacity: 0.95;
            transition: background 0.15s ease, color 0.15s ease;
            z-index: 999;
            cursor: pointer;
            pointer-events: auto;
            border: 1px solid #ffffff;
            box-shadow: none;
        }

        .mini-sidebar-icon:hover .sub-indicator {
            opacity: 1;
            background: #00D7B3;
            color: #2B11DB;
        }

        .mini-sidebar-icon .sub-indicator:active {
            transform: translateY(0);
        }

        .mini-sidebar.expanded .mini-sidebar-icon .sub-indicator {
            position: static;
            background: transparent;
            color: #2B11DB;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-left: auto;
            opacity: 1;
            border: 0;
            cursor: pointer;
            pointer-events: auto;
            z-index: 100;
            box-shadow: none;
        }

        .mini-sidebar-toggle {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            cursor: pointer;
            border-radius: 8px;
            font-size: 20px;
            margin-top: auto;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease;
            flex-shrink: 0;
        }

        .mini-sidebar-toggle:hover {
            background: rgba(255,255,255,0.25);
            transform: scale(1.05);
        }

        .mini-sidebar-toggle:active {
            transform: scale(0.95);
        }

        .mini-sidebar-toggle i {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-block;
        }

        .mini-sidebar.expanded .mini-sidebar-toggle i {
            transform: rotate(180deg);
        }

        .mini-sidebar.expanded .mini-sidebar-toggle {
            width: 100%;
            padding: 12px;
            min-width: auto;
        }

        /* Adjust main container for mini sidebar */
        .main-content, .category-container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 60px 20px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

       
        /* When sidebar is expanded (collapsed mini) */
        .sidebar-overlay.expanded {
            width: 380px;
        }

        .overlay-backdrop.expanded {
            display: none !important;
        }

        @media (max-width: 1024px) {
            .mini-sidebar {
                display: none !important;
            }
            .browse-toggle {
                display: inline-flex !important;
            }
            .browse-toggle .browse-text {
                display: inline !important;
            }
            .main-content, .category-container {
                margin-left: 0 !important;
            }
        }

        /* Mini popover styles for subcategories */
        .mini-popover {
            position: fixed;
            top: -9999px;
            left: -9999px;
            width: 320px;
            max-width: calc(100vw - 32px);
            background: linear-gradient(180deg, #1976D2FF 0%, #19D2B6FF 100%);
            color: #fff;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.25);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 160ms ease, transform 160ms ease, visibility 160ms ease;
            z-index: 200;
        }
        .mini-popover.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
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
            background: #f5f9ff;
            color: #0f5132;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            padding: 12px 16px;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 0.3px;
        }
        .mini-popover-title { color: #0f5132; }
        .mini-popover-body {
            padding: 12px 16px 16px 16px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }
        .mini-popover-list {
            list-style: none;
            margin: 0;
            padding: 6px 0 6px 0;
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
        }
        .mini-popover-item {
            position: relative;
            padding-left: 42px;
            margin: 12px 0;
            display: flex;
            align-items: stretch;
            min-height: 32px;
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
        }
        .mini-popover-item a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            display: block;
            padding: 8px 10px;
            border-radius: 8px;
            transition: background 140ms ease, transform 120ms ease;
            width: 100%;
        }
        .mini-popover-item a:hover {
            background: rgba(255,255,255,0.12);
            transform: translateX(2px);
        }

    </style>
</head>
<body>
    <?php
        $company_name = "ANDISON INDUSTRIAL";
        
        // Contact information
        $phone = "+1(234) 567 8900";
        $phone2 = "+1(234) 567 8900";
        $phone3 = "+1(639) 977 803 7398";
        $email = "info@andison-industrial.com";
    ?>

    <!-- Header -->
    <header>
        <div class="header-top">
            <div class="logo">
                <div class="logo-box"><a href="home.php"><img src="assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
            </div>

            <div class="search-bar">
                <form class="search-field" action="search.php" method="get">
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="inquirylist.php" class="inquiry-btn">INQUIRY LIST <span class="cart-badge hidden" id="cartBadge">0</span></a>
                <div class="header-contact">
                    <div class="contact-dropdown" tabindex="0" aria-haspopup="true">
                        <a href="#contact" class="contact-link" aria-label="Contact Us">Contact Us ▾</a>
                        <div class="contact-popover" role="menu" aria-hidden="true">
                            <button class="contact-close" aria-label="Close contact popover">✕</button>
                            <ul class="contact-list">
                                <li><span class="icon"><i class="bi bi-telephone"></i></span><a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></li>
                                <li><span class="icon"><i class="bi bi-telephone"></i></span><a href="tel:<?php echo $phone2; ?>"><?php echo $phone2; ?></a></li>
                                <li><span class="icon"><i class="bi bi-telephone"></i></span><a href="tel:<?php echo $phone3; ?>"><?php echo $phone3; ?></a></li>
                                <li><span class="icon"><i class="bi bi-envelope"></i></span><a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav>
            <div class="nav-inner">
                <ul class="nav-list">
                    <li>
                        <a href="home.php">Home</a>
                        <div class="nav-dropdown">
                            <h4>Welcome</h4>
                            <p>Discover our complete range of industrial welding solutions and equipment.</p>
                        </div>
                    </li>
                    <li>
                        <a href="aboutus.php">About Us</a>
                        <div class="nav-dropdown">
                            <h4>Our Company</h4>
                            <ul>
                                <li><a href="aboutus.php#mission">Our Mission</a></li>
                                <li><a href="aboutus.php#history">Company History</a></li>
                                <li><a href="aboutus.php#team">Our Team</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="brands.php">Brands</a>
                        <div class="nav-dropdown">
                            <h4>Featured Brands</h4>
                            <ul>
                                <li><a href="brand.php?name=Panasonic%20Connect"><img src="assets/brands/PANASONIC.jpg" alt="Panasonic Connect" title="Panasonic Connect"></a></li>
                                <li><a href="brand.php?name=Kobelco"><img src="assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                                <li><a href="brand.php?name=Metrode"><img src="assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                                <li><a href="brand.php?name=DryRod.%20II"><img src="assets/brands/DRYROD.jpg" alt="DryRod. II" title="DryRod. II"></a></li>
                                <li><a href="brand.php?name=Weldcraft"><img src="assets/brands/WELDCRAFT.jpg" alt="Weldcraft" title="Weldcraft"></a></li>
                                <li><a href="brand.php?name=Truweld"><img src="assets/brands/TRUWELD.jpg" alt="Truweld" title="Truweld"></a></li>
                                <li><a href="brand.php?name=Arcair"><img src="assets/brands/ARCAIR.jpg" alt="Arcair" title="Arcair"></a></li>
                                <li><a href="brand.php?name=MAGNAFLUX"><img src="assets/brands/MAGNAFLUX.jpg" alt="Magnaflux" title="Magnaflux"></a></li>
                                <li><a href="brand.php?name=Tempilstik"><img src="assets/brands/TEMPILSTIK.jpg" alt="Tempilstik" title="Tempilstik"></a></li>
                                <li><a href="brand.php?name=TANAKA"><img src="assets/brands/TANAKA.jpg" alt="Tanaka" title="Tanaka"></a></li>
                                <li><a href="brand.php?name=CHIYODA"><img src="assets/brands/CHIYODA.jpg" alt="Chiyoda" title="Chiyoda"></a></li>
                                <li><a href="brand.php?name=Yutaka"><img src="assets/brands/YUTAKA.jpg" alt="Yutaka" title="Yutaka"></a></li>
                                <li><a href="brand.php?name=HARDWORKER"><img src="assets/brands/HARDWORKER.jpg" alt="Hard Workers" title="Hard Workers"></a></li>
                                <li><a href="brand.php?name=Soyer"><img src="assets/brands/SOYER.jpg" alt="Soyer" title="Soyer"></a></li>
                                <li><a href="brand.php?name=Aquasol"><img src="assets/brands/AQUASOL.jpg" alt="Aquasol" title="Aquasol"></a></li>
                                <li><a href="brand.php?name=SK%20And%20GAL%20GAGE"><img src="assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE" title="SK And GAL GAGE"></a></li>
                                <li><a href="brand.php?name=COPPUS"><img src="assets/brands/COPPUS.jpg" alt="Coppus" title="Coppus"></a></li>
                                <li><a href="brand.php?name=BW%20Technologies"><img src="assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies" title="BW Technologies"></a></li>
                                <li><a href="brand.php?name=RAC"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAC" title="RAC"></a></li>
                                <li><a href="brand.php?name=WELDAS"><img src="assets/brands/WELDAS.jpg" alt="Weldas" title="Weldas"></a></li>
                                <li><a href="brand.php?name=UVEX"><img src="assets/brands/UVEX.jpg" alt="Uvex" title="Uvex"></a></li>
                                <li><a href="brand.php?name=ACES"><img src="assets/brands/ACES.jpg" alt="Aces" title="Aces"></a></li>
                                <li><a href="brand.php?name=MICROGARD"><img src="assets/brands/MICROGARD.jpg" alt="Microgard" title="Microgard"></a></li>
                                <li><a href="brand.php?name=ANSELL"><img src="assets/brands/ANSELL.jpg" alt="Ansell" title="Ansell"></a></li>
                                <li><a href="brand.php?name=Alfra"><img src="assets/brands/ALFRA.jpg" alt="Alfra" title="Alfra"></a></li>
                                <li><a href="brand.php?name=BOSCH"><img src="assets/brands/BOSCH.jpg" alt="Bosch" title="Bosch"></a></li>
                                <li><a href="brand.php?name=Makita"><img src="assets/brands/MAKITA.jpg" alt="Makita" title="Makita"></a></li>
                                <li><a href="brand.php?name=Weller"><img src="assets/brands/WEILER.jpg" alt="Weller" title="Weller"></a></li>
                                <li><a href="brand.php?name=Garryson"><img src="assets/brands/GARRYSON.jpg" alt="Garryson" title="Garryson"></a></li>
                                <li><a href="brand.php?name=Spilfyter"><img src="assets/brands/SPILFYTER.jpg" alt="Spilfyter" title="Spilfyter"></a></li>
                                <li><a href="brand.php?name=Dalo"><img src="assets/brands/DALO.jpg" alt="Dalo" title="Dalo"></a></li>
                                <li><a href="brand.php?name=MOTOLITE"><img src="assets/brands/MOTOLITE.jpg" alt="Motolite" title="Motolite"></a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="industries.php">Industries</a>
                        <div class="nav-dropdown">
                            <h4>Industries We Serve</h4>
                            <ul>
                                <li><a href="industries.php#manufacturing">Manufacturing</a></li>
                                <li><a href="industries.php#construction">Construction</a></li>
                                <li><a href="industries.php#automotive">Automotive</a></li>
                                <li><a href="industries.php#shipbuilding">Shipbuilding</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="services.php">Services</a>
                        <div class="nav-dropdown">
                            <h4>Our Services</h4>
                            <ul>
                                <li><a href="services.php#consultation">Technical Consultation</a></li>
                                <li><a href="services.php#training">Training Programs</a></li>
                                <li><a href="services.php#maintenance">Equipment Maintenance</a></li>
                                <li><a href="services.php#support">After-Sales Support</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="contact.php" class="active">Contact Us</a>
                        <div class="nav-dropdown">
                            <h4>Get In Touch</h4>
                            <p>Reach out to our team for inquiries, quotes, or technical support.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Overlay Backdrop -->
    <div class="overlay-backdrop" id="overlayBackdrop"></div>
    <aside id="sidebar" class="sidebar-overlay" aria-hidden="true">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 12px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; color: #1f2937;">Categories</h3>
            <button class="sidebar-close" id="closeSidebar">✕</button>
        </div>
        <ul class="sidebar-list">
            <li class="has-sub">
                <a href="arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machine</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-welding" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-robot" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-robot" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-robots/g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="arc-welding-robots/g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="arc-welding-robots/featured-products-and-solution.php">Featured Products & Solutions</a></li>
                    <li><a href="arc-welding-robots/robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-batteries" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="batteries/maintenance-free.php">Maintenance Free</a></li>
                    <li><a href="batteries/low-maintenance.php">Low Maintenance</a></li>
                    <li><a href="batteries/special-batteries.php">Special Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-drilling-lifting" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li><a href="drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a></li>
                    <li><a href="drilling-and-lifting/cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="gas-detectors/portable-gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Portable Gas Detectors</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-gas-detectors" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                    <li><a href="gas-detectors/docking-data-management.php">Docking and Data Management</a></li>
                    <li><a href="gas-detectors/calibration-gas-regulators.php">Calibration Gas and Regulators</a></li>
                </ul>
            </li>
            <li>
                <a href="portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
            </li>
            <li class="has-sub">
                <a href="power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-power-tool" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                    <li><a href="power-tools/grinder/grinder.php">Grinder</a></li>
                    <li><a href="power-tools/saw/saw.php">Saw</a></li>
                    <li><a href="power-tools/drill-and-wrench/drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="power-tools/rotary-and-demolition-hammer/rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="power-tools/accessories/accessories.php">Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-protection-safety" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="protection/eye-protection.php">Eye Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="protection/hand-protection.php">Hand Protection</a>
                        <button class="nested-toggle" aria-expanded="false" aria-controls="nested-hand-protection" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                        <ul id="nested-hand-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="protection/working-gloves.php">Working Gloves</a></li>
                            <li><a href="protection/chemical-liquid-protection-gloves.php">Chemical and Liquid Protection Gloves</a></li>
                            <li><a href="protection/disposable-gloves.php">Disposable Gloves</a></li>
                            <li><a href="protection/welding-gloves.php">Welding Gloves</a></li>
                        </ul>
                    </li>
                    <li><a href="protection/hearing-respiratory-protection.php">Hearing &amp; Respiratory Protection</a></li>
                    <li><a href="protection/welding-head-and-face-protection.php">Welding Head and Face Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="protection/body-protection.php">Body Protection</a>
                        <button class="nested-toggle" aria-expanded="false" aria-controls="nested-body-protection" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                        <ul id="nested-body-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="protection/chemical-flame-retardant.php">Chemical and Flame Retardant</a></li>
                            <li><a href="protection/liquid-spray-splash.php">Liquid Spray and Splash</a></li>
                            <li><a href="protection/particulate-low-hazard.php">Particulate and Low Hazard</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-welding-accessories" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-welding-accessories" class="sidebar-sublist collapsed">
                    <li><a href="welding-accessories/welding-electrode-oven.php">Welding Electrode Oven</a></li>
                    <li><a href="welding-accessories/non-destructive-crack-detection.php">Non-Destructive Crack Detection</a></li>
                    <li><a href="welding-accessories/gas-saving-regulator.php">Gas Saving Regulator</a></li>
                    <li><a href="welding-accessories/gas-cutting-equipment.php">Gas Cutting Equipment</a></li>
                    <li><a href="welding-accessories/industrial-markers.php">Industrial Markers</a></li>
                    <li><a href="welding-accessories/measuring-gauge.php">Measuring Gauge</a></li>
                    <li><a href="welding-accessories/others.php">Others</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-welding-consumables" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="welding-consumables/kobelco.php">Kobelco</a></li>
                    <li><a href="welding-consumables/metrode.php">Metrode</a></li>
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

    <!-- Floating popover for mini sidebar subcategories -->
    <div id="miniPopover" class="mini-popover" aria-hidden="true">
        <div class="mini-popover-header">
            <div class="mini-popover-title">Arc Welding Robots</div>
        </div>
        <div class="mini-popover-body">
            <ul class="mini-popover-list"></ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1 class="page-title">Contact Us</h1>
        
        <div class="locations-container">
            <!-- Manila Location -->
            <div class="location-card">
                <iframe 
                    class="location-map"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3860.8757799830987!2d120.9751084745731!3d14.606151376941842!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397ca0674eb2fa7%3A0xc2c12bedb9ad32b!2sAndison%20Industrial%20Sales%20Incorporated!5e0!3m2!1sen!2sph!4v1770104430928!5m2!1sen!2sph" 
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
                <div class="location-info">
                    <h2 class="location-name">MANILA</h2>
                    <div class="info-item">
                        <div class="info-label">Address:</div>
                        <div class="info-value">917-919 Luzon Street, Barangay 260 Zone 024 1012 Tondo Ⅰ/Ⅱ NCR, City of Manila, First District, Philippines</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">
                            <a href="tel:+6328584958">(+632) 8584-4958</a><br>
                            <a href="tel:+6328243287">(+632) 8243-2873</a>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fax:</div>
                        <div class="info-value">
                            <a href="tel:+6328584958">(+632) 8584-4958</a><br>
                            <a href="tel:+6328252922">(+632) 8252-9224</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calabarzon Location -->
            <div class="location-card">
                <iframe 
                    class="location-map"
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.360155465704!2d121.05461077455868!3d13.757141397170837!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd0507ab7e208b%3A0x83becc02b32349e7!2sAndison%20Industrial%20Sales%20Inc.!5e0!3m2!1sen!2sph!4v1770104315437!5m2!1sen!2sph"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
                <div class="location-info">
                    <h2 class="location-name">CALABARZON</h2>
                    <div class="info-item">
                        <div class="info-label">Address:</div>
                        <div class="info-value">29B P. Zamora Street, Barangay 16, 4200 Batangas City, Batangas Philippines</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone:</div>
                        <div class="info-value">
                            <a href="tel:+63434254126">(+6343) 425 4126</a><br>

                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fax:</div>
                        <div class="info-value">
                            <a href="tel:+63437233198">(+6343) 723 3198</a>
                        </div>
                    </div>
                   
                    <div class="contact-note">
                        Do you have questions about how we can help your company? Send us an email and we'll get in touch shortly.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-copyright">
                <p>&copy; 2026 <?php echo $company_name; ?>. All rights reserved.</p>
            </div>
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="#sitemap">Sitemap</a>
            </div>
        </div>
    </footer>

    <script>
        (function(){
            var browseToggle = document.getElementById('browseToggle');
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlay');
            var closeBtn = document.getElementById('closeSidebar');

            function openSidebar(){
                sidebar.classList.add('active');
                overlay.classList.add('active');
                sidebar.setAttribute('aria-hidden','false');
                overlay.setAttribute('aria-hidden','false');
            }

            function closeSidebar(){
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                sidebar.setAttribute('aria-hidden','true');
                overlay.setAttribute('aria-hidden','true');
            }

            if(browseToggle){
                browseToggle.addEventListener('click', function(e){ e.preventDefault(); openSidebar(); });
            }
            if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if(overlay) overlay.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeSidebar(); });
        })();

        // Contact dropdown functionality
        (function(){
            var dropdowns = document.querySelectorAll('.contact-dropdown');
            dropdowns.forEach(function(dd){
                var pop = dd.querySelector('.contact-popover');
                var link = dd.querySelector('.contact-link');
                dd.addEventListener('keydown', function(e){
                    if(e.key === 'Escape') { link.blur(); pop.setAttribute('aria-hidden','true'); }
                });
                dd.addEventListener('focusin', function(){ pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true'); });
                dd.addEventListener('focusout', function(){ setTimeout(function(){ if(!dd.contains(document.activeElement)){ pop.setAttribute('aria-hidden','true'); dd.setAttribute('aria-expanded','false'); } }, 10); });
                dd.addEventListener('mouseenter', function(){ 
                    if(dd.classList.contains('closed')) return;
                    pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true'); 
                });
                dd.addEventListener('mouseleave', function(){ pop.setAttribute('aria-hidden','true'); dd.setAttribute('aria-expanded','false'); dd.classList.remove('closed'); });

                var closeBtn = dd.querySelector('.contact-close');
                if(closeBtn){
                    closeBtn.addEventListener('click', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        pop.setAttribute('aria-hidden','true');
                        dd.setAttribute('aria-expanded','false');
                        dd.classList.add('closed');
                        document.activeElement.blur();
                    });
                }
            });
        })();
    </script>
    <script>
        // Sidebar sublist toggle behavior with persistent state
        (function(){
            var toggles = document.querySelectorAll('.sub-toggle');
            toggles.forEach(function(btn){
                var targetId = btn.getAttribute('aria-controls');
                var list = document.getElementById(targetId);
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
                        try { localStorage.setItem(storageKey,'false'); } catch(e){}
                    } else {
                        btn.setAttribute('aria-expanded','true');
                        list.classList.remove('collapsed');
                        try { localStorage.setItem(storageKey,'true'); } catch(e){}
                    }
                });
            });
        })();
        // Nested sublist toggle behavior
        (function(){
            var nestedToggles = document.querySelectorAll('.nested-toggle');
            nestedToggles.forEach(function(btn){
                var targetId = btn.getAttribute('aria-controls');
                var list = document.getElementById(targetId);
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
                        try { localStorage.setItem(storageKey,'false'); } catch(e){}
                    } else {
                        btn.setAttribute('aria-expanded','true');
                        list.classList.remove('collapsed');
                        try { localStorage.setItem(storageKey,'true'); } catch(e){}
                    }
                });
            });
        })();
    </script>
    <script>
        // BRAND DROPDOWN NAVIGATION (priority handler)
        (function(){
            document.addEventListener('click', function(e){
                var brandLink = e.target.closest('.nav-list li:nth-child(3) .nav-dropdown a');
                if(brandLink){
                    e.preventDefault();
                    e.stopPropagation();
                    var href = brandLink.getAttribute('href');
                    if(href){ window.location.href = href; }
                    return;
                }
            }, true);
        })();
    </script>
    <script>
        // PAGE TRANSITION EFFECTS (match brands/home)
        (function(){
            document.addEventListener('click', function(e){
                var link = e.target.closest('a[href*=".php"], a[href^="#"]');
                if(!link) return;
                var href = link.getAttribute('href');
                if(!href) return;
                if(href.startsWith('#') || href.startsWith('javascript:')) return;
                if(!href.includes('.php')) return;
                e.preventDefault();
                document.body.style.animation = 'none';
                setTimeout(function(){ window.location.href = href; }, 0);
            });
            window.addEventListener('load', function(){ document.body.style.animation = 'none'; });
        })();
    </script>

    <script>
        // ============================================
        // ACTIVE SIDEBAR CATEGORY HIGHLIGHTING
        // ============================================
        (function(){
            var currentPath = window.location.pathname;
            var sidebar = document.getElementById('sidebar');
            if(!sidebar) return;

            // Extract category from URL path

        // ============================================
        // ACTIVE SIDEBAR CATEGORY HIGHLIGHTING
        // ============================================
        var currentPath = window.location.pathname.toLowerCase();
        var sidebar = document.getElementById('sidebar');
        if(sidebar) {
            var pathParts = currentPath.split('/').filter(function(p) { return p && p !== 'andison-1'; });
            var currentCategory = null;
            
            var categoryList = [
                'arc-welding-machine',
                'arc-welding-robots',
                'batteries',
                'drilling-and-lifting',
                'gas-detectors',
                'portable-ventilators',
                'power-tools',
                'protection',
                'welding-accessories',
                'welding-consumables'
            ];
            
            for(var i = 0; i < pathParts.length; i++) {
                if(categoryList.indexOf(pathParts[i]) !== -1) {
                    currentCategory = pathParts[i];
                    break;
                }
            }

            if(currentCategory){
                var links = sidebar.querySelectorAll('.sidebar-list > li > a');
                links.forEach(function(link){
                    var href = link.getAttribute('href').toLowerCase();
                    if(href.includes(currentCategory)){
                        link.classList.add('active');
                    }
                });
            }
        }
    </script>

    <script>
        // Mini Sidebar and Popover functionality
        var miniSidebar = document.getElementById('miniSidebar');
        var mainSidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('overlayBackdrop');
        var expandBtn = document.getElementById('expandSidebar');
        var browseToggle = document.getElementById('browseToggle');
        var miniIcons = document.querySelectorAll('.mini-sidebar-icon');
        var miniPopover = document.getElementById('miniPopover');
        var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;
        var popoverList = miniPopover ? miniPopover.querySelector('.mini-popover-list') : null;
        var currentPopoverKey = null;

        function updateBrowseToggleVisibility() {
            if(!browseToggle) return;
            if(window.innerWidth <= 1024) {
                browseToggle.classList.add('active');
            } else {
                browseToggle.classList.remove('active');
            }
        }

        if(browseToggle) updateBrowseToggleVisibility();
        window.addEventListener('resize', updateBrowseToggleVisibility);

        function getCategoryKeyFromTarget(dataTarget) {
            if (!dataTarget) return null;
            var keys = ['arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting','gas-detectors','portable-ventilators','power-tools','protection','welding-accessories','welding-consumables'];
            for (var i=0;i<keys.length;i++) { if (dataTarget.indexOf('/'+keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'/') !== -1) return keys[i]; }
            return null;
        }
        function getCategoryTitle(key) {
            var map = {'arc-welding-machine': 'Arc Welding Machines','arc-welding-robots': 'Arc Welding Robots','batteries': 'Batteries','drilling-and-lifting': 'Drilling and Lifting','gas-detectors': 'Gas Detectors','portable-ventilators': 'Portable Ventilators','power-tools': 'Power Tools','protection': 'Personal Protective Equipment','welding-accessories': 'Welding Accessories','welding-consumables': 'Welding Consumables'};
            return map[key] || 'Categories';
        }
        function getPopoverItems(key) {
            var base = '.';
            var maps = {
                'arc-welding-robots': [{label: 'G3 Controller Series', href: base + '/arc-welding-robots/g3-controller-series.php'},{label: 'G4 Controller Series', href: base + '/arc-welding-robots/g4-controller-series.php'},{label: 'Featured Products and Solutions', href: base + '/arc-welding-robots/featured-products-and-solution.php'},{label: 'Robot System Peripherals', href: base + '/arc-welding-robots/robot-system-peripherals.php'}],
                'arc-welding-machine': [{label: 'MIG Welding Machine', href: base + '/arc-welding-machine/mig-welding-machine.php'},{label: 'CO1/MAG Welding Machine', href: base + '/arc-welding-machine/co1-mag-welding-machine.php'},{label: 'STUD Welding Machine', href: base + '/arc-welding-machine/stud-welding-machine.php'},{label: 'TIG Welding Machine', href: base + '/arc-welding-machine/tig-welding-machine.php'},{label: 'Plasma Cutting Machine', href: base + '/arc-welding-machine/plasma-cutting-machine.php'}],
                'batteries': [{label: 'Maintenance Free', href: base + '/batteries/maintenance-free.php'},{label: 'Low Maintenance', href: base + '/batteries/low-maintenance.php'},{label: 'Special Batteries', href: base + '/batteries/special-batteries.php'}],
                'drilling-and-lifting': [{label: 'Lifting', href: base + '/drilling-and-lifting/lifting.php'},{label: 'Magnetic Drill', href: base + '/drilling-and-lifting/magnetic-drill.php'},{label: 'Cutters', href: base + '/drilling-and-lifting/cutters.php'}],
                'gas-detectors': [{label: 'Single Gas Detector', href: base + '/gas-detectors/single-gas-detector.php'},{label: 'Multi Gas Detector', href: base + '/gas-detectors/multi-gas-detector.php'},{label: 'Portable Gas Detectors', href: base + '/gas-detectors/portable-gas-detectors.php'},{label: 'Docking and Data Management', href: base + '/gas-detectors/docking-data-management.php'},{label: 'Calibration Gas and Regulators', href: base + '/gas-detectors/calibration-gas-regulators.php'}],
                'power-tools': [{label: 'Grinder', href: base + '/power-tools/grinder.php'},{label: 'Saw', href: base + '/power-tools/saw.php'},{label: 'Drill and Wrench', href: base + '/power-tools/drill-and-wrench.php'},{label: 'Rotary and Demolition Hammer', href: base + '/power-tools/rotary-and-demolition-hammer.php'},{label: 'Accessories', href: base + '/power-tools/accessories.php'}],
                'portable-ventilators': [{label: 'Electric Driven', href: base + '/portable-ventilators/electric-driven.php'},{label: 'Pneumatic Driven', href: base + '/portable-ventilators/pneumatic-driven.php'}],
                'protection': [{label: 'Eye Protection', href: base + '/protection/eye-protection.php'},{label: 'Hand Protection', href: base + '/protection/hand-protection.php'},{label: 'Hearing & Respiratory Protection', href: base + '/protection/hearing-respiratory-protection.php'},{label: 'Welding Head and Face Protection', href: base + '/protection/welding-head-and-face-protection.php'},{label: 'Body Protection', href: base + '/protection/body-protection.php'}],
                'welding-accessories': [{label: 'Welding Electrode Oven', href: base + '/welding-accessories/welding-electrode-oven.php'},{label: 'Non-Destructive Crack Detection', href: base + '/welding-accessories/non-destructive-crack-detection.php'},{label: 'Gas Saving Regulator', href: base + '/welding-accessories/gas-saving-regulator.php'},{label: 'Gas Cutting Equipment', href: base + '/welding-accessories/gas-cutting-equipment.php'},{label: 'Industrial Markers', href: base + '/welding-accessories/industrial-markers.php'},{label: 'Measuring Gauge', href: base + '/welding-accessories/measuring-gauge.php'},{label: 'Others', href: base + '/welding-accessories/others.php'}],
                'welding-consumables': [{label: 'Kobelco', href: base + '/welding-consumables/kobelco.php'},{label: 'Metrode', href: base + '/welding-consumables/metrode.php'}]
            };
            return maps[key] || [];
        }
        function renderPopover(key) {
            if (!miniPopover || !popoverList) return;
            popoverList.innerHTML = '';
            var items = getPopoverItems(key);
            items.forEach(function(it){
                var li = document.createElement('li');
                li.className = 'mini-popover-item';
                li.innerHTML = '<span class="square"></span><a href="'+ it.href +'">'+ it.label +'</a>';
                popoverList.appendChild(li);
            });
            if (popoverTitle) popoverTitle.textContent = getCategoryTitle(key);
        }
        function positionPopoverForIcon(icon) {
            if (!miniPopover || !icon) return;
            miniPopover.style.left = '-9999px';
            miniPopover.style.top = '-9999px';
            miniPopover.classList.add('show');
            var rect = icon.getBoundingClientRect();
            var pw = miniPopover.offsetWidth;
            var ph = miniPopover.offsetHeight;
            var iconCenterY = rect.top + rect.height / 2;
            var left = Math.round(rect.right + 14);
            var top = Math.round(iconCenterY - ph / 2);
            if (left + pw + 12 > window.innerWidth) left = Math.round(rect.left - pw - 14);
            var headerHeight = 170;
            var minTop = headerHeight + 12;
            var maxTop = window.innerHeight - ph - 12;
            if (top < minTop) top = minTop;
            if (top > maxTop) top = maxTop;
            var arrowOffset = iconCenterY - top - 26;
            miniPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');
            miniPopover.style.left = left + 'px';
            miniPopover.style.top = top + 'px';
        }
        function hidePopover() {
            if (!miniPopover) return;
            miniPopover.classList.remove('show');
            miniPopover.setAttribute('aria-hidden', 'true');
            currentPopoverKey = null;
        }
        function showPopoverForKey(key, icon) {
            if (!miniPopover) return;
            if (currentPopoverKey === key && miniPopover.classList.contains('show')) {
                hidePopover();
                return;
            }
            renderPopover(key);
            positionPopoverForIcon(icon);
            miniPopover.classList.add('show');
            miniPopover.setAttribute('aria-hidden', 'false');
            currentPopoverKey = key;
        }

        document.addEventListener('click', function(e){
            if (!miniPopover) return;
            if (!miniPopover.classList.contains('show')) return;
            if (e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;
            hidePopover();
        });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hidePopover(); });

        if(browseToggle) {
            browseToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var isMiniSidebarVisible = window.getComputedStyle(miniSidebar).display !== 'none';
                if(window.innerWidth > 1024 && isMiniSidebarVisible) {
                    miniSidebar.classList.toggle('expanded');
                    browseToggle.classList.toggle('expanded');
                } else {
                    if(mainSidebar.classList.contains('active')) {
                        mainSidebar.classList.remove('active');
                        backdrop.classList.remove('active');
                    } else {
                        mainSidebar.classList.add('active');
                        backdrop.classList.add('active');
                        mainSidebar.style.display = 'block';
                        backdrop.style.display = 'block';
                    }
                }
            });
        }

        expandBtn.addEventListener('click', function() {
            miniSidebar.classList.toggle('expanded');
            if(browseToggle) browseToggle.classList.toggle('expanded');
        });

        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar) {
            menuBar.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        var arrowHandler = function(e) {
            e.stopPropagation();
            e.preventDefault();
            var arrow = (e.target && e.target.closest('.sub-indicator')) || e.currentTarget;
            var icon = arrow ? arrow.closest('.mini-sidebar-icon') : null;
            if (!icon) return;
            var dataTarget = icon.getAttribute('data-target') || '';
            var categoryKey = getCategoryKeyFromTarget(dataTarget);
            if (!categoryKey) return;
            showPopoverForKey(categoryKey, icon);
        };
        
        document.querySelectorAll('.sub-indicator').forEach(function(arrow) {
            arrow.addEventListener('click', arrowHandler, true);
        });
        
        document.addEventListener('click', function(e) {
            if (e.target.closest('.sub-indicator')) arrowHandler(e);
        }, true);

        miniIcons.forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                if (e.target.closest('.sub-indicator')) {
                    e.stopPropagation();
                    return;
                }
                var target = this.getAttribute('data-target');
                if (target) window.location.href = target;
            }, true);
        });

        backdrop.addEventListener('click', function() {
            if(mainSidebar.classList.contains('active')) {
                mainSidebar.classList.remove('active');
                backdrop.classList.remove('active');
            }
        });

        var closeSidebarBtn = document.getElementById('closeSidebar');
        if(closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', function() {
                if(mainSidebar.classList.contains('active')) {
                    mainSidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                }
            });
        }
    </script>

    <script>
        // ============================================
        // UPDATE CART BADGE COUNT IN REAL-TIME
        // ============================================
        (function(){
            function updateCartBadge() {
                var badge = document.getElementById('cartBadge');
                if(!badge) return;
                
                var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                var count = items.length;
                
                if(count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            
            // Update on page load
            updateCartBadge();
            
            // Update on storage change (when items added from other pages)
            window.addEventListener('storage', updateCartBadge);
            
            // Update frequently to catch changes
            setInterval(updateCartBadge, 500);
        })();
    </script>
</body>
</html>


