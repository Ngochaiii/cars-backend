/*
 * Tương tác frontend — JS thuần, không thư viện, không build.
 *
 * Nguyên tắc: mọi khối đều đọc được khi TẮT JS. Script này chỉ nâng cấp
 * thêm (chuyển slide, lọc tab, đổi màu). Không có script thì carousel thành
 * dải cuộn ngang vuốt được, tab hiện tất cả — không có nút chết.
 */
(function () {
    'use strict';

    document.documentElement.classList.add('js');

    /* ── Carousel hero: các slide chồng lên nhau, đổi bằng opacity ────── */
    function initHero(root) {
        var slides = root.querySelectorAll('[data-hero-slide]');
        if (slides.length < 2) return;

        var dots = root.querySelectorAll('[data-hero-dot]');
        var count = root.querySelector('[data-hero-count]');
        var i = 0;

        function pad(n) { return (n < 10 ? '0' : '') + n; }

        function show(next) {
            i = (next + slides.length) % slides.length;
            for (var k = 0; k < slides.length; k++) {
                slides[k].classList.toggle('is-on', k === i);
                slides[k].setAttribute('aria-hidden', k === i ? 'false' : 'true');
            }
            for (var d = 0; d < dots.length; d++) {
                dots[d].classList.toggle('is-on', d === i);
                dots[d].setAttribute('aria-current', d === i ? 'true' : 'false');
            }
            if (count) count.textContent = pad(i + 1) + ' / ' + pad(slides.length);
        }

        root.addEventListener('click', function (e) {
            var prev = e.target.closest('[data-hero-prev]');
            var next = e.target.closest('[data-hero-next]');
            var dot = e.target.closest('[data-hero-dot]');
            if (prev) { e.preventDefault(); show(i - 1); }
            else if (next) { e.preventDefault(); show(i + 1); }
            else if (dot) { e.preventDefault(); show(+dot.dataset.heroDot); }
        });

        show(0);
    }

    /* ── Coverflow "Khám phá dải sản phẩm" ───────────────────────────── */
    function initDiscovery(root) {
        var items = Array.prototype.slice.call(root.querySelectorAll('[data-disc-item]'));
        if (!items.length) return;

        var tabs = root.querySelectorAll('[data-disc-tab]');
        var stage = root.querySelector('[data-disc-stage]');
        var count = root.querySelector('[data-disc-count]');
        var filter = 'all';
        var i = 0;

        function visible() {
            return items.filter(function (el) {
                return filter === 'all' || el.dataset.discCat === filter;
            });
        }

        function pad(n) { return (n < 10 ? '0' : '') + n; }

        function render() {
            var list = visible();
            if (!list.length) return;
            i = Math.min(i, list.length - 1);

            items.forEach(function (el) {
                el.hidden = true;
                el.classList.remove('is-on', 'is-peek');
            });

            list.forEach(function (el, k) {
                var rel = k - i;
                if (Math.abs(rel) <= 1) {
                    el.hidden = false;
                    el.classList.toggle('is-on', rel === 0);
                    el.classList.toggle('is-peek', rel !== 0);
                    el.style.order = String(rel);
                }
            });

            if (count) count.textContent = pad(i + 1) + ' / ' + pad(list.length);
            if (stage) stage.dataset.discSingle = list.length < 2 ? 'true' : 'false';
        }

        root.addEventListener('click', function (e) {
            var tab = e.target.closest('[data-disc-tab]');
            var prev = e.target.closest('[data-disc-prev]');
            var next = e.target.closest('[data-disc-next]');

            if (tab) {
                e.preventDefault();
                filter = tab.dataset.discTab;
                i = 0;
                for (var t = 0; t < tabs.length; t++) {
                    tabs[t].classList.toggle('is-on', tabs[t] === tab);
                }
                render();
            } else if (prev || next) {
                e.preventDefault();
                var n = visible().length;
                if (n > 1) { i = (i + (next ? 1 : -1) + n) % n; render(); }
            }
        });

        root.classList.add('is-live');
        render();
    }

    /* ── Bộ chọn màu ở trang chi tiết ────────────────────────────────── */
    function initSwatches(root) {
        var chips = root.querySelectorAll('[data-swatch]');
        if (chips.length < 2) return;

        var panel = document.querySelector('[data-swatch-panel]');
        var label = document.querySelector('[data-swatch-label]');
        if (!panel) return;

        root.addEventListener('click', function (e) {
            var chip = e.target.closest('[data-swatch]');
            if (!chip) return;
            e.preventDefault();

            for (var c = 0; c < chips.length; c++) chips[c].classList.toggle('is-on', chips[c] === chip);

            var hex = chip.dataset.swatch;
            var img = chip.dataset.swatchImage;
            panel.style.background = img ? 'center/cover url("' + img + '")' : (hex || '#1c1c1a');
            if (label) label.textContent = chip.dataset.swatchName || '';
        });

        var first = root.querySelector('[data-swatch]');
        if (first) first.classList.add('is-on');
    }

    /* ── Băng chuyền ảnh (mục layout-carousel, thư viện ở hero) ──────── */
    function initGallery(root) {
        var slides = root.querySelectorAll('[data-gal-slide]');
        if (slides.length < 2) return;

        var dots = root.querySelectorAll('[data-gal-dot]');
        var label = root.querySelector('[data-gal-label]');
        var count = root.querySelector('[data-gal-count]');
        var i = 0;

        function pad(n) { return (n < 10 ? '0' : '') + n; }

        function show(next) {
            i = (next + slides.length) % slides.length;
            for (var k = 0; k < slides.length; k++) {
                slides[k].classList.toggle('is-on', k === i);
                slides[k].setAttribute('aria-hidden', k === i ? 'false' : 'true');
            }
            for (var d = 0; d < dots.length; d++) {
                dots[d].classList.toggle('is-on', d === i);
            }
            if (label) label.textContent = slides[i].dataset.galLabel || '';
            if (count) count.textContent = pad(i + 1) + ' / ' + pad(slides.length);
        }

        root.addEventListener('click', function (e) {
            var prev = e.target.closest('[data-gal-prev]');
            var next = e.target.closest('[data-gal-next]');
            var dot = e.target.closest('[data-gal-dot]');
            if (prev) { e.preventDefault(); show(i - 1); }
            else if (next) { e.preventDefault(); show(i + 1); }
            else if (dot) { e.preventDefault(); show(+dot.dataset.galDot); }
        });

        root.classList.add('is-live');
        show(0);
    }

    /* ── Tab đánh số (mục layout-tabs) ───────────────────────────────── */
    function initTabs(root) {
        var tabs = root.querySelectorAll('[data-tab]');
        var panels = root.querySelectorAll('[data-tab-panel]');
        if (tabs.length < 2) return;

        function show(i) {
            for (var k = 0; k < tabs.length; k++) {
                tabs[k].classList.toggle('is-on', k === i);
                tabs[k].setAttribute('aria-selected', k === i ? 'true' : 'false');
            }
            for (var k2 = 0; k2 < panels.length; k2++) {
                panels[k2].classList.toggle('is-on', k2 === i);
                panels[k2].hidden = k2 !== i;
            }
        }

        root.addEventListener('click', function (e) {
            var tab = e.target.closest('[data-tab]');
            if (!tab) return;
            e.preventDefault();
            show(+tab.dataset.tab);
        });

        root.classList.add('is-live');
        show(0);
    }

    function boot() {
        document.querySelectorAll('[data-hero]').forEach(initHero);
        document.querySelectorAll('[data-disc]').forEach(initDiscovery);
        document.querySelectorAll('[data-swatches]').forEach(initSwatches);
        document.querySelectorAll('[data-gallery]').forEach(initGallery);
        document.querySelectorAll('[data-tabs]').forEach(initTabs);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
