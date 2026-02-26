<?php
// Contact information for dropdown (always needed)
$contact_phone = "+1(234) 567 8900";
$contact_phone2 = "+1(234) 567 8900";
$contact_phone3 = "+1(639) 977 803 7398";
$contact_email = "info@andison-industrial.com";
?>

    <script>
        // ============================================
        // UPDATE CART BADGE COUNT IN REAL-TIME
        // ============================================
        (function(){
            function updateCartBadge() {
                var badge = document.getElementById('cartBadge');
                if(!badge) return;
                
                var items = JSON.parse(localStorage.getItem('inquiryItems') || '[]');
                var count = items.length;
                
                if(count > 0) {
                    badge.textContent = count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
            
            // Update on page load
            updateCartBadge();
            
            // Update on storage change (when items added from other pages)
            window.addEventListener('storage', updateCartBadge);
            
            // Update frequently to catch changes
            setInterval(updateCartBadge, 500);
        })();
    </script>


    <!-- Step Navigation Logic -->
    <script>
    (function() {
        // Allow clicking a selected radio card to deselect it (highlight updates via :checked CSS)
        document.querySelectorAll('.radio-card').forEach(function(card) {
            var radio = card.querySelector('input[type="radio"]');
            // Record state before click (label click re-checks the radio after our handler)
            card.addEventListener('mousedown', function() {
                radio._wasChecked = radio.checked;
            });
            card.addEventListener('click', function() {
                if (radio._wasChecked) {
                    // Uncheck after browser finishes processing the label click
                    setTimeout(function() {
                        radio.checked = false;
                        radio.dispatchEvent(new Event('change', { bubbles: true }));
                    }, 0);
                }
            });
        });
        var steps = document.querySelectorAll('.inq-step');
        var cards = [
            document.getElementById('card-items'),
            document.getElementById('card-contact'),
            document.getElementById('card-prefs'),
            document.getElementById('card-submit')
        ];

        // Click step → scroll to card
        steps.forEach(function(step) {
            step.addEventListener('click', function() {
                var targetId = step.getAttribute('data-target');
                var target = document.getElementById(targetId);
                if (target) {
                    var offset = 160;
                    var top = target.getBoundingClientRect().top + window.scrollY - offset;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            });
        });

        // Set active step based on scroll position
        function setActiveStep(idx) {
            steps.forEach(function(s, i) {
                s.classList.toggle('active', i === idx);
            });
        }

        // IntersectionObserver — highlight step when card enters viewport
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var idx = cards.indexOf(entry.target);
                    if (idx !== -1) setActiveStep(idx);
                }
            });
        }, { rootMargin: '-40% 0px -50% 0px', threshold: 0 });

        cards.forEach(function(card) { if (card) observer.observe(card); });

        // Mark step as completed when required fields in that card are filled
        function checkCompletion() {
            // Step 2 (Contact) — all required fields filled
            var contactFields = ['fullname','company','email','phone','address'];
            var contactDone = contactFields.every(function(id) {
                var el = document.getElementById(id);
                return el && el.value.trim() !== '';
            });
            steps[1].classList.toggle('completed', contactDone);

            // Step 3 (Preferences) — check if any contact_method radio is actually selected
            var prefsDone = !!document.querySelector('input[name="contact_method"]:checked');
            steps[2].classList.toggle('completed', prefsDone);

            // Step 1 (Items) — at least 1 item
            var hasItems = document.getElementById('inquiryTableBody') &&
                           document.getElementById('inquiryTableBody').children.length > 0;
            steps[0].classList.toggle('completed', hasItems);
        }

        // Listen for input changes
        document.getElementById('inquiryForm').addEventListener('input', checkCompletion);
        document.getElementById('inquiryForm').addEventListener('change', checkCompletion);

        // Re-check after table renders (called from inquiry logic)
        window._inqStepCheck = checkCompletion;
    })();
    </script>

    <!-- Inquiry Form Logic -->
    <script>
    (function() {
        var STORAGE_KEY = 'inquiryItems';

        function getItems() {
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
            catch(e) { return []; }
        }

        function saveItems(items) {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        }

        function renderTable() {
            var items = getItems();
            var tbody = document.getElementById('inquiryTableBody');
            var emptyMsg = document.getElementById('emptyMsg');
            var table = document.getElementById('inquiryTable');

            tbody.innerHTML = '';

            if (items.length === 0) {
                table.style.display = 'none';
                emptyMsg.style.display = 'block';
            } else {
                table.style.display = 'table';
                emptyMsg.style.display = 'none';
                items.forEach(function(item, idx) {
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td>' + escHtml(item.name || '') + (item.brand ? '<br><span style="font-size:11px;color:#888;">(' + escHtml(item.brand) + ')</span>' : '') + '</td>' +
                        '<td><input class="inq-qty-input" type="number" min="1" value="' + (item.qty || 1) + '" data-idx="' + idx + '"></td>' +
                        '<td><button class="inq-remove-btn" data-idx="' + idx + '" type="button">Remove</button></td>';
                    tbody.appendChild(tr);
                });
            }

            // Update hidden input
            document.getElementById('itemsJsonInput').value = JSON.stringify(items);

            // Update step completion state
            if (typeof window._inqStepCheck === 'function') window._inqStepCheck();
        }

        function escHtml(str) {
            return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        // Event delegation for remove, qty input, and qty +/- buttons
        document.getElementById('inquiryTableBody').addEventListener('click', function(e) {
            var btn = e.target.closest('.inq-remove-btn');
            if (btn) {
                var idx = parseInt(btn.getAttribute('data-idx'));
                var items = getItems();
                items.splice(idx, 1);
                saveItems(items);
                renderTable();
                return;
            }
            var decBtn = e.target.closest('.inq-qty-dec');
            if (decBtn) {
                var idx = parseInt(decBtn.getAttribute('data-idx'));
                var items = getItems();
                if (items[idx] && items[idx].qty > 1) {
                    items[idx].qty = (items[idx].qty || 1) - 1;
                    saveItems(items);
                    renderTable();
                }
                return;
            }
            var incBtn = e.target.closest('.inq-qty-inc');
            if (incBtn) {
                var idx = parseInt(incBtn.getAttribute('data-idx'));
                var items = getItems();
                if (items[idx]) {
                    items[idx].qty = (items[idx].qty || 1) + 1;
                    saveItems(items);
                    renderTable();
                }
                return;
            }
        });

        document.getElementById('inquiryTableBody').addEventListener('change', function(e) {
            if (e.target.classList.contains('inq-qty-input')) {
                var idx = parseInt(e.target.getAttribute('data-idx'));
                var items = getItems();
                var val = parseInt(e.target.value);
                if (!isNaN(val) && val > 0) { items[idx].qty = val; }
                saveItems(items);
                document.getElementById('itemsJsonInput').value = JSON.stringify(items);
            }
        });

        // Sync itemsJsonInput before submit
        document.getElementById('inquiryForm').addEventListener('submit', function() {
            document.getElementById('itemsJsonInput').value = JSON.stringify(getItems());
        });

        // Clear button
        document.getElementById('clearFormBtn').addEventListener('click', function() {
            if (confirm('Clear the form and all inquiry items?')) {
                localStorage.removeItem(STORAGE_KEY);
                document.getElementById('inquiryForm').reset();
                renderTable();
            }
        });

        // Initial render
        renderTable();

        // Re-render if localStorage changes from another tab
        window.addEventListener('storage', function(e) {
            if (e.key === STORAGE_KEY) renderTable();
        });
    })();
    </script>

    <script>
    // File drop zone & file name display
    function handleFileChange(input) {
        var nameEl = document.getElementById('fileChosenName');
        if (input.files && input.files.length > 0) {
            nameEl.textContent = '\u2714 ' + input.files[0].name;
            nameEl.style.display = 'block';
        } else {
            nameEl.style.display = 'none';
        }
    }
    (function() {
        var zone = document.getElementById('fileDropZone');
        if (!zone) return;
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            zone.classList.add('drag-over');
        });
        zone.addEventListener('dragleave', function() {
            zone.classList.remove('drag-over');
        });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            zone.classList.remove('drag-over');
            var fileInput = zone.querySelector('input[type="file"]');
            if (fileInput && e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                handleFileChange(fileInput);
            }
        });
    })();
    </script>
</body>
</html>



