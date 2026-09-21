/**
 * Open Role — Symbolic Icon picker.
 *
 * Opens the media frame, stores the attachment ID in a hidden input and keeps
 * the preview in step. Accepts anything the library will serve as an image,
 * including SVG where the upload filter allows it.
 */
(function ($) {
    'use strict';

    $(function () {
        var $input = $('#dnte-role-icon');

        if (!$input.length) {
            return;
        }

        var $preview = $('.dnte-role-icon-preview');
        var $select = $('.dnte-role-icon-select');
        var $remove = $('.dnte-role-icon-remove');
        var frame;

        $select.on('click', function (e) {
            e.preventDefault();

            if (frame) {
                frame.open();
                return;
            }

            frame = wp.media({
                title: $select.data('title') || 'Select icon',
                button: { text: 'Use this icon' },
                library: { type: 'image' },
                multiple: false
            });

            frame.on('select', function () {
                var att = frame.state().get('selection').first().toJSON();

                $input.val(att.id);
                $preview.html($('<img>', { src: att.url, alt: '', css: { maxWidth: '48px', maxHeight: '48px' } }));
                $remove.show();
                $select.text($select.data('replace') || 'Replace Icon');
            });

            frame.open();
        });

        $remove.on('click', function (e) {
            e.preventDefault();
            $input.val('');
            $preview.empty();
            $remove.hide();
            $select.text($select.data('upload') || 'Upload Icon');
        });
    });
})(jQuery);
