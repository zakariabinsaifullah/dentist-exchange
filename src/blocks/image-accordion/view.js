document.addEventListener('DOMContentLoaded', function () {
    const accordions = document.querySelectorAll('.wp-block-dnte-image-accordion');

    accordions.forEach(function (accordion) {
        const items = accordion.querySelectorAll('.wp-block-dnte-image-accordion-item');

        items.forEach(function (item, index) {
            if (index === 0) {
                item.classList.add('active');
            }

            item.addEventListener('click', function () {
                items.forEach(function (sibling) {
                    sibling.classList.remove('active');
                });
                item.classList.add('active');
            });

            // A button with no real link configured falls back to href="#",
            // which otherwise jumps/scrolls the page to the top on click.
            const btn = item.querySelector('.btn');
            if (btn && (!btn.getAttribute('href') || btn.getAttribute('href') === '#')) {
                btn.addEventListener('click', function (event) {
                    event.preventDefault();
                });
            }
        });
    });
});
