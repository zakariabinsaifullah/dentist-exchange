<?php
/**
 * Opening Roles Shortcode
 *
 * Renders active Open Roles as a card grid.
 *
 * Usage: [opening_roles columns="2" per_page="10" type="full-time"]
 *
 * @package Dentist_Exchange
 */

// =============================================================================
// Asset enqueueing
// =============================================================================

if ( ! function_exists( 'dnte_roles_grid_enqueue_assets' ) ) :
	/**
	 * Loads the grid stylesheet. Called only when the shortcode renders.
	 */
	function dnte_roles_grid_enqueue_assets() {
		wp_enqueue_style(
			'dnte-roles-grid',
			get_theme_file_uri( 'assets/css/roles-grid.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'dnte_roles_relative_date' ) ) :
	/**
	 * Formats a post date as "Today", "1 day ago", "12 days ago", and so on.
	 *
	 * Whole days are compared against the site's local midnight rather than a
	 * rolling 24 hours, so something posted last night reads as "1 day ago"
	 * this morning rather than "Today".
	 *
	 * @param  int $post_id Post ID.
	 * @return string
	 */
	function dnte_roles_relative_date( $post_id ) {
		$posted = (int) get_post_timestamp( $post_id );

		if ( ! $posted ) {
			return '';
		}

		$today  = (int) strtotime( wp_date( 'Y-m-d 00:00:00' ) );
		$posted_day = (int) strtotime( wp_date( 'Y-m-d 00:00:00', $posted ) );
		$days   = (int) round( ( $today - $posted_day ) / DAY_IN_SECONDS );

		if ( $days <= 0 ) {
			return __( 'Today', 'dentist-exchange' );
		}

		if ( 1 === $days ) {
			return __( '1 day ago', 'dentist-exchange' );
		}

		if ( $days < 30 ) {
			/* translators: %d: number of days. */
			return sprintf( __( '%d days ago', 'dentist-exchange' ), $days );
		}

		/* translators: %s: human readable time difference, e.g. "2 months". */
		return sprintf( __( '%s ago', 'dentist-exchange' ), human_time_diff( $posted ) );
	}
endif;


if ( ! function_exists( 'dnte_roles_match_percent' ) ) :
	/**
	 * Reads the match rate as a number.
	 *
	 * The field is free text, so "92", "92%" and "92 %" all parse; anything
	 * else has no percentage to compare against.
	 *
	 * @param  string $raw Stored meta value.
	 * @return int|null Null when the value is not a plain percentage.
	 */
	function dnte_roles_match_percent( $raw ) {
		if ( preg_match( '/^(\d{1,3})\s*%?$/', trim( (string) $raw ), $matches ) ) {
			return (int) $matches[1];
		}

		return null;
	}
endif;


if ( ! function_exists( 'dnte_roles_match_label' ) ) :
	/**
	 * Formats the match rate for display.
	 *
	 * A bare number becomes "92% match"; anything already carrying its own
	 * wording is left alone.
	 *
	 * @param  string $raw Stored meta value.
	 * @return string Empty when there is nothing to show.
	 */
	function dnte_roles_match_label( $raw ) {
		$raw = trim( (string) $raw );

		if ( '' === $raw ) {
			return '';
		}

		$percent = dnte_roles_match_percent( $raw );

		if ( null !== $percent ) {
			/* translators: %d: match percentage. */
			return sprintf( __( '%d%% match', 'dentist-exchange' ), $percent );
		}

		return $raw;
	}
endif;


if ( ! function_exists( 'dnte_roles_icon' ) ) :
	/**
	 * Returns one of the inline icons used on a role card.
	 *
	 * Strokes use `currentColor` so each icon takes the colour of the text it
	 * sits beside.
	 *
	 * @param  string $name pin, clock or external.
	 * @return string
	 */
	function dnte_roles_icon( $name ) {
		$icons = array(
			'pin'      => '<path d="M12 21s7-5.686 7-11a7 7 0 1 0-14 0c0 5.314 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
			'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
			'external' => '<path d="M14 4h6v6"/><path d="M20 4 11 13"/><path d="M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"/>',
		);

		if ( ! isset( $icons[ $name ] ) ) {
			return '';
		}

		return '<svg class="hrg-icon hrg-icon--' . esc_attr( $name ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
			. $icons[ $name ]
			. '</svg>';
	}
endif;


if ( ! function_exists( 'dnte_roles_render_card' ) ) :
	/**
	 * Renders one role card.
	 *
	 * @param int $post_id Role ID.
	 * @return string
	 */
	function dnte_roles_render_card( $post_id ) {
		$title = get_the_title( $post_id );

		$location   = get_post_meta( $post_id, 'dnte_role_location', true );
		$salary     = get_post_meta( $post_id, 'dnte_role_salary_range', true );
		$apply_link = get_post_meta( $post_id, 'dnte_role_apply_link', true );

		$match_raw     = get_post_meta( $post_id, 'dnte_role_match_rate', true );
		$match         = dnte_roles_match_label( $match_raw );
		$match_percent = dnte_roles_match_percent( $match_raw );

		// Anything under 90 is styled as a weaker match. A free-text value has
		// no number to judge, so it keeps the default treatment.
		$match_class = ( null !== $match_percent && $match_percent < 90 )
			? 'hrg-card__match hrg-card__match--low'
			: 'hrg-card__match';

		$types     = dnte_open_role_type_options();
		$natures   = dnte_open_role_nature_options();
		$type_key  = get_post_meta( $post_id, 'dnte_role_type', true );
		$nature_key = get_post_meta( $post_id, 'dnte_role_nature', true );

		$type   = isset( $types[ $type_key ] ) ? $types[ $type_key ] : '';
		$nature = isset( $natures[ $nature_key ] ) ? $natures[ $nature_key ] : '';

		$posted = dnte_roles_relative_date( $post_id );

		$html = '<article class="hrg-card">';

		// Row 1 — title + company, match pill, apply button.
		$html .= '<div class="hrg-card__row hrg-card__row--head">';

		$html .= '<div class="hrg-card__identity">';
		$html .= '<div class="hrg-card__title-line">';
		$html .= '<h3 class="hrg-card__title">' . esc_html( $title ) . '</h3>';
		if ( $match ) {
			$html .= '<span class="' . esc_attr( $match_class ) . '">' . esc_html( $match ) . '</span>';
		}
		$html .= '</div>';
		$html .= '<p class="hrg-card__company">' . esc_html__( 'Dentist Exchange', 'dentist-exchange' ) . '</p>';
		$html .= '</div>';

		if ( $apply_link ) {
			$html .= '<a class="hrg-card__apply" href="' . esc_url( $apply_link ) . '">';
			$html .= dnte_roles_icon( 'external' );
			$html .= '<span>' . esc_html__( 'Apply', 'dentist-exchange' ) . '</span>';
			$html .= '</a>';
		}

		$html .= '</div>';

		// Row 2 — location, posted date, nature.
		if ( $location || $posted || $nature ) {
			$html .= '<div class="hrg-card__row hrg-card__row--meta">';

			if ( $location ) {
				$html .= '<span class="hrg-card__meta">' . dnte_roles_icon( 'pin' ) . esc_html( $location ) . '</span>';
			}

			if ( $posted ) {
				$html .= '<span class="hrg-card__meta">' . dnte_roles_icon( 'clock' ) . esc_html( $posted ) . '</span>';
			}

			if ( $nature ) {
				$html .= '<span class="hrg-card__nature">' . esc_html( $nature ) . '</span>';
			}

			$html .= '</div>';
		}

		// Row 3 — salary and job type, separated by a dot.
		if ( $salary || $type ) {
			$parts = array_filter( array( $salary, $type ), 'strlen' );

			$html .= '<div class="hrg-card__row hrg-card__row--terms">';
			$html .= '<span class="hrg-card__terms">' . esc_html( implode( ' • ', $parts ) ) . '</span>';
			$html .= '</div>';
		}

		$html .= '</article>';

		return $html;
	}
endif;


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_opening_roles_shortcode' ) ) :
	/**
	 * [opening_roles columns="2" per_page="10" type="" nature="" order="DESC" orderby="date"]
	 *
	 * `columns`  — grid columns on desktop, 1-4 (default 2).
	 * `per_page` — how many roles to show, 1-50 (default 10).
	 * `type`     — employment type key, e.g. full-time. Empty for all.
	 * `nature`   — work arrangement key, e.g. remote. Empty for all.
	 * `order`    — ASC or DESC (default DESC).
	 * `orderby`  — any WP_Query orderby value (default date).
	 *
	 * Only active roles are listed.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	function dnte_opening_roles_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'columns'  => 2,
				'per_page' => 10,
				'type'     => '',
				'nature'   => '',
				'order'    => 'DESC',
				'orderby'  => 'date',
			),
			$atts,
			'opening_roles'
		);

		if ( ! post_type_exists( 'open-role' ) ) {
			return '';
		}

		$columns  = min( 4, max( 1, (int) $atts['columns'] ) );
		$per_page = min( 50, max( 1, (int) $atts['per_page'] ) );
		$order    = 'ASC' === strtoupper( (string) $atts['order'] ) ? 'ASC' : 'DESC';

		/*
		 * Active means the flag is on, or was never written at all — roles
		 * created before the toggle existed have no stored value and must not
		 * silently drop out of the listing.
		 */
		$meta_query = array(
			'relation' => 'AND',
			array(
				'relation' => 'OR',
				array(
					'key'     => 'dnte_role_active',
					'value'   => '1',
					'compare' => '=',
				),
				array(
					'key'     => 'dnte_role_active',
					'compare' => 'NOT EXISTS',
				),
			),
		);

		$type = sanitize_key( $atts['type'] );
		if ( $type && array_key_exists( $type, dnte_open_role_type_options() ) ) {
			$meta_query[] = array(
				'key'     => 'dnte_role_type',
				'value'   => $type,
				'compare' => '=',
			);
		}

		$nature = sanitize_key( $atts['nature'] );
		if ( $nature && array_key_exists( $nature, dnte_open_role_nature_options() ) ) {
			$meta_query[] = array(
				'key'     => 'dnte_role_nature',
				'value'   => $nature,
				'compare' => '=',
			);
		}

		$query = new WP_Query(
			array(
				'post_type'           => 'open-role',
				'post_status'         => 'publish',
				'posts_per_page'      => $per_page,
				'order'               => $order,
				'orderby'             => sanitize_key( $atts['orderby'] ),
				'ignore_sticky_posts' => true,
				'meta_query'          => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			)
		);

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			return '<p class="hrg-empty">' . esc_html__( 'No open roles at the moment.', 'dentist-exchange' ) . '</p>';
		}

		dnte_roles_grid_enqueue_assets();

		$html = '<div class="hrg-grid" style="--hrg-columns:' . (int) $columns . '">';

		while ( $query->have_posts() ) {
			$query->the_post();
			$html .= dnte_roles_render_card( get_the_ID() );
		}

		$html .= '</div>';

		wp_reset_postdata();

		return $html;
	}
endif;
add_shortcode( 'opening_roles', 'dnte_opening_roles_shortcode' );
