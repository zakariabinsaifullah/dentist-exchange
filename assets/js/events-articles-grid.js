/**
 * Articles & Events Grid — [events_articles_grid]
 *
 * Narrows the Category dropdown to the vocabularies belonging to the selected
 * Type, the moment Type changes.
 *
 * The markup ships every term tagged with the post types its taxonomy is
 * attached to, so this only ever removes options — it never has to fetch. The
 * server still validates the submitted pair, so a reader without JavaScript
 * sees the full list but cannot produce a nonsense result.
 */
document.addEventListener('DOMContentLoaded', function () {
    var forms = document.querySelectorAll('.eag-filters');

    if (!forms.length) {
        return;
    }

    Array.prototype.forEach.call(forms, function (form) {
        var typeSelect = form.querySelector('select[name="ev_type"]');
        var termSelect = form.querySelector('select[data-eag-term]');

        // With a single post type there is no Type control to react to.
        if (!typeSelect || !termSelect) {
            return;
        }

        var placeholder = termSelect.querySelector('option[value=""]');
        var placeholderText = placeholder ? placeholder.textContent : '';

        /**
         * Snapshot the full list before anything is removed from the DOM —
         * rebuilding from this each time means switching Type back and forth
         * cannot progressively eat the options.
         */
        var items = [];

        Array.prototype.forEach.call(termSelect.querySelectorAll('option'), function (option) {
            if (!option.value) {
                return;
            }

            var group = option.parentNode && 'OPTGROUP' === option.parentNode.tagName ? option.parentNode.label : '';

            items.push({
                value: option.value,
                text: option.textContent,
                group: group,
                types: (option.getAttribute('data-types') || '').split(' ').filter(Boolean)
            });
        });

        if (!items.length) {
            return;
        }

        var buildOption = function (item) {
            var option = document.createElement('option');
            option.value = item.value;
            option.textContent = item.text;
            option.setAttribute('data-types', item.types.join(' '));
            return option;
        };

        var render = function () {
            // Remember the selection so it survives a rebuild that still offers it.
            var previous = termSelect.value;
            var type = typeSelect.value;

            var keep = items.filter(function (item) {
                return !type || item.types.indexOf(type) !== -1;
            });

            var groups = [];

            keep.forEach(function (item) {
                if (item.group && groups.indexOf(item.group) === -1) {
                    groups.push(item.group);
                }
            });

            termSelect.innerHTML = '';

            if (placeholder) {
                var blank = document.createElement('option');
                blank.value = '';
                blank.textContent = placeholderText;
                termSelect.appendChild(blank);
            }

            // One vocabulary left means the group heading is just noise.
            if (groups.length > 1) {
                groups.forEach(function (label) {
                    var optgroup = document.createElement('optgroup');
                    optgroup.label = label;

                    keep.forEach(function (item) {
                        if (item.group === label) {
                            optgroup.appendChild(buildOption(item));
                        }
                    });

                    termSelect.appendChild(optgroup);
                });
            } else {
                keep.forEach(function (item) {
                    termSelect.appendChild(buildOption(item));
                });
            }

            var survived = keep.some(function (item) {
                return item.value === previous;
            });

            termSelect.value = survived ? previous : '';
        };

        typeSelect.addEventListener('change', render);

        // Apply the current Type on load, so a filtered URL arrives consistent.
        render();
    });
});
