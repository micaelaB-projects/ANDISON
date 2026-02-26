<?php
require_once __DIR__ . '/andison/includes/analytics.php';
andison_track_visit('services');
require_once __DIR__ . '/andison/includes/home_featured.php';
require_once __DIR__ . '/andison/includes/home_slider.php';
require_once __DIR__ . '/andison/includes/youtube_links.php';

$featured = andison_get_home_featured();
$slides = andison_get_home_slider();
$ytLinks = andison_get_youtube_links();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - ANDISON INDUSTRIAL</title>
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
            color: rgba(252, 249, 249, 0.95);
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

                width: 75%;
                max-width: 320px;
                padding: 12px 0;
            }

                font-size: 14px;
                margin-bottom: 8px;
                padding: 0 12px;
            }

                padding: 0;
            }

                border-bottom: none;
            }

                font-size: 13px;
                padding: 10px 14px;
                gap: 12px;
                min-height: 36px;
                align-items: center;
            }

                background: rgba(43, 17, 219, 0.08);
            }

                width: 20px;
                height: 20px;
                font-size: 16px;
            }

                background: #f8f9fa;
                border-left: 3px solid #2B11DB;
                margin: 2px 0;
                padding: 4px 0 4px 12px;
            }

                font-size: 12px;
                padding: 8px 14px 8px 42px;
                min-height: 32px;
            }

                margin: 4px 0;
                padding: 6px 0 6px 14px;
                background: rgba(43, 17, 219, 0.06);
                border-left: 2px solid rgba(43, 17, 219, 0.3);
            }

                font-size: 11px;
                padding: 8px 14px 8px 36px;
                color: #5a6b7d;
                min-height: 30px;
                margin: 0;
            }

                background: rgba(43, 17, 219, 0.14);
                color: #2B11DB;
                padding-left: 44px;
            }
        }

            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s;
            z-index: 60;
        }

            opacity: 1;
            visibility: visible;
        }

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
            padding: 12px 8px;
            overflow-y: auto;
        }

            transform: translateX(0);
        }

            font-size: 14px;
            margin-bottom: 10px;
            color: #222;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

            display: flex; 
            gap: 12px; 
            padding: 10px 10px; 
            color: #1f2937; 
            text-decoration: none; 
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s ease;
            font-size: 13px;
            min-height: 36px;
        }
            background: #f3f4f6; 
            color: #2B11DB;
            padding-left: 16px;
        }
            background: #f3f4f6;
            color: #2B11DB;
            font-weight: 600;
            border-left: 4px solid #2B11DB;
            padding-left: 12px;
        }
            color: #2B11DB;
        }
            color: #5b21b6; 
            width: 24px; 
            height: 24px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

            flex: 1;
        }

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

            display: flex;
        }

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
        
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
            padding: 0; 
            border: none;
        }
            color: #4b5563; 
            font-size: 12px; 
            padding: 8px 12px 8px 38px; 
            display: block; 
            text-decoration: none;
            justify-content: flex-start;
            min-height: 32px;
            align-items: center;
        }
            color: #2B11DB; 
            background: rgba(43, 17, 219, 0.08);
            padding-left: 52px;
        }

        /* Nested sublists */
        
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
        }
        
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }
            padding: 0;
            border: none;
        }
            color: #5a6b7d; 
            font-size: 11px; 
            padding: 8px 10px 8px 32px; 
            display: block; 
            text-decoration: none;
            position: relative;
            transition: all 0.25s ease;
            border-radius: 4px;
            margin: 0;
            min-height: 30px;
            align-items: center;
        }
            content: '';
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 5px;
            height: 5px;
            background: linear-gradient(135deg, #2B11DB 0%, #6d28d9 100%);
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(43, 17, 219, 0.2);
        }
            color: #2B11DB;
            background: rgba(43, 17, 219, 0.12);
            padding-left: 36px;
            transform: none;
        }

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
            color: #374151;
        }

        section,
        footer,
        .page-content,
        .main-content, 
        .category-container {
            margin-left: 0px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* When expanded, increase margin */
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

                display: none !important;
            }
        }

            width: 380px;
        }

            display: none !important;
        }

        @media (max-width: 768px) {
                top: calc(14px + 36px + 14px + 40px);
                width: 56px !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease, width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }
                transform: translateX(0);
            }
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
                display: flex !important;
            }
        }

        .industries-hero {
            text-align: center;
            padding: 52px 20px 36px;
            background: #fff;
        }
        .industries-hero h2 {
            font-size: 40px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 14px;
            color: #1a0aa8;
        }
        .industries-hero h2 span {
            background: linear-gradient(90deg, #00D7B3, #2B11DB);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .industries-hero p {
            max-width: 680px;
            margin: 0 auto;
            color: #555;
            font-size: 15px;
            line-height: 1.8;
        }

        .industry-cards {
            display: flex;
            flex-direction: column;
            gap: 22px;
            max-width: 860px;
            margin: 0 auto;
            padding: 0 20px 60px;
        }

        .industry-card {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid #d8e8f5;
            border-left: 5px solid #1565C0;
            border-top: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0,0,0,0.06);
            background: #fff;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .industry-card:hover {
            box-shadow: 0 12px 36px rgba(21, 101, 192, 0.12);
            transform: translateY(-3px);
        }

        .industry-card.expanded {
            grid-template-columns: 1fr 1.1fr;
            box-shadow: 0 16px 48px rgba(21, 101, 192, 0.15);
        }

        .industry-card-body {
            padding: 36px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .industry-card.expanded .industry-card-body {
            padding: 48px;
            justify-content: flex-start;
        }

        .industry-card.expanded .industry-card-body > h3,
        .industry-card.expanded .industry-card-body > p:first-of-type {
            display: none;
        }

        .industry-card-body h3 {
            font-size: 24px;
            font-weight: 800;
            color: #1565C0;
            text-align: left;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .industry-card-body > p {
            font-size: 13px;
            color: #666;
            line-height: 1.8;
            margin: 0;
            text-align: left;
        }

        .industry-read-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #1565C0 0%, #1e88e5 100%);
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.8px;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 12px;
            align-self: flex-start;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(21, 101, 192, 0.3);
            text-transform: uppercase;
            border: none;
            cursor: pointer;
        }
        .industry-read-more:hover {
            background: linear-gradient(135deg, #1e88e5 0%, #1976D2 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(21, 101, 192, 0.4);
            color: #fff;
        }
        .industry-read-more:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(21, 101, 192, 0.2);
        }

        .industry-card-expanded {
            display: none;
            margin-top: 20px;
            padding-top: 24px;
            border-top: 2px solid #e8eef7;
        }

        .industry-card.expanded .industry-card-expanded {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .industry-card.expanded .industry-read-more {
            display: none;
        }

        .industry-expanded-content h4 {
            font-size: 22px;
            font-weight: 800;
            color: #1565C0;
            margin-bottom: 20px;
            line-height: 1.3;
            letter-spacing: -0.3px;
        }

        .industry-expanded-content > p:first-of-type {
            font-size: 14px;
            color: #555;
            line-height: 1.9;
            margin-bottom: 24px;
            text-align: left;
            font-weight: 500;
        }

        .industry-expanded-content p {
            font-size: 13px;
            color: #777;
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .industry-expanded-content strong {
            display: block;
            color: #1565C0;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
            margin-top: 22px;
            text-transform: uppercase;
        }

        .industry-expanded-content ul {
            margin: 14px 0 28px 0;
            padding-left: 24px;
        }

        .industry-expanded-content ul li {
            font-size: 13px;
            color: #666;
            line-height: 1.9;
            margin-bottom: 10px;
            list-style: disc;
            list-style-type: none;
            position: relative;
            padding-left: 16px;
        }

        .industry-expanded-content ul li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #1565C0;
            font-weight: 800;
            font-size: 18px;
            line-height: 1;
            top: -2px;
        }

        .industry-expanded-content > p:last-child {
            margin-top: 20px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #666;
        }

        .industry-close-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #1565C0 0%, #1e88e5 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.2px;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
            margin-top: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 16px rgba(21, 101, 192, 0.3);
            text-transform: uppercase;
            border: none;
            cursor: pointer;
        }

        .industry-close-btn:hover {
            background: linear-gradient(135deg, #1e88e5 0%, #1976D2 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(21, 101, 192, 0.4);
        }

        .industry-card-image {
            position: relative;
            height: 300px;
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(21, 101, 192, 0.1);
            order: 2;
        }

        .industry-card.expanded .industry-card-image {
            height: 420px;
            border-radius: 12px;
            box-shadow: 0 16px 48px rgba(21, 101, 192, 0.2);
            transform: scale(1.02);
            order: 1;
        }

        .industry-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .industry-card.expanded .industry-card-image img {
            transform: scale(1.08);
        }

        .industry-card-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.05) 0%, rgba(0, 188, 212, 0.05) 100%);
            pointer-events: none;
            transition: opacity 0.4s ease;
        }

        .industry-card.expanded .industry-card-image::before {
            opacity: 0;
        }

        .industry-card-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 6px;
            box-shadow: inset 0 0 0 1px rgba(21, 101, 192, 0.15);
            pointer-events: none;
        }

        .industry-card.expanded .industry-card-image::after {
            border-radius: 12px;
            box-shadow: inset 0 0 0 2px rgba(21, 101, 192, 0.25);
        }

        @media (max-width: 768px) {
            .industry-card {
                grid-template-columns: 1fr;
            }
            .industry-card.expanded {
                grid-template-columns: 1fr;
            }
            .industry-card-image {
                height: 240px;
                min-height: 240px;
                order: 2 !important;
            }
            .industry-card.expanded .industry-card-image {
                height: 280px;
                margin-top: 24px;
                order: 2 !important;
            }
            .industry-card-body {
                order: 1;
            }
            .industries-hero h2 { font-size: 28px; }
        }
    </style>
