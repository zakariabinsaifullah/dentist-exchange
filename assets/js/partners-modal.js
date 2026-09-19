/**
 * Partners Grid — detail modal for [partners_grid]
 *
 * Each Read More button carries data-hpg-open="<dialog id>". The dialogs are
 * native <dialog> elements, so Escape, the backdrop and focus containment are
 * handled by the browser; this only opens, closes and locks page scroll.
 */
( function () {
    'use strict';

    const LOCK_CLASS = 'hpg-modal-open';

    function open( dialog ) {
        if ( ! dialog ) return;

        // showModal is what puts the dialog in the top layer. Where it is
        // missing, the plain open attribute still reveals the details inline.
        if ( typeof dialog.showModal === 'function' ) {
            dialog.showModal();
        } else {
            dialog.setAttribute( 'open', '' );
        }

        document.body.classList.add( LOCK_CLASS );

        const closeBtn = dialog.querySelector( '.hpg-modal__close' );
        if ( closeBtn ) closeBtn.focus();
    }

    function close( dialog ) {
        if ( ! dialog ) return;

        if ( typeof dialog.close === 'function' ) {
            dialog.close();
        } else {
            dialog.removeAttribute( 'open' );
        }
    }

    document.addEventListener( 'click', function ( e ) {
        const trigger = e.target.closest( '[data-hpg-open]' );

        if ( trigger ) {
            open( document.getElementById( trigger.getAttribute( 'data-hpg-open' ) ) );
            return;
        }

        if ( e.target.closest( '[data-hpg-close]' ) ) {
            close( e.target.closest( '.hpg-modal' ) );
            return;
        }

        /*
         * A click on the backdrop lands on the <dialog> itself, since the
         * inner wrapper covers the whole of its box. Anything inside the
         * wrapper reports that wrapper as the target instead.
         */
        if ( e.target.classList.contains( 'hpg-modal' ) ) {
            close( e.target );
        }
    } );

    /*
     * close fires for the close button, the backdrop and the browser's own
     * Escape handling alike, so the scroll lock is released in one place.
     * Guarded against a second dialog still being open.
     */
    document.addEventListener( 'close', function ( e ) {
        if ( ! e.target.classList || ! e.target.classList.contains( 'hpg-modal' ) ) return;

        if ( ! document.querySelector( '.hpg-modal[open]' ) ) {
            document.body.classList.remove( LOCK_CLASS );
        }
    }, true );
} )();
