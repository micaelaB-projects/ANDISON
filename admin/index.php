<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';

// Load all data for metrics
require_once __DIR__ . '/../../includes/brands_info.php';
require_once __DIR__ . '/../includes/categories_info.php';
require_once __DIR__ . '/../includes/products_management.php';
require_once __DIR__ . '/../includes/home_slider.php';
require_once __DIR__ . '/../includes/home_featured.php';
require_once __DIR__ . '/../includes/youtube_links.php';

$brands = andison_get_brands_info();
$categories = andison_get_categories();
$sliders = andison_get_home_slider();
$featured = andison_get_home_featured();
$youtube = andison_get_youtube_links();

// Calculate metrics
$totalBrands = count($brands);
$totalProducts = 0;
foreach ($brands as $brand) {
    if (isset($brand['products']) && is_array($brand['products'])) {
        $totalProducts += count($brand['products']);
    }
}

$brandsWithProducts = 0;
foreach ($brands as $brand) {
    if (isset($brand['products']) && is_array($brand['products']) && count($brand['products']) > 0) {
        $brandsWithProducts++;
    }
}

// Calculate category metrics
$totalCategories = count($categories);
$totalSubcategories = 0;
$totalCategoryProducts = 0;
foreach ($categories as $category) {
    $subcount = count($category['subcategories'] ?? []);
    $totalSubcategories += $subcount;
    foreach ($category['subcategories'] ?? [] as $sub) {
        $subProducts = andison_get_products_for_subcategory($category['id'], $sub['id']);
        $totalCategoryProducts += count($subProducts);
    }
}

$sliderCount = 0;
foreach ($sliders as $slide) {
    if (!empty($slide)) $sliderCount++;
}

$youtubeCount = 0;
foreach ($youtube['home_highlights'] ?? [] as $yt) {
    if (!empty($yt)) $youtubeCount++;
}

$featuredConfigured = !empty($featured['title']);

andison_admin_header('Dashboard', 'dashboard');
?>

