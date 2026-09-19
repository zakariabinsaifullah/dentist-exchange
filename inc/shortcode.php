<?php
/**
 * Posts Grid Shortcode
 *
 * Renders a filterable, paginated post grid via AJAX.
 *
 * Usage: [dnte_posts_grid per_page="6" post_type="post"]
 */

// =============================================================================
// Asset enqueueing
// =============================================================================

if ( ! function_exists( 'dnte_posts_grid_enqueue_assets' ) ) :
	function dnte_posts_grid_enqueue_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'dnte-posts-grid',
			get_theme_file_uri( 'assets/css/shortcode.css' ),
			array(),
			$version
		);

		wp_enqueue_script(
			'dnte-posts-grid',
			get_theme_file_uri( 'assets/js/shortcode.js' ),
			array(),
			$version,
			true
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'dnte_posts_grid_render_post_item' ) ) :
	/**
	 * Renders a single post card: image → meta (category, date + reading time) → title → excerpt → read more.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy used for the category label.
	 */
	function dnte_posts_grid_render_post_item( $post_id, $taxonomy = 'category' ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$permalink   = get_permalink( $post_id );
		$title       = get_the_title( $post_id );
		$excerpt     = get_the_excerpt( $post_id );
		$date        = get_the_date( 'F j, Y', $post_id );
		$author_id   = (int) $post->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );
		$avatar      = get_avatar( $author_id, 32, '', esc_attr( $author_name ), array( 'class' => 'ipg-card__avatar-img' ) );
		$thumbnail   = has_post_thumbnail( $post_id )
			? get_the_post_thumbnail( $post_id, 'medium_large', array( 'loading' => 'lazy' ) )
			: '';

		// Category (first term of the resolved taxonomy).
		$terms    = get_the_terms( $post_id, $taxonomy );
		$cat_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

		// Reading time: word count / 200 wpm, rounded up to at least 1 minute.
		$word_count   = str_word_count( wp_strip_all_tags( $post->post_content ) );
		$reading_time = max( 1, (int) ceil( $word_count / 200 ) );

		$html  = '<div class="ipg-card">';

		if ( $thumbnail ) {
			$html .= '<a href="' . esc_url( $permalink ) . '" class="ipg-card__image" tabindex="-1" aria-hidden="true">';
			$html .= $thumbnail;
			$html .= '</a>';
		}

		$html .= '<div class="ipg-card__body">';

		// Meta row: category | date + reading time.
		$html .= '<div class="ipg-card__meta-row">';
		if ( $cat_name ) {
			$html .= '<span class="ipg-card__category">' . esc_html( $cat_name ) . '</span>';
		}
		$html .= '<span class="ipg-card__meta">';
		$html .= '<span class="ipg-card__date">' . esc_html( $date ) . '</span>';
		$html .= '<span class="ipg-card__sep" aria-hidden="true">&middot;</span>';
		/* translators: %d: reading time in minutes. */
		$html .= '<span class="ipg-card__read-time">' . sprintf( esc_html__( '%d min read', 'dentist-exchange' ), $reading_time ) . '</span>';
		$html .= '</span>';
		$html .= '</div>';

		$html .= '<h2 class="ipg-card__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h2>';

		if ( $excerpt ) {
			$html .= '<p class="ipg-card__excerpt">' . esc_html( $excerpt ) . '</p>';
		}

		// Read more button.
		$arrow_svg = '<svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M.75 5.417h9.333m-4.666 4.666 4.666-4.666L5.417.75" stroke="url(#dnte-arrow-grad)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="dnte-arrow-grad" x1="12.12" y1=".71" x2="-.53" y2="1.894" gradientUnits="userSpaceOnUse"><stop offset=".184" stop-color="#886066"/><stop offset=".918" stop-color="#551c25"/></linearGradient></defs></svg>';
		$html    .= '<a class="ipg-card__read-more" href="' . esc_url( $permalink ) . '">';
		$html    .= '<span>' . esc_html__( 'Read More', 'dentist-exchange' ) . '</span>';
		$html    .= $arrow_svg;
		$html    .= '</a>';

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_posts_grid_render_posts' ) ) :
	/**
	 * Renders the full grid of post cards for a given WP_Query.
	 *
	 * @param WP_Query $query    The query to render.
	 * @param string   $taxonomy Taxonomy used for the category label.
	 */
	function dnte_posts_grid_render_posts( $query, $taxonomy = 'category' ) {
		if ( ! $query->have_posts() ) {
			return '<p class="ipg-no-posts">' . esc_html__( 'No posts found.', 'dentist-exchange' ) . '</p>';
		}

		$html = '<div class="ipg-grid">';

		while ( $query->have_posts() ) {
			$query->the_post();
			$html .= dnte_posts_grid_render_post_item( get_the_ID(), $taxonomy );
		}

		$html .= '</div>';

		wp_reset_postdata();

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_posts_grid_resolve_category_ids' ) ) :
	/**
	 * Resolves a comma-separated list of term IDs/slugs into an array of term IDs.
	 */
	function dnte_posts_grid_resolve_category_ids( $categories_raw, $taxonomy ) {
		$ids = array();

		foreach ( array_filter( array_map( 'trim', explode( ',', (string) $categories_raw ) ), 'strlen' ) as $token ) {
			$term = is_numeric( $token )
				? get_term_by( 'id', (int) $token, $taxonomy )
				: get_term_by( 'slug', sanitize_title( $token ), $taxonomy );

			if ( $term && ! is_wp_error( $term ) ) {
				$ids[] = (int) $term->term_id;
			}
		}

		return array_unique( $ids );
	}
endif;


if ( ! function_exists( 'dnte_posts_grid_pagination_range' ) ) :
	/**
	 * Returns an array of page numbers and '...' placeholders.
	 * Always shows first/last page and current page ± 1 neighbour.
	 */
	function dnte_posts_grid_pagination_range( $total_pages, $current_page ) {
		$total_pages  = (int) $total_pages;
		$current_page = (int) $current_page;

		if ( $total_pages <= 7 ) {
			return range( 1, $total_pages );
		}

		$left  = max( 2, $current_page - 1 );
		$right = min( $total_pages - 1, $current_page + 1 );
		$pages = array( 1 );

		if ( $left > 2 ) {
			$pages[] = '...';
		}

		for ( $i = $left; $i <= $right; $i++ ) {
			$pages[] = $i;
		}

		if ( $right < $total_pages - 1 ) {
			$pages[] = '...';
		}

		$pages[] = $total_pages;

		return $pages;
	}
endif;


if ( ! function_exists( 'dnte_posts_grid_render_pagination' ) ) :
	/**
	 * Renders prev/next arrows + numbered page buttons with ellipsis.
	 */
	function dnte_posts_grid_render_pagination( $total_pages, $current_page ) {
		$total_pages  = (int) $total_pages;
		$current_page = (int) $current_page;

		if ( $total_pages <= 1 ) {
			return '';
		}

		$svg_prev = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>';
		$svg_next = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>';

		$html = '<div class="ipg-pagination">';

		// Prev.
		$prev_page = max( 1, $current_page - 1 );
		$html     .= '<button class="ipg-page-btn ipg-page-arrow"'
			. ( 1 === $current_page ? ' disabled' : '' )
			. ' data-page="' . $prev_page . '" aria-label="' . esc_attr__( 'Previous page', 'dentist-exchange' ) . '">'
			. $svg_prev
			. '</button>';

		// Pages.
		foreach ( dnte_posts_grid_pagination_range( $total_pages, $current_page ) as $page ) {
			if ( '...' === $page ) {
				$html .= '<span class="ipg-page-ellipsis">&hellip;</span>';
			} else {
				$active = ( (int) $page === $current_page ) ? ' active' : '';
				$html  .= '<button class="ipg-page-btn' . $active . '" data-page="' . (int) $page . '" aria-label="' . sprintf( esc_attr__( 'Page %d', 'dentist-exchange' ), (int) $page ) . '">' . (int) $page . '</button>';
			}
		}

		// Next.
		$next_page = min( $total_pages, $current_page + 1 );
		$html     .= '<button class="ipg-page-btn ipg-page-arrow"'
			. ( $current_page === $total_pages ? ' disabled' : '' )
			. ' data-page="' . $next_page . '" aria-label="' . esc_attr__( 'Next page', 'dentist-exchange' ) . '">'
			. $svg_next
			. '</button>';

		$html .= '</div>';

		return $html;
	}
endif;


// =============================================================================
// AJAX handler
// =============================================================================

if ( ! function_exists( 'dnte_posts_grid_ajax' ) ) :
	function dnte_posts_grid_ajax() {
		check_ajax_referer( 'dnte_posts_grid_nonce', 'nonce' );

		$cat        = isset( $_POST['cat'] )        ? absint( $_POST['cat'] )                                        : 0;
		$page       = isset( $_POST['page'] )       ? max( 1, absint( $_POST['page'] ) )                             : 1;
		$per_page   = isset( $_POST['per_page'] )   ? min( 50, max( 1, absint( $_POST['per_page'] ) ) )              : 6;
		$post_type  = isset( $_POST['post_type'] )  ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) )       : 'post';
		$taxonomy   = isset( $_POST['taxonomy'] )   ? sanitize_key( $_POST['taxonomy'] )                             : 'category';
		$categories = isset( $_POST['categories'] ) ? sanitize_text_field( wp_unslash( $_POST['categories'] ) )      : '';

		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		if ( ! taxonomy_exists( $taxonomy ) ) {
			$taxonomy = 'category';
		}

		$allowed_cat_ids = array_filter( array_map( 'absint', explode( ',', $categories ) ) );

		$args = array(
			'post_type'      => $post_type,
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'post_status'    => 'publish',
		);

		if ( $cat > 0 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $cat,
				),
			);
		} elseif ( ! empty( $allowed_cat_ids ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $allowed_cat_ids,
				),
			);
		}

		$query = new WP_Query( $args );

		wp_send_json_success( array(
			'html'         => dnte_posts_grid_render_posts( $query, $taxonomy ),
			'pagination'   => dnte_posts_grid_render_pagination( (int) $query->max_num_pages, $page ),
			'total_pages'  => (int) $query->max_num_pages,
			'current_page' => $page,
		) );
	}
