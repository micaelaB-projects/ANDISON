<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$page_title = "Arc Welding Robots";
$category_id = "arc-welding-robots";
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
    die("Category not found");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    $category_name = $current_category['name'] ?? 'Arc Welding Robots';
    $category_description = $current_category['description'] ?? 'Explore our comprehensive range of arc welding robots and automated welding systems from leading manufacturers.';
    $phone = "+1(234) 567 8900";
    $phone2 = "+1(234) 567 8900";
    $phone3 = "+1(639) 977 803 7398";
    $email = "info@andison-industrial.com";
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
            --footer-height: 160px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            padding-top: 142px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f8f9fa;
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
            padding-left: 160px;
        }

        /* Pin the browse toggle to the left side of the nav area */
        .browse-toggle {
            position: absolute;
            left: 12px;
            top: 20%;
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
            line: height 6px;
            transition: all 0.2s ease;
        }

        .browse-toggle:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        .browse-toggle:active {
            transform: scale(0.98);
        }

        .browse-toggle .browse-text {
            display: none;
        }

        .browse-toggle.expanded .browse-text {
            display: inline;
        }

        .browse-toggle .browse-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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

        .category-container {
            max-width: 1500px;
            margin: 40px auto 40px;
            padding: 0 40px;
            flex: 1;
        }

        .category-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f0f0 100%);
            border-radius: 12px;
            padding: 45px 40px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            margin-bottom: 35px;
            text-align: center;
            display: none;
        }

        .category-header h1 {
            color: #2B11DB;
            font-size: 42px;
            margin-bottom: 18px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .category-header p {
            color: #666;
            font-size: 16px;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .category-content {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }

        .category-content h2 {
            color: #2B11DB;
            font-size: 28px;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 800;
            text-align: center;
        }

        .category-description {
            color: #666;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 35px;
            padding: 18px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f0f0 100%);
            border-left: 4px solid #2B11DB;
            border-radius: 6px;
            text-align: center;
            display: none;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 24px;
            margin-bottom: 40px;
            margin-top: 30px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            border: 1px solid #f0f0f0;
            min-height: 100%;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            border-color: #2B11DB;
        }

        .product-image {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 15px;
        }

        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(43, 17, 219, 0.3);
        }

        .product-card h4 {
            color: #2B11DB;
            font-size: 15px;
            font-weight: 700;
            padding: 16px 16px 8px;
            line-height: 1.4;
        }

        .product-model {
            color: #666;
            font-size: 12px;
            padding: 0 16px;
            margin: 4px 0 8px;
            font-weight: 500;
        }

        .product-card p {
            color: #888;
            font-size: 13px;
            padding: 0 16px;
            margin: 0;
        }

        .product-card .product-type {
            padding-bottom: 8px;
            font-weight: 500;
            color: #888;
        }

        .product-card .product-description {
            color: #666;
            font-size: 12px;
            padding: 0 16px 12px;
            line-height: 1.5;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            flex-grow: 1;
            margin-bottom: 12px;
        }

        .add-to-inquiry {
            margin: 0 16px 16px;
            padding: 11px 16px;
            background: linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 13px;
            width: calc(100% - 32px);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-top: auto;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(43, 17, 219, 0.2);
        }

        .add-to-inquiry:hover {
            background: linear-gradient(135deg, #1f0aa1 0%, #140570 100%);
            box-shadow: 0 4px 12px rgba(43, 17, 219, 0.35);
            transform: translateY(-2px);
        }

        .add-to-inquiry.already {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
        }

        .add-to-inquiry.already:hover {
            background: linear-gradient(135deg, #388e3c 0%, #2e7d32 100%);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.35);
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

        /* Footer */
        footer {
            background: #2B11DB;
            color: white;
            text-align: center;
            padding: 30px 20px;
            margin-top: auto;
            border-top: 3px solid #2B11DB;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-links {
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: #bbb;
            text-decoration: none;
            font-size: 14px;
        }

        .footer-links a:hover {
            color: #2B11DB;
        }

        .footer-copyright {
            color: #888;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            body {
                padding-top: 120px;
            }

            .header-top {
                flex-direction: column;
                gap: 15px;
            }

            .category-container {
                margin: 20px auto;
                padding: 0 20px;
            }

            .category-header {
                padding: 25px;
            }

            .category-header h1 {
                font-size: 24px;
            }

            .category-content {
                padding: 25px;
            }

            .category-content h2 {
                font-size: 22px;
                margin-bottom: 20px;
            }

            .product-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 16px;
                margin-top: 20px;
            }

            .product-image {
                aspect-ratio: 1 / 1;
            }

            .product-card h4 {
                font-size: 14px;
                padding: 12px 12px 6px;
            }

            .product-model {
                padding: 0 12px;
                font-size: 11px;
            }

            .add-to-inquiry {
                margin: 0 12px 12px;
                padding: 9px 12px;
                font-size: 12px;
                width: calc(100% - 24px);
            }

            .nav-inner { justify-content: space-between; padding-left: 20px; }
            .nav-list { position: static; transform: none; left: auto; margin: 8px auto 0; justify-content: center; flex-wrap: wrap; }
            .browse-toggle { position: static; transform: none; left: auto; top: auto; padding: 6px 10px; }
        }

        @media (max-width: 480px) {
            .category-container {
                padding: 0 15px;
            }

            .category-header {
                padding: 25px 15px;
            }

            .category-header h1 {
                font-size: 22px;
                margin-bottom: 10px;
            }

            .category-content {
                padding: 15px;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }

            .product-card h4 {
                font-size: 13px;
            }

            .add-to-inquiry {
                padding: 8px 10px;
                font-size: 11px;
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
            display: none !important;
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
            background: linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 70;
            padding: 28px 20px;
            overflow-y: auto;
            display: none !important;
        }

        .sidebar-overlay.active {
            transform: translateX(0);
        }

        .sidebar-overlay h3 {
            font-size: 18px;
            margin-bottom: 24px;
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .sidebar-list { list-style: none; padding: 0; margin: 0; }
        .sidebar-list li { border-bottom: 1px solid rgba(255,255,255,0.15); }
        .sidebar-list li:last-child { border-bottom: none; }
        .sidebar-list a { 
            display: flex; 
            gap: 12px; 
            padding: 16px 12px; 
            color: #ffffff; 
            text-decoration: none; 
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            font-size: 15px;
        }
        .sidebar-list a:hover { 
            background: rgba(255,255,255,0.15); 
            color: #00D7B3;
            padding-left: 16px;
        }
        .sidebar-list li a.active {
            background: rgba(0, 215, 179, 0.25);
            color: #00D7B3;
            font-weight: 600;
            border-left: 4px solid #00D7B3;
            padding-left: 12px;
        }
        .sidebar-list li a.active .sidebar-icon {
            color: #00D7B3;
        }
        .sidebar-icon { 
            color: #00D7B3; 
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
            color: #ffffff;
        }

        .sidebar-list a .sidebar-arrow {
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.7);
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
            background: rgba(0,0,0,0.15);
            margin-left: 12px;
            margin-right: 12px;
            padding-left: 16px;
            border-left: 2px solid rgba(255,255,255,0.2);
            padding-top: 8px;
            padding-bottom: 8px;
        }
        .sidebar-sublist li { 
            padding: 4px 0; 
            border: none;
        }
        .sidebar-sublist a { 
            color: rgba(255,255,255,0.85); 
            font-size: 14px; 
            padding: 6px 8px; 
            display: block; 
            text-decoration: none;
            justify-content: flex-start;
        }
        .sidebar-sublist a:hover { 
            color: #00D7B3; 
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
            color: rgba(255,255,255,0.6);
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
            color: rgba(255,255,255,0.75); 
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
            background: #00D7B3;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0, 215, 179, 0.4);
        }
        .sidebar-nested-sublist a:hover { 
            color: #00D7B3;
            background: rgba(0, 215, 179, 0.15);
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
            color: rgba(255,255,255,0.6);
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
            color: rgba(255,255,255,0.7); 
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
            color: #00D7B3;
        }

        /* Mini Sidebar (always visible icon bar) */
        .mini-sidebar {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px + 12px + 52px);
            bottom: 0;
            width: 80px;
            background: linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);
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
            width: 300px;
            overflow-y: auto;
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
            border-radius: 8px;
            margin-bottom: 8px;
            transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1), justify-content 0.5s cubic-bezier(0.4, 0, 0.2, 1), padding 0.5s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease, transform 0.2s ease, height auto;
            position: relative;
            gap: 12px;
            padding: 0;
            flex-shrink: 0;
            min-width: 56px;
            flex-direction: row;
        }

        .mini-sidebar-icon.mini-expanded {
            flex-direction: column;
            align-items: stretch;
            height: auto;
            min-height: 56px;
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
            color: #ffffff;
        }

        .mini-sidebar-icon .mini-arrow {
            display: none;
            margin-left: auto;
            margin-right: 0;
            padding-left: 16px;
            font-size: 16px;
            transition: transform 0.3s ease;
        }

        .mini-sidebar-icon.mini-has-sub .mini-arrow {
            display: inline-block;
        }

        .mini-sidebar-icon.mini-has-sub.mini-expanded .mini-arrow i {
            transform: rotate(90deg);
        }

        .mini-sidebar-sub {
            display: none;
            background: rgba(0,0,0,0.15);
            border-left: 3px solid #00D7B3;
            margin: 8px 0 0 0;
            border-radius: 4px;
            overflow: visible;
            flex-direction: column;
            width: 100%;
            opacity: 0;
            max-height: 0;
            transition: max-height 0.3s ease, opacity 0.3s ease 0.05s;
        }

        .mini-sidebar-icon.mini-expanded .mini-sidebar-sub {
            display: flex;
            max-height: 500px;
            opacity: 1;
        }

        .mini-sub-item {
            color: rgba(255,255,255,0.85);
            font-size: 12px;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            padding-left: 12px;
            white-space: nowrap;
        }

        .mini-sub-item:hover {
            background: rgba(0, 215, 179, 0.15);
            color: #00D7B3;
            padding-left: 16px;
            border-left-color: #00D7B3;
        }

        .mini-sidebar.expanded .mini-sidebar-icon {
            width: 100%;
            justify-content: flex-start;
            padding: 12px;
            min-width: auto;
            height: auto;
        }

        .mini-sidebar.expanded .mini-sidebar-icon .label {
            display: block;
            opacity: 1;
        }

        .mini-sidebar.expanded .mini-sidebar-icon:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(4px);
        }

        .mini-sidebar.expanded .mini-sidebar-icon.mini-expanded {
            background: rgba(255,255,255,0.15);
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

        .mini-sidebar-toggle:hover {
            background: rgba(255,255,255,0.25);
        }

        /* Adjust main container for mini sidebar */
        .category-container {
            margin-left: 80px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mini-sidebar.expanded ~ .category-container {
            margin-left: 300px;
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
                display: flex !important;
            }
            .browse-toggle {
                display: inline-flex !important;
            }
            .browse-toggle .browse-text {
                display: inline !important;
            }
            .category-container {
                margin-left: 80px !important;
            }
            .mini-sidebar.expanded ~ .category-container {
                margin-left: 300px !important;
            }
        }
    </style>
</head>
<body>
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
                <a href="javascript:history.back()" class="inquiry-btn" style="margin-right: 12px;">BACK</a>
                <a href="../inquirylist.php" class="inquiry-btn">INQUIRY LIST</a>
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
                <button id="browseToggle" class="browse-toggle"><span class="browse-icon"><i class="bi bi-list"></i></span><span class="browse-text">BROWSE PRODUCTS</span></button>
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
                                <li><a href="../brand.php?name=KOBELCO"><img src="../assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                                <li><a href="../brand.php?name=METRODE"><img src="../assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                                <li><a href="../brand.php?name=DRYROD"><img src="../assets/brands/DRYROD.jpg" alt="DryRod" title="DryRod"></a></li>
                                <li><a href="../brand.php?name=WELDCRAFT"><img src="../assets/brands/WELDCRAFT.jpg" alt="Weldcraft" title="Weldcraft"></a></li>
                                <li><a href="../brand.php?name=TRUWELD"><img src="../assets/brands/TRUWELD.jpg" alt="Truweld" title="Truweld"></a></li>
                                <li><a href="../brand.php?name=ARCAIR"><img src="../assets/brands/ARCAIR.jpg" alt="Arcair" title="Arcair"></a></li>
                                <li><a href="../brand.php?name=MAGNAFLUX"><img src="../assets/brands/MAGNAFLUX.jpg" alt="Magnaflux" title="Magnaflux"></a></li>
                                <li><a href="../brand.php?name=TEMPILSTIK"><img src="../assets/brands/TEMPILSTIK.jpg" alt="Tempilstik" title="Tempilstik"></a></li>
                                <li><a href="../brand.php?name=TANAKA"><img src="../assets/brands/TANAKA.jpg" alt="TANAKA" title="TANAKA"></a></li>
                                <li><a href="../brand.php?name=CHIYODA"><img src="../assets/brands/CHIYODA.jpg" alt="CHIYODA" title="CHIYODA"></a></li>
                                <li><a href="../brand.php?name=YUTAKA"><img src="../assets/brands/YUTAKA.jpg" alt="Yutaka" title="Yutaka"></a></li>
                                <li><a href="../brand.php?name=HARDWORKER"><img src="../assets/brands/HARDWORKER.jpg" alt="HARDWORKER" title="HARDWORKER"></a></li>
                                <li><a href="../brand.php?name=SOYER"><img src="../assets/brands/SOYER.jpg" alt="Soyer" title="Soyer"></a></li>
                                <li><a href="../brand.php?name=AQUASOL"><img src="../assets/brands/AQUASOL.jpg" alt="Aquasol" title="Aquasol"></a></li>
                                <li><a href="../brand.php?name=SK%20And%20GAL%20GAGE"><img src="../assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE" title="SK And GAL GAGE"></a></li>
                                <li><a href="../brand.php?name=COPPUS"><img src="../assets/brands/COPPUS.jpg" alt="COPPUS" title="COPPUS"></a></li>
                                <li><a href="../brand.php?name=BW%20Technologies"><img src="../assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies" title="BW Technologies"></a></li>
                                <li><a href="../brand.php?name=RAE%20SYSTEMS"><img src="../assets/brands/RAE%20SYSTEMS.jpg" alt="RAE SYSTEMS" title="RAE SYSTEMS"></a></li>
                                <li><a href="../brand.php?name=WELDAS"><img src="../assets/brands/WELDAS.jpg" alt="WELDAS" title="WELDAS"></a></li>
                                <li><a href="../brand.php?name=UVEX"><img src="../assets/brands/UVEX.jpg" alt="UVEX" title="UVEX"></a></li>
                                <li><a href="../brand.php?name=ACES"><img src="../assets/brands/ACES.jpg" alt="ACES" title="ACES"></a></li>
                                <li><a href="../brand.php?name=MICROGARD"><img src="../assets/brands/MICROGARD.jpg" alt="MICROGARD" title="MICROGARD"></a></li>
                                <li><a href="../brand.php?name=ANSELL"><img src="../assets/brands/ANSELL.jpg" alt="ANSELL" title="ANSELL"></a></li>
                                <li><a href="../brand.php?name=ALFRA"><img src="../assets/brands/ALFRA.jpg" alt="Alfra" title="Alfra"></a></li>
                                <li><a href="../brand.php?name=BOSCH"><img src="../assets/brands/BOSCH.jpg" alt="Bosch" title="Bosch"></a></li>
                                <li><a href="../brand.php?name=MAKITA"><img src="../assets/brands/MAKITA.jpg" alt="Makita" title="Makita"></a></li>
                                <li><a href="../brand.php?name=WEILER"><img src="../assets/brands/WEILER.jpg" alt="WEILER" title="WEILER"></a></li>
                                <li><a href="../brand.php?name=GARRYSON"><img src="../assets/brands/GARRYSON.jpg" alt="Garryson" title="Garryson"></a></li>
                                <li><a href="../brand.php?name=SPILFYTER"><img src="../assets/brands/SPILFYTER.jpg" alt="Spilfyter" title="Spilfyter"></a></li>
                                <li><a href="../brand.php?name=DALO"><img src="../assets/brands/DALO.jpg" alt="Dalo" title="Dalo"></a></li>
                                <li><a href="../brand.php?name=MOTOLITE"><img src="../assets/brands/MOTOLITE.jpg" alt="MOTOLITE" title="MOTOLITE"></a></li>
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
        <div style="padding: 16px 12px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; color: #1f2937;">Categories</h3>
        </div>
        <ul class="sidebar-list">
            <li class="has-sub">
                <a href="../arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="../arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="../arc-welding-machine/co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="../arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="../arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                    <li><a href="../arc-welding-machine/plasma-cutting-machine.php">Plasma Cutting Machine</a></li>
                </ul>
            </li>
            <li class="has-sub active">
                <a href="../arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label"><?php echo htmlspecialchars($current_category['name']); ?></span></a>
                <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                    <li><a href="../arc-welding-robots/g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="../arc-welding-robots/g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="../arc-welding-robots/featured-products-and-solution.php">Featured Products and Solutions</a></li>
                    <li><a href="../arc-welding-robots/robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="../batteries/maintenance-free.php">Maintenance Free</a></li>
                    <li><a href="../batteries/low-maintenance.php">Low Maintenance</a></li>
                    <li><a href="../batteries/special-batteries.php">Special Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="../drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li><a href="../drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a></li>
                    <li><a href="../drilling-and-lifting/cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
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
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="../protection/eye-protection.php">Eye Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="../protection/hand-protection.php">Hand Protection</a>
                        <ul id="nested-hand-protection" class="sidebar-nested-sublist collapsed">
                            <li><a href="../protection/working-gloves.php">Working Gloves</a></li>
                            <li><a href="../protection/chemical-liquid-protection-gloves.php">Chemical and Liquid Protection Gloves</a></li>
                            <li><a href="../protection/disposable-gloves.php">Disposable Gloves</a></li>
                            <li><a href="../protection/welding-gloves.php">Welding Gloves</a></li>
                        </ul>
                    </li>
                    <li><a href="../protection/hearing-respiratory-protection.php">Hearing &amp; Respiratory Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="../protection/body-protection.php">Body Protection</a>
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
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="../welding-consumables/kobelco.php">Kobelco</a></li>
                    <li><a href="../welding-consumables/metrode.php">Metrode</a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <!-- Mini Sidebar (Icon Bar) -->
    <div class="mini-sidebar active" id="miniSidebar">
        <div class="mini-sidebar-icon mini-has-sub" data-target="../arc-welding-machine/arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../arc-welding-machine/mig-welding-machine.php" class="mini-sub-item">MIG Welding</a>
                <a href="../arc-welding-machine/co1-mag-welding-machine.php" class="mini-sub-item">CO1/MAG Welding</a>
                <a href="../arc-welding-machine/stud-welding-machine.php" class="mini-sub-item">STUD Welding</a>
                <a href="../arc-welding-machine/tig-welding-machine.php" class="mini-sub-item">TIG Welding</a>
                <a href="../arc-welding-machine/plasma-cutting-machine.php" class="mini-sub-item">Plasma Cutting</a>
            </div>
        </div>
        <div class="mini-sidebar-icon active-icon mini-has-sub" data-target="../arc-welding-robots/arc-welding-robot.php" title="Arc Welding Robots"><i class="bi bi-robot"></i><span class="label">Arc Welding Robots<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../arc-welding-robots/g3-controller-series.php" class="mini-sub-item">G3 Controller Series</a>
                <a href="../arc-welding-robots/g4-controller-series.php" class="mini-sub-item">G4 Controller Series</a>
                <a href="../arc-welding-robots/featured-products-and-solution.php" class="mini-sub-item">Featured Products</a>
                <a href="../arc-welding-robots/robot-system-peripherals.php" class="mini-sub-item">Robot Peripherals</a>
            </div>
        </div>
        <div class="mini-sidebar-icon mini-has-sub" data-target="../batteries/batteries.php" title="Batteries"><i class="bi bi-lightning-fill"></i><span class="label">Batteries<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../batteries/maintenance-free.php" class="mini-sub-item">Maintenance Free</a>
                <a href="../batteries/low-maintenance.php" class="mini-sub-item">Low Maintenance</a>
                <a href="../batteries/special-batteries.php" class="mini-sub-item">Special Batteries</a>
            </div>
        </div>
        <div class="mini-sidebar-icon mini-has-sub" data-target="../drilling-and-lifting/drilling-and-lifting.php" title="Drilling and Lifting"><i class="bi bi-hammer"></i><span class="label">Drilling and Lifting<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../drilling-and-lifting/lifting.php" class="mini-sub-item">Lifting</a>
                <a href="../drilling-and-lifting/magnetic-drill.php" class="mini-sub-item">Magnetic Drill</a>
                <a href="../drilling-and-lifting/cutters.php" class="mini-sub-item">Cutters</a>
            </div>
        </div>
        <div class="mini-sidebar-icon mini-has-sub" data-target="../gas-detectors/gas-detectors.php" title="Gas Detectors"><i class="bi bi-bullseye"></i><span class="label">Gas Detectors<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../gas-detectors/single-gas-detector.php" class="mini-sub-item">Single Gas</a>
                <a href="../gas-detectors/multi-gas-detector.php" class="mini-sub-item">Multi Gas</a>
                <a href="../gas-detectors/portable-gas-detectors.php" class="mini-sub-item">Portable</a>
                <a href="../gas-detectors/docking-data-management.php" class="mini-sub-item">Docking & Data</a>
                <a href="../gas-detectors/calibration-gas-regulators.php" class="mini-sub-item">Calibration</a>
            </div>
        </div>
        <div class="mini-sidebar-icon" data-target="../portable-ventilators/portable-ventilators.php" title="Portable Ventilators"><i class="bi bi-fan"></i><span class="label">Portable Ventilators</span></div>
        <div class="mini-sidebar-icon mini-has-sub" data-target="../power-tools/power-tools.php" title="Power Tools"><i class="bi bi-tools"></i><span class="label">Power Tools<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../power-tools/grinder.php" class="mini-sub-item">Grinder</a>
                <a href="../power-tools/saw.php" class="mini-sub-item">Saw</a>
                <a href="../power-tools/drill-and-wrench.php" class="mini-sub-item">Drill & Wrench</a>
                <a href="../power-tools/rotary-and-demolition-hammer.php" class="mini-sub-item">Rotary Hammer</a>
                <a href="../power-tools/accessories.php" class="mini-sub-item">Accessories</a>
            </div>
        </div>
        <div class="mini-sidebar-icon mini-has-sub" data-target="../protection/protection.php" title="Personal Protective Equipment"><i class="bi bi-shield-check"></i><span class="label">PPE<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../protection/eye-protection.php" class="mini-sub-item">Eye Protection</a>
                <div class="mini-sub-item mini-has-nested-sub">
                    Hand Protection<span class="mini-nested-arrow"><i class="bi bi-chevron-right"></i></span>
                    <div class="mini-nested-sub">
                        <a href="../protection/working-gloves.php" class="mini-nested-item">Working Gloves</a>
                        <a href="../protection/chemical-liquid-protection-gloves.php" class="mini-nested-item">Chemical & Liquid Gloves</a>
                        <a href="../protection/disposable-gloves.php" class="mini-nested-item">Disposable Gloves</a>
                        <a href="../protection/welding-gloves.php" class="mini-nested-item">Welding Gloves</a>
                    </div>
                </div>
                <a href="../protection/hearing-respiratory-protection.php" class="mini-sub-item">Hearing & Respiratory</a>
                <div class="mini-sub-item mini-has-nested-sub">
                    Body Protection<span class="mini-nested-arrow"><i class="bi bi-chevron-right"></i></span>
                    <div class="mini-nested-sub">
                        <a href="../protection/chemical-flame-retardant.php" class="mini-nested-item">Chemical & Flame Retardant</a>
                        <a href="../protection/liquid-spray-splash.php" class="mini-nested-item">Liquid Spray & Splash</a>
                        <a href="../protection/particulate-low-hazard.php" class="mini-nested-item">Particulate & Low Hazard</a>
                    </div>
                </div>
                <a href="../protection/welding-head-and-face-protection.php" class="mini-sub-item">Welding Head & Face Protection</a>
            </div>
        </div>
        <div class="mini-sidebar-icon mini-has-sub" data-target="../welding-accessories/welding-accessories.php" title="Welding Accessories"><i class="bi bi-gear"></i><span class="label">Welding Accessories<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../welding-accessories/welding-electrode-oven.php" class="mini-sub-item">Electrode Oven</a>
                <a href="../welding-accessories/non-destructive-crack-detection.php" class="mini-sub-item">Crack Detection</a>
                <a href="../welding-accessories/gas-saving-regulator.php" class="mini-sub-item">Gas Regulator</a>
                <a href="../welding-accessories/gas-cutting-equipment.php" class="mini-sub-item">Gas Cutting</a>
                <a href="../welding-accessories/industrial-markers.php" class="mini-sub-item">Markers</a>
                <a href="../welding-accessories/measuring-gauge.php" class="mini-sub-item">Measuring Gauge</a>
                <a href="../welding-accessories/others.php" class="mini-sub-item">Others</a>
            </div>
        </div>
        <div class="mini-sidebar-icon mini-has-sub" data-target="../welding-consumables/welding-consumables.php" title="Welding Consumables"><i class="bi bi-box"></i><span class="label">Welding Consumables<span class="mini-arrow"><i class="bi bi-chevron-right"></i></span></span>
            <div class="mini-sidebar-sub">
                <a href="../welding-consumables/kobelco.php" class="mini-sub-item">Kobelco</a>
                <a href="../welding-consumables/metrode.php" class="mini-sub-item">Metrode</a>
            </div>
        </div>
        <button class="mini-sidebar-toggle" id="expandSidebar" title="Toggle Sidebar"><i class="bi bi-chevron-right"></i></button>
    </div>

    <div class="category-container">
        <div class="category-header">
            <h1><?php echo $category_name; ?></h1>
            <p><?php echo $category_description; ?></p>
        </div>

        <div class="category-content">
            <h2><?php echo htmlspecialchars($current_category['name'] ?? 'Arc Welding Robots'); ?></h2>
            <?php if (!empty($current_category['description'])): ?>
                <p class="category-description"><?php echo htmlspecialchars($current_category['description']); ?></p>
            <?php endif; ?>
            <div class="product-grid">
                <?php 
                // Dynamically fetch all products from all arc-welding-robots subcategories
                $subcategories = array();
                if (!empty($current_category['subcategories']) && is_array($current_category['subcategories'])) {
                    foreach ($current_category['subcategories'] as $subcat) {
                        $subcategories[] = $subcat['id'];
                    }
                }
                
                $all_products = array();
                foreach ($subcategories as $subcat) {
                    $products = andison_get_products_for_subcategory('arc-welding-robots', $subcat);
                    if ($products) {
                        $all_products = array_merge($all_products, $products);
                    }
                }
                
                // Display products
                if (!empty($all_products)) {
                    foreach ($all_products as $product) {
                        $image_src = htmlspecialchars($product['image'] ?? '');
                        // Adjust image path for subdirectory context
                        if ($image_src && strpos($image_src, '../') !== 0 && strpos($image_src, 'assets/') === 0) {
                            // For product pages in subdirectories, add ../ prefix to assets
                            $image_src = '../' . $image_src;
                        }
                        $model = htmlspecialchars($product['model'] ?? '');
                        $name = htmlspecialchars($product['name'] ?? '');
                        $type = htmlspecialchars($product['type'] ?? 'Premium Arc Welding Robots');
                        $brand = htmlspecialchars($product['brand'] ?? 'Industrial');
                        $description = htmlspecialchars($product['description'] ?? '');
                        $badge = htmlspecialchars($product['badge'] ?? '');
                        ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($image_src)): ?>
                            <img src="<?php echo $image_src; ?>" alt="<?php echo $name; ?>" onerror="this.parentElement.innerHTML='<i class=\"bi bi-robot\" style=\"font-size: 60px; color: #ccc;\"></i>'">
                        <?php else: ?>
                            <i class="bi bi-robot" style="font-size: 60px; color: #ccc;"></i>
                        <?php endif; ?>
                        <?php if (!empty($badge)): ?>
                            <div class="product-badge"><?php echo $badge; ?></div>
                        <?php endif; ?>
                    </div>
                    <h4><?php echo $name ?: 'Product'; ?></h4>
                    <?php if (!empty($model)): ?>
                        <p class="product-model"><strong>Model:</strong> <?php echo $model; ?></p>
                    <?php endif; ?>
                    <?php if (!empty($description)): ?>
                        <p class="product-description"><?php echo $description; ?></p>
                    <?php endif; ?>
                    <button class="add-to-inquiry" type="button" data-model="<?php echo $model; ?>" data-type="<?php echo $type ?: 'Equipment'; ?>" data-brand="<?php echo $brand; ?>">ADD TO INQUIRY</button>
                </div>
                        <?php
                    }
                } else {
                    // Fallback to placeholder if no products
                    ?>
                <div class="product-card">
                    <div class="product-image">
                        <i class="bi bi-robot" style="font-size: 60px; color: #ccc;"></i>
                    </div>
                    <h4>Arc Welding Robots</h4>
                    <p class="product-type">No products available</p>
                    <button class="add-to-inquiry" type="button" data-model="Arc Welding Robot" data-type="Equipment" data-brand="Industrial" disabled>ADD TO INQUIRY</button>
                </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="#sitemap">Sitemap</a>
            </div>
            <div class="footer-copyright">
                &copy; 2026 ANDISON INDUSTRIAL. All rights reserved.
            </div>
        </div>
    </footer>

    <script>
        // Add to Inquiry client-side handling
        (function(){
            function getItems(){
                try{ return JSON.parse(localStorage.getItem('inquiryItems')||'[]'); }catch(e){ return []; }
            }
            function setItems(items){ localStorage.setItem('inquiryItems', JSON.stringify(items)); }
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
                setTimeout(function(){ btn.textContent = 'ADD TO INQUIRY'; }, 900);
            });
        })();
    </script>
    
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
        // ============================================
        // ACTIVE SIDEBAR CATEGORY HIGHLIGHTING
        // ============================================
        setTimeout(function(){
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
        }, 500);
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

        // Responsive function to show/hide browse toggle
        function updateBrowseToggleVisibility() {
            if(window.innerWidth <= 1024) {
                browseToggle.classList.add('active');
            } else {
                browseToggle.classList.remove('active');
            }
        }

        // Initialize on load
        updateBrowseToggleVisibility();

        // Update on window resize
        window.addEventListener('resize', updateBrowseToggleVisibility);

        // Browse toggle click - works differently on desktop vs mobile
        browseToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Always toggle mini sidebar expand
            miniSidebar.classList.toggle('expanded');
            browseToggle.classList.toggle('expanded');
        });

        // Expand/collapse sidebar when clicking expand button (mini sidebar)
        expandBtn.addEventListener('click', function() {
            miniSidebar.classList.toggle('expanded');
            browseToggle.classList.toggle('expanded');
        });

        // Mini icons navigation and expand
        miniIcons.forEach(function(icon) {
            // Handle expand/collapse for categories with subs
            if(icon.classList.contains('mini-has-sub')) {
                icon.addEventListener('click', function(e) {
                    // Check if the click target is a sub-item link
                    if(e.target.classList.contains('mini-sub-item') || e.target.closest('.mini-sub-item')) {
                        // Allow the link to navigate normally
                        return true;
                    }
                    
                    e.stopPropagation();
                    e.preventDefault();
                    
                    // If sidebar is collapsed (icon only), expand it first
                    if(!miniSidebar.classList.contains('expanded')) {
                        miniSidebar.classList.add('expanded');
                        browseToggle.classList.add('expanded');
                    } else {
                        // Sidebar is already expanded - toggle the subs
                        icon.classList.toggle('mini-expanded');
                    }
                });
            } else {
                // Regular navigation
                icon.addEventListener('click', function(e) {
                    e.preventDefault();
                    var target = this.getAttribute('data-target');
                    if(target) {
                        window.location.href = target;
                    }
                });
            }
        });

        // Handle sub-item clicks
        var subItems = document.querySelectorAll('.mini-sub-item');
        subItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                // Allow natural link behavior
                var href = this.getAttribute('href');
                if(href) {
                    window.location.href = href;
                }
            });
        });

        // Sync active state between sidebars
        var currentPath = window.location.pathname.toLowerCase();
        var pathParts = currentPath.split('/').filter(function(p) { return p && p !== 'andison-1'; });
        
        if(miniIcons.length > 0) {
            miniIcons.forEach(function(icon) {
                icon.classList.remove('active-icon');
                var href = icon.getAttribute('data-target');
                if(href && currentPath.includes(href.split('/')[href.split('/').length - 2])) {
                    icon.classList.add('active-icon');
                }
            });
        }
    </script>
</body>
</html>


