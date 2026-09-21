/**
 * Ticker Gallery — fills the track so the loop never shows a gap.
 *
 * The markup ships two copies of the images and the animation translates the
 * track by exactly half its width, which is seamless only while one copy is at
 * least as wide as the block. A short image set on a wide screen leaves the
 * far edge uncovered for the rest of the pass — the blank space after each
 * loop.
 *
 * So: measure one copy, work out how many it takes to span the block, and
 * clone until the half that scrolls past is always wider than what is visible.
 * Because every copy is identical, any number of them still lands copy n+1
 * exactly where copy 1 began.
 */
(function () {
    'use strict';

    var SELECTOR = '.wp-block-dnte-ticker-gallery';
    var CLONE_ATTR = 'data-dnte-ticker-clone';

    /* Stops a stray configuration — tiny images on a huge screen — from
     * cloning the DOM into the thousands. */
    var MAX_REPEATS = 24;

    function fit(root) {
        var track = root.querySelector('.dnte-ticker__track');

        if (!track) {
            return;
        }

        // Clear a previous fit first, so a resize recalculates from the
        // original set rather than compounding.
        var stale = track.querySelectorAll('[' + CLONE_ATTR + ']');
        for (var i = 0; i < stale.length; i++) {
            stale[i].parentNode.removeChild(stale[i]);
        }

        var items = [].slice.call(track.children);

        if (items.length < 2) {
            return;
        }

        // save.js writes exactly two copies, so the first half is one set.
        var base = items.slice(0, items.length / 2);
        var gap = parseFloat(window.getComputedStyle(track).gap) || 0;

        var setWidth = base.reduce(function (width, el) {
            return width + el.getBoundingClientRect().width + gap;
        }, 0);

        if (!setWidth) {
            return;
        }

        var repeats = Math.ceil(root.getBoundingClientRect().width / setWidth);
        repeats = Math.max(1, Math.min(MAX_REPEATS, repeats));

        // Two copies already exist, so each extra repeat needs two more.
        var extraSets = (repeats - 1) * 2;

        for (var s = 0; s < extraSets; s++) {
            for (var j = 0; j < base.length; j++) {
                var clone = base[j].cloneNode(true);

                // Duplicates of images already in the DOM; one copy is enough
                // for assistive tech.
                clone.setAttribute('aria-hidden', 'true');
                clone.setAttribute(CLONE_ATTR, '');
                track.appendChild(clone);
            }
        }

        /*
         * The pass now covers `repeats` sets rather than one, so the duration
         * scales with it — otherwise adding copies would silently speed the
         * ticker up.
         */
        var speed = parseFloat(window.getComputedStyle(root).getPropertyValue('--dnte-ticker-speed')) || 40;
        track.style.animationDuration = speed * repeats + 's';
    }

    function boot() {
        var roots = [].slice.call(document.querySelectorAll(SELECTOR));

        if (!roots.length) {
            return;
        }

        roots.forEach(fit);

        var timer = null;
        window.addEventListener('resize', function () {
            window.clearTimeout(timer);
            timer = window.setTimeout(function () {
                roots.forEach(fit);
            }, 200);
        });

        /*
         * Images without intrinsic dimensions in the markup measure as zero
         * until they load, which would under-count the set width.
         */
        var images = [].slice.call(document.querySelectorAll(SELECTOR + ' img'));
        images.forEach(function (img) {
            if (!img.complete) {
                img.addEventListener('load', function () {
                    roots.forEach(fit);
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
