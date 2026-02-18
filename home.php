<?php
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
    <title>Industrial Solutions Inc. - Homepage Redesign</title>
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
            padding: 12px 20px;
            gap: 20px;
            margin-bottom: 0;
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
            max-width: calc(100vw - 32px);
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
            overflow: hidden;
        }

        @media (max-width: 480px) {
            .contact-popover {
                left: 50%;
                transform: translateX(-50%) translateY(-6px) scale(0.98);
            }
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
            justify-content: flex-start;
            max-width: 500px;
            margin: 0;
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
            padding: 10px 20px 10px 46px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            font-size: 16px;
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
<<<<<<< HEAD
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00BCD4, #00897B);
            position: relative;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,188,212,0.4);
            gap: 8px;
=======
            color: #333;
            font-weight: 600;
            padding: clamp(8px, 1.5vw, 10px) clamp(12px, 2vw, 18px);
            border-radius: 6px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
            font-size: clamp(12px, 1.2vw, 14px);
            white-space: nowrap;
>>>>>>> d52df9b9871610028cefd7cba29680d74357b1f1
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
<<<<<<< HEAD
            overflow: hidden;
=======
            overflow: visible;
            margin-top: 0;
>>>>>>> d52df9b9871610028cefd7cba29680d74357b1f1
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 8px 0 120px; /* space for the left Browse toggle */
            display: flex;
            flex-wrap: nowrap;
            align-items: center;
<<<<<<< HEAD
            min-height: 52px;
            gap: 0;
            justify-content: center;
            overflow: hidden;
=======
            min-height: 56px;
            gap: 28px;
            justify-content: flex-start;
            padding-left: 20px;
