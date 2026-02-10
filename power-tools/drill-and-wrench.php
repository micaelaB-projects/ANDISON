<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';

$phone = "+1(234) 567 8900";
$email = "info@andison-industrial.com";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drill and Wrench - Welding Consumables - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { height: 100%; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; padding-top: 142px; min-height: 100vh; display: flex; flex-direction: column; background: #f8f9fa; }
        header { background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%); color: white; padding: 14px 0; position: fixed; top: 0; left: 0; right: 0; z-index: 1001; width: 100%; }
        .header-top { display: flex; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 20px; gap: 20px; margin-bottom: 12px; }
        .logo { font-size: 24px; font-weight: bold; text-decoration: none; color: white; display: flex; align-items: center; gap: 10px; }
        .header-contact { margin-left: auto; display: flex; gap: 20px; font-size: 14px; }
        .header-contact a { color: white; text-decoration: none; }
        .sidebar-toggle { background: none; border: none; color: white; cursor: pointer; font-size: 20px; padding: 0; }
        nav { background: #1a0f7c; display: flex; justify-content: center; }
        nav ul { list-style: none; display: flex; gap: 0; flex-wrap: wrap; }
        nav li { position: relative; }
        nav > ul > li > a { display: block; padding: 12px 16px; color: white; text-decoration: none; transition: background 0.3s; }
        nav > ul > li > a:hover { background: rgba(255, 255, 255, 0.1); }
        .sidebar-overlay { position: fixed; top: 142px; left: 0; width: 280px; height: calc(100vh - 142px); background: white; z-index: 999; overflow-y: auto; transform: translateX(-100%); transition: transform 0.3s; }
        .sidebar-overlay.active { transform: translateX(0); }
        .overlay-backdrop { position: fixed; top: 142px; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); opacity: 0; visibility: hidden; z-index: 998; transition: opacity 0.3s, visibility 0.3s; }
        .overlay-backdrop.active { opacity: 1; visibility: visible; }
        .sidebar-close { background: none; border: none; font-size: 20px; cursor: pointer; }
        .sidebar-list { list-style: none; padding: 0; margin: 0; }
        .sidebar-list > li { border-bottom: 1px solid #e5e7eb; position: relative; }
        .sidebar-list > li > a { display: flex; align-items: center; padding: 12px 12px; color: #1f2937; text-decoration: none; transition: background 0.2s; gap: 8px; }
        .sidebar-list > li > a:hover, .sidebar-list > li.active > a { background: #f3f4f6; }
        .sidebar-icon { font-size: 18px; color: #2B11DB; width: 20px; display: flex; }
        .sidebar-label { flex: 1; font-size: 14px; font-weight: 500; }
        .sub-toggle { background: none; border: none; font-size: 14px; cursor: pointer; padding: 8px 12px; color: #6b7280; }
        .sidebar-sublist { list-style: none; padding: 0; margin: 0; background: #f9fafb; max-height: 0; overflow: hidden; transition: max-height 0.3s; }
        .sidebar-sublist.expanded { max-height: 500px; }
        .sidebar-sublist a { display: block; padding: 10px 36px; color: #4b5563; text-decoration: none; font-size: 13px; transition: background 0.2s; }
        .sidebar-sublist a:hover { background: #f3f4f6; }
        .category-container { flex: 1; max-width: 1200px; margin: 0 auto; padding: 40px 20px; width: 100%; }
        .category-header { background: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; }
        .category-header h1 { font-size: 32px; color: #1f2937; margin-bottom: 10px; }
        .category-header p { font-size: 16px; color: #6b7280; line-height: 1.6; }
        footer { background: #1f2937; color: white; margin-top: auto; padding: 30px 20px; text-align: center; font-size: 14px; }
    </style>
</head>
<body>
    <header>
        <div class="header-top">
            <a href="../home.php" class="logo"><i class="bi bi-wrench-adjustable"></i><span>ANDISON</span></a>
            <div class="header-contact">
                <a href="tel:<?php echo urlencode($phone); ?>"><?php echo htmlspecialchars($phone); ?></a>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
        </div>
        <nav>
            <ul>
                <li><a href="../home.php">Home</a></li>
                <li><a href="../aboutus.php">About</a></li>
                <li><a href="../brand.php">Brands</a></li>
                <li><a href="../contact.php">Contact Us</a></li>
            </ul>
        </nav>
    </header>

    <div class="overlay-backdrop" id="overlayBackdrop"></div>

    <aside id="sidebar" class="sidebar-overlay" aria-hidden="true">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 16px 12px; border-bottom: 1px solid #e5e7eb;">
            <h3 style="margin: 0; font-size: 18px;">Categories</h3>
            <button class="sidebar-close" id="closeSidebar"><i class="bi bi-x-lg"></i></button>
        </div>
        <ul class="sidebar-list">
            <li class="has-sub active">
                <a href="welding-consumables.php"><span class="sidebar-icon"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-welding-consumables"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-welding-consumables" class="sidebar-sublist collapsed">
                    <li><a href="grinder.php">Grinder</a></li>
                    <li><a href="saw.php">Saw</a></li>
                    <li><a href="drill-and-wrench.php">Drill and Wrench</a></li>
                    <li><a href="rotary-and-demolition-hammer.php">Rotary and Demolition Hammer</a></li>
                    <li><a href="accessories.php">Welding Consumables Accessories</a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <div class="category-container">
        <div class="category-header">
            <h1>Drill and Wrench</h1>
            <p>Professional drill and wrench welding consumable tools for precision applications.</p>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 ANDISON INDUSTRIAL. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlayBackdrop').classList.toggle('active');
        });
        document.getElementById('closeSidebar').addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('active');
            document.getElementById('overlayBackdrop').classList.remove('active');
        });
        document.querySelectorAll('.sub-toggle').forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                const sublist = document.getElementById(toggle.getAttribute('aria-controls'));
                sublist.classList.toggle('expanded');
            });
        });
    </script>
</body>
</html>
