<?php
require_once __DIR__ . '/Andison/includes/analytics.php';
andison_track_visit('services');
require_once __DIR__ . '/Andison/includes/home_featured.php';
require_once __DIR__ . '/Andison/includes/home_slider.php';
require_once __DIR__ . '/Andison/includes/youtube_links.php';

$featured = andison_get_home_featured();
$slides = andison_get_home_slider();
$ytLinks = andison_get_youtube_links();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ANDISON INDUSTRIAL</title>
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
            margin: 0 0 0 20px;
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
            margin-left: 12px;
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
            padding: 0 20px; /* space for the left Browse toggle */
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
            line-height: 1.6;
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

        .nav-list > li > a:hover::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
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
            background: linear-gradient(90deg, #1565C0 0%, #00BCD4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }
    
        .section-description {
            text-align: center;
            max-width: 750px;
            margin: 0 auto 60px;
            color: #8B4513;
            line-height: 1.9;
            width: 100%;
            box-sizing: border-box;
            padding: 0 20px;
            font-size: 15px;
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

        .product-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e8eef7;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.4s ease;
        }

        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(43, 17, 219, 0.15);
        }

        .product-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 320px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
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
            transition: background 0.1s;
        }

        .play-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        .product-info {
            padding: 28px 24px;
            background: white;
            width: 100%;
            box-sizing: border-box;
            border-top: 1px solid #f0f0f0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-info h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
            color: #2B11DB;
            line-height: 1.4;
        }

        .product-info p {
            font-size: 15px;
            color: #666;
            line-height: 1.7;
            margin: 0;
        }

        /* Service Cards - Old Layout */
        .services-grid {
            display: flex;
            flex-direction: column;
            gap: 28px;
            width: 100%;
            max-width: 1050px;
        }

        .service-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            background: white;
            border-radius: 16px;
            padding: 48px 44px;
            border: 1px solid #E0E3FF;
            box-shadow: 0 4px 16px rgba(30, 136, 229, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(30, 136, 229, 0.15), 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .service-card.reverse {
            direction: rtl;
        }

        .service-card.reverse > * {
            direction: ltr;
        }

        .service-badge {
            display: inline-block;
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 14px;
            box-shadow: 0 4px 12px rgba(30, 136, 229, 0.25);
        }

        .service-card.teal .service-badge {
            background: linear-gradient(135deg, #00bcd4 0%, #00897b 100%);
            box-shadow: 0 4px 12px rgba(0, 188, 212, 0.25);
        }

        .service-content h3 {
            font-size: 26px;
            font-weight: 800;
            color: #1e88e5;
            margin-bottom: 18px;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        .service-card.teal .service-content h3 {
            color: #00bcd4;
        }

        .service-content p {
            font-size: 14px;
            color: #8B4513;
            line-height: 1.85;
            margin: 0;
        }

        .service-icon-box {
            width: 100%;
            aspect-ratio: 4 / 3;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e88e5 0%, #00bcd4 100%);
            font-size: 68px;
            color: white;
            box-shadow: 0 8px 24px rgba(30, 136, 229, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .service-card.teal .service-icon-box {
            background: linear-gradient(135deg, #00bcd4 0%, #00897b 100%);
            box-shadow: 0 8px 24px rgba(0, 188, 212, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
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

            .search-bar .search-field i {
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
                display: flex;
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
                min-height: 260px;
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

            .hero-slide {
                width: 92% !important;
                left: 50% !important;
                transform: translateX(-50%) scale(1) !important;
                filter: blur(0) !important;
                opacity: 0 !important;
            }

            .hero-slide.active {
                width: 92% !important;
                left: 50% !important;
                transform: translateX(-50%) scale(1) !important;
                filter: blur(0) !important;
                opacity: 1 !important;
            }

            .hero-slide.prev,
            .hero-slide.next {
                opacity: 0 !important;
                pointer-events: none;
            }

            .hero-thumb {
                width: 100%;
                height: auto;
                max-width: 100%;
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

            .services-grid {
                gap: 24px;
            }

            .service-card {
                grid-template-columns: 1fr;
                gap: 24px;
                padding: 24px;
            }

            .service-card.reverse {
                direction: ltr;
            }

            .service-badge {
                margin-bottom: 8px;
                font-size: 11px;
            }

            .service-content h3 {
                font-size: 20px;
                margin-bottom: 12px;
            }

            .service-content p {
                font-size: 14px;
                line-height: 1.7;
            }

            .service-icon-box {
                aspect-ratio: 1 / 1;
                font-size: 48px;
            }

            section h2 {
                font-size: 28px;
            }

            .section-description {
                font-size: 14px;
                margin-bottom: 28px;
            }

            /* About Us page — mobile */
            body {
                padding-top: 115px;
            }
            .page-content {
                overflow-x: hidden;
            }
            .about-hero {
                padding: 40px 20px 36px;
            }
            .about-hero h1 {
                font-size: 26px;
                letter-spacing: 0;
                margin-bottom: 10px;
            }
            .about-hero p {
                font-size: 14px;
            }
            .about-hero .hero-tagline {
                font-size: 13px;
                margin-top: 4px;
            }
            .about-building-wrap {
                padding: 28px 12px 12px;
            }
            .about-building-inner {
                max-width: 100%;
                border-radius: 12px;
            }
            .about-company-section {
                padding: 32px 16px 40px;
            }
            .about-company-card {
                padding: 22px 18px;
                border-radius: 10px;
            }
            .about-company-card p {
                font-size: 13.5px;
                line-height: 1.75;
            }
            .about-section-title {
                font-size: 24px;
                margin-bottom: 20px;
            }
            .about-mvv-section {
                padding: 32px 16px 40px;
            }
            .about-mvv-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-top: 24px;
            }
            .about-mvv-body {
                padding: 18px 18px 22px;
            }
            .about-mvv-body h3 {
                font-size: 16px;
            }
            .about-mvv-body p {
                font-size: 13px;
            }
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

        /* ===== ABOUT US PAGE STYLES ===== */
        .about-hero {
            background: linear-gradient(135deg, #2B11DB 0%, #1a0da3 100%);
            color: white;
            text-align: center;
            padding: 80px 20px 70px;
            position: relative;
            overflow: hidden;
        }
        .about-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 70% 30%, rgba(0,215,179,0.18) 0%, transparent 60%);
            pointer-events: none;
        }

        .about-hero h1 {
            font-size: 46px;
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        .about-hero p {
            font-size: 17px;
            color: rgba(255,255,255,0.88);
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        .about-hero .hero-tagline {
            font-size: 16px;
            font-weight: 600;
            color: #00D7B3;
            margin-top: 6px;
            position: relative;
            z-index: 1;
        }

        .about-building-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px 20px;
            background: white;
        }

        .about-building-inner {
            position: relative;
            max-width: 650px;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(43,17,219,0.13);
        }

        .about-section-title {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 32px;
            color: #222;
            position: relative;
        }

        .about-company-section {
            padding: 60px 20px;
            background: white;
        }

        .about-company-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 28px;
            margin-top: 32px;
        }

        .about-company-card {
            padding: 28px 24px;
            background: #f8f9ff;
            border-radius: 12px;
            border-left: 4px solid #2B11DB;
            transition: all 0.3s ease;
        }

        .about-company-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 32px rgba(43, 17, 219, 0.15);
        }

        .about-company-card p {
            font-size: 15px;
            line-height: 1.8;
            color: #5a6b7d;
            margin: 0;
        }

        .about-mvv-section {
            padding: 80px 20px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f3ff 100%);
        }

        .about-mvv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 32px;
            margin-top: 40px;
        }

        .about-mvv-card {
            padding: 36px 28px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .about-mvv-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 16px 40px rgba(43, 17, 219, 0.15);
        }

        .about-mvv-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 20px;
        }

        .about-mvv-icon.blue { background: linear-gradient(135deg, #2B11DB 0%, #4a2ff7 100%); }
        .about-mvv-icon.teal { background: linear-gradient(135deg, #00D7B3 0%, #00897b 100%); }

        .about-mvv-body h3 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #222;
        }

        .about-mvv-body p {
            font-size: 15px;
            line-height: 1.8;
            color: #5a6b7d;
            margin: 0;
        }

        /* ===== ABOUT US PAGE STYLES ===== */
        .about-hero {
            background: linear-gradient(135deg, #2B11DB 0%, #1a0da3 100%);
            color: white;
            text-align: center;
            padding: 80px 20px 70px;
            position: relative;
            overflow: hidden;
        }
        .about-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 70% 30%, rgba(0,215,179,0.18) 0%, transparent 60%);
            pointer-events: none;
        }
        .about-hero h1 {
            font-size: 46px;
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 1;
        }
        .about-hero p {
            font-size: 17px;
            color: rgba(255,255,255,0.88);
            margin-bottom: 0;
            position: relative;
            z-index: 1;
        }
        .about-hero .hero-tagline {
            font-size: 16px;
            font-weight: 600;
            color: #00D7B3;
            margin-top: 6px;
            position: relative;
            z-index: 1;
        }
        .about-building-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 60px 20px 20px;
            background: white;
        }
        .about-building-inner {
            position: relative;
            max-width: 650px;
            width: 100%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(43,17,219,0.13);
        }
        .about-building-inner::before,
        .about-building-inner::after {
            content: '';
            position: absolute;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 3px solid #2B11DB;
            opacity: 0.35;
            z-index: 2;
        }
        .about-building-inner::before { top: 14px; left: 14px; border-right: none; border-bottom: none; }
        .about-building-inner::after  { bottom: 14px; right: 14px; border-left: none; border-top: none; }
        .about-building-inner img {
            width: 100%;
            height: auto;
            display: block;
        }
        /* Company Overview */
        .about-company-section {
            background: white;
            padding: 50px 20px 60px;
            display: flex;
            justify-content: center;
        }
        .about-company-inner {
            max-width: 820px;
            width: 100%;
        }
        .about-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .about-eyebrow-bar {
            width: 4px;
            height: 22px;
            background: #2B11DB;
            border-radius: 2px;
            flex-shrink: 0;
        }
        .about-eyebrow-text {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #2B11DB;
        }
        .about-section-title {
            font-size: 36px;
            font-weight: 800;
            color: #2B11DB;
            margin-bottom: 28px;
            letter-spacing: -0.3px;
        }
        .about-company-card {
            background: #f8faff;
            border-radius: 14px;
            padding: 36px 40px;
            border: 1px solid #e4e9f8;
            box-shadow: 0 4px 18px rgba(43,17,219,0.06);
        }
        .about-company-card p {
            font-size: 14.5px;
            line-height: 1.85;
            color: #444;
            margin-bottom: 18px;
        }
        .about-company-card p:last-child { margin-bottom: 0; }
        .about-company-card strong { color: #2B11DB; font-weight: 700; }
        .about-inline-link {
            color: #00897b;
            font-weight: 700;
            text-decoration: underline;
            text-underline-offset: 2px;
        }
        .about-highlight-pill {
            display: inline-block;
            background: #EEF1FF;
            color: #2B11DB;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 13.5px;
        }
        /* Mission Vision Values */
        .about-mvv-section {
            background: #ffffff;
            padding: 60px 20px 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .about-mvv-inner {
            max-width: 820px;
            width: 100%;
        }
        .about-mvv-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            margin-top: 36px;
        }
        @media (max-width: 640px) {
            .about-mvv-grid { grid-template-columns: 1fr; }
        }
        .about-mvv-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e4e9f8;
            box-shadow: 0 4px 18px rgba(43,17,219,0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .about-mvv-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(43,17,219,0.14);
        }
        .about-mvv-img {
            width: 100%;
            aspect-ratio: 16/9;
            object-fit: cover;
            display: block;
            filter: saturate(1.1);
        }
        .about-mvv-img-overlay {
            position: relative;
        }
        .about-mvv-img-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(43,17,219,0.18) 0%, rgba(0,215,179,0.22) 100%);
            pointer-events: none;
        }
        .about-mvv-body {
            padding: 22px 24px 26px;
        }
        .about-mvv-icon-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        .about-mvv-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            flex-shrink: 0;
        }
        .about-mvv-icon.blue { background: linear-gradient(135deg, #2B11DB 0%, #4a2ff7 100%); }
        .about-mvv-icon.teal { background: linear-gradient(135deg, #00D7B3 0%, #00897b 100%); }
        .about-mvv-body h3 {
            font-size: 18px;
            font-weight: 800;
            color: #1a1a2e;
        }
        .about-mvv-body p {
            font-size: 13.5px;
            line-height: 1.8;
            color: #555;
            margin: 0;
        }
    </style>
</head>
<body>
        <?php
        // Set page title
        $page_title = "Services";
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
                    <i class="bi bi-search"></i>
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
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
                        <a href="aboutus.php" class="active">About Us</a>
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
                                <li><a href="brand.php?name=Robot%20Systems"><img src="assets/brands/ROBOT SYSTEMS.png" alt="Robot Systems Peripherals" title="Robot Systems Peripherals"></a></li>
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
                                <li><a href="brand.php?name=RAE"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAE Systems" title="RAE Systems"></a></li>
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
                                <li><a href="industries.php#motor-vehicle">Motor Vehicle Industry</a></li>
                                <li><a href="industries.php#metal-fabrication">Metal Fabrication and Industrial</a></li>
                                <li><a href="industries.php#power-generation">Power Generation</a></li>
                                <li><a href="industries.php#oil-petrochemical">Oil and Petrochemical Industry</a></li>
                                <li><a href="industries.php#mining">Mining Industry</a></li>
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

    <!-- Sidebar Navigation (via include) -->
    <?php include __DIR__ . '/includes/sidebar.php'; ?>



    <!-- About Us Content -->




    <!-- About Us Content -->
    <div class="page-content">

    <!-- Hero Banner -->
    <div class="about-hero">
        <h1>About Andison Industrial</h1>
        <p>Leading provider of high-quality industrial solutions and equipment</p>
        <p class="hero-tagline">serving the Philippines for over two decades</p>
    </div>

    <!-- Building Photo -->
    <div class="about-building-wrap">
        <div class="about-building-inner">
            <img src="assets/about us/Andison Manila Picture - Edited.jpg" alt="Andison Industrial Sales Inc. — Head Office" loading="lazy" />
        </div>
    </div>

    <!-- Our Company -->
    <div class="about-company-section" id="mission">
        <div class="about-company-inner">
            <div class="about-eyebrow">
                <div class="about-eyebrow-bar"></div>
                <span class="about-eyebrow-text">About Our Business</span>
            </div>
            <h2 class="about-section-title">Our Company</h2>
            <div class="about-company-card">
                <p>
                    <strong>Andison Industrial Sales Inc.</strong> stands as a significant industrial supplier for leading companies across the Philippines. Strategically situated amidst the expansive industrial landscape south of Metro Manila, Andison serves multi-national and export giants within
                    <a class="about-inline-link" href="industries.php">automotive and motorcycle assembly factories, power generation, oil refineries</a>,
                    petrochemical plants, metal fabrications, mining operations, shipyards, and other top contractors.
                </p>
                <p>
                    With specialized knowledge, Andison embraces the evolution of technology and consistently adopts new trends. We offer various solutions to our clientele by providing
                    <span class="about-highlight-pill">high-quality products</span>,
                    technical solutions, comprehensive support, and export services to meet the evolving needs of our clients.
                </p>
                <p>
                    Today, as representatives of various world-class brands, Andison has one of the industry's broadest portfolios of products, including
                    <span class="about-highlight-pill">Robotic &amp; Automated Welding Systems, Cutting Machines, Industrial Equipment, Tools &amp; Supplies, Gas Detection Devices, Safety Products, and PPE</span>
                    solutions.
                </p>
            </div>
        </div>
    </div>

    <!-- Mission, Vision & Values -->
    <div class="about-mvv-section" id="history">
        <div class="about-mvv-inner">
            <div class="about-eyebrow">
                <div class="about-eyebrow-bar"></div>
                <span class="about-eyebrow-text">Our Core</span>
            </div>
            <h2 class="about-section-title">Mission, Vision &amp; Values</h2>
            <div class="about-mvv-grid">
                <!-- Our Mission -->
                <div class="about-mvv-card">
                    <div class="about-mvv-img-overlay">
                        <img class="about-mvv-img" src="assets/about us/Welding Machines.JPG" alt="Our Mission — Welding Machines" loading="lazy" />
                    </div>
                    <div class="about-mvv-body">
                        <div class="about-mvv-icon-row">
                            <div class="about-mvv-icon blue"><i class="bi bi-bullseye"></i></div>
                            <h3>Our Mission</h3>
                        </div>
                        <p>To deliver innovative solutions and high-quality products to businesses across the Philippines at cost-effective prices while cultivating lasting relationships with our industrial clients.</p>
                    </div>
                </div>
                <!-- Our Vision -->
                <div class="about-mvv-card">
                    <div class="about-mvv-img-overlay">
                        <img class="about-mvv-img" src="assets/about us/Welding Robots.JPG" alt="Our Vision — Welding Robots" loading="lazy" />
                    </div>
                    <div class="about-mvv-body">
                        <div class="about-mvv-icon-row">
                            <div class="about-mvv-icon teal"><i class="bi bi-eye"></i></div>
                            <h3>Our Vision</h3>
                        </div>
                        <p>To be the premier supplier of industrial solutions in the Philippines, contributing significantly to national industrialization and being the trusted partner for manufacturing excellence.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Brands Carousel Slider -->
    <section class="brands-carousel-section">
        <div class="brands-carousel-heading">Our Trusted Brands</div>
        <div class="brands-carousel-outer">
            <button class="brands-carousel-btn brands-carousel-prev" id="brandsPrev" aria-label="Previous">
                <i class="bi bi-chevron-left"></i>
            </button>
            <div class="brands-carousel-viewport" id="brandsViewport">
                <div class="brands-carousel-track" id="brandsTrack">
                    <a href="brand.php?name=Panasonic%20Connect" class="brands-carousel-item"><img src="assets/brands/PANASONIC.jpg" alt="Panasonic Connect"></a>
                    <a href="brand.php?name=Robot%20Systems" class="brands-carousel-item"><img src="assets/brands/ROBOT SYSTEMS.png" alt="Robot Systems"></a>
                    <a href="brand.php?name=Kobelco" class="brands-carousel-item"><img src="assets/brands/KOBELCO.jpg" alt="Kobelco"></a>
                    <a href="brand.php?name=Metrode" class="brands-carousel-item"><img src="assets/brands/METRODE.jpg" alt="Metrode"></a>
                    <a href="brand.php?name=DryRod.%20II" class="brands-carousel-item"><img src="assets/brands/DRYROD.jpg" alt="DryRod. II"></a>
                    <a href="brand.php?name=Weldcraft" class="brands-carousel-item"><img src="assets/brands/WELDCRAFT.png" alt="Weldcraft"></a>
                    <a href="brand.php?name=Truweld" class="brands-carousel-item"><img src="assets/brands/TRUWELD.jpg" alt="Truweld"></a>
                    <a href="brand.php?name=Arcair" class="brands-carousel-item"><img src="assets/brands/ARCAIR.jpg" alt="Arcair"></a>
                    <a href="brand.php?name=MAGNAFLUX" class="brands-carousel-item"><img src="assets/brands/MAGNAFLUX.jpg" alt="Magnaflux"></a>
                    <a href="brand.php?name=Tempilstik" class="brands-carousel-item"><img src="assets/brands/TEMPILSTIK.jpg" alt="Tempilstik"></a>
                    <a href="brand.php?name=TANAKA" class="brands-carousel-item"><img src="assets/brands/TANAKA.jpg" alt="Tanaka"></a>
                    <a href="brand.php?name=CHIYODA" class="brands-carousel-item"><img src="assets/brands/CHIYODA.jpg" alt="Chiyoda"></a>
                    <a href="brand.php?name=Yutaka" class="brands-carousel-item"><img src="assets/brands/YUTAKA.jpg" alt="Yutaka"></a>
                    <a href="brand.php?name=HARDWORKER" class="brands-carousel-item"><img src="assets/brands/HARDWORKER.jpg" alt="Hard Workers"></a>
                    <a href="brand.php?name=Soyer" class="brands-carousel-item"><img src="assets/brands/SOYER.jpg" alt="Soyer"></a>
                    <a href="brand.php?name=Aquasol" class="brands-carousel-item"><img src="assets/brands/AQUASOL.jpg" alt="Aquasol"></a>
                    <a href="brand.php?name=SK%20And%20GAL%20GAGE" class="brands-carousel-item"><img src="assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE"></a>
                    <a href="brand.php?name=COPPUS" class="brands-carousel-item"><img src="assets/brands/COPPUS.jpg" alt="Coppus"></a>
                    <a href="brand.php?name=BW%20Technologies" class="brands-carousel-item"><img src="assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies"></a>
                    <a href="brand.php?name=RAE" class="brands-carousel-item"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAE Systems"></a>
                    <a href="brand.php?name=WELDAS" class="brands-carousel-item"><img src="assets/brands/WELDAS.jpg" alt="Weldas"></a>
                    <a href="brand.php?name=UVEX" class="brands-carousel-item"><img src="assets/brands/UVEX.jpg" alt="Uvex"></a>
                    <a href="brand.php?name=ACES" class="brands-carousel-item"><img src="assets/brands/ACES.jpg" alt="Aces"></a>
                    <a href="brand.php?name=MICROGARD" class="brands-carousel-item"><img src="assets/brands/MICROGARD.jpg" alt="Microgard"></a>
                    <a href="brand.php?name=ANSELL" class="brands-carousel-item"><img src="assets/brands/ANSELL.jpg" alt="Ansell"></a>
                    <a href="brand.php?name=Alfra" class="brands-carousel-item"><img src="assets/brands/ALFRA.jpg" alt="Alfra"></a>
                    <a href="brand.php?name=BOSCH" class="brands-carousel-item"><img src="assets/brands/BOSCH.jpg" alt="Bosch"></a>
                    <a href="brand.php?name=Makita" class="brands-carousel-item"><img src="assets/brands/MAKITA.jpg" alt="Makita"></a>
                    <a href="brand.php?name=Weller" class="brands-carousel-item"><img src="assets/brands/WEILER.jpg" alt="Weller"></a>
                    <a href="brand.php?name=Garryson" class="brands-carousel-item"><img src="assets/brands/GARRYSON.jpg" alt="Garryson"></a>
                    <a href="brand.php?name=REVOLT" class="brands-carousel-item"><img src="assets/brands/REVOLT.png" alt="REVOLT"></a>
                    <a href="brand.php?name=Technotex" class="brands-carousel-item"><img src="assets/brands/TECHNOTEX.png" alt="Technotex"></a>
                    <a href="brand.php?name=Spilfyter" class="brands-carousel-item"><img src="assets/brands/SPILFYTER.jpg" alt="Spilfyter"></a>
                    <a href="brand.php?name=Dalo" class="brands-carousel-item"><img src="assets/brands/DALO.jpg" alt="Dalo"></a>
                    <a href="brand.php?name=MOTOLITE" class="brands-carousel-item"><img src="assets/brands/MOTOLITE.jpg" alt="Motolite"></a>
                </div>
            </div>
            <button class="brands-carousel-btn brands-carousel-next" id="brandsNext" aria-label="Next">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        <div class="brands-carousel-dots" id="brandsDots"></div>
    </section>
    <style>
        .brands-carousel-section {
            background: #f9f9f9;
            padding: 40px 0 36px;
            border-top: 1px solid #e8e8e8;
        }
        .brands-carousel-heading {
            text-align: center;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #3f3e3e;
            margin-bottom: 24px;
        }
        .brands-carousel-outer {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 16px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .brands-carousel-viewport {
            overflow: hidden;
            flex: 1;
            min-width: 0;
        }
        .brands-carousel-track {
            display: flex;
            gap: 16px;
            transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }
        .brands-carousel-item {
            flex-shrink: 0;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid #efefef;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 10px 14px;
            transition: box-shadow 0.25s ease, transform 0.25s ease, border-color 0.25s ease;
            text-decoration: none;
        }
        .brands-carousel-item:hover {
            box-shadow: 0 8px 24px rgba(0, 215, 179, 0.22);
            transform: translateY(-4px);
            border-color: #00D7B3;
        }
        .brands-carousel-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: grayscale(25%);
            transition: filter 0.25s ease;
            pointer-events: none;
        }
        .brands-carousel-item:hover img {
            filter: grayscale(0%);
        }
        .brands-carousel-btn {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #ddd;
            background: #fff;
            color: #444;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease, transform 0.15s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .brands-carousel-btn:hover {
            background: #2B11DB;
            border-color: #2B11DB;
            color: #fff;
            transform: scale(1.1);
        }
        .brands-carousel-btn:active {
            transform: scale(0.95);
        }
        .brands-carousel-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            transform: none;
        }
        .brands-carousel-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }
        .brands-carousel-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ddd;
            border: none;
            cursor: pointer;
            padding: 0;
            transition: background 0.2s ease, transform 0.2s ease, width 0.2s ease;
        }
        .brands-carousel-dot.active {
            background: #2B11DB;
            width: 22px;
            border-radius: 4px;
        }
        @media (max-width: 768px) {
            .brands-carousel-section { padding: 28px 0 24px; }
            .brands-carousel-heading { font-size: 13px; margin-bottom: 16px; letter-spacing: 1.5px; }
            .brands-carousel-btn {
                width: 30px;
                height: 30px;
                font-size: 13px;
            }
            .brands-carousel-outer { gap: 6px; padding: 0 6px; }
            .brands-carousel-dots { margin-top: 16px; gap: 6px; }
            .brands-carousel-dot {
                /* expand tap target with padding, keep visual tiny */
                padding: 10px 4px;
                width: 8px;
                height: 8px;
                box-sizing: content-box;
                background-clip: content-box;
            }
            .brands-carousel-dot.active {
                width: 22px;
                padding: 10px 4px;
            }
        }
        @media (max-width: 600px) {
            .brands-carousel-item { width: 90px; height: 60px; padding: 8px 10px; }
            .brands-carousel-track { gap: 10px; }
            .brands-carousel-outer { gap: 6px; padding: 0 4px; }
        }
    </style>
    <script>
    (function(){
        var track    = document.getElementById('brandsTrack');
        var viewport = document.getElementById('brandsViewport');
        var prevBtn  = document.getElementById('brandsPrev');
        var nextBtn  = document.getElementById('brandsNext');
        var dotsWrap = document.getElementById('brandsDots');
        if(!track || !viewport) return;

        var items      = track.querySelectorAll('.brands-carousel-item');
        var totalItems = items.length;
        var currentPage = 0;
        var autoTimer;

        function getVisible() {
            var w = viewport.offsetWidth;
            if(w >= 800)  return 5;
            if(w >= 600)  return 4;
            if(w >= 420)  return 3;
            return 2;
        }

        function itemWidth() {
            if(items.length === 0) return 130;
            var style = window.getComputedStyle(track);
            var gap = parseFloat(style.gap) || 16;
            var vis = getVisible();
            return (viewport.offsetWidth - gap * (vis - 1)) / vis;
        }

        function totalPages() {
            return Math.ceil(totalItems / getVisible());
        }

        function buildDots() {
            dotsWrap.innerHTML = '';
            var pages = totalPages();
            for(var i = 0; i < pages; i++){
                var btn = document.createElement('button');
                btn.className = 'brands-carousel-dot' + (i === currentPage ? ' active' : '');
                btn.setAttribute('aria-label', 'Page ' + (i+1));
                (function(idx){ btn.addEventListener('click', function(){ goTo(idx); resetAuto(); }); })(i);
                dotsWrap.appendChild(btn);
            }
        }

        function updateDots() {
            var dots = dotsWrap.querySelectorAll('.brands-carousel-dot');
            dots.forEach(function(d, i){ d.classList.toggle('active', i === currentPage); });
        }

        function updateButtons() {
            prevBtn.disabled = currentPage === 0;
            nextBtn.disabled = currentPage >= totalPages() - 1;
        }

        function goTo(page) {
            var pages = totalPages();
            if(page < 0) page = 0;
            if(page >= pages) page = pages - 1;
            currentPage = page;

            var vis  = getVisible();
            var gap  = parseFloat(window.getComputedStyle(track).gap) || 16;
            var iw   = (viewport.offsetWidth - gap * (vis - 1)) / vis;

            // Apply computed width to each item so they fill the viewport evenly
            items.forEach(function(item){ item.style.width = iw + 'px'; });

            var offset = currentPage * vis * (iw + gap);
            var maxOffset = (totalItems - vis) * (iw + gap);
            if(offset > maxOffset) offset = maxOffset;

            track.style.transform = 'translateX(-' + offset + 'px)';
            updateDots();
            updateButtons();
        }

        function resetAuto() {
            clearInterval(autoTimer);
            autoTimer = setInterval(function(){
                var next = currentPage + 1;
                if(next >= totalPages()) next = 0;
                goTo(next);
            }, 7000);
        }

        // Resize handler
        var resizeTimer;
        window.addEventListener('resize', function(){
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function(){
                buildDots();
                goTo(currentPage);
            }, 150);
        });

        prevBtn.addEventListener('click', function(){ goTo(currentPage - 1); resetAuto(); });
        nextBtn.addEventListener('click', function(){ goTo(currentPage + 1); resetAuto(); });

        // Touch / swipe support for mobile
        var touchStartX = 0;
        var touchStartY = 0;
        var isSwiping = false;
        viewport.addEventListener('touchstart', function(e){
            touchStartX = e.changedTouches[0].clientX;
            touchStartY = e.changedTouches[0].clientY;
            isSwiping = false;
        }, { passive: true });
        viewport.addEventListener('touchmove', function(e){
            var dx = Math.abs(e.changedTouches[0].clientX - touchStartX);
            var dy = Math.abs(e.changedTouches[0].clientY - touchStartY);
            if(dx > dy && dx > 8) isSwiping = true;
        }, { passive: true });
        viewport.addEventListener('touchend', function(e){
            if(!isSwiping) return;
            var dx = e.changedTouches[0].clientX - touchStartX;
            if(Math.abs(dx) > 40) {
                if(dx < 0) goTo(currentPage + 1);
                else        goTo(currentPage - 1);
                resetAuto();
            }
            isSwiping = false;
        }, { passive: true });

        // Init
        buildDots();
        goTo(0);
        resetAuto();

        // Pause autoplay on hover
        viewport.addEventListener('mouseenter', function(){ clearInterval(autoTimer); });
        viewport.addEventListener('mouseleave', resetAuto);
    })();
    </script>
    </div><!-- /.page-content -->

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#privacy">Privacy Policy</a>
                <a href="#terms">Terms of Service</a>
                <a href="#sitemap">Sitemap</a>
                <a href="contact.php">Contact Us</a>
            </div>
            <div class="footer-copyright">
                <p>&copy; 2026 <?php echo $company_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <?php require_once __DIR__ . '/includes/footer_modernize.php'; ?>
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
        (function(){
            function updateCartBadge() {
                var badge = document.getElementById('cartBadge');
                if(!badge) return;
                var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                var count = items.length;
                if(count > 0) { badge.textContent = count; badge.classList.remove('hidden'); }
                else { badge.classList.add('hidden'); }
            }
            updateCartBadge();
            window.addEventListener('storage', updateCartBadge);
            window.addEventListener('inquiryItemsUpdated', updateCartBadge);
            setInterval(updateCartBadge, 500);
        })();
    </script>
</body>
</html>

