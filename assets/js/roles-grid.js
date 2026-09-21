/**
 * Open Roles job board — [opening_roles]
 *
 * Filters the rendered cards in the browser. Every active role is already in
 * the DOM, so the tabs and the search respond without a round trip.
 */
(function () {
    'use strict';

    function init(root) {
        var grid = root.querySelector('.dnte-jobs__grid');

        if (!grid) {
            return;
        }

        var cards = [].slice.call(grid.querySelectorAll('.dnte-job'));
        var tabs = [].slice.call(root.querySelectorAll('.dnte-jobs__tab'));
        var form = root.querySelector('.dnte-jobs__search');
        var empty = root.querySelector('.dnte-jobs__empty');

        var state = { type: '', keyword: '', location: '' };

        function matches(card) {
            if (state.type) {
                var types = (card.getAttribute('data-types') || '').split(/\s+/);
                if (types.indexOf(state.type) === -1) {
                    return false;
                }
            }

            if (state.keyword && (card.getAttribute('data-keywords') || '').indexOf(state.keyword) === -1) {
                return false;
            }

            if (state.location && (card.getAttribute('data-location') || '').indexOf(state.location) === -1) {
                return false;
            }

            return true;
        }

        function apply() {
            var shown = 0;

            cards.forEach(function (card) {
                var ok = matches(card);
                card.hidden = !ok;
                if (ok) {
                    shown++;
                }
            });

            if (empty) {
                empty.hidden = shown !== 0;
            }
        }

        var typeSelect = form ? form.querySelector('[data-filter="type"]') : null;

        /*
         * The tabs and the Job Type dropdown filter the same field, so both
         * are driven through here and neither can drift out of step with the
         * other.
         */
        function setType(slug) {
            state.type = slug || '';

            tabs.forEach(function (tab) {
                var active = (tab.getAttribute('data-type') || '') === state.type;
                tab.classList.toggle('is-active', active);
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            if (typeSelect && typeSelect.value !== state.type) {
                typeSelect.value = state.type;
            }

            apply();
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                setType(tab.getAttribute('data-type'));
            });
        });

        if (typeSelect) {
            typeSelect.addEventListener('change', function () {
                setType(typeSelect.value);
            });
        }

        if (form) {
            var read = function () {
                var keyword = form.querySelector('[data-filter="keyword"]');
                var location = form.querySelector('[data-filter="location"]');

                state.keyword = keyword ? keyword.value.trim().toLowerCase() : '';
                state.location = location ? location.value.trim().toLowerCase() : '';

                apply();
            };

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                read();
            });

            // Filter as you type too, so the button is a confirmation rather
            // than the only way to search. Only the text inputs are read here;
            // the Job Type select goes through setType.
            form.addEventListener('input', read);
        }

        apply();
    }

    function boot() {
        [].slice.call(document.querySelectorAll('[data-dnte-jobs]')).forEach(init);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
