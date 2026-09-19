( function () {
    'use strict';

    const PANEL_ID   = 'dnte-contact';
    const OVERLAY_ID = 'dnte-form-overlay';
    const OPEN_CLASS = 'is-open';
    const LOCK_CLASS = 'dnte-panel-open';

    let lastTrigger = null;

    function panel()   { return document.getElementById( PANEL_ID ); }
    function overlay() { return document.getElementById( OVERLAY_ID ); }

    function openPanel() {
        const p = panel(), o = overlay();
        if ( ! p || ! o ) return;

        p.classList.add( OPEN_CLASS );
        o.classList.add( OPEN_CLASS );
        document.body.classList.add( LOCK_CLASS );
        p.setAttribute( 'aria-hidden', 'false' );

        const closeBtn = p.querySelector( '.dnte-form-panel__close' );
        if ( closeBtn ) closeBtn.focus();
    }

    function closePanel() {
        const p = panel(), o = overlay();
        if ( ! p || ! o ) return;

        p.classList.remove( OPEN_CLASS );
        o.classList.remove( OPEN_CLASS );
        document.body.classList.remove( LOCK_CLASS );
        p.setAttribute( 'aria-hidden', 'true' );

        if ( lastTrigger ) lastTrigger.focus();
        lastTrigger = null;
    }

    // Trigger: <a href="#dnte-contact"> or [data-open="dnte-contact"]
    document.addEventListener( 'click', function ( e ) {
        const trigger = e.target.closest( 'a[href="#' + PANEL_ID + '"], [data-open="' + PANEL_ID + '"]' );

        if ( trigger ) {
            e.preventDefault();
            lastTrigger = trigger;
            openPanel();
            return;
        }

        // Close button inside panel
        if ( e.target.closest( '.dnte-form-panel__close' ) ) {
            closePanel();
            return;
        }

        // Click on overlay
        const o = overlay();
        if ( o && e.target === o ) {
            closePanel();
        }
    } );

    // Escape key
    document.addEventListener( 'keydown', function ( e ) {
        const p = panel();
        if ( e.key === 'Escape' && p && p.classList.contains( OPEN_CLASS ) ) {
            closePanel();
        }
    } );
} )();
