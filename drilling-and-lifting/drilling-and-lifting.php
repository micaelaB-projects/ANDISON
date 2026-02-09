<?php
declare(strict_types=1);

$page_title = "Drilling and Lifting";
$category_id = "drilling-and-lifting";

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

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
    <title><?php echo htmlspecialchars($page_title); ?> - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; padding-top: 142px; }

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
            justify-content: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            gap: 20px;
            margin-bottom: 12px;
        }

        .header-top .logo {
            position: absolute;
            left: 20px;
        }

        .logo { font-size: 16px; font-weight: 700; flex: 0 0 auto; }
        .search-bar { flex: 1; max-width: 600px; }
        .search-bar input { width: 100%; height: 40px; padding: 10px 16px 10px 40px; border: 2px solid rgba(255,255,255,0.3); border-radius: 6px; font-size: 15px; background: rgba(255,255,255,0.95); color: #333; }

        .inquiry-btn {
            background: #00D7B3;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 25px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .inquiry-btn:hover { background: #00B899; }

        nav {
            background: rgba(0, 215, 179, 0.85);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            gap: 28px;
            min-height: 52px;
            align-items: center;
        }

        .nav-list {
            list-style: none;
            display: flex;
            gap: 28px;
        }

        .nav-list a { color: white; text-decoration: none; font-weight: 600; }
        .nav-list a:hover { text-decoration: underline; }

        .brand-container { max-width: 1500px; margin: 1px auto 40px; padding: 0 40px; }
        .brand-header { background: white; border-radius: 10px; padding: 40px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; text-align: center; }
        .brand-header h1 { color: #2B11DB; font-size: 48px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; gap: 15px; }
        .brand-header p { color: #666; font-size: 18px; max-width: 800px; margin: 0 auto; }

        .brand-content { background: white; border-radius: 10px; padding: 40px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .brand-content h2 { color: #2B11DB; font-size: 32px; margin-bottom: 20px; border-bottom: 3px solid #2B11DB; padding-bottom: 5px; display: flex; align-items: center; gap: 12px; }

        .product-grid { display: flex; flex-wrap: wrap; gap: 25px; margin-top: 30px; justify-content: center; }
        .product-card { flex: 0 1 calc(20% - 20px); min-width: 240px; max-width: 280px; background: white; border: 2px solid #e0e0e0; border-radius: 10px; padding: 25px; text-align: center; transition: all 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .product-card:hover { border-color: #2B11DB; box-shadow: 0 8px 20px rgba(43,17,219,0.15); transform: translateY(-5px); background: #fafafa; }

        .product-image { width: 100%; height: 180px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; overflow: hidden; }
        .product-image img { width: 100%; height: 100%; object-fit: cover; }

        .product-badge { display: inline-block; background: #2B11DB; color: white; padding: 5px 12px; border-radius: 3px; font-size: 11px; font-weight: bold; margin-bottom: 10px; text-transform: uppercase; }
        .product-info h3 { color: #2B11DB; font-size: 18px; margin-bottom: 8px; }
        .product-info p { color: #666; font-size: 14px; margin-bottom: 8px; }
        .product-price { color: #2B11DB; font-size: 20px; font-weight: 700; margin-bottom: 15px; }

        .btn-inquiry { background: #00D7B3; color: white; padding: 10px 20px; border: none; border-radius: 3px; font-weight: bold; cursor: pointer; text-decoration: none; display: inline-block; width: 100%; transition: background 0.2s; }
        .btn-inquiry:hover { background: #00B899; }

        .no-products { grid-column: 1 / -1; padding: 60px; text-align: center; background: #f9f9f9; border-radius: 10px; border: 2px dashed #ddd; }
        .no-products i { font-size: 64px; color: #ddd; margin-bottom: 20px; }

        .back-btn { color: #2B11DB; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }

        footer { background: #2B11DB; color: white; padding: 40px 20px; text-align: center; }
        .footer-links { display: flex; justify-content: center; gap: 30px; margin-bottom: 20px; flex-wrap: wrap; }
        .footer-links a { color: white; text-decoration: none; font-size: 13px; }
        .footer-links a:hover { color: #00d4aa; }

        @media (max-width: 1200px) { .product-card { flex: 0 1 calc(25% - 20px); } }
        @media (max-width: 768px) { body { padding-top: 120px; } .brand-header { padding: 20px; } .brand-header h1 { font-size: 24px; } .brand-container { padding: 0 15px; } .product-card { flex: 0 1 calc(50% - 12px); } }
        @media (max-width: 480px) { .product-card { flex: 0 1 100%; } .brand-header h1 { font-size: 18px; } }
    </style>
</head>
    <!-- Contact information -->
    <?php
        $phone = "+1(234) 567 8900";
        $phone2 = "+1(234) 567 8900";
        $phone3 = "+1(639) 977 803 7398";
        $email = "info@andison-industrial.com";
    ?>

    <!-- Header -->
    <header>
        <div class="header-top">
            <div class="logo">
                <div class="logo-box"><a href="../../home.php"><img src="../../assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
            </div>

            <div class="search-bar">
                <form class="search-field" action="../../search.php" method="get">
                    <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
                </form>
            </div>

            <div class="right-actions">
                <a href="javascript:history.back()" class="inquiry-btn" style="margin-right: 12px;">BACK</a>
                <a href="../../inquirylist.php" class="inquiry-btn">INQUIRY LIST</a>
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
                        <a href="../../home.php">Home</a>
                        <div class="nav-dropdown">
                            <h4>Welcome</h4>
                            <p>Discover our complete range of industrial welding solutions and equipment.</p>
                        </div>
                    </li>
                    <li>
                        <a href="../../aboutus.php">About Us</a>
                        <div class="nav-dropdown">
                            <h4>Our Company</h4>
                            <ul>
                                <li><a href="../../aboutus.php#mission">Our Mission</a></li>
                                <li><a href="../../aboutus.php#history">Company History</a></li>
                                <li><a href="../../aboutus.php#team">Our Team</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../../brands.php">Brands</a>
                        <div class="nav-dropdown">
                            <h4>Featured Brands</h4>
                            <p>Browse our extensive selection of premium industrial brands.</p>
                        </div>
                    </li>
                    <li>
                        <a href="../../industries.php">Industries</a>
                        <div class="nav-dropdown">
                            <h4>Industries We Serve</h4>
                            <ul>
                                <li><a href="../../industries.php#manufacturing">Manufacturing</a></li>
                                <li><a href="../../industries.php#construction">Construction</a></li>
                                <li><a href="../../industries.php#automotive">Automotive</a></li>
                                <li><a href="../../industries.php#shipbuilding">Shipbuilding</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../../services.php">Services</a>
                        <div class="nav-dropdown">
                            <h4>Our Services</h4>
                            <ul>
                                <li><a href="../../services.php#consultation">Technical Consultation</a></li>
                                <li><a href="../../services.php#training">Training Programs</a></li>
                                <li><a href="../../services.php#maintenance">Equipment Maintenance</a></li>
                                <li><a href="../../services.php#support">After-Sales Support</a></li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <a href="../../contact.php">Contact Us</a>
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
                <a href="../../arc-welding-machine/arc-welding-machine.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-lightning-charge"></i></span><span class="sidebar-label">Arc Welding Machine</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-arc-welding" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-arc-welding" class="sidebar-sublist collapsed">
                    <li><a href="../../arc-welding-machine/mig-welding-machine.php">MIG Welding Machine</a></li>
                    <li><a href="../../arc-welding-machine/co1-mag-welding-machine.php">CO1/MAG Welding Machine</a></li>
                    <li><a href="../../arc-welding-machine/stud-welding-machine.php">STUD Welding Machine</a></li>
                    <li><a href="../../arc-welding-machine/tig-welding-machine.php">TIG Welding Machine</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="./drilling-and-lifting.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-hammer"></i></span><span class="sidebar-label">Drilling and Lifting</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-drilling-lifting" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-drilling-lifting" class="sidebar-sublist collapsed">
                    <li><a href="./lifting.php">Lifting</a></li>
                    <li><a href="./magnetic-drill.php">Magnetic Drill</a></li>
                    <li><a href="./cutters.php">Cutters</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../../gas-detectors/portable-gas-detectors.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-bullseye"></i></span><span class="sidebar-label">Portable Gas Detectors</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-gas-detectors" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-gas-detectors" class="sidebar-sublist collapsed">
                    <li><a href="../../gas-detectors/single-gas-detector.php">Single Gas Detector</a></li>
                    <li><a href="../../gas-detectors/multi-gas-detector.php">Multi Gas Detector</a></li>
                    <li><a href="../../gas-detectors/docking-data-management.php">Docking and Data Management</a></li>
                    <li><a href="../../gas-detectors/calibration-gas-regulators.php">Calibration Gas and Regulators</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../../portable-ventilators/portable-ventilators.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-fan"></i></span><span class="sidebar-label">Portable Ventilators</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-ventilators" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-ventilators" class="sidebar-sublist collapsed">
                    <li><a href="../../portable-ventilators/portable-ventilator-accessories.php">Portable Ventilator Accessories</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../../protection/protection.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-shield-check"></i></span><span class="sidebar-label">Protection and Safety</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-protection-safety" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-protection-safety" class="sidebar-sublist collapsed">
                    <li><a href="../../protection/eye-protection.php">Eye Protection</a></li>
                    <li><a href="../../protection/foot-protection.php">Foot Protection</a></li>
                    <li><a href="../../protection/hand-protection.php">Hand Protection</a></li>
                    <li><a href="../../protection/hearing-respiratory-protection.php">Hearing & Respiratory Protection</a></li>
                    <li><a href="../../protection/body-protection.php">Body Protection</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="../../welding-accessories/welding-accessories.php"><span class="sidebar-icon" aria-hidden="true"><i class="bi bi-gear"></i></span><span class="sidebar-label">Welding Accessories</span></a>
                <button class="sub-toggle" aria-expanded="false" aria-controls="sub-welding-accessories" title="Toggle subcategories"><i class="bi bi-chevron-right"></i></button>
                <ul id="sub-welding-accessories" class="sidebar-sublist collapsed">
                    <li><a href="../../welding-accessories/welding-head-face-protection.php">Welding, Head & Face Protection</a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <div class="brand-container">
        <a href="../../home.php" class="back-btn"><i class="bi bi-arrow-left"></i> Back</a>

        <div class="brand-header">
            <h1><i class="bi <?php echo htmlspecialchars($current_category['icon']); ?>"></i> <?php echo htmlspecialchars($current_category['name']); ?></h1>
            <p><?php echo htmlspecialchars($current_category['description']); ?></p>
        </div>

        <?php foreach ($current_category['subcategories'] as $subcategory): ?>
            <?php $subProducts = andison_get_products_for_subcategory($current_category['id'], $subcategory['id']); ?>
            <div class="brand-content">
                <h2><i class="bi bi-box-seam"></i> <?php echo htmlspecialchars($subcategory['name']); ?></h2>

                <?php if (empty($subProducts)): ?>
                    <div class="product-grid"><div class="no-products"><i class="bi bi-inbox"></i><h3>No products</h3></div></div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($subProducts as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                                    <?php else: ?>
                                        <i class="bi bi-image" style="font-size: 48px; color: #ccc;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <?php if (!empty($product['badge'])): ?><div class="product-badge"><?php echo htmlspecialchars($product['badge']); ?></div><?php endif; ?>
                                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                    <?php if (!empty($product['model'])): ?><p><strong>Model:</strong> <?php echo htmlspecialchars($product['model']); ?></p><?php endif; ?>
                                    <?php if (!empty($product['price'])): ?><div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div><?php endif; ?>
                                    <a href="../../contact.php" class="btn-inquiry"><i class="bi bi-chat"></i> Inquire</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <footer>
        <div class="footer-links">
            <a href="../../home.php">Home</a><a href="../../services.php">Services</a><a href="../../brands.php">Brands</a>
        </div>
        <p style="font-size: 12px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 20px;">&copy; 2025 ANDISON INDUSTRIAL</p>
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
        })();
    </script>
    <script>
        // Contact dropdown handler
        (function(){
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
        })();
    </script>
</body>
</html>