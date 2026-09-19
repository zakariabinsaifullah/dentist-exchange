<?php
/**
 * Team Grid Shortcode
 *
 * Renders the Team post type as a card grid.
 *
 * Usage: [team_grid columns="3" ids="12,45,78"]
 *
 * @package Dentist_Exchange
 */

// =============================================================================
// Asset enqueueing
// =============================================================================

if ( ! function_exists( 'dnte_team_grid_enqueue_assets' ) ) :
	/**
	 * Loads the grid stylesheet. Called only when the shortcode renders.
	 */
	function dnte_team_grid_enqueue_assets() {
		wp_enqueue_style(
			'dnte-team-grid',
			get_theme_file_uri( 'assets/css/team-grid.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'dnte_team_grid_render_card' ) ) :
	/**
	 * Renders one team member card: photo → name → designation → short intro.
	 *
	 * @param int $post_id Team member ID.
	 * @return string
	 */
	function dnte_team_grid_render_card( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$designation = get_post_meta( $post_id, 'dnte_designation', true );
		$short_intro = get_post_meta( $post_id, 'dnte_short_intro', true );

		$html = '<article class="dnte-card">';

		if ( has_post_thumbnail( $post_id ) ) {
			$html .= '<div class="dnte-card__photo">';
			$html .= get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy' ) );
			$html .= '</div>';
		}

		$html .= '<div class="dnte-card__body">';

		$html .= '<div class="dnte-card__heading">';
		$html .= '<h3 class="dnte-card__name">' . esc_html( get_the_title( $post_id ) ) . '</h3>';

		if ( $designation ) {
			$html .= '<p class="dnte-card__designation">' . esc_html( $designation ) . '</p>';
		}
		$html .= '</div>';

		if ( $short_intro ) {
			$html .= '<p class="dnte-card__intro">' . esc_html( $short_intro ) . '</p>';
		}

		$html .= '</div>';
		$html .= '</article>';

		return $html;
	}
endif;


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_team_grid_shortcode' ) ) :
	/**
	 * [team_grid columns="3" per_page="12" ids="" order="DESC" orderby="date"]
	 *
	 * `columns`  — grid columns on desktop, 1-6 (default 3).
	 * `per_page` — how many members to show, 1-50 (default 12).
	 * `ids`      — comma-separated team member IDs to render, in that order.
	 *              Empty renders all members in descending order.
	 * `order`    — ASC or DESC (default DESC).
	 * `orderby`  — any WP_Query orderby value (default date).
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	function dnte_team_grid_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'columns'  => 3,
				'per_page' => 12,
				'ids'      => '',
				'order'    => 'DESC',
				'orderby'  => 'date',
			),
			$atts,
			'team_grid'
		);

		if ( ! post_type_exists( 'team' ) ) {
			return '';
		}

		$columns  = min( 6, max( 1, (int) $atts['columns'] ) );
		$per_page = min( 50, max( 1, (int) $atts['per_page'] ) );
		$order    = 'ASC' === strtoupper( (string) $atts['order'] ) ? 'ASC' : 'DESC';

		$args = array(
			'post_type'           => 'team',
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'order'               => $order,
			'orderby'             => sanitize_key( $atts['orderby'] ),
			'ignore_sticky_posts' => true,
		);

		// When IDs are given, render exactly those members, preserving the
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
			return '<p class="dnte-empty">' . esc_html__( 'No team members found.', 'dentist-exchange' ) . '</p>';
		}

		dnte_team_grid_enqueue_assets();

		$html = '<div class="dnte-grid" style="--dnte-columns:' . (int) $columns . '">';

		while ( $query->have_posts() ) {
			$query->the_post();
			$html .= dnte_team_grid_render_card( get_the_ID() );
		}

		$html .= '</div>';

		wp_reset_postdata();

		return $html;
	}
endif;
add_shortcode( 'team_grid', 'dnte_team_grid_shortcode' );
