<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$page_title = "Others";
$category_id = "welding-accessories";
$subcategory_id = "others";
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
        'name' => 'Others',
        'description' => 'Other welding accessories and miscellaneous equipment for completing your welding setup and operations.',
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
    $category_name = $current_category['name'] ?? 'Others';
    $category_description = $current_category['description'] ?? 'Other welding accessories and miscellaneous equipment for completing your welding setup and operations.';
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
        }

        .logo-box {
            width: 40px;
            height: 40px;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .search-bar {
            flex: 1;
            display: flex;
            align-items: center;
        }

        .search-field {
            width: 100%;
            display: flex;
            align-items: center;
        }

        .search-field input {
            width: 100%;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            color: #333;
        }

        .right-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .inquiry-btn {
            background: #f97316;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .inquiry-btn:hover {
            background: #ea580c;
        }

        .contact-dropdown {
            position: relative;
            display: inline-block;
        }

        .contact-link {
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .contact-popover {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            color: #333;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 220px;
            margin-top: 4px;
            z-index: 1000;
            display: none;
        }

        .contact-dropdown:focus .contact-popover {
            display: block;
        }

        .contact-popover ul {
            list-style: none;
            padding: 8px;
        }

        .contact-popover li {
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-radius: 4px;
        }

        .contact-popover li:hover {
            background: #f3f4f6;
        }

        .contact-popover a {
            color: #2B11DB;
            text-decoration: none;
            flex: 1;
        }

        /* Navigation */
        nav {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
        }

        .browse-toggle {
            background: #2B11DB;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            margin-right: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .browse-toggle:hover {
            background: #1f0bb5;
        }

        .nav-list {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            flex: 1;
        }

        .nav-list li {
            border-right: 1px solid #e5e7eb;
            padding: 0;
        }

        .nav-list a {
            display: block;
            padding: 14px 16px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-list a:hover {
            color: #2B11DB;
        }

        /* Main Content */
        .main-container {
            display: flex;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
            gap: 20px;
            padding: 20px;
        }

        aside {
            width: 250px;
            flex-shrink: 0;
        }

        .sidebar-list {
            list-style: none;
            padding: 0;
            margin: 0;
            background: white;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .sidebar-list li {
            border-bottom: 1px solid #e5e7eb;
            position: relative;
        }

        .sidebar-list li:last-child {
            border-bottom: none;
        }

        .sidebar-list a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            gap: 12px;
        }

        .sidebar-list a:hover {
            background: #f3f4f6;
            color: #2B11DB;
            padding-left: 20px;
        }

        .sidebar-list li.active > a {
            color: #2B11DB;
            background: #f0f4ff;
            font-weight: 600;
        }

        .sidebar-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .sidebar-icon i {
            font-size: 18px;
        }

        .sidebar-label {
            flex: 1;
        }

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
                    <li><a href="../home.php">Home</a></li>
                    <li><a href="../aboutus.php">About Us</a></li>
                    <li><a href="../industries.php">Industries</a></li>
                    <li><a href="../brands.php">Brands</a></li>
                    <li><a href="../contact.php">Contact Us</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="main-container">
        <aside>
            <ul class="sidebar-list">
                <li>
                    <a href="../arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machines</span></a>
                </li>
                <li>
                    <a href="../arc-welding-robots/arc-welding-robot.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-robot"></i></span><span class="sidebar-label">Arc Welding Robots</span></a>
                </li>
                <li>
                    <a href="../batteries/batteries.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-fill"></i></span><span class="sidebar-label">Batteries</span></a>
                </li>
                <li>
                    <a href="../drilling-and-lifting/drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling &amp; Lifting</span></a>
                </li>
                <li>
                    <a href="../gas-detectors/gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Gas Detectors</span></a>
                </li>
                <li>
                    <a href="../portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
                </li>
                <li>
                    <a href="../power-tools/power-tools.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-tools"></i></span><span class="sidebar-label">Power Tools</span></a>
                </li>
                <li>
                    <a href="../protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Personal Protective Equipment</span></a>
                </li>
                <li>
                    <a href="../welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
                </li>
                <li>
                    <a href="../welding-consumables/welding-consumables.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-box"></i></span><span class="sidebar-label">Welding Consumables</span></a>
                </li>
            </ul>
        </aside>

        <div class="category-header">
            <h1><?php echo $category_name; ?></h1>
            <p><?php echo $category_description; ?></p>
        </div>

        <div class="category-content">
            <h2><?php echo htmlspecialchars($current_category['name'] ?? 'Others'); ?></h2>
            <?php if (!empty($current_category['description'])): ?>
                <p class="category-description"><?php echo htmlspecialchars($current_category['description']); ?></p>
            <?php endif; ?>
            <div class="product-grid">
                <?php 
                $all_products = array();
                
                if (!empty($current_category['subcategories']) && is_array($current_category['subcategories'])) {
                    foreach ($current_category['subcategories'] as $subcat) {
                        $products = andison_get_products_for_subcategory($category_id, $subcat['id']);
                        if ($products) {
                            $all_products = array_merge($all_products, $products);
                        }
                    }
                } else {
                    $products = andison_get_products_for_subcategory($category_id, $subcategory_id);
                    if ($products) {
                        $all_products = $products;
                    }
                }
                
                if (!empty($all_products)) {
                    foreach ($all_products as $product) {
                        $image_src = htmlspecialchars($product['image'] ?? '');
                        if ($image_src && strpos($image_src, 'andison/') === 0) {
                            $image_src = '../' . $image_src;
                        }
                        $model = htmlspecialchars($product['model'] ?? '');
                        $name = htmlspecialchars($product['name'] ?? '');
                        $type = htmlspecialchars($product['type'] ?? 'Welding Equipment');
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
                    ?>
                <div class="product-card">
                    <div class="product-image">
                        <i class="bi bi-hammer" style="font-size: 60px; color: #ccc;"></i>
                    </div>
                    <h4>Others</h4>
                    <p class="product-type">No products available</p>
                    <button class="add-to-inquiry" type="button" data-model="Others" data-type="Welding Equipment" data-brand="Industrial">ADD TO INQUIRY</button>
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
        (function(){
            function getItems(){
                try{ return JSON.parse(localStorage.getItem('inquiryItems')||'[]'); }catch(e){ return []; }
            }
            function setItems(items){ localStorage.setItem('inquiryItems', JSON.stringify(items)); }
            function addItem(item){
                var items = getItems();
                var found = items.find(function(i){ return i.model === item.model && i.brand === item.brand; });
                if(found){
                    return false;
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
                    btn.classList.add('already');
                    setTimeout(function(){ btn.classList.remove('already'); }, 700);
                    return;
                }
                btn.textContent = 'Added';
                setTimeout(function(){ btn.textContent = 'ADD TO INQUIRY'; }, 900);
            });
        })();
    </script>
</body>
</html>
