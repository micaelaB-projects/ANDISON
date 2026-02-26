<style>
    /* ============================================
       HEADER & NAVIGATION — extracted from home.php
       ============================================ */

    /* Font */
    header, header *, nav, nav * {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

    .contact-list { list-style: none !important; margin: 0 !important; padding: 6px 0 !important; display: block !important; visibility: visible !important; }
    .contact-list li { display: flex; gap: 12px; align-items: center; padding: 10px 6px; }
    .contact-list .icon { font-size: 18px; width: 28px; text-align: center; color: #2B11DB; }
    .contact-list a { color: #111; text-decoration: none; font-weight: 600; }
    .contact-list a:hover { text-decoration: underline; }

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
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #00E5C8 0%, #347aec 100%);
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

    .nav-list > li > a:hover {
        background: rgba(0,0,0,0.10);
        border-radius: 6px;
    }

    .nav-list > li > a:hover::after {
        transform: translateX(-50%) scaleX(1);
        width: 44px;
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

    .nav-dropdown ul li { margin: 0; }

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

    /* Brands dropdown — wide grid */
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
        cursor: pointer;
    }

    nav li:nth-child(3) .nav-dropdown ul a img {
        max-width: 85px;
        max-height: 45px;
        object-fit: contain;
        display: block;
        pointer-events: all;
        cursor: pointer;
    }

    /* Responsive — header & nav */
    @media (max-width: 768px) {
        .header-top {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            gap: 8px;
            padding: 0 10px;
            margin-bottom: 8px;
        }

        .logo { flex: 0 0 auto; }
        .logo-box img { height: 36px; }

        .search-bar {
            position: static;
            transform: none;
            flex: 1 1 0;
            min-width: 0;
            width: auto;
            max-width: none;
            margin: 0;
        }

        .search-bar .search-field { width: 100%; }

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

        .header-contact { display: none; }

        nav ul { flex-wrap: nowrap; gap: 0; }
        nav li { margin-right: 0; }

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
    }

    /* Prevent header/nav from receiving scroll/page animations */
    header, nav, .header-top, .nav-inner, .browse-toggle, .nav-list, .right-actions {
        animation: none !important;
        opacity: 1 !important;
        transform: none !important;
        transition-property: background, color, box-shadow !important;
    }

    .nav-list li { animation: none !important; opacity: 1 !important; transform: none !important; }
</style>

<?php
// Set page title (override in including file if needed)
if (!isset($page_title))     $page_title     = "Home";
if (!isset($company_name))   $company_name   = "ANDISON INDUSTRIAL";

// Contact information
if (!isset($phone))  $phone  = "+1(234) 567 8900";
if (!isset($phone2)) $phone2 = "+1(234) 567 8900";
if (!isset($phone3)) $phone3 = "+1(639) 977 803 7398";
if (!isset($email))  $email  = "info@andison-industrial.com";

// Auto-detect base path so image/asset URLs work from any subdirectory depth.
// e.g., included from root → $base_path = ""
//        included from arc-welding-machine/ → $base_path = "../"
if (!isset($base_path)) {
    $project_root = realpath(__DIR__ . '/..');          // ANDISON-1 root
    $current_dir  = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
    $rel = '';
    $tmp = $current_dir;
    while (realpath($tmp) !== $project_root && strlen($tmp) > 3) {
        $rel .= '../';
        $tmp  = dirname($tmp);
    }
    $base_path = $rel;
}
?>

<!-- Header -->
<header>
    <div class="header-top">
        <div class="logo">
            <div class="logo-box"><a href="<?php echo $base_path; ?>home.php"><img src="<?php echo $base_path; ?>assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
        </div>

        <div class="search-bar">
            <form class="search-field" action="search.php" method="get">
                <input type="text" name="q" placeholder="Search for products" value="<?php echo htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '', ENT_QUOTES); ?>" onkeydown="if(event.key==='Enter') this.form.submit();">
            </form>
        </div>

        <div class="right-actions">
            <a href="<?php echo $base_path; ?>inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
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
                <li>
                    <a href="<?php echo $base_path; ?>home.php" <?php if(isset($active_page) && $active_page === 'home') echo 'class="active"'; ?>>Home</a>
                    <div class="nav-dropdown">
                        <h4>Welcome</h4>
                        <p>Discover our complete range of industrial welding solutions and equipment.</p>
                    </div>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>aboutus.php" <?php if(isset($active_page) && $active_page === 'aboutus') echo 'class="active"'; ?>>About Us</a>
                    <div class="nav-dropdown">
                        <h4>Our Company</h4>
                        <ul>
                            <li><a href="<?php echo $base_path; ?>aboutus.php#mission">Our Mission</a></li>
                            <li><a href="<?php echo $base_path; ?>aboutus.php#history">Company History</a></li>
                            <li><a href="<?php echo $base_path; ?>aboutus.php#team">Our Team</a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>brands.php" <?php if(isset($active_page) && $active_page === 'brands') echo 'class="active"'; ?>>Brands</a>
                    <div class="nav-dropdown">
                        <h4>Featured Brands</h4>
                        <?php $b = $base_path; ?>
                        <ul>
                            <li><a href="<?php echo $b; ?>brand.php?name=Panasonic%20Connect"><img src="<?php echo $b; ?>assets/brands/PANASONIC.jpg" alt="Panasonic Connect" title="Panasonic Connect"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Robot%20Systems"><img src="<?php echo $b; ?>assets/brands/ROBOT SYSTEMS.png" alt="Robot Systems Peripherals" title="Robot Systems Peripherals"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Kobelco"><img src="<?php echo $b; ?>assets/brands/KOBELCO.jpg" alt="Kobelco" title="Kobelco"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Metrode"><img src="<?php echo $b; ?>assets/brands/METRODE.jpg" alt="Metrode" title="Metrode"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=DryRod.%20II"><img src="<?php echo $b; ?>assets/brands/DRYROD.jpg" alt="DryRod. II" title="DryRod. II"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Weldcraft"><img src="<?php echo $b; ?>assets/brands/WELDCRAFT.png" alt="Weldcraft" title="Weldcraft"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Truweld"><img src="<?php echo $b; ?>assets/brands/TRUWELD.jpg" alt="Truweld" title="Truweld"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Arcair"><img src="<?php echo $b; ?>assets/brands/ARCAIR.jpg" alt="Arcair" title="Arcair"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=MAGNAFLUX"><img src="<?php echo $b; ?>assets/brands/MAGNAFLUX.jpg" alt="Magnaflux" title="Magnaflux"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Tempilstik"><img src="<?php echo $b; ?>assets/brands/TEMPILSTIK.jpg" alt="Tempilstik" title="Tempilstik"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=TANAKA"><img src="<?php echo $b; ?>assets/brands/TANAKA.jpg" alt="Tanaka" title="Tanaka"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=CHIYODA"><img src="<?php echo $b; ?>assets/brands/CHIYODA.jpg" alt="Chiyoda" title="Chiyoda"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Yutaka"><img src="<?php echo $b; ?>assets/brands/YUTAKA.jpg" alt="Yutaka" title="Yutaka"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=HARDWORKER"><img src="<?php echo $b; ?>assets/brands/HARDWORKER.jpg" alt="Hard Workers" title="Hard Workers"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Soyer"><img src="<?php echo $b; ?>assets/brands/SOYER.jpg" alt="Soyer" title="Soyer"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Aquasol"><img src="<?php echo $b; ?>assets/brands/AQUASOL.jpg" alt="Aquasol" title="Aquasol"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=SK%20And%20GAL%20GAGE"><img src="<?php echo $b; ?>assets/brands/SK%20AND%20GAL%20GAGE.jpg" alt="SK And GAL GAGE" title="SK And GAL GAGE"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=COPPUS"><img src="<?php echo $b; ?>assets/brands/COPPUS.jpg" alt="Coppus" title="Coppus"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=BW%20Technologies"><img src="<?php echo $b; ?>assets/brands/BW%20TECHNOLOGIES.jpg" alt="BW Technologies" title="BW Technologies"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=RAC"><img src="<?php echo $b; ?>assets/brands/RAE%20SYSTEMS.jpg" alt="RAE Systems" title="RAE Systems"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=WELDAS"><img src="<?php echo $b; ?>assets/brands/WELDAS.jpg" alt="Weldas" title="Weldas"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=UVEX"><img src="<?php echo $b; ?>assets/brands/UVEX.jpg" alt="Uvex" title="Uvex"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=ACES"><img src="<?php echo $b; ?>assets/brands/ACES.jpg" alt="Aces" title="Aces"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=MICROGARD"><img src="<?php echo $b; ?>assets/brands/MICROGARD.jpg" alt="Microgard" title="Microgard"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=ANSELL"><img src="<?php echo $b; ?>assets/brands/ANSELL.jpg" alt="Ansell" title="Ansell"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Alfra"><img src="<?php echo $b; ?>assets/brands/ALFRA.jpg" alt="Alfra" title="Alfra"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=BOSCH"><img src="<?php echo $b; ?>assets/brands/BOSCH.jpg" alt="Bosch" title="Bosch"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Makita"><img src="<?php echo $b; ?>assets/brands/MAKITA.jpg" alt="Makita" title="Makita"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Weller"><img src="<?php echo $b; ?>assets/brands/WEILER.jpg" alt="Weller" title="Weller"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Garryson"><img src="<?php echo $b; ?>assets/brands/GARRYSON.jpg" alt="Garryson" title="Garryson"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=REVOLT"><img src="<?php echo $b; ?>assets/brands/REVOLT.png" alt="REVOLT" title="REVOLT"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Technotex"><img src="<?php echo $b; ?>assets/brands/TECHNOTEX.png" alt="Technotex" title="Technotex"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Spilfyter"><img src="<?php echo $b; ?>assets/brands/SPILFYTER.jpg" alt="Spilfyter" title="Spilfyter"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=Dalo"><img src="<?php echo $b; ?>assets/brands/DALO.jpg" alt="Dalo" title="Dalo"></a></li>
                            <li><a href="<?php echo $b; ?>brand.php?name=MOTOLITE"><img src="<?php echo $b; ?>assets/brands/MOTOLITE.jpg" alt="Motolite" title="Motolite"></a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>industries.php" <?php if(isset($active_page) && $active_page === 'industries') echo 'class="active"'; ?>>Industries</a>
                    <div class="nav-dropdown">
                        <h4>Industries We Serve</h4>
                        <ul>
                            <li><a href="<?php echo $base_path; ?>industries.php#manufacturing">Manufacturing</a></li>
                            <li><a href="<?php echo $base_path; ?>industries.php#construction">Construction</a></li>
                            <li><a href="<?php echo $base_path; ?>industries.php#automotive">Automotive</a></li>
                            <li><a href="<?php echo $base_path; ?>industries.php#shipbuilding">Shipbuilding</a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>services.php" <?php if(isset($active_page) && $active_page === 'services') echo 'class="active"'; ?>>Services</a>
                    <div class="nav-dropdown">
                        <h4>Our Services</h4>
                        <ul>
                            <li><a href="<?php echo $base_path; ?>services.php#consultation">Technical Consultation</a></li>
                            <li><a href="<?php echo $base_path; ?>services.php#training">Training Programs</a></li>
                            <li><a href="<?php echo $base_path; ?>services.php#maintenance">Equipment Maintenance</a></li>
                            <li><a href="<?php echo $base_path; ?>services.php#support">After-Sales Support</a></li>
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>contact.php" <?php if(isset($active_page) && $active_page === 'contact') echo 'class="active"'; ?>>Contact Us</a>
                    <div class="nav-dropdown">
                        <h4>Get In Touch</h4>
                        <p>Reach out to our team for inquiries, quotes, or technical support.</p>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>

<script>
    // ============================================
    // CONTACT DROPDOWN — aria & mobile toggle
    // ============================================
    (function(){
        var dropdowns = document.querySelectorAll('.contact-dropdown');
        dropdowns.forEach(function(dd){
            var pop  = dd.querySelector('.contact-popover');
            var link = dd.querySelector('.contact-link');
            dd.addEventListener('keydown', function(e){
                if(e.key === 'Escape') { link.blur(); pop.setAttribute('aria-hidden','true'); }
            });
            dd.addEventListener('focusin', function(){ pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true'); });
            dd.addEventListener('focusout', function(){
                setTimeout(function(){
                    if(!dd.contains(document.activeElement)){
                        pop.setAttribute('aria-hidden','true');
                        dd.setAttribute('aria-expanded','false');
                    }
                }, 10);
            });
            dd.addEventListener('mouseenter', function(){
                if(dd.classList.contains('closed')) return;
                pop.setAttribute('aria-hidden','false'); dd.setAttribute('aria-expanded','true');
            });
            dd.addEventListener('mouseleave', function(){
                pop.setAttribute('aria-hidden','true'); dd.setAttribute('aria-expanded','false');
                dd.classList.remove('closed');
            });

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
    // ============================================
    // BRAND DROPDOWN NAVIGATION (priority handler)
    // ============================================
    (function(){
        document.addEventListener('click', function(e){
            var brandLink = e.target.closest('.nav-list li:nth-child(3) .nav-dropdown a');
            if(brandLink){
                e.preventDefault();
                e.stopPropagation();
                var href = brandLink.getAttribute('href');
                if(href){ window.location.href = href; }
                return;
            }
        }, true);
    })();
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
            if(count > 0){
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
        updateCartBadge();
        window.addEventListener('storage', updateCartBadge);
        window.addEventListener('inquiryItemsUpdated', updateCartBadge);
        setInterval(updateCartBadge, 500);
    })();
</script>
