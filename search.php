<?php
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
        body { font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height:1.6; color:#333; background:#fff; padding-top:110px; }

        header { background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%); color:white; padding:14px 0; position:fixed; top:0; left:0; right:0; z-index:1000; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        .header-top { display:flex; align-items:center; max-width:1200px; margin:0 auto; padding:0 20px; gap:20px; }
        .logo-box img { height:50px; width:auto; display:block; }

        .search-bar { flex: 1 1 auto; display:flex; justify-content:center; max-width: 720px; margin:0 auto; }
        .search-bar .search-field { width:100%; display:flex; align-items:center; gap:8px; position:relative; margin:0; }
        .search-bar input { width:100%; height:40px; padding:10px 16px 10px 40px; border:2px solid rgba(255,255,255,0.3); border-radius:6px; font-size:15px; background: rgba(255,255,255,0.95); color:#333; }
        .search-bar input::placeholder { color:#999; }
        .search-bar .search-field::before { content:'🔍'; position:absolute; left:12px; font-size:16px; pointer-events:none; color:#666; }

        .right-actions { margin-left:auto; display:flex; align-items:center; gap:12px; }
        .back-btn { background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); color:#1a1a2e; border:none; padding:10px 24px; border-radius:25px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-size:14px; letter-spacing:0.5px; box-shadow:0 4px 15px rgba(0, 217, 255, 0.3); transition:all 0.3s ease; }
        .back-btn:hover { background:linear-gradient(135deg, #00E6FF 0%, #00C8F7 100%); box-shadow:0 6px 20px rgba(0, 217, 255, 0.5); transform:translateY(-2px); }
        .inquiry-btn { background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); color:#1a1a2e; border:none; padding:10px 24px; border-radius:25px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-size:14px; letter-spacing:0.5px; box-shadow:0 4px 15px rgba(0, 217, 255, 0.3); transition:all 0.3s ease; }
        .inquiry-btn:hover { background:linear-gradient(135deg, #00E6FF 0%, #00C8F7 100%); box-shadow:0 6px 20px rgba(0, 217, 255, 0.5); transform:translateY(-2px); }

        .container { max-width: 1200px; margin: 0 auto; padding: 24px 20px; }
        .results-header { display:flex; align-items:baseline; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom: 18px; }
        .results-header h1 { font-size: 26px; color:#2b00d9; }
        .results-header .count { color:#666; font-size: 14px; }

        .results-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .result-card { background:#fff; border:1px solid #e9ecef; border-radius: 12px; overflow:hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); display:flex; flex-direction:column; min-height: 260px; }
        .result-image { height: 150px; background: #f5f6f8; display:flex; align-items:center; justify-content:center; }
        .result-image img { width:100%; height:100%; object-fit:contain; }
        .result-body { padding: 14px 14px 16px; display:flex; flex-direction:column; gap: 6px; flex: 1 1 auto; }
        .result-brand { font-size: 12px; color:#2b00d9; font-weight:800; letter-spacing: 0.3px; text-transform: uppercase; }
        .result-model { font-size: 15px; font-weight: 800; color:#222; }
        .result-type { font-size: 13px; color:#666; }
        .result-actions { margin-top:auto; padding-top: 10px; }
        .result-actions a { text-decoration:none; }
        .view-brand-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%);
            color: #2E2E2E;
            border: none;
            padding: 10px 14px;
            border-radius: 999px;
            font-weight: 900;
            letter-spacing: 0.3px;
            box-shadow: 0 8px 18px rgba(0, 215, 179, 0.28);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }
        .view-brand-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(0, 215, 179, 0.38);
            filter: brightness(0.98);
        }
        .view-brand-btn:active {
            transform: translateY(0);
        }

        .no-results { padding: 28px 18px; border: 2px dashed #d5d7df; border-radius: 12px; background: #fafbff; color:#2b00d9; font-weight: 900; text-align:center; letter-spacing: 1px; }
        .hint { padding: 18px; border-radius: 12px; background: #f7f8ff; border:1px solid #e8eaff; color:#333; }

        @media (max-width: 768px) {
            body { padding-top: 140px; }
            .header-top { flex-direction: column; align-items: stretch; }
            .right-actions { margin-left: 0; justify-content: space-between; }
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
            <a href="inquirylist.php" class="inquiry-btn">INQUIRY LIST</a>
        </div>
    </div>
</header>

<main class="container">
    <div class="results-header">
        <h1>Search Results</h1>
        <?php if ($q !== ''): ?>
            <div class="count">Query: <strong><?php echo htmlspecialchars($q); ?></strong> • <?php echo count($matches); ?> result(s)</div>
        <?php else: ?>
            <div class="count">Type a product name/model to search</div>
        <?php endif; ?>
    </div>

    <?php if ($q === ''): ?>
        <div class="hint">
            Try searching for a <strong>brand</strong>, <strong>model</strong>, or <strong>type</strong> (example: <em>Panasonic</em>, <em>YD-350</em>, <em>Gloves</em>).
        </div>
    <?php elseif (count($matches) === 0): ?>
        <div class="no-results">NO RESULT FOUND.</div>
    <?php else: ?>
        <div class="results-grid">
            <?php foreach ($matches as $item): ?>
                <div class="result-card">
                    <div class="result-image">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars($item['model'], ENT_QUOTES); ?>" onerror="this.style.display='none'; this.parentElement.textContent='No Image';" />
                        <?php else: ?>
                            <span style="font-weight:800;color:#888;">No Image</span>
                        <?php endif; ?>
                    </div>
                    <div class="result-body">
                        <div class="result-brand"><?php echo htmlspecialchars($item['brand']); ?></div>
                        <div class="result-model"><?php echo htmlspecialchars($item['model']); ?></div>
                        <div class="result-type"><?php echo htmlspecialchars($item['type']); ?></div>
                        <div class="result-actions">
                            <a class="view-brand-btn" href="brand.php?name=<?php echo urlencode($item['brand']); ?>&product=<?php echo urlencode($item['model']); ?>">
                                View product <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
