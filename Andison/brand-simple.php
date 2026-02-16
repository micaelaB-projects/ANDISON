<?php
require_once __DIR__ . '/includes/brands_info.php';

$brand_name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Brand';

// Map all brand names to their logo filenames
$logo_filename = function($brand) {
    $logoMap = [
        'Panasonic Connect' => 'PANASONIC',
        'BW Technologies' => 'BW TECHNOLOGIES',
        'Weldcraft' => 'WELDCRAFT',
        'Soyer' => 'SOYER',
        'Alfra' => 'ALFRA',
        'ACES' => 'ACES',
        'UVEX' => 'UVEX',
        'ANSELL' => 'ANSELL',
        'MICROGARD' => 'MICROGARD',
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
$brand_info = isset($brands_info[$brand_name]) ? $brands_info[$brand_name] : [
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
</head>
<body>
    <div class="container">
        <a href="home.php" class="back-link">← Back to Home</a>

        <div class="brand-header">
            <div class="brand-logo-section">
                <img src="assets/brands/<?php echo htmlspecialchars(urlencode($logo_filename($brand_name))); ?>.jpg" 
                     alt="<?php echo $brand_name; ?>" 
                     class="brand-logo" 
                     onerror="this.style.display='none';">
            </div>
            <div class="brand-info">
                <h1><?php echo $brand_name; ?></h1>
                <p><?php echo $brand_info['description']; ?></p>
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
                item.qty = 1;
                items.push(item);
                setItems(items);
                return true;
            }

            function showToast(msg){
                var t = document.querySelector('.inquiry-toast');
                if(!t){
                    t = document.createElement('div');
                    t.className = 'inquiry-toast';
                    document.body.appendChild(t);
                }
                t.textContent = msg;
                requestAnimationFrame(function(){
                    t.style.opacity = '1';
                    t.style.transform = 'translateX(-50%) translateY(-6px)';
                });
                clearTimeout(t._hide);
                t._hide = setTimeout(function(){
                    t.style.opacity = '0';
                    t.style.transform = 'translateX(-50%) translateY(0)';
                }, 1800);
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
                setTimeout(function(){ btn.textContent = 'ADD TO INQUIRY LIST'; }, 900);
                showToast('Added to inquiry list!');
            });
        })();
    </script>
</body>
</html>



