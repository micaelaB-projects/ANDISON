<?php
declare(strict_types=1);

require_once __DIR__ . '/../andison/includes/categories_info.php';
require_once __DIR__ . '/../andison/includes/products_management.php';

$category_id = "drilling-and-lifting";
$subcategory_id = "lifting";

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
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_subcategory['name']); ?> - ANDISON INDUSTRIAL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;color:#333;padding-top:142px;min-height:100vh;display:flex;flex-direction:column;background:#f8f9fa}header{background:linear-gradient(135deg,#2B11DB 0%,#2B11DB 100%);color:white;padding:14px 0;position:fixed;top:0;left:0;right:0;z-index:1001;width:100%}.header-top{display:flex;align-items:center;max-width:1200px;margin:0 auto;padding:0 20px;gap:20px;margin-bottom:12px}.logo img{height:50px;width:auto}.search-bar{flex:1;display:flex;justify-content:center;max-width:600px;margin:0 auto}.search-bar input{width:100%;height:40px;padding:10px 16px 10px 40px;border:2px solid rgba(255,255,255,.3);border-radius:6px;font-size:15px;background:rgba(255,255,255,.95);color:#333}.search-bar .search-field{width:100%;display:flex;align-items:center;position:relative}.search-bar .search-field::before{content:'🔍';position:absolute;left:12px;pointer-events:none}.inquiry-btn{background:linear-gradient(135deg,#00D7B3 0%,#00D7B3 100%);color:#1a1a2e;border:none;padding:10px 24px;border-radius:25px;font-weight:700;cursor:pointer;box-shadow:0 4px 15px rgba(0,217,255,.3);text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all .3s ease;font-size:14px}.inquiry-btn:hover{background:linear-gradient(135deg,#00E6FF 0%,#00C8F7 100%);box-shadow:0 6px 20px rgba(0,217,255,.5);transform:translateY(-2px)}.right-actions{margin-left:auto;display:flex;align-items:center;gap:12px}nav{background:rgba(0,215,179,.85)}.nav-inner{max-width:1200px;margin:0 auto;padding:0 20px;display:flex;align-items:center;min-height:52px}.nav-list{list-style:none;display:flex;gap:28px;margin:0;padding:0}.nav-list a{color:white;text-decoration:none;font-size:15px;padding:12px 6px}.category-container{max-width:1500px;margin:40px auto;padding:0 40px;flex:1}.category-header{background:white;border-radius:10px;padding:40px;box-shadow:0 2px 10px rgba(0,0,0,.1);margin-bottom:30px;text-align:center}.category-header h1{color:#2B11DB;font-size:36px;margin-bottom:15px}.category-header p{color:#666;font-size:16px;max-width:800px;margin:0 auto}.category-content{background:white;border-radius:10px;padding:40px;box-shadow:0 2px 10px rgba(0,0,0,.1)}.category-content h2{color:#2B11DB;font-size:24px;margin-bottom:30px;text-transform:uppercase;letter-spacing:1px}.product-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-bottom:40px}.product-card{background:white;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1);transition:transform .3s ease,box-shadow .3s ease;display:flex;flex-direction:column;border:2px solid transparent;min-height:100%}.product-card:hover{transform:translateY(-5px);box-shadow:0 5px 20px rgba(0,0,0,.15);border-color:#2B11DB}.product-image{width:100%;height:200px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;overflow:hidden}.product-image img{width:100%;height:100%;object-fit:contain;padding:10px}.product-card h4{color:#2B11DB;font-size:16px;font-weight:600;padding:15px 15px 5px;line-height:1.4}.product-model{color:#999;font-size:12px;padding:0 15px;margin:3px 0}.product-description{color:#666;font-size:12px;padding:0 15px 12px;line-height:1.5;max-height:60px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;margin-bottom:12px}.add-to-inquiry{margin:0 15px 15px;padding:12px 20px;background:#2B11DB;color:white;border:none;border-radius:5px;font-weight:600;cursor:pointer;transition:background .3s;font-size:14px;width:calc(100% - 30px);text-transform:uppercase;margin-top:auto}footer{background:#2B11DB;color:white;text-align:center;padding:30px 20px;margin-top:auto}</style>
</head>
<body>
    <header>
        <div class="header-top">
            <div class="logo"><div class="logo-box"><a href="../home.php"><img src="../assets/HOME/image-removebg-preview.png" alt="Andison Industrial" /></a></div></div>
            <div class="search-bar"><form class="search-field" action="../search.php" method="get"><input type="text" name="q" placeholder="Search"></form></div>
            <div class="right-actions"><a href="javascript:history.back()" class="inquiry-btn">BACK</a><a href="../inquirylist.php" class="inquiry-btn">INQUIRY LIST</a></div>
        </div>
        <nav><div class="nav-inner"><ul class="nav-list"><li><a href="../home.php">Home</a></li></ul></div></nav>
    </header>
    <div class="category-container">
        <div class="category-header">
            <h1><?php echo htmlspecialchars($current_subcategory['name']); ?></h1>
            <p><?php echo htmlspecialchars($current_category['description']); ?></p>
        </div>
        <div class="category-content">
            <h2><?php echo htmlspecialchars($current_subcategory['name']); ?></h2>
            <div class="product-grid">
                <?php if(!empty($products)):
                    foreach($products as $product):
                        $image_src=htmlspecialchars($product['image']??'');
                        if($image_src&&strpos($image_src,'andison/')===0)$image_src='../'.$image_src;
                        $model=htmlspecialchars($product['model']??'');
                        $name=htmlspecialchars($product['name']??'');
                ?>
                <div class="product-card">
                    <div class="product-image"><?php if(!empty($image_src)):?><img src="<?php echo $image_src;?>" alt="<?php echo $name;?>"><?php else:?><i class="bi bi-hammer" style="font-size:60px;color:#ccc"></i><?php endif;?></div>
                    <h4><?php echo $name?:'Product';?></h4>
                    <?php if(!empty($model)):?><p class="product-model">Model: <?php echo $model;?></p><?php endif;?>
                    <button class="add-to-inquiry" type="button" data-model="<?php echo $model;?>" data-type="<?php echo htmlspecialchars($product['type']??'Equipment');?>" data-brand="<?php echo htmlspecialchars($product['brand']??'Industrial');?>">ADD TO INQUIRY</button>
                </div>
                <?php endforeach; else:?>
                <div class="product-card" style="grid-column:1/-1;text-align:center">
                    <div class="product-image"><i class="bi bi-hammer" style="font-size:60px;color:#ccc"></i></div>
                    <h4><?php echo htmlspecialchars($current_subcategory['name']);?></h4>
                    <p class="product-description">No products available</p>
                </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <footer><div>&copy; 2026 ANDISON INDUSTRIAL</div></footer>
    <script>document.addEventListener('click',function(e){var btn=e.target.closest('.add-to-inquiry');if(!btn)return;var items=JSON.parse(localStorage.getItem('inquiryItems')||'[]');var found=items.find(function(i){return i.model===btn.dataset.model});if(!found){items.push({model:btn.dataset.model,type:btn.dataset.type,brand:btn.dataset.brand,qty:1});localStorage.setItem('inquiryItems',JSON.stringify(items))}});</script>
</body>
</html>