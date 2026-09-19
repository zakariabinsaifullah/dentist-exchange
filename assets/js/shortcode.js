( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {

		// ── Shared: initialise AJAX fetch for a grid wrapper ─────────────────
		function initGrid( wrapper ) {
			var config       = JSON.parse( wrapper.dataset.config || '{}' );
			var postsEl      = wrapper.querySelector( '.ipg-posts' );
			var paginationEl = wrapper.querySelector( '.ipg-pagination-wrap' );
			var state        = { cat: 0, page: 1 };
			var loading      = false;

			function fetchPosts() {
				if ( loading ) return;
				loading = true;
				wrapper.classList.add( 'is-loading' );

				var body = new URLSearchParams( {
					action:     'dnte_posts_grid',
					nonce:      config.nonce,
					cat:        state.cat,
					page:       state.page,
					per_page:   config.perPage   || 6,
					post_type:  config.postType  || 'post',
					taxonomy:   config.taxonomy  || 'category',
					categories: config.categories || '',
				} );

				fetch( config.ajaxUrl, { method: 'POST', body: body } )
					.then( function ( r ) { return r.json(); } )
					.then( function ( data ) {
						if ( data.success ) {
							postsEl.innerHTML      = data.data.html;
							paginationEl.innerHTML = data.data.pagination;
							bindPagination();
						}
					} )
					.finally( function () {
						loading = false;
						wrapper.classList.remove( 'is-loading' );
					} );
			}

			function bindPagination() {
				paginationEl.querySelectorAll( '.ipg-page-btn' ).forEach( function ( btn ) {
					btn.addEventListener( 'click', function () {
						state.page = parseInt( this.dataset.page, 10 );
						fetchPosts();
						wrapper.scrollIntoView( { behavior: 'smooth', block: 'start' } );
					} );
				} );
			}

			bindPagination();

			return { state: state, fetch: fetchPosts };
		}


		// ── Self-contained grids (tabs embedded, no data-grid-id) ────────────
		document.querySelectorAll( '.ipg-wrapper:not([data-grid-id])' ).forEach( function ( wrapper ) {
			var grid = initGrid( wrapper );

			wrapper.querySelectorAll( '.ipg-filter-btn' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					wrapper.querySelectorAll( '.ipg-filter-btn' ).forEach( function ( b ) {
						b.classList.remove( 'active' );
					} );
					this.classList.add( 'active' );
					grid.state.cat  = parseInt( this.dataset.cat || '0', 10 );
					grid.state.page = 1;
					grid.fetch();
				} );
			} );
		} );


		// ── Remote grids (listen for dnte:filter CustomEvent) ───────────────
		document.querySelectorAll( '.ipg-wrapper[data-grid-id]' ).forEach( function ( wrapper ) {
			var gridId = wrapper.dataset.gridId;
			var grid   = initGrid( wrapper );

			document.addEventListener( 'dnte:filter', function ( e ) {
				if ( e.detail.id !== gridId ) return;
				grid.state.cat  = e.detail.cat;
				grid.state.page = 1;
				grid.fetch();
			} );
		} );


		// ── Remote tabs (fire dnte:filter CustomEvent) ───────────────────────
		document.querySelectorAll( '.ipg-tabs-remote' ).forEach( function ( tabsEl ) {
			var forId = tabsEl.dataset.for;

			tabsEl.querySelectorAll( '.ipg-filter-btn' ).forEach( function ( btn ) {
				btn.addEventListener( 'click', function () {
					tabsEl.querySelectorAll( '.ipg-filter-btn' ).forEach( function ( b ) {
						b.classList.remove( 'active' );
					} );
					this.classList.add( 'active' );

					document.dispatchEvent( new CustomEvent( 'dnte:filter', {
						detail: {
							id:  forId,
							cat: parseInt( this.dataset.cat || '0', 10 ),
						},
					} ) );
				} );
			} );
		} );

	} );
}() );
