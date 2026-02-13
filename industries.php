<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Industries - ANDISON INDUSTRIAL</title>
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
            padding-top: 142px;
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
            z-index: 1001;
            width: 100%;
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
            left: 50%;
            transform: translateX(-50%);
            top: -8px;
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
            height: 40px;
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

        .search-btn {
            display: none;
        }

        
        .inquiry-btn {
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
            color: #1a1a2e;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 217, 255, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .inquiry-btn:hover { 
            background: linear-gradient(135deg, #00E6FF 0%, #00C8F7 100%);
            box-shadow: 0 6px 20px rgba(0, 217, 255, 0.5);
            transform: translateY(-2px);
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
            padding-left: 160px; /* space for the left Browse toggle */
        }

        /* Pin the browse toggle to the left side of the nav area */
        .browse-toggle {
            position: absolute;
            left: 12px;
            top: 20%;
            transform: translateY(-50%);
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
        }

        .nav-list {
            list-style: none;
            display: flex;
            gap: 28px;
            margin: 0;
            padding: 0;
        }

        .nav-list li { position: relative; }

        .nav-list a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            padding: 12px 6px;
            display: block;
            transition: color 0.2s;
            position: relative;
        }

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

        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        .nav-list a:hover { color: #00d4aa; }

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
            border-bottom: none;
        }

        .nav-dropdown ul a:hover {
            background: #f0f5ff;
            color: #2b00d9;
            border-bottom: none;
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

        /* Shared Animation Keyframes (standardized) - DISABLED */

        /* Page transition keyframes - DISABLED */

        /* Overlay sidebar */
        .overlay-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.08);
            opacity: 0;
            visibility: hidden;
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .industries-section {
            padding: 60px 0;
        }

        .industries-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .industries-header h1 {
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 15px;
            color: #2b00d9;
        }

        .industries-header p {
            font-size: 16px;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .industries-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .industry-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            opacity: 1;
            transform: translateY(0);
            transition: box-shadow 0.3s, transform 0.3s;
            will-change: transform, opacity, box-shadow;
        }
        .industry-card:nth-of-type(1){ --i:1; }
        .industry-card:nth-of-type(2){ --i:2; }
        .industry-card:nth-of-type(3){ --i:3; }

        .industry-card:hover {
            box-shadow: 0 25px 50px rgba(43, 17, 219, 0.12);
            transform: translateY(-12px) scale(1.03);
            z-index: 1000;
        }

        .industry-card.reverse {
            direction: rtl;
        }

        .industry-card.reverse > * {
            direction: ltr;
        }

        .industry-content h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #2b00d9;
        }

        .industry-content p {
            color: #555;
            margin-bottom: 15px;
            line-height: 1.8;
        }

        .read-more-btn {
            background: transparent;
            color: #2b00d9;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .read-more-btn:hover {
            gap: 12px;
        }

        .read-more-btn::after {
            content: '▼';
            font-size: 12px;
            transition: transform 0.3s ease;
        }

        .read-more-btn.active::after {
            transform: rotate(180deg);
        }

        .expanded-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .expanded-content.show {
            max-height: 1000px;
        }

        .expanded-content-inner {
            padding-top: 20px;
            border-top: 1px solid #ddd;
            margin-top: 20px;
        }

        .expanded-content p {
            color: #555;
            margin-bottom: 15px;
            line-height: 1.8;
        }

        .products-list {
            margin-top: 15px;
            padding-left: 20px;
        }

        .products-list li {
            color: #555;
            margin-bottom: 8px;
            list-style-type: disc;
        }

        .industry-image img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        @media (max-width: 768px) {
            .industry-card {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .industry-card.reverse {
                direction: ltr;
            }

            .industries-header h1 {
                font-size: 32px;
            }

            .header-top {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
            }

            .nav-inner { justify-content: space-between; padding-left: 20px; }
            .nav-list { position: static; transform: none; left: auto; margin: 8px auto 0; justify-content: center; flex-wrap: wrap; }
            .browse-toggle { position: static; transform: none; left: auto; top: auto; padding: 6px 10px; }
        }

        /* Footer */
        footer {
            background: #2B11DB;
            color: white;
            padding: 40px 0;
            text-align: center;
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
        }
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: #00d4aa;
        }

        .footer-copyright {
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
        }
            justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s;
        }

        
        /* Shared animations and utilities */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes contentSlide { from { opacity:0; transform: translateX(-28px);} to { opacity:1; transform: translateX(0);} }
        @keyframes imageSlide { from { opacity:0; transform: translateX(18px) scale(1.04);} to { opacity:1; transform: translateX(0) scale(1);} }

        .industry-card { opacity:1; transform: translateY(0); }
        .industry-card:nth-child(1){ animation-delay: 0ms; } .industry-card:nth-child(2){ animation-delay:0ms; }
        .industry-card .industry-content { opacity: 1; transform: translateX(0); }
        .industry-card .industry-image { opacity: 1; transform: translateX(0); }

        .read-more-btn .arrow { transition: transform .32s ease; }
        .read-more-btn[aria-expanded="true"] .arrow { transform: rotate(90deg); }

        .industry-image img { opacity: 1; transform: scale(1); }

        /* Ensure header/navigation/footer do not animate or move */
        header, nav, footer, .header-top, .nav-inner, .browse-toggle, .nav-list, .right-actions, .footer-content {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        /* Prevent individual nav items from receiving reveal animations */
        .nav-list li { animation: none !important; opacity: 1 !important; transform: none !important; }

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
            overflow-y: hidden;
            overflow-x: hidden;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            pointer-events: auto;
        }

        .mini-sidebar.expanded {
            width: 280px;
            overflow-y: auto;
            overflow-x: hidden;
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
            pointer-events: auto;
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

        .browse-label {
            display: none;
            opacity: 0;
            transition: opacity 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mini-sidebar.expanded .browse-label {
            display: inline-block !important;
            opacity: 1;
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
            min-width: 56px;
            position: relative;
            z-index: 100;
            pointer-events: auto;
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
                <a href="inquirylist.php" class="inquiry-btn">INQUIRY LIST</a>
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
                        <a href="industries.php" class="active">Industries</a>
                        <div class="nav-dropdown">
                            <h4>Industries We Serve</h4>
                            <ul>
                                <li><a href="industries.php#motorvehicleindustry">Motor Vehicle Industry</a></li>
                                <li><a href="industries.php#metalfabricationandindustrial">Metal Fabrication and Industrial</a></li>
                                <li><a href="industries.php#powergeneration">Power Generation</a></li>
                                <li><a href="industries.php#oilandpetrochemicalindustry">Oil and Petrochemical Industry</a></li>
                                <li><a href="industries.php#miningindustries">Mining Industries</a></li>
                                <li><a href="industries.php#shipyard">Shipyard</a></li>
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
                        <a href="contact.php">Contact Us</a>
                        <div class="nav-dropdown">
                            <h4>Get In Touch</h4>
                            <p>Reach out to our team for inquiries, quotes, or technical support.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Mini Sidebar (Icon Bar) -->
   <div class="mini-sidebar active" id="miniSidebar">
        <div id="miniSidebarMenuBar" style="background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); border-radius: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <i class="bi bi-list" style="font-size: 18px; font-weight: 700; color: white;"></i>
            <span style="font-size: 13px; font-weight: 700; color: white; letter-spacing: 0.5px; white-space: nowrap;" class="browse-label">BROWSE CATEGORIES</span>
        </div>
        <div class="mini-sidebar-icon has-sub" data-target="./arc-welding-machine/arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./arc-welding-robots/arc-welding-robot.php" title="Arc Welding Robots"><i class="bi bi-robot"></i><span class="label">Arc Welding Robots</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./batteries/batteries.php" title="Batteries"><i class="bi bi-lightning-fill"></i><span class="label">Batteries</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./drilling-and-lifting/drilling-and-lifting.php" title="Drilling and Lifting"><i class="bi bi-hammer"></i><span class="label">Drilling and Lifting</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./gas-detectors/gas-detectors.php" title="Gas Detectors"><i class="bi bi-bullseye"></i><span class="label">Gas Detectors</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./portable-ventilators/portable-ventilators.php" title="Portable Ventilators"><i class="bi bi-fan"></i><span class="label">Portable Ventilators</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./power-tools/power-tools.php" title="Power Tools"><i class="bi bi-tools"></i><span class="label">Power Tools</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./protection/protection.php" title="Personal Protective Equipment"><i class="bi bi-shield-check"></i><span class="label">PPE</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./welding-accessories/welding-accessories.php" title="Welding Accessories"><i class="bi bi-gear"></i><span class="label">Welding Accessories</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="./welding-consumables/welding-consumables.php" title="Welding Consumables"><i class="bi bi-box"></i><span class="label">Welding Consumables</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <button class="mini-sidebar-toggle" id="expandSidebar" title="Toggle Sidebar"><i class="bi bi-chevron-right"></i></button>
    </div>

    <!-- Floating popover for mini sidebar subcategories -->
    <div id="miniPopover" class="mini-popover" aria-hidden="true">
        <div class="mini-popover-header">
            <div class="mini-popover-title">Personal Protective Equipment</div>
        </div>
        <div class="mini-popover-body">
            <ul class="mini-popover-list"></ul>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container">
        <section class="industries-section">
            <div class="industries-header">
                <h1>Our Industrial Expertise</h1>
                <p>Andison Industrial proudly serves a diverse range of sectors, delivering precision, innovation, and reliability to drive progress in every industry we touch.</p>
            </div>

            <div class="industries-grid">
                <!-- Motor Vehicle Industry -->
                <div class="industry-card">
                    <div class="industry-content">
                        <h2>Motor Vehicle Industry</h2>
                        <p>This industry manufactures automobiles, motorcycles, buses, and truck vans. They have a growing presence in the Philippine market, especially with the high demand for motorcycles. We offer a wide assortment of welding equipment and consumables necessary to produce world-class products.</p>
                        <button class="read-more-btn" onclick="toggleExpanded(this)">Read More</button>
                        <div class="expanded-content">
                            <div class="expanded-content-inner">
                                <p>Top multinational and domestic automotive companies choose our Panasonic Welding Systems to significantly improve weld quality and boost efficiency while reducing production costs. We provide consultation, training, maintenance, and reliable after-sales service to satisfy our customers’ expectations.</p>
                                <p><strong>Some of our products used in this industry are:</strong></p>
                                <ul class="products-list">
                                    <li>Arc Welding Robot with Power Source</li>
                                    <li>Arc Welding Equipment and Filler Metals</li>
                                    <li>Power Tools and Hand Tools</li>
                                    <li>Personal Protective Equipment (PPEs)</li>
                                </ul>
                                <p>For items not found on our website, kindly see our contact details and send us an inquiry.</p>
</p>
                            </div>
                        </div>
                    </div>
                    <div class="industry-image">
                        <img src="assets/HOME/photo_2026-02-03_10-30-46.jpg" alt="Motor Vehicle Industry">
                    </div>
                </div>

                <!-- Metal Fabrication and Industrial Projects -->
                <div class="industry-card reverse">
                    <div class="industry-content">
                        <h2>Metal Fabrication and Industrial</h2>
                        <p>Bridges, railways, refineries, shipyards, transmission lines, and other large-scale projects require steel frames and other metals to support the large infrastructures. Workers in the metal fabrication industry do welding, metal cutting, and fastening to assemble metal parts.</p>
                        <button class="read-more-btn" onclick="toggleExpanded(this)">Read More</button>
                        <div class="expanded-content">
                            <div class="expanded-content-inner">
                                <p>We supply our clients with equipment that makes quality welds in a short time. Our safety products protect workers from hazards such as working from heights, glaring lights, and hazardous gases.</p>
                                <p><strong>Some of our products used in this industry are:</strong></p>
                                <ul class="products-list">
                                    <li>Arc Welding Equipment and Filler Metals</li>
                                    <li>Pipe Cutting and Beveling Equipment</li>
                                    <li>Gas Welding and Cutting Equipment</li>
                                    <li>Power Tools and Hand Tools</li>
                                    <li>Personal Protective Equipment (PPEs)</li>
                                </ul>
                                <p>For items not found on our website, kindly see our <a href="#contact">contact details</a> and send your inquiry.</p>
                            </div>
                        </div>
                    </div>
                    <div class="industry-image">
                        <img src="assets/HOME/photo_2026-02-03_10-30-46 (2).jpg" alt="Metal Fabrication">
                    </div>
                </div>

                <!-- Power Generation -->
                <div class="industry-card">
                    <div class="industry-content">
                        <h2>Power Generation</h2>
                        <p>The Power Generation Industry is vital in a country’s growth. They must be a reliable partner in meeting the Philippine Energy Market’s ever-growing demands.</p>
                        <button class="read-more-btn" onclick="toggleExpanded(this)">Read More</button>
                        <div class="expanded-content">
                            <div class="expanded-content-inner">
                                <p>From plant maintenance, shutdown, building power transmission lines, and other infrastructures, we work closely with our clients and supply much-needed equipment, tools, and consumables to help finish their projects on schedule.</p>
                                <p><strong>Some of our products used in this industry are:</strong></p>
                                <ul class="products-list">
                                    <li>Arc Welding Equipment and Filler Metal</li>
                                    <li>Power Tools and Hand Tools</li>
                                    <li>Bearings, Maintenance Tools and Equipment</li>
                                    <li>Height Protection Equipment and other PPEs</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="industry-image">
                        <img src="assets/HOME/photo_2026-02-03_10-30-47.jpg" alt="Power Generation">
                    </div>
                </div>

                <!-- Oil and Petrochemical Industry -->
                <div class="industry-card reverse">
                    <div class="industry-content">
                        <h2>Oil and Petrochemical Industry</h2>
                        <p>Oil refineries use fractional distillation and other methods to process crude oil into more useful products like petroleum, gasoline, and other fuels. During the distillation, heavier by-products settle at the bottom. Petrochemical plants crack the by-products and further process them into more useful chemicals. Other industries use these petrochemicals to create different products.</p>
                        <button class="read-more-btn" onclick="toggleExpanded(this)">Read More</button>
                        <div class="expanded-content">
                            <div class="expanded-content-inner">
                                <p>Oil refineries use fractional distillation and other methods to process crude oil into more useful products like petroleum, gasoline, and other fuels. During the distillation, heavier by-products settle at the bottom. Petrochemical plants crack the by-products and further process them into more useful chemicals. Other industries use these petrochemicals to create everyday items including deodorants, perfumes, plastics, fertilizer, and car tires.</p>
                                <p><strong>Some of our products used in this industry are:</strong></p>
                                <ul class="products-list">
                                    <li>Arc Welding Equipment and Filler Metals</li>
                                    <li>Portable and Area Hazardous Gas Detectors</li>
                                    <li>Air Movers and Industrial Ventilators</li>
                                    <li>Bearings, Maintenance Tools and Equipment</li>
                                    <li>Pipe Cutting and Beveling Machine</li>
                                    <li>Power Tools and Hand Tools</li>
                                    <li>Personal Protective Equipment (PPEs)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="industry-image">
                        <img src="assets/HOME/photo_2026-02-03_10-30-47 (2).jpg" alt="Oil and Petrochemical">
                    </div>
                </div>

                <!-- Mining Industry -->
                <div class="industry-card">
                    <div class="industry-content">
                        <h2>Mining Industry</h2>
                        <p>This industry extracts coal, oil, metals, and other raw materials from the earth. These resources are processed by other industries to create products such as fuel, jewelry, construction materials, and everyday items. Mining is vital to the economy.</p>
                        <button class="read-more-btn" onclick="toggleExpanded(this)">Read More</button>
                        <div class="expanded-content">
                            <div class="expanded-content-inner">
                                <p>However, digging deep into the ground could pose a safety risk to workers without the proper equipment. We at Andison promote safety by providing high-quality PPEs. Our portfolio includes various <em>single and multi-gas detectors</em> including maintenance-free gas detectors. We provide clients with training on the proper use of the equipment to fully use its functions and ensure a safe working environment. We also do recalibration for the gas detectors.</p>
                                <p><strong>Some of our products used in this industry are:</strong></p>
                                <ul class="products-list">
                                    <li>Portable and Area Hazardous Gas Detectors</li>
                                    <li>PPEs and other Safety Products</li>
                                    <li>Air Movers and Ventilators</li>
                                    <li>Bearings, Maintenance Tools and Equipment</li>
                                    <li>Cordless Power Tools</li>
                                    <li>Floodlights and other Light Sources</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="industry-image">
                        <img src="assets/HOME/photo_2026-02-03_10-30-47 (3).jpg" alt="Mining Industry">
                    </div>
                </div>

                <!-- Shipyard -->
                <div class="industry-card reverse">
                    <div class="industry-content">
                        <h2>Shipyard</h2>
                        <p>World trade relies heavily on freight ships because it offers a high capacity at a low cost in transporting goods. Being an archipelago, the Philippines also uses ships to ferry people to the country’s many islands. Shipyards play a critical role in maintaining ships, ensuring they are seaworthy and safe.</p>
                        <button class="read-more-btn" onclick="toggleExpanded(this)">Read More</button>
                        <div class="expanded-content">
                            <div class="expanded-content-inner">
                                <p>Metal fabrication is an integral part of the shipbuilding industry. Andison has a wide product catalog for working with metal fabrication, providing clients with equipment ready for the job.</p>
                                <p><strong>Some of our products used in this industry are:</strong></p>
                                <ul class="products-list">
                                    <li>Arc Welding Equipment and Filler Metals</li>
                                    <li>Gas Welding and Cutting Equipment</li>
                                    <li>Air Movers and Industrial Ventilators</li>
                                    <li>Power Tools and Hand Tools</li>
                                    <li>Pipe Cutting and Beveling Machine</li>
                                    <li>Personal Protective Equipment (PPEs)</li>
                                    <li>Portable Gas Detectors</li>
                                </ul>
                                <p>For items not found on our website, kindly see our <a href="#contact">contact details</a> and send your inquiry.</p>
                            </div>
                        </div>
                    </div>
                    <div class="industry-image">
                        <img src="assets/HOME/photo_2026-02-03_10-30-47 (4).jpg" alt="Shipyard">
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="#sitemap">Sitemap</a>
            </div>
            <div class="footer-copyright">
                &copy; 2024 ANDISON INDUSTRIAL. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        function toggleExpanded(button) {
            const expandedContent = button.nextElementSibling;
            button.classList.toggle('active');
            expandedContent.classList.toggle('show');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const browseToggle = document.getElementById('browseToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const closeSidebar = document.getElementById('closeSidebar');
            const contactDropdown = document.querySelector('.contact-dropdown');
            const contactClose = document.querySelector('.contact-close');

            // Browse toggle
            if (browseToggle) {
                browseToggle.addEventListener('click', function() {
                    sidebar.classList.add('active');
                    overlay.classList.add('active');
                });
            }

            // Close sidebar
            if (closeSidebar) {
                closeSidebar.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }

            // Close on overlay click
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                });
            }

            // Contact dropdown close
            if (contactClose) {
                contactClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    contactDropdown.classList.add('closed');
                    setTimeout(() => {
                        contactDropdown.classList.remove('closed');
                    }, 3000);
                });
            }

            // Set active nav link
            const currentPage = 'industries.php';
            document.querySelectorAll('.nav-list a').forEach(link => {
                if (link.href.includes(currentPage)) {
                    link.classList.add('active');
                }
            });
        });
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
        // Brand dropdown navigation handler
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
        // ============================================
        // MINI SIDEBAR AND BROWSE TOGGLE FUNCTIONALITY
        // ============================================
        var miniSidebar = document.getElementById('miniSidebar');
        var mainSidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('overlay');
        var expandBtn = document.getElementById('expandSidebar');
        var browseToggle = document.getElementById('browseToggle');
        var miniIcons = document.querySelectorAll('.mini-sidebar-icon');
        var miniPopover = document.getElementById('miniPopover');
        var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;
        var popoverList = miniPopover ? miniPopover.querySelector('.mini-popover-list') : null;
        var currentPopoverKey = null;

        // Responsive function to show/hide browse toggle
        function updateBrowseToggleVisibility() {
            if(window.innerWidth <= 1024) {
                if(browseToggle) browseToggle.classList.add('active');
            } else {
                if(browseToggle) browseToggle.classList.remove('active');
            }
        }

        // Initialize on load
        if(browseToggle) updateBrowseToggleVisibility();

        // Update on window resize
        if(browseToggle) window.addEventListener('resize', updateBrowseToggleVisibility);

        // HIGHLIGHT CURRENT CATEGORY IN MINI SIDEBAR
        (function() {
            var currentPath = window.location.pathname.toLowerCase();
            var pathParts = currentPath.split('/').filter(function(p) { return p && p !== 'andison'; });
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
            
            // Find matching category in path
            for(var i = 0; i < pathParts.length; i++) {
                if(categoryList.indexOf(pathParts[i]) !== -1) {
                    currentCategory = pathParts[i];
                    break;
                }
            }
            
            // If on home page or no category found, don't highlight anything
            if(currentCategory) {
                // Remove any existing active-icon
                document.querySelectorAll('.mini-sidebar-icon.active-icon').forEach(function(el) {
                    el.classList.remove('active-icon');
                });
                
                // Find and highlight the matching category icon
                var icons = document.querySelectorAll('.mini-sidebar-icon[data-target]');
                icons.forEach(function(icon) {
                    var dataTarget = icon.getAttribute('data-target');
                    if(dataTarget && dataTarget.indexOf('/' + currentCategory + '/') !== -1) {
                        icon.classList.add('active-icon');
                    }
                });
            }
        })();

        // Helpers for popover
        function getCategoryKeyFromTarget(dataTarget) {
            if (!dataTarget) return null;
            var keys = [
                'arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting','gas-detectors','portable-ventilators','power-tools','protection','welding-accessories','welding-consumables'
            ];
            for (var i=0;i<keys.length;i++) { if (dataTarget.indexOf('/'+keys[i]+'/') !== -1) return keys[i]; }
            return null;
        }
        function getCategoryTitle(key) {
            var map = {
                'arc-welding-machine': 'Arc Welding Machines',
                'arc-welding-robots': 'Arc Welding Robots',
                'batteries': 'Batteries',
                'drilling-and-lifting': 'Drilling and Lifting',
                'gas-detectors': 'Gas Detectors',
                'portable-ventilators': 'Portable Ventilators',
                'power-tools': 'Power Tools',
                'protection': 'Personal Protective Equipment',
                'welding-accessories': 'Welding Accessories',
                'welding-consumables': 'Welding Consumables'
            };
            return map[key] || 'Categories';
        }
        function getPopoverItems(key) {
            var base = '.';
            var maps = {
                'arc-welding-robots': [
                    { label: 'G3 Controller Series', href: base + '/arc-welding-robots/g3-controller-series.php' },
                    { label: 'G4 Controller Series', href: base + '/arc-welding-robots/g4-controller-series.php' },
                    { label: 'Featured Products and Solutions', href: base + '/arc-welding-robots/featured-products-and-solution.php' },
                    { label: 'Robot System Peripherals', href: base + '/arc-welding-robots/robot-system-peripherals.php' }
                ],
                'arc-welding-machine': [
                    { label: 'CO2/MAG Welding Machine', href: base + '/arc-welding-machine/co2-mag-welding-machine.php' },
                    { label: 'MIG Welding Machine', href: base + '/arc-welding-machine/mig-welding-machine.php' },
                    { label: 'TIG Welding Machine', href: base + '/arc-welding-machine/tig-welding-machine.php' },
                    { label: 'Plasma Cutting Machine', href: base + '/arc-welding-machine/plasma-cutting-machine.php' },
                    { label: 'Stud Welding', href: base + '/arc-welding-machine/stud-welding-machine.php' },
                    { label: 'Accessories & Consumables', href: base + '/arc-welding-machine/accessories-and-consumables.php' }
                ],
                'batteries': [
                    { label: 'Maintenance Free', href: base + '/batteries/maintenance-free.php' },
                    { label: 'Low Maintenance', href: base + '/batteries/low-maintenance.php' },
                    { label: 'Special Batteries', href: base + '/batteries/special-batteries.php' }
                ],
                'drilling-and-lifting': [
                    { label: 'Material Handling & Lifting', href: base + '/drilling-and-lifting/lifting.php' },
                    { label: 'Magnetic Drill', href: base + '/drilling-and-lifting/magnetic-drill.php' },
                    { label: 'Core Cutters', href: base + '/drilling-and-lifting/cutters.php' }
                ],
                'gas-detectors': [
                    { label: 'Single Gas Detector', href: base + '/gas-detectors/single-gas-detector.php' },
                    { label: 'Multi Gas Detector', href: base + '/gas-detectors/multi-gas-detector.php' },
                    { label: 'Portable Gas Detectors', href: base + '/gas-detectors/portable-gas-detectors.php' },
                    { label: 'Docking and Data Management', href: base + '/gas-detectors/docking-data-management.php' },
                    { label: 'Calibration Gas and Regulators', href: base + '/gas-detectors/calibration-gas-regulators.php' }
                ],
                'power-tools': [
                    { label: 'Grinder', href: base + '/power-tools/grinder.php' },
                    { label: 'Saw', href: base + '/power-tools/saw.php' },
                    { label: 'Drill and Wrench', href: base + '/power-tools/drill-and-wrench.php' },
                    { label: 'Rotary and Demolition Hammer', href: base + '/power-tools/rotary-and-demolition-hammer.php' },
                    { label: 'Accessories', href: base + '/power-tools/accessories.php' }
                ],
                'portable-ventilators': [
                    { label: 'Electric Driven', href: base + '/portable-ventilators/electric-driven.php' },
                    { label: 'Pneumatic Driven', href: base + '/portable-ventilators/pneumatic-driven.php' }
                ],
                'protection': [
                    { label: 'Eye Protection', href: base + '/protection/eye-protection.php' },
                    { label: 'Hand Protection', href: base + '/protection/hand-protection.php' },
                    { label: 'Hearing & Respiratory Protection', href: base + '/protection/hearing-respiratory-protection.php' },
                    { label: 'Welding Head and Face Protection', href: base + '/protection/welding-head-and-face-protection.php' },
                    { label: 'Body Protection', href: base + '/protection/body-protection.php' }
                ],
                'welding-accessories': [
                    { label: 'Welding Electrode Oven', href: base + '/welding-accessories/welding-electrode-oven.php' },
                    { label: 'Non-Destructive Crack Detection', href: base + '/welding-accessories/non-destructive-crack-detection.php' },
                    { label: 'Gas Saving Regulator', href: base + '/welding-accessories/gas-saving-regulator.php' },
                    { label: 'Gas Cutting Equipment', href: base + '/welding-accessories/gas-cutting-equipment.php' },
                    { label: 'Industrial Markers', href: base + '/welding-accessories/industrial-markers.php' },
                    { label: 'Measuring Gauge', href: base + '/welding-accessories/measuring-gauge.php' },
                    { label: 'Others', href: base + '/welding-accessories/others.php' }
                ],
                'welding-consumables': [
                    { label: 'Kobelco', href: base + '/welding-consumables/kobelco.php' },
                    { label: 'Metrode', href: base + '/welding-consumables/metrode.php' }
                ]
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

            if (left + pw + 12 > window.innerWidth) {
                left = Math.round(rect.left - pw - 14);
            }

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

        // Set up click handler for browse/expand button
        var sidebarToggleBtn = browseToggle || expandBtn;
        if(sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var isMiniSidebarVisible = window.getComputedStyle(miniSidebar).display !== 'none';
                if(window.innerWidth > 1024 && isMiniSidebarVisible) {
                    miniSidebar.classList.toggle('expanded');
                    if(browseToggle) browseToggle.classList.toggle('expanded');
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

        if(expandBtn) {
            expandBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar) {
            menuBar.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        // Category icon click handler - navigate to category page
        document.querySelectorAll('.mini-sidebar-icon').forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                // Skip if clicking the arrow/sub-indicator
                if (e.target.closest('.sub-indicator')) {
                    return;
                }
                
                e.preventDefault();
                e.stopPropagation();
                
                var dataTarget = icon.getAttribute('data-target') || '';
                if (dataTarget) {
                    window.location.href = dataTarget;
                }
            }, false);
        });
        
        // Direct click handler for mini sidebar icons to ensure navigation
        document.addEventListener('click', function(e) {
            var icon = e.target.closest('.mini-sidebar-icon');
            if (!icon) return;
            
            // Skip if clicking the arrow indicator
            if (e.target.closest('.sub-indicator')) return;
            
            var dataTarget = icon.getAttribute('data-target') || '';
            if (dataTarget) {
                window.location.href = dataTarget;
            }
        }, false);

        // Sub-indicator (arrow) click handler - show popover
        var arrowHandler = function(e) {
            e.stopPropagation();
            e.preventDefault();
            var arrow = e.target.closest('.sub-indicator');
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

        // Hover handlers for mini-sidebar icons - show popover on mouseenter
        var popoverHideTimeout = null;
        
        document.querySelectorAll('.mini-sidebar-icon.has-sub').forEach(function(icon) {
            icon.addEventListener('mouseenter', function(e) {
                clearTimeout(popoverHideTimeout);
                var dataTarget = icon.getAttribute('data-target') || '';
                var categoryKey = getCategoryKeyFromTarget(dataTarget);
                if (!categoryKey) return;
                showPopoverForKey(categoryKey, icon);
            });
            
            icon.addEventListener('mouseleave', function(e) {
                popoverHideTimeout = setTimeout(function() {
                    hidePopover();
                }, 150);
            });
        });

        // Keep popover visible when hovering over the popover itself
        if (miniPopover) {
            miniPopover.addEventListener('mouseenter', function(e) {
                clearTimeout(popoverHideTimeout);
            });
            
            miniPopover.addEventListener('mouseleave', function(e) {
                popoverHideTimeout = setTimeout(function() {
                    hidePopover();
                }, 150);
            });
        }

        // ============================================
        // MAIN SIDEBAR CATEGORY AND SUBCATEGORY LINKS
        // ============================================
        // Ensure all sidebar links navigate properly
        document.querySelectorAll('.sidebar-list li').forEach(function(item) {
            var link = item.querySelector('a');
            var toggle = item.querySelector('.sub-toggle');
            
            if (link) {
                // Make sure the link is clickable and navigates
                link.style.cursor = 'pointer';
                link.addEventListener('click', function(e) {
                    // If the toggle button was clicked, don't navigate
                    if (e.target.closest('.sub-toggle')) {
                        return;
                    }
                    // Otherwise, navigate to the link
                    var href = link.getAttribute('href');
                    if (href) {
                        setTimeout(function() {
                            window.location.href = href;
                        }, 50);
                    }
                });
            }
        });
        
        // Handle subcategory links
        document.querySelectorAll('.sidebar-sublist li').forEach(function(item) {
            var link = item.querySelector('a');
            if (link && !item.classList.contains('has-nested-sub')) {
                link.addEventListener('click', function(e) {
                    var href = link.getAttribute('href');
                    if (href) {
                        setTimeout(function() {
                            window.location.href = href;
                        }, 50);
                    }
                });
            }
        });
        
        // Handle nested subcategory links
        document.querySelectorAll('.sidebar-nested-sublist a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                var href = link.getAttribute('href');
                if (href) {
                    setTimeout(function() {
                        window.location.href = href;
                    }, 50);
                }
            });
        });
        
        // Close sidebar when navigating
        document.querySelectorAll('.sidebar-list a, .sidebar-sublist a, .sidebar-nested-sublist a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Close sidebar after navigation
                setTimeout(function() {
                    if (mainSidebar.classList.contains('active')) {
                        mainSidebar.classList.remove('active');
                        backdrop.classList.remove('active');
                    }
                }, 50);
            });
        });
    </script>
    </body>
</html>
