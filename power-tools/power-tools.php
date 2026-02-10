<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$page_title = "Power Tools";
$category_id = "power-tools";
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
    $category_name = $current_category['name'] ?? 'Power Tools';
    $category_description = $current_category['description'] ?? 'Explore our comprehensive range of power tools for industrial welding and fabrication applications.';
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
            cursor: pointer;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 20px;
            padding: 0;
            margin-left: auto;
            display: none;
        }

        nav {
            background: #1a0f7c;
            display: flex;
            justify-content: center;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        nav li {
            position: relative;
        }

        nav > ul > li > a {
            display: block;
            padding: 12px 16px;
            color: white;
            text-decoration: none;
            transition: background 0.3s;
        }

        nav > ul > li > a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            color: #333;
            min-width: 250px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s;
            z-index: 1000;
        }

        nav li:hover .nav-dropdown {
            opacity: 1;
            visibility: visible;
        }

        .nav-dropdown h4 {
            padding: 12px 16px;
            border-bottom: 1px solid #e5e7eb;
            margin: 0;
            font-size: 16px;
        }

        .nav-dropdown ul {
            display: flex;
            flex-direction: column;
            gap: 0;
            padding: 8px 0;
        }

        .nav-dropdown li {
            width: 100%;
        }

        .nav-dropdown a {
            display: block;
            padding: 10px 16px;
            color: #333;
            text-decoration: none;
            transition: background 0.2s;
        }

        .nav-dropdown a:hover {
            background: #f3f4f6;
        }

        nav li:nth-child(3) .nav-dropdown {
            left: auto;
            right: -180px;
            width: 400px;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            padding: 8px 0;
        }

        nav li:nth-child(3) .nav-dropdown li {
            width: 100%;
        }

        nav li:nth-child(3) .nav-dropdown a {
            padding: 10px 16px;
        }

        nav li:nth-child(3) .nav-dropdown ul li:nth-child(n+4) {
            border-top: 1px solid #e5e7eb;
        }

        nav li:nth-child(3) .nav-dropdown h4 {
            grid-column: 1 / -1;
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
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }

        .product-info {
            padding: 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-name {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .product-price {
            color: #2B11DB;
            font-weight: 700;
            font-size: 16px;
        }

        /* Sidebar */
        .sidebar-overlay {
            display: none;
        }

        .overlay-backdrop {
            display: none;
        }

        /* Footer */
        footer {
            background: #1f2937;
            color: white;
            margin-top: auto;
            padding: 40px 20px;
            text-align: center;
            font-size: 14px;
        }

        footer p {
            margin: 0;
        }

        footer a {
            color: #60a5fa;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 1024px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .header-contact {
                display: none;
            }

            .sidebar-toggle {
                display: block;
            }

            .sidebar-overlay {
                display: block;
            }

            .category-container {
                padding: 0 20px;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            nav {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr;
            }

            .category-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <header>
        <div class="header-top">
            <div class="logo">
                <a href="../home.php" class="logo-box">ANDISON</a>
            </div>
            <div class="header-contact">
                <div class="contact-dropdown">
                    <a href="tel:<?php echo urlencode($phone); ?>" class="contact-link"><?php echo htmlspecialchars($phone); ?></a>
                    <div class="contact-popover">
                        <button class="contact-close" id="contact-close-1">×</button>
                        <p>Reach us by phone during business hours.</p>
                        <p><strong><?php echo htmlspecialchars($phone); ?></strong></p>
                    </div>
                </div>
                <div class="contact-dropdown">
                    <a href="tel:<?php echo urlencode($phone2); ?>" class="contact-link"><?php echo htmlspecialchars($phone2); ?></a>
                    <div class="contact-popover">
                        <button class="contact-close" id="contact-close-2">×</button>
                        <p>Alternative contact number for urgent inquiries.</p>
                        <p><strong><?php echo htmlspecialchars($phone2); ?></strong></p>
                    </div>
                </div>
                <div class="contact-dropdown">
                    <a href="tel:<?php echo urlencode($phone3); ?>" class="contact-link"><?php echo htmlspecialchars($phone3); ?></a>
                    <div class="contact-popover">
                        <button class="contact-close" id="contact-close-3">×</button>
                        <p>International contact line.</p>
                        <p><strong><?php echo htmlspecialchars($phone3); ?></strong></p>
                    </div>
                </div>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle navigation menu">
                <i class="bi bi-list"></i>
            </button>
        </div>
        <nav aria-label="Main navigation">
            <ul>
                <li>
                    <a href="../home.php">Home</a>
                </li>
                <li>
                    <a href="../aboutus.php">About</a>
                </li>
                <li>
                    <a href="../brand.php">Brands</a>
                    <div class="nav-dropdown">
                        <h4>Featured Brands</h4>
                        <ul>
                            <li><a href="../brand.php?name=KEMPPI"><img src="../assets/brands/KEMPPI.jpg" alt="Kemppi" title="Kemppi"></a></li>
                            <li><a href="../brand.php?name=HYPERTHERM"><img src="../assets/brands/HYPERTHERM.jpg" alt="Hypertherm" title="Hypertherm"></a></li>
                            <li><a href="../brand.php?name=OTC"><img src="../assets/brands/OTC.jpg" alt="OTC" title="OTC"></a></li>
                            <li><a href="../brand.php?name=PUMA"><img src="../assets/brands/PUMA.jpg" alt="PUMA" title="PUMA"></a></li>
                            <li><a href="../brand.php?name=MILCO"><img src="../assets/brands/MILCO.jpg" alt="Milco" title="Milco"></a></li>
                            <li><a href="../brand.php?name=AMETEK"><img src="../assets/brands/AMETEK.jpg" alt="AMETEK" title="AMETEK"></a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="../contact.php">Contact Us</a>
                </li>
            </ul>
        </nav>
    </header>

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
            </li>
            <li class="has-sub">
                <a href="../arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-robot" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
            </li>
            <li class="has-sub">
                <a href="../batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-batteries" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
            </li>
            <li class="has-sub">
                <a href="../drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling &amp; Lifting</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-drilling-lifting" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
            </li>
            <li class="has-sub">
                <a href="../gas-detectors/portable-gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Portable Gas Detectors</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-gas-detectors" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
            </li>
            <li class="has-sub">
                <a href="../portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-ventilators" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
            </li>
            <li class="has-sub active">
                <a href="power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-power-tools" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-power-tools" class="sidebar-sublist collapsed">
                    <li><a href="grinder.php">Grinder</a></li>
                    <li><a href="saw.php">Saw</a></li>
                    <li><a href="drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="accessories.php">Power Tools Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Protection and Safety</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-protection-safety" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
            </li>
            <li class="has-sub">
                <a href="../welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-welding-accessories" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
            </li>
            <li class="has-sub">
                <a href="../welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
            </li>
        </ul>
    </aside>

    <!-- Overlay Backdrop -->
    <div class="overlay-backdrop" id="overlayBackdrop"></div>

    <!-- Hero Section -->
    <section class="hero" id="heroSlider">
        <div style="display: none;"></div>
    </section>

    <!-- Main Content -->
    <main class="category-container">
        <div class="category-header">
            <h1><?php echo htmlspecialchars($category_name); ?></h1>
            <p><?php echo htmlspecialchars($category_description); ?></p>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 ANDISON INDUSTRIAL. All rights reserved. | <a href="../contact.php">Contact Us</a></p>
    </footer>

    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlayBackdrop = document.getElementById('overlayBackdrop');
        const subToggles = document.querySelectorAll('.sub-toggle');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
            overlayBackdrop.classList.toggle('active');
            sidebar.setAttribute('aria-hidden', sidebar.classList.contains('active') ? 'false' : 'true');
        });

        closeSidebar.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlayBackdrop.classList.remove('active');
            sidebar.setAttribute('aria-hidden', 'true');
        });

        overlayBackdrop.addEventListener('click', () => {
            sidebar.classList.remove('active');
            overlayBackdrop.classList.remove('active');
            sidebar.setAttribute('aria-hidden', 'true');
        });

        subToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.stopPropagation();
                const sublist = document.getElementById(toggle.getAttribute('aria-controls'));
                const isExpanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', !isExpanded);
                sublist.classList.toggle('expanded');
            });
        });

        // Contact popover close buttons
        document.getElementById('contact-close-1')?.addEventListener('click', (e) => {
            e.target.closest('.contact-dropdown').classList.add('closed');
        });
        document.getElementById('contact-close-2')?.addEventListener('click', (e) => {
            e.target.closest('.contact-dropdown').classList.add('closed');
        });
        document.getElementById('contact-close-3')?.addEventListener('click', (e) => {
            e.target.closest('.contact-dropdown').classList.add('closed');
        });
    </script>
</body>
</html>
