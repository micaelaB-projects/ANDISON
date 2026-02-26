<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

// Update page name and subcategory
$page_title = "Accessories";
$category_id = "arc-welding-machine";
$subcategory_id = "accessories";
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
        'name' => 'Accessories',
        'description' => 'Essential welding and cutting accessories for optimal equipment performance.',
        'subcategories' => array(
            array('id' => 'accessories', 'name' => 'Accessories')
        )
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
    $category_name = 'Accessories';
    $category_description = 'Essential welding and cutting accessories for optimal equipment performance.';
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

        .contact-list { list-style: none !important; margin: 0 !important; padding: 6px 0 !important; display: block !important; visibility: visible !important; }
        .contact-list li { display:flex !important; gap:12px !important; align-items:center !important; padding:10px 6px !important; visibility: visible !important; }
        .contact-list .icon { font-size:18px !important; width:28px !important; text-align:center !important; color:#2B11DB !important; }
        .contact-list a { color: #111 !important; text-decoration:none !important; font-weight:600 !important; }
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

        .search-bar .search-field i {
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
            padding: 0 8px 0 120px;
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
            min-height: 52px;
            gap: 0;
            justify-content: center;
        }

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

        .nav-list > li > a {
            position: relative;
            padding: 10px 14px;
            color: white;
            transition: color 180ms ease, background 180ms ease;
        }

        .nav-list > li > a:hover {
            background: rgba(0,0,0,0.10);
            border-radius: 6px;
        }

        .nav-list > li > a.active {
            background: rgba(0,0,0,0.14);
            color: #fff;
            font-weight: 700;
            border-radius: 6px;
            box-shadow: inset 0 -6px 18px rgba(0,0,0,0.06);
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
            display: none;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
            z-index: 110;
            padding: 16px;
            margin-top: 8px;
        }

        .nav-list > li:hover .nav-dropdown {
            opacity: 1;
            visibility: visible;
            display: block;
            transform: translateX(-50%) translateY(0);
        }

        .nav-dropdown:hover {
            opacity: 1;
            visibility: visible;
            display: block;
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

        /* Pagination */
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
        }

        .pagination span.active {
            background: linear-gradient(135deg, #2B11DB 0%, #1a0a7f 100%);
            color: white;
            border-color: #2B11DB;
        }

        .pagination span.dots {
            border: none;
            cursor: default;
            padding: 0 4px;
        }

        .pagination .page-info {
            color: #666;
            font-size: 12px;
            margin-left: 12px;
            border-left: 1px solid #e0e0e0;
            padding-left: 12px;
        }

        @media (max-width: 1024px) {
            .category-content {
                flex-direction: column;
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
        }

        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: 1fr;
            }

            .product-filters {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            .filter-section {
                border-bottom: none;
                border: 1px solid #e5e7eb;
                padding: 12px;
                border-radius: 6px;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
    <?php
        $page_title = "Brands";
        $company_name = "ANDISON INDUSTRIAL";
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
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="../inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
                <div class="header-contact">
                    <div class="contact-dropdown" tabindex="0" aria-haspopup="true">
                        <a href="javascript:void(0)" class="contact-link" aria-label="Contact Us">Contact Us ▾</a>
                        <div class="contact-popover" role="menu" aria-hidden="true">
                                <button type="button" class="contact-close" aria-label="Close contact popover">&times;</button>
                                <p style="font-weight:700;font-size:13px;color:#2B11DB;margin-bottom:8px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">Get in Touch</p>
                                <ul class="contact-list" style="display:block!important;visibility:visible!important;list-style:none;margin:0;padding:6px 0;">
                                    <li style="display:flex!important;gap:12px;align-items:center;padding:10px 6px;color:#333!important;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB!important;flex-shrink:0;"><i class="bi bi-telephone-fill"></i></span><div style="color:#333!important;"><small style="color:#999!important;font-size:11px;display:block!important;">Landline</small><a href="tel:+12345678900" style="color:#111!important;text-decoration:none;font-weight:600;">+1(234) 567 8900</a></div></li>
                                    <li style="display:flex!important;gap:12px;align-items:center;padding:10px 6px;color:#333!important;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB!important;flex-shrink:0;"><i class="bi bi-telephone-fill"></i></span><div style="color:#333!important;"><small style="color:#999!important;font-size:11px;display:block!important;">Mobile</small><a href="tel:+16399778037398" style="color:#111!important;text-decoration:none;font-weight:600;">+1(639) 977 803 7398</a></div></li>
                                    <li style="display:flex!important;gap:12px;align-items:center;padding:10px 6px;color:#333!important;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB!important;flex-shrink:0;"><i class="bi bi-envelope-fill"></i></span><div style="color:#333!important;"><small style="color:#999!important;font-size:11px;display:block!important;">Email</small><a href="mailto:info@andison-industrial.com" style="color:#111!important;text-decoration:none;font-weight:600;">info@andison-industrial.com</a></div></li>
                                    <li style="display:flex!important;gap:12px;align-items:center;padding:10px 6px;color:#333!important;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB!important;flex-shrink:0;"><i class="bi bi-facebook"></i></span><div style="color:#333!important;"><small style="color:#999!important;font-size:11px;display:block!important;">Facebook</small><a href="https://www.facebook.com/AndisonIndustrialSalesInc" target="_blank" style="color:#111!important;text-decoration:none;font-weight:600;">Andison Industrial</a></div></li>
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
                    <li><a href="../home.php">Home</a></li>
                    <li><a href="../aboutus.php">About Us</a></li>
                    <li><a href="../brands.php">Brands</a></li>
                    <li><a href="../industries.php">Industries</a></li>
                    <li><a href="../services.php">Services</a></li>
                    <li><a href="../contact.php">Contact Us</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="category-content">
        <h2>Accessories</h2>
        
        <!-- Left Filters Panel -->
        <div class="product-filters">
            <div class="filter-section">
                <h3>Categories</h3>
                <div class="filter-option">
                    <input type="checkbox" id="cat-all" onchange="filterProducts()">
                    <label for="cat-all">All Products</label>
                </div>
            </div>

            <div class="filter-section">
                <h3>Product Type</h3>
                <div class="filter-option">
                    <input type="checkbox" id="type-accessories" onchange="filterProducts()">
                    <label for="type-accessories">Accessories</label>
                </div>
            </div>

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
                </select>
            </div>

            <!-- Product Grid -->
            <div class="product-grid">
            <?php 
            $all_products = andison_get_products_for_subcategory('arc-welding-machine', 'accessories');
            
            if (!empty($all_products)) {
                foreach ($all_products as $product) {
                    $image_src = htmlspecialchars($product['image'] ?? '');
                    if ($image_src) {
                        if (strpos($image_src, 'assets/') === 0) {
                            $image_src = '../' . $image_src;
                        }
                    }
                    $model = htmlspecialchars($product['model'] ?? '');
                    $name = htmlspecialchars($product['name'] ?? '');
                    $type = htmlspecialchars($product['type'] ?? 'Equipment');
                    $brand = htmlspecialchars($product['brand'] ?? 'Industrial');
                    $description = htmlspecialchars($product['description'] ?? '');
                    ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if (!empty($image_src)): ?>
                        <img src="<?php echo $image_src; ?>" alt="<?php echo $name; ?>" onerror="this.parentElement.innerHTML='<i class=&quot;bi bi-lightning-charge&quot; style=&quot;font-size: 56px; color: #ccc;&quot;></i>'">
                    <?php else: ?>
                        <i class="bi bi-lightning-charge" style="font-size: 56px; color: #ccc;"></i>
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
                    <button class="add-to-inquiry" type="button" data-model="<?php echo $model; ?>" data-type="<?php echo $type; ?>" data-brand="<?php echo $brand; ?>">ADD TO INQUIRY LIST</button>
                </div>
            </div>
                    <?php
                }
            } else {
                ?>
            <div class="product-card">
                <div class="product-image">
                    <i class="bi bi-lightning-charge" style="font-size: 56px; color: #ccc;"></i>
                </div>
                <div class="product-info">
                    <h4>Accessories</h4>
                    <p class="product-description">No products available</p>
                    <button class="add-to-inquiry" type="button" disabled>ADD TO INQUIRY</button>
                </div>
            </div>
            <?php
            }
            ?>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <span class="active">1</span>
                <span class="page-info">Page 1</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2026 <?php echo $company_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function filterProducts() {
            var searchInput = document.getElementById('searchInput')?.value?.toLowerCase() || '';
            var allCards = Array.from(document.querySelectorAll('.product-grid .product-card'));
            var visibleCards = [];

            allCards.forEach(function(card) {
                var text = card.textContent.toLowerCase();
                var matchesSearch = !searchInput || text.includes(searchInput);
                if (matchesSearch) {
                    card.style.display = '';
                    visibleCards.push(card);
                } else {
                    card.style.display = 'none';
                }
            });

            var sortSelect = document.getElementById('sortSelect')?.value || 'default';
            if (sortSelect !== 'default') {
                visibleCards.sort(function(a, b) {
                    var aText = a.querySelector('h4')?.textContent || '';
                    var bText = b.querySelector('h4')?.textContent || '';
                    if (sortSelect === 'name-asc') return aText.localeCompare(bText);
                    if (sortSelect === 'name-desc') return bText.localeCompare(aText);
                    return 0;
                });

                var grid = document.querySelector('.product-grid');
                visibleCards.forEach(function(card) {
                    grid.appendChild(card);
                });
            }
        }

        function clearAllFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('sortSelect').value = 'default';
            filterProducts();
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to inquiry buttons
            document.querySelectorAll('.add-to-inquiry').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    if (!btn.disabled) {
                        e.stopPropagation();
                        var model = btn.dataset.model;
                        var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                        var product = {
                            model: model,
                            qty: 1,
                            timestamp: new Date().getTime()
                        };
                        items.push(product);
                        localStorage.setItem('inquiryItems', JSON.stringify(items));
                        btn.innerHTML = '<i class="bi bi-check-circle"></i> Added!';
                        btn.style.background = 'linear-gradient(135deg, #10B981 0%, #059669 100%)';
                        setTimeout(function() {
                            btn.innerHTML = 'ADD TO INQUIRY LIST';
                            btn.style.background = '';
                        }, 1500);
                    }
                });
            });
        });
    </script>
</body>
</html>


