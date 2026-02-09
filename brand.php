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
            --footer-height: 160px; /* adjust if footer height changes */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            padding-top: 142px;
            /* reserve space at bottom for fixed footer so content isn't hidden */
            padding-bottom: var(--footer-height);
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
            margin-bottom: 40px;
            z-index: 1;
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
        }

        section h2 {
            text-align: center;
            font-size: 45px;
            margin-bottom: 20px;
            color: #2B11DB;
        }
    
        .section-description {
            text-align: center;
            max-width: 700px;
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
            height: 400px;
            background: linear-gradient(135deg, #888 0%, #666 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            position: relative;
            overflow: hidden;
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
            padding: 20px;
            background: white;
        }

        .product-info h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #333;
        }

        .product-info p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
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
        }

        .featured-content p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.8;
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

        /* Footer - fixed to viewport bottom and full-bleed */
        footer {
            --footer-height: var(--footer-height);
            background: #2B11DB;
            color: white;
            /* remove internal vertical padding so height is controlled by variable */
            padding: 0;
            text-align: center;
            /* make footer fixed and full-bleed */
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100vw;
            height: var(--footer-height);
            z-index: 1100;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .footer-content {
            width: 100%;
            margin: 0 auto;
            padding: 18px 20px; /* inner spacing for links and text */
            box-sizing: border-box;
            max-width: 1400px; /* keep content centered while footer background stays full-bleed */
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
         justify-content: center;
            gap: 30px;
            margin-bottom: 20px;
            flex-wrap: wrap;

        /* Responsive */
        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                gap: 15px;
            }

            nav ul {
                flex-wrap: wrap;
            }

            nav li {
                margin-right: 20px;
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
            .nav-inner { justify-content: space-between; padding-left: 20px; }
            .nav-list { position: static; transform: none; left: auto; margin: 8px auto 0; justify-content: center; flex-wrap: wrap; }
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

        /* ============================================
           BRAND PAGE SPECIFIC PRODUCT STYLING
           ============================================ */
        
        /* Brand Container */
        .brand-container {
            max-width: 1500px;
            margin: 40px auto 40px;
            padding: 0 40px;
            flex: 1;
        }

        .brand-header {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }

        .brand-header h1 {
            color: #2B11DB;
            font-size: 20px;
            margin-bottom: 10px;
            margin-top: 10px;
            display: block;
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
        }

        .brand-content {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .brand-content h2 {
            color: #2B11DB;
            font-size: 32px;
            margin-bottom: 20px;
            border-bottom: 3px solid #2B11DB;
            padding-bottom: 5px;
        }

        .brand-content h3 {
            color: #333;
            font-size: 24px;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .brand-content p {
            color: #555;
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .brand-content ul {
            margin-left: 30px;
            margin-bottom: 20px;
        }

        .brand-content li {
            margin-bottom: 10px;
            color: #555;
        }

        .product-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            margin-top: 30px;
            justify-content: center;
            align-items: stretch;
        }

        .brand-page .product-card {
            flex: 0 1 calc(20% - 20px);
            min-width: 240px;
            max-width: 280px;
            height: auto;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            cursor: pointer;
        }

        .brand-page .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(43,17,219,0.15);
            border-color: #2B11DB;
            background: #fafafa;
        }

        .brand-page .product-image {
            width: 100%;
            height: 220px;
            background: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 60px;
            color: #ccc;
        }

        .brand-page .product-card h4 {
            color: #2B11DB;
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: 700;
        }

        .brand-page .product-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            flex-grow: 1;
        }

        .product-badge {
            display: inline-block;
            background: #00d4aa;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .add-to-inquiry {
            background: #00D7B3;
            color: #2E2E2E;
            padding: 10px 20px;
            border-radius: 999px;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: inline-block;
            box-shadow: 0 6px 18px rgba(43,17,219,0.18);
        }

        .add-to-inquiry:active {
            transform: translateY(1px);
        }

        .brand-page .product-card > div:last-child {
            margin-top: auto;
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
            background: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-container {
            background: white;
            border-radius: 12px;
            max-width: 900px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
            position: relative;
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
            z-index: 2001;
        }

        .modal-close:hover {
            background: #f0f0f0;
            color: #333;
        }

        .modal-media {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            background: #f5f5f5;
        }

        .media-slider {
            position: relative;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
        }

        .media-item {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .media-item.active {
            opacity: 1;
            position: relative;
        }

        .media-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }

        .media-item iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
        }

        .media-controls {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
            align-items: center;
        }

        .media-nav-btn {
            background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 20px;
        }

        .media-nav-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(43,17,219,0.3);
        }

        .media-nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: scale(1);
        }

        .media-dots {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ddd;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .dot.active {
            background: #2B11DB;
        }

        .dot:hover {
            background: #2B11DB;
        }

        .modal-content {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .modal-content h2 {
            color: #2B11DB;
            font-size: 28px;
            margin: 0 0 10px 0;
            border: none;
        }

        .modal-content .model-type {
            color: #888;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .modal-specs {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .modal-specs h3 {
            color: #2B11DB;
            font-size: 18px;
            margin: 0 0 15px 0;
            border-bottom: 2px solid #2B11DB;
            padding-bottom: 10px;
        }

        .specs-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .specs-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e5e5e5;
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }

        .specs-list li:last-child {
            border-bottom: none;
        }

        .specs-list strong {
            color: #333;
            display: block;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: auto;
        }

        .modal-close-btn,
        .modal-inquiry-btn {
            flex: 1;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .modal-close-btn {
            background: #e5e5e5;
            color: #333;
        }

        .modal-close-btn:hover {
            background: #d0d0d0;
        }

        .modal-inquiry-btn {
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
            color: #1a1a2e;
            box-shadow: 0 4px 15px rgba(0, 217, 255, 0.3);
        }

        .modal-inquiry-btn:hover {
            background: linear-gradient(135deg, #00E6FF 0%, #00C8F7 100%);
            box-shadow: 0 6px 20px rgba(0, 217, 255, 0.5);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .modal-container {
                grid-template-columns: 1fr;
                padding: 20px;
                gap: 20px;
                max-height: 95vh;
            }

            .media-slider {
                height: 300px;
            }

            .modal-content h2 {
                font-size: 24px;
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
                padding: 25px;
            }

            .product-grid {
                justify-content: center;
            }

            .brand-page .product-card {
                flex: 0 1 calc(50% - 13px);
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
    
    // Fallback if empty for some reason
    if (empty($brands_info)) {
        $brands_info = [
        'Panasonic Connect' => [
            'description' => 'Leading manufacturer of welding robots and automated welding systems.',
            'products' => [
                ['model' => 'YD-350KR2', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-350KR2.jpg'],
                ['model' => 'YD-500KR2', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-500KR2.jpg'],
                ['model' => 'YD-600KH2', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-600KH2.jpg'],
                ['model' => 'YD-350RX1', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-350RX1.jpg'],
                ['model' => 'YD-350GR3', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-350GR3.jpeg'],
                ['model' => 'YD-350VR1', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-350VR1.jpg'],
                ['model' => 'YD-400VP1', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-400VP1.png'],
                ['model' => 'YD-350GZ4', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/CO2,MAG,MIG Welding Machine/YD-350GZ4.jpeg'],
                ['model' => 'YD-200BL3', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/TIG Welding Machine/YC-200BL3.jpeg'],
                ['model' => 'YD-300BZ3', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/TIG Welding Machine/YC-300BZ3.jpeg'],
                ['model' => 'YD-300BP4', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/TIG Welding Machine/YC-300BP4.png'],
                ['model' => 'YD-300WX4', 'type' => 'Welding Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/TIG Welding Machine/YC-300WX4.jpg'],
                ['model' => 'YP-060PF3', 'type' => 'Positioner', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic Positioner 1.webp'],
                ['model' => 'YP-080PF3', 'type' => 'Positioner', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic Positioner 2.webp'],
                ['model' => 'YP-130PF1', 'type' => 'Positioner', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic Positioner 3.jpg'],
                ['model' => 'TM/TL G3 Series', 'type' => 'Welding System', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic TAWERS WG3 Welding Robot.jpg'],
                ['model' => 'TM/TL TAWERS Series', 'type' => 'Welding System', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic TAWERS SAWP Welding Robot.jpg'],
                ['model' => 'Super Active TAWERS', 'type' => 'Welding System', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic TAWERS SAWP Welding Robot.jpg'],
                ['model' => 'TM/TL G4 Series', 'type' => 'Welding System', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic TAWERS WG4 Welding Robot.jpg'],
                ['model' => 'TM/TL TAWERS WG4 Series', 'type' => 'Welding System', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic TAWERS WG4 Welding Robot.jpg'],
                ['model' => 'Active TAWERS 4 AWP4-WG4 Series', 'type' => 'Welding System', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic TAWERS AWP4 Welding Robot - 1.png'],
                ['model' => 'Tig Welding Robot', 'type' => 'TIG Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic Tig Welding Robot 1.png'],
                ['model' => 'Plasma Cutting Robot', 'type' => 'Cutting Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic Plasma Cutting Robot 1.jpg'],
                ['model' => 'Handling Robot for Welding', 'type' => 'Handling Robot', 'badge' => '', 'image' => 'assets/brands%20items/PANASONIC/Arc Welding Robot/Panasonic G3 Welding Robot.jpg'],
                ['model' => 'DTPS 3', 'type' => 'Software/System', 'badge' => '', 'image' => ''],
                ['model' => 'VPRS', 'type' => 'Software/System', 'badge' => '', 'image' => ''],
                ['model' => 'iWNB', 'type' => 'Software/System', 'badge' => '', 'image' => ''],
                ['model' => 'i-Reporter', 'type' => 'Software/System', 'badge' => '', 'image' => '']
            ],
            
        ],
        'BW Technologies' => [
            'description' => 'A manufacturer of gas detection instrumentation intended to protect personnel and facilities around the world.',
            'products' => [
                ['model' => 'BW Clip', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW Clip 1.jpg'],
                ['model' => 'BW Solo', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW Solo 1.jpg'],
                ['model' => 'BW Clip 4 - Low Maintenance', 'type' => 'Gas Equipment', 'badge' => ' ', 'image' => 'assets/brands%20items/BW/BW Clip4 - 1.jpg'],
                ['model' => 'BW Microclip XL', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW MicroClip XL 1.jpg'],
                ['model' => 'BW Microclip X3', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW MicroClip X3 - 2.jpg'],
                ['model' => 'BW Max XT II', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW Max XT II 1.jpg'],
                ['model' => 'BW Icon', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW Icon 1.jpg'],
                ['model' => 'BW Flex', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW Flex 4 - 1.jpg'],
                ['model' => 'BW Ultra', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW Ultra 1.jpg'],
                ['model' => 'BW Rigrat', 'type' => 'Gas Equipment', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW RigRat 1.jpg'],
                ['model' => 'IntelliDoX', 'type' => 'System', 'badge' => '', 'image' => 'assets/brands%20items/BW/BW IntelliDoX 1.jpg'],
                ['model' => 'Calibration Gas', 'type' => 'Accessory', 'badge' => '', 'image' => 'assets/brands%20items/BW/calibration gas 1.jpg'],
                ['model' => 'Gas Regulator', 'type' => 'Accessory', 'badge' => '', 'image' => 'assets/brands%20items/BW/reg-df-1 gas regulator.jpg']
            ],
            'features' => [
                'Superior weld quality',
                'Wide range of applications',
                'Consistent performance',
                'Internationally certified'
            ]
        ],
        'Weldcraft' => [
            'description' => 'Professional TIG welding torches and accessories.',
            'products' => ['TIG Torches', 'Torch Bodies', 'Consumables', 'Accessories'],
            'features' => [
                'Ergonomic design',
                'Durable construction',
                'Easy maintenance',
                'Compatible with major brands'
            ]
        ],
        'Soyer' => [
            'description' => 'Professional welding equipment and accessories.',
            'products' => [
                ['model' => 'BMAS BN', 'type' => 'Welding Equipment', 'badge' => '', 'image' => 'assets/brands%20items/SOYER/BMS-8N.png'],
                ['model' => 'BMK - B', 'type' => 'Welding Equipment', 'badge' => '', 'image' => 'assets/brands%20items/SOYER/BMK-8i.png'],
                ['model' => 'BMK 12W2', 'type' => 'Welding Equipment', 'badge' => '', 'image' => 'assets/brands%20items/SOYER/BMK-12W.png']
            ]
        ],
        'Alfra' => [
            'description' => 'High-performance magnetic base core drills and annular cutters.',
            'products' => [
                ['model' => 'Alfra V-Line Rotabroach V 32 Low Profile Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/S-Line/RB_V32 - 1.jpg'],
                ['model' => 'Alfra B-Line Rotabroach RB 35/XE Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/B-Line/RB_35_B.jpg'],
                ['model' => 'Alfra B-Line Rotabroach RB 130 Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/B-Line/RB_130_B.jpg'],
                ['model' => 'Alfra RL-E Line Rotabroach RB 60 RL-E Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/RL-E-Line/RB60RL-E - 1.jpg'],
                ['model' => 'Alfra RL-E Line Rotabroach RB 100 RL-E Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/RL-E-Line/RB-100RL-E.jpg'],
                ['model' => 'Alfra Xcl Line Rotabroach RB 35 SP Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/SP-Line/RB_35_SP.jpg'],
                ['model' => 'Alfra X-Line Rotabroach RB 35 Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/B-Line/RB_35_B.jpg'],
                ['model' => 'Alfra X-Line Rotabroach RB 35 X Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/X-Line/RB35_50_X Piccolo.jpg'],
                ['model' => 'Alfra X-Line Rotabroach RB 50 X Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/X-Line/RB_50_X.jpg'],
                ['model' => 'Alfra X-Line Rotabroach RB 80 X Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/X-Line/RB_80_X.jpg'],
                ['model' => 'Alfra X-Line Rotabroach RB 35/50 X Magnetic Base Core Drill', 'type' => 'Core Drill', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/X-Line/RB35_50_X Piccolo.jpg'],
                ['model' => 'Alfra HSS CO Annular', 'type' => 'Annular Cutter', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/Annular Cutter/HSS Co - 1.jpg'],
                ['model' => 'Alfra TCT Annular', 'type' => 'Annular Cutter', 'badge' => '', 'image' => 'assets/brands%20items/ALFRA/Annular Cutter/TCT - 1.jpg']
            ]
        ],
        'ACES' => [
            'description' => 'Premium welding safety equipment and protective gear.',
            'products' => [
                ['model' => 'Aces 2118-1AF Clear', 'type' => 'Safety Equipment', 'badge' => '', 'image' => 'assets/brands%20items/ACES/ACES-2118-1AF.jpg'],
                ['model' => 'Aces 2118-4AF Gray', 'type' => 'Safety Equipment', 'badge' => '', 'image' => 'assets/brands%20items/ACES/ACES-2118-4AF%20GREY.jpg'],
                ['model' => 'Aces 250B', 'type' => 'Welding Helmet', 'badge' => '', 'image' => 'assets/brands%20items/ACES/A188.jpg'],
                ['model' => 'Aces A38B Poly-carbonate Clear Visor', 'type' => 'Visor', 'badge' => '', 'image' => 'assets/brands%20items/ACES/A388.jpg'],
                ['model' => 'Aces A366 Faceshield/Hard Hat Attachment', 'type' => 'Attachment', 'badge' => '', 'image' => 'assets/brands%20items/ACES/A366.jpg'],
                ['model' => 'Aces A28B Welding Helmet', 'type' => 'Welding Helmet', 'badge' => '', 'image' => 'assets/brands%20items/ACES/A288.jpg'],
                ['model' => 'Aces A2538 Welding Helmet w/ Mounting Adaptor', 'type' => 'Welding Helmet', 'badge' => '', 'image' => 'assets/brands%20items/ACES/A238.jpg'],
                ['model' => 'Aces Headgear w/ Polycarbonate Clear Visor', 'type' => 'Safety Equipment', 'badge' => '', 'image' => 'assets/brands%20items/ACES/A300.jpg']
            ]
        ],
        'UVEX' => [
            'description' => 'Professional eye protection and safety equipment.',
            'products' => [
                ['model' => 'UVEX i VO', 'type' => 'Safety Glasses', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/I-VO_9160.265 Clear.jpg'],
                ['model' => 'UVEX Ultra Vision', 'type' => 'Safety Glasses', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/Ultra Vision.jpg'],
                ['model' => 'UVEX Ultrasonic', 'type' => 'Safety Glasses', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/Ultrasonic.jpg'],
                ['model' => 'Uvex OTG', 'type' => 'Safety Glasses', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/OTG.jpg'],
                ['model' => 'Replacement Lens', 'type' => 'Accessory', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/I-VO_9160.520 Amber.jpg'],
                ['model' => 'Uvex KHI Helmet Earmuff', 'type' => 'Safety Equipment', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/K1H Helmet Earmuff.jpg'],
                ['model' => 'Uvex Whisper', 'type' => 'Safety Equipment', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/Whisper.jpg'],
                ['model' => 'Uvex X-Fit', 'type' => 'Safety Equipment', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/X-Fit.jpg'],
                ['model' => 'Uvex X-Fit w/ Cord', 'type' => 'Safety Equipment', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/X-Fit with cord.jpg'],
                ['model' => 'Uvex Slv-Air 2200', 'type' => 'Respirator', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/Silv-Air 2200.jpg'],
                ['model' => 'Uvex Slv-Air 2210', 'type' => 'Respirator', 'badge' => '', 'image' => 'assets/brands%20items/UVEX/Silv-Air 2210.jpg']
            ]
        ],
        'ANSELL' => [
            'description' => 'Premium protective gloves for industrial applications.',
            'products' => [
                ['model' => 'Alphatec-Solvex 37-185', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Solvex 37-185_1.jpg'],
                ['model' => 'Alphatec-Solvex 37-176', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Solvex 37-176_1.png'],
                ['model' => 'Hyflex 11-724', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Hyflex 11-724 - 1.jpg'],
                ['model' => 'Hyflex 11-735', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Hyflex 11-735 - 1.jpg'],
                ['model' => 'Edge 48-126', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Edge 48-126 - 1.png'],
                ['model' => 'Edge 48-128', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Edge 48-128 - 1.png'],
                ['model' => 'Edge 48-706', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Edge 48-706 - 1.jpg'],
                ['model' => 'Edge 82-133', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Edge_82-133_1.png'],
                ['model' => 'TouchNTuff 92-670', 'type' => 'Gloves', 'badge' => '', 'image' => 'assets/brands%20items/ANSELL/Touchntuff_92-670_1.png']
            ]
        ],
        'MICROGARD' => [
            'description' => 'High-quality protective clothing and coveralls.',
            'products' => [
                ['model' => 'Alphatec 1500 Plus Model 111', 'type' => 'Protective Suit', 'badge' => '', 'image' => 'assets/brands%20items/MICROGARD/Alphatec 1500 Plus 1.png'],
                ['model' => 'Alphatec 2000 Standard Model 111', 'type' => 'Protective Suit', 'badge' => '', 'image' => 'assets/brands%20items/MICROGARD/Alphatec 2000 Standard 1.png'],
                ['model' => 'Alphatec 2300 Plus Model 132', 'type' => 'Protective Suit', 'badge' => '', 'image' => 'assets/brands%20items/MICROGARD/Alphatec 2300 Plus 1.png'],
                ['model' => 'Alphatec 3000 Model 111', 'type' => 'Protective Suit', 'badge' => '', 'image' => 'assets/brands%20items/MICROGARD/Alphatec 3000 1.png'],
                ['model' => 'Alphatec 4000 Model 111', 'type' => 'Protective Suit', 'badge' => '', 'image' => 'assets/brands%20items/MICROGARD/Alphatec 4000 1.png'],
                ['model' => 'Alphatec 1500 FR Plus Model', 'type' => 'Protective Suit', 'badge' => '', 'image' => 'assets/brands%20items/MICROGARD/Alphatec 1500 Plus FR 1.png']
            ]
        ],
        'WELDAS' => [
            'description' => 'Professional welding safety equipment and protective gear.',
            'products' => [
                ['model' => 'Weldas 10-0160', 'type' => 'Welding Safety', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/10-0160 - 1.png'],
                ['model' => 'Weldas 10-2101', 'type' => 'Welding Safety', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/10-2101 - 1.jpg'],
                ['model' => 'Weldas 10-1023', 'type' => 'Welding Safety', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/10-1003 - 1.jpg'],
                ['model' => 'Weldas 10-1009', 'type' => 'Welding Safety', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/10-1009 - 1.jpg'],
                ['model' => 'Weldas 10-1206', 'type' => 'Welding Safety', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/10-1206 - 1.jpg'],
                ['model' => 'Weldas 10-2064', 'type' => 'Welding Safety', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/10-2064 - 1.jpg'],
                ['model' => 'Weldas Lava Shield Welding PVC Screen with Grommets', 'type' => 'Welding Screen', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/55-5466_Yellow Welding Screen.png'],
                ['model' => 'Weldas PYTHON®Axx Tig Torch Cable Cover', 'type' => 'Cable Cover', 'badge' => '', 'image' => 'assets/brands%20items/WELDAS/44-4022-1.png']
            ]
        ],
        'Safety Jogger' => [
            'description' => 'Premium safety footwear for industrial applications.',
            'products' => [
                ['model' => 'Safety Jogger Ceres', 'type' => 'Safety Shoes', 'badge' => '', 'image' => ''],
                ['model' => 'Safety Jogger Best Girl', 'type' => 'Safety Shoes', 'badge' => '', 'image' => ''],
                ['model' => 'Safety Jogger Best Run 251', 'type' => 'Safety Shoes', 'badge' => '', 'image' => ''],
                ['model' => 'Safety Jogger Advance', 'type' => 'Safety Shoes', 'badge' => '', 'image' => ''],
                ['model' => 'Safety Jogger Elevate', 'type' => 'Safety Shoes', 'badge' => '', 'image' => ''],
                ['model' => 'Safety Jogger Best Boy 2', 'type' => 'Safety Shoes', 'badge' => '', 'image' => ''],
                ['model' => 'Safety Jogger Dubai Black', 'type' => 'Safety Shoes', 'badge' => '', 'image' => ''],
                ['model' => 'Safety Jogger Mars', 'type' => 'Safety Shoes', 'badge' => '', 'image' => '']
            ]
        ],
        'TANAKA' => [
            'description' => 'Professional gas regulators and cutting equipment.',
            'products' => [
                ['model' => 'Tanaka Argon Flowmeter / Regulator F22AR with adapter', 'type' => 'Regulator', 'badge' => '', 'image' => 'assets/brands%20items/TANAKA/TANAKA F22AR - 1.jpg'],
                ['model' => 'Tanaka Straight Cutting Machine KT30X', 'type' => 'Cutting Machine', 'badge' => '', 'image' => 'assets/brands%20items/TANAKA/KT-5NX-1.jpg']
            ]
        ],
        'CHIYODA' => [
            'description' => 'Gas saving regulators and welding accessories.',
            'products' => [
                ['model' => 'Chiyoda Economical Gas Saving Regulator', 'type' => 'Regulator', 'badge' => '', 'image' => 'assets/brands%20items/CHIYODA/H-AR, H-CO2 Type.jpg']
            ]
        ],
        'HARDWORKER' => [
            'description' => 'Welding tools and accessories.',
            'products' => [
                ['model' => 'Hardworker MIG Welding Plier', 'type' => 'Welding Tool', 'badge' => '', 'image' => 'assets/brands%20items/HARDWORKER/MO-ZERO.8 - 1.jpg']
            ]
        ],
        'MAGNAFLUX' => [
            'description' => 'Non-destructive testing and inspection solutions.',
            'products' => [
                ['model' => 'Magnaflux Spotcheck Cleaner / Remover SKC-S', 'type' => 'Inspection', 'badge' => '', 'image' => 'assets/brands%20items/MAGNAFLUX/SKC-S.webp'],
                ['model' => 'Magnaflux Spotcheck Developer Aerosol SKD-S2', 'type' => 'Inspection', 'badge' => '', 'image' => 'assets/brands%20items/MAGNAFLUX/SKD-S2.jpg'],
                ['model' => 'Magnaflux Spotcheck Penetrant Aerosol SKL-SP2', 'type' => 'Inspection', 'badge' => '', 'image' => 'assets/brands%20items/MAGNAFLUX/SKL-SP2.webp'],
                ['model' => 'Magnaflux Prepared Bath Black Magnetic Ink Wet Method 7HF', 'type' => 'Inspection', 'badge' => '', 'image' => 'assets/brands%20items/MAGNAFLUX/7HF.jpg'],
                ['model' => 'Magnaflux Prepared Bath Fluorescent Magnetic Ink Dry Method 14AM', 'type' => 'Inspection', 'badge' => '', 'image' => 'assets/brands%20items/MAGNAFLUX/14HF.png']
            ]
        ],
        'COPPUS' => [
            'description' => 'Industrial ventilation and air movement equipment.',
            'products' => [
                ['model' => 'Coppus® MEB 12 Blower', 'type' => 'Ventilator', 'badge' => '', 'image' => 'assets/brands%20items/COPPUS/Air-Max 12.png'],
                ['model' => 'Turbo Vaneaxial and Centrifugal Ventilators', 'type' => 'Ventilator', 'badge' => '', 'image' => 'assets/brands%20items/COPPUS/Reaction Fan-1.jpg'],
                ['model' => 'Explosion Fan', 'type' => 'Ventilator', 'badge' => '', 'image' => 'assets/brands%20items/COPPUS/Jectair.png'],
                ['model' => 'Double Duty Heat Killer', 'type' => 'Ventilator', 'badge' => '', 'image' => 'assets/brands%20items/COPPUS/Double-Duty-Heat-Killer-1.png'],
                ['model' => 'Nectar and Hornet HP', 'type' => 'Ventilator', 'badge' => '', 'image' => 'assets/brands%20items/COPPUS/Cadet.png'],
                ['model' => 'Vano 175 CV and 250 CV', 'type' => 'Ventilator', 'badge' => '', 'image' => 'assets/brands%20items/COPPUS/Vano.png']
            ]
        ],
        'BOSCH' => [
            'description' => 'Professional power tools for industrial applications.',
            'products' => [
                ['model' => 'Bosch GWS 9-100P Angle Grinder', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GWS 2200-180 Angle Grinder', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GGS 3000L Straight Grinder', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GCO 14-24J Metal Cut Off Saw', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GKS 235 Turbo Hand Held Circular Saw', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GST 80PBE Jig Saw', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GDS 250 Cordless Impact Wrench', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GSR 120-LI Cordless Drill', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GSH 5 Demolition Hammer with SDS Max', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GSR 18V-50 Cordless Drill/Driver GSR 18V-50', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GBH 4-32DFR Rotary Hammer with SDS Plus', 'type' => 'Power Tool', 'badge' => '', 'image' => ''],
                ['model' => 'Bosch GSH 16-30 Demolition Jack Hammer', 'type' => 'Power Tool', 'badge' => '', 'image' => '']
            ]
        ],
        'MOTOLITE' => [
            'description' => 'Automotive and industrial batteries.',
            'products' => [
                ['model' => 'Gold', 'type' => 'Battery', 'badge' => '', 'image' => 'assets/brands%20items/MOTOLITE/Gold.jpg'],
                ['model' => 'Silver', 'type' => 'Battery', 'badge' => '', 'image' => 'assets/brands%20items/MOTOLITE/excel.png'],
                ['model' => 'Enduro', 'type' => 'Battery', 'badge' => '', 'image' => 'assets/brands%20items/MOTOLITE/Enduro.jpg'],
                ['model' => 'TruckMaster', 'type' => 'Battery', 'badge' => '', 'image' => 'assets/brands%20items/MOTOLITE/Truckmaster.png']
            ]
        ],
        // Add more brands as needed
        ];
    }
    
    // Get brand info or use defaults
    $brand_info = isset($brands_info[$brand_name]) ? $brands_info[$brand_name] : [
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
                <a href="javascript:history.back()" class="inquiry-btn" style="margin-right: 12px;">BACK</a>
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
                <button id="browseToggle" class="browse-toggle"><span class="hamburger"><i class="bi bi-list"></i></span> BROWSE PRODUCTS</button>
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
                                <li><a href="brand.php?name=Kobelco"><img src="assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                                <li><a href="brand.php?name=Metrode"><img src="assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                                <li><a href="brand.php?name=DryRod.%20II"><img src="assets/brands/DRYROD.jpg" alt="DryRod. II" title="DryRod. II"></a></li>
                                <li><a href="brand.php?name=Weldcraft"><img src="assets/brands/WELDCRAFT.jpg" alt="Weldcraft" title="Weldcraft"></a></li>
                                <li><a href="brand.php?name=Truweld"><img src="assets/brands/TRUWELD.jpg" alt="Truweld" title="Truweld"></a></li>
                                <li><a href="brand.php?name=Arcair"><img src="assets/brands/ARCAIR.jpg" alt="Arcair" title="Arcair"></a></li>
                                <li><a href="brand.php?name=Magnaflux"><img src="assets/brands/MAGNAFLUX.jpg" alt="Magnaflux" title="Magnaflux"></a></li>
                                <li><a href="brand.php?name=Tempilstik"><img src="assets/brands/TEMPILSTIK.jpg" alt="Tempilstik" title="Tempilstik"></a></li>
                                <li><a href="brand.php?name=Tanaka"><img src="assets/brands/TANAKA.jpg" alt="Tanaka" title="Tanaka"></a></li>
                                <li><a href="brand.php?name=Chiyoda"><img src="assets/brands/CHIYODA.jpg" alt="Chiyoda" title="Chiyoda"></a></li>
                                <li><a href="brand.php?name=Yutaka"><img src="assets/brands/YUTAKA.jpg" alt="Yutaka" title="Yutaka"></a></li>
                                <li><a href="brand.php?name=Hard%20Workers"><img src="assets/brands/HARDWORKER.jpg" alt="Hard Workers" title="Hard Workers"></a></li>
                                <li><a href="brand.php?name=Soyer"><img src="assets/brands/SOYER.jpg" alt="Soyer" title="Soyer"></a></li>
                                <li><a href="brand.php?name=Aquasol"><img src="assets/brands/AQUASOL.jpg" alt="Aquasol" title="Aquasol"></a></li>
                                <li><a href="brand.php?name=SK%20And%20GAL%20GAGE"><img src="assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE" title="SK And GAL GAGE"></a></li>
                                <li><a href="brand.php?name=Coppus"><img src="assets/brands/COPPUS.jpg" alt="Coppus" title="Coppus"></a></li>
                                <li><a href="brand.php?name=BW%20Technologies"><img src="assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies" title="BW Technologies"></a></li>
                                <li><a href="brand.php?name=RAC"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAC" title="RAC"></a></li>
                                <li><a href="brand.php?name=Weldas"><img src="assets/brands/WELDAS.jpg" alt="Weldas" title="Weldas"></a></li>
                                <li><a href="brand.php?name=Uvex"><img src="assets/brands/UVEX.jpg" alt="Uvex" title="Uvex"></a></li>
                                <li><a href="brand.php?name=Aces"><img src="assets/brands/ACES.jpg" alt="Aces" title="Aces"></a></li>
                                <li><a href="brand.php?name=Microgard"><img src="assets/brands/MICROGARD.jpg" alt="Microgard" title="Microgard"></a></li>
                                <li><a href="brand.php?name=Ansell"><img src="assets/brands/ANSELL.jpg" alt="Ansell" title="Ansell"></a></li>
                                <li><a href="brand.php?name=Alfra"><img src="assets/brands/ALFRA.jpg" alt="Alfra" title="Alfra"></a></li>
                                <li><a href="brand.php?name=Bosch"><img src="assets/brands/BOSCH.jpg" alt="Bosch" title="Bosch"></a></li>
                                <li><a href="brand.php?name=Makita"><img src="assets/brands/MAKITA.jpg" alt="Makita" title="Makita"></a></li>
                                <li><a href="brand.php?name=Weller"><img src="assets/brands/WEILER.jpg" alt="Weller" title="Weller"></a></li>
                                <li><a href="brand.php?name=Garryson"><img src="assets/brands/GARRYSON.jpg" alt="Garryson" title="Garryson"></a></li>
                                <li><a href="brand.php?name=Spilfyter"><img src="assets/brands/SPILFYTER.jpg" alt="Spilfyter" title="Spilfyter"></a></li>
                                <li><a href="brand.php?name=Dalo"><img src="assets/brands/DALO.jpg" alt="Dalo" title="Dalo"></a></li>
                                <li><a href="brand.php?name=Motolite"><img src="assets/brands/MOTOLITE.jpg" alt="Motolite" title="Motolite"></a></li>
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
    <div id="overlay" class="overlay-backdrop" aria-hidden="true"></div>
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
                    <li><a href="arc-welding-machine/co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                </ul>
            </li>
            <li>
                <a href="#arc-handmetal-robots"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc HandMetal Robots</span><span class="sidebar-arrow"><i class="bi bi-chevron-right"></i></span></a>
            </li>
            <li>
                <a href="#batteries"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span><span class="sidebar-arrow"><i class="bi bi-chevron-right"></i></span></a>
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
            <li class="has-sub">
                <a href="portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-ventilators" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-ventilators" class="sidebar-sublist collapsed">
                    <li><a href="portable-ventilators/portable-ventilator-accessories.php">Portable Ventilator Accessories</a></li>
                </ul>
            </li>
            <li>
                <a href="#power-tools"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span><span class="sidebar-arrow"><i class="bi bi-chevron-right"></i></span></a>
            </li>
            <li class="has-sub">
                <a href="protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Protection and Safety</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-protection-safety" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="protection/eye-protection.php">Eye Protection</a></li>
                    <li><a href="protection/foot-protection.php">Foot Protection</a></li>
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
                    <li><a href="welding-accessories/welding-head-face-protection.php">Welding, Head & Face Protection</a></li>
                </ul>
            </li>
            <li>
                <a href="#handmetal-consumables"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">HandMetal Consumables</span><span class="sidebar-arrow"><i class="bi bi-chevron-right"></i></span></a>
            </li>
        </ul>
    </aside>

    <div class="brand-container">
        <div class="brand-header">
            <div class="brand-logo-container">
                <img src="assets/brands/<?php echo htmlspecialchars(urlencode($logo_filename($brand_name))); ?>.jpg" alt="<?php echo $brand_name; ?>" class="brand-logo" onerror="console.log('Logo failed:', this.src); this.style.opacity='0.5';">
                <h1><?php echo $brand_name; ?></h1>
            </div>
            <p><?php echo $brand_info['description']; ?></p>
        </div>

        <!-- Brand Content -->
        <div class="brand-content">
            <h2>Product Range</h2>
            <div class="product-grid">
                <?php foreach($brand_info['products'] as $product): ?>
                    <?php
                        $product_model_value = is_array($product) ? (string)($product['model'] ?? '') : (string)$product;
                        $is_highlight = $highlight_product !== '' && strcasecmp($product_model_value, $highlight_product) === 0;
                    ?>
                    <div class="product-card<?php echo $is_highlight ? ' product-highlight' : ''; ?>"<?php echo $is_highlight ? ' id="highlight-product"' : ''; ?> data-model="<?php echo htmlspecialchars($product_model_value, ENT_QUOTES); ?>">
                        <div class="product-image">
                            <?php if(is_array($product) && !empty($product['image'])): ?>
                                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['model']; ?>" style="width: 100%; height: 100%; object-fit: contain;">
                            <?php else: ?>
                                <img src="assets/brands%20items/PANASONIC/Arc Welding Robot/robot-placeholder.jpg" alt="Product" style="width: 100%; height: 100%; object-fit: contain;" onerror="this.style.display='none'; this.parentElement.innerHTML='🤖';">
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
                        <div style="margin-top:auto; padding-top:15px;">
                            <button class="add-to-inquiry" type="button"
                                data-model="<?php echo htmlspecialchars(is_array($product) ? $product['model'] : $product, ENT_QUOTES); ?>"
                                data-type="<?php echo htmlspecialchars(is_array($product) ? (isset($product['type']) ? $product['type'] : '') : 'Product', ENT_QUOTES); ?>"
                                data-brand="<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>"
                            >ADD TO INQUIRY LIST</button>
                        </div>
                    </div>
                <?php endforeach; ?>
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
            </div>

            <div class="modal-content">
                <h2 id="modalProductName"></h2>
                <p class="model-type" id="modalProductType"></p>

                <div class="modal-specs">
                    <h3>Product Specifications</h3>
                    <ul class="specs-list" id="modalSpecs">
                        <li>No specifications available</li>
                    </ul>
                </div>

                <div class="modal-actions">
                    <button class="modal-close-btn" id="modalCloseBtn">CLOSE</button>
                    <button class="modal-inquiry-btn" id="modalInquiryBtn">ADD TO INQUIRY LIST</button>
                </div>
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
                &copy; 2026 <?php echo $company_name; ?>. All rights reserved.
            </div>
        </div>
    </footer>
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
                        
                        // Find product data
                        var productKey = '<?php echo strtolower(str_replace(' ', '-', $brand_name)); ?>';
                        var productSpecs = (productsData[productKey] || productsData['default']).specs;
                        
                        // Populate modal
                        document.getElementById('modalProductName').textContent = model;
                        document.getElementById('modalProductType').textContent = type;
                        
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
                        
                        // Update media dots and buttons
                        updateMediaDots(0, mediaItems.length);
                        updateMediaNavButtons(0, mediaItems.length);
                        currentMediaIndex = 0;
                        
                        // Store current product for inquiry button
                        currentProduct = {
                            model: model,
                            type: type,
                            brand: '<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>'
                        };
                        
                        // Show modal
                        modal.classList.add('active');
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

                // Close modal
                var closeModal = function() {
                    modal.classList.remove('active');
                    currentMediaIndex = 0;
                };

                modalClose.addEventListener('click', closeModal);
                modalCloseBtn.addEventListener('click', closeModal);
                modal.addEventListener('click', function(e) {
                    if(e.target === modal) closeModal();
                });

                // Navigation buttons
                document.getElementById('prevMedia').addEventListener('click', function() {
                    var mediaItems = document.querySelectorAll('.media-item');
                    currentMediaIndex = Math.max(0, currentMediaIndex - 1);
                    showMediaItem(currentMediaIndex, Array.from(mediaItems));
                    updateMediaDots(currentMediaIndex, mediaItems.length);
                    updateMediaNavButtons(currentMediaIndex, mediaItems.length);
                });

                document.getElementById('nextMedia').addEventListener('click', function() {
                    var mediaItems = document.querySelectorAll('.media-item');
                    currentMediaIndex = Math.min(mediaItems.length - 1, currentMediaIndex + 1);
                    showMediaItem(currentMediaIndex, Array.from(mediaItems));
                    updateMediaDots(currentMediaIndex, mediaItems.length);
                    updateMediaNavButtons(currentMediaIndex, mediaItems.length);
                });

                // Inquiry button in modal
                document.getElementById('modalInquiryBtn').addEventListener('click', function() {
                    if(currentProduct) {
                        var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                        var found = items.find(function(i) { return i.model === currentProduct.model && i.brand === currentProduct.brand; });
                        if(!found) {
                            currentProduct.qty = 1;
                            items.push(currentProduct);
                            localStorage.setItem('inquiryItems', JSON.stringify(items));
                            this.textContent = 'Added ✓';
                            setTimeout(function() { document.getElementById('modalInquiryBtn').textContent = 'ADD TO INQUIRY LIST'; }, 1200);
                        } else {
                            this.textContent = 'Already Added ✓';
                            setTimeout(function() { document.getElementById('modalInquiryBtn').textContent = 'ADD TO INQUIRY LIST'; }, 1200);
                        }
                    }
                });
            })();
        })();
    </script>
    <script>
        // Product highlighting and scrolling
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
</body>
</html>
