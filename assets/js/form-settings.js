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

	document.addEventListener( 'DOMContentLoaded', function () {
		var buttons = document.querySelectorAll( '.dnte-copy-button' );

		buttons.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				var text  = button.getAttribute( 'data-copy' ) || '';
				var label = button.getAttribute( 'data-label' ) || 'Copy';
				var done  = button.getAttribute( 'data-copied' ) || '\u2713 Copied!';

				copyText( text ).then( function ( ok ) {
					if ( ! ok ) {
						// Last resort: let the user select the code manually.
						button.textContent = 'Press Ctrl+C to copy';
						setTimeout( function () {
							button.textContent = label;
						}, 2500 );
						return;
					}

					button.textContent = done;
					setTimeout( function () {
						button.textContent = label;
					}, 2000 );
				} );
			} );
		} );
	} );
}() );
