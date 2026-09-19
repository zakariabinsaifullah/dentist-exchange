( function () {
	'use strict';

	/**
	 * Copy text to the clipboard, falling back to a hidden textarea +
	 * execCommand for non-secure contexts (http, iframes).
	 *
	 * @param {string} text Text to copy.
	 * @return {Promise<boolean>} Resolves true when the copy succeeded.
	 */
	function copyText( text ) {
		if ( navigator.clipboard && window.isSecureContext ) {
			return navigator.clipboard.writeText( text ).then(
				function () { return true; },
				function () { return fallbackCopy( text ); }
			);
		}
		return Promise.resolve( fallbackCopy( text ) );
	}

	function fallbackCopy( text ) {
		var textarea = document.createElement( 'textarea' );
		textarea.value = text;
		textarea.setAttribute( 'readonly', '' );
		textarea.style.position = 'fixed';
		textarea.style.top = '-9999px';
		document.body.appendChild( textarea );
		textarea.select();
		textarea.setSelectionRange( 0, textarea.value.length );

		var ok = false;
		try {
			ok = document.execCommand( 'copy' );
		} catch ( e ) {
			ok = false;
		}
		document.body.removeChild( textarea );
		return ok;
	}

	var COPY_ICON = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
	var CHECK_ICON = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';

	function bindButton( btn ) {
		btn.addEventListener( 'click', function () {
			var text = btn.getAttribute( 'data-code' ) || btn.getAttribute( 'data-clipboard' ) || '';

			copyText( text ).then( function ( ok ) {
				if ( ! ok ) {
					return;
				}

				btn.classList.add( 'copied' );
				btn.innerHTML = CHECK_ICON + ' Copied!';
				setTimeout( function () {
					btn.classList.remove( 'copied' );
					btn.innerHTML = COPY_ICON + ' Copy';
				}, 2000 );
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		document.querySelectorAll( '.psr-copy-btn, .dnte-copy__btn' ).forEach( bindButton );
	} );
}() );
