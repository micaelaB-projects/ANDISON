<?php
require_once __DIR__ . '/andison/includes/analytics.php';
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
        body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height:1.6; color:#333; background: linear-gradient(135deg, #f8f9fa 0%, #f0f2f5 100%); padding-top:110px; min-height: 100vh; }

        header { background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%); color:white; padding:14px 0; position:fixed; top:0; left:0; right:0; z-index:1000; box-shadow: 0 4px 16px rgba(43, 17, 219, 0.2); }
        .header-top { display:flex; align-items:center; max-width:1200px; margin:0 auto; padding:0 20px; gap:20px; }
        .logo-box img { height:50px; width:auto; display:block; }

        .search-bar { flex: 1 1 auto; display:flex; justify-content:center; max-width: 720px; margin:0 auto; }
        .search-bar .search-field { width:100%; display:flex; align-items:center; gap:8px; position:relative; margin:0; }
        .search-bar input { width:100%; height:46px; padding:10px 16px 10px 40px; border:2px solid rgba(255,255,255,0.3); border-radius:6px; font-size:15px; background: rgba(255,255,255,0.95); color:#333; transition: all 0.3s ease; }
        .search-bar input:focus { outline: none; background: #fff; border-color: rgba(255,255,255,0.8); box-shadow: 0 0 0 3px rgba(43, 17, 219, 0.1); }
        .search-bar input::placeholder { color:#999; }
        .search-bar .search-field::before { content:'🔍'; position:absolute; left:12px; font-size:16px; pointer-events:none; color:#666; }

        .right-actions { margin-left:auto; display:flex; align-items:center; gap:10px; }
        .back-btn { 
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); 
            color: #333; 
            border: none; 
            padding: 10px 18px; 
            border-radius: 6px; 
            font-weight: 600; 
            cursor: pointer; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            gap: 8px; 
            font-size: 15px; 
            line-height: 1.3; 
            height: auto; 
            min-height: 40px; 
            transition: all 0.3s ease; 
        }
        .back-btn:hover { 
            background: linear-gradient(135deg, #00FFD1 0%, #00FFD1 100%); 
            transform: translateY(-2px); 
            box-shadow: 0 4px 12px rgba(0, 255, 209, 0.4); 
            color: #333; 
        }
        .inquiry-btn,
        .cart-icon-wrapper { display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; text-decoration: none; color: #1a1a2e; font-weight: 700; padding: 12px 28px; border-radius: 25px; transition: all 0.35s ease; background: linear-gradient(135deg, #00E5C8  0%, #347aec   100%); line-height: 1.3; height: auto; min-height: 40px; }
        .inquiry-btn:hover,
        .cart-icon-wrapper:hover { background: linear-gradient(135deg, #00FFD1 0%, #00FFD1 100%); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 255, 209, 0.4); color: #1a1a2e; }
        
        .inquiry-btn,
        .cart-icon-wrapper {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 6px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
        }

        .inquiry-btn:hover,
        .cart-icon-wrapper:hover {
            background: linear-gradient(135deg, #00FFD1 0%, #00FFD1 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 255, 209, 0.4);
            color: #333;
        }

        .inquiry-btn {
            position: relative;
        }

        .cart-badge {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            text-align: center;
            line-height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(255, 102, 102, 0.4);
            flex-shrink: 0;
        }

        .cart-badge.hidden {
            display: none;
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

        @media (max-width: 768px) {
            body { padding-top: 140px; }
            .header-top { flex-direction: column; align-items: stretch; gap: 12px; }
            .logo-box { text-align: center; }
            .right-actions { margin-left: 8px; margin-right: 8px; padding-right: 8px; justify-content: flex-end; gap: 8px; }
            .container { padding: 28px 16px; }
            .results-header { flex-direction: column; align-items: flex-start; gap: 16px; margin-bottom: 32px; }
            .results-header h1 { font-size: 28px; }
            .results-grid { grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
            .result-card { min-height: 300px; }
            .result-image { height: 160px; }
            .result-body { padding: 20px; }
        }

        @media (max-width: 480px) {
            .header-top { padding: 0 12px; }
            .back-btn, .inquiry-btn { padding: 8px 16px; font-size: 12px; }
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
<header>
    <div class="header-top">
        <div class="logo">
            <div class="logo-box"><a href="home.php"><img src="assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div>
        </div>

        <div class="search-bar">
            <form class="search-field" action="search.php" method="get">
                <input type="text" name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES); ?>" placeholder="Search for products" />
            </form>
        </div>

        <div class="right-actions">
            <a href="javascript:history.back()" class="back-btn">BACK</a>
            <a href="inquirylist.php" class="inquiry-btn">INQUIRY LIST <span class="cart-badge hidden" id="cartBadge">0</span></a>
        </div>
    </div>
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

</body>
</html>