endif;
add_action( 'wp_ajax_dnte_posts_grid', 'dnte_posts_grid_ajax' );
add_action( 'wp_ajax_nopriv_dnte_posts_grid', 'dnte_posts_grid_ajax' );


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_posts_grid_resolve_taxonomy' ) ) :
	/**
	 * Returns the primary hierarchical taxonomy for a post type.
	 */
	function dnte_posts_grid_resolve_taxonomy( $post_type ) {
		foreach ( get_object_taxonomies( $post_type, 'objects' ) as $tax ) {
			if ( $tax->public && $tax->hierarchical ) {
				return $tax->name;
			}
		}
		return 'category';
	}
endif;


if ( ! function_exists( 'dnte_posts_grid_resolve_allowed_cats' ) ) :
	/**
	 * Resolves allowed category IDs, falling back to all non-empty terms.
	 */
	function dnte_posts_grid_resolve_allowed_cats( $categories_raw, $taxonomy ) {
		$ids = dnte_posts_grid_resolve_category_ids( $categories_raw, $taxonomy );

		if ( empty( $ids ) ) {
			$all = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true, 'fields' => 'ids' ) );
			$ids = is_wp_error( $all ) ? array() : array_map( 'intval', $all );
		}

		return $ids;
	}
endif;


if ( ! function_exists( 'dnte_posts_grid_render_tabs' ) ) :
	/**
	 * Renders the filter tab buttons for a given set of terms.
	 *
	 * @param array $terms Array of WP_Term objects.
	 */
	function dnte_posts_grid_render_tabs( $terms ) {
		if ( empty( $terms ) ) {
			return '';
		}

		$html  = '<div class="ipg-nav">';
		$html .= '<button class="ipg-filter-btn active" data-cat="0">' . esc_html__( 'All', 'dentist-exchange' ) . '</button>';
		foreach ( $terms as $term ) {
			$html .= '<button class="ipg-filter-btn" data-cat="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</button>';
		}
		$html .= '</div>';

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_posts_grid_shortcode' ) ) :
	/**
	 * [dnte_posts_grid per_page="6" post_type="post" categories="4,9" id=""]
	 *
	 * `per_page`   — posts per page (default 6).
	 * `categories` — comma-separated term IDs or slugs; omit for all categories.
	 * `id`         — when set, tabs are omitted and the grid listens for a remote
	 *                dnte:filter event fired by [dnte_posts_tabs for="<id>"].
	 */
	function dnte_posts_grid_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'per_page'   => 6,
				'post_type'  => 'post',
				'categories' => '',
				'id'         => '',
			),
			$atts,
			'dnte_posts_grid'
		);

		$per_page  = min( 50, max( 1, (int) $atts['per_page'] ) );
		$post_type = sanitize_key( $atts['post_type'] );
		$grid_id   = sanitize_html_class( $atts['id'] );

		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		$taxonomy        = dnte_posts_grid_resolve_taxonomy( $post_type );
		$allowed_cat_ids = dnte_posts_grid_resolve_allowed_cats( $atts['categories'], $taxonomy );

		if ( empty( $allowed_cat_ids ) ) {
			return '<p class="ipg-no-posts">' . esc_html__( 'No categories found.', 'dentist-exchange' ) . '</p>';
		}

		// Initial query (page 1, no category filter).
		$query = new WP_Query( array(
			'post_type'      => $post_type,
			'posts_per_page' => $per_page,
			'paged'          => 1,
			'post_status'    => 'publish',
			'tax_query'      => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $allowed_cat_ids,
				),
			),
		) );

		dnte_posts_grid_enqueue_assets();

		$config = wp_json_encode( array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'dnte_posts_grid_nonce' ),
			'perPage'    => $per_page,
			'postType'   => $post_type,
			'taxonomy'   => $taxonomy,
			'categories' => implode( ',', $allowed_cat_ids ),
		) );

		$grid_id_attr = $grid_id ? ' data-grid-id="' . esc_attr( $grid_id ) . '"' : '';
		$html = '<div class="ipg-wrapper" data-config="' . esc_attr( $config ) . '"' . $grid_id_attr . '>';

		// Embed tabs only in self-contained mode (no id attribute).
		if ( ! $grid_id ) {
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'include'    => $allowed_cat_ids,
				'orderby'    => 'include',
				'hide_empty' => true,
			) );
			$html .= dnte_posts_grid_render_tabs( is_wp_error( $terms ) ? array() : $terms );
		}

		$html .= '<div class="ipg-posts">' . dnte_posts_grid_render_posts( $query, $taxonomy ) . '</div>';
		$html .= '<div class="ipg-pagination-wrap">' . dnte_posts_grid_render_pagination( (int) $query->max_num_pages, 1 ) . '</div>';

		$html .= '</div>';

		wp_reset_postdata();

		return $html;
	}