</head>
<body>
        <?php
        // Set page title
        $page_title = "Industries";
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
                <div class="header-contact">
                        <div class="contact-dropdown" tabindex="0" aria-haspopup="true">
                            <a href="javascript:void(0)" class="contact-link" aria-label="Contact Us">Contact Us &#9662;</a>
                            <div class="contact-popover" role="menu" aria-hidden="true">
                                <button type="button" class="contact-close" aria-label="Close contact popover">&times;</button>
                                <p style="font-weight:700;font-size:13px;color:#2B11DB;margin-bottom:8px;padding-bottom:8px;border-bottom:2px solid #f0f0f0;">Get in Touch</p>
                                <ul class="contact-list" style="display:block;list-style:none;margin:0;padding:6px 0;">
                                    <li style="display:flex;gap:12px;align-items:center;padding:10px 6px;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB;flex-shrink:0;"><i class="bi bi-telephone-fill"></i></span><div><small style="color:#999;font-size:11px;display:block;">Landline</small><a href="tel:+12345678900" style="color:#111;text-decoration:none;font-weight:600;">+1(234) 567 8900</a></div></li>
                                    <li style="display:flex;gap:12px;align-items:center;padding:10px 6px;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB;flex-shrink:0;"><i class="bi bi-telephone-fill"></i></span><div><small style="color:#999;font-size:11px;display:block;">Mobile</small><a href="tel:+16399778037398" style="color:#111;text-decoration:none;font-weight:600;">+1(639) 977 803 7398</a></div></li>
                                    <li style="display:flex;gap:12px;align-items:center;padding:10px 6px;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB;flex-shrink:0;"><i class="bi bi-envelope-fill"></i></span><div><small style="color:#999;font-size:11px;display:block;">Email</small><a href="mailto:info@andison-industrial.com" style="color:#111;text-decoration:none;font-weight:600;">info@andison-industrial.com</a></div></li>
                                    <li style="display:flex;gap:12px;align-items:center;padding:10px 6px;"><span class="icon" style="font-size:18px;width:28px;text-align:center;color:#2B11DB;flex-shrink:0;"><i class="bi bi-facebook"></i></span><div><small style="color:#999;font-size:11px;display:block;">Facebook</small><a href="https://www.facebook.com/AndisonIndustrialSalesInc" target="_blank" style="color:#111;text-decoration:none;font-weight:600;">Andison Industrial</a></div></li>
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
                                <li><a href="brand.php?name=RAC"><img src="assets/brands/RAE%20SYSTEMS.jpg" alt="RAE Systems" title="RAE Systems"></a></li>
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
                        <a href="industries.php" class="active">Industries</a>
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

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/ANDISON/includes/sidebar.php'; ?>

    <!-- Services Content -->
    <div class="page-content">

    <!-- Industries Hero -->
    <section id="industries-overview" style="padding: 0; background: #fff;">
        <div class="industries-hero">
            <h2>Our Industrial <span>Expertise</span></h2>
            <p>Andison Industrial proudly serves a diverse range of sectors, delivering precision, innovation, and reliability to drive progress in every industry we touch.</p>
        </div>

        <div class="industry-cards">

            <!-- Motor Vehicle Industry -->
            <div class="industry-card" data-industry="motor-vehicle">
                <div class="industry-card-body">
                    <h3>Motor Vehicle Industry</h3>
                    <p>This industry manufactures automobiles, motorcycles, buses, and truck vans. They have a growing presence in the Philippine market, especially with the high market for motorcycles. We offer a wide assortment of welding equipment and consumables necessary to produce world-class products.</p>
                    <a href="#" class="industry-read-more">READ MORE &#9660;</a>
                    
                    <div class="industry-card-expanded">
                        <div class="industry-expanded-content">
                            <h4>Motor Vehicle Industry</h4>
                            <p>Top multinational and domestic automotive companies choose our Panasonic Welding Systems to significantly improve weld quality and boost efficiency while reducing production costs. We provide consultation, training, maintenance, and reliable after-sales service to satisfy our customers' expectations.</p>
                            
                            <strong style="color: #1565C0; font-size: 13px;">Some of our products used in this industry are:</strong>
                            <ul>
                                <li>Arc Welding Robot with Power Source</li>
                                <li>Arc Welding Equipment and Filler Metals</li>
                                <li>Power Tools and Hand Tools</li>
                                <li>Personal Protective Equipment (PPEs)</li>
                            </ul>
                            
                            <p style="margin-top: 14px; font-size: 13px; color: #666;">For items not found on our website, kindly see our <a href="contact.php" style="color: #1565C0; text-decoration: none; font-weight: 600;">contact details</a> and send us an inquiry.</p>
                            
                            <button class="industry-close-btn">CLOSE &#9652;</button>
                        </div>
                    </div>
                </div>
                <div class="industry-card-image">
                    <img src="assets/HOME/MOTOR VEHICLE.jpg" alt="Motor Vehicle Industry">
                </div>
            </div>

            <!-- Metal Fabrication -->
            <div class="industry-card" data-industry="metal-fabrication">
                <div class="industry-card-body">
                    <h3>Metal Fabrication and Industrial</h3>
                    <p>Bridges, railways, refineries, shipyards, transmission lines, and other large-scale projects require steel frames and other metals to support the large infrastructures. Workers in the metal fabrication industry do welding, metal cutting, and fastening to assemble metal parts.</p>
                    <a href="#" class="industry-read-more">READ MORE &#9660;</a>
                    
                    <div class="industry-card-expanded">
                        <div class="industry-expanded-content">
                            <h4>Metal Fabrication and Industrial</h4>
                            <p>We supply our clients with equipment that makes quality welds in a short time. Our safety products protect workers from hazards such as working from heights, sparks, glaring lights, and hazardous gases.</p>
                            
                            <strong style="color: #1565C0; font-size: 13px;">Some of our products used in this industry are:</strong>
                            <ul>
                                <li>Arc Welding Equipment and Filler Metals</li>
                                <li>Plate Cutting and Beveling Equipment</li>
                                <li>Gas Welding and Cutting Equipment</li>
                                <li>Power Tools and Hand Tools</li>
                                <li>Personal Protective Equipment (PPEs)</li>
                            </ul>
                            
                            <p style="margin-top: 14px; font-size: 13px; color: #666;">For items not found on our website, kindly see our <a href="contact.php" style="color: #1565C0; text-decoration: none; font-weight: 600;">contact details</a> and send us an inquiry.</p>
                            
                            <button class="industry-close-btn">CLOSE &#9652;</button>
                        </div>
                    </div>
                </div>
                <div class="industry-card-image">
                    <img src="assets/HOME/METAL FABRICATION.jpg" alt="Metal Fabrication and Industrial">
                </div>
            </div>

            <!-- Power Generation -->
            <div class="industry-card" data-industry="power-generation">
                <div class="industry-card-body">
                    <h3>Power Generation</h3>
                    <p>The Power Generation Industry is vital in a country's growth. They must be a reliable partner in meeting the Philippine Energy Market's ever-growing demands.</p>
                    <a href="#" class="industry-read-more">READ MORE &#9660;</a>
                    
                    <div class="industry-card-expanded">
                        <div class="industry-expanded-content">
                            <h4>Power Generation</h4>
                            <p>From plant maintenance, shutdown, building power transmission lines, and other infrastructures, we work closely with our clients and supply them finish their projects on schedule.</p>
                            
                            <strong style="color: #1565C0; font-size: 13px;">Some of our products used in this industry are:</strong>
                            <ul>
                                <li>Arc Welding Equipment and Filler Metals</li>
                                <li>Power Tools and Hand Tools</li>
                                <li>Grinders, Maintenance Tools and Equipment</li>
                                <li>Bearings, Maintenance Tools and Equipment</li>
                                <li>Height Protection Equipment and other PPEs</li>
                            </ul>
                            
                            <p style="margin-top: 14px; font-size: 13px; color: #666;">For items not found on our website, kindly see our <a href="contact.php" style="color: #1565C0; text-decoration: none; font-weight: 600;">contact details</a> and send us an inquiry.</p>
                            
                            <button class="industry-close-btn">CLOSE &#9652;</button>
                        </div>
                    </div>
                </div>
                <div class="industry-card-image">
                    <img src="assets/HOME/POWER GENERATION.jpg" alt="Power Generation">
                </div>
            </div>

            <!-- Oil and Petrochemical -->
            <div class="industry-card" data-industry="oil-petrochemical">
                <div class="industry-card-body">
                    <h3>Oil and Petrochemical Industry</h3>
                    <p>Oil refineries use fractional distillation and other methods to process crude oil into more useful products like petroleum, gasoline, and other fuels. During the distillation, heavier by-products settle at the bottom. Petrochemical plants crack the by-products and further process them into more useful chemicals. Other industries use these petrochemicals to create different products.</p>
                    <a href="#" class="industry-read-more">READ MORE &#9660;</a>
                    
                    <div class="industry-card-expanded">
                        <div class="industry-expanded-content">
                            <h4>Oil and Petrochemical Industry</h4>
                            <p>Oil and petrochemical industries regularly perform industrial works (projects) that require maintenance, shutdowns, and expanding facilities and pipelines. We provide our clients with safety products, equipment and consumables for maintaining the facilities and building industrial projects.</p>
                            
                            <strong style="color: #1565C0; font-size: 13px;">Some of our products used in this industry are:</strong>
                            <ul>
                                <li>Arc Welding Equipment and Filler Metals</li>
                                <li>Portable Gas Detectors</li>
                                <li>Air Movers and Industrial Ventilators</li>
                                <li>Bearings, Maintenance Tools and Equipment</li>
                                <li>Pipe Cutting and Beveling Machine</li>
                                <li>Power Tools and Hand Tools</li>
                                <li>Personal Protective Equipment (PPEs)</li>
                            </ul>
                            
                            <p style="margin-top: 14px; font-size: 13px; color: #666;">For items not found on our website, kindly see our <a href="contact.php" style="color: #1565C0; text-decoration: none; font-weight: 600;">contact details</a> and send us an inquiry.</p>
                            
                            <button class="industry-close-btn">CLOSE &#9652;</button>
                        </div>
                    </div>
                </div>
                <div class="industry-card-image">
                    <img src="assets/HOME/OIL AND PETROCHEMICAL.jpg" alt="Oil and Petrochemical Industry">
                </div>
            </div>

            <!-- Mining Industry -->
            <div class="industry-card" data-industry="mining">
                <div class="industry-card-body">
                    <h3>Mining Industry</h3>
                    <p>This industry extracts coal, oil, metals, and other raw materials from the earth. These resources are processed by other industries to create products such as fuel, jewelry, construction materials, and everyday items. Mining is vital to the economy.</p>
                    <a href="#" class="industry-read-more">READ MORE &#9660;</a>
                    
                    <div class="industry-card-expanded">
                        <div class="industry-expanded-content">
                            <h4>Mining Industry</h4>
                            <p>However, digging deep into the ground could pose a safety risk to workers without the proper equipment. We at Andison promote safety by providing high-quality PPEs. Our portfolio includes various single and multi-gas detectors including maintenance-free gas detection. We provide clients with training on the proper use of the equipment to fully use its functions and ensure a safe working environment. We also do recalibration for the gas detection.</p>
                            
                            <strong style="color: #1565C0; font-size: 13px;">Some of our products used in this industry are:</strong>
                            <ul>
                                <li>Portable and Multi-Gas Detectors</li>
                                <li>PPEs and other Safety Products</li>
                                <li>Air Movers and Ventilation Equipment</li>
                                <li>Bearings, Maintenance Tools and Equipment</li>
                                <li>Cordless Power Tools</li>
                                <li>Floodlights and other Light Sources</li>
                            </ul>
                            
                            <p style="margin-top: 14px; font-size: 13px; color: #666;">For items not found on our website, kindly see our <a href="contact.php" style="color: #1565C0; text-decoration: none; font-weight: 600;">contact details</a> and send us an inquiry.</p>
                            
                            <button class="industry-close-btn">CLOSE &#9652;</button>
                        </div>
                    </div>
                </div>
                <div class="industry-card-image">
                    <img src="assets/HOME/MINING.jpg" alt="Mining Industry">
                </div>
            </div>

            <!-- Shipyard -->
            <div class="industry-card" data-industry="shipyard">
                <div class="industry-card-body">
                    <h3>Shipyard</h3>
                    <p>World trade relies heavily on freight ships because it offers a high capacity at a low cost in transporting goods. Being an archipelago, the Philippines also uses ships to ferry people to the country's many islands. Shipyards play a critical role in maintaining ships, ensuring they are seaworthy and safe.</p>
                    <a href="#" class="industry-read-more">READ MORE &#9660;</a>
                    
                    <div class="industry-card-expanded">
                        <div class="industry-expanded-content">
                            <h4>Shipyard</h4>
                            <p>Metal fabrication is an integral part of the shipbuilding industry. Andison has a wide product catalog for working with metal fabrication, providing clients with equipment ready for the job.</p>
                            
                            <strong style="color: #1565C0; font-size: 13px;">Some of our products used in this industry are:</strong>
                            <ul>
                                <li>Arc Welding Equipment and Filler Metals</li>
                                <li>Gas Welding and Cutting Equipment</li>
                                <li>Air Movers and Industrial Ventilators</li>
                                <li>Power Tools and Hand Tools</li>
                                <li>Pipe Cutting and Beveling Machine</li>
                                <li>Personal Protective Equipment (PPEs)</li>
                                <li>Portable Gas Detectors</li>
                            </ul>
                            
                            <p style="margin-top: 14px; font-size: 13px; color: #666;">For items not found on our website, kindly see our <a href="contact.php" style="color: #1565C0; text-decoration: none; font-weight: 600;">contact details</a> and send us an inquiry.</p>
                            
                            <button class="industry-close-btn">CLOSE &#9652;</button>
                        </div>
                    </div>
                </div>
                <div class="industry-card-image">
                    <img src="assets/HOME/shipyard.jpg" alt="Shipyard">
                </div>
            </div>

        </div>
    </section>

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
        (function(){);
            }
            
            
                
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
                    li.innerHTML = '<span class="square"></span><a href="' + item.href + '">' + item.text + '</a>';
                    popoverList.appendChild(li);
                });
                
                // Position popover next to toggle button
                setTimeout(function() {
                    
                    var toggleRect = toggle.getBoundingClientRect();
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
                    
                }, 5);
            }
            
            }
            
            var subToggles = document.querySelectorAll('.sub-toggle');
            subToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    } else {
                    }
                });
            });
            
            // Close popover on outside click
            document.addEventListener('click', function(e) {
            });
            
            // Close popover on Escape
            document.addEventListener('keydown', function(e) {
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
        // ============================================
        setTimeout(function(){
            var currentPath = window.location.pathname.toLowerCase();
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

    <script>
        (function() {
            var fabIcon = document.getElementById('mobileFabIcon');

            function isMobile() { return window.innerWidth <= 768; }

            function syncFab() {
                if (!isMobile()) { fab.classList.remove('open', 'wide'); return; }
                fab.classList.toggle('open', isOpen);
                fab.classList.toggle('wide', isOpen && isExpanded);
                fabIcon.className = isOpen ? 'bi bi-chevron-left' : 'bi bi-chevron-right';
            }

            fab.addEventListener('click', function(e) {
                e.stopPropagation();
                if (!isMobile()) return;
                syncFab();
            });

            var observer = new MutationObserver(function() { syncFab(); });

            window.addEventListener('resize', syncFab);
        })();
    </script>

    <script>
        // Industry Card Expand/Collapse Functionality
        (function(){
            var readMoreButtons = document.querySelectorAll('.industry-read-more');
            var closeButtons = document.querySelectorAll('.industry-close-btn');

            // Handle READ MORE clicks
            readMoreButtons.forEach(function(button){
                button.addEventListener('click', function(e){
                    e.preventDefault();
                    var card = button.closest('.industry-card');
                    if(card){
                        card.classList.add('expanded');
                        // Scroll to card
                        setTimeout(function(){
                            card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }, 100);
                    }
                });
            });

            // Handle CLOSE clicks
            closeButtons.forEach(function(button){
                button.addEventListener('click', function(e){
                    e.preventDefault();
                    var card = button.closest('.industry-card');
                    if(card){
                        card.classList.remove('expanded');
                    }
                });
            });
        })();
    </script>
</body>
</html>



