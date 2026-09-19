<?php
/**
 * Articles & Events Grid Shortcode
 *
 * Posts and Events together in one paginated three-column grid, with a filter
 * bar above it: a Type dropdown (Articles / Events) plus one dropdown per
 * taxonomy shared by both post types.
 *
 * Filtering and paging run through query arguments rather than AJAX, so a
 * filtered view has a URL that can be shared, bookmarked and crawled, and the
 * back button behaves. Every parameter is namespaced `ev_` to stay clear of the
 * main query — notably `ev_page` rather than `paged`, which WordPress owns and
 * which would otherwise fight the page's own pagination.
 *
 * Usage: [events_articles_grid columns="3" per_page="9"]
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types the grid covers, in the order their Type dropdown lists them.
 */
if ( ! function_exists( 'dnte_eag_post_types' ) ) :
	/**
	 * @return array<string,string> Post type => Type dropdown label.
	 */
	function dnte_eag_post_types() {
		$types = array( 'post' => __( 'Articles', 'dentist-exchange' ) );

		if ( post_type_exists( 'event' ) ) {
			$types['event'] = __( 'Events', 'dentist-exchange' );
		}

		/**
		 * Filters the post types offered by the Articles & Events grid.
		 *
		 * @param array<string,string> $types Post type => label.
		 */
		return apply_filters( 'dnte_eag_post_types', $types );
	}
endif;


// =============================================================================
// Assets
// =============================================================================

if ( ! function_exists( 'dnte_eag_enqueue_assets' ) ) :
	/**
	 * Loads the grid stylesheet. Called only when the shortcode renders.
	 */
	function dnte_eag_enqueue_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'dnte-events-articles-grid',
			get_theme_file_uri( 'assets/css/events-articles-grid.css' ),
			array(),
			$version
		);

		// Narrows the Category dropdown as Type changes; the grid works without it.
		wp_enqueue_script(
			'dnte-events-articles-grid',
			get_theme_file_uri( 'assets/js/events-articles-grid.js' ),
			array(),
			$version,
			true
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'dnte_eag_filter_taxonomies' ) ) :
	/**
	 * The taxonomies whose terms populate the category dropdown.
	 *
	 * Each post type brings its own vocabulary — Posts have Categories, Events
	 * have Types — so this is the union across the types being shown, narrowed
	 * to whichever type the reader has picked. Only hierarchical taxonomies
	 * qualify: those are the subject vocabularies, whereas free-form ones like
	 * Tags and the internal `post_format` are not what this filter is for.
	 *
	 * @param array  $post_types Post types being queried.
	 * @param string $explicit   Comma-separated taxonomy names from the shortcode.
	 * @return WP_Taxonomy[]
	 */
	function dnte_eag_filter_taxonomies( $post_types, $explicit = '' ) {
		$names = array_filter( array_map( 'trim', explode( ',', (string) $explicit ) ), 'strlen' );

		if ( ! $names ) {
			$names = array();

			foreach ( $post_types as $post_type ) {
				foreach ( get_object_taxonomies( $post_type ) as $name ) {
					$names[ $name ] = $name;
				}
			}
		}

		$taxonomies = array();

		foreach ( $names as $name ) {
			$taxonomy = get_taxonomy( $name );

			if ( ! $taxonomy || ! $taxonomy->public || ! $taxonomy->hierarchical || 'post_format' === $name ) {
				continue;
			}

			$taxonomies[] = $taxonomy;
		}

		return $taxonomies;
	}
endif;


