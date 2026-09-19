/**
 * Hero Slider front end.
 *
 * Two Swipers: the background images and the thumbnail strip. The strip is wired
 * in as Swiper's `thumbs` source so it keeps the active thumbnail in view and in
 * sync, while the fixed content layer sits outside both and never moves.
 */
document.addEventListener('DOMContentLoaded', function () {
    const heroes = document.querySelectorAll('.wp-block-dnte-hero-slider');

    if (!heroes.length || typeof Swiper === 'undefined') {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    heroes.forEach(function (hero) {
        const bgEl = hero.querySelector('.dnte-hero-bg');

        if (!bgEl) {
            return;
        }

        let options = {};

        try {
            options = JSON.parse(hero.getAttribute('data-options')) || {};
        } catch (error) {
            options = {};
        }

        const { effect = 'fade', speed = 800, loop = false, autoplay = false, thumbGaps = {} } = options;

        const thumbsEl = hero.querySelector('.dnte-hero-thumbs');
        let thumbsSwiper = null;

        /**
         * The strip never loops. Looping both instances makes Swiper reconcile two
         * sets of duplicated slides against each other, and the thumbnail that
         * lights up drifts out of step with the image on screen; a plain strip lets
         * the thumbs module map the background's `realIndex` straight onto it.
         */
        if (thumbsEl) {
            thumbsSwiper = new Swiper(thumbsEl, {
                slidesPerView: 'auto',
                watchSlidesProgress: true,
                slideToClickedSlide: true,
                spaceBetween: thumbGaps.Mobile ?? 12,
                breakpoints: {
                    320: { spaceBetween: thumbGaps.Mobile ?? 12 },
                    768: { spaceBetween: thumbGaps.Tablet ?? 20 },
                    1025: { spaceBetween: thumbGaps.Desktop ?? 20 }
                }
            });
        }

        const mainSwiper = new Swiper(bgEl, {
            effect: effect,
            fadeEffect: { crossFade: true },
            speed: prefersReducedMotion ? 0 : speed,
            loop: loop,
            slidesPerView: 1,
            grabCursor: true,
            allowTouchMove: true,
            touchRatio: 1,
            touchAngle: 45,
            autoplay: autoplay && !prefersReducedMotion ? { delay: autoplay.delay || 5000, disableOnInteraction: false } : false,
            navigation: {
                nextEl: hero.querySelector('.swiper-custom-next'),
                prevEl: hero.querySelector('.swiper-custom-prev')
            },
            ...(thumbsSwiper ? { thumbs: { swiper: thumbsSwiper } } : {})
        });

        /**
         * Per-slide mode stacks one content layer per slide. They cross-fade in
         * step with the background rather than riding inside the carousel, so
         * the content keeps its own position and is never dragged sideways.
         */
        const contentSlides = Array.from(hero.querySelectorAll('.dnte-hero-content-slide'));

        const syncContent = function () {
            if (!contentSlides.length) {
                return;
            }

            contentSlides.forEach(function (panel, index) {
                const isActive = index === mainSwiper.realIndex;

                panel.classList.toggle('is-active', isActive);
                // Inert while hidden, so tabbing cannot land on an offscreen button.
                panel.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });
        };

        mainSwiper.on('slideChange', syncContent);
        syncContent();

        const thumbButtons = Array.from(hero.querySelectorAll('.dnte-hero-thumb'));

        if (!thumbButtons.length) {
            return;
        }

        /**
         * The thumbs module already handles the click, but it listens on the slide
         * wrapper. Driving the jump from the button itself keeps keyboard
         * activation (Enter/Space both fire `click`) working identically.
         */
        thumbButtons.forEach(function (button, index) {
            button.addEventListener('click', function () {
                if (loop) {
                    mainSwiper.slideToLoop(index);
                } else {
                    mainSwiper.slideTo(index);
                }
            });
        });

        const syncActiveThumb = function () {
            const active = mainSwiper.realIndex;

            thumbButtons.forEach(function (button, index) {
                if (index === active) {
                    button.setAttribute('aria-current', 'true');
                } else {
                    button.removeAttribute('aria-current');
                }
            });
        };

        mainSwiper.on('slideChange', syncActiveThumb);
        syncActiveThumb();
    });
});
