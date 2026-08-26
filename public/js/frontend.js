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

    /* ── Trang đặt cọc: đổi tab hình thức + wizard 2 bước ────────────── */
    /* Không có script thì cả hai bước nằm liền nhau trong cùng một <form>
       và các tab là link thật (?hinh-thuc=) — không có nút chết. */
    function initBooking(root) {
        var panes = root.querySelectorAll('[data-booking-pane]');
        if (!panes.length) return;

        var intros = root.querySelectorAll('[data-booking-intro]');
        var modes = root.querySelectorAll('[data-booking-mode]');
        var steps = root.querySelectorAll('[data-booking-step]');

        function activeKey() {
            for (var i = 0; i < panes.length; i++) {
                if (!panes[i].hidden) return panes[i].dataset.bookingPane;
            }
            return panes[0].dataset.bookingPane;
        }

        function pane(key) {
            for (var i = 0; i < panes.length; i++) {
                if (panes[i].dataset.bookingPane === key) return panes[i];
            }
            return null;
        }

        /* Bước 3 (màn cảm ơn) không có fieldset — coi như đã xong cả 3. */
        function showStep(key, n) {
            var host = pane(key);
            if (!host) return;

            var sets = host.querySelectorAll('[data-booking-pane-step]');
            if (!sets.length) { markSteps(3); return; }

            for (var i = 0; i < sets.length; i++) {
                sets[i].hidden = +sets[i].dataset.bookingPaneStep !== n;
            }
            markSteps(n);

            var kicker = null;
            for (var k = 0; k < intros.length; k++) {
                if (intros[k].dataset.bookingIntro === key) {
                    kicker = intros[k].querySelector('[data-booking-kicker]');
                }
            }
            if (kicker) kicker.textContent = kicker.textContent.replace(/bước \d\/3/, 'bước ' + n + '/3');
        }

        function markSteps(n) {
            for (var i = 0; i < steps.length; i++) {
                var num = +steps[i].dataset.bookingStep;
                steps[i].classList.toggle('is-on', num === n);
                steps[i].classList.toggle('is-done', num < n);
                var badge = steps[i].querySelector('.booking__step-num');
                if (badge) badge.textContent = num < n ? '✓' : String(num);
            }
        }

        function showMode(key) {
            for (var i = 0; i < panes.length; i++) panes[i].hidden = panes[i].dataset.bookingPane !== key;
            for (var j = 0; j < intros.length; j++) intros[j].hidden = intros[j].dataset.bookingIntro !== key;
            for (var m = 0; m < modes.length; m++) {
                modes[m].classList.toggle('chip--on', modes[m].dataset.bookingMode === key);
            }
            showStep(key, 1);
        }

        root.addEventListener('click', function (e) {
            var mode = e.target.closest('[data-booking-mode]');
            var next = e.target.closest('[data-booking-next]');
            var prev = e.target.closest('[data-booking-prev]');

            if (mode) {
                e.preventDefault();
                showMode(mode.dataset.bookingMode);
                history.replaceState(null, '', mode.getAttribute('href'));
            } else if (next) {
                e.preventDefault();
                showStep(activeKey(), 2);
                root.scrollIntoView({ block: 'start', behavior: 'smooth' });
            } else if (prev) {
                e.preventDefault();
                showStep(activeKey(), 1);
                root.scrollIntoView({ block: 'start', behavior: 'smooth' });
            }
        });

        /* Thẻ chọn: đánh dấu cái đang chọn trong cùng một nhóm radio. */
        root.addEventListener('change', function (e) {
            var input = e.target.closest('.pick input');
            if (!input) return;

            var group = root.querySelectorAll('.pick input[name="' + input.name + '"]');
            for (var i = 0; i < group.length; i++) {
                group[i].closest('.pick').classList.toggle('is-on', group[i].checked);
            }
        });

        /* Ô đang lỗi validate nằm ở bước nào thì mở đúng bước đó. */
        var key = activeKey();
        var bad = pane(key) && pane(key).querySelector('.field__error');
        var set = bad && bad.closest('[data-booking-pane-step]');
        showStep(key, set ? +set.dataset.bookingPaneStep : 1);
    }

    /* ── Popup thu lead ───────────────────────────────────────────────── */
    /* Mọi điều kiện đọc từ data-* (Cài đặt đổ xuống). Đóng rồi thì ghi mốc vào
       localStorage và im đúng số ngày đã khai — không hỏi lại mỗi lần tải. */
    function initPopup(root) {
        var key = 'popup:' + (root.dataset.popupKey || 'mac-dinh');
        var days = parseInt(root.dataset.popupDays, 10) || 0;
        var delay = (parseInt(root.dataset.popupDelay, 10) || 10) * 1000;

        try {
            var until = parseInt(localStorage.getItem(key), 10);
            if (until && Date.now() < until) return;
        } catch (e) { /* trình duyệt chặn localStorage thì cứ hiện */ }

        var opener = null;
        var timer = setTimeout(open, delay);

        function remember() {
            if (!days) return;
            try {
                localStorage.setItem(key, String(Date.now() + days * 86400000));
            } catch (e) { /* không ghi được thì thôi */ }
        }

        function focusables() {
            return root.querySelectorAll(
                'a[href], button:not([disabled]), input:not([type=hidden]), select, textarea'
            );
        }

        function open() {
            opener = document.activeElement;
            root.hidden = false;
            var first = root.querySelector('input:not([type=hidden]), button, select, textarea');
            if (first) first.focus();
            document.addEventListener('keydown', onKey);
        }

        function close() {
            clearTimeout(timer);
            root.hidden = true;
            remember();
            document.removeEventListener('keydown', onKey);
            if (opener && opener.focus) opener.focus();
        }

        /* Esc để đóng, Tab quẩn trong panel — không để tiêu điểm chạy ra sau
           lớp phủ rồi người dùng bàn phím mắc kẹt. */
        function onKey(e) {
            if (e.key === 'Escape') { e.preventDefault(); close(); return; }
            if (e.key !== 'Tab') return;

            var items = focusables();
            if (!items.length) return;

            var first = items[0];
            var last = items[items.length - 1];

            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        }

        root.addEventListener('click', function (e) {
            if (e.target.closest('[data-popup-close]')) { e.preventDefault(); close(); }
        });

        /* Bấm Gửi thì coi như xong việc: ghi mốc luôn để lần sau khỏi hiện. */
        var form = root.querySelector('form');
        if (form) form.addEventListener('submit', remember);
    }

    function boot() {
        document.querySelectorAll('[data-hero]').forEach(initHero);
        document.querySelectorAll('[data-disc]').forEach(initDiscovery);
        document.querySelectorAll('[data-swatches]').forEach(initSwatches);
        document.querySelectorAll('[data-gallery]').forEach(initGallery);
        document.querySelectorAll('[data-tabs]').forEach(initTabs);
        document.querySelectorAll('.booking').forEach(initBooking);
        document.querySelectorAll('[data-popup]').forEach(initPopup);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