>>>>>>> d52df9b9871610028cefd7cba29680d74357b1f1
        }

        /* Browse-toggle hamburger menu styles */
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
            line-height: 1;
            transition: all 0.3s ease;
        }

        .browse-toggle:hover {
            background: rgba(255,255,255,0.1);
            border-radius: 6px;
        }

        .browse-toggle:active {
            transform: translateY(-50%) scale(0.95);
        }

        .browse-toggle i,
        .browse-toggle .bi {
            display: block;
            font-size: 18px;
            line-height: 1;
        }

        .browse-toggle .browse-text {
            display: inline;
            font-size: 13px;
            white-space: nowrap;
        }

        .nav-list {
            list-style: none;
            display: flex;
<<<<<<< HEAD
            flex-wrap: nowrap;
            gap: 0;
=======
            gap: 32px;
>>>>>>> d52df9b9871610028cefd7cba29680d74357b1f1
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .nav-list li { position: relative; }

        .nav-list a {
            color: white;
            text-decoration: none;
            font-size: 13px;
            padding: 12px 10px;
            display: block;
            transition: color 0.2s;
            position: relative;
            white-space: nowrap;
        }

        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        /* Glowing underline + dark active background for top-level nav links */
        .nav-list > li > a {
            position: relative;
<<<<<<< HEAD
            padding: 10px 8px;
=======
            padding: 14px 16px;
>>>>>>> d52df9b9871610028cefd7cba29680d74357b1f1
            color: white;
            font-size: 13px;
            white-space: nowrap;
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
            max-width: calc(100vw - 40px);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
            z-index: 110;
            padding: 18px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .nav-dropdown {
                max-width: calc(100vw - 32px);
            }
        }

        @media (max-width: 480px) {
            .nav-dropdown {
                max-width: calc(100vw - 24px);
                padding: 12px;
            }
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
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23888888" width="1200" height="600"/></svg>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            text-align: center;
            padding: clamp(30px, 8vw, 60px) 20px;
            aspect-ratio: 16 / 9;
            min-height: 300px;
            max-height: 600px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: clamp(25px, 5vw, 40px);
            z-index: 1;
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .hero {
                aspect-ratio: 4 / 3;
                min-height: 200px;
                max-height: 400px;
                background-attachment: scroll;
                margin-bottom: 30px;
            }
        }

        @media (max-width: 480px) {
            .hero {
                aspect-ratio: 3 / 2;
                min-height: 160px;
                padding: 25px 12px;
                margin-bottom: 20px;
            }
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
            font-size: clamp(24px, 7vw, 48px);
            margin-bottom: 20px;
            font-weight: 700;
            line-height: 1.2;
        }

        .hero p {
            font-size: clamp(14px, 2.5vw, 18px);
            margin-bottom: 30px;
            color: rgba(255, 255, 255, 0.9);
        }

        .cta-button {
            background: #00d4aa;
            color: white;
            padding: clamp(10px, 2vw, 12px) clamp(20px, 4vw, 35px);
            border: none;
            border-radius: 3px;
            font-size: clamp(14px, 1.5vw, 16px);
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
            width: 100%;
            box-sizing: border-box;
        }

        section {
            padding: 60px 20px;
            position: relative;
            z-index: 10;
            background: white;
            width: 100%;
            box-sizing: border-box;
        }

        section h2 {
            text-align: center;
            font-size: clamp(28px, 8vw, 45px);
            margin-bottom: 20px;
            color: #2B11DB;
        }
    
        .section-description {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 50px;
            color: #666;
            line-height: 1.8;
            font-size: clamp(14px, 2.5vw, 16px);
            padding: 0 10px;
        }

        /* Product Highlights */
        .highlights-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 40px;
        }

        @media (min-width: 600px) {
            .highlights-grid {
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 24px;
            }
        }

        @media (min-width: 900px) {
            .highlights-grid {
                grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
                gap: 28px;
            }
        }

        @media (min-width: 1200px) {
            .highlights-grid {
                grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
                gap: 30px;
            }
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

        .product-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 280px;
            background: linear-gradient(135deg, #888 0%, #666 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .product-image {
                aspect-ratio: 4 / 3;
                min-height: 200px;
            }
        }

        @media (max-width: 480px) {
            .product-image {
                aspect-ratio: 3 / 2;
                min-height: 160px;
            }
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
            display: block;
        }

        .product-image iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }

        .product-image video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: none;
            display: block;
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
            flex-shrink: 0;
        }

        .play-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }

        /* Generic responsive media */
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        iframe {
            max-width: 100%;
            display: block;
        }

        video {
            max-width: 100%;
            height: auto;
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .product-info {
            padding: clamp(12px, 3vw, 20px);
            background: white;
        }

        .product-info h3 {
            font-size: clamp(14px, 1.5vw, 16px);
            margin-bottom: 8px;
            color: #333;
        }

        .product-info p {
            font-size: clamp(12px, 1.2vw, 13px);
            color: #666;
            line-height: 1.6;
        }

        /* Featured Section */
        .featured-section {
            background: linear-gradient(135deg, #c8f0ed 0%, #a8e6e1 100%);
            padding: 60px 50px;
            border-radius: 16px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
            box-shadow: 0 8px 32px rgba(0, 212, 170, 0.12);
            overflow: hidden;
            position: relative;
            width: 100%;
            box-sizing: border-box;
            max-width: 100%;
        }

        @media (max-width: 900px) {
            .featured-section {
                grid-template-columns: 1fr;
                padding: 40px 30px;
                gap: 30px;
            }
        }

        @media (max-width: 600px) {
            .featured-section {
                padding: 30px 16px;
                gap: 20px;
                border-radius: 12px;
            }
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
            background: #00d4aa;
            color: white;
            padding: clamp(6px, 1.2vw, 8px) clamp(12px, 2vw, 16px);
            border-radius: 4px;
            font-size: clamp(9px, 0.9vw, 11px);
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: clamp(12px, 2vw, 20px);
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 212, 170, 0.25);
        }

        .featured-content h3 {
            font-size: clamp(22px, 6vw, 36px);
            margin-bottom: 8px;
            color: #1a1a1a;
            font-weight: 700;
            line-height: 1.3;
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

        @media (max-width: 768px) {
            .featured-content h3::after {
                width: 40px;
                height: 3px;
                margin-top: 12px;
                margin-bottom: 16px;
            }
        }

        .featured-meta {
            display: flex;
            gap: clamp(12px, 3vw, 24px);
            margin-bottom: clamp(12px, 3vw, 24px);
            padding-bottom: clamp(12px, 3vw, 24px);
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            flex-wrap: wrap;
        }

        .featured-event-info {
            display: flex;
            gap: clamp(10px, 2vw, 20px);
            flex-wrap: wrap;
        }

        .featured-discount {
            display: flex;
            align-items: center;
            gap: clamp(6px, 1.5vw, 12px);
            flex-wrap: wrap;
        }

        .featured-discount-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: clamp(6px, 1.2vw, 8px) clamp(10px, 2vw, 16px);
            border-radius: 6px;
            font-weight: 700;
            font-size: clamp(12px, 1.2vw, 14px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            white-space: nowrap;
        }

        .featured-offer-text {
            color: #ff6b6b;
            font-size: clamp(11px, 1vw, 13px);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: normal;
        }

        .featured-event-detail {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: clamp(12px, 1.5vw, 14px);
            color: #333;
        }

        .featured-event-detail strong {
            color: #1a1a1a;
            font-weight: 600;
        }

        .featured-event-detail i {
            color: #2B11DB;
            font-size: clamp(14px, 1.5vw, 16px);
        }

        .featured-content p {
            color: #555;
            margin-bottom: 24px;
            line-height: 1.8;
            font-size: clamp(13px, 2vw, 15px);
        }

        .featured-btn {
            background: linear-gradient(135deg, #00D7B3 0%, #00b8a0 100%);
            color: white;
            padding: clamp(10px, 2vw, 14px) clamp(20px, 4vw, 36px);
            border: none;
            border-radius: 6px;
            font-weight: 700;
            font-size: clamp(13px, 1.5vw, 14px);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 6px 20px rgba(0, 215, 179, 0.35);
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        @media (max-width: 480px) {
            .featured-btn {
                width: 100%;
                text-align: center;
            }
        }

        .featured-btn:hover {
            background: linear-gradient(135deg, #00E6FF 0%, #00d4aa 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0, 215, 179, 0.45);
        }

        .featured-btn:active {
            transform: translateY(0);
        }

        .featured-image {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 280px;
            background: linear-gradient(135deg, #0066cc 0%, #82a2c9 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            box-shadow: 0 12px 40px rgba(0, 102, 204, 0.25);
            position: relative;
            z-index: 2;
            overflow: hidden;
            flex-shrink: 0;
            max-width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .featured-image {
                aspect-ratio: 4 / 3;
                min-height: 220px;
                border-radius: 10px;
            }
        }

        @media (max-width: 480px) {
            .featured-image {
                aspect-ratio: 3 / 2;
                min-height: 160px;
                border-radius: 8px;
            }
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
            background: #2B11DB;
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-top: auto;
            width: 100%;
            position: relative;
            left: 0;
            right: 0;
            margin-left: 0;
            margin-right: 0;
            box-sizing: border-box;
        }

        .footer-content {
            width: 100%;
            margin: 0;
            padding: 0 20px;
            box-sizing: border-box;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: clamp(12px, 3vw, 30px);
            margin-bottom: 20px;
            flex-wrap: wrap;
            padding: 0 12px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            font-size: clamp(11px, 1.2vw, 13px);
            transition: color 0.1s;
            white-space: nowrap;
        }

        .footer-links a:hover {
            color: #00d4aa;
        }

        .footer-copyright {
            font-size: clamp(10px, 1vw, 12px);
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: clamp(12px, 2vw, 20px);
            padding-left: 12px;
            padding-right: 12px;
        }

<<<<<<< HEAD
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
                gap: 6px;
                margin-left: 0;
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
                flex: 0 0 auto;
            }

            nav ul {
                flex-wrap: nowrap;
                gap: 0;
            }

            nav li {
                margin-right: 0;
            }

            .nav-inner {
                padding-left: 50px;
                padding-right: 6px;
                gap: 2px;
                min-height: 40px;
            }

            .nav-list {
                gap: 2px;
            }

            .nav-list a {
                font-size: 11px;
                padding: 8px 6px;
            }

            .browse-toggle {
                font-size: 12px;
                padding: 6px 8px;
                gap: 4px;
=======
        /* ============================================
           RESPONSIVE DESIGN - MOBILE FIRST APPROACH
           ============================================ */

        /* Mobile Sidebar Off-Canvas (max-width: 992px) */
        @media (max-width: 992px) {
            /* Show mini sidebar as thin tab on mobile/tablet */
            .mini-sidebar {
                display: flex !important;
                width: 80px !important;
                padding: 20px 12px !important;
                overflow: hidden !important;
            }

            .mini-sidebar.expanded {
                width: 280px !important;
                padding: 20px 12px !important;
            }

            /* Hide off-canvas sidebar overlay on mobile since we use mini-sidebar */
            .sidebar-overlay {
                display: none !important;
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                bottom: 0 !important;
                width: 260px !important;
                max-width: 85vw !important;
                background: linear-gradient(180deg, #2B11DB 0%, #4a2ba8 100%) !important;
                box-shadow: 2px 0 16px rgba(0,0,0,0.25) !important;
                transform: translateX(-100%) !important;
                transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.4s ease !important;
                z-index: 75 !important;
                padding: 0 !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch !important;
                visibility: hidden !important;
            }

            .sidebar-overlay.active {
                transform: translateX(0) !important;
                visibility: visible !important;
            }

            /* Overlay backdrop hidden on mobile */
            .overlay-backdrop {
                display: none !important;
                position: fixed !important;
                inset: 0 !important;
                background: rgba(0,0,0,0.5) !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transition: opacity 0.4s ease, visibility 0.4s ease !important;
                z-index: 60 !important;
                will-change: opacity, visibility !important;
            }

            .overlay-backdrop.active {
                opacity: 1 !important;
                visibility: visible !important;
            }

            /* Sidebar header styling */
            .sidebar-overlay > div:first-child {
                background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
                backdrop-filter: blur(10px);
                padding: 16px 14px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                position: sticky;
                top: 0;
                z-index: 101;
                flex-shrink: 0;
            }

            .sidebar-overlay > div:first-child > div {
                display: flex;
                align-items: center;
                gap: 8px;
                color: white;
                font-weight: 700;
                font-size: 14px;
                letter-spacing: 0.5px;
                flex: 1;
            }

            .sidebar-overlay > div:first-child .bi {
                font-size: 16px;
            }

            /* Sidebar list styling for mobile */
            .sidebar-list {
                padding: 0 !important;
                margin: 0 !important;
            }

            .sidebar-list li {
                border-bottom: 1px solid rgba(255,255,255,0.1) !important;
            }

            .sidebar-list a {
                color: white !important;
                padding: 14px 16px !important;
                display: flex !important;
                align-items: center !important;
                gap: 12px !important;
                font-size: 14px !important;
                transition: all 0.2s ease !important;
            }

            .sidebar-list a:hover {
                background: rgba(255,255,255,0.1) !important;
                padding-left: 18px !important;
            }

            .sidebar-list .sidebar-icon {
                width: 24px !important;
                height: 24px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 20px !important;
                color: #00D7B3 !important;
                flex-shrink: 0 !important;
            }

            .sidebar-list .sidebar-label {
                flex: 1 !important;
                color: rgba(255,255,255,0.95) !important;
            }

            .sidebar-sublist {
                background: rgba(0,0,0,0.15) !important;
                margin: 0 !important;
                padding: 0 !important;
                border-left: 2px solid #00D7B3 !important;
            }

            .sidebar-sublist a {
                padding-left: 40px !important;
                font-size: 13px !important;
                color: rgba(255,255,255,0.85) !important;
            }

            .sidebar-sublist a:hover {
                background: rgba(255,255,255,0.1) !important;
            }

            /* Mobile sub-toggle button styling */
            .sidebar-list .sub-toggle {
                position: absolute !important;
                right: 16px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                background: transparent !important;
                border: none !important;
                color: #00D7B3 !important;
                cursor: pointer !important;
                padding: 4px !important;
                width: 28px !important;
                height: 28px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 4px !important;
                box-shadow: none !important;
                transition: all 0.2s ease !important;
                font-size: 0 !important;
                z-index: 10 !important;
            }

            .sidebar-list .sub-toggle:hover {
                background: rgba(255, 255, 255, 0.1) !important;
                transform: translateY(-50%) scale(1.1) !important;
            }

            .sidebar-list .sub-toggle:active {
                transform: translateY(-50%) scale(0.95) !important;
            }

            .sidebar-list .sub-toggle .bi {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                font-size: 14px !important;
                display: inline-flex !important;
                color: #00D7B3 !important;
            }

            .sidebar-list .sub-toggle[aria-expanded="true"] .bi {
                transform: rotate(180deg) !important;
            }

            /* Mobile nested-toggle button styling */
            .sidebar-list .nested-toggle {
                position: absolute !important;
                right: 16px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                background: transparent !important;
                border: none !important;
                color: #00D7B3 !important;
                cursor: pointer !important;
                padding: 4px !important;
                width: 28px !important;
                height: 28px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                border-radius: 4px !important;
                box-shadow: none !important;
                transition: all 0.2s ease !important;
                font-size: 0 !important;
                z-index: 10 !important;
            }

            .sidebar-list .nested-toggle:hover {
                background: rgba(255, 255, 255, 0.1) !important;
                transform: translateY(-50%) scale(1.1) !important;
            }

            .sidebar-list .nested-toggle:active {
                transform: translateY(-50%) scale(0.95) !important;
            }

            .sidebar-list .nested-toggle .bi {
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                font-size: 14px !important;
                display: inline-flex !important;
                color: #00D7B3 !important;
            }

            .sidebar-list .nested-toggle[aria-expanded="true"] .bi {
                transform: rotate(90deg) !important;
            }

            /* Mobile nested-sublist styling */
            .sidebar-nested-sublist {
                background: rgba(0, 0, 0, 0.15) !important;
                margin: 0 0 0 0 !important;
                padding: 0 !important;
                list-style: none !important;
                max-height: 500px !important;
                overflow: hidden !important;
                transition: max-height 0.3s ease, opacity 0.3s ease !important;
                opacity: 1 !important;
                border-left: 2px solid #00D7B3 !important;
            }

            .sidebar-nested-sublist.collapsed {
                max-height: 0 !important;
                opacity: 0 !important;
                overflow: hidden !important;
            }

            .sidebar-nested-sublist li {
                padding: 0 !important;
                border: none !important;
            }

            .sidebar-nested-sublist a {
                color: rgba(255, 255, 255, 0.85) !important;
                font-size: 13px !important;
                padding: 10px 12px 10px 28px !important;
                display: block !important;
                text-decoration: none !important;
                position: relative !important;
                transition: all 0.25s ease !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }

            .sidebar-nested-sublist a::before {
                content: '' !important;
                position: absolute !important;
                left: 8px !important;
                top: 50% !important;
                transform: translateY(-50%) !important;
                width: 6px !important;
                height: 6px !important;
                background: #00D7B3 !important;
                border-radius: 50% !important;
                box-shadow: none !important;
            }

            .sidebar-nested-sublist a:hover {
                color: white !important;
                background: rgba(255, 255, 255, 0.1) !important;
                padding-left: 32px !important;
                transform: none !important;
            }

            /* Browse toggle (hamburger) hidden - using mini-sidebar instead */
            .browse-toggle {
                display: none !important;
                left: 12px;
                top: 65px;
                z-index: 90;
                background: rgba(255, 255, 255, 0.15);
                border: none;
                color: white;
                font-weight: 700;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                gap: 6px;
                width: 44px;
                height: 44px;
                padding: 0;
                cursor: pointer;
                font-size: 16px;
                line-height: 1;
                transform: none;
                border-radius: 6px;
                transition: all 0.3s ease;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }

            .browse-toggle:hover {
                background: rgba(255, 255, 255, 0.25);
            }

            .browse-toggle:active {
                transform: scale(0.95);
            }

            .browse-toggle .bi {
                font-size: 20px;
                line-height: 1;
            }

            .browse-toggle .browse-text {
                display: none;
                white-space: nowrap;
            }

            .browse-toggle.expanded .browse-text {
                display: none;
            }

            /* Prevent body scroll when sidebar is open */
            body.sidebar-open {
                overflow: hidden;
                height: 100vh;
            }

            /* Ensure close button is visible */
            .sidebar-close {
                display: flex !important;
                position: absolute;
                top: 12px;
                right: 12px;
                z-index: 100;
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                cursor: pointer;
                font-size: 24px;
                padding: 4px 8px;
                width: auto;
                height: auto;
                border-radius: 4px;
                transition: background 0.2s ease;
                line-height: 1;
            }

            .sidebar-close:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            /* Main content shifts for sidebar on tablet */
            main,
            .category-container,
            section,
            footer {
                margin-left: 0 !important;
            }
        }

        /* Mobile (320px - 480px) */
        @media (max-width: 480px) {
            body {
                padding-top: 140px;
            }

            /* Hamburger menu positioning */
            .browse-toggle {
                position: fixed !important;
                left: 12px !important;
                top: 65px !important;
                z-index: 95 !important;
                transform: none !important;
                background: rgba(255, 255, 255, 0.15) !important;
                border-radius: 6px !important;
                padding: 0 !important;
                width: 44px !important;
                height: 44px !important;
                font-size: 16px !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 6px !important;
                line-height: 1 !important;
                border: none !important;
                cursor: pointer !important;
                transition: all 0.3s ease !important;
                -webkit-tap-highlight-color: transparent !important;
                visibility: visible !important;
            }

            .browse-toggle:hover {
                background: rgba(255, 255, 255, 0.25) !important;
            }

            .browse-toggle:active {
                transform: scale(0.95) !important;
            }

            .browse-toggle .bi {
                font-size: 20px !important;
            }

            .browse-toggle .browse-text {
                display: none !important;
            }

            .browse-toggle i {
                display: block;
            }
            
            body {
                padding-top: 140px;
            }

            .header-top {
                flex-direction: column;
                gap: 12px;
                padding: 0 12px;
                margin-bottom: 8px;
            }

            .logo {
                width: 100%;
                justify-content: center;
                margin-bottom: 4px;
            }

            .logo-box img {
                height: 40px;
            }

            .header-contact {
                display: none;
            }

            .search-bar {
                width: 100%;
                max-width: 100%;
                padding: 0 12px;
            }

            .search-bar input {
                height: 38px;
                font-size: 14px;
            }

            .right-actions {
                width: 100%;
                justify-content: center;
                gap: 8px;
                padding: 0 12px;
                margin-left: 0;
            }

            .inquiry-btn,
            .cart-icon-wrapper {
                padding: 8px 12px;
                font-size: 13px;
            }

            .nav-inner {
                padding: 0 12px;
                padding-left: 60px;
                gap: 16px;
                min-height: 52px;
                justify-content: flex-start;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .browse-toggle {
                padding: 6px 10px;
                font-size: 13px;
                left: 8px;
                top: 50%;
                gap: 6px;
            }

            .nav-list {
                gap: 18px;
                padding: 0 4px;
            }

            .nav-list a {
                font-size: 13px;
                padding: 12px 13px;
                white-space: nowrap;
            }

            .nav-dropdown {
                min-width: calc(100vw - 32px);
                max-width: calc(100vw - 32px);
                left: 50%;
                transform: translateX(-50%) translateY(8px);
                position: fixed;
                z-index: 115;
            }

            nav li:nth-child(3) .nav-dropdown {
                min-width: calc(100vw - 32px);
                max-width: calc(100vw - 32px);
                padding: 16px;
            }

            nav li:nth-child(3) .nav-dropdown ul {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 8px 12px !important;
            }

            .contact-popover {
                width: calc(100vw - 32px);
                max-width: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(-6px) scale(0.98);
            }

            .hero {
                aspect-ratio: 4 / 3;
                min-height: 200px;
                max-height: 400px;
                padding: 40px 12px;
                margin-bottom: 30px;
>>>>>>> d52df9b9871610028cefd7cba29680d74357b1f1
            }

            .hero h1 {
                font-size: 24px;
                margin-bottom: 10px;
            }

            .hero p {
                font-size: 14px;
                margin-bottom: 20px;
            }

            section {
                padding: 40px 12px;
            }

            section h2 {
                font-size: 28px;
                margin-bottom: 16px;
            }

            .section-description {
                font-size: 14px;
                margin: 0 auto 30px;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                margin-bottom: 30px;
            }

            .product-card {
                border-radius: 8px;
            }

            .product-image {
                aspect-ratio: 4 / 3;
                min-height: 160px;
            }

            .product-info {
                padding: 16px;
            }

            .product-info h3 {
                font-size: 15px;
                margin-bottom: 8px;
            }

            .product-info p {
                font-size: 12px;
            }

            .featured-section {
                grid-template-columns: 1fr;
                gap: 24px;
                padding: 32px 16px;
                border-radius: 12px;
                margin: 0 auto;
            }

            .featured-badge {
                font-size: 10px;
                padding: 6px 12px;
                margin-bottom: 12px;
            }

            .featured-content h3 {
                font-size: 22px;
                margin-bottom: 6px;
            }

            .featured-content h3::after {
                width: 40px;
                height: 3px;
                margin-top: 12px;
                margin-bottom: 16px;
            }

            .featured-meta {
                gap: 12px;
                margin-bottom: 16px;
                padding-bottom: 12px;
                flex-direction: column;
            }

            .featured-event-info {
                flex-direction: column;
                gap: 10px;
            }

            .featured-event-detail {
                font-size: 12px;
            }

            .featured-content p {
                font-size: 13px;
                margin-bottom: 16px;
            }

            .featured-btn {
                padding: 12px 24px;
                font-size: 13px;
                width: 100%;
                text-align: center;
            }

            .featured-image {
                aspect-ratio: 4 / 3;
                min-height: 160px;
                border-radius: 8px;
            }

            footer {
                width: 100%;
                padding: 30px 12px;
            }

            .footer-links {
                flex-direction: column;
                gap: 10px;
                margin-bottom: 16px;
                font-size: 12px;
            }

            .footer-copyright {
                font-size: 11px;
                padding-top: 12px;
            }

            .sidebar-overlay {
                width: 85vw;
                max-width: 100%;
            }

            .mini-sidebar {
                display: flex !important;
                width: 80px !important;
            }
        }

        /* Tablet (481px - 1024px) */
        @media (min-width: 481px) and (max-width: 1024px) {
            body {
                padding-top: 142px;
            }

            /* Hide hamburger menu on tablet - using mini-sidebar instead */
            .browse-toggle {
                display: none !important;
            }

            body {
                padding-top: 142px;
            }

            .header-top {
                flex-wrap: wrap;
                gap: 12px;
                padding: 0 16px;
                margin-bottom: 10px;
            }

            .logo {
                flex: 0 0 auto;
                margin-bottom: 0;
            }

            .logo-box img {
                height: 45px;
            }

            .header-contact {
                display: flex;
                font-size: 12px;
                gap: 12px;
                flex: 1 1 auto;
                justify-content: center;
            }

            .contact-link {
                font-size: 12px;
                white-space: nowrap;
            }

            .search-bar {
                flex: 1 1 auto;
                max-width: 400px;
            }

            .search-bar input {
                height: 38px;
                font-size: 14px;
            }

            .right-actions {
                gap: 10px;
            }

            .inquiry-btn,
            .cart-icon-wrapper {
                padding: 8px 14px;
                font-size: 13px;
            }

            .nav-inner {
                padding: 0 16px;
                padding-left: 120px;
                gap: 16px;
                min-height: 50px;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .nav-list {
                gap: 20px;
            }

            .nav-list a {
                font-size: 14px;
                padding: 10px 12px;
            }

            .nav-dropdown {
                min-width: 260px;
                left: 50%;
                transform: translateX(-50%) translateY(8px);
            }

            nav li:nth-child(3) .nav-dropdown {
                min-width: 550px;
                max-width: calc(100vw - 40px);
            }

            nav li:nth-child(3) .nav-dropdown ul {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 10px 16px !important;
            }

            nav li:nth-child(3) .nav-dropdown ul a img {
                max-width: 75px;
                max-height: 40px;
            }

            .hero {
                aspect-ratio: 16 / 9;
                min-height: 250px;
                max-height: 500px;
                padding: 50px 16px;
                margin-bottom: 35px;
            }

            .hero h1 {
                font-size: 36px;
                margin-bottom: 16px;
            }

            .hero p {
                font-size: 16px;
                margin-bottom: 24px;
            }

            section {
                padding: 50px 16px;
            }

            section h2 {
                font-size: 38px;
                margin-bottom: 18px;
            }

            .section-description {
                font-size: 15px;
                max-width: 600px;
                margin: 0 auto 40px;
            }

            .highlights-grid {
                grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
                gap: 25px;
                margin-bottom: 40px;
            }

            .product-image {
                aspect-ratio: 16 / 9;
                min-height: 240px;
            }

            .product-info {
                padding: 18px;
            }

            .product-info h3 {
                font-size: 15px;
                margin-bottom: 8px;
            }

            .featured-section {
                grid-template-columns: 1fr 1fr;
                gap: 40px;
                padding: 50px 32px;
                border-radius: 14px;
                margin: 0 auto;
            }

            .featured-badge {
                font-size: 11px;
                padding: 8px 14px;
                margin-bottom: 16px;
            }

            .featured-content h3 {
                font-size: 28px;
                margin-bottom: 8px;
            }

            .featured-content h3::after {
                width: 50px;
                height: 4px;
                margin-top: 14px;
                margin-bottom: 20px;
            }

            .featured-meta {
                gap: 20px;
                margin-bottom: 20px;
                padding-bottom: 20px;
            }

            .featured-event-info {
                flex-wrap: wrap;
                gap: 16px;
            }

            .featured-event-detail {
                font-size: 13px;
            }

<<<<<<< HEAD
            .footer-links {
                flex-direction: column;
                gap: 10px;
            }

            section {
                padding: 40px 16px;
                text-align: center;
            }

            .container {
                padding: 0 12px;
                margin: 0 auto;
                width: 100%;
                box-sizing: border-box;
            }

            .highlights-grid {
                grid-template-columns: 1fr;
                gap: 20px;
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
=======
            .featured-content p {
                font-size: 14px;
>>>>>>> d52df9b9871610028cefd7cba29680d74357b1f1
                margin-bottom: 20px;
            }

            .featured-btn {
                padding: 12px 32px;
                font-size: 13px;
            }

            .featured-image {
                aspect-ratio: 16 / 9;
                min-height: 260px;
                border-radius: 12px;
            }

            footer {
                width: 100%;
                padding: 35px 16px;
            }

            .footer-links {
                flex-wrap: wrap;
                gap: 24px;
                margin-bottom: 16px;
                font-size: 12px;
            }

            .footer-copyright {
                font-size: 12px;
                padding-top: 16px;
            }

            .sidebar-overlay {
                width: 320px;
                max-width: 80vw;
            }

            .mini-sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .category-container {
                margin-left: 0 !important;
            }
        }

        /* Desktop (1025px and above) */
        @media (min-width: 1025px) {
            .nav-dropdown {
                min-width: 280px;
            }

            nav li:nth-child(3) .nav-dropdown {
                min-width: 650px;
                max-width: 650px;
            }

            .highlights-grid {
                grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            }

            .featured-section {
                grid-template-columns: 1fr 1fr;
            }

            /* Desktop: Show mini sidebar */
            .mini-sidebar {
                display: flex !important;
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
                flex-direction: column;
                align-items: center;
            }

            /* Desktop: Hide browse toggle */
            .browse-toggle {
                display: none !important;
            }

            /* Desktop: Hide overlay */
            .overlay-backdrop {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }

            /* Desktop: Hide sidebar overlay */
            .sidebar-overlay {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
                transform: translateX(-100%) !important;
            }

            /* Desktop: Adjust main content for mini sidebar */
            main,
            .category-container {
                margin-left: 80px;
            }

            .mini-sidebar.expanded {
                width: 280px;
                align-items: stretch;
            }

            .mini-sidebar.expanded ~ main,
            .mini-sidebar.expanded ~ .category-container {
                margin-left: 280px;
            }
        }

        /* Overlay sidebar (full-height left panel) - Initially hidden */
        .overlay-backdrop {
            display: none !important;
        }

        .sidebar-overlay {
            display: none !important;
        }

        /* Default mini-sidebar state */
        .mini-sidebar {
            display: flex !important;
            visibility: visible !important;
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
            .nav-inner { justify-content: space-between; padding-left: 0 !important; }
            .nav-list { position: static; transform: none; left: auto; margin: 8px auto 0; justify-content: center; flex-wrap: wrap; }
            .browse-toggle { position: fixed !important; transform: none; left: 12px !important; top: 65px !important; padding: 0 !important; width: 44px !important; height: 44px !important; z-index: 95 !important; }
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

        .overlay-backdrop {
            display: none !important;
            position: fixed;
            opacity: 0;
            visibility: hidden;
        }

        .overlay-backdrop.active {
            opacity: 1 !important;
            visibility: visible !important;
        }

        .sidebar-overlay {
            display: none !important;
            position: fixed;
            transform: translateX(-100%);
            visibility: hidden;
        }

        .sidebar-overlay.active {
            transform: translateX(0) !important;
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

        /* Mini Sidebar (always visible icon bar) - Desktop default */
        .mini-sidebar {
            position: fixed;
            left: 0;
            top: calc(14px + 50px + 14px);
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
            align-items: stretch;
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

        /* Adjust main container for mini sidebar — starts expanded on desktop */
        .page-content,
        .main-content, .category-container {
            margin-left: 280px;
            transition: margin-left 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* When collapsed (not expanded), reduce margin */
        .mini-sidebar:not(.expanded) ~ .page-content,
        .mini-sidebar:not(.expanded) ~ .main-content,
        .mini-sidebar:not(.expanded) ~ .category-container {
            margin-left: 80px;
        }

        .mini-sidebar.expanded ~ .page-content,
        .mini-sidebar.expanded ~ .main-content,
        .mini-sidebar.expanded ~ .category-container {
            margin-left: 280px;
        }

        @media (max-width: 768px) {
            .page-content, section, footer,
            .main-content, .category-container {
                margin-left: 0 !important;
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
    </style>
</head>
<body>
        <?php
        // Set page title
        $page_title = "Home";
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
            <button class="browse-toggle" aria-label="Toggle navigation menu" aria-controls="sidebar">
                <i class="bi bi-list"></i>
                <span class="browse-text">Browse</span>
            </button>
            <div class="nav-inner">
                <ul class="nav-list">
                    <li>
                        <a href="home.php" class="active">Home</a>
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
                <a href="arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
                <button class="sub-toggle" aria-controls="sub-arc-welding" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                    <li><a href="arc-welding-machine/plasma-cutting-machine.php">Plasma Cutting Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-controls="sub-arc-robots" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-arc-robots" class="sidebar-sublist collapsed">
                    <li><a href="arc-welding-robots/g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="arc-welding-robots/g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="arc-welding-robots/featured-products-and-solution.php">Featured Products and Solutions</a></li>
                    <li><a href="arc-welding-robots/robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-controls="sub-batteries" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="batteries/maintenance-free.php">Maintenance Free</a></li>
                    <li><a href="batteries/low-maintenance.php">Low Maintenance</a></li>
                    <li><a href="batteries/special-batteries.php">Special Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
                <button class="sub-toggle" aria-controls="sub-drilling-lifting" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li><a href="drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a></li>
                    <li><a href="drilling-and-lifting/cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
                <button class="sub-toggle" aria-controls="sub-gas-detectors" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                    <li><a href="gas-detectors/portable-gas-detectors.php">Portable Gas Detectors</a></li>
                    <li><a href="gas-detectors/docking-data-management.php">Docking and Data Management</a></li>
                    <li><a href="gas-detectors/calibration-gas-regulators.php">Calibration Gas and Regulators</a></li>
                </ul>
            </li>
            <li class="">
                <a href="portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
            </li>
            <li class="has-sub">
                <a href="power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-controls="sub-power-tool" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
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
                <button class="sub-toggle" aria-controls="sub-protection-safety" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="protection/eye-protection.php">Eye Protection</a></li>
                    <li class="has-nested-sub">
                        <a href="protection/hand-protection.php">Hand Protection</a>
                        <button class="nested-toggle" aria-controls="nested-hand-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
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
                        <button class="nested-toggle" aria-controls="nested-body-protection" aria-expanded="false"><i class="bi bi-chevron-right"></i></button>
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
                <button class="sub-toggle" aria-controls="sub-welding-accessories" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
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
                <button class="sub-toggle" aria-controls="sub-welding-consumables" aria-expanded="false"><i class="bi bi-chevron-down"></i></button>
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="welding-consumables/kobelco.php">Kobelco</a></li>
                    <li><a href="welding-consumables/metrode.php">Metrode</a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <!-- Mini Sidebar (Icon Bar) -->
    <div class="mini-sidebar active expanded" id="miniSidebar">
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

    <!-- Hero Section -->
    <div class="page-content">
    <section class="hero" id="heroSlider">
        <?php for ($i = 0; $i < 4; $i++): ?>
            <?php 
                $slideClass = $i === 0 ? 'hero-slide active' : 'hero-slide';
                $slideImage = htmlspecialchars((string)($slides[$i] ?? ''), ENT_QUOTES);
            ?>
            <div class="<?php echo $slideClass; ?>" style="background-image: url('<?php echo $slideImage; ?>');">
                <div class="hero-content">
                    <div class="hero-thumb" style="background-image: url('<?php echo $slideImage; ?>');"></div>
                </div>
            </div>
        <?php endfor; ?>
        <div class="hero-indicators">
            <span class="hero-dot active" data-slide="0"></span>
            <span class="hero-dot" data-slide="1"></span>
            <span class="hero-dot" data-slide="2"></span>
            <span class="hero-dot" data-slide="3"></span>
        </div>
    </section>

    <!-- Product Highlights & News -->
    <section id="products">
        <div class="container">
            <h2>Product Highlights & News</h2>
            <p class="section-description">
                We will still keep the Youtube embeded video for product highlights. The video contents will be updated once in a while. 
                Then same as before with News, Events, and Announcements section that can be added.
            </p>

            <div class="highlights-grid">
                <?php 
                $titles = ['Revolutionizing Manufacturing Processes', 'Innovations in Sustainable Industrial Solutions'];
                $descriptions = [
                    'Discover how our innovative technology is transforming industrial manufacturing.',
                    'Learn about our commitment to eco-friendly and sustainable products.'
                ];
                for ($i = 0; $i < 2; $i++): 
                    $ytUrl = (string)($ytLinks['home_highlights'][$i] ?? '');
                    if (empty($ytUrl)) continue;
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <iframe src="<?php echo htmlspecialchars($ytUrl, ENT_QUOTES); ?>" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($titles[$i], ENT_QUOTES); ?></h3>
                        <p><?php echo htmlspecialchars($descriptions[$i], ENT_QUOTES); ?></p>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <!-- Featured Product -->
    <section>
        <div class="container">
            <div class="featured-section">
                <div class="featured-content">
                    <?php if (!empty($featured['badge'])): ?>
                        <span class="featured-badge"><?php echo htmlspecialchars((string)($featured['badge'] ?? ''), ENT_QUOTES); ?></span>
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars((string)($featured['title'] ?? ''), ENT_QUOTES); ?></h3>
                    
                    <!-- Event/Sales Meta Information -->
                    <?php if (!empty($featured['discount']) || !empty($featured['event_date']) || !empty($featured['event_location'])): ?>
                        <div class="featured-meta">
                            <?php if (!empty($featured['discount'])): ?>
                                <div class="featured-discount">
                                    <span class="featured-discount-badge"><?php echo htmlspecialchars((string)($featured['discount'] ?? ''), ENT_QUOTES); ?></span>
                                    <?php if (!empty($featured['offer_text'])): ?>
                                        <span class="featured-offer-text"><?php echo htmlspecialchars((string)($featured['offer_text'] ?? ''), ENT_QUOTES); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Event Details -->
                    <?php if (!empty($featured['event_date']) || !empty($featured['event_location'])): ?>
                        <div class="featured-event-info">
                            <?php if (!empty($featured['event_date'])): ?>
                                <div class="featured-event-detail">
                                    <i class="bi bi-calendar-event"></i>
                                    <strong><?php echo htmlspecialchars((string)($featured['event_date'] ?? ''), ENT_QUOTES); ?></strong>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($featured['event_location'])): ?>
                                <div class="featured-event-detail">
                                    <i class="bi bi-geo-alt"></i>
                                    <strong><?php echo htmlspecialchars((string)($featured['event_location'] ?? ''), ENT_QUOTES); ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Description -->
                    <?php if (!empty($featured['description'])): ?>
                        <p><?php echo htmlspecialchars((string)($featured['description'] ?? ''), ENT_QUOTES); ?></p>
                    <?php endif; ?>

                    <!-- Call-to-Action Button -->
                    <?php if (!empty($featured['button_text']) && !empty($featured['button_url'])): ?>
                        <a href="<?php echo htmlspecialchars((string)($featured['button_url'] ?? ''), ENT_QUOTES); ?>" class="featured-btn"><?php echo htmlspecialchars((string)($featured['button_text'] ?? ''), ENT_QUOTES); ?></a>
                    <?php endif; ?>
                </div>
                <div class="featured-image">
                    <?php 
                        $mType = $featured['media_type'] ?? 'picture';
                        if ($mType === 'picture'):
                            $imgPath = (string)($featured['image'] ?? '');
                            if ($imgPath !== ''):
                    ?>
                        <img src="<?php echo htmlspecialchars($imgPath, ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars((string)($featured['image_alt'] ?? ''), ENT_QUOTES); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:12px;">
                    <?php 
                            else:
                                echo '▶';
                            endif;
                        elseif ($mType === 'youtube'):
                            $ytUrl = htmlspecialchars((string)($featured['youtube_url'] ?? ''), ENT_QUOTES);
                    ?>
                        <iframe src="<?php echo $ytUrl; ?>" style="width:100%;height:100%;border:0;border-radius:12px;" allowfullscreen></iframe>
                    <?php 
                        elseif ($mType === 'video'):
                            $vidPath = (string)($featured['video_file'] ?? '');
                            if ($vidPath !== ''):
                    ?>
                        <video style="width:100%;height:100%;object-fit:cover;border-radius:12px;" controls>
                            <source src="<?php echo htmlspecialchars($vidPath, ENT_QUOTES); ?>" type="video/mp4">
                        </video>
                    <?php 
                            else:
                                echo '▶';
                            endif;
                        else:
                            echo '▶';
                        endif;
                    ?>
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
    <script>
        (function(){
            var browseToggle = document.querySelector('.browse-toggle');
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlayBackdrop');
            var closeBtn = document.getElementById('closeSidebar');

            function openSidebar(){
                sidebar.classList.add('active');
                overlay.classList.add('active');
                sidebar.setAttribute('aria-hidden','false');
                overlay.setAttribute('aria-hidden','false');
                document.body.classList.add('sidebar-open');
            }

            function closeSidebar(){
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                sidebar.setAttribute('aria-hidden','true');
                overlay.setAttribute('aria-hidden','true');
                document.body.classList.remove('sidebar-open');
            }

            if(browseToggle){
                browseToggle.addEventListener('click', function(e){ e.preventDefault(); openSidebar(); });
            }
            if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if(overlay) overlay.addEventListener('click', closeSidebar);
            document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeSidebar(); });
        })();
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
        var miniPopover = document.getElementById('miniPopover');
        var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;
        var popoverList = miniPopover ? miniPopover.querySelector('.mini-popover-list') : null;
        var currentPopoverKey = null;

        // Responsive function to show/hide browse toggle
        function updateBrowseToggleVisibility() {
            if(!browseToggle) return;
            if(window.innerWidth <= 1024) {
                browseToggle.classList.add('active');
            } else {
                browseToggle.classList.remove('active');
            }
        }

        // Initialize on load
        if(browseToggle) updateBrowseToggleVisibility();

        // Update on window resize
        window.addEventListener('resize', updateBrowseToggleVisibility);

        // Helpers for popover
        function getCategoryKeyFromTarget(dataTarget) {
            if (!dataTarget) return null;
            var keys = [
                'arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting','gas-detectors','portable-ventilators','power-tools','protection','welding-accessories','welding-consumables'
            ];
            for (var i=0;i<keys.length;i++) { if (dataTarget.indexOf('/'+keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'/') !== -1) return keys[i]; }
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
                    { label: 'MIG Welding Machine', href: base + '/arc-welding-machine/mig-welding-machine.php' },
                    { label: 'CO1/MAG Welding Machine', href: base + '/arc-welding-machine/co1-mag-welding-machine.php' },
                    { label: 'STUD Welding Machine', href: base + '/arc-welding-machine/stud-welding-machine.php' },
                    { label: 'TIG Welding Machine', href: base + '/arc-welding-machine/tig-welding-machine.php' },
                    { label: 'Plasma Cutting Machine', href: base + '/arc-welding-machine/plasma-cutting-machine.php' }
                ],
                'batteries': [
                    { label: 'Maintenance Free', href: base + '/batteries/maintenance-free.php' },
                    { label: 'Low Maintenance', href: base + '/batteries/low-maintenance.php' },
                    { label: 'Special Batteries', href: base + '/batteries/special-batteries.php' }
                ],
                'drilling-and-lifting': [
                    { label: 'Lifting', href: base + '/drilling-and-lifting/lifting.php' },
                    { label: 'Magnetic Drill', href: base + '/drilling-and-lifting/magnetic-drill.php' },
                    { label: 'Cutters', href: base + '/drilling-and-lifting/cutters.php' }
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

        // Close on outside click / Escape
        document.addEventListener('click', function(e){
            if (!miniPopover) return;
            if (!miniPopover.classList.contains('show')) return;
            if (e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;
            hidePopover();
        });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hidePopover(); });

        // Browse toggle click
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

        // Expand/collapse sidebar when clicking expand button
        expandBtn.addEventListener('click', function() {
            miniSidebar.classList.toggle('expanded');
            if(browseToggle) browseToggle.classList.toggle('expanded');
        });

        // On mobile, collapse by default
        if(window.innerWidth <= 768) {
            miniSidebar.classList.remove('expanded');
            if(browseToggle) browseToggle.classList.remove('expanded');
        }

        // Menu bar click handler
        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar) {
            menuBar.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        // ARROW CLICK HANDLER
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
            if (e.target.closest('.sub-indicator')) {
                arrowHandler(e);
            }
        }, true);

        // Mini icon navigation
        miniIcons.forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                if (e.target.closest('.sub-indicator')) {
                    e.stopPropagation();
                    return;
                }
                
                var target = this.getAttribute('data-target');
                if (target) {
                    window.location.href = target;
                }
            }, true);
        });

        // Close sidebar backdrop click
        backdrop.addEventListener('click', function() {
            if(mainSidebar.classList.contains('active')) {
                mainSidebar.classList.remove('active');
                backdrop.classList.remove('active');
            }
        });

        // Close sidebar button click
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
</body>
</html>

