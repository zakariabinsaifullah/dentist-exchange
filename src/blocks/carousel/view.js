document.addEventListener('DOMContentLoaded', function () {
    const sliders = document.querySelectorAll('.wp-block-dnte-carousel');
    sliders.forEach(function (slider) {
        const options = JSON.parse(slider.getAttribute('data-options'));
        const { loop, autoplay, columns, gaps } = options;

        const swiper = new Swiper(slider.querySelector('.swiper'), {
            loop: loop,
            autoplay: autoplay,
            navigation: {
                nextEl: slider.querySelector('.swiper-custom-next'),
                prevEl: slider.querySelector('.swiper-custom-prev')
            },
            pagination: {
                el: slider.querySelector('.swiper-pagination'),
                type: 'fraction',
                formatFractionCurrent: function (number) {
                    return ('0' + number).slice(-2);
                },
                formatFractionTotal: function (number) {
                    return ('0' + number).slice(-2);
                },
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
            slidesPerView: columns?.Desktop || 1,
            spaceBetween: gaps?.Desktop || 20,
            breakpoints: {
                // when window width is >= 480px
                320: {
                    slidesPerView: columns?.Mobile || 1,
                    spaceBetween: gaps?.Mobile || 0
                },
                // when window width is >= 768px
                768: {
                    slidesPerView: columns?.Tablet || 2,
                    spaceBetween: gaps?.Tablet || 15
                },
                1025: {
                    slidesPerView: columns?.Desktop || 1,
                    spaceBetween: gaps?.Desktop || 20
                }
            }
        });
    });
});
