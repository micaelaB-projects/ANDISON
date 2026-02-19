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
require_once __DIR__ . '/../includes/analytics.php';

$brands = andison_get_brands_info();
$categories = andison_get_categories();
$sliders = andison_get_home_slider();
$featured = andison_get_home_featured();
$youtube = andison_get_youtube_links();
$analytics = andison_get_analytics();
$chartData = andison_get_daily_chart(7);
$analyticsBrands = $analytics['brands'] ?? [];
arsort($analyticsBrands);
$analyticsCategories = $analytics['categories'] ?? [];
arsort($analyticsCategories);

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
    <!-- ── ANALYTICS (Visitor Stats) ─────────────────────────── -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:15px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-bar-chart-line"></i> Website Analytics
            <span id="analytics-live-dot" style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:600;color:#10b981;background:rgba(16,185,129,0.10);padding:2px 8px;border-radius:999px;letter-spacing:0.3px;">
                <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;animation:livePulse 1.4s ease-in-out infinite;"></span>
                LIVE
            </span>
            <span id="analytics-last-updated" style="font-size:10px;font-weight:400;color:#9ca3af;margin-left:auto;"></span>
        </h2>
        <style>
            @keyframes livePulse {
                0%,100%{opacity:1;transform:scale(1)}
                50%{opacity:0.4;transform:scale(0.7)}
            }
            .analytics-num{transition:color 0.4s ease}
            .analytics-num.flash{color:#00D7B3!important}
        </style>

        <!-- Stat Tiles -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;margin-bottom:14px;">
            <!-- Total Page Views -->
            <div style="padding:12px;background:linear-gradient(135deg,#2B11DB,#1a0a8f);border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-total-pv" class="analytics-num" style="font-size:22px;font-weight:800;"><?php echo number_format($analytics['total_pageviews']); ?></div>
                    <i class="bi bi-eye" style="font-size:18px;opacity:0.55;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;margin-top:4px;">Total Page Views</div>
                <div style="font-size:10px;opacity:0.65;margin-top:1px;">All time</div>
            </div>

            <!-- Today -->
            <div style="padding:12px;background:linear-gradient(135deg,#f59e0b,#92400e);border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-today-pv" class="analytics-num" style="font-size:22px;font-weight:800;"><?php echo number_format($analytics['today_pageviews']); ?></div>
                    <i class="bi bi-calendar-day" style="font-size:18px;opacity:0.55;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;margin-top:4px;">Views Today</div>
                <div id="an-today-unique" style="font-size:10px;opacity:0.65;margin-top:1px;"><?php echo number_format($analytics['today_unique']); ?> unique</div>
            </div>

            <!-- This Week -->
            <div style="padding:12px;background:linear-gradient(135deg,#06b6d4,#0e7490);border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-week-pv" class="analytics-num" style="font-size:22px;font-weight:800;"><?php echo number_format($analytics['week_pageviews']); ?></div>
                    <i class="bi bi-calendar-week" style="font-size:18px;opacity:0.55;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;margin-top:4px;">This Week</div>
                <div id="an-week-unique" style="font-size:10px;opacity:0.65;margin-top:1px;"><?php echo number_format($analytics['week_unique']); ?> unique</div>
            </div>

            <!-- This Month -->
            <div style="padding:12px;background:linear-gradient(135deg,#8b5cf6,#4c1d95);border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-month-pv" class="analytics-num" style="font-size:22px;font-weight:800;"><?php echo number_format($analytics['month_pageviews']); ?></div>
                    <i class="bi bi-calendar-month" style="font-size:18px;opacity:0.55;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;margin-top:4px;">This Month</div>
                <div id="an-month-unique" style="font-size:10px;opacity:0.65;margin-top:1px;"><?php echo number_format($analytics['month_unique']); ?> unique</div>
            </div>
        </div>

        <!-- Last 7 Days Bar Chart -->
        <div style="margin-bottom:4px;">
            <div style="font-size:11px;font-weight:700;color:#374151;margin-bottom:6px;"><i class="bi bi-graph-up-arrow" style="color:var(--accent);"></i> Page Views — Last 7 Days</div>
            <?php
                $maxViews = max(array_column($chartData, 'views') ?: [1]);
                $maxViews = max($maxViews, 1);
            ?>
            <div id="an-chart" style="display:flex;align-items:flex-end;gap:6px;height:50px;">
                <?php foreach ($chartData as $i => $bar): ?>
                    <?php $barH = (int) round(($bar['views'] / $maxViews) * 44); $barH = max($barH, 3); ?>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                        <span id="an-bar-val-<?php echo $i; ?>" style="font-size:10px;color:#6b7280;font-weight:700;"><?php echo $bar['views']; ?></span>
                        <div id="an-bar-<?php echo $i; ?>"
                             title="<?php echo htmlspecialchars($bar['date']); ?>: <?php echo $bar['views']; ?> views"
                             style="width:100%;height:<?php echo $barH; ?>px;background:linear-gradient(180deg,var(--accent),rgba(43,17,219,0.55));border-radius:6px 6px 0 0;transition:height 0.5s ease;"
                             onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'"></div>
                        <span style="font-size:10px;color:#9ca3af;white-space:nowrap;"><?php echo htmlspecialchars($bar['date']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </section>

    <!-- Brand & Category Analytics -->
    <section class="card" style="grid-column:span 6;">
        <h2 style="font-size:13px;margin-bottom:10px;"><i class="bi bi-building"></i> Brand Views</h2>
        <div id="an-brands-list">
            <?php if (empty($analyticsBrands)): ?>
                <div style="color:#9ca3af;font-size:13px;">No brand visits recorded yet.</div>
            <?php else: ?>
                <?php
                    $maxBV = max(array_values($analyticsBrands) ?: [1]);
                    foreach ($analyticsBrands as $bname => $bcount):
                        $bpct = (int) round(($bcount / $maxBV) * 100);
                ?>
                <div style="margin-bottom:6px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:2px;">
                        <span style="font-size:11px;font-weight:600;color:#374151;"><?php echo htmlspecialchars($bname); ?></span>
                        <span style="font-size:11px;font-weight:700;color:var(--accent);"><?php echo number_format($bcount); ?></span>
                    </div>
                    <div style="background:#e5e7eb;border-radius:999px;height:5px;">
                        <div style="width:<?php echo $bpct; ?>%;height:5px;background:linear-gradient(90deg,var(--accent),rgba(43,17,219,0.6));border-radius:999px;transition:width 0.5s ease;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <section class="card" style="grid-column:span 6;">
        <h2 style="font-size:13px;margin-bottom:10px;"><i class="bi bi-tag"></i> Category Views</h2>
        <div id="an-categories-list">
            <?php if (empty($analyticsCategories)): ?>
                <div style="color:#9ca3af;font-size:13px;">No category visits recorded yet.</div>
            <?php else: ?>
                <?php
                    $maxCV = max(array_values($analyticsCategories) ?: [1]);
                    foreach ($analyticsCategories as $cname => $ccount):
                        $cpct = (int) round(($ccount / $maxCV) * 100);
                ?>
                <div style="margin-bottom:6px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:2px;">
                        <span style="font-size:11px;font-weight:600;color:#374151;"><?php echo htmlspecialchars($cname); ?></span>
                        <span style="font-size:11px;font-weight:700;color:#10b981;"><?php echo number_format($ccount); ?></span>
                    </div>
                    <div style="background:#e5e7eb;border-radius:999px;height:5px;">
                        <div style="width:<?php echo $cpct; ?>%;height:5px;background:linear-gradient(90deg,#10b981,rgba(16,185,129,0.5));border-radius:999px;transition:width 0.5s ease;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
    (function(){
        var POLL_MS = 5000; // refresh every 5 seconds

        function fmt(n){ return Number(n).toLocaleString(); }

        function flashEl(el){
            if(!el) return;
            el.classList.add('flash');
            setTimeout(function(){ el.classList.remove('flash'); }, 600);
        }

        function setVal(id, newVal, doFlash){
            var el = document.getElementById(id);
            if(!el) return;
            var old = el.textContent.replace(/,/g,'');
            var nv  = String(newVal).replace(/,/g,'');
            if(old !== nv){
                el.textContent = fmt(newVal);
                if(doFlash) flashEl(el);
            }
        }

        function rebuildChart(chart){
            if(!chart || !chart.length) return;
            var maxV = Math.max.apply(null, chart.map(function(b){ return b.views; }));
            maxV = Math.max(maxV, 1);
            for(var i=0;i<chart.length;i++){
                var bar = document.getElementById('an-bar-'+i);
                var val = document.getElementById('an-bar-val-'+i);
                if(bar){
                    var h = Math.round((chart[i].views / maxV) * 44);
                    h = Math.max(h, 3);
                    bar.style.height = h + 'px';
                    bar.title = chart[i].date + ': ' + chart[i].views + ' views';
                }
                if(val) val.textContent = chart[i].views;
            }
        }

        function rebuildRankList(wrapperId, data, accentColor) {
            var wrap = document.getElementById(wrapperId);
            if (!wrap) return;
            var keys = Object.keys(data || {});
            if (!keys.length) {
                wrap.innerHTML = '<div style="color:#9ca3af;font-size:13px;">No visits recorded yet.</div>';
                return;
            }
            // sort desc
            keys.sort(function(a,b){ return data[b]-data[a]; });
            var maxV = data[keys[0]] || 1;
            var html = '';
            keys.forEach(function(k){
                var pct = Math.round((data[k]/maxV)*100);
                html += '<div style="margin-bottom:10px;">'
                    + '<div style="display:flex;justify-content:space-between;margin-bottom:3px;">'
                    + '<span style="font-size:12px;font-weight:600;color:#374151;">' + k + '</span>'
                    + '<span style="font-size:12px;font-weight:700;color:' + accentColor + ';">' + fmt(data[k]) + '</span>'
                    + '</div>'
                    + '<div style="background:#e5e7eb;border-radius:999px;height:6px;">'
                    + '<div style="width:'+pct+'%;height:6px;background:linear-gradient(90deg,'+accentColor+',rgba(43,17,219,0.4));border-radius:999px;transition:width 0.5s ease;"></div>'
                    + '</div></div>';
            });
            wrap.innerHTML = html;
        }

        function updateTimestamp(){
            var el = document.getElementById('analytics-last-updated');
            if(el){
                var now = new Date();
                var h = String(now.getHours()).padStart(2,'0');
                var m = String(now.getMinutes()).padStart(2,'0');
                var s = String(now.getSeconds()).padStart(2,'0');
                el.textContent = 'Updated ' + h + ':' + m + ':' + s;
            }
        }

        function poll(){
            fetch('analytics-api.php', {cache:'no-store'})
                .then(function(r){ return r.json(); })
                .then(function(d){
                    setVal('an-total-pv',   d.total_pageviews,  true);
                    setVal('an-today-pv',   d.today_pageviews,  true);
                    setVal('an-week-pv',    d.week_pageviews,   true);
                    setVal('an-month-pv',   d.month_pageviews,  true);

                    var todayUq = document.getElementById('an-today-unique');
                    if(todayUq) todayUq.textContent = fmt(d.today_unique) + ' unique';

                    var weekUq = document.getElementById('an-week-unique');
                    if(weekUq) weekUq.textContent = fmt(d.week_unique) + ' unique';

                    var monthUq = document.getElementById('an-month-unique');
                    if(monthUq) monthUq.textContent = fmt(d.month_unique) + ' unique';

                    rebuildChart(d.chart);
                    rebuildRankList('an-brands-list',     d.brands,     'var(--accent)');
                    rebuildRankList('an-categories-list', d.categories, '#10b981');
                    updateTimestamp();
                })
                .catch(function(){/* silently ignore network errors */});
        }

        // Start polling
        setInterval(poll, POLL_MS);
        updateTimestamp();
    })();
    </script>

    <!-- Quick Actions -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:15px;margin-bottom:10px;">Quick Actions</h2>
        <div class="row" style="gap:8px;flex-wrap:wrap;">
            <a class="btn btn-primary" href="products.php" style="font-size:12px;padding:6px 12px;"><i class="bi bi-box-seam"></i> Manage Products</a>
            <a class="btn btn-outline" href="categories.php" style="font-size:12px;padding:6px 12px;"><i class="bi bi-tag"></i> Categories & Items</a>
            <a class="btn btn-outline" href="youtube.php" style="font-size:12px;padding:6px 12px;"><i class="bi bi-youtube"></i> YouTube Links</a>
            <a class="btn btn-outline" href="featured.php" style="font-size:12px;padding:6px 12px;"><i class="bi bi-star"></i> Featured Section</a>
            <a class="btn btn-outline" href="slider.php" style="font-size:12px;padding:6px 12px;"><i class="bi bi-images"></i> Homepage Slider</a>
            <a class="btn btn-outline" href="../home.php" target="_blank" style="font-size:12px;padding:6px 12px;"><i class="bi bi-eye"></i> View Homepage</a>
        </div>
        <p class="hint" style="margin-top:7px;font-size:11px;"><i class="bi bi-info-circle"></i> All changes sync automatically with the homepage</p>
    </section>

    <!-- Categories Hierarchy -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:15px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-diagram-3" style="color:var(--accent);"></i> Categories &amp; Subcategories
            <span style="font-size:11px;font-weight:400;color:#9ca3af;margin-left:4px;"><?php echo count($categories); ?> categories</span>
        </h2>
        <style>
            .cat-card { border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;transition:box-shadow 0.2s,border-color 0.2s;display:flex;flex-direction:column;height:100%; }
            .cat-card:hover { border-color:rgba(43,17,219,0.3);box-shadow:0 4px 16px rgba(43,17,219,0.1); }
            .cat-sub-row { display:flex;align-items:center;justify-content:space-between;padding:6px 10px;border-radius:6px;transition:background 0.15s; }
            .cat-sub-row:hover { background:#f5f3ff; }
            .cat-edit-btn { display:inline-flex;align-items:center;gap:3px;padding:3px 8px;font-size:10px;font-weight:600;color:var(--accent);background:rgba(43,17,219,0.07);border-radius:5px;text-decoration:none;border:none;transition:background 0.15s; }
            .cat-edit-btn:hover { background:rgba(43,17,219,0.15); }
            .cat-manage-btn { display:block;text-align:center;padding:8px;font-size:12px;font-weight:600;color:white;background:linear-gradient(90deg,var(--accent),#4f35e8);border-radius:0 0 11px 11px;text-decoration:none;transition:opacity 0.15s; }
            .cat-manage-btn:hover { opacity:0.88; }
            .prod-badge { display:inline-block;padding:1px 6px;font-size:9px;font-weight:700;border-radius:999px;background:#f3f4f6;color:#6b7280; }
        </style>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px;">
            <?php foreach ($categories as $cat): ?>
                <div class="cat-card">
                    <!-- Card Header -->
                    <div style="display:flex;align-items:center;gap:10px;padding:12px 12px 10px;background:linear-gradient(135deg,rgba(43,17,219,0.06),rgba(43,17,219,0.02));border-bottom:1px solid #e5e7eb;">
                        <div style="width:34px;height:34px;border-radius:8px;background:rgba(43,17,219,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi <?php echo htmlspecialchars($cat['icon']); ?>" style="font-size:16px;color:var(--accent);"></i>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:700;font-size:13px;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($cat['name']); ?></div>
                            <div style="font-size:10px;color:#9ca3af;margin-top:1px;"><?php echo count($cat['subcategories'] ?? []); ?> subcategories</div>
                        </div>
                    </div>

                    <!-- Subcategory List -->
                    <div style="padding:8px;flex:1;overflow-y:auto;">
                        <?php foreach ($cat['subcategories'] ?? [] as $sub): ?>
                            <?php
                                $subProducts = andison_get_products_for_subcategory($cat['id'], $sub['id']);
                                $prodCount = count($subProducts);
                            ?>
                            <div class="cat-sub-row">
                                <div style="display:flex;align-items:center;gap:7px;min-width:0;">
                                    <div style="width:4px;height:4px;border-radius:50%;background:var(--accent);opacity:0.5;flex-shrink:0;"></div>
                                    <span style="font-size:12px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($sub['name']); ?></span>
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;margin-left:6px;">
                                    <span class="prod-badge"><?php echo $prodCount; ?></span>
                                    <a href="categories.php?cat=<?php echo urlencode($cat['id']); ?>&sub=<?php echo urlencode($sub['id']); ?>" class="cat-edit-btn">
                                        <i class="bi bi-pencil" style="font-size:9px;"></i> Edit
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($cat['subcategories'])): ?>
                            <div style="text-align:center;padding:16px 0;font-size:11px;color:#d1d5db;"><i class="bi bi-inbox" style="display:block;font-size:20px;margin-bottom:4px;"></i>No subcategories</div>
                        <?php endif; ?>
                    </div>

                    <!-- Manage Button -->
                    <a href="categories.php?cat=<?php echo urlencode($cat['id']); ?>" class="cat-manage-btn">
                        <i class="bi bi-chevron-right"></i> Manage
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Content Metrics -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:15px;margin-bottom:10px;"><i class="bi bi-graph-up"></i> Content Overview</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;">
            <div style="padding:12px;background:linear-gradient(135deg,var(--accent),rgba(43,17,219,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $totalBrands; ?></div>
                    <i class="bi bi-building" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">Total Brands</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;"><?php echo $brandsWithProducts; ?> with products</div>
            </div>

            <div style="padding:12px;background:linear-gradient(135deg,var(--mint),rgba(16,185,129,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $totalProducts; ?></div>
                    <i class="bi bi-box-seam" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">Total Products</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;">Across all brands</div>
            </div>

            <div style="padding:12px;background:linear-gradient(135deg,#ec4899,rgba(236,72,153,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $totalCategories; ?></div>
                    <i class="bi bi-tag" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">Categories</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;"><a href="categories.php" style="color:white;text-decoration:underline;">Manage</a></div>
            </div>

            <div style="padding:12px;background:linear-gradient(135deg,#06b6d4,rgba(6,182,212,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $totalCategoryProducts; ?></div>
                    <i class="bi bi-box2" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">Category Items</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;">In hierarchies</div>
            </div>

            <div style="padding:12px;background:linear-gradient(135deg,#f59e0b,rgba(245,158,11,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $sliderCount; ?>/4</div>
                    <i class="bi bi-images" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">Slider Images</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;"><a href="slider.php" style="color:white;text-decoration:underline;">Edit</a></div>
            </div>

            <div style="padding:12px;background:linear-gradient(135deg,#ef4444,rgba(239,68,68,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $youtubeCount; ?>/2</div>
                    <i class="bi bi-youtube" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">YouTube Videos</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;"><a href="youtube.php" style="color:white;text-decoration:underline;">Edit</a></div>
            </div>

            <div style="padding:12px;background:linear-gradient(135deg,#8b5cf6,rgba(139,92,246,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $featuredConfigured ? '✓' : '○'; ?></div>
                    <i class="bi bi-star" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">Featured Section</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;"><a href="featured.php" style="color:white;text-decoration:underline;">Configure</a></div>
            </div>

            <div style="padding:12px;background:linear-gradient(135deg,#06b6d4,rgba(6,182,212,0.9));border-radius:10px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <div style="font-size:22px;font-weight:700;"><?php echo $totalBrands > 0 ? '✓' : '○'; ?></div>
                    <i class="bi bi-check-circle" style="font-size:18px;opacity:0.6;"></i>
                </div>
                <div style="font-size:11px;opacity:0.9;">Live Status</div>
                <div style="font-size:10px;opacity:0.7;margin-top:2px;">Auto-synced</div>
            </div>
        </div>
    </section>

    <!-- Content Status & Recent Updates -->
    <section class="card" style="grid-column:span 6;">
        <h2 style="font-size:15px;margin-bottom:10px;"><i class="bi bi-clock-history"></i> Quick Stats</h2>
        <div style="display:flex;flex-direction:column;gap:7px;">
            <div style="padding:9px;background:#f0fdf4;border-left:3px solid #22c55e;border-radius:6px;">
                <div style="font-weight:600;font-size:12px;color:#166534;">Products</div>
                <div style="font-size:11px;color:#4b5563;margin-top:1px;"><?php echo $totalProducts; ?> items across <?php echo $totalBrands; ?> brands ready to display</div>
            </div>
            <div style="padding:9px;background:<?php echo $sliderCount >= 4 ? '#f0fdf4' : '#fef3c7'; ?>;border-left:3px solid <?php echo $sliderCount >= 4 ? '#22c55e' : '#f59e0b'; ?>;border-radius:6px;">
                <div style="font-weight:600;font-size:12px;color:<?php echo $sliderCount >= 4 ? '#166534' : '#92400e'; ?>;">Homepage Slider</div>
                <div style="font-size:11px;color:#4b5563;margin-top:1px;"><?php echo $sliderCount; ?>/4 images configured <?php echo $sliderCount < 4 ? '(add more for better rotation)' : '(complete)'; ?></div>
            </div>
            <div style="padding:9px;background:<?php echo $youtubeCount >= 2 ? '#f0fdf4' : '#fef3c7'; ?>;border-left:3px solid <?php echo $youtubeCount >= 2 ? '#22c55e' : '#f59e0b'; ?>;border-radius:6px;">
                <div style="font-weight:600;font-size:12px;color:<?php echo $youtubeCount >= 2 ? '#166534' : '#92400e'; ?>;">YouTube Videos</div>
                <div style="font-size:11px;color:#4b5563;margin-top:1px;"><?php echo $youtubeCount; ?>/2 videos set for homepage highlights</div>
            </div>
            <div style="padding:9px;background:<?php echo $featuredConfigured ? '#f0fdf4' : '#fee2e2'; ?>;border-left:3px solid <?php echo $featuredConfigured ? '#22c55e' : '#ef4444'; ?>;border-radius:6px;">
                <div style="font-weight:600;font-size:12px;color:<?php echo $featuredConfigured ? '#166534' : '#7f1d1d'; ?>;">Featured Section</div>
                <div style="font-size:11px;color:#4b5563;margin-top:1px;"><?php echo $featuredConfigured ? 'Configured with ' . htmlspecialchars(substr($featured['title'], 0, 30)) . '...' : 'Not yet configured'; ?></div>
            </div>
        </div>
    </section>

    <!-- Helpful Resources -->
    <section class="card" style="grid-column:span 6;">
        <h2 style="font-size:15px;margin-bottom:10px;"><i class="bi bi-lightbulb"></i> Quick Tips</h2>
        <div style="display:flex;flex-direction:column;gap:6px;">
            <div style="padding:9px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:11px;margin-bottom:2px;"><i class="bi bi-info-circle" style="color:var(--accent);"></i> Automatic Sync</div>
                <div style="font-size:11px;color:var(--muted);">All changes are instantly reflected on your website homepage</div>
            </div>
            <div style="padding:9px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:11px;margin-bottom:2px;"><i class="bi bi-image" style="color:#f59e0b;"></i> Image Paths</div>
                <div style="font-size:11px;color:var(--muted);">Images are auto-saved to andison/assets/uploads/ directory</div>
            </div>
            <div style="padding:9px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:11px;margin-bottom:2px;"><i class="bi bi-link-45deg" style="color:#10b981;"></i> YouTube URLs</div>
                <div style="font-size:11px;color:var(--muted);">Paste full YouTube URLs or just the video ID</div>
            </div>
            <div style="padding:9px;background:#f9fafb;border-radius:6px;border:1px solid #e5e7eb;">
                <div style="font-weight:600;font-size:11px;margin-bottom:2px;"><i class="bi bi-eye" style="color:#8b5cf6;"></i> Preview Changes</div>
                <div style="font-size:11px;color:var(--muted);">Click "View Homepage" above to preview all your changes live</div>
            </div>
        </div>
    </section>
</div>

<?php
andison_admin_footer();
    


