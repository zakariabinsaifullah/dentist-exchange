document.addEventListener('DOMContentLoaded', function () {
    const sliders = document.querySelectorAll('.wp-block-dnte-carousel');
    sliders.forEach(function (slider) {
        const options = JSON.parse(slider.getAttribute('data-options'));
        const { loop, autoplay, gaps, visibleItems } = options;

        const swiper = new Swiper(slider.querySelector('.swiper'), {
            loop: loop,
            autoplay: autoplay,
            pagination: {
                el: slider.querySelector('.swiper-pagination'),
                type: 'bullets',
                clickable: true
            },
            // Touch/Swipe navigation options for mobile
            touchRatio: 1,
            touchAngle: 45,
            grabCursor: true,
            allowTouchMove: true,
            touchMoveStopPropagation: false,
            touchStartPreventDefault: false,
            touchStartForcePreventDefault: false,
            touchReleaseOnEdges: true,
            // Each Slide bakes its own rotate/offset/z-index into its own
            // markup (see slide/save.js), so the default 'slide' effect is
            // used here — Swiper only transforms the wrapper track for
            // horizontal movement in that effect, leaving each slide's own
            // transform untouched. `centeredSlides` is skipped on purpose:
            // it reserves extra space to center the active slide, which
            // shrinks how many slides actually fit versus "Visible Items".
            slidesPerView: visibleItems?.Desktop || 5,
            spaceBetween: gaps?.Desktop || 0,
            breakpoints: {
                320: {
                    slidesPerView: visibleItems?.Mobile || 1,
                    spaceBetween: gaps?.Mobile || 0
                },
                768: {
                    slidesPerView: visibleItems?.Tablet || 3,
                    spaceBetween: gaps?.Tablet || 0
                },
                1025: {
                    slidesPerView: visibleItems?.Desktop || 5,
                    spaceBetween: gaps?.Desktop || 0
                }
            }
        });
    });
});
