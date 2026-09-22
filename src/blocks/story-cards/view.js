/**
 * Story Cards — absolute layout and scroll reveal.
 *
 * LAYOUT. The design's vertical rhythm is absolute: each card's top is a fixed
 * share of the section's width (its `--dnte-card-top`), independent of how
 * tall any other card renders. CSS alone cannot finish the job — the section
 * must be exactly as tall as its lowest card, and absolutely positioned
 * children contribute no height — so this script adds `.is-positioned`, lets
 * the stylesheet place the cards, and sets the section's height itself. A card
 * without an explicit top is placed just below the card before it. Below 900px
 * the class is dropped and the cards stack as a plain column.
 *
 * REVEAL. Each card fades and rises into place as it enters the viewport, its
 * connector arrow wiping in just behind it. Once per card, top to bottom.
 * Nothing is hidden unless this script runs (`.is-animated` gates the initial
 * state), and order is enforced through a queue — the lanes overlap, so a fast
 * scroll can hand the observer several cards in one callback.
 */
(function () {
    'use strict';

    var SECTION = '.dnte-story-cards';
    var CARD = '.dnte-story-card';

    /* Matches the brief: a card counts as arrived once ~30% of it is on
     * screen. */
    var THRESHOLD = 0.3;
    var DEFAULT_STAGGER = 120;

    var NARROW = window.matchMedia('(max-width: 900px)');

    /* ── Layout ───────────────────────────────────────────────────────────── */

    function layout(section, cards) {
        if (NARROW.matches) {
            section.classList.remove('is-positioned');
            section.style.height = '';
            cards.forEach(function (card) {
                card.style.marginTop = '';
            });

            var flowTrailing = section.querySelector('.dnte-story-cards__trailing');
            if (flowTrailing) {
                flowTrailing.style.top = '';
            }
            return;
        }

        section.classList.add('is-positioned');

        /*
         * Cards are measured and placed in order: an explicit top comes from
         * the card's own --dnte-card-top (already applied by the stylesheet);
         * a card without one is dropped just below the lowest card so far.
         * Reading each rect right after writing the previous margin forces a
         * reflow per card, but the section holds a handful of cards, not
         * hundreds.
         */
        var sectionTop = section.getBoundingClientRect().top;
        var lowest = 0;

        cards.forEach(function (card) {
            if (card.style.getPropertyValue('--dnte-card-top')) {
                card.style.marginTop = '';
            } else {
                card.style.marginTop = Math.round(lowest) + 'px';
            }

            var rect = card.getBoundingClientRect();
            lowest = Math.max(lowest, rect.bottom - sectionTop);
        });

        /*
         * Absolutely positioned children give the section no height of its
         * own, so it is sized here. The lowest card also anchors the trailing
         * arrow's `top: 100%` — and since that curve then hangs below the
         * cards, the section grows to cover it too, or it would run over
         * whatever content follows.
         */
        var trailing = section.querySelector('.dnte-story-cards__trailing');

        if (trailing) {
            /*
             * Anchored to the lowest card rather than the stylesheet's
             * `top: 100%`: the section is about to be sized to cover the
             * curve, and an anchor at the section's own bottom would move
             * every time the height does — a circle that never contains it.
             */
            trailing.style.top = Math.round(lowest) + 'px';
            lowest = Math.max(lowest, trailing.getBoundingClientRect().bottom - sectionTop);
        }

        section.style.height = Math.ceil(lowest) + 'px';
    }

    function watchLayout(section, cards) {
        layout(section, cards);

        var timer = null;
        var relayout = function () {
            if (timer) {
                return;
            }
            /*
             * setTimeout rather than requestAnimationFrame: rAF is suspended
             * in hidden tabs, which would leave a resize that happened in the
             * background unapplied until the next repaint after focus.
             */
            timer = window.setTimeout(function () {
                timer = null;
                layout(section, cards);
            }, 32);
        };

        /*
         * Width changes move every percentage top; a card's own height changes
         * when its copy wraps differently or webfonts arrive. Observing both
         * covers resize, zoom and late-loading content without polling. The
         * observer also fires for the section height set by layout() itself,
         * but the recomputation lands on the same number, so it settles after
         * one extra frame instead of looping.
         */
        if ('ResizeObserver' in window) {
            var observer = new ResizeObserver(relayout);
            observer.observe(section);
            cards.forEach(function (card) {
                observer.observe(card);
            });
        } else {
            window.addEventListener('resize', relayout);
        }

        if (NARROW.addEventListener) {
            NARROW.addEventListener('change', relayout);
        }
    }

    /* ── Reveal ───────────────────────────────────────────────────────────── */

    function setupReveal(section, cards) {
        if (section.dataset.animate === 'false') {
            return;
        }

        // From here on the CSS may hide things, because we are certain we can
        // show them again.
        section.classList.add('is-animated');

        var stagger = parseInt(section.dataset.stagger, 10);
        if (isNaN(stagger) || stagger < 0) {
            stagger = DEFAULT_STAGGER;
        }

        var queue = [];
        var lastRevealAt = 0;
        var timer = null;

        function revealTrailing() {
            var trailing = section.querySelector('.dnte-story-cards__trailing');

            if (!trailing) {
                return;
            }

            var art = trailing.querySelector('.dnte-story-card__connector-art');
            var delay = parseInt(trailing.getAttribute('data-arrow-delay'), 10);

            if (art && !isNaN(delay)) {
                art.style.transitionDelay = delay + 'ms';
            }

            section.classList.add('is-trailing-revealed');
        }

        function reveal(card) {
            /*
             * The arrow belongs to the card but trails it, so its delay lives
             * on the connector and is applied here rather than in CSS — the
             * card's own transition must not inherit it. Set before the class,
             * so the delay is in place for the transition it governs.
             */
            var connector = card.querySelector('.dnte-story-card__connector');
            var art = card.querySelector('.dnte-story-card__connector-art');

            if (connector && art) {
                var delay = parseInt(connector.getAttribute('data-arrow-delay'), 10);

                if (!isNaN(delay)) {
                    art.style.transitionDelay = delay + 'ms';
                }
            }

            card.classList.add('is-revealed');

            /*
             * The section's trailing curve has no card of its own, so it
             * follows the last one — on a phone that is the only arrow left,
             * and it still has to draw rather than simply appear.
             */
            if (card === cards[cards.length - 1]) {
                revealTrailing();
            }
        }

        function drain() {
            timer = null;

            if (!queue.length) {
                return;
            }

            // DOM order, so cards always arrive top to bottom however they
            // happened to enter the viewport.
            queue.sort(function (a, b) {
                return cards.indexOf(a) - cards.indexOf(b);
            });

            var now = Date.now();
            var wait = lastRevealAt + stagger - now;

            if (wait > 0) {
                timer = window.setTimeout(drain, wait);
                return;
            }

            reveal(queue.shift());
            lastRevealAt = now;

            if (queue.length) {
                timer = window.setTimeout(drain, stagger);
            }
        }

        function enqueue(card) {
            if (card.classList.contains('is-revealed') || queue.indexOf(card) !== -1) {
                return;
            }

            queue.push(card);

            if (!timer) {
                drain();
            }
        }

        // No IntersectionObserver (or no JS support for it): show everything
        // rather than leave the section blank.
        if (!('IntersectionObserver' in window)) {
            cards.forEach(reveal);
            return;
        }

        var observer = new IntersectionObserver(
            function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    /*
                     * A card taller than the viewport can never reach 30% of
                     * *itself* on screen, and would sit unrevealed forever. So
                     * either measure counts: 30% of the card, or enough of it
                     * to fill 30% of the screen.
                     */
                    var enough = entry.intersectionRatio >= THRESHOLD || entry.intersectionRect.height >= window.innerHeight * THRESHOLD;

                    if (!enough) {
                        return;
                    }

                    // Runs once per card.
                    observer.unobserve(entry.target);
                    enqueue(entry.target);
                });
            },
            // The 0 stop is what lets the tall-card case above be evaluated at
            // all; without it the callback never fires for one.
            { threshold: [0, THRESHOLD] }
        );

        cards.forEach(function (card) {
            observer.observe(card);
        });
    }

    /* ── Boot ─────────────────────────────────────────────────────────────── */

    function setup(section) {
        var cards = [].slice.call(section.querySelectorAll(CARD));

        if (!cards.length) {
            return;
        }

        // Layout first: the reveal observer measures visibility, and a card is
        // only where the design wants it once it has been positioned.
        watchLayout(section, cards);
        setupReveal(section, cards);
    }

    function boot() {
        [].slice.call(document.querySelectorAll(SECTION)).forEach(setup);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
