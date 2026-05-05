<?php
require_once __DIR__ . '/includes/brands_info.php';
require_once __DIR__ . '/includes/analytics.php';
andison_track_visit('brand-detail');
$brand_input = isset($_GET['name']) ? trim(strip_tags($_GET['name'])) : '';
if ($brand_input) andison_track_brand_visit($brand_input);

function andison_brand_display_name(string $brand): string
{
    $normalized = strtolower(trim($brand));
    if ($normalized === 'dryrod. ii' || $normalized === 'dryrod ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
        return 'DryRod. II';
    }
    if ($normalized === 'bw' || $normalized === 'bw technologies') {
        return 'BW Technologies';
    }
    if ($normalized === 'panasonic' || $normalized === 'panasonic connect') {
        return 'Panasonic Connect';
    }
    if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
        return 'RAE SYSTEMS';
    }
    return $normalized === 'weiler' ? 'WEILER' : $brand;
}

function andison_brand_data_candidates(string $brand): array
{
    $normalized = strtolower(trim($brand));
    if ($normalized === 'dryrod. ii' || $normalized === 'dryrod ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
        return ['DryRod. II', 'DryRod II', 'Phoenix Dry Rod', 'Phoenix DryRod', 'PHOENIX DRY ROD', 'PHOENIX DRYROD'];
    }
    if ($normalized === 'bw' || $normalized === 'bw technologies') {
        return ['BW', 'BW Technologies', 'BW TECHNOLOGIES'];
    }
    if ($normalized === 'panasonic connect' || $normalized === 'panasonic') {
        return ['Panasonic Connect', 'PANASONIC'];
    }
    if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
        return ['RAE SYSTEMS', 'RAC'];
    }
    if ($normalized === 'weiler' || $normalized === 'weller') {
        return ['WEILER', 'Weller'];
    }
    return [$brand];
}

function andison_pick_brand_bucket(array $brands, array $candidates): string
{
    foreach ($candidates as $candidate) {
        if (isset($brands[$candidate]) && !empty($brands[$candidate]['products'])) {
            return (string)$candidate;
        }
    }

    foreach ($candidates as $candidate) {
        if (isset($brands[$candidate])) {
            return (string)$candidate;
        }
    }

    return (string)($candidates[0] ?? '');
}

function andison_render_brand_description($rawDescription): string
{
    $value = trim((string)$rawDescription);
    if ($value === '') {
        return '';
    }

    $value = preg_replace('~<(button|input|textarea|select|option)[^>]*class=("|\")[^"\"]*(desc-cell-select-toggle|desc-cell-image-delete)[^"\"]*\2[^>]*>.*?</\1>~is', '', $value) ?? $value;
    $value = preg_replace('~<input[^>]*(data-desc-cell-select|data-desc-image-delete)[^>]*\/?>~is', '', $value) ?? $value;
    $value = preg_replace('~<button[^>]*(data-desc-cell-select|data-desc-image-delete|desc-cell-select-toggle|desc-cell-image-delete)[^>]*>.*?</button>~is', '', $value) ?? $value;
    $value = preg_replace('~<(script|style|iframe|object|embed|form|input|button|textarea|select)[^>]*>.*?</\\1>~is', '', $value) ?? $value;
    $value = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $value) ?? $value;
    $allowed = '<p><br><strong><b><em><i><u><ul><ol><li><a><span><div><table><thead><tbody><tfoot><tr><th><td><img>';
    $safeHtml = strip_tags($value, $allowed);
    $safeHtml = preg_replace_callback('/\s(href|src)\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', static function (array $m): string {
        $attr = strtolower((string)$m[1]);
        $uri = trim((string)$m[2], "\"'");
        $check = strtolower(trim($uri));
        if (str_starts_with($check, 'javascript:') || str_starts_with($check, 'data:text/html')) {
            $uri = '#';
        }
        return ' ' . $attr . '="' . htmlspecialchars($uri, ENT_QUOTES) . '"';
    }, $safeHtml) ?? $safeHtml;

    if (strip_tags($value) === $value) {
        return nl2br(htmlspecialchars($value));
    }

    return trim(str_replace(['☐', '☑', '□'], '', $safeHtml));
}

$brand_name = htmlspecialchars(andison_brand_display_name($brand_input));