if ( ! function_exists( 'dnte_eag_parse_term' ) ) :
	/**
	 * Splits the `ev_term` argument into a taxonomy and term slug.
	 *
	 * One dropdown mixes terms from several taxonomies, so the value has to say
	 * which vocabulary it came from: `category:tax-services`. Anything that does
	 * not resolve to a real term in an offered taxonomy is discarded, so a
	 * hand-edited URL degrades to "no filter" rather than an empty grid.
	 *
	 * @param string        $value      Raw `ev_term` value.
	 * @param WP_Taxonomy[] $taxonomies Taxonomies currently on offer.
	 * @return array{taxonomy:string,slug:string}|null
	 */
	function dnte_eag_parse_term( $value, $taxonomies ) {
		if ( ! $value || false === strpos( $value, ':' ) ) {
			return null;
		}

		list( $taxonomy, $slug ) = explode( ':', $value, 2 );

		$taxonomy = sanitize_key( $taxonomy );
		$slug     = sanitize_title( $slug );

		if ( ! $taxonomy || ! $slug ) {
			return null;
		}

		$offered = wp_list_pluck( $taxonomies, 'name' );

		if ( ! in_array( $taxonomy, $offered, true ) ) {
			return null;
		}

		if ( ! term_exists( $slug, $taxonomy ) ) {
			return null;
		}

		return array(
			'taxonomy' => $taxonomy,
			'slug'     => $slug,
		);
	}
endif;


if ( ! function_exists( 'dnte_eag_badge_label' ) ) :
	/**
	 * The card's badge: the event's Type term, or plain "Article" for a post.
	 *
	 * @param int    $post_id   Post ID.
	 * @param string $post_type Post type.
	 * @return string
	 */
	function dnte_eag_badge_label( $post_id, $post_type ) {
		if ( 'event' === $post_type ) {
			$terms = get_the_terms( $post_id, 'event-type' );

			if ( $terms && ! is_wp_error( $terms ) ) {
				return $terms[0]->name;
			}

			return __( 'Event', 'dentist-exchange' );
		}

		return __( 'Article', 'dentist-exchange' );
	}
endif;


