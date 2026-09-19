<?php
/**
 * Partners Grid Shortcode
 *
 * Renders the Partner post type as a card grid, each card opening a modal
 * with the partner's full details.
 *
 * Usage: [partners_grid columns="3" ids="12,45,78"]
 *
 * @package Dentist_Exchange
 */

// =============================================================================
// Asset enqueueing
// =============================================================================

if ( ! function_exists( 'dnte_partners_grid_enqueue_assets' ) ) :
	/**
	 * Loads the grid stylesheet and modal script. Called only when the
	 * shortcode renders.
	 */
	function dnte_partners_grid_enqueue_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'dnte-partners-grid',
			get_theme_file_uri( 'assets/css/partners-grid.css' ),
			array(),
			$version
		);

		wp_enqueue_script(
			'dnte-partners-modal',
			get_theme_file_uri( 'assets/js/partners-modal.js' ),
			array(),
			$version,
			true
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'dnte_partners_grid_render_card' ) ) :
	/**
	 * Renders one partner card: photo → name → designation → excerpt → Read More.
	 *
	 * The button carries the modal's ID rather than an href so the markup stays
	 * meaningful with JavaScript unavailable — the modal below it is inert but
	 * its content is still in the page.
	 *
	 * @param int    $post_id  Partner ID.
	 * @param string $modal_id ID of the modal this card opens.
	 * @return string
	 */
	function dnte_partners_grid_render_card( $post_id, $modal_id ) {
		$designation = get_post_meta( $post_id, 'dnte_partner_designation', true );
		$excerpt     = get_the_excerpt( $post_id );

		$html = '<article class="hpg-card">';

		if ( has_post_thumbnail( $post_id ) ) {
			$html .= '<div class="hpg-card__photo">';
			$html .= get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy' ) );
			$html .= '</div>';
		}

		$html .= '<div class="hpg-card__body">';

		$html .= '<div class="hpg-card__heading">';
		$html .= '<h3 class="hpg-card__name">' . esc_html( get_the_title( $post_id ) ) . '</h3>';

		if ( $designation ) {
			$html .= '<p class="hpg-card__designation">' . esc_html( $designation ) . '</p>';
		}
		$html .= '</div>';

		if ( $excerpt ) {
			$html .= '<p class="hpg-card__excerpt">' . esc_html( $excerpt ) . '</p>';
		}

		$html .= '<button type="button" class="hpg-card__more" data-hpg-open="' . esc_attr( $modal_id ) . '">';
		$html .= esc_html__( 'Read More', 'dentist-exchange' );
		$html .= '<span class="hpg-card__more-arrow" aria-hidden="true">&rarr;</span>';
		$html .= '</button>';

		$html .= '</div>';
		$html .= '</article>';

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_partners_grid_render_modal' ) ) :
	/**
	 * Renders one partner's detail modal.
	 *
	 * Top section is a two-column split — photo on the left, name and
	 * designation on the right. The bottom section holds the full editor
	 * content.
	 *
	 * A native <dialog> handles the top layer, the backdrop, Escape and focus
	 * trapping, so the script only has to open and close it.
	 *
	 * @param int    $post_id  Partner ID.
	 * @param string $modal_id ID to give the dialog.
	 * @return string
	 */
	function dnte_partners_grid_render_modal( $post_id, $modal_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$designation = get_post_meta( $post_id, 'dnte_partner_designation', true );
		$title_id    = $modal_id . '-title';

		/*
		 * Partner content is authored in the block editor by an editor-level
		 * user, so it runs through the_content for blocks, shortcodes and
		 * wpautop rather than being escaped as plain text.
		 */
		$content = apply_filters( 'the_content', $post->post_content );

		$html = '<dialog class="hpg-modal" id="' . esc_attr( $modal_id ) . '" aria-labelledby="' . esc_attr( $title_id ) . '">';
		$html .= '<div class="hpg-modal__dialog">';

		$html .= '<button type="button" class="hpg-modal__close" data-hpg-close aria-label="' . esc_attr__( 'Close', 'dentist-exchange' ) . '">';
		$html .= '<span aria-hidden="true">&times;</span>';
		$html .= '</button>';

		// ── Top: photo | name + designation ──────────────────────────────
		$html .= '<div class="hpg-modal__top">';

		if ( has_post_thumbnail( $post_id ) ) {
			$html .= '<div class="hpg-modal__photo">';
			$html .= get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy' ) );
			$html .= '</div>';
		}

		$html .= '<div class="hpg-modal__heading">';
		$html .= '<h2 class="hpg-modal__name" id="' . esc_attr( $title_id ) . '">' . esc_html( get_the_title( $post_id ) ) . '</h2>';

		if ( $designation ) {
			$html .= '<p class="hpg-modal__designation">' . esc_html( $designation ) . '</p>';
		}

		$html .= '</div>';
		$html .= '</div>';

		// ── Bottom: full content ─────────────────────────────────────────
		if ( $content ) {
			$html .= '<div class="hpg-modal__content">' . $content . '</div>';
		}

		$html .= '</div>';
		$html .= '</dialog>';

		return $html;
	}
endif;


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_partners_grid_shortcode' ) ) :
	/**
	 * [partners_grid columns="3" per_page="12" ids="" order="DESC" orderby="date"]
	 *
	 * `columns`  — grid columns on desktop, 1-6 (default 3).
	 * `per_page` — how many partners to show, 1-50 (default 12).
	 * `ids`      — comma-separated partner IDs to render, in that order.
	 *              Empty renders all partners in descending order.
	 * `order`    — ASC or DESC (default DESC).
	 * `orderby`  — any WP_Query orderby value (default date).
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	function dnte_partners_grid_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'columns'  => 3,
				'per_page' => 12,
				'ids'      => '',
				'order'    => 'DESC',
				'orderby'  => 'date',
			),
			$atts,
			'partners_grid'
		);

		if ( ! post_type_exists( 'partner' ) ) {
			return '';
		}

		$columns  = min( 6, max( 1, (int) $atts['columns'] ) );
		$per_page = min( 50, max( 1, (int) $atts['per_page'] ) );
		$order    = 'ASC' === strtoupper( (string) $atts['order'] ) ? 'ASC' : 'DESC';

		$args = array(
			'post_type'           => 'partner',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'order'               => $order,
			'orderby'             => sanitize_key( $atts['orderby'] ),
			'ignore_sticky_posts' => true,
		);

		// When IDs are given, render exactly those partners, preserving the
		// order they were written in (WP_Query's post__in follows ID order).
		// A bare array_filter drops the 0 that an empty attribute produces.
		$ids = array_values( array_filter( array_map( 'absint', explode( ',', (string) $atts['ids'] ) ) ) );

		if ( $ids ) {
			$args['post__in']       = $ids;
			$args['orderby']        = 'post__in';
			$args['posts_per_page'] = count( $ids );
		}

		$query = new WP_Query( $args );

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			return '<p class="hpg-empty">' . esc_html__( 'No partners found.', 'dentist-exchange' ) . '</p>';
		}

		dnte_partners_grid_enqueue_assets();

		$cards  = '';
		$modals = '';

		while ( $query->have_posts() ) {
			$query->the_post();

			$post_id  = get_the_ID();
			$modal_id = 'hpg-modal-' . $post_id;

			$cards  .= dnte_partners_grid_render_card( $post_id, $modal_id );
			$modals .= dnte_partners_grid_render_modal( $post_id, $modal_id );
		}

		wp_reset_postdata();

		$html  = '<div class="hpg-grid" style="--hpg-columns:' . (int) $columns . '">' . $cards . '</div>';
		$html .= $modals;

		return $html;
	}
endif;
add_shortcode( 'partners_grid', 'dnte_partners_grid_shortcode' );
