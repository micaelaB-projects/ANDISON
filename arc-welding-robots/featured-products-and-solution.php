<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$page_title = "Featured Products and Solutions";
$category_id = "arc-welding-robots";
$subcategory_id = "featured-products-and-solution";
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
    $current_category = array(
        'id' => $subcategory_id,
        'name' => 'Featured Products and Solutions',
        'description' => 'Explore our featured arc welding robot products and complete solutions for automated welding applications.',
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
    $category_name = $current_category['name'] ?? 'Featured Products and Solutions';
    $category_description = $current_category['description'] ?? 'Featured arc welding robot products and solutions.';
    ?>
    <title><?php echo htmlspecialchars($category_name); ?> - Arc Welding Robots - ANDISON INDUSTRIAL</title>
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
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo i {
            font-size: 28px;
        }

        .header-contact {
            margin-left: auto;
            display: flex;
            gap: 20px;
            font-size: 14px;
        }

        .header-contact a {
            color: white;
            text-decoration: none;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 20px;
            padding: 0;
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

        /* Sidebar */
        .sidebar-overlay {
            position: fixed;
            top: 142px;
            left: 0;
            width: 280px;
            height: calc(100vh - 142px);
            background: white;
            z-index: 999;
            overflow-y: auto;
            transform: translateX(-100%);
            transition: transform 0.3s;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar-overlay.active {
            transform: translateX(0);
        }

        .overlay-backdrop {
            position: fixed;
            top: 142px;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            z-index: 998;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .overlay-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #4b5563;
        }

        .sidebar-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-list > li {
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }

        .sidebar-list > li.has-sub > a {
            display: flex;
            align-items: center;
            padding: 12px 12px;
            color: #1f2937;
            text-decoration: none;
            transition: background 0.2s;
            gap: 8px;
            flex: 1;
        }

        .sidebar-list > li > a {
            display: flex;
            align-items: center;
            padding: 12px 12px;
            color: #1f2937;
            text-decoration: none;
            transition: background 0.2s;
            gap: 8px;
        }

        .sidebar-list > li > a:hover,
        .sidebar-list > li.active > a {
            background: #f3f4f6;
        }

        .sidebar-icon {
            font-size: 18px;
            color: #2B11DB;
            width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-label {
            flex: 1;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-arrow {
            font-size: 12px;
            color: #9ca3af;
        }

        .sub-toggle {
            background: none;
            border: none;
            font-size: 14px;
            cursor: pointer;
            padding: 8px 12px;
            color: #6b7280;
            transition: transform 0.2s;
        }

        .sub-toggle[aria-expanded="true"] {
            transform: rotate(90deg);
        }

        .sidebar-sublist {
            list-style: none;
            padding: 0;
            margin: 0;
            background: #f9fafb;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s;
        }

        .sidebar-sublist.expanded {
            max-height: 500px;
        }

        .sidebar-sublist li {
            border: none;
        }

        .sidebar-sublist a {
            display: block;
            padding: 10px 36px;
            color: #4b5563;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar-sublist a:hover {
            background: #f3f4f6;
            border-left-color: #2B11DB;
        }

        .category-container {
            flex: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            width: 100%;
        }

        .category-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .category-header h1 {
            font-size: 32px;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .category-header p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* Footer */
        footer {
            background: #1f2937;
            color: white;
            margin-top: auto;
            padding: 30px 20px;
            text-align: center;
            font-size: 14px;
        }

        footer a {
            color: #60a5fa;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header and Navigation -->
    <header>
        <div class="header-top">
            <a href="../home.php" class="logo">
                <i class="bi bi-wrench-adjustable"></i>
                <span>ANDISON</span>
            </a>
            <div class="header-contact">
                <a href="tel:<?php echo urlencode($phone); ?>"><?php echo htmlspecialchars($phone); ?></a>
                <a href="tel:<?php echo urlencode($phone2); ?>"><?php echo htmlspecialchars($phone2); ?></a>
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
                </li>
                <li>
                    <a href="../contact.php">Contact Us</a>
                </li>
            </ul>
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
            <li class="has-sub active">
                <a href="arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-robot" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-robot" class="sidebar-sublist collapsed">
                    <li><a href="g3-controller-series.php">G3 Controller Series</a></li>
                    <li><a href="g4-controller-series.php">G4 Controller Series</a></li>
                    <li><a href="featured-products-and-solution.php">Featured Products and Solutions</a></li>
                    <li><a href="robot-system-peripherals.php">Robot System Peripherals</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-batteries" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-batteries" class="sidebar-sublist collapsed">
                    <li><a href="../batteries/maintenance-free.php">Maintenance Free Batteries</a></li>
                    <li><a href="../batteries/low-maintenance.php">Low Maintenance Batteries</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling &amp; Lifting</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-drilling-lifting" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="../drilling-and-lifting/lifting.php">Lifting</a></li>
                    <li><a href="../drilling-and-lifting/magnetic-drill.php">Magnetic Drill</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../gas-detectors/portable-gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Portable Gas Detectors</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-gas-detectors" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="../gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="../gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                </ul>
            </li>
            <li>
                <a href="../portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
            </li>
            <li class="has-sub">
                <a href="../power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-power-tool" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-power-tool" class="sidebar-sublist collapsed">
                    <li><a href="../power-tools/grinder.php">Grinder</a></li>
                    <li><a href="../power-tools/saw.php">Saw</a></li>
                    <li><a href="../power-tools/drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="../power-tools/rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="../power-tools/accessories.php">Power Tools Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-protection-safety" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                </ul>
            </li>
            <li class="has-sub">
                <<a href="../welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
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
            </li>
        </ul>
    </aside>

    <div class="category-container">
        <div class="category-header">
            <h1><?php echo $category_name; ?></h1>
            <p><?php echo $category_description; ?></p>
        </div>
    </div>

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
    </script>
</body>
</html>
