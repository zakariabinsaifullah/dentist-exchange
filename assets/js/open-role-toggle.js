/**
 * Active toggle on the Open Roles list table.
 *
 * The switch flips immediately for responsiveness and rolls back if the
 * request fails, so the UI never shows a state the database does not hold.
 */
(function () {
    'use strict';

    var config = window.hangOpenRole;

    if (!config) {
        return;
    }

    document.addEventListener('click', function (event) {
        var button = event.target.closest('.dnte-role-toggle');

        if (!button || button.disabled || button.classList.contains('is-busy')) {
            return;
        }

        var previous = button.getAttribute('aria-checked') === 'true';
        var next = !previous;

        // Optimistic flip.
        button.setAttribute('aria-checked', next ? 'true' : 'false');
        button.classList.add('is-busy');

        var body = new URLSearchParams();
        body.append('action', 'dnte_toggle_open_role_active');
        body.append('nonce', config.nonce);
        body.append('post_id', button.dataset.id);
        body.append('active', next ? '1' : '0');

        window
            .fetch(config.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: body
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (!result || !result.success) {
                    throw new Error('Request rejected');
                }

                // Trust the server's answer over the optimistic guess.
                button.setAttribute('aria-checked', result.data.active ? 'true' : 'false');
            })
            .catch(function () {
                button.setAttribute('aria-checked', previous ? 'true' : 'false');
                window.alert('Could not update the role. Please reload and try again.');
            })
            .finally(function () {
                button.classList.remove('is-busy');
            });
    });
})();
