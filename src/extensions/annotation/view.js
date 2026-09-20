/**
 * Annotation scroll-draw.
 *
 * Two jobs: measure each stroke path so the stylesheet can dash exactly one
 * full length, and add `.is-drawn` when the phrase scrolls into view, which
 * starts the CSS animation that walks the stroke on.
 *
 * Enqueued by dnte_render_annotation() only on pages that render an annotation.
 */

const SELECTOR = '.dnte-annotation';
const DRAWN_CLASS = 'is-drawn';
const LENGTH_VARIABLE = '--dnte-stroke-length';

/**
 * Publishes each path's own length so the stylesheet can dash exactly one
 * stroke. See the draw-in comment in style.scss for why this is measured here
 * rather than declared with `pathLength` on the artwork.
 *
 * The length is in the SVG's user units, which do not change when the element
 * is resized, so this never needs recomputing.
 *
 * @param {Element} annotation An annotated span.
 */
const measure = annotation => {
    annotation.querySelectorAll('.dnte-annotation__stroke path').forEach(path => {
        if (typeof path.getTotalLength !== 'function') {
            return;
        }

        path.style.setProperty(LENGTH_VARIABLE, path.getTotalLength());
    });
};

const draw = () => {
    const annotations = document.querySelectorAll(`${SELECTOR}:not(.${DRAWN_CLASS})`);

    if (!annotations.length) {
        return;
    }

    annotations.forEach(measure);

    // Without IntersectionObserver there is no way to know when a stroke comes
    // into view, so draw them all at once rather than leaving them hidden.
    if (typeof IntersectionObserver === 'undefined') {
        annotations.forEach(node => node.classList.add(DRAWN_CLASS));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, self) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add(DRAWN_CLASS);
                self.unobserve(entry.target);
            });
        },
        // A stroke that is only half on screen looks broken mid-draw, so wait
        // until most of the phrase is visible before starting.
        { threshold: 0.6, rootMargin: '0px 0px -5% 0px' }
    );

    annotations.forEach(node => observer.observe(node));
};

if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', draw);
} else {
    draw();
}