if ( ! function_exists( 'dnte_eag_render_card' ) ) :
	/**
	 * Renders one card: badge → title → summary → Learn More.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	function dnte_eag_render_card( $post_id ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return '';
		}

		$permalink = get_permalink( $post_id );
		$summary   = dnte_insights_get_summary( $post, 20 );
		$badge     = dnte_eag_badge_label( $post_id, $post->post_type );

		$html  = '<article class="eag-card">';
		$html .= '<span class="eag-card__badge">' . esc_html( $badge ) . '</span>';
		$html .= '<h3 class="eag-card__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a></h3>';

		if ( $summary ) {
			$html .= '<p class="eag-card__excerpt">' . esc_html( $summary ) . '</p>';
		}

		$html .= '<a class="eag-card__more" href="' . esc_url( $permalink ) . '">';
		$html .= '<span>' . esc_html__( 'Learn More', 'dentist-exchange' ) . '</span>';
		$html .= dnte_insights_arrow_svg();
		$html .= '</a>';

		$html .= '</article>';

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_eag_render_filters' ) ) :
	/**
	 * The filter bar: a GET form of native selects plus a submit button.
	 *
	 * Native `<select>` rather than a scripted dropdown so the control is
	 * keyboard operable, announced correctly, and uses the platform picker on
	 * touch devices. Existing query arguments that the form does not own are
	 * carried through as hidden inputs, since a GET form replaces the whole
	 * query string on submit and would otherwise drop them.
	 *
	 * @param WP_Taxonomy[] $taxonomies Taxonomies to offer.
	 * @param array         $post_types Post types in the grid.
	 * @param array         $selected   Current selections.
	 * @param string        $anchor     Section id, so submitting returns to the grid.
	 * @return string
	 */
	function dnte_eag_render_filters( $taxonomies, $post_types, $selected, $anchor ) {
		$type_labels = dnte_eag_post_types();
		$owned       = array( 'ev_type', 'ev_term', 'ev_page' );

		// Submitting a GET form replaces the query string but keeps the action's
		// fragment, which lands the reader back at the grid rather than the top.
		$base   = remove_query_arg( $owned );
		$action = $base . ( $anchor ? '#' . $anchor : '' );

		$html = '<form class="eag-filters" method="get" action="' . esc_url( $action ) . '">';

		// Preserve unrelated query arguments across the submit.
		foreach ( $_GET as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$key = sanitize_key( $key );

			if ( in_array( $key, $owned, true ) || ! is_scalar( $value ) ) {
				continue;
			}

			$html .= '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( wp_unslash( $value ) ) . '">';
		}

		// ── Type ───────────────────────────────────────────────────────────────
		if ( count( $post_types ) > 1 ) {
			$html .= '<label class="eag-filters__field">';
			$html .= '<span class="screen-reader-text">' . esc_html__( 'Type', 'dentist-exchange' ) . '</span>';
			$html .= '<select name="ev_type">';
			$html .= '<option value="">' . esc_html__( 'Type', 'dentist-exchange' ) . '</option>';

			foreach ( $post_types as $post_type ) {
				$label = isset( $type_labels[ $post_type ] ) ? $type_labels[ $post_type ] : $post_type;
				$html .= '<option value="' . esc_attr( $post_type ) . '"' . selected( $selected['type'], $post_type, false ) . '>' . esc_html( $label ) . '</option>';
			}

			$html .= '</select></label>';
		}

		// ── Category ───────────────────────────────────────────────────────────
		/*
		 * One dropdown covering every offered taxonomy. Each option carries its
		 * taxonomy in the value, and the groups are labelled so a reader can see
		 * that "Webinar" is an Event Type while "Tax" is a Category.
		 *
		 * The whole set is rendered, tagged with the post types each vocabulary
		 * belongs to, and the companion script narrows it the moment Type
		 * changes. Scoping this list server-side instead would only take effect
		 * after a submit, leaving Event Types and post Categories side by side
		 * in the dropdown while the reader is still choosing.
		 */
		$groups  = array();
		$current = $selected['term'] ? $selected['term']['taxonomy'] . ':' . $selected['term']['slug'] : '';

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy->name,
					'hide_empty' => true,
				)
			);

			if ( is_wp_error( $terms ) || ! $terms ) {
				continue;
			}

			$groups[] = array(
				'label' => $taxonomy->label,
				'types' => array_values( array_intersect( (array) $taxonomy->object_type, $post_types ) ),
				'terms' => $terms,
			);
		}

		if ( $groups ) {
			$html .= '<label class="eag-filters__field">';
			$html .= '<span class="screen-reader-text">' . esc_html__( 'Category', 'dentist-exchange' ) . '</span>';
			$html .= '<select name="ev_term" data-eag-term>';
			$html .= '<option value="">' . esc_html__( 'Category', 'dentist-exchange' ) . '</option>';

			foreach ( $groups as $group ) {
				// Grouping only earns its keep when more than one vocabulary shows.
				$grouped = count( $groups ) > 1;
				$types   = esc_attr( implode( ' ', $group['types'] ) );

				if ( $grouped ) {
					$html .= '<optgroup label="' . esc_attr( $group['label'] ) . '" data-types="' . $types . '">';
				}

				foreach ( $group['terms'] as $term ) {
					$value = $term->taxonomy . ':' . $term->slug;
					$html .= '<option value="' . esc_attr( $value ) . '" data-types="' . $types . '"' . selected( $current, $value, false ) . '>' . esc_html( $term->name ) . '</option>';
				}

				if ( $grouped ) {
					$html .= '</optgroup>';
				}
			}

			$html .= '</select></label>';
		}

		$html .= '<button type="submit" class="eag-filters__submit">' . esc_html__( 'Submit', 'dentist-exchange' ) . '</button>';

		/*
		 * Clearing is a link back to the unfiltered URL rather than a reset
		 * button: resetting the form would only restore the selects, leaving the
		 * page still showing filtered results until the reader submitted again.
		 */
		if ( $selected['type'] || $selected['term'] ) {
			$clear = $base . ( $anchor ? '#' . $anchor : '' );

			$html .= '<a class="eag-filters__clear" href="' . esc_url( $clear ) . '">';
			$html .= '<span class="screen-reader-text">' . esc_html__( 'Clear filters', 'dentist-exchange' ) . '</span>';
			$html .= '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6"'
				. ' stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M3 3l8 8M11 3l-8 8"/></svg>';
			$html .= '</a>';
		}

		$html .= '</form>';

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_eag_render_pagination' ) ) :
	/**
	 * Numbered pagination with always-present prev/next controls.
	 *
	 * `paginate_links` supplies the numbers and the ellipsis logic, but it drops
	 * the prev link on the first page and next on the last. The design keeps
	 * both visible in a disabled state, so they are rendered here instead.
	 *
	 * @param int    $current Current page.
	 * @param int    $total   Total pages.
	 * @param string $anchor  Section id to jump back to.
	 * @return string
	 */
	function dnte_eag_render_pagination( $current, $total, $anchor ) {
		if ( $total < 2 ) {
			return '';
		}

		$fragment = $anchor ? '#' . $anchor : '';

		// `add_query_arg` does not encode values, so the %#% token survives for
		// paginate_links to substitute — and the current filters ride along.
		$base = add_query_arg( 'ev_page', '%#%' );

		$numbers = paginate_links(
			array(
				'base'         => $base,
				'format'       => '',
				'current'      => $current,
				'total'        => $total,
				'type'         => 'array',
				'mid_size'     => 1,
				'end_size'     => 2,
				'prev_next'    => false,
				'add_fragment' => $fragment,
			)
		);

		$arrow = static function ( $direction ) {
			$path = 'prev' === $direction ? 'M10 12 6 8l4-4' : 'M6 4l4 4-4 4';

			return '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor"'
				. ' stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
				. '<path d="' . $path . '"/></svg>';
		};

		$step = static function ( $page, $direction, $label ) use ( $arrow, $fragment ) {
			$url = add_query_arg( 'ev_page', $page ) . $fragment;

			return '<a class="eag-pagination__step" href="' . esc_url( $url ) . '" aria-label="' . esc_attr( $label ) . '">' . $arrow( $direction ) . '</a>';
		};

		$html = '<nav class="eag-pagination" aria-label="' . esc_attr__( 'Articles and events pagination', 'dentist-exchange' ) . '">';

		$html .= $current > 1
			? $step( $current - 1, 'prev', __( 'Previous page', 'dentist-exchange' ) )
			: '<span class="eag-pagination__step is-disabled" aria-hidden="true">' . $arrow( 'prev' ) . '</span>';

		$html .= '<span class="eag-pagination__numbers">' . implode( '', (array) $numbers ) . '</span>';

		$html .= $current < $total
			? $step( $current + 1, 'next', __( 'Next page', 'dentist-exchange' ) )
			: '<span class="eag-pagination__step is-disabled" aria-hidden="true">' . $arrow( 'next' ) . '</span>';

		$html .= '</nav>';

		return $html;
	}
