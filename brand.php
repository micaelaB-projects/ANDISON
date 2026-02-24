
<?php
require_once __DIR__ . '/andison/includes/analytics.php';
andison_track_visit('brand');
$_btrack = isset($_GET['name']) ? trim(strip_tags($_GET['name'])) : '';
if ($_btrack) andison_track_brand_visit($_btrack);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    $brand_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Brand';
    $highlight_product = isset($_GET['product']) ? trim((string)$_GET['product']) : '';
    ?>
    <title><?php echo $brand_name; ?> - ANDISON INDUSTRIAL</title>
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
            padding-left: 80px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: padding-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body.sidebar-expanded {
            padding-left: 280px;
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
        /* Contact popover */
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

        /* when user explicitly closes, keep hidden until they move away */
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

        .search-btn {
            display: none;
        }

        .inquiry-btn,
        .cart-icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00E5C8 0%, #347aec 100%);
            position: relative;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,188,212,0.4);
            gap: 8px;
        }

        .inquiry-btn:hover,
        .cart-icon-wrapper:hover {
            background: linear-gradient(135deg, #00ACC1, #00796B);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,188,212,0.5);
            color: white;
        }

        .cart-badge {
            background: #c70d0d;
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(199,13,13,0.5);
            position: static;
            margin-left: 2px;
        }

        .cart-badge.hidden {
            display: none;
        }

        .add-to-inquiry {
            background: linear-gradient(135deg, #00D7B3 0%, #00C9A0 100%);
            color: white;
            border: none;
            padding: 13px 26px;
            border-radius: 9px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 800;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            height: auto;
            min-height: 44px;
            line-height: 1.2;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 215, 179, 0.35);
            position: relative;
            z-index: 2;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            width: 100%;
        }

        .add-to-inquiry:hover {
            background: linear-gradient(135deg, #00C9A0, #00B08A);
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(0, 215, 179, 0.5);
            color: white;
        }

        .add-to-inquiry:active {
            transform: translateY(-1px);
        }

        .add-to-inquiry.already {
            background: linear-gradient(135deg, #4caf50 0%, #45a344 100%);
            color: white;
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
            flex-wrap: nowrap;
            align-items: center;
            min-height: 52px;
            gap: 0;
            justify-content: center;
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
            display: none;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            cursor: pointer;
            font-size: 15px;
            line: height 6px;;
        }

        .nav-list {
            list-style: none;
            display: flex;
            flex-wrap: nowrap;
            gap: 30px;
            margin: 0;
            padding: 0;
            width: 100%;
            justify-content: center;
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
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
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
            color: #2B11DB;
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

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 12px 20px !important;
            margin-top: 16px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 60px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 70px;
        }

        nav li:nth-child(3) .nav-dropdown ul a img {
            max-width: 85px;
            max-height: 45px;
            object-fit: contain;
            display: block;
        }

        /* Hero Section */
        .hero {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23888888" width="1200" height="600"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 60px 20px;
            min-height: 500px;
            max-height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin: 0 auto 40px auto;
            z-index: 1;
            max-width: 1400px;
        }

        .hero-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
            overflow: hidden;
        }

        .hero-slide {
            position: absolute;
            width: 40%;
            height: 80%;
            max-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.3;
            transition: all 0.1s ease;
            transform: translateX(0) scale(0.85);
            filter: blur(4px);
            overflow: hidden;
        }

        .hero-slide.prev {
            left: 8%;
            opacity: 0.35;
            transform: translateX(-50px) scale(0.8);
            filter: blur(5px);
        }

        .hero-slide.active {
            left: 30%;
            opacity: 1;
            transform: translateX(0) scale(1);
            filter: blur(0);
            z-index: 10;
        }

        .hero-slide.next {
            right: 8%;
            opacity: 0.35;
            transform: translateX(50px) scale(0.8);
            filter: blur(5px);
        }

        /* blurred full-bleed background taken from the slide's background-image */
        .hero-slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: inherit;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(15px) brightness(0.7) saturate(1.3);
            z-index: 0;
        }

        /* subtle dark overlay above the blur to improve text contrast */
        .hero-slide::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.2);
            z-index: 1;
        }

        /* centered clear image card on top of the blurred background */
        .hero-content {
            max-width: 900px;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
        }

        .hero-content h1,
        .hero-content p,
        .hero-content .cta-button {
            display: none;
        }

        .hero-thumb {
            width: 100%;
            height: 100%;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 12px;
            box-shadow: 0 18px 40px rgba(2,6,23,0.45);
            overflow: hidden;
            background-color: rgba(255,255,255,0.05);
        }

        .hero-content {
            width: 100%;
            height: 100%;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .hero-content h1,
        .hero-content p,
        .hero-content .cta-button {
            display: none;
        }

        .hero-indicators {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 20;
        }

        .hero-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: background 0.1s;
        }

        .hero-dot.active {
            background: rgba(255,255,255,0.9);
        }

        .hero-dot:hover {
            background: rgba(255,255,255,0.7);
        }

        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }

        .cta-button {
            background: #00d4aa;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 3px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.1s;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            background: #00b88a;
        }

        /* Section */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        section {
            padding: 60px 20px;
            position: relative;
            z-index: 10;
            background: white;
            text-align: center;
            max-width: 1400px;
            margin: 0 auto;
        }

        section h2 {
            text-align: center;
            font-size: 45px;
            margin-bottom: 20px;
            color: #2B11DB;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
    
        .section-description {
            text-align: center;
            max-width: 900px;
            margin: 0 auto 50px;
            color: #666;
            line-height: 1.8;
        }

        /* Product Highlights */
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(550px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .product-card {
            background: #f5f5f5;
            border-radius: 5px;
            overflow: hidden;
            transition: transform 0.1s, box-shadow 0.1s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .product-card.product-highlight {
            border: 3px solid #00D7B3 !important;
            box-shadow: 0 0 30px rgba(0, 215, 179, 0.6), inset 0 0 20px rgba(0, 215, 179, 0.1) !important;
            background: rgba(0, 215, 179, 0.02) !important;
            transform: scale(1.05);
            z-index: 100;
        }

        @keyframes highlightPulse {
            0% { 
                box-shadow: 0 0 20px rgba(0, 215, 179, 0.4), inset 0 0 20px rgba(0, 215, 179, 0.1);
                transform: scale(1.05);
            }
            50% { 
                box-shadow: 0 0 50px rgba(0, 215, 179, 0.8), inset 0 0 20px rgba(0, 215, 179, 0.2);
                transform: scale(1.08);
            }
            100% { 
                box-shadow: 0 0 20px rgba(0, 215, 179, 0.4), inset 0 0 20px rgba(0, 215, 179, 0.1);
                transform: scale(1.05);
            }
        }

        .product-card.product-highlight {
            animation: highlightPulse 1.5s ease-in-out 0s 3;
        }

        .product-image {
            width: 100%;
            height: 260px;
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f4ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ddd;
            font-size: 60px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .brand-page .product-card:hover .product-image {
            background: linear-gradient(135deg, #e8f4ff 0%, #d0ecff 100%);
            transform: scale(1.08);
        }

        .product-image iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .play-btn {
            width: 60px;
            height: 60px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            cursor: pointer;
            transition: background 0.1s;
        }

        .play-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .product-info {
            padding: 32px 24px 24px;
            background: white;
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 14px;
            align-items: center;
            justify-content: flex-start;
        }

        .product-info h3 {
            font-size: 19px;
            font-weight: 800;
            margin: 0;
            color: #2B11DB;
            text-align: center;
            letter-spacing: -0.4px;
            line-height: 1.3;
        }

        .product-info p {
            font-size: 12px;
            color: #999;
            line-height: 1.5;
            text-align: center;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }

        .product-info > div {
            width: 100%;
            margin-top: auto;
            padding-top: 8px;
        }

        /* Featured Section */
        .featured-section {
            background: linear-gradient(135deg, #e0f7f4 0%, #d0f0ec 100%);
            padding: 50px 30px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .featured-badge {
            display: inline-block;
            background: #00d4aa;
            color: white;
            padding: 5px 12px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .featured-content h3 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #333;
            text-align: center;
        }

        .featured-content p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.8;
            text-align: center;
        }

        .featured-btn {
            background: #00D7B3;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 3px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.1s;
        }

        .featured-btn:hover {
            background: #00B899;
        }

        .featured-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #0066cc 0%, #82a2c9 100%);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
        }

        /* Footer - scrolls naturally with page content */
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
            text-align: center;
        }

        body.sidebar-expanded .footer-content {
            width: 100%;
            margin: 0;
            padding: 0 20px;
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
            transition: color 0.1s;
        }

        .footer-links a:hover {
            color: #00d4aa;
        }

        .footer-copyright {
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            /* Single row: logo | search | inquiry | contact */
            .header-top {
                display: flex;
                flex-direction: row;
                flex-wrap: nowrap;
                align-items: center;
                gap: 8px;
                padding: 0 10px;
                margin-bottom: 8px;
            }

            .logo {
                flex: 0 0 auto;
            }

            .logo-box img {
                height: 36px;
            }

            .search-bar {
                position: static;
                transform: none;
                flex: 1 1 0;
                min-width: 0;
                width: auto;
                max-width: none;
                margin: 0;
            }

            .search-bar .search-field {
                width: 100%;
            }

            .search-bar input {
                width: 100%;
                height: 36px;
                font-size: 12px;
                padding: 6px 8px 6px 30px;
            }

            .search-bar .search-field::before {
                font-size: 13px;
                left: 8px;
            }

            .right-actions {
                flex: 0 0 auto;
                display: flex;
                flex-direction: row;
                align-items: center;
                gap: 8px;
                margin-left: 8px;
                margin-right: 8px;
                padding-right: 8px;
            }

            .inquiry-btn,
            .cart-icon-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 6px !important;
                font-size: 28px !important;
                position: relative;
            }

            .inquiry-btn .btn-text { display: none; }
            .inquiry-btn .btn-icon { font-size: 28px; }

            .cart-badge {
                background: #2196F3 !important;
                box-shadow: 0 2px 8px rgba(33,150,243,0.5) !important;
                width: 26px !important;
                height: 26px !important;
                font-size: 13px !important;
                position: absolute !important;
                top: -4px !important;
                right: -8px !important;
                margin-left: 0 !important;
            }

            .cart-badge.hidden { display: inline-flex !important; }

            .header-contact {
                display: none;
            }

            nav ul {
                flex-wrap: nowrap;
                gap: 0;
            }

            nav li {
                margin-right: 0;
            }

            .nav-inner {
                padding-left: 0;
                padding-right: 0;
                gap: 0;
                min-height: auto;
                overflow-x: hidden;
                overflow-y: visible;
                justify-content: center;
                flex-wrap: wrap;
            }

            .nav-inner::-webkit-scrollbar { display: none; }

            .nav-list {
                gap: 0;
                flex-wrap: wrap;
                flex-shrink: 1;
                justify-content: center;
            }

            .nav-list > li > a {
                white-space: normal;
                font-size: 11px;
                padding: 10px 8px;
            }

            .hero h1 {
                font-size: 32px;
            }

            .featured-section {
                grid-template-columns: 1fr;
            }

            .footer-links {
                flex-direction: column;
                gap: 10px;
            }

            .sidebar-overlay {
                width: 95%;
                max-width: 100%;
                max-height: 95vh;
                padding: 28px 20px;
            }

            .sidebar-overlay h3 {
                font-size: 16px;
                margin-bottom: 20px;
            }

            .sidebar-list a {
                font-size: 14px;
                padding: 14px 10px;
            }

            .sidebar-sublist a {
                font-size: 13px;
            }
        }

        /* Overlay sidebar (full-height left panel) */
        .overlay-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s;
            z-index: 60;
            pointer-events: none;
        }

        .overlay-backdrop.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .sidebar-overlay {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            width: 380px;
            max-width: 90%;
            background: #fff;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
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
        .sidebar-close:hover {
            color: #374151;
        }

        /* ============================================
           ANIMATIONS
           ============================================ */

        /* 1. HOVER EFFECTS */
        @keyframes hoverGlow {
            0% { box-shadow: 0 0 0px rgba(0, 212, 170, 0); }
            100% { box-shadow: 0 0 20px rgba(0, 212, 170, 0.4); }
        }

        @keyframes hoverScale {
            from { transform: scale(1); }
            to { transform: scale(1.05); }
        }

        @keyframes buttonBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .product-card {
            transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            opacity: 1;
            transform: translateY(0);
            will-change: transform, opacity, box-shadow;
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 25px 50px rgba(43,17,219,0.2);
            z-index: 1000;
        }

        .featured-btn:hover,
        .cta-button:hover {
            animation: buttonBounce 0.6s ease;
        }

        .nav-list a:hover {
            animation: hoverScale 0.3s ease;
        }

        .inquiry-btn:hover {
            animation: hoverGlow 0.4s ease forwards;
        }

        /* 2. SCROLLING ANIMATIONS */
        /* Use shared fadeUp keyframe for consistent reveals */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .scroll-animate { opacity: 0; transform: translateY(40px); transition: opacity 0s ease, transform 0s ease; }
        .scroll-animate.visible { }

        /* Match brands.php staggered reveal timings (faster) */
        .product-card { opacity: 1; transform: translateY(0); will-change: transform,opacity; }
        .product-card:nth-of-type(1){ --i:1; }
        .product-card:nth-of-type(2){ --i:2; }

        section h2 { opacity: 1; }
        .section-description { opacity: 1; }
        .featured-section { opacity: 1; }

        /* 3. PAGE TRANSITIONS */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pageExit {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(20px);
            }
        }

        body {
            opacity: 1;
        }

        section {
            opacity: 1;
        }

        section:nth-of-type(1) { animation-delay: 0s; }
        section:nth-of-type(2) { animation-delay: 0.1s; }
        section:nth-of-type(3) { animation-delay: 0.2s; }
        section:nth-of-type(4) { animation-delay: 0.3s; }

        /* 4. SELF-DRAWING ANIMATIONS */
        @keyframes drawBorder {
            to {
                stroke-dashoffset: 0;
            }
        }

        @keyframes pulseGlow {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(0, 212, 170, 0.7);
            }
            50% {
                box-shadow: 0 0 0 10px rgba(0, 212, 170, 0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }

        .featured-badge {
            animation: pulseGlow 2s infinite;
        }

        .product-image {
            position: relative;
            overflow: hidden;
        }

        .product-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        /* 5. TEXT ANIMATIONS */
        @keyframes typeWriter {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }

        @keyframes blinkCursor {
            0%, 49% {
                border-right-color: transparent;
            }
            50%, 100% {
                border-right-color: #00d4aa;
            }
        }

        @keyframes textGradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes textFadeIn {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            animation: textFadeIn 0.8s ease;
        }

        .hero p {
            animation: textFadeIn 0.8s ease 0.2s both;
        }

        .product-info h3,
        .featured-content h3 {
            animation: textFadeIn 0.6s ease;
            position: relative;
        }

        .product-info h3::after,
        .featured-content h3::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(90deg, #2B11DB, #00d4aa, transparent);

            border-radius: 2px;
        }

        /*
        .footer-links a {
            position: relative;
            animation: textFadeIn 0.6s ease;
        }

        .footer-links a::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #00d4aa;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::before {
            width: 100%;
        }
        */

        /* Stagger text animations */
        .nav-list li { opacity: 1; }

        .nav-list li:nth-child(1) { animation-delay: 0.1s; }
        .nav-list li:nth-child(2) { animation-delay: 0.2s; }
        .nav-list li:nth-child(3) { animation-delay: 0.3s; }
        .nav-list li:nth-child(4) { animation-delay: 0.4s; }
        .nav-list li:nth-child(5) { animation-delay: 0.5s; }
        .nav-list li:nth-child(6) { animation-delay: 0.6s; }

        /* Smooth transitions for all interactive elements */
        a, button, input, [role="button"] {
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @media (max-width: 768px) {
            .main-wrapper {
                grid-template-columns: 1fr;
                padding: 0 12px;
            }

            .sidebar {
                position: static;
            }
            .nav-inner { padding-left: 50px; padding-right: 6px; min-height: 40px; overflow-x: auto; overflow-y: visible; justify-content: flex-start; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
            .nav-inner::-webkit-scrollbar { display: none; }
            .nav-list { position: static; transform: none; left: auto; flex-wrap: nowrap; flex-shrink: 0; gap: 0; }
            .browse-toggle { position: static; transform: none; left: auto; top: auto; padding: 6px 10px; }
        }

        /* Global animation utilities (shared) */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }

        .reveal-hidden { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease; }
        .reveal { opacity: 1; transform: none; }
        .reveal-stagger > * { opacity: 0; transform: translateY(18px); }
        .reveal-stagger.revealed > * { opacity: 1; transform: none; transition: all .48s ease; }

        h1, .page-title { opacity: 1; }
        h1 + p, .page-subtitle { opacity: 1; }
        img:not(.no-anim) { opacity: 1; }

        @media (prefers-reduced-motion: reduce) {
            .reveal, .reveal-hidden, img { animation: none !important; transition: none !important; }
        }
        /* Ensure header/navigation do not animate or move */
        header, nav, .header-top, .nav-inner, .browse-toggle, .nav-list, .right-actions {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        /* Prevent individual nav items from receiving reveal animations */
        .nav-list li { animation: none !important; opacity: 1 !important; transform: none !important; }

        /* ============================================
           BRAND PAGE SPECIFIC PRODUCT STYLING
           ============================================ */
        
        /* Brand Container */
        .brand-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            flex: 1;
            text-align: center;
        }

        /* Breadcrumbs Navigation - Enhanced */
        .breadcrumbs-section {
            margin-bottom: 30px;
            text-align: center;
        }

        .breadcrumbs {
            display: inline-block;
            background: linear-gradient(135deg, rgba(0, 215, 179, 0.95) 0%, rgba(0, 180, 160, 0.95) 100%);
            padding: 12px 28px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 215, 179, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
        }

        .breadcrumbs-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .breadcrumbs-list li {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .breadcrumbs-list li::before {
            content: '🏠';
            font-size: 14px;
            display: none;
        }

        .breadcrumbs-list li:first-child::before {
            display: inline;
            content: '🏠';
        }

        .breadcrumbs-list li::after {
            content: '/';
            margin-left: 4px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 300;
            font-size: 13px;
        }

        .breadcrumbs-list li:last-child::after {
            content: '';
            margin-left: 0;
        }

        .breadcrumbs-list a {
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            padding: 4px 8px;
            border-radius: 4px;
            position: relative;
            text-transform: capitalize;
        }

        .breadcrumbs-list a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.8);
            transition: width 0.3s ease;
        }

        .breadcrumbs-list a:hover {
            background: rgba(255, 255, 255, 0.15);
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .breadcrumbs-list a:hover::after {
            width: 100%;
        }

        .breadcrumb-current {
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
            padding: 4px 12px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            text-transform: capitalize;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .brand-header {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .brand-header h1 {
            color: #2B11DB;
            font-size: 20px;
            margin-bottom: 10px;
            margin-top: 10px;
            display: block;
            text-align: center;
        }

        .brand-logo-container {
            min-height: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .brand-logo-container img {
            max-height: 120px;
            max-width: 100%;
            object-fit: contain;
            border-radius: 8px;
        }

        .brand-logo-container img[alt]{
            /* Fallback text styling */
        }

        .brand-header p {
            color: #666;
            font-size: 18px;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .brand-content {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .brand-content h2 {
            color: #2B11DB;
            font-size: 32px;
            margin-bottom: 20px;
            border-bottom: 3px solid #2B11DB;
            padding-bottom: 5px;
            text-align: center;
        }

        .brand-content h3 {
            color: #333;
            font-size: 24px;
            margin-top: 30px;
            margin-bottom: 15px;
            text-align: center;
        }

        .brand-content p {
            color: #555;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
            text-align: center;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .brand-content ul {
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 20px;
            max-width: 900px;
            text-align: left;
            display: inline-block;
        }

        .brand-content li {
            margin-bottom: 10px;
            color: #555;
        }

        /* ============================================
           FEATURE BOXES SECTION
           ============================================ */
        .feature-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin: 50px auto;
            max-width: 1200px;
            padding: 0 20px;
        }

        .feature-box {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .feature-box:hover {
            border-color: #00D7B3;
            box-shadow: 0 4px 16px rgba(0, 215, 179, 0.15);
            transform: translateY(-3px);
        }

        .feature-box i {
            font-size: 32px;
            color: #2B11DB;
            margin-bottom: 12px;
            display: block;
        }

        .feature-box h4 {
            color: #2B11DB;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .feature-box p {
            color: #666;
            font-size: 12px;
            margin: 0;
            line-height: 1.4;
        }

        /* ============================================
           BRAND CONTENT WITH FILTERS
           ============================================ */
        .brand-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
        }

        .brand-content h2 {
            grid-column: 1 / -1;
            color: #2B11DB;
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        /* ============================================
           LEFT SIDEBAR FILTERS
           ============================================ */
        .product-filters {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .filter-section {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
        }

        .filter-section h3 {
            color: #2B11DB;
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-section h3::before {
            content: '⚙';
            font-size: 12px;
            opacity: 0.7;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            cursor: pointer;
        }

        .filter-option:last-child {
            margin-bottom: 0;
        }

        .filter-option input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #00D7B3;
        }

        .filter-option label {
            color: #555;
            font-size: 13px;
            cursor: pointer;
            flex: 1;
            margin: 0;
        }

        .filter-option span {
            color: #999;
            font-size: 11px;
        }

        .clear-filters-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #FF6B6B 0%, #FF5252 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .clear-filters-btn:hover {
            background: linear-gradient(135deg, #FF5252 0%, #FF3333 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        /* ============================================
           SEARCH/SORT AND VIEW CONTROLS
           ============================================ */
        .product-controls {
            grid-column: 2;
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 220px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 10px 12px 10px 36px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 13px;
            transition: border-color 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: #00D7B3;
        }

        .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 14px;
        }

        .sort-dropdown, .view-toggle {
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            color: #333;
            font-size: 13px;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }

        .sort-dropdown:hover, .sort-dropdown:focus,
        .view-toggle:hover, .view-toggle:focus {
            border-color: #00D7B3;
            outline: none;
        }

        .view-toggle {
            display: flex;
            gap: 1px;
            padding: 0;
            border: none;
        }

        .view-toggle button {
            padding: 9px 12px;
            background: #f0f0f0;
            border: 1px solid #e0e0e0;
            color: #666;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .view-toggle button.active {
            background: #2B11DB;
            color: white;
            border-color: #2B11DB;
        }

        .results-info {
            color: #666;
            font-size: 12px;
            font-weight: 500;
            padding: 6px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #00D7B3;
        }

        .results-info span {
            color: #2B11DB;
            font-weight: 600;
        }

        /* ============================================
           MAIN PRODUCT AREA
           ============================================ */
        .main-product-area {
            grid-column: 2;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 0;
            margin-bottom: 40px;
            padding: 0;
        }

        .brand-page .product-card {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: flex-start;
            gap: 0;
            padding: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 8px rgba(43, 17, 219, 0.05);
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
        }

        .brand-page .product-card:hover {
            border-color: #00D7B3;
            box-shadow: 0 8px 24px rgba(0, 215, 179, 0.25);
            transform: translateY(-6px) scale(1.01);
            border-width: 2px;
        }

        .brand-page .product-card .product-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #e0e0e0;
        }

        .brand-page .product-card .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            padding: 16px;
        }

        .brand-page .product-card h4 {
            padding: 12px 12px 6px 12px;
            margin: 0;
            color: #2B11DB;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
        }

        .brand-page .product-card p {
            padding: 0 12px 8px 12px;
            margin: 0;
            color: #666;
            font-size: 11px;
            line-height: 1.4;
            min-height: 22px;
        }

        .brand-page .product-card > div:last-child {
            padding: 8px 12px 12px 12px;
            margin-top: auto;
        }

        .product-badge {
            display: inline-block;
            position: absolute;
            top: 8px;
            right: 8px;
            background: #FF6B6B;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            z-index: 10;
        }

        .add-to-inquiry {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-weight: 600;
            padding: 10px 12px;
            border: none;
            border-radius: 6px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00D7B3 0%, #00C9A0 100%);
            position: relative;
            font-size: 12px;
            box-shadow: 0 2px 8px rgba(0,215,179,0.3);
        }

        .add-to-inquiry:hover {
            background: linear-gradient(135deg, #00C9A0, #00B690);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,215,179,0.4);
        }

        .inquiry-btn,
        .cart-icon-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00D7B3 0%, #347aec 100%);
            position: relative;
            z-index: 2;
            font-size: 13px;
            box-shadow: 0 4px 12px rgba(0,215,179,0.3);
            margin-top: 4px;
        }

        .inquiry-btn:hover,
        .cart-icon-wrapper:hover {
            background: linear-gradient(135deg, #00ACC1, #00796B);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,215,179,0.45);
            color: white;
        }

        .inquiry-btn {
            position: relative;
        }

        .inquiry-toast {
            position: fixed;
            left: 50%;
            bottom: 28px;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.85);
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 1200;
            opacity: 0;
            transition: opacity 200ms ease, transform 200ms ease;
            pointer-events: none;
        }

        /* Product Preview Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(4px);
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-container {
            background: white;
            border-radius: 20px;
            max-width: 1200px;
            width: 95%;
            max-height: 95vh;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 0 25px 80px rgba(43, 17, 219, 0.2), 0 0 80px rgba(0, 215, 179, 0.1), inset 0 1px 0 rgba(255,255,255,0.8);
            display: flex;
            flex-direction: column;
            position: relative;
            animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(43, 17, 219, 0.08);
            scrollbar-width: thin;
            scrollbar-color: #00D7B3 #f0f0f0;
        }
        
        .modal-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .modal-container::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }
        
        .modal-container::-webkit-scrollbar-thumb {
            background: #00D7B3;
            border-radius: 10px;
        }
        
        .modal-container::-webkit-scrollbar-thumb:hover {
            background: #00B690;
        }

        .modal-container::-webkit-scrollbar {
            display: none;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.92) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(43, 17, 219, 0.1);
            font-size: 24px;
            cursor: pointer;
            color: #2b00d9;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2001;
            font-weight: 600;
            backdrop-filter: blur(8px);
        }

        .modal-close:hover {
            background: #2b00d9;
            color: #fff;
            transform: rotate(90deg) scale(1.1);
            box-shadow: 0 8px 24px rgba(43, 17, 219, 0.3);
            border-color: transparent;
        }

        .modal-media {
            position: relative;
            border-radius: 0;
            overflow: hidden;
            background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
            border: none;
            box-shadow: none;
            flex-shrink: 0;
            width: 100%;
            height: 500px;
            border-bottom: 2px solid #e0e0e0;
            display: flex;
            flex-direction: column;
        }

        .media-slider {
            position: relative;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8faff 0%, #f2f6ff 100%);
            border-radius: 0;
            padding: 40px 30px;
            border-bottom: none;
            flex: 1;
        }

        .media-item {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-item.active {
            opacity: 1;
            position: relative;
        }

        .media-item img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            border-radius: 0;
            box-shadow: none;
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .media-item.active img {
            transform: scale(0.98);
        }

        .media-item iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
        }

        .media-controls {
            display: flex;
            gap: 16px;
            justify-content: center;
            align-items: center;
            padding: 20px 30px;
            background: #f5f7fa;
            border-top: 1px solid #e0e0e0;
            flex-shrink: 0;
            flex-wrap: wrap;
        }

        .media-thumbnails {
            display: flex;
            gap: 12px;
            justify-content: center;
            padding: 20px 30px 30px;
            background: white;
            border-top: 1px solid #e0e0e0;
            flex-wrap: wrap;
            flex-shrink: 0;
        }

        .thumbnail-item {
            width: 70px;
            height: 70px;
            border: 3px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f9fafb;
            flex-shrink: 0;
        }

        .thumbnail-item img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .thumbnail-item:hover {
            border-color: #00D7B3;
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.25);
            transform: scale(1.08);
        }

        .thumbnail-item.active {
            border-color: #FF6B6B;
            border-width: 3px;
            box-shadow: 0 0 12px rgba(255, 107, 107, 0.4);
        }

        .media-nav-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid #e0e0e0;
            border-radius: 50%;
            background: white;
            cursor: pointer;
            color: #2B11DB;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .media-nav-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.3s ease;
        }

        .media-nav-btn:hover {
            transform: scale(1.1) translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 215, 179, 0.3);
            background: linear-gradient(135deg, #00D7B3 0%, #00C9A0 100%);
            color: white;
            border-color: transparent;
        }

        .media-nav-btn:hover::before {
            left: 100%;
        }

        .media-nav-btn:active {
            transform: scale(1.02) translateY(-1px);
            box-shadow: 0 4px 10px rgba(0, 215, 179, 0.25);
        }

        .media-nav-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }

        .media-dots {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 0 12px;
            flex-wrap: wrap;
            flex: 1;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #cbd5e1;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .dot.active {
            background: #00D7B3;
            transform: scale(1.4);
            box-shadow: 0 0 16px rgba(0, 215, 179, 0.6);
            border-color: rgba(0, 215, 179, 0.3);
        }

        .dot:hover {
            background: #00D7B3;
            transform: scale(1.3);
            box-shadow: 0 0 12px rgba(0, 215, 179, 0.5);
        }

        @keyframes dotPulse {
            0%, 100% {
                box-shadow: 0 0 18px rgba(0, 215, 179, 0.6);
            }
            50% {
                box-shadow: 0 0 32px rgba(0, 215, 179, 0.8), 0 0 48px rgba(0, 230, 255, 0.4);
            }
        }

        .modal-content {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 40px 50px;
            flex: 1;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            background: white;
        }
        
        .modal-content::-webkit-scrollbar {
            display: none;
        }

        .modal-content::-webkit-scrollbar {
            display: none;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-content h2 {
            color: #2B11DB;
            font-size: 28px;
            font-weight: 900;
            margin: 0 0 6px 0;
            border: none;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .modal-content .model-type {
            color: #00D7B3;
            font-size: 12px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 2px solid #00D7B3;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .modal-price {
            font-size: 28px;
            font-weight: 900;
            color: #2B11DB;
            margin: 0 0 16px 0;
            letter-spacing: -0.5px;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e0e0e0;
        }

        .product-rating .stars {
            display: flex;
            gap: 2px;
            font-size: 16px;
            color: #FFB800;
        }

        .product-rating .rating-text {
            font-size: 13px;
            color: #666;
            font-weight: 600;
        }

        .product-stock {
            font-size: 12px;
            font-weight: 700;
            color: #00D7B3;
            padding: 4px 0;
        }

        .modal-description {
            font-size: 13px;
            line-height: 1.8;
            color: #555;
            margin: 20px 0 32px 0;
            padding: 16px 20px;
            background: linear-gradient(135deg, rgba(0, 215, 179, 0.05) 0%, rgba(43, 17, 219, 0.03) 100%);
            border-left: 4px solid #00D7B3;
            border-radius: 6px;
        }

        .color-selection {
            margin: 24px 0;
            padding: 0;
        }

        .color-selection h4 {
            font-size: 13px;
            font-weight: 800;
            color: #2B11DB;
            margin: 0 0 14px 0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .color-options {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .color-swatch {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 3px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .color-swatch:hover {
            transform: scale(1.15);
            border-color: #00D7B3;
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.3);
        }

        .color-swatch.active {
            border-color: #2B11DB;
            box-shadow: 0 0 16px rgba(43, 17, 219, 0.4);
            border-width: 3px;
        }

        .color-swatch.active::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: 900;
            font-size: 18px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .quantity-section {
            display: flex;
            align-items: center;
            gap: 20px;
            margin: 28px 0 32px 0;
            padding: 18px 22px;
            background: linear-gradient(135deg, #f8fafc 0%, #f0f4f8 100%);
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .quantity-section label {
            font-size: 13px;
            font-weight: 700;
            color: #2B11DB;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background: white;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .quantity-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            color: #2B11DB;
            cursor: pointer;
            font-weight: 700;
            font-size: 18px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:hover {
            background: #f5f7fa;
            color: #00D7B3;
        }

        .quantity-btn:active {
            background: #e0e0e0;
        }

        .quantity-input {
            width: 56px;
            height: 40px;
            border: none;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #2B11DB;
            background: white;
        }

        .quantity-input:focus {
            outline: none;
            background: #f9fafb;
        }

        .modal-specs {
            background: white;
            border-radius: 8px;
            padding: 0;
            margin: 24px 0;
            border: none;
            box-shadow: none;
            width: 100%;
        }

        .modal-specs h3 {
            color: #2B11DB;
            font-size: 16px;
            font-weight: 900;
            margin: 28px 0 20px 0;
            border-bottom: 3px solid #00D7B3;
            padding-bottom: 14px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-specs h3::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #00D7B3;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .specs-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }

        .specs-list li {
            padding: 18px 22px;
            border: 1px solid #e5e7eb;
            color: #555;
            font-size: 13px;
            line-height: 1.6;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .specs-list li:nth-child(odd) {
            background: #f9fafb;
        }

        .specs-list li:hover {
            background: linear-gradient(135deg, rgba(0, 215, 179, 0.12) 0%, rgba(0, 215, 179, 0.05) 100%);
            border-color: #00D7B3;
            padding-left: 26px;
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.15);
        }

        .specs-list strong {
            color: #2B11DB;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            display: block;
            margin-bottom: 6px;
            opacity: 0.9;
        }
        }

        /* Related Products Section */
        .modal-related-products {
            margin-top: 32px;
            padding-top: 32px;
            border-top: 2px solid #e0e0e0;
        }

        .modal-related-products h3 {
            color: #2B11DB;
            font-size: 16px;
            font-weight: 900;
            margin: 0 0 24px 0;
            border-bottom: 3px solid #00D7B3;
            padding-bottom: 12px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .related-products-carousel {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 12px;
            margin: 0 -50px;
            padding-left: 50px;
            padding-right: 50px;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: #00D7B3 transparent;
        }

        .related-products-carousel::-webkit-scrollbar {
            height: 6px;
        }

        .related-products-carousel::-webkit-scrollbar-track {
            background: transparent;
        }

        .related-products-carousel::-webkit-scrollbar-thumb {
            background: #00D7B3;
            border-radius: 3px;
        }

        .related-product-card {
            flex-shrink: 0;
            width: 200px;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .related-product-card:hover {
            box-shadow: 0 8px 24px rgba(0, 215, 179, 0.25);
            border-color: #00D7B3;
            transform: translateY(-8px);
        }

        .related-product-card .product-image {
            width: 100%;
            height: 150px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .related-product-card .product-image img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .related-product-card .product-info {
            padding: 16px;
        }

        .related-product-card h5 {
            color: #2B11DB;
            font-size: 13px;
            font-weight: 700;
            margin: 0 0 4px 0;
            line-height: 1.3;
        }

        .related-product-card .product-type {
            color: #666;
            font-size: 11px;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }

        .related-product-card .product-price {
            color: #00D7B3;
            font-size: 14px;
            font-weight: 800;
            margin-top: 8px;
        }

        .related-product-card .product-badge {
            display: inline-block;
            background: #FF6B6B;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            margin-top: 6px;
        }

        .related-products-section {
            margin-top: 32px;
            padding-top: 28px;
            border-top: 2px solid #e5e7eb;
        }

        .related-products-section h4 {
            color: #2B11DB;
            font-size: 16px;
            font-weight: 900;
            margin: 0 0 20px 0;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .related-products-section h4::before {
            content: '';
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #FF6B6B;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            margin-left: -50px;
            margin-right: -50px;
            margin-bottom: -40px;
            padding: 28px 50px;
            background: linear-gradient(135deg, #00D7B3 0%, #00C9A0 100%);
            border-radius: 0 0 20px 20px;
            border-top: none;
            align-items: center;
            justify-content: flex-end;
            box-shadow: 0 -4px 16px rgba(0, 215, 179, 0.15);
        }

        .modal-close-btn,
        .modal-inquiry-btn {
            flex: 0 1 auto;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 44px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .modal-close-btn {
            background: white;
            color: #2B11DB;
            border: 2px solid white;
        }

        .modal-close-btn:hover {
            background: transparent;
            color: white;
            border-color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-close-btn:active {
            transform: translateY(0);
        }

        .modal-inquiry-btn {
            background: white;
            color: #2B11DB;
            min-width: 160px;
            font-weight: 900;
        }

        .modal-inquiry-btn:hover {
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        .modal-inquiry-btn:active {
            transform: translateY(0);
        }

        .modal-inquiry-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }


        .modal-close-btn {
            background: rgba(255, 255, 255, 0.95);
            color: #00D7B3;
            font-weight: 700;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-close-btn:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-color: transparent;
        }

        .modal-inquiry-btn {
            background: rgba(255, 255, 255, 0.98);
            color: #00D7B3;
            font-weight: 800;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .modal-inquiry-btn:hover {
            background: white;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .modal-inquiry-btn:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .modal-container {
                max-width: 98%;
                max-height: 98vh;
            }

            .modal-media {
                width: 100%;
                height: 350px;
                border-bottom: 2px solid #e0e0e0;
            }

            .media-slider {
                padding: 20px 15px;
            }

            .modal-content {
                padding: 24px 20px 0 20px;
            }

            .modal-content h2 {
                font-size: 20px;
            }

            .modal-actions {
                margin-left: -20px;
                margin-right: -20px;
                margin-bottom: -24px;
                padding: 16px 20px;
                gap: 8px;
            }

            .modal-close-btn,
            .modal-inquiry-btn {
                flex: 1;
                padding: 12px 16px;
                font-size: 12px;
                min-height: 40px;
            }

            .quantity-section {
                flex-direction: row;
                justify-content: space-between;
            }

            .color-options {
                gap: 10px;
            }

            .specs-list {
                grid-template-columns: 1fr;
            }

            .related-products-carousel {
                margin: 0 -20px;
                padding-left: 20px;
                padding-right: 20px;
            }

            .related-product-card {
                width: 160px;
            }
        }

        @media (max-width: 480px) {
            .modal-container {
                max-width: 100%;
                max-height: 100vh;
                border-radius: 12px 12px 0 0;
            }

            .modal-media {
                height: 280px;
            }

            .media-slider {
                padding: 15px 12px;
            }

            .media-controls .control-btn {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .media-dots {
                gap: 6px;
            }

            .media-dot {
                width: 8px;
                height: 8px;
            }

            .media-thumbnails {
                gap: 8px;
                padding: 10px;
            }

            .thumbnail-item {
                width: 60px;
                height: 60px;
            }

            .modal-content {
                padding: 20px 16px 0 16px;
            }

            .modal-content h2 {
                font-size: 18px;
                margin-bottom: 12px;
            }

            .modal-content p {
                font-size: 12px;
                margin-bottom: 12px;
            }

            .specs-table {
                font-size: 11px;
            }

            .specs-table td {
                padding: 8px 6px;
            }

            .quantity-input {
                width: 40px;
                height: 32px;
                font-size: 12px;
            }

            .qty-btn {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .modal-actions {
                gap: 6px;
                padding: 12px 16px;
            }

            .modal-close-btn,
            .modal-inquiry-btn {
                padding: 10px 12px;
                font-size: 11px;
                min-height: 36px;
            }

            .related-products-section h4 {
                font-size: 14px;
            }

            .related-products-carousel {
                margin: 0 -16px;
                padding-left: 16px;
                padding-right: 16px;
            }

            .related-product-card {
                width: 140px;
                min-width: 140px;
            }

            .related-product-card .product-image {
                height: 100px;
            }

            .related-product-card h5 {
                font-size: 11px;
                padding: 8px;
            }
        }

        .contact-section {
            background: linear-gradient(135deg, #2B11DB 0%, #1a0a8f 100%);
            color: white;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            margin-top: 30px;
        }

        .contact-section h3 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .contact-section p {
            font-size: 16px;
            margin-bottom: 25px;
        }

        .contact-btn {
            background: white;
            color: #2B11DB;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .contact-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .brand-header h1 {
                font-size: 16px;
            }

            .brand-logo-container {
                min-height: 120px;
                margin-bottom: 15px;
            }

            .brand-logo-container img {
                max-height: 90px;
            }

            .brand-content {
                padding: 20px 15px;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
                margin-bottom: 30px;
            }

            .brand-page .product-card {
                display: flex;
                flex-direction: column;
                padding: 0;
                min-height: auto;
                box-shadow: 0 2px 6px rgba(43, 17, 219, 0.08);
            }

            .brand-page .product-card:hover {
                box-shadow: 0 6px 16px rgba(0, 215, 179, 0.2);
                transform: translateY(-4px);
            }

            .brand-page .product-image {
                width: 100%;
                height: 140px;
                min-width: auto;
            }

            .brand-page .product-card h4 {
                padding: 10px 10px 4px 10px;
                font-size: 12px;
                line-height: 1.3;
            }

            .brand-page .product-card p {
                padding: 0 10px 6px 10px;
                font-size: 10px;
                min-height: 20px;
            }

            .brand-page .product-card > div:last-child {
                padding: 6px 10px 10px 10px;
            }

            .add-to-inquiry {
                padding: 8px 10px;
                font-size: 10px;
                height: auto;
                min-height: 36px;
            }
        }

        /* Mini Sidebar Styles */
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
            margin-bottom: 16px;
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
        }

        .mini-sidebar.expanded .browse-label {
            display: inline-block !important;
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .mini-sidebar-icon:hover .sub-indicator {
            opacity: 1;
            background: #00D7B3;
            color: #2B11DB;
            transform: scale(1.15);
        }

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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

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
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease, border-color 0.3s ease;
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

        section, footer, .page-content, .main-content, .category-container {
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
            section, footer, .page-content, .main-content, .category-container { margin-left: 0 !important; }
            .mini-sidebar { display: none !important; }
        }

        @media (max-width: 768px) {
            .mini-sidebar {
                top: calc(14px + 36px + 14px + 40px);
                width: 56px !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease, width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex !important;
            }
            .mini-sidebar.mobile-visible { transform: translateX(0); }
            .mini-sidebar.expanded { width: 240px !important; }
            .main-content, .category-container { margin-left: 0 !important; }
            .mobile-sidebar-fab { display: flex !important; }
        }

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

        .brand-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }

        /* Mini popover styles */
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

        /* ============================================
           PAGINATION
           ============================================ */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 50px 0;
            flex-wrap: wrap;
        }

        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 10px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            color: #333;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: white;
            cursor: pointer;
        }

        .pagination a:hover {
            border-color: #00D7B3;
            color: #00D7B3;
            background: rgba(0, 215, 179, 0.08);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.15);
        }

        .pagination span.active {
            background: linear-gradient(135deg, #2B11DB 0%, #1a0a7f 100%);
            color: white;
            border-color: #2B11DB;
            box-shadow: 0 4px 16px rgba(43, 17, 219, 0.3);
        }

        .pagination span.dots {
            border: none;
            cursor: default;
            padding: 0 4px;
        }

        .pagination span.dots:hover {
            background: transparent;
            transform: none;
            box-shadow: none;
            color: #666;
        }

        .pagination .page-info {
            color: #666;
            font-size: 12px;
            font-weight: 500;
            margin-left: 12px;
            padding-left: 12px;
            border-left: 1px solid #e0e0e0;
        }

        .pagination a.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }

        .pagination a.disabled:hover {
            border-color: #ddd;
            color: #999;
            background: white;
            transform: none;
            box-shadow: none;
        }

        /* ============================================
           RESPONSIVE DESIGN
           ============================================ */
        @media (max-width: 1200px) {
            .feature-boxes {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 16px;
            }

            .brand-content {
                grid-template-columns: 260px 1fr;
                gap: 30px;
            }

            .product-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .feature-boxes {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }

            .brand-content {
                grid-template-columns: 240px 1fr;
                gap: 24px;
            }

            .product-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
            }

            .product-controls {
                gap: 8px;
            }

            .search-box {
                min-width: 180px;
            }
        }

        @media (max-width: 768px) {
            .breadcrumbs-section {
                margin-bottom: 18px;
            }

            .breadcrumbs {
                padding: 10px 20px;
            }

            .breadcrumbs-list {
                gap: 8px;
                font-size: 12px;
            }

            .breadcrumbs-list a {
                font-size: 12px;
                padding: 3px 6px;
            }

            .breadcrumb-current {
                font-size: 12px;
                padding: 3px 8px;
            }

            .feature-boxes {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                margin: 30px auto;
            }

            .feature-box {
                padding: 16px;
            }

            .feature-box h4 {
                font-size: 12px;
            }

            .feature-box p {
                font-size: 11px;
            }

            .brand-content {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 0 12px;
            }

            .brand-content h2 {
                grid-column: 1;
                font-size: 22px;
            }

            .product-filters {
                grid-column: 1 / -1;
            }

            .filter-section {
                padding: 16px;
            }

            .product-controls {
                grid-column: 1 / -1;
                gap: 8px;
                flex-direction: column;
            }

            .search-box {
                width: 100%;
                min-width: auto;
            }

            .sort-dropdown {
                width: 100%;
            }

            .main-product-area {
                grid-column: 1 / -1;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .brand-page .product-card {
                border-radius: 8px;
            }

            .pagination a, .pagination span {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .breadcrumbs-section {
                margin-bottom: 14px;
            }

            .breadcrumbs {
                padding: 8px 16px;
                border-radius: 6px;
            }

            .breadcrumbs-list {
                gap: 6px;
                font-size: 11px;
            }

            .breadcrumbs-list a {
                font-size: 11px;
                padding: 2px 4px;
            }

            .breadcrumbs-list li::after {
                font-size: 11px;
            }

            .breadcrumb-current {
                font-size: 11px;
                padding: 2px 6px;
            }

            .feature-boxes {
                grid-template-columns: 1fr;
                margin: 20px auto;
            }

            .feature-box h4 {
                font-size: 11px;
            }

            .feature-box i {
                font-size: 24px;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 12px;
                padding: 0 5px;
            }

            .brand-page .product-card {
                border-radius: 8px;
                display: flex;
                flex-direction: column;
            }

            .brand-page .product-image {
                width: 100%;
                height: 120px;
            }

            .brand-page .product-image img {
                padding: 12px;
            }

            .brand-page .product-card h4 {
                padding: 8px 8px 3px 8px;
                font-size: 11px;
            }

            .brand-page .product-card p {
                padding: 0 8px 5px 8px;
                font-size: 9px;
                min-height: 18px;
            }

            .brand-page .product-card > div:last-child {
                padding: 5px 8px 8px 8px;
                margin-top: auto;
            }

            .add-to-inquiry {
                padding: 6px 8px;
                font-size: 9px;
                min-height: 32px;
            }

            .product-controls {
                gap: 6px;
            }

            .search-box input {
                padding: 8px 10px 8px 32px;
                font-size: 12px;
            }

            .sort-dropdown, .view-toggle {
                padding: 8px 10px;
                font-size: 12px;
            }

            .view-toggle button {
                padding: 7px 10px;
            }

            .brand-content h2 {
                font-size: 18px;
                margin-bottom: 16px;
            }

            .filter-section {
                padding: 12px;
            }

            .filter-section h3 {
                font-size: 13px;
            }

            .filter-option label {
                font-size: 12px;
            }
        }

        @media (max-width: 1024px) {
            .mini-sidebar {
                display: none !important;
            }
            body {
                padding-left: 0 !important;
            }
            .brand-container {
                padding: 20px 15px;
            }
        }

    </style>
</head>
<body class="brand-page">
    <?php
    // Load brand info from the same source as admin
    require_once __DIR__ . '/includes/brands_info.php';
    
    $company_name = "ANDISON INDUSTRIAL";
    $brand_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Brand';
    
    // Map all brand names to their canonical keys in brands_info and logo filenames
    $brand_name_map = function($brand) {
        $brandMap = [
            // Input variations => Actual key in brands_info
            'Panasonic Connect' => 'Panasonic Connect',
            'BW Technologies' => 'BW Technologies',
            'Weldcraft' => 'Weldcraft',
            'Soyer' => 'Soyer',
            'Alfra' => 'Alfra',
            'Aces' => 'ACES',
            'ACES' => 'ACES',
            'Uvex' => 'UVEX',
            'UVEX' => 'UVEX',
            'Ansell' => 'ANSELL',
            'ANSELL' => 'ANSELL',
            'Microgard' => 'MICROGARD',
            'MICROGARD' => 'MICROGARD',
            'Weldas' => 'WELDAS',
            'WELDAS' => 'WELDAS',
            'Tanaka' => 'TANAKA',
            'TANAKA' => 'TANAKA',
            'Chiyoda' => 'CHIYODA',
            'CHIYODA' => 'CHIYODA',
            'Hardworker' => 'HARDWORKER',
            'Hard Workers' => 'HARDWORKER',
            'HARDWORKER' => 'HARDWORKER',
            'Magnaflux' => 'MAGNAFLUX',
            'MAGNAFLUX' => 'MAGNAFLUX',
            'Coppus' => 'COPPUS',
            'COPPUS' => 'COPPUS',
            'Bosch' => 'BOSCH',
            'BOSCH' => 'BOSCH',
            'Motolite' => 'MOTOLITE',
            'MOTOLITE' => 'MOTOLITE',
            'Aquasol' => 'Aquasol',
            'Arcair' => 'Arcair',
            'Dalo' => 'Dalo',
            'Dryrod' => 'Dryrod',
            'DryRod. II' => 'Dryrod',
            'Garryson' => 'Garryson',
            'Kobelco' => 'Kobelco',
            
            'Makita' => 'Makita',
            'Metrode' => 'Metrode',
            'RAE SYSTEMS' => 'RAE SYSTEMS',
            'ROBOT SYSTEMS' => 'Robot Systems Peripherals',
            'Robot Systems' => 'Robot Systems Peripherals',
            'Robot Systems Peripherals' => 'Robot Systems Peripherals',
            'RAC' => 'RAE SYSTEMS',
            'Spilfyter' => 'Spilfyter',
            'Tempilstik' => 'Tempilstik',
            'Truweld' => 'Truweld',
            'Weiler' => 'Weiler',
            'Weller' => 'Weiler',
            'Yutaka' => 'Yutaka'
        ];
        return isset($brandMap[$brand]) ? $brandMap[$brand] : $brand;
    };
    
    // Map all brand names to their logo filenames
    $logo_filename = function($brand) {
        $logoMap = [
            'Panasonic Connect' => 'PANASONIC',
            'BW Technologies' => 'BW TECHNOLOGIES',
            'Weldcraft' => 'WELDCRAFT',
            'Soyer' => 'SOYER',
            'Alfra' => 'ALFRA',
            'ACES' => 'ACES',
            'UVEX' => 'UVEX',
            'ANSELL' => 'ANSELL',
            'MICROGARD' => 'MICROGARD',
            'WELDAS' => 'WELDAS',
            'TANAKA' => 'TANAKA',
            'CHIYODA' => 'CHIYODA',
            'HARDWORKER' => 'HARDWORKER',
            'Hard Workers' => 'HARDWORKER',
            'Magnaflux' => 'MAGNAFLUX',
            'MAGNAFLUX' => 'MAGNAFLUX',
            'COPPUS' => 'COPPUS',
            'BOSCH' => 'BOSCH',
            'MOTOLITE' => 'MOTOLITE',
            'Aquasol' => 'AQUASOL',
            'Arcair' => 'ARCAIR',
            'Dalo' => 'DALO',
            'Dryrod' => 'DRYROD',
            'DryRod. II' => 'DRYROD',
            'Garryson' => 'GARRYSON',
            'Kobelco' => 'KOBELCO',
            'Makita' => 'MAKITA',
            'Metrode' => 'METRODE',
            'RAE SYSTEMS' => 'RAE SYSTEMS',
            'ROBOT SYSTEMS' => 'ROBOT SYSTEMS',
            'Robot Systems Peripherals' => 'ROBOT SYSTEMS',
            'Robot Systems' => 'ROBOT SYSTEMS',
            'RAC' => 'RAE SYSTEMS',
            'SK And GAL GAGE' => 'SK AND GAL GAGE',
            'Spilfyter' => 'SPILFYTER',
            'Tempilstik' => 'TEMPILSTIK',
            'Truweld' => 'TRUWELD',
            'Weiler' => 'WEILER',
            'Weller' => 'WEILER',
            'Yutaka' => 'YUTAKA'
        ];
        return isset($logoMap[$brand]) ? $logoMap[$brand] : strtoupper($brand);
    };
    
    // Get brand information from JSON file (synced with admin changes)
    $brands_info = andison_get_brands_info();
    
    // If no data from JSON, use empty array (don't use outdated fallback)
    if (empty($brands_info)) {
        $brands_info = [];
    }
    
    // Normalize the brand name to match the array key
    $normalized_brand_name = $brand_name_map($brand_name);
    // Overwrite display name so breadcrumb, title, h1 all show the canonical name
    $brand_name = $normalized_brand_name;
    
    // Get brand info or use defaults
    $brand_info = isset($brands_info[$normalized_brand_name]) ? $brands_info[$normalized_brand_name] : [
        'description' => 'High-quality industrial products and solutions.',
        'products' => ['Industrial Equipment', 'Tools', 'Accessories'],
        'features' => [
            'Quality guaranteed',
            'Professional grade',
            'Reliable performance',
            'Expert support'
        ]
    ];
    
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
                <a href="brands.php" class="inquiry-btn" style="margin-right: 12px;"><i class="bi bi-arrow-left btn-icon"></i> <span class="btn-text"> BRANDS</span></a>
                <a href="inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
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
                        <a href="brands.php" class="active">Brands</a>
                        <div class="nav-dropdown">
                            <h4>Featured Brands</h4>
                            <ul>
                                  <li><a href="brand.php?name=Panasonic%20Connect"><img src="assets/brands/PANASONIC.jpg" alt="Panasonic Connect" title="Panasonic Connect"></a></li>
                                <li><a href="brand.php?name=Robot%20Systems"><img src="assets/brands/ROBOT%20SYSTEMS.png" alt="Robot Systems Peripherals" title="Robot Systems Peripherals"></a></li>
                                <li><a href="brand.php?name=Kobelco"><img src="assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                                <li><a href="brand.php?name=Metrode"><img src="assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                                <li><a href="brand.php?name=DryRod.%20II"><img src="assets/brands/DRYROD.jpg" alt="DryRod. II" title="DryRod. II"></a></li>
                                <li><a href="brand.php?name=Weldcraft"><img src="assets/brands/WELDCRAFT.png" alt="Weldcraft" title="Weldcraft"></a></li>
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
                                <li><a href="brand.php?name=RAE SYSTEMS"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAE Systems" title="RAE Systems"></a></li>
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
                                <li><a href="brand.php?name=REVOLT"><img src="assets/brands/REVOLT.png" alt="REVOLT" title="REVOLT"></a></li>
                                <li><a href="brand.php?name=Technotex"><img src="assets/brands/TECHNOTEX.png" alt="Technotex" title="Technotex"></a></li>
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

    <!-- Sidebar overlay -->
    <!-- Overlay Backdrop -->
    <div class="overlay-backdrop" id="overlayBackdrop" aria-hidden="true"></div>
    <aside id="sidebar" class="sidebar-overlay" aria-hidden="true">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 12px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; color: #1f2937;">Categories</h3>
            <button class="sidebar-close" id="closeSidebar"><i class="bi bi-x-lg"></i></button>
        </div>
        <ul class="sidebar-list">
            <li class="has-sub">
                <a href="arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machine</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-welding" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/co1-mag-welding-machine.php">CO2/MAG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-robots" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-robots/featured-products-and-solution.php">Featured Products and Solutions</a></li>
                    <li><a href="arc-welding-robots/G3-Controller-Series.php">G3 Controller Series</a></li>
                    <li><a href="arc-welding-robots/G4-Controller-Series.php">G4 Controller Series</a></li>
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
                    <li><a href="power-tools/grinder.php">Grinder</a></li>
                    <li><a href="power-tools/saw.php">Saw</a></li>
                    <li><a href="power-tools/drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="power-tools/rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="power-tools/accessories.php">Accessories</a></li>
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
            <span style="font-size: 13px; font-weight: 700; color: white; letter-spacing: 0.5px;" class="browse-label">BROWSE CATEGORIES</span>
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
            <div class="mini-popover-title">Arc Welding Robots</div>
        </div>
        <div class="mini-popover-body">
            <ul class="mini-popover-list"></ul>
        </div>
    </div>

    <div class="brand-container">
        <div class="breadcrumbs-section">
            <nav class="breadcrumbs" aria-label="Breadcrumb">
                <ol class="breadcrumbs-list">
                    <li><a href="home.php">Home</a></li>
                    <li><a href="brands.php">Brands</a></li>
                    <li class="breadcrumb-current"><?php echo $brand_name; ?></li>
                </ol>
            </nav>
        </div>

        <div class="brand-header">
            <div class="brand-logo-container">
                <img src="assets/brands/<?php echo htmlspecialchars($logo_filename($brand_name)); ?>.jpg" alt="<?php echo $brand_name; ?>" class="brand-logo" onerror="if(!this.dataset.tried){this.dataset.tried=1;this.src=this.src.replace('.jpg','.png');}else{this.style.opacity='0.5';}">
                <h1><?php echo $brand_name; ?></h1>
            </div>
            <p><?php echo $brand_info['description']; ?></p>
        </div>

        <!-- Brand Content with Filters -->
        <div class="brand-content">
            <h2>Our Products</h2>

            <!-- Left Sidebar Filters -->
            <div class="product-filters">
                <!-- Category Filter -->
                <div class="filter-section">
                    <h3>Categories</h3>
                    <div class="filter-option">
                        <input type="checkbox" id="cat-all" onchange="filterProducts()">
                        <label for="cat-all">All Products</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="cat-popular" onchange="filterProducts()">
                        <label for="cat-popular">Popular</label>
                        <span>(<?php echo count($brand_info['products']); ?>)</span>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="cat-new" onchange="filterProducts()">
                        <label for="cat-new">New Items</label>
                        <span>(<?php echo count(array_filter($brand_info['products'], fn($p) => is_array($p) && !empty($p['badge']))); ?>)</span>
                    </div>
                </div>

                <!-- Type Filter -->
                <div class="filter-section">
                    <h3>Product Type</h3>
                    <div class="filter-option">
                        <input type="checkbox" id="type-machinery" onchange="filterProducts()">
                        <label for="type-machinery">Machinery</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="type-accessories" onchange="filterProducts()">
                        <label for="type-accessories">Accessories</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="type-consumables" onchange="filterProducts()">
                        <label for="type-consumables">Consumables</label>
                    </div>
                </div>

                <!-- Tags -->
                <div class="filter-section">
                    <h3>Tags</h3>
                    <div class="filter-option">
                        <input type="checkbox" id="tag-industrial" onchange="filterProducts()">
                        <label for="tag-industrial">Industrial</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="tag-professional" onchange="filterProducts()">
                        <label for="tag-professional">Professional</label>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="tag-premium" onchange="filterProducts()">
                        <label for="tag-premium">Premium</label>
                    </div>
                </div>

                <!-- Clear Filters Button -->
                <button onclick="clearAllFilters()" class="clear-filters-btn">Clear All Filters</button>
            </div>

            <!-- Main Product Area -->
            <div class="main-product-area">
                <!-- Search, Sort & View Controls -->
                <div class="product-controls">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
                    </div>
                    <select class="sort-dropdown" id="sortSelect" onchange="filterProducts()">
                        <option value="default">Sort by: Default</option>
                        <option value="name-asc">Name: A to Z</option>
                        <option value="name-desc">Name: Z to A</option>
                        <option value="new">Newest First</option>
                    </select>
                    <div class="view-toggle">
                        <button class="active" data-view="grid" onclick="setView('grid')">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </button>
                        <button data-view="list" onclick="setView('list')">
                            <i class="bi bi-list-ul"></i>
                        </button>
                    </div>
                    <div class="results-info">
                        <span id="resultsCount">Showing all products</span>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="product-grid">
                    <?php foreach($brand_info['products'] as $product): ?>
                        <?php
                            $product_model_value = is_array($product) ? (string)($product['model'] ?? '') : (string)$product;
                            $is_highlight = $highlight_product !== '' && strcasecmp($product_model_value, $highlight_product) === 0;
                        ?>
                        <div class="product-card<?php echo $is_highlight ? ' product-highlight' : ''; ?>"<?php echo $is_highlight ? ' id="highlight-product"' : ''; ?> data-model="<?php echo htmlspecialchars($product_model_value, ENT_QUOTES); ?>" data-description="<?php echo htmlspecialchars(is_array($product) && !empty($product['description']) ? $product['description'] : 'Premium quality ' . $product_model_value . '. ' . (is_array($product) ? $product['type'] : 'Professional grade solution for your industrial needs.'), ENT_QUOTES); ?>">
                            <div class="product-image">
                                <?php if(is_array($product) && !empty($product['image'])): ?>
                                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['model']; ?>">
                                <?php else: ?>
                                    <img src="assets/brands%20items/PANASONIC/Arc Welding Robot/robot-placeholder.jpg" alt="Product" onerror="this.style.display='none'; this.parentElement.innerHTML='🔧';">
                                <?php endif; ?>
                            </div>
                            <?php if(is_array($product)): ?>
                                <?php if(!empty($product['badge'])): ?>
                                    <span class="product-badge"><?php echo $product['badge']; ?></span>
                                <?php endif; ?>
                                <h4><?php echo $product['model']; ?></h4>
                                <p><?php echo $product['type']; ?></p>
                            <?php else: ?>
                                <h4><?php echo $product; ?></h4>
                                <p>Professional grade solutions for your industrial needs</p>
                            <?php endif; ?>
                            <div>
                                <button class="add-to-inquiry" type="button"
                                    data-model="<?php echo htmlspecialchars(is_array($product) ? $product['model'] : $product, ENT_QUOTES); ?>"
                                    data-type="<?php echo htmlspecialchars(is_array($product) ? (isset($product['type']) ? $product['type'] : '') : 'Product', ENT_QUOTES); ?>"
                                    data-brand="<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>"
                                >ADD TO INQUIRY LIST</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <a href="#page-prev" title="Previous Page"><i class="bi bi-chevron-left"></i></a>
                    <a href="#page-1">1</a>
                    <span class="active">2</span>
                    <a href="#page-3">3</a>
                    <a href="#page-4">4</a>
                    <a href="#page-5">5</a>
                    <span class="dots">...</span>
                    <a href="#page-10">10</a>
                    <a href="#page-next" title="Next Page"><i class="bi bi-chevron-right"></i></a>
                    <span class="page-info">Page 2 of 10</span>
                </div>
            </div>
        </div>

    <!-- Product Preview Modal -->
    <div id="productModal" class="modal-overlay">
        <div class="modal-container">
            <button class="modal-close" id="modalClose">&times;</button>
            
            <div class="modal-media">
                <div class="media-slider" id="mediaSlider">
                    <!-- Media items will be inserted here -->
                </div>
                <div class="media-controls">
                    <button class="media-nav-btn" id="prevMedia">&larr;</button>
                    <div class="media-dots" id="mediaDots"></div>
                    <button class="media-nav-btn" id="nextMedia">&rarr;</button>
                </div>
                <div class="media-thumbnails" id="mediaThumbnails"></div>
            </div>

            <div class="modal-content">
                <h2 id="modalProductName"></h2>
                <p class="model-type" id="modalProductType"></p>

                <div class="modal-description" id="modalDescription">
                    The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic words etc.
                </div>

                <div class="quantity-section">
                    <label for="modalQuantity">Quantity:</label>
                    <div class="quantity-control">
                        <button class="quantity-btn" id="qtyDecrement">−</button>
                        <input type="number" id="modalQuantity" class="quantity-input" value="1" min="1" max="999">
                        <button class="quantity-btn" id="qtyIncrement">+</button>
                    </div>
                </div>

                <div class="modal-specs">
                    <h3>Product Specifications</h3>
                    <ul class="specs-list" id="modalSpecs">
                        <li><strong>Category:</strong> Professional Welding Equipment</li>
                        <li><strong>Quality:</strong> Industrial Grade</li>
                        <li><strong>Support:</strong> 24/7 Technical Support</li>
                        <li><strong>Warranty:</strong> 2 Years Full Coverage</li>
                    </ul>
                </div>

                <div class="modal-related-products">
                    <h3>Related Products</h3>
                    <div class="related-products-carousel" id="relatedProductsCarousel">
                        <!-- Related products will be inserted here by JavaScript -->
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="modal-inquiry-btn" id="modalInquiryBtn"><i class="bi bi-plus-circle"></i> ADD TO INQUIRY LIST</button>
                    <button class="modal-close-btn" id="modalCloseBtn">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer REMOVED -->
    <!--
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="#sitemap">Sitemap</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2026 <?php echo $company_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    -->
    <script>
        // Sidebar toggle functionality
        (function(){
            var browseToggle = document.getElementById('browseToggle');
            var sidebarOverlay = document.querySelector('.sidebar-overlay');
            var overlayBackdrop = document.querySelector('.overlay-backdrop');
            var sidebarClose = document.querySelector('.sidebar-close');
            
            if(browseToggle && sidebarOverlay && overlayBackdrop) {
                browseToggle.addEventListener('click', function(){
                    sidebarOverlay.classList.toggle('active');
                    overlayBackdrop.classList.toggle('active');
                });
                
                overlayBackdrop.addEventListener('click', function(){
                    sidebarOverlay.classList.remove('active');
                    overlayBackdrop.classList.remove('active');
                });
                
                if(sidebarClose) {
                    sidebarClose.addEventListener('click', function(){
                        sidebarOverlay.classList.remove('active');
                        overlayBackdrop.classList.remove('active');
                    });
                }
            }
            
            // Sidebar sub-toggle functionality
            var subToggles = document.querySelectorAll('.sub-toggle');
            subToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var sublist = document.getElementById(toggle.getAttribute('aria-controls'));
                    if(sublist) {
                        sublist.classList.toggle('collapsed');
                        toggle.setAttribute('aria-expanded', sublist.classList.contains('collapsed') ? 'false' : 'true');
                    }
                });
            });
            
            // Nested toggle functionality
            var nestedToggles = document.querySelectorAll('.nested-toggle');
            nestedToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var nested = document.getElementById(toggle.getAttribute('aria-controls'));
                    if(nested) {
                        nested.classList.toggle('collapsed');
                        toggle.setAttribute('aria-expanded', nested.classList.contains('collapsed') ? 'false' : 'true');
                    }
                });
            });
        })();
    </script>
    <script>
        // Add to Inquiry client-side handling
        (function(){
            function getItems(){
                try{ return JSON.parse(localStorage.getItem('inquiryItems')||'[]'); }catch(e){ return []; }
            }
            function setItems(items){ 
                localStorage.setItem('inquiryItems', JSON.stringify(items));
                // Dispatch custom event to update badges on all pages
                window.dispatchEvent(new Event('inquiryItemsUpdated'));
            }
            function addItem(item){
                var items = getItems();
                var found = items.find(function(i){ return i.model === item.model && i.brand === item.brand; });
                if(found){
                    return false; // already present
                }
                item.qty = 1; items.push(item);
                setItems(items);
                return true;
            }

            function showToast(msg){
                var t = document.querySelector('.inquiry-toast');
                if(!t){ t = document.createElement('div'); t.className = 'inquiry-toast'; document.body.appendChild(t); }
                t.textContent = msg;
                requestAnimationFrame(function(){ t.style.opacity = '1'; t.style.transform = 'translateX(-50%) translateY(-6px)'; });
                clearTimeout(t._hide);
                t._hide = setTimeout(function(){ t.style.opacity = '0'; t.style.transform = 'translateX(-50%) translateY(0)'; }, 1800);
            }

            document.addEventListener('click', function(e){
                var btn = e.target.closest('.add-to-inquiry');
                if(!btn) return;
                var model = btn.dataset.model || '';
                var type = btn.dataset.type || '';
                var brand = btn.dataset.brand || '';
                var added = addItem({ model: model, type: type, brand: brand });
                if(!added){
                    showToast('Product already in inquiry list');
                    // small visual feedback on button
                    btn.classList.add('already');
                    setTimeout(function(){ btn.classList.remove('already'); }, 700);
                    return;
                }
                btn.textContent = 'Added';
                setTimeout(function(){ btn.textContent = 'ADD TO INQUIRY LIST'; }, 900);
            });

            // Contact dropdown handler
            var dropdowns = document.querySelectorAll('.contact-dropdown');
            dropdowns.forEach(function(dropdown) {
                var closeBtn = dropdown.querySelector('.contact-close');
                if(closeBtn) {
                    closeBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        dropdown.classList.add('closed');
                    });
                }
                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
                dropdown.addEventListener('focusout', function() {
                    setTimeout(function() {
                        if(!dropdown.contains(document.activeElement)) {
                            dropdown.classList.add('closed');
                        }
                    }, 100);
                });
            });
            document.addEventListener('click', function() {
                dropdowns.forEach(function(dropdown) { dropdown.classList.add('closed'); });
            });

            // Product Preview Modal Handler
            (function() {
                var modal = document.getElementById('productModal');
                var modalClose = document.getElementById('modalClose');
                var modalCloseBtn = document.getElementById('modalCloseBtn');
                var productCards = document.querySelectorAll('.brand-page .product-card');
                var currentMediaIndex = 0;
                var currentProduct = null;
                var currentProductImageSrc = null;
                var autoPlayInterval = null;
                
                // Product data with specs and videos
                var productsData = {
                    'panasonic-connect': {
                        specs: ['Brand: Panasonic Connect', 'Category: Professional Welding Equipment', 'Quality: Industrial Grade', 'Support: 24/7 Technical Support']
                    },
                    'default': {
                        specs: ['Professional Grade Equipment', 'Industrial Standard', 'Quality Assured', 'Full Support Available']
                    }
                };

                productCards.forEach(function(card, index) {
                    card.addEventListener('click', function() {
                        var model = card.querySelector('h4').textContent;
                        var type = card.querySelector('p').textContent;
                        var imgSrc = card.querySelector('.product-image img').src;
                        var description = card.getAttribute('data-description') || 'Premium quality product for your industrial needs.';
                        
                        // Find product data
                        var productKey = '<?php echo strtolower(str_replace(' ', '-', $brand_name)); ?>';
                        var productSpecs = (productsData[productKey] || productsData['default']).specs;
                        
                        // Populate modal
                        document.getElementById('modalProductName').textContent = model;
                        document.getElementById('modalProductType').textContent = type;
                        document.getElementById('modalDescription').textContent = description;
                        
                        // Set up media items
                        var mediaSlider = document.getElementById('mediaSlider');
                        mediaSlider.innerHTML = '';
                        var mediaItems = [];
                        
                        // Add image
                        var imgItem = document.createElement('div');
                        imgItem.className = 'media-item active';
                        var img = document.createElement('img');
                        img.src = imgSrc;
                        img.alt = model;
                        imgItem.appendChild(img);
                        mediaSlider.appendChild(imgItem);
                        mediaItems.push(imgItem);
                        
                        // Store image source for thumbnail display
                        currentProductImageSrc = imgSrc;
                        
                        // Add video if available (can be added by admin)
                        // Example: data-video attribute on product card
                        var videoUrl = card.getAttribute('data-video');
                        if(videoUrl) {
                            var videoItem = document.createElement('div');
                            videoItem.className = 'media-item';
                            var iframe = document.createElement('iframe');
                            iframe.src = videoUrl;
                            iframe.allowFullscreen = true;
                            videoItem.appendChild(iframe);
                            mediaSlider.appendChild(videoItem);
                            mediaItems.push(videoItem);
                        }
                        
                        // Update specs
                        var specsList = document.getElementById('modalSpecs');
                        specsList.innerHTML = '';
                        productSpecs.forEach(function(spec) {
                            var li = document.createElement('li');
                            li.innerHTML = '<strong>' + spec.split(':')[0] + ':</strong> ' + (spec.split(':')[1] || spec);
                            specsList.appendChild(li);
                        });
                        
                        // Populate related products
                        populateRelatedProducts();
                        
                        // Update media dots and buttons
                        updateMediaDots(0, mediaItems.length);
                        updateMediaNavButtons(0, mediaItems.length);
                        updateMediaThumbnails(0, mediaItems.length, currentProductImageSrc);
                        currentMediaIndex = 0;
                        
                        // Store current product for inquiry button
                        currentProduct = {
                            model: model,
                            type: type,
                            brand: '<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>'
                        };
                        
                        // Show modal
                        modal.classList.add('active');
                        
                        // Start auto-play
                        startAutoPlay();
                    });
                });

                function showMediaItem(index, items) {
                    items.forEach(function(item) { item.classList.remove('active'); });
                    if(items[index]) {
                        items[index].classList.add('active');
                    }
                }

                function updateMediaDots(currentIndex, totalItems) {
                    var dotsContainer = document.getElementById('mediaDots');
                    dotsContainer.innerHTML = '';
                    for(var i = 0; i < totalItems; i++) {
                        var dot = document.createElement('div');
                        dot.className = 'dot' + (i === currentIndex ? ' active' : '');
                        dot.addEventListener('click', function() {
                            stopAutoPlay();
                            currentMediaIndex = i;
                            var mediaItems = document.querySelectorAll('.media-item');
                            showMediaItem(i, Array.from(mediaItems));
                            updateMediaDots(i, totalItems);
                            updateMediaNavButtons(i, totalItems);
                        });
                        dotsContainer.appendChild(dot);
                    }
                }

                function updateMediaNavButtons(currentIndex, totalItems) {
                    document.getElementById('prevMedia').disabled = totalItems <= 1;
                    document.getElementById('nextMedia').disabled = totalItems <= 1;
                }

                function updateMediaThumbnails(currentIndex, totalItems, imageSrc) {
                    var thumbnailsContainer = document.getElementById('mediaThumbnails');
                    thumbnailsContainer.innerHTML = '';
                    
                    // Create thumbnail for main image
                    var mainThumb = document.createElement('div');
                    mainThumb.className = 'thumbnail-item' + (currentIndex === 0 ? ' active' : '');
                    var mainImg = document.createElement('img');
                    mainImg.src = imageSrc;
                    mainImg.alt = 'Main Image';
                    mainThumb.appendChild(mainImg);
                    mainThumb.addEventListener('click', function() {
                        stopAutoPlay();
                        currentMediaIndex = 0;
                        var mediaItems = document.querySelectorAll('.media-item');
                        showMediaItem(0, Array.from(mediaItems));
                        updateMediaDots(0, totalItems);
                        updateMediaNavButtons(0, totalItems);
                        updateMediaThumbnails(0, totalItems, imageSrc);
                    });
                    thumbnailsContainer.appendChild(mainThumb);
                    
                    // Create thumbnails for additional images (if any)
                    for(var i = 1; i < totalItems; i++) {
                        var thumb = document.createElement('div');
                        thumb.className = 'thumbnail-item' + (i === currentIndex ? ' active' : '');
                        var thumbImg = document.createElement('img');
                        thumbImg.src = imageSrc;
                        thumbImg.alt = 'Image ' + (i + 1);
                        thumb.appendChild(thumbImg);
                        
                        thumb.addEventListener('click', (function(index) {
                            return function() {
                                stopAutoPlay();
                                currentMediaIndex = index;
                                var mediaItems = document.querySelectorAll('.media-item');
                                showMediaItem(index, Array.from(mediaItems));
                                updateMediaDots(index, totalItems);
                                updateMediaNavButtons(index, totalItems);
                                updateMediaThumbnails(index, totalItems, imageSrc);
                            };
                        })(i));
                        thumbnailsContainer.appendChild(thumb);
                    }
                }

                function startAutoPlay() {
                    stopAutoPlay();
                    var mediaItems = document.querySelectorAll('.media-item');
                    if(mediaItems.length <= 1) return;
                    
                    autoPlayInterval = setInterval(function() {
                        currentMediaIndex = (currentMediaIndex + 1) % mediaItems.length;
                        showMediaItem(currentMediaIndex, Array.from(mediaItems));
                        updateMediaDots(currentMediaIndex, mediaItems.length);
                        updateMediaThumbnails(currentMediaIndex, mediaItems.length, currentProductImageSrc);
                    }, 4000);
                }

                function stopAutoPlay() {
                    if(autoPlayInterval) {
                        clearInterval(autoPlayInterval);
                        autoPlayInterval = null;
                    }
                }

                function populateRelatedProducts() {
                    var carousel = document.getElementById('relatedProductsCarousel');
                    carousel.innerHTML = '';
                    
                    // Get all product cards from the page
                    var allCards = document.querySelectorAll('.product-card:not(#highlight-product)');
                    
                    // Show up to 6 related products
                    var relatedCount = Math.min(6, allCards.length);
                    for(var i = 0; i < relatedCount; i++) {
                        var card = allCards[i];
                        var title = card.querySelector('h4').textContent;
                        var type = card.querySelector('p').textContent;
                        var imgSrc = card.querySelector('.product-image img').src;
                        var badge = card.querySelector('.product-badge');
                        
                        var productCard = document.createElement('div');
                        productCard.className = 'related-product-card';
                        
                        var html = '<div class="product-image"><img src="' + imgSrc + '" alt="' + title + '"></div>';
                        html += '<div class="product-info">';
                        html += '<h5>' + title + '</h5>';
                        html += '<p class="product-type">' + type + '</p>';
                        if(badge) {
                            html += '<span class="product-badge">' + badge.textContent + '</span>';
                        }
                        html += '</div>';
                        
                        productCard.innerHTML = html;
                        carousel.appendChild(productCard);
                    }
                    
                    // Auto-scroll related products every 5 seconds
                    startRelatedProductsAutoScroll();
                }

                var relatedProductsScrollInterval = null;
                
                function startRelatedProductsAutoScroll() {
                    clearRelatedProductsAutoScroll();
                    var carousel = document.getElementById('relatedProductsCarousel');
                    if(!carousel) return;
                    
                    relatedProductsScrollInterval = setInterval(function() {
                        if(carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 10) {
                            carousel.scrollLeft = 0;
                        } else {
                            carousel.scrollLeft += 230;
                        }
                    }, 5000);
                }

                function clearRelatedProductsAutoScroll() {
                    if(relatedProductsScrollInterval) {
                        clearInterval(relatedProductsScrollInterval);
                        relatedProductsScrollInterval = null;
                    }
                }

                // Close modal
                var closeModal = function() {
                    modal.classList.remove('active');
                    currentMediaIndex = 0;
                    stopAutoPlay();
                    clearRelatedProductsAutoScroll();
                };

                modalClose.addEventListener('click', closeModal);
                modalCloseBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function(e) {
                    if(e.target === modal) closeModal();
                });

                // Navigation buttons
                document.getElementById('prevMedia').addEventListener('click', function() {
                    stopAutoPlay();
                    var mediaItems = document.querySelectorAll('.media-item');
                    currentMediaIndex = Math.max(0, currentMediaIndex - 1);
                    showMediaItem(currentMediaIndex, Array.from(mediaItems));
                    updateMediaDots(currentMediaIndex, mediaItems.length);
                    updateMediaNavButtons(currentMediaIndex, mediaItems.length);
                    updateMediaThumbnails(currentMediaIndex, mediaItems.length, currentProductImageSrc);
                });

                document.getElementById('nextMedia').addEventListener('click', function() {
                    stopAutoPlay();
                    var mediaItems = document.querySelectorAll('.media-item');
                    currentMediaIndex = Math.min(mediaItems.length - 1, currentMediaIndex + 1);
                    showMediaItem(currentMediaIndex, Array.from(mediaItems));
                    updateMediaDots(currentMediaIndex, mediaItems.length);
                    updateMediaNavButtons(currentMediaIndex, mediaItems.length);
                    updateMediaThumbnails(currentMediaIndex, mediaItems.length, currentProductImageSrc);
                });

                // Inquiry button in modal
                document.getElementById('modalInquiryBtn').addEventListener('click', function() {
                    if(currentProduct) {
                        var qtyInput = document.getElementById('modalQuantity');
                        var quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                        var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                        var found = items.find(function(i) { return i.model === currentProduct.model && i.brand === currentProduct.brand; });
                        if(!found) {
                            currentProduct.qty = quantity;
                            items.push(currentProduct);
                            localStorage.setItem('inquiryItems', JSON.stringify(items));
                            this.innerHTML = '<i class="bi bi-check-circle"></i> Added ✓';
                            setTimeout(function() { document.getElementById('modalInquiryBtn').innerHTML = '<i class="bi bi-plus-circle"></i> ADD TO INQUIRY LIST'; }, 1200);
                        } else {
                            this.innerHTML = '<i class="bi bi-check-circle"></i> Already Added ✓';
                            setTimeout(function() { document.getElementById('modalInquiryBtn').innerHTML = '<i class="bi bi-plus-circle"></i> ADD TO INQUIRY LIST'; }, 1200);
                        }
                    }
                });

                // Quantity controls
                document.getElementById('qtyIncrement').addEventListener('click', function() {
                    var qtyInput = document.getElementById('modalQuantity');
                    var current = parseInt(qtyInput.value) || 1;
                    qtyInput.value = Math.min(999, current + 1);
                });

                document.getElementById('qtyDecrement').addEventListener('click', function() {
                    var qtyInput = document.getElementById('modalQuantity');
                    var current = parseInt(qtyInput.value) || 1;
                    qtyInput.value = Math.max(1, current - 1);
                });

                // Color swatch selection
                document.addEventListener('click', function(e) {
                    if(e.target.classList.contains('color-swatch')) {
                        document.querySelectorAll('.color-swatch').forEach(function(swatch) {
                            swatch.classList.remove('active');
                        });
                        e.target.classList.add('active');
                    }
                });
            })();
        })();
    </script>
    <script>
        // Product highlighting and scrolling
        // Update brand dropdown links with product parameter ONLY when coming from search results
        (function(){
            // Get product parameter from URL
            var urlParams = new URLSearchParams(window.location.search);
            var product = urlParams.get('product');
            var currentBrand = urlParams.get('name');
            
            // Only modify links if we came from search/have a product parameter
            if(product){
                // Find all brand dropdown links
                var brandLinks = document.querySelectorAll('.nav-dropdown ul li a[href*="brand.php?name="]');
                brandLinks.forEach(function(link){
                    var href = link.getAttribute('href');
                    if(href){
                        // Extract brand name from href
                        var regex = /name=([^&]+)/;
                        var match = href.match(regex);
                        var linkBrand = match ? decodeURIComponent(match[1]) : '';
                        
                        if(linkBrand === currentBrand){
                            // Same brand - preserve product parameter
                            if(!href.includes('&product=')){
                                href = href.replace(/(\?|\&)product=[^&]*/g, '');
                                link.setAttribute('href', href + '&product=' + encodeURIComponent(product));
                            }
                        } else {
                            // Different brand - remove product parameter to show all products
                            href = href.replace(/(\?|\&)product=[^&]*/g, '');
                            link.setAttribute('href', href);
                        }
                    }
                });
            }
        })();

        (function(){
            function normalize(str) {
                return (str || '').trim().toLowerCase();
            }

            document.addEventListener('DOMContentLoaded', function(){
                var product = normalize('<?php echo htmlspecialchars($highlight_product, ENT_QUOTES); ?>');
                if(!product) return;

                var target = document.getElementById('highlight-product');
                if(!target){
                    var needle = normalize(product);
                    var cards = document.querySelectorAll('.product-card[data-model]');
                    for(var i = 0; i < cards.length; i++){
                        var card = cards[i];
                        if(normalize(card.getAttribute('data-model')) === needle){
                            target = card;
                            target.id = 'highlight-product';
                            target.classList.add('product-highlight');
                            break;
                        }
                    }
                }

                if(target && typeof target.scrollIntoView === 'function'){
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Remove highlight after 10 seconds
                    setTimeout(function(){
                        if(target){
                            target.classList.remove('product-highlight');
                        }
                    }, 10000);
                }
            });
        })();
    </script>

    <script>
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
                'arc-welding-machine': [{label: 'MIG Welding Machine', href: base + '/arc-welding-machine/mig-welding-machine.php'},{label: 'CO2/MAG Welding Machine', href: base + '/arc-welding-machine/co1-mag-welding-machine.php'},{label: 'STUD Welding Machine', href: base + '/arc-welding-machine/stud-welding-machine.php'},{label: 'TIG Welding Machine', href: base + '/arc-welding-machine/tig-welding-machine.php'},{label: 'Plasma Cutting Machine', href: base + '/arc-welding-machine/plasma-cutting-machine.php'}],
                'batteries': [{label: 'Maintenance Free', href: base + '/batteries/maintenance-free.php'},{label: 'Low Maintenance', href: base + '/batteries/low-maintenance.php'},{label: 'Special Batteries', href: base + '/batteries/special-batteries.php'}],
                'drilling-and-lifting': [{label: 'Lifting', href: base + '/drilling-and-lifting/lifting.php'},{label: 'Magnetic Drill', href: base + '/drilling-and-lifting/magnetic-drill.php'},{label: 'Cutters', href: base + '/drilling-and-lifting/cutters.php'}],
                'gas-detectors': [{label: 'Single Gas Detector', href: base + '/gas-detectors/single-gas-detector.php'},{label: 'Multi Gas Detector', href: base + '/gas-detectors/multi-gas-detector.php'},{label: 'Portable Gas Detectors', href: base + '/gas-detectors/portable-gas-detectors.php'},{label: 'Docking and Data Management', href: base + '/gas-detectors/docking-data-management.php'},{label: 'Calibration Gas and Regulators', href: base + '/gas-detectors/calibration-gas-regulators.php'}],
                'power-tools': [{label: 'Grinder', href: base + '/power-tools/grinder.php'},{label: 'Saw', href: base + '/power-tools/saw.php'},{label: 'Drill and Wrench', href: base + '/power-tools/drill-and-wrench.php'},{label: 'Rotary and Demolition Hammer', href: base + '/power-tools/rotary-and-demolition-hammer.php'},{label: 'Accessories', href: base + '/power-tools/accessories.php'}],
                'portable-ventilators': [{label: 'Electric Driven', href: base + '/portable-ventilators/electric-driven.php'},{label: 'Pneumatic Driven', href: base + '/portable-ventilators/pneumatic-driven.php'}],
                'protection': [{label: 'Eye Protection', href: base + '/protection/eye-protection.php'},{label: 'Hand Protection', href: base + '/protection/hand-protection.php'},{label: 'Hearing & Respiratory Protection', href: base + '/protection/hearing-respiratory-protection.php'},{label: 'Body Protection', href: base + '/protection/body-protection.php'}],
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
                    document.body.classList.toggle('sidebar-expanded');
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
            document.body.classList.toggle('sidebar-expanded');
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
        // Mobile FAB toggle for mini sidebar
        (function() {
            var fab = document.getElementById('mobileSidebarFab');
            var sidebar = document.getElementById('miniSidebar');
            var fabIcon = document.getElementById('mobileFabIcon');
            if (!fab || !sidebar) return;

            function isMobile() { return window.innerWidth <= 768; }

            function syncFab() {
                if (!isMobile()) { fab.classList.remove('open', 'wide'); return; }
                var isOpen = sidebar.classList.contains('mobile-visible');
                var isExpanded = sidebar.classList.contains('expanded');
                fab.classList.toggle('open', isOpen);
                fab.classList.toggle('wide', isOpen && isExpanded);
                fabIcon.className = isOpen ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
            }

            fab.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!isMobile()) return;
                sidebar.classList.toggle('mobile-visible');
                syncFab();
            });

            var observer = new MutationObserver(function() { syncFab(); });
            observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

            window.addEventListener('resize', syncFab);
        })();
    </script>

    <!-- Inquiry List Handler -->
    <script src="assets/js/inquiry-handler.js"></script>

    <script>
        // PRODUCT FILTERING AND PAGINATION
        // ============================================
        var currentPage = 1;
        var itemsPerPage = 16; // 4x4 grid
        var allCards = [];
        var filteredCards = [];

        function initPagination() {
            allCards = Array.from(document.querySelectorAll('.product-grid .product-card'));
            filteredCards = allCards.slice();
            updatePagination();
        }

        function filterProducts() {
            var searchTerm = document.getElementById('searchInput').value.toLowerCase();
            var sortValue = document.getElementById('sortSelect').value;
            
            // Get selected filters
            var categoryFilters = {
                popular: document.getElementById('cat-popular').checked,
                new: document.getElementById('cat-new').checked,
            };
            
            var typeFilters = {
                machinery: document.getElementById('type-machinery').checked,
                accessories: document.getElementById('type-accessories').checked,
                consumables: document.getElementById('type-consumables').checked,
            };
            
            var tagFilters = {
                industrial: document.getElementById('tag-industrial').checked,
                professional: document.getElementById('tag-professional').checked,
                premium: document.getElementById('tag-premium').checked,
            };

            var visibleCards = [];

            // Filter cards based on all criteria
            allCards.forEach(function(card) {
                var model = card.querySelector('h4').textContent.toLowerCase();
                var type = card.querySelector('p').textContent.toLowerCase();
                var badge = card.querySelector('.product-badge');
                
                // Search filter
                var matchesSearch = model.includes(searchTerm) || type.includes(searchTerm);
                if(!matchesSearch) return;

                // Category filters - if any category is checked, card must match one
                var anyCategory = categoryFilters.popular || categoryFilters.new;
                if(anyCategory) {
                    var isPopular = true; // All items are popular by default
                    var isNew = badge !== null;
                    if(categoryFilters.popular && !isPopular) return;
                    if(categoryFilters.new && !isNew) return;
                }

                // Type filter - if any type is checked, card must match one
                var anyType = typeFilters.machinery || typeFilters.accessories || typeFilters.consumables;
                if(anyType) {
                    var isMachinery = type.includes('machine') || type.includes('robot') || type.includes('oven');
                    var isAccessories = type.includes('accessory') || type.includes('electrode') || type.includes('regulator');
                    var isConsumables = type.includes('consumable') || type.includes('wire') || type.includes('flux');
                    if(typeFilters.machinery && !isMachinery) return;
                    if(typeFilters.accessories && !isAccessories) return;
                    if(typeFilters.consumables && !isConsumables) return;
                }

                // Tag filter - if any tag is checked, card must match one
                var anyTag = tagFilters.industrial || tagFilters.professional || tagFilters.premium;
                if(anyTag) {
                    var isIndustrial = true; // All are industrial
                    var isProfessional = badge ? true : false; // New items marked as professional
                    var isPremium = false; // Can be set based on specific products
                    
                    if(tagFilters.industrial && !isIndustrial) return;
                    if(tagFilters.professional && !isProfessional) return;
                    if(tagFilters.premium && !isPremium) return;
                }

                visibleCards.push(card);
            });

            // Sort
            if(sortValue !== 'default') {
                if(sortValue === 'name-asc') {
                    visibleCards.sort(function(a, b) {
                        var aText = a.querySelector('h4').textContent;
                        var bText = b.querySelector('h4').textContent;
                        return aText.localeCompare(bText);
                    });
                } else if(sortValue === 'name-desc') {
                    visibleCards.sort(function(a, b) {
                        var aText = a.querySelector('h4').textContent;
                        var bText = b.querySelector('h4').textContent;
                        return bText.localeCompare(aText);
                    });
                } else if(sortValue === 'new') {
                    visibleCards.sort(function(a, b) {
                        var aBadge = a.querySelector('.product-badge');
                        var bBadge = b.querySelector('.product-badge');
                        return (bBadge ? 1 : 0) - (aBadge ? 1 : 0);
                    });
                }
            }

            filteredCards = visibleCards;
            currentPage = 1;
            updatePagination();
        }

        function updatePagination() {
            var totalPages = Math.ceil(filteredCards.length / itemsPerPage);
            var start = (currentPage - 1) * itemsPerPage;
            var end = start + itemsPerPage;

            // Hide all cards
            allCards.forEach(function(card) { card.style.display = 'none'; });

            // Show only cards for current page
            filteredCards.slice(start, end).forEach(function(card) { card.style.display = ''; });

            // Update pagination buttons
            updatePaginationButtons(totalPages);

            // Update results counter
            var resultsCount = document.getElementById('resultsCount');
            if(resultsCount) {
                if(filteredCards.length === 0) {
                    resultsCount.innerHTML = 'No products found';
                } else if(filteredCards.length === allCards.length) {
                    resultsCount.innerHTML = 'Showing <span>' + filteredCards.length + '</span> products';
                } else {
                    resultsCount.innerHTML = 'Found <span>' + filteredCards.length + '</span> out of ' + allCards.length + ' products';
                }
            }
        }

        function updatePaginationButtons(totalPages) {
            var paginationDiv = document.querySelector('.pagination');
            if(!paginationDiv) return;

            var html = '';

            // Previous button
            html += '<a href="#" class="' + (currentPage === 1 ? 'disabled' : '') + '" onclick="goToPage(' + (currentPage - 1) + '); return false;" title="Previous"><i class="bi bi-chevron-left"></i></a>';

            // Page numbers
            var startPage = Math.max(1, currentPage - 2);
            var endPage = Math.min(totalPages, currentPage + 2);

            if(startPage > 1) {
                html += '<a href="#" onclick="goToPage(1); return false;">1</a>';
                if(startPage > 2) html += '<span class="dots">...</span>';
            }

            for(var i = startPage; i <= endPage; i++) {
                if(i === currentPage) {
                    html += '<span class="active">' + i + '</span>';
                } else {
                    html += '<a href="#" onclick="goToPage(' + i + '); return false;">' + i + '</a>';
                }
            }

            if(endPage < totalPages) {
                if(endPage < totalPages - 1) html += '<span class="dots">...</span>';
                html += '<a href="#" onclick="goToPage(' + totalPages + '); return false;">' + totalPages + '</a>';
            }

            // Next button
            html += '<a href="#" class="' + (currentPage === totalPages ? 'disabled' : '') + '" onclick="goToPage(' + (currentPage + 1) + '); return false;" title="Next"><i class="bi bi-chevron-right"></i></a>';

            // Page info
            html += '<span class="page-info">Page ' + currentPage + ' of ' + totalPages + '</span>';

            paginationDiv.innerHTML = html;
        }

        function goToPage(page) {
            var totalPages = Math.ceil(filteredCards.length / itemsPerPage);
            if(page >= 1 && page <= totalPages) {
                currentPage = page;
                updatePagination();
                window.scrollTo({ top: document.querySelector('.product-controls').offsetTop - 100, behavior: 'smooth' });
            }
        }

        function clearAllFilters() {
            // Clear search
            document.getElementById('searchInput').value = '';
            
            // Clear sort
            document.getElementById('sortSelect').value = 'default';
            
            // Clear all checkboxes
            document.querySelectorAll('.filter-option input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
            
            // Trigger filter update
            filterProducts();
        }

        function setView(viewType) {
            var buttons = document.querySelectorAll('.view-toggle button');
            buttons.forEach(function(btn) { btn.classList.remove('active'); });
            document.querySelector('[data-view="' + viewType + '"]').classList.add('active');

            var grid = document.querySelector('.product-grid');
            if(viewType === 'list') {
                grid.style.gridTemplateColumns = '1fr';
            } else {
                grid.style.gridTemplateColumns = 'repeat(4, 1fr)';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initPagination();
        });

        // Initialize search on enter key
        var searchInput = document.getElementById('searchInput');
        if(searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if(e.key === 'Enter') filterProducts();
            });
        }
    </script>

    <script>
        // UPDATE CART BADGE COUNT IN REAL-TIME
        // ============================================
        (function(){
            function updateCartBadge() {
                var badge = document.getElementById('cartBadge');
                if(!badge) return;
                
                var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                var count = items.length;
                
                if(count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            // Update on page load
            updateCartBadge();

            // Listen for storage changes from other pages
            window.addEventListener('storage', updateCartBadge);
            
            // Listen for custom event when items are added on same page
            window.addEventListener('inquiryItemsUpdated', updateCartBadge);
            
            // Also check periodically in case other tabs update
            setInterval(updateCartBadge, 500);
        })();
    </script>
</body>



