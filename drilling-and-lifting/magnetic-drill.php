<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/brands_info.php';
require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$category_id = "drilling-and-lifting";
$subcategory_id = "magnetic-drill";
$phone = "+1(234) 567 8900";
$phone2 = "+1(234) 567 8900";
$phone3 = "+1(639) 977 803 7398";
$email = "info@andison-industrial.com";

$categories = andison_get_categories();
$current_category = null;
$current_subcategory = null;

foreach ($categories as $cat) {
    if ($cat['id'] === $category_id) {
        $current_category = $cat;
        foreach ($cat['subcategories'] as $subcat) {
            if ($subcat['id'] === $subcategory_id) {
                $current_subcategory = $subcat;
                break;
            }
        }
        break;
    }
}

if (!$current_category || !$current_subcategory) {
    die("Category or subcategory not found");
}

$products = andison_get_products_for_subcategory($category_id, $subcategory_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_subcategory['name']); ?> - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; padding-top: 142px; min-height: 100vh; display: flex; flex-direction: column; background: #f8f9fa; }
        header { background: linear-gradient(135deg, #2B11DB 0%, #2B11DB 100%); color: white; padding: 14px 0; position: fixed; top: 0; left: 0; right: 0; z-index: 1001; width: 100%; }
        .header-top { display: flex; align-items: center; max-width: 1200px; margin: 0 auto; padding: 0 20px; gap: 20px; margin-bottom: 12px; }
        .logo img { height: 50px; width: auto; display: block; }
        .search-bar { flex: 1 1 auto; display: flex; justify-content: center; max-width: 600px; margin: 0 auto; }
        .search-bar input { width: 100%; height: 40px; padding: 10px 16px 10px 40px; border: 2px solid rgba(255,255,255,0.3); border-radius: 6px; font-size: 15px; background: rgba(255,255,255,0.95); color: #333; }
        .search-bar .search-field { width: 100%; display: flex; align-items: center; position: relative; }
        .search-bar .search-field::before { content: '🔍'; position: absolute; left: 12px; pointer-events: none; }
        .inquiry-btn { background: linear-gradient(135deg, #00D7B3 0%, #00D7B3 100%); color: #1a1a2e; border: none; padding: 10px 24px; border-radius: 25px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(0, 217, 255, 0.3); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; font-size: 14px; letter-spacing: 0.5px; }
        .inquiry-btn:hover { background: linear-gradient(135deg, #00E6FF 0%, #00C8F7 100%); box-shadow: 0 6px 20px rgba(0, 217, 255, 0.5); transform: translateY(-2px); }
        .right-actions { margin-left: auto; display: flex; align-items: center; gap: 12px; }
        nav { background: rgba(0, 215, 179, 0.85); position: relative; }
        .nav-inner { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; align-items: center; min-height: 52px; gap: 18px; }
        .nav-list { list-style: none; display: flex; gap: 28px; margin: 0; padding: 0; }
        .nav-list a { color: white; text-decoration: none; font-size: 15px; padding: 12px 6px; display: block; transition: color 0.2s; }
        .category-container { max-width: 1500px; margin: 40px auto; padding: 0 40px; flex: 1; }
        .category-header { background: linear-gradient(135deg, #f8f9fa 0%, #f0f0f0 100%); border-radius: 12px; padding: 45px 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 35px; text-align: center; display: none; }
        .category-header h1 { color: #2B11DB; font-size: 42px; margin-bottom: 18px; font-weight: 800; letter-spacing: -0.5px; }
        .category-header p { color: #666; font-size: 16px; max-width: 700px; margin: 0 auto; line-height: 1.6; }
        .category-content { background: white; border-radius: 12px; padding: 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
        .category-content h2 { color: #2B11DB; font-size: 28px; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; text-align: center; }
        .product-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 24px; margin-bottom: 40px; margin-top: 30px; max-width: 1400px; margin-left: auto; margin-right: auto; }
        .product-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; display: flex; flex-direction: column; border: 1px solid #f0f0f0; min-height: 100%; }
        .product-card:hover { transform: translateY(-8px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); border-color: #2B11DB; }
        .product-image { width: 100%; aspect-ratio: 1 / 1; background: white; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; flex-shrink: 0; }
        .product-image img { width: 100%; height: 100%; object-fit: contain; padding: 15px; }
        .product-card h4 { color: #2B11DB; font-size: 15px; font-weight: 700; padding: 16px 16px 8px; line-height: 1.4; }
        .product-model { color: #666; font-size: 12px; padding: 0 16px; margin: 4px 0 8px; font-weight: 500; }
        .product-description { color: #666; font-size: 12px; padding: 0 16px 12px; line-height: 1.5; max-height: 60px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; margin-bottom: 12px; flex-grow: 1; }
        .add-to-inquiry { margin: 0 16px 16px; padding: 11px 16px; background: linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%); color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer; transition: all 0.3s ease; font-size: 13px; width: calc(100% - 32px); text-transform: uppercase; letter-spacing: 0.6px; margin-top: auto; flex-shrink: 0; box-shadow: 0 2px 8px rgba(43, 17, 219, 0.2); }
        .add-to-inquiry:hover { background: linear-gradient(135deg, #1f0aa1 0%, #140570 100%); box-shadow: 0 4px 12px rgba(43, 17, 219, 0.35); transform: translateY(-2px); }
        footer { background: #2B11DB; color: white; text-align: center; padding: 30px 20px; margin-top: auto; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #2B11DB; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        @media (max-width: 1400px) {
            .product-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }
        }

        @media (max-width: 1200px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            body { padding-top: 120px; }
            .category-container { padding: 0 20px; }
            .category-header { padding: 35px 25px; margin-bottom: 30px; }
            .category-header h1 { font-size: 28px; margin-bottom: 12px; }
            .category-header p { font-size: 15px; }
            .category-content { padding: 25px; }
            .category-content h2 { font-size: 22px; margin-bottom: 20px; }
            .product-grid { grid-template-columns: repeat(3, 1fr); gap: 16px; margin-top: 20px; }
            .product-image { aspect-ratio: 1 / 1; }
            .product-card h4 { font-size: 14px; padding: 12px 12px 6px; }
            .product-model { padding: 0 12px; font-size: 11px; }
            .add-to-inquiry { margin: 0 12px 12px; padding: 9px 12px; font-size: 12px; width: calc(100% - 24px); }
        }

        @media (max-width: 480px) {
            .category-container { padding: 0 15px; }
            .category-header { padding: 25px 15px; }
            .category-header h1 { font-size: 22px; margin-bottom: 10px; }
            .category-content { padding: 15px; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .product-card h4 { font-size: 13px; }
            .add-to-inquiry { padding: 8px 10px; font-size: 11px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-top">
            <div class="logo"><div class="logo-box"><a href="../home.php"><img src="../assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div></div>
            <div class="search-bar"><form class="search-field" action="../search.php" method="get"><input type="text" name="q" placeholder="Search for products"></form></div>
            <div class="right-actions">
                <a href="javascript:history.back()" class="inquiry-btn">BACK</a>
                <a href="../inquirylist.php" class="inquiry-btn">INQUIRY LIST</a>
            </div>
        </div>
        <nav><div class="nav-inner"><ul class="nav-list"><li><a href="../home.php">Home</a></li><li><a href="../brands.php">Brands</a></li><li><a href="../contact.php">Contact Us</a></li></ul></div></nav>
    </header>

    <div class="category-container">
        <div class="breadcrumb">
            <a href="../home.php">Home</a> / 
            <a href="./drilling-and-lifting.php"><?php echo htmlspecialchars($current_category['name']); ?></a> / 
            <strong><?php echo htmlspecialchars($current_subcategory['name']); ?></strong>
        </div>

        <div class="category-header">
            <h1><?php echo htmlspecialchars($current_subcategory['name']); ?></h1>
            <p><?php echo htmlspecialchars($current_category['description']); ?></p>
        </div>

        <div class="category-content">
            <h2><?php echo htmlspecialchars($current_subcategory['name']); ?></h2>
            <div class="product-grid">
                <?php 
                if (!empty($products)) {
                    foreach ($products as $product) {
                        $image_src = htmlspecialchars($product['image'] ?? '');
                        if ($image_src && strpos($image_src, 'andison/') === 0) {
                            $image_src = '../' . $image_src;
                        }
                        $model = htmlspecialchars($product['model'] ?? '');
                        $name = htmlspecialchars($product['name'] ?? '');
                        $type = htmlspecialchars($product['type'] ?? 'Equipment');
                        $brand = htmlspecialchars($product['brand'] ?? 'Industrial');
                        $description = htmlspecialchars($product['description'] ?? '');
                ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($image_src)): ?>
                            <img src="<?php echo $image_src; ?>" alt="<?php echo $name; ?>" onerror="this.parentElement.innerHTML='<i class=&quot;bi bi-hammer&quot; style=&quot;font-size: 60px; color: #ccc;&quot;></i>'">
                        <?php else: ?>
                            <i class="bi bi-hammer" style="font-size: 60px; color: #ccc;"></i>
                        <?php endif; ?>
                    </div>
                    <h4><?php echo $name ?: 'Product'; ?></h4>
                    <?php if (!empty($model)): ?><p class="product-model"><strong>Model:</strong> <?php echo $model; ?></p><?php endif; ?>
                    <?php if (!empty($description)): ?><p class="product-description"><?php echo $description; ?></p><?php endif; ?>
                    <button class="add-to-inquiry" type="button" data-model="<?php echo $model; ?>" data-type="<?php echo $type; ?>" data-brand="<?php echo $brand; ?>">ADD TO INQUIRY</button>
                </div>
                <?php
                    }
                } else {
                ?>
                <div class="product-card" style="grid-column: 1/-1; text-align: center;">
                    <div class="product-image"><i class="bi bi-hammer" style="font-size: 60px; color: #ccc;"></i></div>
                    <h4><?php echo htmlspecialchars($current_subcategory['name']); ?></h4>
                    <p class="product-description">No products available yet</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <footer><div>&copy; 2026 ANDISON INDUSTRIAL. All rights reserved.</div></footer>

    <script>
        (function(){
            function getItems(){ try{ return JSON.parse(localStorage.getItem('inquiryItems')||'[]'); }catch(e){ return []; } }
            function setItems(items){ localStorage.setItem('inquiryItems', JSON.stringify(items)); }
            function addItem(item){
                var items = getItems();
                var found = items.find(function(i){ return i.model === item.model && i.brand === item.brand; });
                if(found) return false;
                item.qty = 1; items.push(item);
                setItems(items);
                return true;
            }
            function showToast(msg){
                var t = document.querySelector('.inquiry-toast');
                if(!t){ t = document.createElement('div'); t.className = 'inquiry-toast'; document.body.appendChild(t); }
                t.textContent = msg;
                t.style.opacity = '1';
                clearTimeout(t._hide);
                t._hide = setTimeout(function(){ t.style.opacity = '0'; }, 1800);
            }
            document.addEventListener('click', function(e){
                var btn = e.target.closest('.add-to-inquiry');
                if(!btn) return;
                var added = addItem({ model: btn.dataset.model, type: btn.dataset.type, brand: btn.dataset.brand });
                showToast(added ? 'Added to inquiry list!' : 'Already in inquiry list');
            });
        })();
    </script>
</body>
</html>