<?php
require_once __DIR__ . '/Andison/includes/analytics.php';
andison_track_visit('search');
$company_name = 'ANDISON INDUSTRIAL';

require_once __DIR__ . '/includes/brands_info.php';

$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$brands_info = andison_get_brands_info();

$matches = [];

if ($q !== '') {
    foreach ($brands_info as $brandName => $brandInfo) {
        $products = $brandInfo['products'] ?? [];
        foreach ($products as $product) {
            $model = '';
            $type = '';
            $image = '';

            if (is_array($product)) {
                $model = (string)($product['model'] ?? '');
                $type = (string)($product['type'] ?? '');
                $image = (string)($product['image'] ?? '');
            } else {
                $model = (string)$product;
                $type = 'Product';
            }

            $haystack = $brandName . ' ' . $model . ' ' . $type;
            if (stripos($haystack, $q) !== false) {
                $matches[] = [
                    'brand' => $brandName,
                    'model' => $model,
                    'type' => $type,
                    'image' => $image,
                ];
            }
        }
    }
}

// Limit results to keep the page fast
$matches = array_slice($matches, 0, 80);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - <?php echo htmlspecialchars($company_name); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height:1.6; color:#333; background: linear-gradient(135deg, #f8f9fa 0%, #f0f2f5 100%); padding-top:142px; min-height: 100vh; display:flex; flex-direction:column; }

        header { background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%); color:white; padding:14px 0; position:fixed; top:0; left:0; right:0; z-index:1200; width:100%; }
        .header-top { display:flex; align-items:center; max-width:1200px; margin:0 auto; padding:0 20px; gap:20px; margin-bottom:12px; }
        .logo { font-size:16px; font-weight:700; display:flex; align-items:center; gap:10px; flex:0 0 auto; }
        .logo-box { background:transparent; color:#2b00d9; padding:0; border-radius:0; font-weight:800; letter-spacing:0.6px; }
        .logo-box img { height:50px; width:auto; display:block; }

        .search-bar { flex: 1 1 auto; display:flex; justify-content:center; max-width: 600px; margin: 0 0 0 20px; }
        .search-bar .search-field { width:100%; display:flex; align-items:center; gap:8px; position:relative; margin:0; }
        .search-bar input { width:100%; height:46px; padding:10px 16px 10px 40px; border:2px solid rgba(255,255,255,0.3); border-radius:6px; font-size:15px; background: rgba(255,255,255,0.95); color:#333; transition: all 0.3s ease; }
        .search-bar input:focus { outline: none; background: #fff; border-color: rgba(255,255,255,0.8); box-shadow: 0 0 0 3px rgba(43, 17, 219, 0.1); }
        .search-bar input::placeholder { color:#999; }
        .search-bar .search-field i { position:absolute; left:12px; font-size:16px; pointer-events:none; color:#666; }

        .right-actions { margin-left: 12px; display:flex; align-items:center; gap:12px; }
        .inquiry-btn,
        .cart-icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00E5C8 0%, #347aec 100%);
            position: relative;
            font-size: 14px;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(0,188,212,0.4);
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

        .inquiry-btn {
            position: relative;
        }

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
            flex-wrap: nowrap;
            align-items: center;
            min-height: 52px;
            gap: 0;
            justify-content: center;
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
        .nav-list a { text-decoration: none; display: block; }
        .nav-list a:hover { color: rgba(255,255,255,0.8); }

        .nav-list > li > a {
            position: relative;
            padding: 10px 14px;
            color: white;
            transition: color 180ms ease, background 180ms ease;
            border-radius: 6px;
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

        .nav-list > li > a:hover::after,
        .nav-list > li > a.active::after {
            transform: translateX(-50%) scaleX(1);
            width: 44px;
        }

        .nav-list > li > a:hover {
            background: rgba(0,0,0,0.10);
        }

        .nav-list > li > a.active {
            background: rgba(0,0,0,0.14);
            color: #fff;
            font-weight: 700;
            box-shadow: inset 0 -6px 18px rgba(0,0,0,0.06);
        }

        .nav-dropdown {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            background: white;
            min-width: 240px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
            z-index: 110;
            padding: 14px;
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
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f0f0f0;
        }

        .nav-dropdown ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-dropdown ul a {
            color: #333;
            font-size: 14px;
            padding: 8px 10px;
            display: block;
            border-radius: 6px;
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
            min-width: 576px;
            max-width: 576px;
            padding: 20px 22px;
        }

        nav li:nth-child(3) .nav-dropdown ul {
            display: grid !important;
            grid-template-columns: repeat(5, 1fr) !important;
            gap: 10px 14px !important;
            margin-top: 14px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul li {
            min-height: 58px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        nav li:nth-child(3) .nav-dropdown ul a {
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 50px !important;
        }

        nav li:nth-child(3) .nav-dropdown ul a img {
            max-width: 74px;
            max-height: 37px;
            object-fit: contain;
            display: block;
            pointer-events: all;
            cursor: pointer;
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 32px 20px; }
        .results-header { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; margin-bottom: 32px; padding-bottom: 20px; border-bottom: 2px solid #f0f0f0; }
        .results-header h1 { font-size: 32px; font-weight: 900; color:#2b00d9; letter-spacing: -0.5px; }
        .results-header .count { background: linear-gradient(135deg, #2B11DB 0%, #1a009e 100%); color: white; font-size: 14px; font-weight: 800; padding: 10px 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(43, 17, 219, 0.25); display: flex; align-items: center; gap: 8px; }
        .results-header .count strong { font-size: 16px; }

        .results-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 28px; }
        .result-card { background:#fff; border:1px solid #e0e4e8; border-radius: 16px; overflow:hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); display:flex; flex-direction:column; min-height: 340px; transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .result-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background: linear-gradient(90deg, #2b00d9 0%, #00d7b3 100%); opacity:0; transition: opacity 0.35s ease; }
        .result-card:hover { box-shadow: 0 16px 40px rgba(43, 17, 219, 0.12); transform: translateY(-6px); border-color: #d0d4e0; }
        .result-card:hover::before { opacity: 1; }
        .result-image { height: 200px; background: linear-gradient(135deg, #f5f7fb 0%, #eef2f8 100%); display:flex; align-items:center; justify-content:center; position: relative; border-bottom: 1px solid #f0f0f5; }
        .result-image img { width:100%; height:100%; object-fit:contain; padding: 16px; }
        .result-body { padding: 24px; display:flex; flex-direction:column; gap: 12px; flex: 1 1 auto; }
        .result-brand { font-size: 10px; color:#2b00d9; font-weight:900; letter-spacing: 1.2px; text-transform: uppercase; opacity: 0.75; }
        .result-model { font-size: 18px; font-weight: 900; color:#1a1a2e; line-height: 1.4; }
        .result-type { font-size: 13px; color:#666; font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .result-actions { margin-top:auto; padding-top: 16px; display:flex; gap: 8px; }
        .result-actions a { text-decoration:none; flex: 1; }
        .view-brand-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
            color: #1a1a2e;
            border: none;
            padding: 12px 28px;
            border-radius: 25px;
            font-weight: 700;
            font-size: 13px;
            box-shadow: 0 4px 14px rgba(0, 215, 179, 0.28);
            transition: all 0.35s ease;
            cursor: pointer;
            line-height: 1.3;
            height: auto;
            min-height: 40px;
        }
        .view-brand-btn:hover {
            background: linear-gradient(135deg, #00FFD1 0%, #00FFD1 100%);
            box-shadow: 0 8px 24px rgba(0, 215, 179, 0.38);
            transform: translateY(-2px);
        }
        .view-brand-btn:active {
            transform: translateY(0);
        }

        .no-results { padding: 60px 32px; border: 2px solid #e0e4e8; border-radius: 16px; background: linear-gradient(135deg, #fafbff 0%, #f5f7ff 100%); color:#2b00d9; font-weight: 900; text-align:center; letter-spacing: 0.5px; font-size: 20px; }
        .no-results small { font-size: 14px; font-weight: 600; opacity: 0.8; }
        .hint { padding: 24px 28px; border-radius: 14px; background: linear-gradient(135deg, #f0f4ff 0%, #f7f8ff 100%); border:2px solid #e2e6ff; color:#333; font-weight: 600; line-height: 1.8; }

        @media (min-width: 1024px) and (max-width: 1366px) {
            .container {
                max-width: 1120px;
                padding: 28px 18px;
            }
            .results-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 22px;
            }
            .result-body { padding: 20px; }
            .result-model { font-size: 17px; }
        }

        @media (max-width: 768px) {
            body { padding-top: 142px; }
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
            .search-bar .search-field i { font-size: 13px; left: 8px; }
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
            .nav-dropdown { display: none; }
            .container { padding: 24px 14px 28px; }
            .results-header { flex-direction: column; align-items: flex-start; gap: 14px; margin-bottom: 22px; padding-bottom: 14px; }
            .results-header h1 { font-size: 28px; }
            .results-header .count { width: 100%; justify-content: center; border-radius: 14px; }
            .results-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
            .result-card { min-height: 300px; }
            .result-image { height: 160px; }
            .result-body { padding: 20px; }
        }

        @media (max-width: 480px) {
            .container { padding: 20px 12px; }
            .results-header h1 { font-size: 24px; }
            .results-grid { grid-template-columns: 1fr; gap: 16px; }
            .result-card { min-height: 280px; }
            .result-image { height: 140px; }
            .result-model { font-size: 16px; }
            .view-brand-btn { padding: 11px 24px; font-size: 13px; }
        }
    </style>
</head>
<body>
<!-- GLOBAL LOADER -->
<style>
.global-page-loader { position: fixed; inset: 0; background: linear-gradient(135deg, rgba(43, 17, 219, 0.96) 0%, rgba(30, 48, 140, 0.98) 100%); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 999999; opacity: 1; visibility: visible; transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.global-page-loader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.gpl-box { width: min(320px, 88vw); padding: 32px 24px; border-radius: 24px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16); box-shadow: 0 24px 54px rgba(0, 0, 0, 0.4); display: flex; flex-direction: column; align-items: center; gap: 20px; transform: translateY(0); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.global-page-loader.is-hidden .gpl-box { transform: translateY(15px) scale(0.95); }
.gpl-logo { width: 140px; height: 140px; object-fit: contain; filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.4)); animation: gplPulse 2s ease-in-out infinite; }
.gpl-ring { width: 54px; height: 54px; border-radius: 50%; border: 4px solid rgba(255, 255, 255, 0.15); border-top-color: #00D7B3; animation: gplSpin 1s linear infinite; }
.gpl-text { margin: 0; color: #ffffff; font-size: 15px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; text-align: center; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
@keyframes gplPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.06); } }
@keyframes gplSpin { to { transform: rotate(360deg); } }
html.gpl-loading, html.gpl-loading body { overflow: hidden !important; }
</style>
<div id="globalPageLoader" class="global-page-loader" aria-hidden="false">
    <div class="gpl-box" role="status" aria-live="polite" aria-label="Loading page">
        <img class="gpl-logo" id="gplLogoImgTop" src="/ANDISON/assets/HOME/image-removebg-preview.png" alt="ANDISON Logo">
        <div class="gpl-ring" aria-hidden="true"></div>
        <p class="gpl-text">Loading...</p>
    </div>
</div>
<script>
(function() {
    var loader = document.getElementById('globalPageLoader');
    if (!loader) return;
    document.documentElement.classList.add('gpl-loading');
    var gplLogoTop = document.getElementById('gplLogoImgTop');
    if (gplLogoTop) {
        var base = window.location.pathname.split('/').slice(0, window.location.pathname.split('/').findIndex(p => p.toLowerCase() === 'andison') + 1).join('/');
        if (base && base !== '/') gplLogoTop.src = base + '/assets/HOME/image-removebg-preview.png';
    }
    
    function hideLoader() {
        if (loader.classList.contains('is-hidden')) return;
        loader.classList.add('is-hidden');
        loader.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('gpl-loading');
    }
    
    if (document.readyState === 'complete') {
        setTimeout(hideLoader, 150);
    } else {
        window.addEventListener('load', function() {
            setTimeout(hideLoader, 150);
        });
    }
    
    setTimeout(hideLoader, 5000);
    
    document.addEventListener('click', function(e) {
        var target = e.target.closest('a');
        if (!target) return;
        var href = target.getAttribute('href');
        if (!href) return;
        if (href.startsWith('javascript:') || href.startsWith('#') || target.getAttribute('target') === '_blank' || href.startsWith('tel:') || href.startsWith('mailto:') || e.ctrlKey || e.shiftKey || e.metaKey || e.button !== 0) return;
        var isInternal = false;
        try {
            var url = new URL(target.href, window.location.href);
            if (url.origin === window.location.origin) isInternal = true;
        } catch (err) {}
        if (isInternal) {
            var currentUrl = new URL(window.location.href);
            var targetUrl = new URL(target.href);
            if (currentUrl.pathname === targetUrl.pathname && currentUrl.search === targetUrl.search && targetUrl.hash) return;
            loader.classList.remove('is-hidden');
            loader.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('gpl-loading');
        }
    });
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) hideLoader();
    });
})();
</script>
<!-- /GLOBAL LOADER -->
<header>
    <div class="header-top">
        <div class="logo">
            <div class="logo-box"><a href="home.php"><img src="assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
        </div>

        <div class="search-bar">
            <form class="search-field" action="search.php" method="get">
                <i class="bi bi-search"></i>
                <input type="text" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES); ?>" placeholder="Search for products" />
            </form>
        </div>

        <div class="right-actions">
            <a href="#" class="inquiry-btn email-admin-btn" data-subject="Client Inquiry" aria-label="Message" title="Message"><i class="bi bi-envelope" aria-hidden="true"></i></a>
            <a href="inquirylist.php" class="inquiry-btn"><i class="bi bi-card-checklist btn-icon"></i> <span class="btn-text">INQUIRY LIST</span> <span class="cart-badge hidden" id="cartBadge">0</span></a>
        </div>
    </div>

    <nav>
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
<?php $andisonNavBrandsPath = __DIR__ . '/includes/nav_brands_dropdown_items.php'; if (!is_file($andisonNavBrandsPath)) { $andisonNavBrandsPath = dirname(__DIR__) . '/includes/nav_brands_dropdown_items.php'; } if (!is_file($andisonNavBrandsPath)) { $andisonNavBrandsPath = dirname(dirname(__DIR__)) . '/includes/nav_brands_dropdown_items.php'; } if (is_file($andisonNavBrandsPath)) { include $andisonNavBrandsPath; } ?>
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

<main class="container">
    <div class="results-header">
        <div>
            <h1>Search Results</h1>
        </div>
        <?php if ($q !== ''): ?>
            <div class="count"><i class="bi bi-search"></i> <strong><?php echo count($matches); ?></strong> result<?php echo count($matches) !== 1 ? 's' : ''; ?> for "<strong><?php echo htmlspecialchars($q); ?></strong>"</div>
        <?php else: ?>
            <div class="count"><i class="bi bi-info-circle"></i> <strong>Search Products</strong></div>
        <?php endif; ?>
    </div>

    <?php if ($q === ''): ?>
        <div class="hint">
            🔍 <strong>Search Tips:</strong> Try searching by <em>brand name</em> (e.g., Panasonic, Bosch), <em>model number</em> (e.g., YD-350, GSB 16), or <em>product type</em> (e.g., Welding Robot, Grinder, Gloves).
        </div>
    <?php elseif (count($matches) === 0): ?>
        <div class="no-results">
            <i class="bi bi-search" style="font-size: 32px; margin-bottom: 12px; display: block;"></i>
            NO RESULT FOUND<br/><small style="font-size: 13px; font-weight: 500; margin-top: 8px; display: block;">Try a different search term or browse our categories</small>
        </div>
    <?php else: ?>
        <div class="results-grid">
            <?php foreach ($matches as $item): ?>
                <div class="result-card">
                    <div class="result-image">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($item['model'], ENT_QUOTES); ?>" onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\"font-weight:800;color:#ccc;\">
                        <?php else: ?>
                            <span style="font-weight:800;color:#ccc;"><i class="bi bi-image" style="font-size: 32px;"></i></span>
                        <?php endif; ?>
                    </div>
                    <div class="result-body">
                        <div class="result-brand"><?php echo htmlspecialchars($item['brand']); ?></div>
                        <div class="result-model"><?php echo htmlspecialchars($item['model']); ?></div>
                        <div class="result-type"><i class="bi bi-tag" style="margin-right: 4px;"></i><?php echo htmlspecialchars($item['type']); ?></div>
                        <div class="result-actions">
                            <a class="view-brand-btn" href="brand.php?name=<?php echo urlencode($item['brand']); ?>&product=<?php echo urlencode($item['model']); ?>" title="View product details">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
    // UPDATE CART BADGE COUNT IN REAL-TIME
    (function(){
        function updateCartBadge() {
            var badge = document.getElementById('cartBadge');
            if(!badge) return;
            var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
            var count = items.length;
            if(count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
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

<script src="assets/js/scroll-fade.js"></script>
<script src="assets/js/email_admin_compose.js"></script>

</body>
</html>







