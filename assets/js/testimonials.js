/**
 * Testimonials carousel — [dnte_testimonials]
 *
 * Initialises one Swiper per shortcode instance. Options come from the
 * shortcode attributes, serialised onto the element by PHP.
 */
(function () {
    'use strict';

    var SELECTOR = '[data-dnte-testimonials]';

    function readOptions(el) {
        try {
            return JSON.parse(el.getAttribute('data-dnte-testimonials')) || {};
        } catch (e) {
            return {};
        }
    }

    function init() {
        var nodes = document.querySelectorAll(SELECTOR);

        if (!nodes.length || typeof window.Swiper === 'undefined') {
            return;
        }

        Array.prototype.forEach.call(nodes, function (el) {
            if (el.dataset.dnteTestimonialsReady) {
                return;
            }

            var opts = readOptions(el);
            var root = el.closest('.dnte-testimonials') || el.parentNode;
            var pagination = root ? root.querySelector('.dnte-testimonials__pagination') : null;

            var slideCount = el.querySelectorAll('.swiper-slide').length;

            var config = {
                slidesPerView: 'auto',
                centeredSlides: true,

                /*
                 * Open on the middle card. Centred mode centres whichever slide
                 * is active, so starting on the first one pushes the whole deck
                 * right and leaves a gap at the left. Starting in the middle
                 * spreads the deck either side of centre, which is how the
                 * design is framed.
                 */
                initialSlide: slideCount > 2 ? Math.floor(slideCount / 2) : 0,

                /*
                 * Negative, so the cards overlap slightly rather than merely
                 * touching — the deck reads as a stack, not a row.
                 */
                spaceBetween: -12,

                loop: !!opts.loop,
                grabCursor: true,
                // The deck is tilted and its shadows spill past the track, so
                // the slides must stay visible outside it.
                watchOverflow: true
            };

            if (pagination) {
                config.pagination = { el: pagination, clickable: true };
            }

            if (opts.autoplay) {
                config.autoplay = {
                    delay: opts.delay || 5000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                };
            }

            // Honour the visitor's motion preference rather than autoplaying.
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                delete config.autoplay;
            }

            new window.Swiper(el, config);
            el.dataset.dnteTestimonialsReady = 'true';
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