// Map all brand names to their logo filenames
$logo_filename = function($brand) {
    $logoMap = [
        'Panasonic Connect' => 'PANASONIC',
        'BW' => 'BW TECHNOLOGIES',
        'BW Technologies' => 'BW TECHNOLOGIES',
        'Weldcraft' => 'WELDCRAFT',
        'Soyer' => 'SOYER',
        'Alfra' => 'ALFRA',
        'ACES' => 'ACES',
        'UVEX' => 'UVEX',
        'ANSELL' => 'ANSELL',
        'MICROGARD' => 'MICROGARD',
        'AlphaTec' => 'MICROGARD',
        'WELDAS' => 'WELDAS',
        'TANAKA' => 'TANAKA',
        'CHIYODA' => 'CHIYODA',
        'HARDWORKER' => 'HARDWORKER',
        'Hard Workers' => 'HARDWORKER',
        'Magnaflux' => 'MAGNAFLUX',
        'MAGNAFLUX' => 'MAGNAFLUX',
        'COPPUS' => 'COPPUS',
        'BOSCH' => 'BOSCH',
        'MOTOLITE' => 'MOTOLITE',
        'Aquasol' => 'AQUASOL',
        'Arcair' => 'ARCAIR',
        'Dalo' => 'DALO',
        'Dryrod' => 'DRYROD',
        'DryRod. II' => 'DRYROD',
        'Garryson' => 'GARRYSON',
        'Kobelco' => 'KOBELCO',
        'Makita' => 'MAKITA',
        'Metrode' => 'METRODE',
        'RAE SYSTEMS' => 'RAE SYSTEMS',
        'ROBOT SYSTEMS' => 'ROBOT SYSTEMS',
        'RAC' => 'RAE SYSTEMS',
        'SK And GAL GAGE' => 'SK AND GAL GAGE',
        'Spilfyter' => 'SPILFYTER',
        'Tempilstik' => 'TEMPILSTIK',
        'Truweld' => 'TRUWELD',
        'Weiler' => 'WEILER',
        'Weller' => 'WEILER',
        'Yutaka' => 'YUTAKA'
    ];
    return isset($logoMap[$brand]) ? $logoMap[$brand] : strtoupper($brand);
};