<div class="grid">
    <!-- Quick Actions -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:28px;margin-bottom:20px;">Quick Actions</h2>
        <div class="row" style="gap:12px;flex-wrap:wrap;">
            <a class="btn btn-primary" href="products.php"><i class="bi bi-box-seam"></i> Manage Products</a>
            <a class="btn btn-outline" href="categories.php"><i class="bi bi-tag"></i> Categories & Items</a>
            <a class="btn btn-outline" href="youtube.php"><i class="bi bi-youtube"></i> YouTube Links</a>
            <a class="btn btn-outline" href="featured.php"><i class="bi bi-star"></i> Featured Section</a>
            <a class="btn btn-outline" href="slider.php"><i class="bi bi-images"></i> Homepage Slider</a>
            <a class="btn btn-outline" href="../home.php" target="_blank"><i class="bi bi-eye"></i> View Homepage</a>
        </div>
        <p class="hint" style="margin-top:12px;"><i class="bi bi-info-circle"></i> All changes sync automatically with the homepage</p>
    </section>

    <!-- Categories Hierarchy -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:20px;margin-bottom:16px;"><i class="bi bi-diagram-3"></i> Categories & Subcategories</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;">
            <?php foreach ($categories as $cat): ?>
                <div style="border:2px solid #e5e7eb;border-radius:12px;padding:16px;transition:all 0.2s;cursor:pointer;" onmouseover="this.style.borderColor='var(--accent)';this.style.boxShadow='0 4px 12px rgba(43,17,219,0.15)'" onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='none'">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                        <i class="bi <?php echo htmlspecialchars($cat['icon']); ?>" style="font-size:24px;color:var(--accent);"></i>
                        <div>
                            <div style="font-weight:700;color:#1a1a1a;"><?php echo htmlspecialchars($cat['name']); ?></div>
                            <div style="font-size:12px;color:#999;"><?php echo count($cat['subcategories'] ?? []); ?> items</div>
                        </div>
                    </div>
                    
                    <div style="border-top:1px solid #e5e7eb;padding-top:12px;">
                        <?php $itemCount = 0; foreach ($cat['subcategories'] ?? [] as $sub): ?>
                            <?php 
                                $subProducts = andison_get_products_for_subcategory($cat['id'], $sub['id']);
                                $prodCount = count($subProducts);
                                $itemCount++;
                            ?>
                            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:<?php echo $itemCount < count($cat['subcategories']) ? '1px solid #f0f0f0' : 'none'; ?>">
                                <div>
                                    <div style="font-size:13px;color:#374151;"><?php echo htmlspecialchars($sub['name']); ?></div>
                                    <div style="font-size:11px;color:#999;"><?php echo $prodCount; ?> product<?php echo $prodCount !== 1 ? 's' : ''; ?></div>
                                </div>
                                <a href="categories.php?cat=<?php echo urlencode($cat['id']); ?>&sub=<?php echo urlencode($sub['id']); ?>" class="btn btn-outline" style="padding:6px 10px;font-size:11px;text-decoration:none;">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <a href="categories.php?cat=<?php echo urlencode($cat['id']); ?>" class="btn btn-primary" style="width:100%;margin-top:12px;text-align:center;text-decoration:none;">
                        <i class="bi bi-chevron-right"></i> Manage
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Content Metrics -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:20px;margin-bottom:16px;"><i class="bi bi-graph-up"></i> Content Overview</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:14px;">
            <div style="padding:16px;background:linear-gradient(135deg,var(--accent),rgba(43,17,219,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $totalBrands; ?></div>
                    <i class="bi bi-building" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">Total Brands</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;"><?php echo $brandsWithProducts; ?> with products</div>
            </div>

            <div style="padding:16px;background:linear-gradient(135deg,var(--mint),rgba(16,185,129,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $totalProducts; ?></div>
                    <i class="bi bi-box-seam" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">Total Products</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;">Across all brands</div>
            </div>

            <div style="padding:16px;background:linear-gradient(135deg,#ec4899,rgba(236,72,153,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $totalCategories; ?></div>
                    <i class="bi bi-tag" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">Categories</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;"><a href="categories.php" style="color:white;text-decoration:underline;">Manage</a></div>
            </div>

            <div style="padding:16px;background:linear-gradient(135deg,#06b6d4,rgba(6,182,212,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $totalCategoryProducts; ?></div>
                    <i class="bi bi-box2" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">Category Items</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;">In hierarchies</div>
            </div>

            <div style="padding:16px;background:linear-gradient(135deg,#f59e0b,rgba(245,158,11,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $sliderCount; ?>/4</div>
                    <i class="bi bi-images" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">Slider Images</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;"><a href="slider.php" style="color:white;text-decoration:underline;">Edit</a></div>
            </div>

            <div style="padding:16px;background:linear-gradient(135deg,#ef4444,rgba(239,68,68,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $youtubeCount; ?>/2</div>
                    <i class="bi bi-youtube" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">YouTube Videos</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;"><a href="youtube.php" style="color:white;text-decoration:underline;">Edit</a></div>
            </div>

            <div style="padding:16px;background:linear-gradient(135deg,#8b5cf6,rgba(139,92,246,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $featuredConfigured ? '✓' : '○'; ?></div>
                    <i class="bi bi-star" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">Featured Section</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;"><a href="featured.php" style="color:white;text-decoration:underline;">Configure</a></div>
            </div>

            <div style="padding:16px;background:linear-gradient(135deg,#06b6d4,rgba(6,182,212,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                    <div style="font-size:32px;font-weight:700;"><?php echo $totalBrands > 0 ? '✓' : '○'; ?></div>
                    <i class="bi bi-check-circle" style="font-size:24px;opacity:0.6;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;">Live Status</div>
                <div style="font-size:11px;opacity:0.7;margin-top:4px;">Auto-synced</div>
            </div>
        </div>
    </section>

    <!-- Content Status & Recent Updates -->
    <section class="card" style="grid-column:span 6;">
        <h2 style="font-size:18px;margin-bottom:16px;"><i class="bi bi-clock-history"></i> Quick Stats</h2>
        <div style="display:flex;flex-direction:column;gap:12px;">
            <div style="padding:12px;background:#f0fdf4;border-left:4px solid #22c55e;border-radius:6px;">
                <div style="font-weight:600;color:#166534;">Products</div>
                <div style="font-size:13px;color:#4b5563;margin-top:2px;"><?php echo $totalProducts; ?> items across <?php echo $totalBrands; ?> brands ready to display</div>
            </div>
            <div style="padding:12px;background:<?php echo $sliderCount >= 4 ? '#f0fdf4' : '#fef3c7'; ?>;border-left:4px solid <?php echo $sliderCount >= 4 ? '#22c55e' : '#f59e0b'; ?>;border-radius:6px;">
                <div style="font-weight:600;color:<?php echo $sliderCount >= 4 ? '#166534' : '#92400e'; ?>;">Homepage Slider</div>
                <div style="font-size:13px;color:#4b5563;margin-top:2px;"><?php echo $sliderCount; ?>/4 images configured <?php echo $sliderCount < 4 ? '(add more for better rotation)' : '(complete)'; ?></div>
            </div>
            <div style="padding:12px;background:<?php echo $youtubeCount >= 2 ? '#f0fdf4' : '#fef3c7'; ?>;border-left:4px solid <?php echo $youtubeCount >= 2 ? '#22c55e' : '#f59e0b'; ?>;border-radius:6px;">
                <div style="font-weight:600;color:<?php echo $youtubeCount >= 2 ? '#166534' : '#92400e'; ?>;">YouTube Videos</div>
                <div style="font-size:13px;color:#4b5563;margin-top:2px;"><?php echo $youtubeCount; ?>/2 videos set for homepage highlights</div>
            </div>
            <div style="padding:12px;background:<?php echo $featuredConfigured ? '#f0fdf4' : '#fee2e2'; ?>;border-left:4px solid <?php echo $featuredConfigured ? '#22c55e' : '#ef4444'; ?>;border-radius:6px;">
                <div style="font-weight:600;color:<?php echo $featuredConfigured ? '#166534' : '#7f1d1d'; ?>;">Featured Section</div>
                <div style="font-size:13px;color:#4b5563;margin-top:2px;"><?php echo $featuredConfigured ? 'Configured with ' . htmlspecialchars(substr($featured['title'], 0, 30)) . '...' : 'Not yet configured'; ?></div>
            </div>
        </div>
    </section>

    <!-- Helpful Resources -->
    <section class="card" style="grid-column:span 6;">
        <h2 style="font-size:18px;margin-bottom:16px;"><i class="bi bi-lightbulb"></i> Quick Tips</h2>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="padding:12px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px;"><i class="bi bi-info-circle" style="color:var(--accent);"></i> Automatic Sync</div>
                <div style="font-size:12px;color:var(--muted);">All changes are instantly reflected on your website homepage</div>
            </div>
            <div style="padding:12px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px;"><i class="bi bi-image" style="color:#f59e0b;"></i> Image Paths</div>
                <div style="font-size:12px;color:var(--muted);">Images are auto-saved to andison/assets/uploads/ directory</div>
            </div>
            <div style="padding:12px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px;"><i class="bi bi-link-45deg" style="color:#10b981;"></i> YouTube URLs</div>
                <div style="font-size:12px;color:var(--muted);">Paste full YouTube URLs or just the video ID</div>
            </div>
            <div style="padding:12px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:13px;margin-bottom:4px;"><i class="bi bi-eye" style="color:#8b5cf6;"></i> Preview Changes</div>
                <div style="font-size:12px;color:var(--muted);">Click "View Homepage" above to preview all your changes live</div>
            </div>
        </div>
    </section>
</div>

<?php
andison_admin_footer();
    