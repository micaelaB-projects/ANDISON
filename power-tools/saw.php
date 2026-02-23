<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$page_title = "Saw";
$category_id = "power-tools";
$subcategory_id = "saw";
$phone = "+1(234) 567 8900";
$phone2 = "+1(234) 567 8900";
$phone3 = "+1(639) 977 803 7398";
$email = "info@andison-industrial.com";

$categories = andison_get_categories();
$current_category = null;

foreach ($categories as $cat) {
    if ($cat['id'] === $category_id) {
        $current_category = $cat;
        break;
    }
}

if (!$current_category) {
    // Fallback: create a default category object
    $current_category = array(
        'id' => $category_id,
        'name' => 'Power Tools',
        'description' => 'Professional power tools for industrial applications.',
        'subcategories' => array(
            array('id' => 'saw', 'name' => 'Saw')
        )
    );
}

// Override category for subcategory-specific display
$current_category['name'] = 'Saw';
$current_category['description'] = 'Professional saws for cutting wood, metal and composite materials.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    $category_name = $current_category['name'] ?? 'Saw';
    $category_description = $current_category['description'] ?? 'Professional saws for cutting wood, metal and composite materials.';
    ?>
    <title><?php echo htmlspecialchars($category_name); ?> - ANDISON INDUSTRIAL</title>
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
            z-index: 1200;
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
            font-size: 15px;
            padding-bottom: 8px;
            white-space: nowrap;
            position: relative;
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

        /* mobile: click-to-open; .open class used instead of hover */
        @media (max-width: 768px) {
            .contact-dropdown:hover:not(.closed) .contact-popover,
            .contact-dropdown:focus-within:not(.closed) .contact-popover {
                opacity: 0;
                visibility: hidden;
                transform: translateX(-50%) translateY(-6px) scale(0.98);
            }
            .contact-dropdown.open .contact-popover {
                opacity: 1;
                visibility: visible;
                transform: translateX(-50%) translateY(0) scale(1);
            }
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

        /* compact on mobile */
        @media (max-width: 768px) {
            .contact-popover { width: 240px; padding: 8px 10px; }
            .contact-list { padding: 2px 0; }
            .contact-list li { gap: 8px; padding: 6px 4px; }
            .contact-list .icon { font-size: 14px; width: 20px; }
            .contact-list a { font-size: 12px; }
        }

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
            background: linear-gradient(135deg,  #00E5C8  0%, #347aec 100%);
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

        .inquiry-btn .btn-icon { display: inline; }
        .inquiry-btn .btn-text { display: inline; }

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

        .right-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 0 0 auto;
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
            padding: 0 8px 0 120px; /* space for the left Browse toggle */
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
            display: inline-flex;
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
        }

        .nav-list li { position: relative; }

        .nav-list a {
            text-decoration: none;
            display: block;
        }

        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        /* Glowing underline + dark active background for top-level nav links */
        .nav-list > li > a {
            position: relative;
            padding: 10px 14px;
            color: white;
            transition: color 180ms ease, background 180ms ease;
        }

        .nav-list > li > a::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 2px;
            transform: translateX(-50%) scaleX(0);
            transform-origin: center;
            width: 44px;
            height: 5px;
            border-radius: 6px;
            background: linear-gradient(90deg, #00ffd1 0%, #00d4aa 50%, #2B11DB 100%);
            box-shadow: 0 2px 10px rgba(0,212,170,0.35);
            pointer-events: none;
            transition: transform 180ms ease, width 180ms ease;
        }

        .nav-list > li > a:hover {
            background: rgba(0,0,0,0.10);
            border-radius: 6px;
        }

        .nav-list > li > a:hover::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
        }

        .nav-list > li > a.active {
            background: rgba(0,0,0,0.14);
            color: #fff;
            font-weight: 700;
            border-radius: 6px;
            box-shadow: inset 0 -6px 18px rgba(0,0,0,0.06);
        }

        .nav-list > li > a.active::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
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

        /* Hero Section */
        .hero {
            position: relative;
            background: linear-gradient(135deg, rgba(43, 17, 219, 0.8) 0%, rgba(0, 215, 179, 0.8) 100%), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23888888" width="1200" height="600"/></svg>');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 80px 20px;
            aspect-ratio: 16;
            min-height: 400px;
            max-height: 700px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 80px;
            z-index: 1;
            box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.1);
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
            aspect-ratio: 16 / 9;
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
            aspect-ratio: 16 / 9;
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
            background: linear-gradient(135deg, #00D7B3 0%, #00C99A 100%);
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 215, 179, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 215, 179, 0.4);
        }

        /* Section */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            width: 100%;
            box-sizing: border-box;
        }

        section {
            width: 100%;
            padding: 100px 20px;
            position: relative;
            z-index: 10;
            background: white;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
        }

        section h2 {
            text-align: center;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 16px;
            color: #2B11DB;
            width: 100%;
            background: linear-gradient(135deg, #2B11DB 0%, #00D7B3 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
    
        .section-description {
            text-align: center;
            max-width: 750px;
            margin: 0 auto 60px;
            color: #555;
            line-height: 1.9;
            width: 100%;
            box-sizing: border-box;
            padding: 0 20px;
            font-size: 16px;
            font-weight: 500;
        }

        /* Product Highlights */
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            padding: 0 20px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            width: 100%;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
            padding: 0 20px;
        }

        .product-card {
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

        .product-card:hover {
            border-color: #00D7B3;
            box-shadow: 0 8px 24px rgba(0, 215, 179, 0.25);
            transform: translateY(-6px) scale(1.01);
            border-width: 2px;
        }

        .product-image {
            width: 100%;
            height: 180px;
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #e0e0e0;
        }

        .product-card:hover .product-image {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        }

        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            padding: 16px;
        }

        .product-card:hover .product-image img {
            transform: scale(1.08);
        }

        .product-image iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .product-image video {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }

        .play-btn:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.4);
        }

        .product-info {
            padding: 0;
            background: white;
            width: 100%;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            gap: 0;
            flex-grow: 1;
        }

        .product-card > div:last-child {
            padding: 8px 12px 12px 12px;
            margin-top: auto;
        }

        .product-card h4 {
            padding: 12px 12px 6px 12px;
            margin: 0;
            color: #2B11DB;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.3;
        }

        .product-model {
            padding: 0 12px 8px 12px;
            margin: 0;
            color: #666;
            font-size: 11px;
            line-height: 1.4;
            min-height: 22px;
        }

        .product-description {
            padding: 0 12px 8px 12px;
            margin: 0;
            color: #666;
            font-size: 11px;
            line-height: 1.4;
            min-height: 22px;
        }

        .product-info p {
            margin: 0;
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

        .add-to-inquiry:active {
            transform: translateY(0);
        }

        .add-to-inquiry:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
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
            transition: all 0.3s ease;
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

        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #00D7B3 0%, #00C99A 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.3);
            z-index: 2;
        }

        /* Category Content Section */
        .category-content {
            width: 100%;
            margin: 0 auto;
            padding: 80px 20px;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
            display: flex;
            gap: 40px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .category-content > h2 {
            position: absolute;
            top: 40px;
            left: 20px;
            font-size: 54px;
            font-weight: 900;
            margin: 0;
            color: #2B11DB;
            line-height: 1.2;
            letter-spacing: -1.5px;
            text-align: left;
            width: 100%;
        }

        /* Products Filters Sidebar */
        .product-filters {
            width: 280px;
            flex-shrink: 0;
            margin-top: 120px;
        }

        .filter-section {
            margin-bottom: 32px;
            padding-bottom: 32px;
            border-bottom: 1px solid #e5e7eb;
        }

        .filter-section:last-child {
            border-bottom: none;
        }

        .filter-section h3 {
            font-size: 14px;
            font-weight: 700;
            color: #2B11DB;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .filter-option {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            cursor: pointer;
        }

        .filter-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #2B11DB;
        }

        .filter-option label {
            font-size: 14px;
            color: #555;
            cursor: pointer;
            flex: 1;
        }

        .filter-option span {
            font-size: 13px;
            color: #999;
            margin-left: auto;
        }

        .clear-filters-btn {
            width: 100%;
            padding: 12px 16px;
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .clear-filters-btn:hover {
            background: #ff3838;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 71, 87, 0.3);
        }

        /* Main Product Area */
        .main-product-area {
            flex: 1;
            margin-top: 120px;
        }

        .product-controls {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: #2B11DB;
            box-shadow: 0 0 0 3px rgba(43, 17, 219, 0.1);
        }

        .search-box i {
            color: #999;
            font-size: 16px;
        }

        .search-box input {
            border: none;
            outline: none;
            flex: 1;
            font-size: 14px;
            color: #333;
        }

        .search-box input::placeholder {
            color: #999;
        }

        .sort-dropdown {
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: white;
            color: #333;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .sort-dropdown:hover,
        .sort-dropdown:focus {
            border-color: #2B11DB;
            outline: none;
            box-shadow: 0 0 0 3px rgba(43, 17, 219, 0.1);
        }

        .view-toggle {
            display: flex;
            gap: 4px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px;
        }

        .view-toggle button {
            padding: 8px 12px;
            border: none;
            background: transparent;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .view-toggle button.active {
            background: #2B11DB;
            color: white;
        }

        .results-info {
            font-size: 14px;
            color: #666;
            white-space: nowrap;
        }

        .category-description {
            display: none;
        }
            margin: 0 0 60px 0;
            line-height: 1.7;
            max-width: 650px;
            font-weight: 500;
            letter-spacing: 0.2px;
        }

        /* Featured Section */


        .featured-section {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 70px 60px;
            border-radius: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 70px;
            align-items: center;
            box-shadow: 0 4px 20px rgba(43, 17, 219, 0.08);
            overflow: hidden;
            position: relative;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #e8eef7;
        }

        .featured-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 100% 0%, rgba(255,255,255,0.4) 0%, transparent 70%);
            pointer-events: none;
        }

        .featured-content {
            position: relative;
            z-index: 2;
        }

        .featured-badge {
            display: inline-block;
            background: linear-gradient(135deg, #00D7B3 0%, #00C99A 100%);
            color: white;
            padding: 8px 18px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.2px;
            margin-bottom: 24px;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 215, 179, 0.3);
        }

        .featured-content h3 {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 12px;
            color: #2B11DB;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .featured-content h3::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #2B11DB 0%, #00d4aa 100%);
            margin-top: 16px;
            margin-bottom: 24px;
            border-radius: 2px;
        }

        .featured-meta {
            display: flex;
            gap: 24px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            flex-wrap: wrap;
        }

        .featured-discount {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .featured-discount-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .featured-offer-text {
            color: #ff6b6b;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .featured-event-info {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .featured-event-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #333;
        }

        .featured-event-detail strong {
            color: #1a1a1a;
            font-weight: 600;
        }

        .featured-event-detail i {
            color: #2B11DB;
            font-size: 16px;
        }

        .featured-content p {
            color: #555;
            margin-bottom: 32px;
            line-height: 1.9;
            font-size: 16px;
            font-weight: 500;
        }

        .featured-btn {
            background: linear-gradient(135deg, #2B11DB 0%, #1e0aa3 100%);
            color: white;
            padding: 14px 42px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(43, 17, 219, 0.3);
            letter-spacing: 0.5px;
        }

        .featured-btn:hover {
            background: linear-gradient(135deg, #3d1ffa 0%, #2B11DB 100%);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(43, 17, 219, 0.4);
        }

        .featured-btn:active {
            transform: translateY(-1px);
        }

        .featured-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 400px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            box-shadow: 0 20px 40px rgba(43, 17, 219, 0.15);
            position: relative;
            z-index: 2;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid #e8eef7;
        }

        .featured-image img {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            border-radius: 12px;
        }

        .featured-image video {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 12px;
        }

        .featured-image iframe {
            display: block !important;
            width: 100% !important;
            height: 100% !important;
            border: none !important;
            border-radius: 12px;
        }

        /* Footer */
        footer {
            background: linear-gradient(135deg, #1a0d7a 0%, #2B11DB 100%);
            color: white;
            padding: 60px 0 40px;
            text-align: center;
            margin-top: auto;
            width: 100vw;
            position: relative;
            left: 0;
            right: 0;
            margin-left: 0;
            margin-right: 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .footer-content {
            width: 100%;
            margin: 0;
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.95);
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 4px;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #00D7B3;
            transition: width 0.3s ease;
        }

        .footer-links a:hover::after {
            width: 100%;
        }

        .footer-copyright {
            font-size: 14px;
            opacity: 0.85;
            font-weight: 500;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 24px;
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

            .browse-toggle {
                font-size: 12px;
                padding: 6px 8px;
                gap: 4px;
            }

            .hero h1 {
                font-size: 32px;
            }
            
            .hero {
                aspect-ratio: auto;
                min-height: 420px;
                padding: 20px 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .hero-content {
                max-width: 100%;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .hero-thumb {
                width: 85%;
                height: auto;
                max-width: 95%;
                aspect-ratio: 16 / 9 !important;
            }
            
            .product-image {
                aspect-ratio: 4 / 3;
                min-height: 240px;
            }
            
            .featured-image {
                aspect-ratio: 4 / 3;
                min-height: 260px;
            }

            .featured-section {
                grid-template-columns: 1fr;
                padding: 40px 28px;
                gap: 40px;
                border-radius: 16px;
            }

            .featured-content h3 {
                font-size: 28px;
                font-weight: 800;
            }

            .featured-meta {
                gap: 12px;
                padding-bottom: 12px;
            }

            .featured-event-info {
                gap: 12px;
            }

            .featured-event-detail {
                font-size: 13px;
            }

            .featured-btn {
                padding: 12px 32px;
                font-size: 14px;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            section h2 {
                font-size: 28px;
            }

            .section-description {
                font-size: 14px;
                margin-bottom: 28px;
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
            background: rgba(43, 17, 219, 0.08);
            padding-left: 12px;
            border-radius: 4px;
        }

        /* Subcategory hover when mini-sidebar is expanded */
        .mini-sidebar.expanded .sidebar-sublist a:hover {
            color: #00D7B3;
            background: rgba(255, 255, 255, 0.12);
            padding-left: 12px;
            border-radius: 4px;
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

        /* Expanded sidebar subcategory styling */
        .mini-sidebar.expanded .sidebar-sublist a {
            color: #ffffff;
            background: transparent;
        }

        .mini-sidebar.expanded .sidebar-sublist {
            background: transparent;
            border-left: 2px solid rgba(255, 255, 255, 0.15);
            margin-left: 12px;
            padding-left: 16px;
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

        .sidebar-list li.has-sub { position: relative; }
        .has-sub > a { padding-right: 40px; }


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
        /* Ensure header/navigation/footer do not animate or move */
        header, nav, footer, .header-top, .nav-inner, .browse-toggle, .nav-list, .right-actions, .footer-content {
            animation: none !important;
            transition: none !important;
            transform: none !important;
            opacity: 1 !important;
        }

        /* Prevent individual nav items from receiving reveal animations */
        .nav-list li { animation: none !important; opacity: 1 !important; transform: none !important; }

        /* Overlay sidebar (full-height left panel) */
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

        .sidebar-overlay {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            right: auto;
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

        .sidebar-list { list-style: none; padding: 28px 20px 0 20px; margin: 0; }
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

        /* Expanded sidebar active state styling */
        .sidebar-overlay.expanded .sidebar-list li a.active {
            background: #f3f4f6 !important;
            color: #2B11DB !important;
            font-weight: 600;
            border-left: 4px solid #2B11DB;
            padding-left: 12px;
            display: flex !important;
        }

        .sidebar-overlay.expanded .sidebar-list li a.active .sidebar-icon {
            color: #2B11DB !important;
        }

        /* Also ensure active link shows in regular sidebar state */
        .sidebar-list li a.active {
            background: #f3f4f6 !important;
            color: #2B11DB !important;
            font-weight: 600 !important;
            border-left: 4px solid #2B11DB !important;
            padding-left: 12px !important;
            display: flex !important;
        }

        .sidebar-list li a.active .sidebar-icon {
            color: #2B11DB !important;
        }

        .sidebar-sublist { 
            list-style: none; 
            margin: 0; 
            padding: 8px 0 8px 44px; 
            background: #fafafa;
            margin-left: 12px;
            margin-right: 12px;
            padding-left: 16px;
            border-left: 2px solid #e5e7eb;
            padding-top: 8px;
            padding-bottom: 8px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 0;
            display: none;
        }
        
        .sidebar-sublist.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
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
            background: rgba(43, 17, 219, 0.08);
            padding-left: 12px;
            border-radius: 4px;
        }

        /* Subcategory hover when mini-sidebar is expanded */
        .mini-sidebar.expanded .sidebar-sublist a:hover {
            color: #00D7B3;
            background: rgba(255, 255, 255, 0.12);
            padding-left: 12px;
            border-radius: 4px;
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

        /* Expanded sidebar subcategory styling */
        .mini-sidebar.expanded .sidebar-sublist a {
            color: #ffffff;
            background: transparent;
        }

        .mini-sidebar.expanded .sidebar-sublist {
            background: transparent;
            border-left: 2px solid rgba(255, 255, 255, 0.15);
            margin-left: 12px;
            padding-left: 16px;
        }

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

        .mini-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .mini-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .mini-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }

        .mini-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.2);
        }

        .mini-sidebar.expanded {
            width: 280px;
            overflow-y: auto;
            padding: 24px 16px;
            scrollbar-width: thin;
            scrollbar-color: rgba(0,0,0,0.1) transparent;
            align-items: stretch;
        }

        /* Style sidebar-list items when mini-sidebar is expanded */
        .mini-sidebar.expanded .sidebar-list {
            padding: 0;
            margin: 0;
        }

        .mini-sidebar.expanded .sidebar-list li a.active {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-weight: 600;
            border-left: 4px solid #00D7B3;
            padding-left: 12px;
        }

        .mini-sidebar.expanded .sidebar-list li a.active .sidebar-icon {
            color: #00D7B3;
        }

        .mini-sidebar.expanded .sidebar-list a {
            color: #ffffff;
        }

        .mini-sidebar.expanded .sidebar-list a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #00D7B3;
        }

        .mini-sidebar.expanded::-webkit-scrollbar {
            width: 6px;
        }

        .mini-sidebar.expanded::-webkit-scrollbar-track {
            background: transparent;
        }

        .mini-sidebar.expanded::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.1);
            border-radius: 3px;
        }

        .mini-sidebar.expanded::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.2);
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
            display: none !important;
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

        .mini-sidebar-icon.active-icon .label {
            color: #2B11DB;
            font-weight: 600;
        }

        /* Active icon styling when expanded */
        .mini-sidebar.expanded .mini-sidebar-icon.active-icon {
            background: rgba(0, 215, 179, 0.25) !important;
            color: #00D7B3 !important;
            font-weight: 600;
            border-left: 4px solid #00D7B3;
            padding-left: 10px;
            transform: none;
        }

        .mini-sidebar.expanded .mini-sidebar-icon.active-icon .label {
            color: #00D7B3 !important;
            font-weight: 600;
        }

        .mini-sidebar-icon.active-icon .label {
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

        .mini-sidebar-icon .sub-indicator:active {
            transform: translateY(0);
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
            padding: 14px;
            min-width: auto;
            margin-bottom: 12px;
        }

        /* Adjust main container for mini sidebar — collapsed by default on desktop */
        section,
        footer,
        .page-content,
        .main-content, 
        .category-container {
            margin-left: 0px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* When expanded, increase margin */
        .mini-sidebar.expanded ~ section,
        .mini-sidebar.expanded ~ footer,
        .mini-sidebar.expanded ~ .page-content,
        .mini-sidebar.expanded ~ .main-content,
        .mini-sidebar.expanded ~ .category-container {
            margin-left: 280px;
        }

        @media (max-width: 992px) {
            section,
            footer,
            .page-content, 
            .main-content, 
            .category-container {
                margin-left: 0 !important;
            }

            .mini-sidebar {
                display: none !important;
            }
        }

        /* When sidebar is expanded (collapsed mini) */
        .sidebar-overlay.expanded {
            width: 380px;
        }

        .overlay-backdrop.expanded {
            display: none !important;
        }

        @media (max-width: 768px) {
            .mini-sidebar {
                top: calc(14px + 36px + 14px + 40px);
                width: 56px !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease, width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .mini-sidebar.mobile-visible {
                transform: translateX(0);
            }
            .mini-sidebar.expanded {
                width: 240px !important;
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
            /* Floating toggle button to show/hide mini sidebar on mobile */
            .mobile-sidebar-fab {
                display: flex !important;
            }
        }

        /* FAB button to toggle mini sidebar on mobile */
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

        /* Brands Grid Styling - Premium Elegant Design */
        #brands-list {
            padding: 0;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 50%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background elements */
        #brands-list::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 1000px;
            height: 1000px;
            background: radial-gradient(circle, rgba(0, 102, 255, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        #brands-list::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -50%;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(0, 215, 179, 0.06) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        #brands-list h2 {
            font-size: 72px;
            font-weight: 900;
            margin-bottom: 16px;
            margin-top: 0;
            padding-top: 80px;
            background: linear-gradient(135deg, #00a884 0%, #0066ff 50%, #00a884 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
            text-align: center;
            letter-spacing: -1px;
            line-height: 1.1;
            text-transform: capitalize;
            filter: drop-shadow(0 0 30px rgba(0, 168, 132, 0.08));
        }

        #brands-list .section-description {
            font-size: 18px;
            color: #3f4a5e;
            text-align: center;
            margin-bottom: 100px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.8;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .brands-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 40px;
            margin-top: 60px;
            margin-bottom: 100px;
            padding: 0 20px;
        }

        .brand-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 24px;
            padding: 50px 32px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.9) 100%);
            border: 3px solid rgba(0, 215, 179, 0.35);
            border-radius: 20px;
            text-decoration: none;
            color: #1f2937;
            transition: all 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            min-height: 420px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08), inset 0 1px 1px rgba(255, 255, 255, 0.5), 0 0 1px rgba(0, 215, 179, 0.2);
            border-top: 3px solid rgba(0, 215, 179, 0.6);
        }

        /* Animated background glow */
        .brand-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 215, 179, 0.15) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.7s ease;
        }

        .brand-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 215, 179, 0.1) 0%, rgba(0, 102, 255, 0.08) 100%);
            opacity: 0;
            transition: opacity 0.7s ease;
            pointer-events: none;
        }

        .brand-card:hover::before {
            opacity: 1;
        }

        .brand-card:hover::after {
            opacity: 1;
        }

        .brand-card:hover {
            transform: translateY(-24px) scale(1.03);
            border-color: rgba(0, 215, 179, 0.8);
            box-shadow: 0 40px 100px rgba(0, 215, 179, 0.25), inset 0 1px 1px rgba(255, 255, 255, 0.8), inset 0 -1px 0 rgba(0, 215, 179, 0.1), 0 0 20px rgba(0, 215, 179, 0.15);
            border-top-color: rgba(0, 215, 179, 0.9);
        }

        .brand-logo-container {
            width: 220px;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0, 215, 179, 0.12) 0%, rgba(0, 102, 255, 0.1) 100%);
            border: 2.5px solid rgba(0, 215, 179, 0.4);
            border-radius: 20px;
            padding: 20px;
            transition: all 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            flex-shrink: 0;
            position: relative;
            z-index: 2;
            box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.6), 0 4px 15px rgba(0, 215, 179, 0.1);
        }

        .brand-card:hover .brand-logo-container {
            background: linear-gradient(135deg, rgba(0, 215, 179, 0.2) 0%, rgba(0, 102, 255, 0.15) 100%);
            border-color: rgba(0, 215, 179, 0.75);
            transform: scale(1.15) rotate(3deg);
            box-shadow: 0 20px 50px rgba(0, 215, 179, 0.3), inset 0 1px 3px rgba(255, 255, 255, 0.8);
        }

        .brand-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
            filter: brightness(1.05) saturate(1.1) contrast(1.1);
            transition: all 0.7s ease;
        }

        .brand-card:hover .brand-logo {
            filter: brightness(1.15) saturate(1.3) contrast(1.2) drop-shadow(0 0 15px rgba(0, 215, 179, 0.4));
        }

        .brand-card h3 {
            font-size: 24px;
            font-weight: 900;
            text-align: center;
            color: #2B11DB;
            line-height: 1.2;
            letter-spacing: 1.2px;
            transition: all 0.7s ease;
            position: relative;
            z-index: 2;
            text-transform: uppercase;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .brand-card:hover h3 {
            color: #0066ff;
            font-size: 24px;
            letter-spacing: 1.5px;
            font-weight: 800;
            text-shadow: 0 4px 8px rgba(0, 102, 255, 0.2);
        }

        /* Decorative accent line - industrial metallic bottom stripe */
        .brand-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, rgba(0, 215, 179, 0.8) 50%, transparent 100%);
            opacity: 0;
            transition: all 0.7s ease;
        }

        .brand-card:hover::after {
            opacity: 1;
            height: 4px;
            box-shadow: 0 -2px 10px rgba(0, 215, 179, 0.3);
        }

        @media (max-width: 1200px) {
            .brands-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 32px;
            }

            #brands-list h2 {
                font-size: 56px;
            }
        }

        @media (max-width: 768px) {
            #brands-list {
                padding: 0;
            }

            #brands-list h2 {
                font-size: 42px;
                padding-top: 60px;
                margin-bottom: 16px;
            }

            #brands-list .section-description {
                font-size: 16px;
                margin-bottom: 60px;
            }

            .brands-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 24px;
                margin-top: 40px;
                margin-bottom: 60px;
                padding: 0 15px;
            }

            .brand-card {
                padding: 32px 24px;
                min-height: 260px;
                gap: 16px;
            }

            .brand-logo-container {
                width: 140px;
                height: 140px;
            }

            .brand-card h3 {
                font-size: 16px;
                font-weight: 700;
                color: #1a0080;
            }

            .brand-card:hover h3 {
                font-size: 18px;
                font-weight: 800;
                color: #0066ff;
            }
        }

        @media (max-width: 480px) {
            #brands-list h2 {
                font-size: 32px;
                padding-top: 50px;
            }

            #brands-list .section-description {
                font-size: 14px;
                margin-bottom: 40px;
            }

            .brands-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
                margin-bottom: 50px;
            }

            .brand-card {
                padding: 24px 16px;
                min-height: 220px;
                gap: 12px;
            }

            .brand-logo-container {
                width: 90px;
                height: 90px;
            }

            .brand-card h3 {
                font-size: 14px;
                font-weight: 700;
                color: #1a0080;
            }

            .brand-card:hover h3 {
                font-size: 15px;
                font-weight: 800;
                color: #0066ff;
            }
        }

        @media (max-width: 1200px) {
            .brands-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 32px;
            }

            #brands-list h2 {
                font-size: 56px;
            }
        }

        @media (max-width: 768px) {
            #brands-list {
                padding: 0;
            }

            #brands-list h2 {
                font-size: 42px;
                padding-top: 60px;
                margin-bottom: 16px;
            }

            #brands-list .section-description {
                font-size: 16px;
                margin-bottom: 60px;
            }

            .brands-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 24px;
                margin-top: 40px;
                margin-bottom: 60px;
                padding: 0 15px;
            }

            .brand-card {
                padding: 32px 24px;
                min-height: 260px;
                gap: 16px;
            }

            .brand-logo-container {
                width: 110px;
                height: 110px;
            }

            .brand-card h3 {
                font-size: 16px;
                font-weight: 700;
                color: #1a0080;
            }

            .brand-card:hover h3 {
                font-size: 18px;
                font-weight: 800;
                color: #0066ff;
            }
        }

        @media (max-width: 480px) {
            #brands-list h2 {
                font-size: 32px;
                padding-top: 50px;
            }

            #brands-list .section-description {
                font-size: 14px;
                margin-bottom: 40px;
            }

            .brands-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
                margin-bottom: 50px;
            }

            .brand-card {
                padding: 24px 16px;
                min-height: 220px;
                gap: 12px;
            }

            .brand-logo-container {
                width: 110px;
                height: 110px;
            }

            .brand-card h3 {
                font-size: 14px;
                font-weight: 700;
                color: #1a0080;
            }

            .brand-card:hover h3 {
                font-size: 15px;
                font-weight: 800;
                color: #0066ff;
            }
        }

        /* Responsive: Filters and Product Area */
        @media (max-width: 1024px) {
            .category-content {
                flex-direction: column;
                gap: 20px;
            }

            .product-filters {
                width: 100%;
                margin-top: 0;
            }

            .main-product-area {
                margin-top: 0;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .product-controls {
                gap: 10px;
            }

            .search-box {
                min-width: 200px;
            }
        }

        @media (max-width: 768px) {
            .product-filters {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
                width: 100%;
            }

            .filter-section {
                border-bottom: none;
                border: 1px solid #e5e7eb;
                padding: 12px;
                border-radius: 6px;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }

            .product-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
                order: 1;
            }

            .sort-dropdown {
                width: 100%;
            }

            .view-toggle {
                width: 100%;
                justify-content: center;
            }

            .results-info {
                text-align: center;
                order: -1;
            }
        }

        @media (max-width: 480px) {
            .category-content > h2 {
                font-size: 32px;
                top: 20px;
            }

            .product-filters {
                grid-template-columns: 1fr;
            }

            .product-controls {
                gap: 8px;
            }

            .search-box input {
                font-size: 14px;
            }
        }
        @media (max-width: 992px) {
            .category-content {
                padding: 60px 20px;
            }

            .category-content h2 {
                font-size: 44px;
                margin-bottom: 18px;
            }

            .category-description {
                font-size: 15px;
                margin-bottom: 45px;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 26px;
            }

            .product-card h4 {
                font-size: 14px;
            }

            .add-to-inquiry {
                padding: 10px 18px;
                font-size: 11px;
            }
        }

        @media (max-width: 768px) {
            .category-content {
                padding: 50px 16px;
            }

            .category-content h2 {
                font-size: 38px;
                margin-bottom: 14px;
            }

            .category-description {
                font-size: 14px;
                margin-bottom: 40px;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 18px;
                padding: 0 8px;
            }

            .product-image {
                height: 220px;
            }

            .product-info {
                padding: 18px;
                gap: 10px;
            }

            .product-card h4 {
                font-size: 13px;
                margin-bottom: 2px;
            }

            .product-model {
                font-size: 11px;
            }

            .product-description {
                font-size: 12px;
                margin-bottom: 10px;
            }

            .add-to-inquiry {
                padding: 9px 16px;
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .category-content {
                padding: 40px 12px;
            }

            .category-content h2 {
                font-size: 28px;
                margin-bottom: 10px;
                letter-spacing: -0.8px;
            }

            .category-description {
                font-size: 13px;
                margin-bottom: 30px;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                padding: 0;
            }

            .product-image {
                height: 240px;
            }

            .product-info {
                padding: 16px;
                gap: 10px;
            }

            .product-card h4 {
                font-size: 13px;
                margin-bottom: 2px;
            }

            .product-model {
                font-size: 10px;
            }

            .product-description {
                font-size: 12px;
                margin-bottom: 12px;
            }

            .add-to-inquiry {
                padding: 10px 14px;
                font-size: 11px;
            }
        }

        /* ============================================
           PRODUCT MODAL
           ============================================ */
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
            height: 400px;
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
        }

        .media-item img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .media-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            padding: 16px;
            background: rgba(255,255,255,0.95);
        }

        .media-nav-btn {
            background: white;
            border: 1.5px solid #ddd;
            color: #2B11DB;
            font-size: 18px;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-nav-btn:hover {
            border-color: #00D7B3;
            color: #00D7B3;
            background: rgba(0, 215, 179, 0.08);
        }

        .media-dots {
            display: flex;
            gap: 6px;
        }

        .media-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .media-dot.active {
            background: #00D7B3;
            width: 24px;
            border-radius: 4px;
        }

        .modal-content {
            padding: 32px;
            overflow-y: auto;
            flex: 1;
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

        .modal-specs {
            margin: 32px 0;
            padding-bottom: 32px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-specs h3 {
            color: #2B11DB;
            font-size: 14px;
            font-weight: 800;
            margin: 0 0 16px 0;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .specs-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .specs-list li {
            color: #555;
            font-size: 12px;
            line-height: 1.6;
        }

        .specs-list strong {
            color: #2B11DB;
            font-weight: 700;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .modal-inquiry-btn {
            flex: 1;
            background: linear-gradient(135deg, #00D7B3 0%, #00C9A0 100%);
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .modal-inquiry-btn:hover {
            background: linear-gradient(135deg, #00C9A0, #00B690);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 215, 179, 0.3);
        }

        .modal-close-btn {
            flex: 1;
            background: #f0f0f0;
            color: #2B11DB;
            border: 1.5px solid #e0e0e0;
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modal-close-btn:hover {
            background: #e8e8e8;
            border-color: #2B11DB;
            color: #2B11DB;
        }

        @media (max-width: 768px) {
            .modal-media {
                height: 300px;
            }

            .modal-content {
                padding: 20px;
            }

            .modal-content h2 {
                font-size: 22px;
            }

            .specs-list {
                grid-template-columns: 1fr;
            }

            .modal-actions {
                flex-direction: column;
            }

            .modal-actions button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
        <?php
        // Set page title
        $page_title = "Brands";
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
                <div class="logo-box"><a href="../home.php"><img src="../assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
            </div>

            <div class="search-bar">
                <form class="search-field" action="../search.php" method="get">
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="../inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
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
                        <a href="../home.php">Home</a>
                        <div class="nav-dropdown">
                            <h4>Welcome</h4>
                            <p>Discover our complete range of industrial welding solutions and equipment.</p>
                        </div>
                    </li>
                    <li>
                        <a href="../aboutus.php">About Us</a>
                        <div class="nav-dropdown">
                            <h4>Our Company</h4>
                            <ul>
                                <li><a href="../aboutus.php#mission">Our Mission</a></li>
                                <li><a href="../aboutus.php#history">Company History</a></li>
                                <li><a href="../aboutus.php#team">Our Team</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../brands.php">Brands</a>
                        <div class="nav-dropdown">
                            <h4>Featured Brands</h4>
                            <ul>
                                <li><a href="../brand.php?name=Panasonic%20Connect"><img src="../assets/brands/PANASONIC.jpg" alt="Panasonic Connect" title="Panasonic Connect"></a></li>
                                <li><a href="../brand.php?name=Robot%20Systems"><img src="../assets/brands/ROBOT SYSTEMS.jpg" alt="Robot Systems Peripherals" title="Robot Systems Peripherals"></a></li>
                                <li><a href="../brand.php?name=Kobelco"><img src="../assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                                <li><a href="../brand.php?name=Metrode"><img src="../assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                                <li><a href="../brand.php?name=DryRod.%20II"><img src="../assets/brands/DRYROD.jpg" alt="DryRod. II" title="DryRod. II"></a></li>
                                <li><a href="../brand.php?name=Weldcraft"><img src="../assets/brands/WELDCRAFT.jpg" alt="Weldcraft" title="Weldcraft"></a></li>
                                <li><a href="../brand.php?name=Truweld"><img src="../assets/brands/TRUWELD.jpg" alt="Truweld" title="Truweld"></a></li>
                                <li><a href="../brand.php?name=Arcair"><img src="../assets/brands/ARCAIR.jpg" alt="Arcair" title="Arcair"></a></li>
                                <li><a href="../brand.php?name=MAGNAFLUX"><img src="../assets/brands/MAGNAFLUX.jpg" alt="Magnaflux" title="Magnaflux"></a></li>
                                <li><a href="../brand.php?name=Tempilstik"><img src="../assets/brands/TEMPILSTIK.jpg" alt="Tempilstik" title="Tempilstik"></a></li>
                                <li><a href="../brand.php?name=TANAKA"><img src="../assets/brands/TANAKA.jpg" alt="Tanaka" title="Tanaka"></a></li>
                                <li><a href="../brand.php?name=CHIYODA"><img src="../assets/brands/CHIYODA.jpg" alt="Chiyoda" title="Chiyoda"></a></li>
                                <li><a href="../brand.php?name=Yutaka"><img src="../assets/brands/YUTAKA.jpg" alt="Yutaka" title="Yutaka"></a></li>
                                <li><a href="../brand.php?name=HARDWORKER"><img src="../assets/brands/HARDWORKER.jpg" alt="Hard Workers" title="Hard Workers"></a></li>
                                <li><a href="../brand.php?name=Soyer"><img src="../assets/brands/SOYER.jpg" alt="Soyer" title="Soyer"></a></li>
                                <li><a href="../brand.php?name=Aquasol"><img src="../assets/brands/AQUASOL.jpg" alt="Aquasol" title="Aquasol"></a></li>
                                <li><a href="../brand.php?name=SK%20And%20GAL%20GAGE"><img src="../assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE" title="SK And GAL GAGE"></a></li>
                                <li><a href="brand.php?name=COPPUS"><img src="assets/brands/COPPUS.jpg" alt="Coppus" title="Coppus"></a></li>
                                <li><a href="brand.php?name=BW%20Technologies"><img src="assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies" title="BW Technologies"></a></li>
                                <li><a href="../brand.php?name=RAC"><img src="../assets/brands/RAE%20SYSTEMS.jpg" alt="RAC" title="RAC"></a></li>
                                <li><a href="../brand.php?name=WELDAS"><img src="../assets/brands/WELDAS.jpg" alt="Weldas" title="Weldas"></a></li>
                                <li><a href="../brand.php?name=UVEX"><img src="../assets/brands/UVEX.jpg" alt="Uvex" title="Uvex"></a></li>
                                <li><a href="../brand.php?name=ACES"><img src="../assets/brands/ACES.jpg" alt="Aces" title="Aces"></a></li>
                                <li><a href="../brand.php?name=MICROGARD"><img src="../assets/brands/MICROGARD.jpg" alt="Microgard" title="Microgard"></a></li>
                                <li><a href="../brand.php?name=ANSELL"><img src="../assets/brands/ANSELL.jpg" alt="Ansell" title="Ansell"></a></li>
                                <li><a href="../brand.php?name=Alfra"><img src="../assets/brands/ALFRA.jpg" alt="Alfra" title="Alfra"></a></li>
                                <li><a href="../brand.php?name=BOSCH"><img src="../assets/brands/BOSCH.jpg" alt="Bosch" title="Bosch"></a></li>
                                <li><a href="../brand.php?name=Makita"><img src="../assets/brands/MAKITA.jpg" alt="Makita" title="Makita"></a></li>
                                <li><a href="../brand.php?name=Weller"><img src="../assets/brands/WEILER.jpg" alt="Weller" title="Weller"></a></li>
                                <li><a href="../brand.php?name=Garryson"><img src="../assets/brands/GARRYSON.jpg" alt="Garryson" title="Garryson"></a></li>
                                <li><a href="../brand.php?name=Spilfyter"><img src="../assets/brands/SPILFYTER.jpg" alt="Spilfyter" title="Spilfyter"></a></li>
                                <li><a href="../brand.php?name=Dalo"><img src="../assets/brands/DALO.jpg" alt="Dalo" title="Dalo"></a></li>
                                <li><a href="brand.php?name=MOTOLITE"><img src="assets/brands/MOTOLITE.jpg" alt="Motolite" title="Motolite"></a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../industries.php">Industries</a>
                        <div class="nav-dropdown">
                            <h4>Industries We Serve</h4>
                            <ul>
                                <li><a href="../industries.php#manufacturing">Manufacturing</a></li>
                                <li><a href="../industries.php#construction">Construction</a></li>
                                <li><a href="../industries.php#automotive">Automotive</a></li>
                                <li><a href="../industries.php#shipbuilding">Shipbuilding</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../services.php">Services</a>
                        <div class="nav-dropdown">
                            <h4>Our Services</h4>
                            <ul>
                                <li><a href="../services.php#consultation">Technical Consultation</a></li>
                                <li><a href="../services.php#training">Training Programs</a></li>
                                <li><a href="../services.php#maintenance">Equipment Maintenance</a></li>
                                <li><a href="../services.php#support">After-Sales Support</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../contact.php">Contact Us</a>
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

    <!-- Sidebar Navigation -->
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
                <a href="arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
                <button class="sub-toggle" aria-controls="sub-arc-welding" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="tig-welding-machine.php">TIG Welding Machine</a></li>
                    <li><a href="plasma-cutting-machine.php">Plasma Cutting Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-controls="sub-arc-robots" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                    <li><a href="../arc-welding-robots/g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="../arc-welding-robots/g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="../arc-welding-robots/featured-products-and-solution.php">Featured Products and Solutions</a></li>
                    <li><a href="../arc-welding-robots/robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-controls="sub-batteries" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="../batteries/maintenance-free.php">Maintenance Free</a></li>
                    <li><a href="../batteries/low-maintenance.php">Low Maintenance</a></li>
                    <li><a href="../batteries/special-batteries.php">Special Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
                <button class="sub-toggle" aria-controls="sub-drilling-lifting" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="../drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li><a href="../drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a></li>
                    <li><a href="../drilling-and-lifting/cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
                <button class="sub-toggle" aria-controls="sub-gas-detectors" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="../gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="../gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                    <li><a href="../gas-detectors/portable-gas-detectors.php">Portable Gas Detectors</a></li>
                    <li><a href="../gas-detectors/docking-data-management.php">Docking and Data Management</a></li>
                    <li><a href="../gas-detectors/calibration-gas-regulators.php">Calibration Gas and Regulators</a></li>
                </ul>
            </li>
            <li class="">
                <a href="../portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
            </li>
            <li class="has-sub">
                <a href="../power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-controls="sub-power-tool" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                    <li><a href="../power-tools/grinder.php">Grinder</a></li>
                    <li><a href="../power-tools/saw.php">Saw</a></li>
                    <li><a href="../power-tools/drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="../power-tools/rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="../power-tools/accessories.php">Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
                <button class="sub-toggle" aria-controls="sub-protection-safety" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="../protection/eye-protection.php">Eye Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="../protection/hand-protection.php">Hand Protection</a>
                        <button class="nested-toggle" aria-controls="nested-hand-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                        <ul id="nested-hand-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="../protection/working-gloves.php">Working Gloves</a></li>
                            <li><a href="../protection/chemical-liquid-protection-gloves.php">Chemical and Liquid Protection Gloves</a></li>
                            <li><a href="../protection/disposable-gloves.php">Disposable Gloves</a></li>
                            <li><a href="../protection/welding-gloves.php">Welding Gloves</a></li>
                        </ul>
                    </li>
                    <li><a href="../protection/hearing-respiratory-protection.php">Hearing &amp; Respiratory Protection</a></li>
                    <li><a href="../protection/welding-head-and-face-protection.php">Welding Head and Face Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="../protection/body-protection.php">Body Protection</a>
                        <button class="nested-toggle" aria-controls="nested-body-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
                        <ul id="nested-body-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="../protection/chemical-flame-retardant.php">Chemical and Flame Retardant</a></li>
                            <li><a href="../protection/liquid-spray-splash.php">Liquid Spray and Splash</a></li>
                            <li><a href="../protection/particulate-low-hazard.php">Particulate and Low Hazard</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
                <button class="sub-toggle" aria-controls="sub-welding-accessories" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-welding-accessories" class="sidebar-sublist collapsed">
                    <li><a href="../welding-accessories/welding-electrode-oven.php">Welding Electrode Oven</a></li>
                    <li><a href="../welding-accessories/non-destructive-crack-detection.php">Non-Destructive Crack Detection</a></li>
                    <li><a href="../welding-accessories/gas-saving-regulator.php">Gas Saving Regulator</a></li>
                    <li><a href="../welding-accessories/gas-cutting-equipment.php">Gas Cutting Equipment</a></li>
                    <li><a href="../welding-accessories/industrial-markers.php">Industrial Markers</a></li>
                    <li><a href="../welding-accessories/measuring-gauge.php">Measuring Gauge</a></li>
                    <li><a href="../welding-accessories/others.php">Others</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
                <button class="sub-toggle" aria-controls="sub-welding-consumables" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="../welding-consumables/kobelco.php">Kobelco</a></li>
                    <li><a href="../welding-consumables/metrode.php">Metrode</a></li>
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
        <div class="mini-sidebar-icon has-sub" data-target="arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../arc-welding-robots/arc-welding-robot.php" title="Arc Welding Robots"><i class="bi bi-robot"></i><span class="label">Arc Welding Robots</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../batteries/batteries.php" title="Batteries"><i class="bi bi-lightning-fill"></i><span class="label">Batteries</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../drilling-and-lifting/drilling-and-lifting.php" title="Drilling and Lifting"><i class="bi bi-hammer"></i><span class="label">Drilling and Lifting</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../gas-detectors/gas-detectors.php" title="Gas Detectors"><i class="bi bi-bullseye"></i><span class="label">Gas Detectors</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../portable-ventilators/portable-ventilators.php" title="Portable Ventilators"><i class="bi bi-fan"></i><span class="label">Portable Ventilators</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../power-tools/power-tools.php" title="Power Tools"><i class="bi bi-tools"></i><span class="label">Power Tools</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../protection/protection.php" title="Personal Protective Equipment"><i class="bi bi-shield-check"></i><span class="label">PPE</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../welding-accessories/welding-accessories.php" title="Welding Accessories"><i class="bi bi-gear"></i><span class="label">Welding Accessories</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../welding-consumables/welding-consumables.php" title="Welding Consumables"><i class="bi bi-box"></i><span class="label">Welding Consumables</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
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

        <div class="category-content">
            <h2><?php echo htmlspecialchars($current_category['name'] ?? 'Arc Welding Machines'); ?></h2>
            
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
                        <span>(0)</span>
                    </div>
                    <div class="filter-option">
                        <input type="checkbox" id="cat-new" onchange="filterProducts()">
                        <label for="cat-new">New Items</label>
                        <span>(0)</span>
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
                        <span id="resultsCount">Showing <?php echo count($all_products ?? []); ?> products</span>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="product-grid">
                <?php 
                // Fetch saw products
                $all_products = andison_get_products_for_subcategory('power-tools', 'saw');
                
                // Display products
                if (!empty($all_products)) {
                    foreach ($all_products as $product) {
                        $image_src = htmlspecialchars($product['image'] ?? '');
                        // Adjust image path for subdirectory context - assets are at root level
                        if ($image_src) {
                            // If path starts with assets/, add ../ since we're in subdirectory
                            if (strpos($image_src, 'assets/') === 0) {
                                $image_src = '../' . $image_src;
                            }
                            // If path starts with andison/, convert to ../assets/ path
                            else if (strpos($image_src, 'andison/assets/') === 0) {
                                $image_src = '../' . substr($image_src, 8);
                            }
                        }
                        $model = htmlspecialchars($product['model'] ?? '');
                        $name = htmlspecialchars($product['name'] ?? '');
                        $type = htmlspecialchars($product['type'] ?? 'Power Tool');
                        $brand = htmlspecialchars($product['brand'] ?? 'Bosch');
                        $description = htmlspecialchars($product['description'] ?? '');
                        $badge = htmlspecialchars($product['badge'] ?? '');
                        ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($image_src)): ?>
                            <img src="<?php echo $image_src; ?>" alt="<?php echo $name; ?>" onerror="this.parentElement.innerHTML='<i class=&quot;bi bi-tools&quot; style=&quot;font-size: 56px; color: #ccc;&quot;></i>'">
                        <?php else: ?>
                            <i class="bi bi-tools" style="font-size: 56px; color: #ccc;"></i>
                        <?php endif; ?>
                        <?php if (!empty($badge)): ?>
                            <div class="product-badge"><?php echo $badge; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <?php if (!empty($model)): ?>
                            <p class="product-model"><?php echo $model; ?></p>
                        <?php endif; ?>
                        <h4><?php echo $name ?: 'Product'; ?></h4>
                        <?php if (!empty($description)): ?>
                            <p class="product-description"><?php echo $description; ?></p>
                        <?php endif; ?>
                        <button class="add-to-inquiry" type="button" data-model="<?php echo $model; ?>" data-type="<?php echo $type ?: 'Equipment'; ?>" data-brand="<?php echo $brand; ?>">ADD TO INQUIRY LIST</button>
                    </div>
                </div>
                        <?php
                    }
                } else {
                    // Fallback to placeholder if no products
                    ?>
                <div class="product-card">
                    <div class="product-image">
                        <i class="bi bi-lightning-charge" style="font-size: 56px; color: #ccc;"></i>
                    </div>
                    <div class="product-info">
                        <h4>Arc Welding Machines</h4>
                        <p class="product-description">No products available</p>
                        <button class="add-to-inquiry" type="button" data-model="Arc Welding Machines" data-type="Equipment" data-brand="Industrial" disabled>ADD TO INQUIRY</button>
                    </div>
                </div>
                <?php
                }
                ?>
                </div><!-- /.product-grid -->

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
                    <span class="page-info">Page 1 of 2</span>
                </div>
            </div><!-- /.main-product-area -->
        </div><!-- /.category-content -->
    </div><!-- /.page-content -->

    <!-- Footer -->
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
    </div><!-- /.page-content -->
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
        // Sidebar sub-toggle functionality - Show popover card
        (function(){
            var mainSidebarPopover = document.getElementById('miniPopover');
            var popoverTitle = mainSidebarPopover ? mainSidebarPopover.querySelector('.mini-popover-title') : null;
            var popoverList = mainSidebarPopover ? mainSidebarPopover.querySelector('.mini-popover-list') : null;
            var currentMainSidebarKey = null;
            
            function showMainSidebarPopover(toggle) {
                if (!mainSidebarPopover || !popoverList || !popoverTitle) return;
                
                var sublistId = toggle.getAttribute('aria-controls');
                var sublist = document.getElementById(sublistId);
                if (!sublist) return;
                
                // Extract title from parent li's main link
                var parentLi = toggle.closest('.has-sub');
                var mainLink = parentLi ? parentLi.querySelector(':scope > a:not([class])') : null;
                var title = mainLink ? mainLink.textContent.trim() : 'Items';
                
                // Extract items from the sublist
                var items = [];
                var listItems = sublist.querySelectorAll('li > a');
                listItems.forEach(function(link) {
                    items.push({
                        text: link.textContent.trim(),
                        href: link.getAttribute('href') || '#'
                    });
                });
                
                // Populate popover
                popoverTitle.textContent = title;
                popoverList.innerHTML = '';
                items.forEach(function(item) {
                    var li = document.createElement('li');
                    li.className = 'mini-popover-item';
                    li.innerHTML = '<span class="square"></span><a href="' + item.href + '">' + item.text + '</a>';
                    popoverList.appendChild(li);
                });
                
                // Position popover next to toggle button
                setTimeout(function() {
                    // First, add the show class to make it visible (but positioned off-screen)
                    mainSidebarPopover.style.left = '-9999px';
                    mainSidebarPopover.style.top = '-9999px';
                    mainSidebarPopover.classList.add('show');
                    
                    var toggleRect = toggle.getBoundingClientRect();
                    var pw = mainSidebarPopover.offsetWidth;
                    var ph = mainSidebarPopover.offsetHeight;
                    var toggleCenterY = toggleRect.top + toggleRect.height / 2;
                    
                    // Position to the right of the toggle button
                    var left = Math.round(toggleRect.right + 14);
                    var top = Math.round(toggleCenterY - ph / 2);
                    
                    // Adjust if off-screen horizontally
                    if (left + pw + 12 > window.innerWidth) {
                        left = Math.round(toggleRect.left - pw - 14);
                    }
                    
                    // Adjust if off-screen vertically
                    var headerHeight = 100;
                    var minTop = headerHeight + 12;
                    var maxTop = window.innerHeight - ph - 12;
                    if (top < minTop) top = minTop;
                    if (top > maxTop) top = maxTop;
                    
                    var arrowOffset = toggleCenterY - top - 26;
                    mainSidebarPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');
                    
                    // Now position it at the final location
                    mainSidebarPopover.style.left = left + 'px';
                    mainSidebarPopover.style.top = top + 'px';
                    mainSidebarPopover.setAttribute('aria-hidden', 'false');
                    currentMainSidebarKey = sublistId;
                }, 5);
            }
            
            function hideMainSidebarPopover() {
                if (!mainSidebarPopover) return;
                mainSidebarPopover.classList.remove('show');
                mainSidebarPopover.setAttribute('aria-hidden', 'true');
                currentMainSidebarKey = null;
            }
            
            var subToggles = document.querySelectorAll('.sub-toggle');
            
            subToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Toggle popover
                    if (currentMainSidebarKey === toggle.getAttribute('aria-controls') && mainSidebarPopover.classList.contains('show')) {
                        hideMainSidebarPopover();
                    } else {
                        showMainSidebarPopover(toggle);
                    }
                });
            });
            
            // Close popover on outside click
            document.addEventListener('click', function(e) {
                if (!mainSidebarPopover) return;
                if (!mainSidebarPopover.classList.contains('show')) return;
                if (e.target.closest('.mini-popover') || e.target.closest('.sub-toggle')) return;
                hideMainSidebarPopover();
            });
            
            // Close popover on Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') hideMainSidebarPopover();
            });
        })();
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
        
        // Highlight current category in mini-sidebar
        (function(){
            var currentCategory = '<?php echo htmlspecialchars(strtolower(basename(dirname($_SERVER["PHP_SELF"])))); ?>';
            var miniIcons = document.querySelectorAll('.mini-sidebar-icon');
            miniIcons.forEach(function(icon){
                var target = (icon.getAttribute('data-target') || '').toLowerCase().replace('../', '');
                var category = target.split('/')[0].replace('.php', '');
                if(category && category === currentCategory) {
                    icon.classList.add('active-icon');
                }
            });
        })();
    </script>
    <script>
        // Manage aria states for contact dropdown (improves accessibility)
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

                // Mobile: click to toggle
                dd.addEventListener('click', function(e){
                    if(window.innerWidth > 768) return;
                    e.stopPropagation();
                    var isOpen = dd.classList.contains('open');
                    document.querySelectorAll('.contact-dropdown').forEach(function(d){ d.classList.remove('open'); });
                    if(!isOpen) dd.classList.add('open');
                });

                // Close button
                var closeBtn = dd.querySelector('.contact-close');
                if(closeBtn){
                    closeBtn.addEventListener('click', function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        pop.setAttribute('aria-hidden','true');
                        dd.setAttribute('aria-expanded','false');
                        dd.classList.add('closed');
                        dd.classList.remove('open');
                        document.activeElement.blur();
                    });
                }
            });

            // Mobile: click outside closes all
            document.addEventListener('click', function(){
                if(window.innerWidth > 768) return;
                document.querySelectorAll('.contact-dropdown').forEach(function(d){ d.classList.remove('open'); });
            });
        })();
    </script>
    <script>
        // Hero slider functionality
        (function(){
            var slider = document.getElementById('heroSlider');
            var slides = slider.querySelectorAll('.hero-slide');
            var dots = slider.querySelectorAll('.hero-dot');
            var currentSlide = 0;
            var autoplayInterval;

            function showSlide(n) {
                slides.forEach(function(slide) { 
                    slide.classList.remove('active', 'prev', 'next'); 
                });
                dots.forEach(function(dot) { dot.classList.remove('active'); });
                
                var prevIndex = (n - 1 + slides.length) % slides.length;
                var nextIndex = (n + 1) % slides.length;
                
                slides[prevIndex].classList.add('prev');
                slides[n].classList.add('active');
                slides[nextIndex].classList.add('next');
                
                dots[n].classList.add('active');
                currentSlide = n;
            }

            function nextSlide() {
                showSlide((currentSlide + 1) % slides.length);
            }

            function goToSlide(n) {
                showSlide(n);
                clearInterval(autoplayInterval);
                autoplayInterval = setInterval(nextSlide, 5000);
            }

            // Dot click handlers
            dots.forEach(function(dot, index) {
                dot.addEventListener('click', function() {
                    goToSlide(index);
                });
            });

            // Initialize first slide
            showSlide(0);
            
            // Auto-play
            autoplayInterval = setInterval(nextSlide, 5000);
        })();
    </script>

    <script>
        // ============================================
        // SCROLL ANIMATIONS - Trigger animations when elements come into view
        // ============================================
        (function(){
            var observerOptions = {
                threshold: 0.15,
                rootMargin: '0px 0px -100px 0px'
            };

            var observer = new IntersectionObserver(function(entries){
                entries.forEach(function(entry){
                    if(entry.isIntersecting){
                        entry.target.classList.add('visible');
                        // Optional: stop observing once animated
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all elements with scroll-animate class
            var animatedElements = document.querySelectorAll('.scroll-animate, .product-card, section h2, .section-description, .featured-section');
            animatedElements.forEach(function(el){
                observer.observe(el);
            });

            // Stagger animations for product cards on page load
            setTimeout(function(){
                var cards = document.querySelectorAll('.product-card');
                cards.forEach(function(card, index){
                    setTimeout(function(){
                        card.style.opacity = '1';
                    }, index * 150);
                });
            }, 300);
        })();
    </script>

    <script>
        // ============================================
        // BRAND DROPDOWN NAVIGATION (priority handler)
        // ============================================
        (function(){
            // Handle brand dropdown clicks with immediate navigation
            document.addEventListener('click', function(e){
                // Check if click is within brands dropdown
                var brandLink = e.target.closest('.nav-list li:nth-child(3) .nav-dropdown a');
                if(brandLink){
                    e.preventDefault();
                    e.stopPropagation();
                    var href = brandLink.getAttribute('href');
                    if(href){
                        window.location.href = href;
                    }
                    return;
                }
            }, true); // Use capture phase for priority
        })();
    </script>

    <script>
        // ============================================
        // PAGE TRANSITION EFFECTS
        // ============================================
        (function(){
            // Smooth page transitions on link clicks
            document.addEventListener('click', function(e){
                var link = e.target.closest('a[href*=".php"], a[href^="#"]');
                if(!link) return;
                
                var href = link.getAttribute('href');
                
                // Skip if it's an anchor link or javascript link
                if(href.startsWith('#') || href.startsWith('javascript:')) return;
                
                // Check if it's an internal PHP file
                if(!href.includes('.php')) return;
                
                // Prevent default and add exit animation
                e.preventDefault();
                
                var body = document.body;
                body.style.animation = 'none';

                setTimeout(function(){
                    window.location.href = href;
                }, 0);
            });

            // Add page entry animation on load
            window.addEventListener('load', function(){
                document.body.style.animation = 'none';
            });
        })();
    </script>

    <script>
        // ============================================
        // TEXT ANIMATIONS - Enhanced text reveal effects
        // ============================================
        (function(){
            // Add text animation to headings and descriptions
            var headings = document.querySelectorAll('h2, h3');
            headings.forEach(function(heading, index){
                heading.style.animationDelay = (index * 0.1) + 's';
            });

            // Animate footer links on hover
            var footerLinks = document.querySelectorAll('.footer-links a');
            footerLinks.forEach(function(link, index){
                link.style.animationDelay = (index * 0.1) + 's';
            });

            // Stagger contact list items
            var contactItems = document.querySelectorAll('.contact-list li');
            contactItems.forEach(function(item, index){
                item.style.opacity = '0';
                item.style.animation = 'fadeInUp 0.5s ease ' + (index * 0.1) + 's forwards';
            });
        })();
    </script>

    <script>
        // ============================================
        // HOVER EFFECTS - Enhanced interactive feedback
        // ============================================
        (function(){
            // Add hover effects to product cards
            var cards = document.querySelectorAll('.product-card');
            cards.forEach(function(card){
                card.addEventListener('mouseenter', function(){
                    this.style.boxShadow = '0 20px 40px rgba(0, 212, 170, 0.2)';
                });
                card.addEventListener('mouseleave', function(){
                    this.style.boxShadow = '';
                });
            });

            // Enhance button interactions
            var buttons = document.querySelectorAll('button, .cta-button, .featured-btn');
            buttons.forEach(function(btn){
                btn.addEventListener('mousedown', function(){
                    this.style.transform = 'scale(0.98)';
                });
                btn.addEventListener('mouseup', function(){
                    this.style.transform = '';
                });
                btn.addEventListener('mouseleave', function(){
                    this.style.transform = '';
                });
            });

            // Enhance navigation link hover effects
            var navLinks = document.querySelectorAll('.nav-list a');
            navLinks.forEach(function(link){
                link.addEventListener('mouseenter', function(){
                    this.style.color = '#ffffff';
                });
                link.addEventListener('mouseleave', function(){
                    if(!this.classList.contains('active')){
                        this.style.color = '';
                    }
                });
            });
        })();
    </script>

    <script>
        // ============================================
        // PARALLAX & SCROLL EFFECTS
        // ============================================
        (function(){
            var heroSlider = document.getElementById('heroSlider');
            if(!heroSlider) return;

            window.addEventListener('scroll', function(){
                var scrolled = window.pageYOffset;
                if(scrolled < 500){
                    heroSlider.style.transform = 'translateY(' + (scrolled * 0.5) + 'px)';
                    heroSlider.style.opacity = 1 - (scrolled / 800);
                }
            }, false);
        })();
    </script>

    <script>
        // Sidebar overlay functionality (for backdrop close)
        (function(){
            var overlayBackdrop = document.querySelector('.overlay-backdrop');
            var sidebar = document.getElementById('sidebar');
            
            if(overlayBackdrop) {
                overlayBackdrop.addEventListener('click', function(){
                    if(sidebar) sidebar.classList.remove('active');
                    overlayBackdrop.classList.remove('active');
                });
            }
            
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
        // ============================================
        // ACTIVE SIDEBAR CATEGORY HIGHLIGHTING
        // ============================================
        function highlightActiveSidebarLink() {
            var currentPath = window.location.pathname.toLowerCase();
            var sidebar = document.getElementById('sidebar');
            if(!sidebar) return;
            
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
            
            // Find the current category
            for(var i = 0; i < pathParts.length; i++) {
                if(categoryList.indexOf(pathParts[i]) !== -1) {
                    currentCategory = pathParts[i];
                    break;
                }
            }

            if(currentCategory){
                // Remove active class from all links first
                var allLinks = sidebar.querySelectorAll('.sidebar-list > li > a');
                allLinks.forEach(function(link){
                    link.classList.remove('active');
                });
                
                // Apply active class to the matching link
                allLinks.forEach(function(link){
                    var href = link.getAttribute('href').toLowerCase();
                    // Check if this link matches the current category
                    if(href.includes(currentCategory)){
                        link.classList.add('active');
                        
                        // Auto-expand the submenu for this category
                        var parentLi = link.closest('li.has-sub');
                        if(parentLi) {
                            var subToggle = parentLi.querySelector('.sub-toggle');
                            var sublist = document.getElementById(subToggle.getAttribute('aria-controls'));
                            if(sublist && sublist.classList.contains('collapsed')) {
                                sublist.classList.remove('collapsed');
                                subToggle.setAttribute('aria-expanded', 'true');
                            }
                        }
                    }
                });
            }
        }
        
        // Run on page load
        highlightActiveSidebarLink();
        
        // Run after a delay to ensure DOM is ready
        setTimeout(highlightActiveSidebarLink, 500);
        
        // Re-run when sidebar expands
        var browseToggle = document.getElementById('browseToggle');
        if(browseToggle) {
            browseToggle.addEventListener('click', function(){
                setTimeout(highlightActiveSidebarLink, 300);
            });
        }
    </script>

    <script>
        // ============================================
        // MINI SIDEBAR AND BROWSE TOGGLE FUNCTIONALITY
        // ============================================
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



        // Helpers for popover
        function getCategoryKeyFromTarget(dataTarget) {
            if (!dataTarget) return null;
            var keys = [
                'arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting','gas-detectors','portable-ventilators','power-tools','protection','welding-accessories','welding-consumables'
            ];
            for (var i=0;i<keys.length;i++) { 
                if (dataTarget.indexOf('/'+keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'.php') !== -1) return keys[i]; 
            }
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
                    { label: 'G3 Controller Series', href: base + '/../arc-welding-robots/g3-controller-series.php' },
                    { label: 'G4 Controller Series', href: base + '/../arc-welding-robots/g4-controller-series.php' },
                    { label: 'Featured Products and Solutions', href: base + '/../arc-welding-robots/featured-products-and-solution.php' },
                    { label: 'Robot System Peripherals', href: base + '/../arc-welding-robots/robot-system-peripherals.php' }
                ],
                'arc-welding-machine': [
                    { label: 'MIG Welding Machine', href: base + '/mig-welding-machine.php' },
                    { label: 'CO1/MAG Welding Machine', href: base + '/co1-mag-welding-machine.php' },
                    { label: 'STUD Welding Machine', href: base + '/stud-welding-machine.php' },
                    { label: 'TIG Welding Machine', href: base + '/tig-welding-machine.php' },
                    { label: 'Plasma Cutting Machine', href: base + '/plasma-cutting-machine.php' }
                ],
                'batteries': [
                    { label: 'Maintenance Free', href: base + '/../batteries/maintenance-free.php' },
                    { label: 'Low Maintenance', href: base + '/../batteries/low-maintenance.php' },
                    { label: 'Special Batteries', href: base + '/../batteries/special-batteries.php' }
                ],
                'drilling-and-lifting': [
                    { label: 'Lifting', href: base + '/../drilling-and-lifting/lifting.php' },
                    { label: 'Magnetic Drill', href: base + '/../drilling-and-lifting/magnetic-drill.php' },
                    { label: 'Cutters', href: base + '/../drilling-and-lifting/cutters.php' }
                ],
                'gas-detectors': [
                    { label: 'Single Gas Detector', href: base + '/../gas-detectors/single-gas-detector.php' },
                    { label: 'Multi Gas Detector', href: base + '/../gas-detectors/multi-gas-detector.php' },
                    { label: 'Portable Gas Detectors', href: base + '/../gas-detectors/portable-gas-detectors.php' },
                    { label: 'Docking and Data Management', href: base + '/../gas-detectors/docking-data-management.php' },
                    { label: 'Calibration Gas and Regulators', href: base + '/../gas-detectors/calibration-gas-regulators.php' }
                ],
                'power-tools': [
                    { label: 'Grinder', href: base + '/../power-tools/grinder.php' },
                    { label: 'Saw', href: base + '/../power-tools/saw.php' },
                    { label: 'Drill and Wrench', href: base + '/../power-tools/drill-and-wrench.php' },
                    { label: 'Rotary and Demolition Hammer', href: base + '/../power-tools/rotary-and-demolition-hammer.php' },
                    { label: 'Accessories', href: base + '/../power-tools/accessories.php' }
                ],
                'portable-ventilators': [
                    { label: 'Electric Driven', href: base + '/../portable-ventilators/electric-driven.php' },
                    { label: 'Pneumatic Driven', href: base + '/../portable-ventilators/pneumatic-driven.php' }
                ],
                'protection': [
                    { label: 'Eye Protection', href: base + '/../protection/eye-protection.php' },
                    { label: 'Hand Protection', href: base + '/../protection/hand-protection.php' },
                    { label: 'Hearing & Respiratory Protection', href: base + '/../protection/hearing-respiratory-protection.php' },
                    { label: 'Welding Head and Face Protection', href: base + '/../protection/welding-head-and-face-protection.php' },
                    { label: 'Body Protection', href: base + '/../protection/body-protection.php' }
                ],
                'welding-accessories': [
                    { label: 'Welding Electrode Oven', href: base + '/../welding-accessories/welding-electrode-oven.php' },
                    { label: 'Non-Destructive Crack Detection', href: base + '/../welding-accessories/non-destructive-crack-detection.php' },
                    { label: 'Gas Saving Regulator', href: base + '/../welding-accessories/gas-saving-regulator.php' },
                    { label: 'Gas Cutting Equipment', href: base + '/../welding-accessories/gas-cutting-equipment.php' },
                    { label: 'Industrial Markers', href: base + '/../welding-accessories/industrial-markers.php' },
                    { label: 'Measuring Gauge', href: base + '/../welding-accessories/measuring-gauge.php' },
                    { label: 'Others', href: base + '/../welding-accessories/others.php' }
                ],
                'welding-consumables': [
                    { label: 'Kobelco', href: base + '/../welding-consumables/kobelco.php' },
                    { label: 'Metrode', href: base + '/../welding-consumables/metrode.php' }
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

        // Close on outside click / Escape
        document.addEventListener('click', function(e){
            if (!miniPopover) return;
            if (!miniPopover.classList.contains('show')) return;
            if (e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;
            hidePopover();
        });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hidePopover(); });

        // Expand/collapse sidebar
        if (expandBtn) {
            expandBtn.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        // Menu bar click to expand
        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar) {
            menuBar.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        // Arrow click handler for popover
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

        // Attach arrow click handlers
        document.querySelectorAll('.sub-indicator').forEach(function(arrow) {
            arrow.addEventListener('click', arrowHandler, true);
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.sub-indicator')) arrowHandler(e);
        }, true);

        // Mini icon click handler (navigate to category page)
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
            
            // Update on custom event (when items added on this page)
            window.addEventListener('inquiryItemsUpdated', updateCartBadge);
            
            // Update frequently to catch changes
            setInterval(updateCartBadge, 500);
        })();
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

            // Keep FAB in sync when sidebar expand/collapse changes its width
            var observer = new MutationObserver(function() { syncFab(); });
            observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });

            window.addEventListener('resize', syncFab);
        })();
    </script>

    <script>
        // Product filtering and search functionality
        function filterProducts() {
            var searchInput = document.getElementById('searchInput')?.value?.toLowerCase() || '';
            var sortSelect = document.getElementById('sortSelect')?.value || 'default';
            var allCards = Array.from(document.querySelectorAll('.product-grid .product-card'));
            var visibleCards = [];

            // Get filter states
            var filters = {
                popular: document.getElementById('cat-popular')?.checked || false,
                new: document.getElementById('cat-new')?.checked || false,
                machinery: document.getElementById('type-machinery')?.checked || false,
                accessories: document.getElementById('type-accessories')?.checked || false,
                consumables: document.getElementById('type-consumables')?.checked || false,
                industrial: document.getElementById('tag-industrial')?.checked || false,
                professional: document.getElementById('tag-professional')?.checked || false,
                premium: document.getElementById('tag-premium')?.checked || false
            };

            // Filter cards
            allCards.forEach(function(card) {
                var text = card.textContent.toLowerCase();
                var matchesSearch = !searchInput || text.includes(searchInput);
                var hasFilters = Object.values(filters).some(v => v);
                var matchesFilters = !hasFilters; // Show all if no filters applied

                if (matchesSearch && matchesFilters) {
                    card.style.display = '';
                    visibleCards.push(card);
                } else {
                    card.style.display = 'none';
                }
            });

            // Sort cards
            if (sortSelect !== 'default') {
                visibleCards.sort(function(a, b) {
                    var aText = a.querySelector('h4')?.textContent || '';
                    var bText = b.querySelector('h4')?.textContent || '';
                    
                    if (sortSelect === 'name-asc') return aText.localeCompare(bText);
                    if (sortSelect === 'name-desc') return bText.localeCompare(aText);
                    return 0;
                });

                // Reorder cards in DOM
                var grid = document.querySelector('.product-grid');
                visibleCards.forEach(function(card) {
                    grid.appendChild(card);
                });
            }

            // Update results count
            var resultsSpan = document.getElementById('resultsCount');
            if (resultsSpan) {
                resultsSpan.textContent = 'Showing ' + visibleCards.length + ' products';
            }
        }

        function clearAllFilters() {
            document.querySelectorAll('.filter-option input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.checked = false;
            });
            document.getElementById('searchInput').value = '';
            document.getElementById('sortSelect').value = 'default';
            filterProducts();
        }

        function setView(viewType) {
            var grid = document.querySelector('.product-grid');
            var buttons = document.querySelectorAll('.view-toggle button');
            
            buttons.forEach(function(btn) {
                btn.classList.remove('active');
            });
            event.target.closest('button').classList.add('active');

            if (viewType === 'list') {
                grid.style.gridTemplateColumns = '1fr';
            } else {
                grid.style.gridTemplateColumns = 'repeat(3, 1fr)';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            filterProducts();
            initPagination();
        });

        // ============================================
        // PAGINATION SYSTEM
        // ============================================
        var currentPage = 1;
        var itemsPerPage = 8; // 2 rows x 4 columns
        var allProductCards = [];
        var filteredCards = [];

        function initPagination() {
            allProductCards = Array.from(document.querySelectorAll('.product-grid .product-card'));
            filteredCards = allProductCards.slice();
            updatePagination();
        }

        function updatePagination() {
            var totalPages = Math.ceil(filteredCards.length / itemsPerPage);
            var start = (currentPage - 1) * itemsPerPage;
            var end = start + itemsPerPage;

            // Hide all cards
            allProductCards.forEach(function(card) { card.style.display = 'none'; });

            // Show only cards for current page
            filteredCards.slice(start, end).forEach(function(card) { card.style.display = ''; });

            // Update pagination buttons
            updatePaginationButtons(totalPages);

            // Update results counter
            var resultsCount = document.getElementById('resultsCount');
            if(resultsCount) {
                resultsCount.innerHTML = 'Showing <span>' + Math.min(itemsPerPage, filteredCards.length - start) + '</span> of <span>' + filteredCards.length + '</span> products';
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
                document.querySelector('.product-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // ============================================
        // PRODUCT MODAL FUNCTIONALITY
        // ============================================
        var modal = document.getElementById('productModal');
        var modalClose = document.getElementById('modalClose');
        var modalCloseBtn = document.getElementById('modalCloseBtn');
        var currentProduct = null;
        var currentMediaIndex = 0;

        function openProductModal(cardElement) {
            var model = cardElement.querySelector('.product-model').textContent || '';
            var name = cardElement.querySelector('h4').textContent || '';
            var imgSrc = cardElement.querySelector('.product-image img')?.src || '';
            var description = cardElement.querySelector('.product-description').textContent || 'Premium quality arc welding equipment.';
            
            // Populate modal
            document.getElementById('modalProductName').textContent = name;
            document.getElementById('modalProductType').textContent = model;
            document.getElementById('modalDescription').textContent = description;
            
            // Set up media slider
            var mediaSlider = document.getElementById('mediaSlider');
            mediaSlider.innerHTML = '';
            
            var mediaItem = document.createElement('div');
            mediaItem.className = 'media-item active';
            var img = document.createElement('img');
            img.src = imgSrc;
            img.alt = name;
            mediaItem.appendChild(img);
            mediaSlider.appendChild(mediaItem);
            
            // Setup media dots
            var mediaDots = document.getElementById('mediaDots');
            mediaDots.innerHTML = '<div class="media-dot active"></div>';
            
            // Set specs
            var specsList = document.getElementById('modalSpecs');
            specsList.innerHTML = `
                <li><strong>Category:</strong> Arc Welding Equipment</li>
                <li><strong>Model:</strong> ${model}</li>
                <li><strong>Type:</strong> Professional Grade</li>
                <li><strong>Support:</strong> 24/7 Technical Support</li>
            `;
            
            // Store current product
            currentProduct = {
                model: model,
                name: name,
                description: description
            };
            
            currentMediaIndex = 0;
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }

        // Add click listeners to all product cards
        document.addEventListener('DOMContentLoaded', function() {
            var productCards = document.querySelectorAll('.product-card');
            productCards.forEach(function(card) {
                card.addEventListener('click', function() {
                    openProductModal(card);
                });
            });

            // Modal controls
            modalClose.addEventListener('click', closeModal);
            modalCloseBtn.addEventListener('click', closeModal);
            
            // Close on overlay click
            modal.addEventListener('click', function(e) {
                if(e.target === modal) {
                    closeModal();
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if(e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal();
                }
            });

            // Modal inquiry button
            document.getElementById('modalInquiryBtn').addEventListener('click', function() {
                if(currentProduct) {
                    var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                    var found = items.find(function(i) { 
                        return i.model === currentProduct.model && i.name === currentProduct.name; 
                    });
                    
                    if(!found) {
                        var product = {
                            model: currentProduct.model,
                            name: currentProduct.name,
                            description: currentProduct.description,
                            qty: 1,
                            timestamp: new Date().getTime()
                        };
                        items.push(product);
                        localStorage.setItem('inquiryItems', JSON.stringify(items));
                        
                        // Show success message
                        this.innerHTML = '<i class="bi bi-check-circle"></i> Added ✓';
                        this.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
                        
                        // Dispatch event to update badge
                        window.dispatchEvent(new Event('inquiryItemsUpdated'));
                        
                        // Reset button after 1.5 seconds and close modal
                        var self = this;
                        setTimeout(function() {
                            self.innerHTML = '<i class="bi bi-plus-circle"></i> ADD TO INQUIRY LIST';
                            self.style.background = '';
                            closeModal();
                        }, 1500);
                    } else {
                        // Already added
                        this.innerHTML = '<i class="bi bi-check-circle"></i> Already in List ✓';
                        this.style.background = 'linear-gradient(135deg, #6366F1 0%, #4F46E5 100%)';
                        
                        var self = this;
                        setTimeout(function() {
                            self.innerHTML = '<i class="bi bi-plus-circle"></i> ADD TO INQUIRY LIST';
                            self.style.background = '';
                        }, 1500);
                    }
                }
            });

            // Regular product card buttons
            document.querySelectorAll('.product-grid .add-to-inquiry').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    var card = this.closest('.product-card');
                    var model = card.querySelector('.product-model').textContent || '';
                    var name = card.querySelector('h4').textContent || '';
                    var description = card.querySelector('.product-description').textContent || 'Premium quality product';
                    
                    var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                    var found = items.find(function(i) { 
                        return i.model === model && i.name === name; 
                    });
                    
                    if(!found) {
                        var product = {
                            model: model,
                            name: name,
                            description: description,
                            qty: 1,
                            timestamp: new Date().getTime()
                        };
                        items.push(product);
                        localStorage.setItem('inquiryItems', JSON.stringify(items));
                        
                        // Show success animation
                        var originalText = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check-circle"></i> Added ✓';
                        this.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
                        
                        // Dispatch event to update badge
                        window.dispatchEvent(new Event('inquiryItemsUpdated'));
                        
                        // Reset after 1.5 seconds
                        var self = this;
                        setTimeout(function() {
                            self.innerHTML = originalText;
                            self.style.background = '';
                        }, 1500);
                    } else {
                        // Already added
                        var originalText = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check-circle"></i> Added ✓';
                        this.style.background = 'linear-gradient(135deg, #6366F1 0%, #4F46E5 100%)';
                        
                        var self = this;
                        setTimeout(function() {
                            self.innerHTML = originalText;
                            self.style.background = '';
                        }, 1500);
                    }
                });
            });
        });
    </script>

    <!-- Product Modal -->
    <div id="productModal" class="modal-overlay">
        <div class="modal-container">
            <button class="modal-close" id="modalClose">&times;</button>
            
            <div class="modal-media">
                <div class="media-slider" id="mediaSlider">
                    <!-- Media items will be inserted here -->
                </div>
                <div class="media-controls">
                    <div class="media-dots" id="mediaDots"></div>
                </div>
            </div>

            <div class="modal-content">
                <h2 id="modalProductName"></h2>
                <p class="model-type" id="modalProductType"></p>

                <div class="modal-description" id="modalDescription">
                    Professional arc welding equipment for industrial applications.
                </div>

                <div class="modal-specs">
                    <h3>Product Specifications</h3>
                    <ul class="specs-list" id="modalSpecs">
                        <li><strong>Category:</strong> Arc Welding Equipment</li>
                        <li><strong>Type:</strong> Professional Grade</li>
                        <li><strong>Support:</strong> 24/7 Technical Support</li>
                        <li><strong>Warranty:</strong> 2 Years Full Coverage</li>
                    </ul>
                </div>

                <div class="modal-actions">
                    <button class="modal-inquiry-btn" id="modalInquiryBtn"><i class="bi bi-plus-circle"></i> ADD TO INQUIRY LIST</button>
                    <button class="modal-close-btn" id="modalCloseBtn">CLOSE</button>
                </div>
            </div>
        </div>
    </div>


