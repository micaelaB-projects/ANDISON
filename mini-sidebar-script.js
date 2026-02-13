        // ============================================
        // MINI SIDEBAR AND BROWSE TOGGLE FUNCTIONALITY
        // ============================================
        var miniSidebar = document.getElementById('miniSidebar');
        var mainSidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('overlay');
        var expandBtn = document.getElementById('expandSidebar');
        var browseToggle = document.getElementById('browseToggle');
        var miniIcons = document.querySelectorAll('.mini-sidebar-icon');
        var miniPopover = document.getElementById('miniPopover');
        var popoverTitle = miniPopover ? miniPopover.querySelector('.mini-popover-title') : null;
        var popoverList = miniPopover ? miniPopover.querySelector('.mini-popover-list') : null;
        var currentPopoverKey = null;

        // Responsive function to show/hide browse toggle
        function updateBrowseToggleVisibility() {
            if(window.innerWidth <= 1024) {
                if(browseToggle) browseToggle.classList.add('active');
            } else {
                if(browseToggle) browseToggle.classList.remove('active');
            }
        }

        // Initialize on load
        if(browseToggle) updateBrowseToggleVisibility();

        // Update on window resize
        if(browseToggle) window.addEventListener('resize', updateBrowseToggleVisibility);

        // Helpers for popover
        function getCategoryKeyFromTarget(dataTarget) {
            if (!dataTarget) return null;
            var keys = [
                'arc-welding-machine','arc-welding-robots','batteries','drilling-and-lifting','gas-detectors','portable-ventilators','power-tools','protection','welding-accessories','welding-consumables'
            ];
            for (var i=0;i<keys.length;i++) { if (dataTarget.indexOf('/'+keys[i]+'/') !== -1 || dataTarget.indexOf(keys[i]+'/') !== -1) return keys[i]; }
            return null;
        }
        function getCategoryTitle(key) {
            var map = {
                'arc-welding-machine': 'Arc Welding Machines',
                'arc-welding-robots': 'Arc Welding Robots',
                'batteries': 'Batteries',
                'drilling-and-lifting': 'Drilling and Lifting',
                'gas-detectors': 'Gas Detectors',
                'portable-ventilators': 'Portable Ventilators',
                'power-tools': 'Power Tools',
                'protection': 'Personal Protective Equipment',
                'welding-accessories': 'Welding Accessories',
                'welding-consumables': 'Welding Consumables'
            };
            return map[key] || 'Categories';
        }
        function getPopoverItems(key) {
            var maps = {
                'arc-welding-robots': [
                    { label: 'G3 Controller Series', href: 'arc-welding-robots/g3-controller-series.php' },
                    { label: 'G4 Controller Series', href: 'arc-welding-robots/g4-controller-series.php' },
                    { label: 'Featured Products and Solutions', href: 'arc-welding-robots/featured-products-and-solution.php' },
                    { label: 'Robot System Peripherals', href: 'arc-welding-robots/robot-system-peripherals.php' }
                ],
                'arc-welding-machine': [
                    { label: 'CO2/MAG Welding Machine', href: 'arc-welding-machine/co2-mag-welding-machine.php' },
                    { label: 'MIG Welding Machine', href: 'arc-welding-machine/mig-welding-machine.php' },
                    { label: 'TIG Welding Machine', href: 'arc-welding-machine/tig-welding-machine.php' },
                    { label: 'Plasma Cutting Machine', href: 'arc-welding-machine/plasma-cutting-machine.php' },
                    { label: 'Stud Welding', href: 'arc-welding-machine/stud-welding-machine.php' },
                    { label: 'Accessories & Consumables', href: 'arc-welding-machine/accessories-and-consumables.php' }
                ],
                'batteries': [
                    { label: 'Maintenance Free', href: 'batteries/maintenance-free.php' },
                    { label: 'Low Maintenance', href: 'batteries/low-maintenance.php' },
                    { label: 'Special Batteries', href: 'batteries/special-batteries.php' }
                ],
                'drilling-and-lifting': [
                    { label: 'Material Handling & Lifting', href: 'drilling-and-lifting/lifting.php' },
                    { label: 'Magnetic Drill', href: 'drilling-and-lifting/magnetic-drill.php' },
                    { label: 'Core Cutters', href: 'drilling-and-lifting/cutters.php' }
                ],
                'gas-detectors': [
                    { label: 'Single Gas Detector', href: 'gas-detectors/single-gas-detector.php' },
                    { label: 'Multi Gas Detector', href: 'gas-detectors/multi-gas-detector.php' },
                    { label: 'Portable Gas Detectors', href: 'gas-detectors/portable-gas-detectors.php' },
                    { label: 'Docking and Data Management', href: 'gas-detectors/docking-data-management.php' },
                    { label: 'Calibration Gas and Regulators', href: 'gas-detectors/calibration-gas-regulators.php' }
                ],
                'power-tools': [
                   { label: 'Grinder', href: 'power-tools/grinder.php' },
                    { label: 'Saw', href: 'power-tools/saw.php' },
                    { label: 'Drill and Wrench', href: 'power-tools/drill-and-wrench.php' },
                    { label: 'Rotary and Demolition Hammer', href: 'power-tools/rotary-and-demolition-hammer.php' },
                    { label: 'Accessories', href: 'power-tools/accessories.php' }
                ],
                'portable-ventilators': [
                    { label: 'Electric Driven', href: 'portable-ventilators/electric-driven.php' },
                    { label: 'Pneumatic Driven', href: 'portable-ventilators/pneumatic-driven.php' }
                ],
                'protection': [
                    { label: 'Eye Protection', href: 'protection/eye-protection.php' },
                    { label: 'Hand Protection', href: 'protection/hand-protection.php' },
                    { label: 'Hearing & Respiratory Protection', href: 'protection/hearing-respiratory-protection.php' },
                    { label: 'Welding Head and Face Protection', href: 'protection/welding-head-and-face-protection.php' },
                    { label: 'Body Protection', href: 'protection/body-protection.php' }
                ],
                'welding-accessories': [
                    { label: 'Welding Electrode Oven', href: 'welding-accessories/welding-electrode-oven.php' },
                    { label: 'Non-Destructive Crack Detection', href: 'welding-accessories/non-destructive-crack-detection.php' },
                    { label: 'Gas Saving Regulator', href: 'welding-accessories/gas-saving-regulator.php' },
                    { label: 'Gas Cutting Equipment', href: 'welding-accessories/gas-cutting-equipment.php' },
                    { label: 'Industrial Markers', href: 'welding-accessories/industrial-markers.php' },
                    { label: 'Measuring Gauge', href: 'welding-accessories/measuring-gauge.php' },
                    { label: 'Others', href: 'welding-accessories/others.php' }
                ],
                'welding-consumables': [
                    { label: 'Kobelco', href: 'welding-consumables/kobelco.php' },
                    { label: 'Metrode', href: 'welding-consumables/metrode.php' }
                ]
            };
            return maps[key] || [];
        }
        function renderPopover(key) {
            if (!miniPopover || !popoverList) return;
            popoverList.innerHTML = '';
            var items = getPopoverItems(key);
            items.forEach(function(it){
                var li = document.createElement('li');
                li.className = 'mini-popover-item';
                li.innerHTML = '<span class="square"></span><a href="'+ it.href +'">'+ it.label +'</a>';
                popoverList.appendChild(li);
            });
            if (popoverTitle) popoverTitle.textContent = getCategoryTitle(key);
        }
        function positionPopoverForIcon(icon) {
            if (!miniPopover || !icon) return;
            miniPopover.style.left = '-9999px';
            miniPopover.style.top = '-9999px';
            miniPopover.classList.add('show');
            var rect = icon.getBoundingClientRect();
            var pw = miniPopover.offsetWidth;
            var ph = miniPopover.offsetHeight;
            var iconCenterY = rect.top + rect.height / 2;
            var left = Math.round(rect.right + 14);
            var top = Math.round(iconCenterY - ph / 2);
            if (left + pw + 12 > window.innerWidth) {
                left = Math.round(rect.left - pw - 14);
            }
            var headerHeight = 170;
            var minTop = headerHeight + 12;
            var maxTop = window.innerHeight - ph - 12;
            if (top < minTop) top = minTop;
            if (top > maxTop) top = maxTop;
            var arrowOffset = iconCenterY - top - 26;
            miniPopover.style.setProperty('--arrow-offset', arrowOffset + 'px');
            miniPopover.style.left = left + 'px';
            miniPopover.style.top = top + 'px';
        }
        function hidePopover() {
            if (!miniPopover) return;
            miniPopover.classList.remove('show');
            miniPopover.setAttribute('aria-hidden', 'true');
            currentPopoverKey = null;
        }
        function showPopoverForKey(key, icon) {
            if (!miniPopover) return;
            if (currentPopoverKey === key && miniPopover.classList.contains('show')) {
                hidePopover();
                return;
            }
            renderPopover(key);
            positionPopoverForIcon(icon);
            miniPopover.classList.add('show');
            miniPopover.setAttribute('aria-hidden', 'false');
            currentPopoverKey = key;
        }

        document.addEventListener('click', function(e){
            if (!miniPopover) return;
            if (!miniPopover.classList.contains('show')) return;
            if (e.target.closest('.mini-popover') || e.target.closest('.sub-indicator')) return;
            hidePopover();
        });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hidePopover(); });

        if(browseToggle) {
            browseToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var isMiniSidebarVisible = miniSidebar && window.getComputedStyle(miniSidebar).display !== 'none';
                if(window.innerWidth > 1024 && isMiniSidebarVisible) {
                    miniSidebar.classList.toggle('expanded');
                    browseToggle.classList.toggle('expanded');
                } else {
                    if(mainSidebar.classList.contains('active')) {
                        mainSidebar.classList.remove('active');
                        backdrop.classList.remove('active');
                    } else {
                        mainSidebar.classList.add('active');
                        backdrop.classList.add('active');
                        mainSidebar.style.display = 'block';
                        backdrop.style.display = 'block';
                    }
                }
            });
        }

        if(expandBtn) {
            expandBtn.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        var menuBar = document.getElementById('miniSidebarMenuBar');
        if(menuBar) {
            menuBar.addEventListener('click', function() {
                miniSidebar.classList.toggle('expanded');
                if(browseToggle) browseToggle.classList.toggle('expanded');
            });
        }

        var arrowHandler = function(e) {
            e.stopPropagation();
            e.preventDefault();
            var arrow = (e.target && e.target.closest('.sub-indicator')) || e.currentTarget;
            var icon = arrow ? arrow.closest('.mini-sidebar-icon') : null;
            if (!icon) return;
            var dataTarget = icon.getAttribute('data-target') || '';
            var categoryKey = getCategoryKeyFromTarget(dataTarget);
            if (!categoryKey) return;
            showPopoverForKey(categoryKey, icon);
        };
        
        document.querySelectorAll('.sub-indicator').forEach(function(arrow) {
            arrow.addEventListener('click', arrowHandler, true);
        });
        
        document.addEventListener('click', function(e) {
            if (e.target.closest('.sub-indicator')) {
                arrowHandler(e);
            }
        }, true);

        miniIcons.forEach(function(icon) {
            icon.addEventListener('click', function(e) {
                if (e.target.closest('.sub-indicator')) {
                    e.stopPropagation();
                    return;
                }
                var target = this.getAttribute('data-target');
                if (target) {
                    window.location.href = target;
                }
            }, true);
        });

        if(backdrop) {
            backdrop.addEventListener('click', function() {
                if(mainSidebar.classList.contains('active')) {
                    mainSidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                }
            });
        }

        var closeSidebarBtn = document.getElementById('closeSidebar');
        if(closeSidebarBtn) {
            closeSidebarBtn.addEventListener('click', function() {
                if(mainSidebar.classList.contains('active')) {
                    mainSidebar.classList.remove('active');
                    backdrop.classList.remove('active');
                }
            });
        }
