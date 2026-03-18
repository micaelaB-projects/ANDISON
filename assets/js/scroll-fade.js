(function () {
    if (window.__andisonScrollFadeInit) {
        return;
    }
    window.__andisonScrollFadeInit = true;

    var STYLE_ID = 'andison-scroll-fade-style';
    var TARGET_CLASS = 'andison-scroll-fade-target';
    var VISIBLE_CLASS = 'andison-scroll-fade-visible';
    var READY_ATTR = 'data-andison-fade-ready';

    var EXCLUDE_SELECTOR = [
        'header',
        'nav',
        '.header-top',
        '.right-actions',
        '.overlay-backdrop',
        '.sidebar-overlay',
        '.mini-sidebar',
        '.mobile-sidebar-fab',
        '#sidebar',
        '#miniSidebar',
        '#miniPopover',
        '.contact-popover',
        '.nav-dropdown',
        '.modal',
        '.modal-overlay',
        '.modal-backdrop',
        '.inquiry-toast'
    ].join(',');

    var CANDIDATE_SELECTOR = [
        'main > *',
        'section',
        'article',
        '.container > *',
        '.page-content > *',
        '.main-content > *',
        '.category-container > *',
        '.results-grid > *',
        '.product-grid > *',
        '.products-grid > *',
        '.brand-grid > *',
        '.card',
        '.result-card',
        '.product-card',
        '.brand-card',
        '.service-card',
        '.industry-card',
        'footer.footer-modernized .footer-main-grid > *'
    ].join(',');

    var observer = null;
    var nextDelayIndex = 0;
    var revealQueue = [];
    var revealRaf = 0;

    function injectStyles() {
        if (document.getElementById(STYLE_ID)) {
            return;
        }

        var style = document.createElement('style');
        style.id = STYLE_ID;
        style.textContent = [
            '.' + TARGET_CLASS + ' {',
            '  opacity: 0;',
            '  transform: translate3d(0, 18px, 0);',
            '  transition: opacity 0.82s cubic-bezier(0.22, 1, 0.36, 1), transform 0.82s cubic-bezier(0.22, 1, 0.36, 1);',
            '  transition-delay: var(--andison-fade-delay, 0ms);',
            '  will-change: opacity, transform;',
            '  backface-visibility: hidden;',
            '}',
            '.' + TARGET_CLASS + '.' + VISIBLE_CLASS + ' {',
            '  opacity: 1;',
            '  transform: translate3d(0, 0, 0);',
            '}',
            'html.andison-no-fade .' + TARGET_CLASS + ' {',
            '  opacity: 1 !important;',
            '  transform: none !important;',
            '  transition: none !important;',
            '  will-change: auto !important;',
            '}',
            '@media (prefers-reduced-motion: reduce) {',
            '  .' + TARGET_CLASS + ' {',
            '    opacity: 1 !important;',
            '    transform: none !important;',
            '    transition: none !important;',
            '    will-change: auto !important;',
            '  }',
            '}'
        ].join('\n');

        document.head.appendChild(style);
    }

    function isEligible(el) {
        if (!(el instanceof HTMLElement)) {
            return false;
        }

        if (el.getAttribute(READY_ATTR) === '1') {
            return false;
        }

        if (el.closest(EXCLUDE_SELECTOR)) {
            return false;
        }

        var tag = el.tagName;
        if (tag === 'SCRIPT' || tag === 'STYLE' || tag === 'LINK' || tag === 'NOSCRIPT') {
            return false;
        }

        var cs = window.getComputedStyle(el);
        if (cs.display === 'none' || cs.visibility === 'hidden') {
            return false;
        }

        if (cs.position === 'fixed' || cs.position === 'sticky') {
            return false;
        }

        var rect = el.getBoundingClientRect();
        if (rect.height < 32 || rect.width < 32) {
            return false;
        }

        return true;
    }

    function isNearViewport(el) {
        var rect = el.getBoundingClientRect();
        return rect.top <= window.innerHeight * 0.92;
    }

    function markTarget(el) {
        el.setAttribute(READY_ATTR, '1');
        el.classList.add(TARGET_CLASS);
        el.style.setProperty('--andison-fade-delay', String((nextDelayIndex % 5) * 42) + 'ms');
        nextDelayIndex += 1;
    }

    function queueReveal(el) {
        revealQueue.push(el);
        if (revealRaf !== 0) {
            return;
        }

        revealRaf = window.requestAnimationFrame(function () {
            revealRaf = 0;
            var seen = new Set();

            for (var i = 0; i < revealQueue.length; i++) {
                var target = revealQueue[i];
                if (!(target instanceof HTMLElement) || seen.has(target)) {
                    continue;
                }
                seen.add(target);
                target.classList.add(VISIBLE_CLASS);
                target.style.willChange = 'opacity, transform';
            }

            revealQueue = [];
        });
    }

    function addIfEligible(el, output, seen) {
        if (!(el instanceof HTMLElement) || seen.has(el) || !isEligible(el)) {
            return;
        }
        seen.add(el);
        output.push(el);
    }

    function collectTargetsFromRoot(root) {
        var out = [];
        var seen = new Set();

        if (root && root.nodeType === 1 && typeof root.matches === 'function' && root.matches(CANDIDATE_SELECTOR)) {
            addIfEligible(root, out, seen);
        }

        var nodes = null;
        if (root && typeof root.querySelectorAll === 'function') {
            nodes = root.querySelectorAll(CANDIDATE_SELECTOR);
        } else {
            nodes = document.querySelectorAll(CANDIDATE_SELECTOR);
        }

        for (var i = 0; i < nodes.length; i++) {
            addIfEligible(nodes[i], out, seen);
        }

        return out;
    }

    function registerTargets(targets) {
        for (var i = 0; i < targets.length; i++) {
            var target = targets[i];
            markTarget(target);

            if (observer) {
                observer.observe(target);
            }

            if (isNearViewport(target)) {
                queueReveal(target);
            }
        }
    }

    function initMutationRegistration() {
        var queuedRoots = [];
        var rootsTimer = 0;

        function flushRoots() {
            rootsTimer = 0;
            if (!queuedRoots.length) {
                return;
            }

            var localRoots = queuedRoots;
            queuedRoots = [];

            var targets = [];
            var seenTargets = new Set();

            for (var i = 0; i < localRoots.length; i++) {
                var root = localRoots[i];
                var found = collectTargetsFromRoot(root);
                for (var j = 0; j < found.length; j++) {
                    var candidate = found[j];
                    if (seenTargets.has(candidate)) {
                        continue;
                    }
                    seenTargets.add(candidate);
                    targets.push(candidate);
                }
            }

            registerTargets(targets);
        }

        var mo = new MutationObserver(function (mutations) {
            var hasElementAdds = false;

            for (var i = 0; i < mutations.length; i++) {
                var mutation = mutations[i];
                if (mutation.type !== 'childList' || !mutation.addedNodes.length) {
                    continue;
                }

                for (var j = 0; j < mutation.addedNodes.length; j++) {
                    var node = mutation.addedNodes[j];
                    if (node.nodeType === 1) {
                        queuedRoots.push(node);
                        hasElementAdds = true;
                    }
                }
            }

            if (!hasElementAdds || rootsTimer !== 0) {
                return;
            }

            rootsTimer = window.setTimeout(flushRoots, 130);
        });

        if (document.body) {
            mo.observe(document.body, { childList: true, subtree: true });
        }
    }

    function initObserver() {
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.classList.add('andison-no-fade');
            return;
        }

        if (typeof window.IntersectionObserver !== 'function') {
            document.documentElement.classList.add('andison-no-fade');
            return;
        }

        injectStyles();

        observer = new IntersectionObserver(function (entries) {
            for (var i = 0; i < entries.length; i++) {
                var entry = entries[i];
                if (entry.isIntersecting || entry.intersectionRatio > 0) {
                    queueReveal(entry.target);
                } else if (entry.target instanceof HTMLElement) {
                    entry.target.classList.remove(VISIBLE_CLASS);
                    entry.target.style.willChange = 'opacity, transform';
                }
            }
        }, {
            root: null,
            threshold: 0.08,
            rootMargin: '0px 0px -8% 0px'
        });

        registerTargets(collectTargetsFromRoot(document));
        initMutationRegistration();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initObserver, { once: true });
    } else {
        initObserver();
    }
})();