endif;


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_events_articles_grid_shortcode' ) ) :
	/**
	 * [events_articles_grid columns="3" per_page="9" post_types="post,event" taxonomies="" filters="yes" order="DESC" orderby="date"]
	 *
	 * `columns`    — grid columns on desktop, 1-4 (default 3).
	 * `per_page`   — cards per page, 1-48 (default 9).
	 * `post_types` — comma-separated post types (default post,event).
	 * `taxonomies` — comma-separated taxonomies for the filter bar; omit to use
	 *                every public taxonomy shared by all the post types.
	 * `filters`    — yes/no, show the filter bar (default yes).
	 * `order`      — ASC or DESC (default DESC).
	 * `orderby`    — any WP_Query orderby value (default date).
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	function dnte_events_articles_grid_shortcode( $atts ) {
		static $instance = 0;
		++$instance;

		$atts = shortcode_atts(
			array(
				'columns'    => 3,
				'per_page'   => 9,
				'post_types' => '',
				'taxonomies' => '',
				'filters'    => 'yes',
				'order'      => 'DESC',
				'orderby'    => 'date',
			),
			$atts,
			'events_articles_grid'
		);

		$available = array_keys( dnte_eag_post_types() );

		$requested = array_filter( array_map( 'trim', explode( ',', (string) $atts['post_types'] ) ), 'strlen' );
		$post_types = $requested ? array_values( array_intersect( $requested, $available ) ) : $available;

		if ( ! $post_types ) {
			return '';
		}

		$columns  = min( 4, max( 1, (int) $atts['columns'] ) );
		$per_page = min( 48, max( 1, (int) $atts['per_page'] ) );
		$order    = 'ASC' === strtoupper( (string) $atts['order'] ) ? 'ASC' : 'DESC';
		$anchor   = 'events-articles-' . $instance;

		// ── Read the filter state off the query string ─────────────────────────
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Public, read-only filtering.
		$selected = array(
			'type' => '',
			'term' => null,
		);

		if ( isset( $_GET['ev_type'] ) ) {
			$candidate = sanitize_key( wp_unslash( $_GET['ev_type'] ) );

			// Only honour a type this grid actually covers.
			if ( in_array( $candidate, $post_types, true ) ) {
				$selected['type'] = $candidate;
			}
		}

		/*
		 * Two lists, deliberately.
		 *
		 * `$taxonomies` is everything the dropdown renders, so the script has the
		 * full set to narrow from as Type changes. `$scoped` is only those
		 * belonging to the chosen type, and the submitted term is validated
		 * against that — which is what discards a stale pairing. Picking a
		 * Category, switching to Events and submitting drops the Category rather
		 * than returning an empty grid.
		 */
		$taxonomies = dnte_eag_filter_taxonomies( $post_types, $atts['taxonomies'] );
		$scope      = $selected['type'] ? array( $selected['type'] ) : $post_types;
		$scoped     = dnte_eag_filter_taxonomies( $scope, $atts['taxonomies'] );

		if ( ! empty( $_GET['ev_term'] ) ) {
			$selected['term'] = dnte_eag_parse_term( sanitize_text_field( wp_unslash( $_GET['ev_term'] ) ), $scoped );
		}

		$paged = isset( $_GET['ev_page'] ) ? max( 1, (int) $_GET['ev_page'] ) : 1;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// ── Query ──────────────────────────────────────────────────────────────
		$args = array(
			'post_type'           => $selected['type'] ? array( $selected['type'] ) : $post_types,
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'paged'               => $paged,
			'order'               => $order,
			'orderby'             => sanitize_key( $atts['orderby'] ),
			'ignore_sticky_posts' => true,
		);

		if ( $selected['term'] ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $selected['term']['taxonomy'],
					'field'    => 'slug',
					'terms'    => $selected['term']['slug'],
				),
			);
		}

		$query = new WP_Query( $args );

		dnte_eag_enqueue_assets();

		$html = '<div class="eag" id="' . esc_attr( $anchor ) . '" style="--eag-columns:' . (int) $columns . '">';

		if ( 'no' !== strtolower( (string) $atts['filters'] ) ) {
			$html .= dnte_eag_render_filters( $taxonomies, $post_types, $selected, $anchor );
		}

		if ( $query->have_posts() ) {
			$html .= '<div class="eag-grid">';

			while ( $query->have_posts() ) {
				$query->the_post();
				$html .= dnte_eag_render_card( get_the_ID() );
			}

			$html .= '</div>';
			$html .= dnte_eag_render_pagination( $paged, (int) $query->max_num_pages, $anchor );
		} else {
			$html .= '<p class="eag-empty">' . esc_html__( 'Nothing matches those filters.', 'dentist-exchange' ) . '</p>';
		}

		wp_reset_postdata();

		$html .= '</div>';

		return $html;
	}
endif;
add_shortcode( 'events_articles_grid', 'dnte_events_articles_grid_shortcode' );