endif;
add_shortcode( 'dnte_posts_grid', 'dnte_posts_grid_shortcode' );
// Backwards-compatible alias: some content used the callback name as the tag.
add_shortcode( 'dnte_posts_grid_shortcode', 'dnte_posts_grid_shortcode' );


if ( ! function_exists( 'dnte_posts_tabs_shortcode' ) ) :
	/**
	 * [dnte_posts_tabs for="blog" post_type="post" categories="4,9"]
	 *
	 * Renders standalone filter tabs that control a remote [dnte_posts_grid id="blog"].
	 * `for`        — must match the `id` of the target [dnte_posts_grid].
	 * `categories` — must match the `categories` passed to the target grid.
	 * `post_type`  — must match the `post_type` of the target grid.
	 */
	function dnte_posts_tabs_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'for'        => '',
				'post_type'  => 'post',
				'categories' => '',
			),
			$atts,
			'dnte_posts_tabs'
		);

		$grid_id   = sanitize_html_class( $atts['for'] );
		$post_type = sanitize_key( $atts['post_type'] );

		if ( ! $grid_id ) {
			return '';
		}

		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		$taxonomy        = dnte_posts_grid_resolve_taxonomy( $post_type );
		$allowed_cat_ids = dnte_posts_grid_resolve_allowed_cats( $atts['categories'], $taxonomy );

		if ( empty( $allowed_cat_ids ) ) {
			return '';
		}

		$terms = get_terms( array(
			'taxonomy'   => $taxonomy,
			'include'    => $allowed_cat_ids,
			'orderby'    => 'include',
			'hide_empty' => true,
		) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return '';
		}

		dnte_posts_grid_enqueue_assets();

		$html  = '<div class="ipg-tabs-remote" data-for="' . esc_attr( $grid_id ) . '">';
		$html .= dnte_posts_grid_render_tabs( $terms );
		$html .= '</div>';

		return $html;
	}
endif;
add_shortcode( 'dnte_posts_tabs', 'dnte_posts_tabs_shortcode' );