$brands_info = andison_get_brands_info();
$brand_key = andison_pick_brand_bucket($brands_info, andison_brand_data_candidates($brand_input));
$brand_info = isset($brands_info[$brand_key]) ? $brands_info[$brand_key] : [
    'description' => 'Professional industrial products and solutions.',
    'products' => []
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $brand_name; ?> - ANDISON INDUSTRIAL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
            background: #f9f9f9;
            padding: 28px 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 28px;
        }

        a.back-link {
            color: #2B11DB;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 20px;
        }

        a.back-link:hover {
            text-decoration: underline;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-bottom: 30px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .brand-logo-section {
            flex-shrink: 0;
        }

        .brand-logo {
            max-width: 200px;
            max-height: 150px;
            object-fit: contain;
        }

        .brand-info {
            flex: 1;
        }

        .brand-info h1 {
            color: #2B11DB;
            font-size: 32px;
            margin-bottom: 15px;
        }

        .brand-info p {
            color: #666;
            font-size: 16px;
            line-height: 1.6;
        }

        .brand-rich-description table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .brand-rich-description table.desc-custom-table {
            background: #121722;
            color: #e5e7eb;
            table-layout: fixed;
            border: 1px solid #404756;
            border-radius: 10px;
            overflow: hidden;
        }

        .brand-rich-description th,
        .brand-rich-description td {
            border: 1px solid #d1d5db;
            padding: 7px 9px;
            vertical-align: top;
            text-align: left;
        }

        .brand-rich-description table.desc-custom-table th,
        .brand-rich-description table.desc-custom-table td {
            border-color: #404756;
            background: #1a202c;
            color: #e5e7eb;
            padding: 10px 12px;
            line-height: 1.6;
        }

        .brand-rich-description table.desc-custom-table .desc-head-label {
            background: #22293a;
            font-weight: 800;
        }

        .brand-rich-description table.desc-custom-table .desc-cell-editor {
            display: block;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .brand-rich-description table.desc-custom-table .desc-cell-image-wrap {
            position: relative;
            display: inline-block;
            margin: 8px 8px 0 0;
            padding: 2px;
            border: 1px solid rgba(96, 165, 250, 0.25);
            border-radius: 8px;
            background: rgba(17, 24, 39, 0.85);
        }

        .brand-rich-description table.desc-custom-table .desc-cell-image-wrap img {
            display: block;
            max-width: 100%;
            height: auto;
            border-radius: 6px;
        }

        .brand-rich-description img {
            max-width: 100%;
            height: auto;
        }

        .products-section {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .products-section h2 {
            color: #2B11DB;
            font-size: 24px;
            margin-bottom: 20px;
            border-bottom: 2px solid #2B11DB;
            padding-bottom: 10px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .product-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            background: #f5f5f5;
            border-radius: 4px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.2);
        }

        .product-card h4 {
            color: #333;
            font-size: 14px;
            margin: 10px 0;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card p {
            color: #888;
            font-size: 12px;
            margin-bottom: 12px;
        }

        .product-badge {
            display: inline-block;
            background: #2B11DB;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin-bottom: 8px;
        }

        .add-to-inquiry {
            background: #2B11DB;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            margin-top: auto;
            transition: background 0.3s;
        }

        .add-to-inquiry:hover {
            background: #1f0aa1;
        }

        .add-to-inquiry.already {
            background: #4caf50;
        }

        .no-products {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 14px;
            background: #f9f9f9;
            border-radius: 4px;
        }

        .inquiry-toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(0);
            background: #2B11DB;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 14px;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .brand-header {
                flex-direction: column;
                text-align: center;
                gap: 20px;
            }

            .brand-logo {
                max-width: 150px;
            }

            .brand-info h1 {
                font-size: 24px;
            }

            .products-section {
                padding: 20px;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 15px;
            }
        }
    </style>
    <!-- Inquiry List Handler -->
    <script src="../assets/js/inquiry-handler.js"></script>
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
    <div class="container">
        <a href="home.php" class="back-link">← Back to Home</a>

        <div class="brand-header">
            <div class="brand-logo-section">
                <img src="../assets/brands/<?php echo htmlspecialchars(urlencode($logo_filename($brand_name))); ?>.jpg" 
                     alt="<?php echo $brand_name; ?>" 
                     class="brand-logo" 
                     onerror="this.style.display='none';">
            </div>
            <div class="brand-info">
                <h1><?php echo $brand_name; ?></h1>
                <div class="brand-rich-description"><?php echo andison_render_brand_description($brand_info['description'] ?? ''); ?></div>
            </div>
        </div>

        <div class="products-section">
            <h2>Product Range</h2>
            <?php if(!empty($brand_info['products'])): ?>
                <div class="product-grid">
                    <?php foreach($brand_info['products'] as $product): ?>
                        <?php
                            $product_model = is_array($product) ? (string)($product['model'] ?? '') : (string)$product;
                            $product_type = is_array($product) ? (isset($product['type']) ? $product['type'] : '') : 'Product';
                            $product_image = is_array($product) && !empty($product['image']) ? $product['image'] : '';
                            $product_badge = is_array($product) && !empty($product['badge']) ? $product['badge'] : '';
                        ?>
                        <div class="product-card">
                            <?php if($product_image): ?>
                                <div class="product-image">
                                    <img src="<?php echo $product_image; ?>" alt="<?php echo $product_model; ?>" onerror="this.style.display='none'; this.parentElement.innerHTML='<span style=\"color:#ccc;\">No image</span>';">
                                </div>
                            <?php else: ?>
                                <div class="product-image">
                                    <span style="color:#ccc;">No image</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($product_badge): ?>
                                <span class="product-badge"><?php echo $product_badge; ?></span>
                            <?php endif; ?>
                            
                            <h4><?php echo $product_model; ?></h4>
                            <p><?php echo $product_type; ?></p>
                            
                            <button class="add-to-inquiry" 
                                type="button"
                                data-model="<?php echo htmlspecialchars($product_model, ENT_QUOTES); ?>"
                                data-type="<?php echo htmlspecialchars($product_type, ENT_QUOTES); ?>"
                                data-brand="<?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?>">
                                ADD TO INQUIRY LIST
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-products">
                    No products yet — add product blocks or include a catalog.
                </div>
            <?php endif; ?>
        </div>
    </div>

    </script>
    <script src="../assets/js/scroll-fade.js"></script>
</body>
</html>







