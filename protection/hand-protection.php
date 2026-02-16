<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$page_title = "Hand Protection";
$category_id = "protection";
$subcategory_id = "hand-protection";
$phone = "+1(234) 567 8900";
$phone2 = "+1(234) 567 8900";
$phone3 = "+1(639) 977 803 7398";
$email = "info@andison-industrial.com";

$categories = andison_get_categories();
$current_category = null;
$parent_category = null;

foreach ($categories as $cat) {
    if ($cat['id'] === $category_id) {
        $parent_category = $cat;
        if (!empty($cat['subcategories'])) {
            foreach ($cat['subcategories'] as $subcat) {
                if ($subcat['id'] === $subcategory_id) {
                    $current_category = $subcat;
                    break;
                }
            }
        }
        break;
    }
}

if (!$current_category) {
    // Fallback: create a default category object
    $current_category = array(
        'id' => $subcategory_id,
        'name' => 'Hand Protection',
        'description' => 'Comprehensive hand protection solutions for various work environments.',
        'subcategories' => array()
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    $category_name = $current_category['name'] ?? 'Hand Protection';
    $category_description = $current_category['description'] ?? 'Comprehensive hand protection solutions for various work environments.';
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
            line: height 6px;;
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
            background: linear-gradient(135deg, rgba(43, 17, 219, 0.05) 0%, rgba(0, 215, 179, 0.05) 100%);
            border-radius: 12px;
            padding: 50px 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 40px;
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

        .product-image i {
            font-size: 48px;
            color: #e0e0e0;
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

        .add-to-inquiry:active {
            transform: translateY(0);
        }

        .add-to-inquiry:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .add-to-inquiry.already {
            background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
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

        @media (max-width: 1400px) {
            .product-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }
        }

        @media (max-width: 1200px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
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
                padding: 35px 25px;
                margin-bottom: 30px;
            }

            .category-header h1 {
                font-size: 28px;
                margin-bottom: 12px;
            }

            .category-header p {
                font-size: 15px;
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

        .sidebar-list a.active {
            background: #f0e6ff;
            color: #2B11DB;
            border-left: 5px solid #2B11DB;
            padding-left: 11px;
        }

        .sidebar-list a.active .sidebar-icon {
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
        .sidebar-sublist li.has-nested-sub { 
            position: relative;
            padding-right: 0;
        }
        .sidebar-sublist li.has-nested-sub > a { 
            padding-right: 30px;
        }
        
        .nested-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            width: auto;
            height: auto;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            z-index: 100;
            pointer-events: auto;
        }
        .nested-toggle:focus { outline: none; }
        .nested-toggle .bi { transition: transform 200ms ease; }
        .nested-toggle[aria-expanded="true"] .bi { transform: rotate(90deg); }

        .sidebar-nested-sublist { 
            list-style: none; 
            margin: 0 !important; 
            padding: 0 !important; 
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
            width: 0 !important;
            max-height: 0 !important;
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

        .sidebar-nested-sublist.collapsed { 
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            overflow: hidden !important;
        }
        .sidebar-nested-sublist:not(.collapsed) { 
            display: block !important;
            visibility: visible !important;
            height: auto !important;
            overflow: visible !important;
            position: relative !important;
        }
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

        /* Hand Protection Category Cards */
        .hand-protection-categories-header {
            margin-bottom: 30px;
            text-align: center;
        }

        .hand-protection-categories-header h3 {
            color: #2B11DB;
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .hand-protection-categories-header p {
            color: #666;
            font-size: 15px;
        }

        .hand-protection-categories-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
            margin-bottom: 50px !important;
            margin-top: 30px !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
        }

        .hand-category-card {
            background: linear-gradient(135deg, #4b1f8f 0%, #2B11DB 100%) !important;
            border-radius: 14px !important;
            padding: 28px 20px !important;
            min-height: 200px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            align-items: center !important;
            cursor: pointer !important;
            text-decoration: none !important;
            color: white !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 4px 16px rgba(43, 17, 219, 0.15) !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .hand-category-card:hover {
            transform: translateY(-8px) !important;
            box-shadow: 0 12px 32px rgba(43, 17, 219, 0.25) !important;
        }

        .hand-category-card-icon {
            font-size: 48px;
            color: #00ffd1;
            margin-bottom: 16px;
            transition: transform 0.3s ease;
            filter: drop-shadow(0 2px 4px rgba(0, 255, 209, 0.2));
        }

        .hand-category-card:hover .hand-category-card-icon {
            transform: scale(1.15) rotate(-5deg);
        }

        .hand-category-card-title {
            font-size: 16px;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            text-align: center;
        }

        .hand-category-card-arrow {
            color: #00ffd1;
            font-size: 18px;
            transition: transform 0.3s ease;
            margin-top: auto;
        }

        .hand-category-card:hover .hand-category-card-arrow {
            transform: translateX(4px);
        }

        @media (max-width: 1200px) {
            .hand-protection-categories-grid {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }

        @media (max-width: 768px) {
            .hand-protection-categories-grid {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 480px) {
            .hand-protection-categories-grid {
                grid-template-columns: 1fr !important;
            }
        }

        /* Sidebar sublist animation */
        .sidebar-sublist {
            max-height: 1000px;
            overflow: hidden;
            transition: max-height 0.3s ease, opacity 0.3s ease;
            opacity: 1;
        }
        
        .sidebar-sublist.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
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
            margin-left: 80px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mini-sidebar.expanded ~ .main-content,
        .mini-sidebar.expanded ~ .category-container {
            margin-left: 280px;
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
                <button id="browseToggle" class="browse-toggle"><span class="hamburger"><i class="bi bi-list"></i></span> BROWSE PRODUCTS</button>
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
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 12px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px; color: #1f2937;">Categories</h3>
            <button class="sidebar-close" id="closeSidebar"><i class="bi bi-x-lg"></i></button>
        </div>
        <ul class="sidebar-list">
            <li class="has-sub">
                <a href="../arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-welding" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="../arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="../arc-welding-machine/co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="../arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="../arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-robot" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-robot" class="sidebar-sublist collapsed">
                    <li><a href="../arc-welding-robots/g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="../arc-welding-robots/g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="../arc-welding-robots/featured-products-and-solution.php">Featured Products & Solutions</a></li>
                    <li><a href="../arc-welding-robots/robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-batteries" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="../batteries/maintenance-free.php">Maintenance Free</a></li>
                    <li><a href="../batteries/low-maintenance.php">Low Maintenance</a></li>
                    <li><a href="../batteries/special-batteries.php">Special Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling &amp; Lifting</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-drilling-lifting" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="../drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li><a href="../drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a></li>
                    <li><a href="../drilling-and-lifting/cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub active">
                <a href="../gas-detectors/portable-gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Portable Gas Detectors</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-gas-detectors" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="../gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="../gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                    <li><a href="../gas-detectors/docking-data-management.php">Docking and Data Management</a></li>
                    <li><a href="../gas-detectors/calibration-gas-regulators.php">Calibration Gas and Regulators</a></li>
                </ul>
            </li>
            <li>
                <a href="../portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
            </li>
            <li class="has-sub">
                <a href="../power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-power-tool" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                    <li><a href="../power-tools/grinder/grinder.php">Grinder</a></li>
                    <li><a href="../power-tools/saw/saw.php">Saw</a></li>
                    <li><a href="../power-tools/drill-and-wrench/drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="../power-tools/rotary-and-demolition-hammer/rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="../power-tools/accessories/accessories.php">Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-protection-safety" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="../protection/eye-protection.php">Eye Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="../protection/hand-protection.php">Hand Protection</a>
                        <button class="nested-toggle" aria-expanded="false" aria-controls="nested-hand-protection" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
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
                        <button class="nested-toggle" aria-expanded="false" aria-controls="nested-body-protection" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
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
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-welding-accessories" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
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
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-welding-consumables" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="../welding-consumables/kobelco.php">Kobelco</a></li>
                    <li><a href="../welding-consumables/metrode.php">Metrode</a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <!-- Mini Sidebar (Icon Bar) -->
    <div class="mini-sidebar active" id="miniSidebar">
        <div id="miniSidebarMenuBar" style="background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); border-radius: 0; display: flex; align-items: center; gap: 8px; cursor: pointer;"><i class="bi bi-list" style="font-size: 18px; font-weight: 700; color: white;"></i><span style="font-size: 13px; font-weight: 700; color: white; letter-spacing: 0.5px; display: none;" class="browse-label">BROWSE CATEGORIES</span></div>
        <div class="mini-sidebar-icon has-sub" data-target="../arc-welding-machine/arc-welding-machine.php" title="Arc Welding Machines"><i class="bi bi-lightning-charge"></i><span class="label">Arc Welding Machines</span><span class="sub-indicator"><i class="bi bi-chevron-right"></i></span></div>
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

    <!-- Floating popover for mini sidebar subcategories -->
    <div id="miniPopover" class="mini-popover" aria-hidden="true">
        <div class="mini-popover-header">
            <div class="mini-popover-title">Arc Welding Robots</div>
        </div>
        <div class="mini-popover-body">
            <ul class="mini-popover-list"></ul>
        </div>
    </div>

    <div class="category-container">
        <div class="category-header">
            <h1><?php echo $category_name; ?></h1>
            <p><?php echo $category_description; ?></p>
        </div>

        <div class="category-content">
            <h2><?php echo htmlspecialchars($current_category['name'] ?? 'Hand Protection'); ?></h2>
            <?php if (!empty($current_category['description'])): ?>
                <p class="category-description"><?php echo htmlspecialchars($current_category['description']); ?></p>
            <?php endif; ?>

            <!-- Hand Protection Category Cards -->
            <div class="hand-protection-categories-header">
                <h3>Browse by Glove Type</h3>
                <p>Select a hand protection category to explore our comprehensive range</p>
            </div>

            <div class="hand-protection-categories-grid">
                <a href="welding-gloves.php" class="hand-category-card">
                    <i class="bi bi-fire hand-category-card-icon"></i>
                    <h4 class="hand-category-card-title">Welding Gloves</h4>
                    <i class="bi bi-chevron-right hand-category-card-arrow"></i>
                </a>
                <a href="working-gloves.php" class="hand-category-card">
                    <i class="bi bi-hand-index hand-category-card-icon"></i>
                    <h4 class="hand-category-card-title">Working Gloves</h4>
                    <i class="bi bi-chevron-right hand-category-card-arrow"></i>
                </a>
                <a href="chemical-liquid-protection-gloves.php" class="hand-category-card">
                    <i class="bi bi-droplet hand-category-card-icon"></i>
                    <h4 class="hand-category-card-title">Chemical & Liquid Protection</h4>
                    <i class="bi bi-chevron-right hand-category-card-arrow"></i>
                </a>
                <a href="disposable-gloves.php" class="hand-category-card">
                    <i class="bi bi-shield-check hand-category-card-icon"></i>
                    <h4 class="hand-category-card-title">Disposable Gloves</h4>
                    <i class="bi bi-chevron-right hand-category-card-arrow"></i>
                </a>
            </div>

            <div class="product-grid">
                <?php 
                // Dynamically fetch products for hand-protection
                $all_products = array();
                
                // Check if current category has subcategories (like magnetic-drill has b-line, rl-e, etc)
                if (!empty($current_category['subcategories']) && is_array($current_category['subcategories'])) {
                    // If it has subcategories, fetch from each subcategory
                    foreach ($current_category['subcategories'] as $subcat) {
                        $products = andison_get_products_for_subcategory($category_id, $subcat['id']);
                        if ($products) {
                            $all_products = array_merge($all_products, $products);
                        }
                    }
                } else {
                    // If it doesn't have subcategories, fetch directly for this category
                    $products = andison_get_products_for_subcategory($category_id, $subcategory_id);
                    if ($products) {
                        $all_products = $products;
                    }
                }
                
                // Display products
                if (!empty($all_products)) {
                    foreach ($all_products as $product) {
                        $image_src = htmlspecialchars($product['image'] ?? '');
                        // Adjust image path for subdirectory context
                        if ($image_src && strpos($image_src, 'andison/') === 0) {
                            $image_src = '../' . $image_src;
                        }
                        $model = htmlspecialchars($product['model'] ?? '');
                        $name = htmlspecialchars($product['name'] ?? '');
                        $type = htmlspecialchars($product['type'] ?? 'Gas Detection Equipment');
                        $brand = htmlspecialchars($product['brand'] ?? 'Industrial');
                        $description = htmlspecialchars($product['description'] ?? '');
                        $badge = htmlspecialchars($product['badge'] ?? '');
                        ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($image_src)): ?>
                            <img src="<?php echo $image_src; ?>" alt="<?php echo $name; ?>" onerror="this.parentElement.innerHTML='<i class=&quot;bi bi-hammer&quot; style=&quot;font-size: 60px; color: #ccc;&quot;></i>'">
                        <?php else: ?>
                            <i class="bi bi-hammer" style="font-size: 60px; color: #ccc;"></i>
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
                        <i class="bi bi-hammer" style="font-size: 60px; color: #ccc;"></i>
                    </div>
                    <h4>Single Gas Detector</h4>
                    <p class="product-type">No products available</p>
                    <button class="add-to-inquiry" type="button" data-model="Single Gas Detector" data-type="Detection Equipment" data-brand="Industrial">ADD TO INQUIRY</button>
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
        // ACTIVE SIDEBAR CATEGORY HIGHLIGHTING
        // ============================================
        (function(){
            var currentPath = window.location.pathname.toLowerCase();
            var sidebar = document.getElementById('sidebar');
            if(!sidebar) return;

            // Extract category - split path and find category directory
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
            
            // Find matching category in path
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
        })();
    </script>

    <script>
        // ACTIVE SIDEBAR CATEGORY HIGHLIGHTING WITH DELAY
        // ============================================
        setTimeout(function(){
            var currentPath = window.location.pathname.toLowerCase();
            console.log('Delayed run - Pathname:', currentPath);
            
            var sidebar = document.getElementById('sidebar');
            if(!sidebar) {
                console.log('Sidebar NOT found even after delay');
                return;
            }

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
                console.log('Highlighting for category:', currentCategory, 'Links found:', links.length);
                
                links.forEach(function(link){
                    var href = link.getAttribute('href');
                    if(href){
                        var hrefLower = href.toLowerCase();
                        if(hrefLower.includes(currentCategory)){
                            link.classList.add('active');
                            console.log('Added active to:', href);
                        }
                    }
                });
            }
        }, 500);
    </script>

    <script>
        var miniSidebar = document.getElementById('miniSidebar');var mainSidebar = document.getElementById('sidebar');var backdrop = document.getElementById('overlayBackdrop');var expandBtn = document.getElementById('expandSidebar');var miniIcons = document.querySelectorAll('.mini-sidebar-icon');var miniPopover = document.getElementById('miniPopover');var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;var popoverList = miniPopover ? miniPopover.querySelector('.mini-popover-list') : null;var currentPopoverKey = null;
        function getCategoryKeyFromTarget(dataTarget) {if (!dataTarget) return null;var keys = ['arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting','gas-detectors','portable-ventilators','power-tools','protection','welding-accessories','welding-consumables'];for (var i=0;i<keys.length;i++) { if (dataTarget.indexOf('/'+ keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'/') !== -1) return keys[i]; }return null;}
        function getCategoryTitle(key) {var map = {'arc-welding-machine': 'Arc Welding Machines','arc-welding-robots': 'Arc Welding Robots','batteries': 'Batteries','drilling-and-lifting': 'Drilling and Lifting','gas-detectors': 'Gas Detectors','portable-ventilators': 'Portable Ventilators','power-tools': 'Power Tools','protection': 'Personal Protective Equipment','welding-accessories': 'Welding Accessories','welding-consumables': 'Welding Consumables'};return map[key] || 'Categories';}
        function getPopoverItems(key) {var base = '.';var maps = {'arc-welding-robots': [{label: 'G3 Controller Series', href: base + '/../arc-welding-robots/g3-controller-series.php'},{label: 'G4 Controller Series', href: base + '/../arc-welding-robots/g4-controller-series.php'},{label: 'Featured Products and Solutions', href: base + '/../arc-welding-robots/featured-products-and-solution.php'},{label: 'Robot System Peripherals', href: base + '/../arc-welding-robots/robot-system-peripherals.php'}],'arc-welding-machine': [{label: 'MIG Welding Machine', href: base + '/../arc-welding-machine/mig-welding-machine.php'},{label: 'CO1/MAG Welding Machine', href: base + '/../arc-welding-machine/co1-mag-welding-machine.php'},{label: 'STUD Welding Machine', href: base + '/../arc-welding-machine/stud-welding-machine.php'},{label: 'TIG Welding Machine', href: base + '/../arc-welding-machine/tig-welding-machine.php'},{label: 'Plasma Cutting Machine', href: base + '/../arc-welding-machine/plasma-cutting-machine.php'}],'batteries': [{label: 'Maintenance Free', href: base + '/../batteries/maintenance-free.php'},{label: 'Low Maintenance', href: base + '/../batteries/low-maintenance.php'},{label: 'Special Batteries', href: base + '/../batteries/special-batteries.php'}],'drilling-and-lifting': [{label: 'Lifting', href: base + '/../drilling-and-lifting/lifting.php'},{label: 'Magnetic Drill', href: base + '/../drilling-and-lifting/magnetic-drill.php'},{label: 'Cutters', href: base + '/../drilling-and-lifting/cutters.php'}],'gas-detectors': [{label: 'Single Gas Detector', href: base + '/../gas-detectors/single-gas-detector.php'},{label: 'Multi Gas Detector', href: base + '/../gas-detectors/multi-gas-detector.php'},{label: 'Portable Gas Detectors', href: base + '/../gas-detectors/portable-gas-detectors.php'},{label: 'Docking and Data Management', href: base + '/../gas-detectors/docking-data-management.php'},{label: 'Calibration Gas and Regulators', href: base + '/../gas-detectors/calibration-gas-regulators.php'}],'power-tools': [{label: 'Grinder', href: base + '/../power-tools/grinder.php'},{label: 'Saw', href: base + '/../power-tools/saw.php'},{label: 'Drill and Wrench', href: base + '/../power-tools/drill-and-wrench.php'},{label: 'Rotary and Demolition Hammer', href: base + '/../power-tools/rotary-and-demolition-hammer.php'},{label: 'Accessories', href: base + '/../power-tools/accessories.php'}],'portable-ventilators': [{label: 'Electric Driven', href: base + '/../portable-ventilators/electric-driven.php'},{label: 'Pneumatic Driven', href: base + '/../portable-ventilators/pneumatic-driven.php'}],'protection': [{label: 'Eye Protection', href: base + '/../protection/eye-protection.php'},{label: 'Hand Protection', href: base + '/../protection/hand-protection.php'},{label: 'Hearing & Respiratory Protection', href: base + '/../protection/hearing-respiratory-protection.php'},{label: 'Welding Head and Face Protection', href: base + '/../protection/welding-head-and-face-protection.php'},{label: 'Body Protection', href: base + '/../protection/body-protection.php'}],'welding-accessories': [{label: 'Welding Electrode Oven', href: base + '/../welding-accessories/welding-electrode-oven.php'},{label: 'Non-Destructive Crack Detection', href: base + '/../welding-accessories/non-destructive-crack-detection.php'},{label: 'Gas Saving Regulator', href: base + '/../welding-accessories/gas-saving-regulator.php'},{label: 'Gas Cutting Equipment', href: base + '/../welding-accessories/gas-cutting-equipment.php'},{label: 'Industrial Markers', href: base + '/../welding-accessories/industrial-markers.php'},{label: 'Measuring Gauge', href: base + '/../welding-accessories/measuring-gauge.php'},{label: 'Others', href: base + '/../welding-accessories/others.php'}],'welding-consumables': [{label: 'Kobelco', href: base + '/../welding-consumables/kobelco.php'},{label: 'Metrode', href: base + '/../welding-consumables/metrode.php'}]};return maps[key] || [];}
        function renderPopover(key) {if (!miniPopover || !popoverList) return;popoverList.innerHTML = '';var items = getPopoverItems(key);items.forEach(function(it){var li = document.createElement('li');li.className = 'mini-popover-item';li.innerHTML = '<span class="square"></span><a href="'+ it.href +'">'+ it.label +'</a>';popoverList.appendChild(li);});if (popoverTitle) popoverTitle.textContent = getCategoryTitle(key);}
        function positionPopoverForIcon(icon) {if (!miniPopover || !icon) return;miniPopover.style.left = '-9999px';miniPopover.style.top = '-9999px';miniPopover.classList.add('show');var rect = icon.getBoundingClientRect();var pw = miniPopover.offsetWidth;var ph = miniPopover.offsetHeight;var iconCenterY = rect.top + rect.height / 2;var left = Math.round(rect.right + 14);var top = Math.round(iconCenterY - ph / 2);if (left + pw + 12 > window.innerWidth) { left = Math.round(rect.left - pw - 14); }var headerHeight = 170;var minTop = headerHeight + 12;var maxTop = window.innerHeight - ph - 12;if (top < minTop) top = minTop;if (top > maxTop) top = maxTop;var arrowOffset = iconCenterY - top - 26;miniPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');miniPopover.style.left = left + 'px';miniPopover.style.top = top + 'px';}
        function hidePopover() {if (!miniPopover) return;miniPopover.classList.remove('show');miniPopover.setAttribute('aria-hidden', 'true');currentPopoverKey = null;}
        function showPopoverForKey(key, icon) {if (!miniPopover) return;if (currentPopoverKey === key && miniPopover.classList.contains('show')) { hidePopover(); return; }renderPopover(key);positionPopoverForIcon(icon);miniPopover.classList.add('show');miniPopover.setAttribute('aria-hidden', 'false');currentPopoverKey = key;}
        document.addEventListener('click', function(e){if (!miniPopover) return;if (!miniPopover.classList.contains('show')) return;if (e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;hidePopover();});document.addEventListener('keydown', function(e){if (e.key === 'Escape') hidePopover();});
        expandBtn.addEventListener('click', function() {miniSidebar.classList.toggle('expanded');});var menuBar = document.getElementById('miniSidebarMenuBar');if(menuBar) {menuBar.addEventListener('click', function() {miniSidebar.classList.toggle('expanded');});}
        var arrowHandler = function(e) {e.stopPropagation();e.preventDefault();var arrow = (e.target && e.target.closest('.sub-indicator')) || e.currentTarget;var icon = arrow ? arrow.closest('.mini-sidebar-icon') : null;if (!icon) return;var dataTarget = icon.getAttribute('data-target') || '';var categoryKey = getCategoryKeyFromTarget(dataTarget);if (!categoryKey) return;showPopoverForKey(categoryKey, icon);};document.querySelectorAll('.sub-indicator').forEach(function(arrow) {arrow.addEventListener('click', arrowHandler, true);});document.addEventListener('click', function(e) {if (e.target.closest('.sub-indicator')) {arrowHandler(e);}}, true);
        miniIcons.forEach(function(icon) {icon.addEventListener('click', function(e) {if (e.target.closest('.sub-indicator')) {e.stopPropagation();return;}var target = this.getAttribute('data-target');if (target) {window.location.href = target;}}, true);});
        backdrop.addEventListener('click', function() {if(mainSidebar.classList.contains('active')) {mainSidebar.classList.remove('active');backdrop.classList.remove('active');}});var closeSidebarBtn = document.getElementById('closeSidebar');if(closeSidebarBtn) {closeSidebarBtn.addEventListener('click', function() {if(mainSidebar.classList.contains('active')) {mainSidebar.classList.remove('active');backdrop.classList.remove('active');}})}
    </script>
</body>
</html>