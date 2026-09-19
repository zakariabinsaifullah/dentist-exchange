/**
 * Latest Insights — [latest_insights]
 *
 * Initialises the post carousel: one slide per group of posts, dot pagination,
 * no arrows, and a vertical bar tracking which slide is showing.
 *
 * The bar is driven from `slideChange` rather than Swiper's own progress value
 * so it steps cleanly from one slide to the next instead of creeping along
 * mid-drag.
 */
document.addEventListener('DOMContentLoaded', function () {
    var sections = document.querySelectorAll('.hli__posts');

    if (!sections.length || typeof Swiper === 'undefined') {
        return;
    }

    Array.prototype.forEach.call(sections, function (section) {
        var carousel = section.querySelector('.hli-carousel');
        var fill = section.querySelector('.hli-progress__fill');
        var pagination = section.querySelector('.hli-pagination');

        if (!carousel) {
            return;
        }

        var total = carousel.querySelectorAll('.swiper-slide').length;

        // One slide is not a carousel: leave the bar and dots off entirely.
        if (total < 2) {
            section.classList.add('hli__posts--single');
            return;
        }

        var swiper = new Swiper(carousel, {
            slidesPerView: 1,
            spaceBetween: 24,
            autoHeight: true,
            grabCursor: true,
            a11y: {
                enabled: true
            },
            pagination: pagination
                ? {
                      el: pagination,
                      clickable: true,
                      bulletClass: 'hli-pagination__dot',
                      bulletActiveClass: 'hli-pagination__dot--active'
                  }
                : false
        });

        if (!fill) {
            return;
        }

        /**
         * The fill grows a step per slide — 1/total on the first, the whole
         * track on the last. Scaling from the top rather than setting a height
         * keeps this off the layout path and immune to Swiper's `autoHeight`
         * resizing the track underneath it.
         *
         * The concrete scale is written here rather than by updating the
         * `--hli-progress` custom property the stylesheet reads. Both work; this
         * keeps the animated value plain and self-contained, leaving the CSS var
         * responsible only for the first step in the server-rendered markup,
         * before this script runs.
         */
        var updateProgress = function () {
            fill.style.transform = 'scaleY(' + (swiper.realIndex + 1) / total + ')';
        };

        swiper.on('slideChange', updateProgress);
        updateProgress();
    });
});
