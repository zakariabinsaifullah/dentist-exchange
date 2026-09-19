<?php
/**
 * Latest Insights Shortcode
 *
 * A two-column section: the most recent Events as cards on the left, and the
 * most recent blog posts on the right as a Swiper carousel showing two posts
 * per slide, with a vertical progress bar and dot pagination.
 *
 * Usage: [latest_insights events="2" posts="8" per_slide="2"]
 *
 * Replaces the earlier [events_grid], which showed events alone.
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// Asset enqueueing
// =============================================================================

if ( ! function_exists( 'dnte_insights_enqueue_assets' ) ) :
	/**
	 * Loads the section stylesheet and, when the carousel is present, Swiper.
	 *
	 * Swiper is registered in inc/enqueue.php with `$in_footer = true`, so
	 * enqueueing it here — during `the_content`, long after `wp_enqueue_scripts`
	 * — still lands it in the footer alongside the init script.
	 *
	 * @param bool $with_carousel Whether the carousel rendered.
	 */
	function dnte_insights_enqueue_assets( $with_carousel = true ) {
		$version = wp_get_theme()->get( 'Version' );

		/*
		 * Swiper is declared a dependency rather than merely enqueued alongside,
		 * so WordPress prints it first. Order matters here: several of the rules
		 * below override Swiper's base styles at the same specificity — notably
		 * the `margin-inline` reset on `.hli-carousel` against `.swiper`'s auto
		 * margins — and would lose the cascade if Swiper came second.
		 */
		wp_enqueue_style(
			'dnte-latest-insights',
			get_theme_file_uri( 'assets/css/latest-insights.css' ),
			$with_carousel ? array( 'dnte-swiper-style' ) : array(),
			$version
		);

		if ( ! $with_carousel ) {
			return;
		}

		wp_enqueue_script( 'dnte-swiper-script' );

		wp_enqueue_script(
			'dnte-latest-insights',
			get_theme_file_uri( 'assets/js/latest-insights.js' ),
			array( 'dnte-swiper-script' ),
			$version,
			true
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'dnte_insights_arrow_svg' ) ) :
	/**
	 * The trailing arrow used by the Learn More link.
	 *
	 * Filled with `currentColor` so it follows the link colour on hover, the
	 * same way the Link button variation's masked arrow does.
	 *
	 * @return string
	 */
	function dnte_insights_arrow_svg() {
		return '<svg class="hli-event__more-icon" width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
			. '<path d="m13.75 6.625-5.5 5.25c-.312.281-.781.281-1.062-.031s-.282-.781.03-1.063l4.157-3.969H.75a.72.72 0 0 1-.75-.75.74.74 0 0 1 .75-.75h10.625L7.219 1.376A.746.746 0 0 1 7.187.313.746.746 0 0 1 8.25.28l5.5 5.25c.156.157.25.344.25.532a.78.78 0 0 1-.25.562" fill="currentColor"/>'
			. '</svg>';
	}
endif;


if ( ! function_exists( 'dnte_insights_icon_svg' ) ) :
	/**
	 * Calendar and clock glyphs for the post meta row.
	 *
	 * Stroked with `currentColor` so they take the meta row's colour.
	 *
	 * @param string $name Either `calendar` or `clock`.
	 * @return string
	 */
	function dnte_insights_icon_svg( $name ) {
		$paths = array(
			'calendar' => '<rect x="2.4" y="3.2" width="11.2" height="10.4" rx="1.4"/><path d="M2.4 6.4h11.2M5.6 2.4v2M10.4 2.4v2"/>',
			'clock'    => '<circle cx="8" cy="8" r="5.6"/><path d="M8 4.8V8l2.4 1.6"/>',
		);

		if ( ! isset( $paths[ $name ] ) ) {
			return '';
		}

		return '<svg class="hli-post__meta-icon" width="14" height="14" viewBox="0 0 16 16" fill="none"'
			. ' stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"'
			. ' xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
			. $paths[ $name ]
			. '</svg>';
	}
endif;


if ( ! function_exists( 'dnte_insights_get_summary' ) ) :
	/**
	 * Returns a card summary.
	 *
	 * A hand-written excerpt is used verbatim; otherwise the post content is
	 * trimmed to the given word count.
	 *
	 * @param WP_Post $post  Post or event.
	 * @param int     $words Words to trim generated summaries to.
	 * @return string
	 */
	function dnte_insights_get_summary( $post, $words = 25 ) {
		if ( ! empty( $post->post_excerpt ) ) {
			return $post->post_excerpt;
		}

		$content = strip_shortcodes( $post->post_content );
		$content = wp_strip_all_tags( $content );

		return wp_trim_words( $content, $words, '&hellip;' );
	}
endif;


if ( ! function_exists( 'dnte_insights_read_time' ) ) :
	/**
	 * Estimated reading time in whole minutes.
	 *
	 * Derived from the content rather than stored on the post, so it can never
	 * fall out of step with an edit. `str_word_count` is Latin-alphabet only, so
	 * the count falls back to splitting on whitespace when it comes back empty —
	 * otherwise a CJK or Cyrillic post would always report the one-minute floor.
	 *
	 * @param WP_Post $post Post object.
	 * @return int Minutes, never less than 1.
	 */
	function dnte_insights_read_time( $post ) {
		$content = strip_shortcodes( $post->post_content );
		$content = wp_strip_all_tags( $content );

		$count = str_word_count( $content );

		if ( ! $count ) {
			$count = count( preg_split( '/\s+/u', trim( $content ), -1, PREG_SPLIT_NO_EMPTY ) ?: array() );
		}

		/**
		 * Filters the words-per-minute used for the reading estimate.
		 *
		 * @param int     $wpm  Words per minute. Default 200.
		 * @param WP_Post $post Post being measured.
		 */
		$wpm = (int) apply_filters( 'dnte_insights_words_per_minute', 200, $post );
		$wpm = max( 1, $wpm );

		return max( 1, (int) ceil( $count / $wpm ) );
	}
endif;


if ( ! function_exists( 'dnte_insights_render_event' ) ) :
	/**
	 * Renders one event card: image → (date | type + title) → summary → Learn More.
	 *
	 * @param int $post_id Event ID.
	 * @return string
	 */
	function dnte_insights_render_event( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$permalink = get_permalink( $post_id );
		$summary   = dnte_insights_get_summary( $post );

		// Date stack, from the publish date.
		$timestamp = (int) get_post_timestamp( $post_id );
		$month     = wp_date( 'M', $timestamp );
		$day       = wp_date( 'd', $timestamp );
		$year      = wp_date( 'Y', $timestamp );
		$machine   = wp_date( 'Y-m-d', $timestamp );

		// First Type term.
		$terms     = get_the_terms( $post_id, 'event-type' );
		$type_name = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : '';

		$html = '<article class="hli-event">';

		if ( has_post_thumbnail( $post_id ) ) {
			$html .= '<a class="hli-event__image" href="' . esc_url( $permalink ) . '" tabindex="-1" aria-hidden="true">';
			$html .= get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy' ) );
			$html .= '</a>';
		}

		$html .= '<div class="hli-event__body">';

		// Two columns: date | type + title.
		$html .= '<div class="hli-event__head">';
		$html .= '<time class="hli-event__date" datetime="' . esc_attr( $machine ) . '">';
		$html .= '<span class="hli-event__month">' . esc_html( $month ) . '</span>';
		$html .= '<span class="hli-event__day">' . esc_html( $day ) . '</span>';
		$html .= '<span class="hli-event__year">' . esc_html( $year ) . '</span>';
		$html .= '</time>';

		$html .= '<div class="hli-event__headings">';
		if ( $type_name ) {
			$html .= '<span class="hli-event__type">' . esc_html( $type_name ) . '</span>';
		}
		$html .= '<h3 class="hli-event__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a></h3>';
		$html .= '</div>';
		$html .= '</div>';

		if ( $summary ) {
			$html .= '<p class="hli-event__excerpt">' . esc_html( $summary ) . '</p>';
		}

		$html .= '<a class="hli-event__more" href="' . esc_url( $permalink ) . '">';
		$html .= '<span>' . esc_html__( 'Learn More', 'dentist-exchange' ) . '</span>';
		$html .= dnte_insights_arrow_svg();
		$html .= '</a>';

		$html .= '</div>'; // .hli-event__body
		$html .= '</article>';

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_insights_render_post' ) ) :
	/**
	 * Renders one carousel post: category → title → meta row → summary.
	 *
	 * @param int $post_id Post ID.
	 * @return string
	 */
	function dnte_insights_render_post( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post ) {
			return '';
		}

		$permalink = get_permalink( $post_id );
		$summary   = dnte_insights_get_summary( $post, 22 );
		$timestamp = (int) get_post_timestamp( $post_id );
		$minutes   = dnte_insights_read_time( $post );

		$categories = get_the_category( $post_id );
		$category   = ( $categories && ! is_wp_error( $categories ) ) ? $categories[0] : null;

		$html = '<article class="hli-post">';

		if ( $category ) {
			$html .= '<a class="hli-post__category" href="' . esc_url( get_category_link( $category ) ) . '">';
			$html .= esc_html( $category->name );
			$html .= '</a>';
		}

		$html .= '<h3 class="hli-post__title"><a href="' . esc_url( $permalink ) . '">' . esc_html( get_the_title( $post_id ) ) . '</a></h3>';

		$html .= '<div class="hli-post__meta">';

		$html .= '<span class="hli-post__meta-item hli-post__author">';
		// Avatars can be switched off site-wide, in which case this returns ''.
		$html .= get_avatar( $post->post_author, 36, '', '', array( 'class' => 'hli-post__avatar' ) );
		$html .= '<span>' . esc_html( get_the_author_meta( 'display_name', $post->post_author ) ) . '</span>';
		$html .= '</span>';

		$html .= '<span class="hli-post__meta-item">';
		$html .= dnte_insights_icon_svg( 'calendar' );
		$html .= '<time datetime="' . esc_attr( wp_date( 'Y-m-d', $timestamp ) ) . '">' . esc_html( wp_date( 'd F Y', $timestamp ) ) . '</time>';
		$html .= '</span>';

		$html .= '<span class="hli-post__meta-item">';
		$html .= dnte_insights_icon_svg( 'clock' );
		$html .= '<span>' . sprintf(
			/* translators: %d: estimated reading time in minutes. */
			esc_html__( '%d Min. To Read', 'dentist-exchange' ),
			(int) $minutes
		) . '</span>';
		$html .= '</span>';

		$html .= '</div>'; // .hli-post__meta

		if ( $summary ) {
			$html .= '<p class="hli-post__excerpt">' . esc_html( $summary ) . '</p>';
		}

		$html .= '</article>';

		return $html;
	}
endif;


if ( ! function_exists( 'dnte_insights_tax_query' ) ) :
	/**
	 * Builds a tax_query clause from a comma-separated list of slugs or IDs.
	 *
	 * Terms are matched by ID only when every token is numeric; a single
	 * non-numeric token makes the whole list slugs, since mixing the two in one
	 * clause is not something WP_Query supports.
	 *
	 * @param string $tokens   Comma-separated slugs or IDs.
	 * @param string $taxonomy Taxonomy name.
	 * @return array|null A tax_query array, or null when there is nothing to filter on.
	 */
	function dnte_insights_tax_query( $tokens, $taxonomy ) {
		$list = array_filter( array_map( 'trim', explode( ',', (string) $tokens ) ), 'strlen' );

		if ( ! $list || ! taxonomy_exists( $taxonomy ) ) {
			return null;
		}

		$all_numeric = count( array_filter( $list, 'is_numeric' ) ) === count( $list );

		return array(
			array(
				'taxonomy' => $taxonomy,
				'field'    => $all_numeric ? 'term_id' : 'slug',
				'terms'    => $all_numeric ? array_map( 'absint', $list ) : array_map( 'sanitize_title', $list ),
			),
		);
	}
endif;


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_latest_insights_shortcode' ) ) :
	/**
	 * [latest_insights events="2" posts="8" per_slide="2" type="" category="" order="DESC" orderby="date"]
	 *
	 * `events`    — event cards on the left, 0-6 (default 2). 0 hides the column.
	 * `posts`     — blog posts fed into the carousel, 0-30 (default 8). 0 hides it.
	 * `per_slide` — posts shown per carousel slide, 1-4 (default 2).
	 * `type`      — comma-separated event-type slugs or IDs; omit for all.
	 * `category`  — comma-separated post category slugs or IDs; omit for all.
	 * `order`     — ASC or DESC (default DESC). Applies to both queries.
	 * `orderby`   — any WP_Query orderby value (default date). Applies to both.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	function dnte_latest_insights_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'events'    => 2,
				'posts'     => 8,
				'per_slide' => 2,
				'type'      => '',
				'category'  => '',
				'order'     => 'DESC',
				'orderby'   => 'date',
			),
			$atts,
			'latest_insights'
		);

		$event_count = min( 6, max( 0, (int) $atts['events'] ) );
		$post_count  = min( 30, max( 0, (int) $atts['posts'] ) );
		$per_slide   = min( 4, max( 1, (int) $atts['per_slide'] ) );
		$order       = 'ASC' === strtoupper( (string) $atts['order'] ) ? 'ASC' : 'DESC';
		$orderby     = sanitize_key( $atts['orderby'] );

		// ── Left column: events ────────────────────────────────────────────────
		$events_html = '';

		if ( $event_count && post_type_exists( 'event' ) ) {
			$args = array(
				'post_type'           => 'event',
				'post_status'         => 'publish',
				'posts_per_page'      => $event_count,
				'order'               => $order,
				'orderby'             => $orderby,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			);

			$tax_query = dnte_insights_tax_query( $atts['type'], 'event-type' );
			if ( $tax_query ) {
				$args['tax_query'] = $tax_query;
			}

			$events = new WP_Query( $args );

			while ( $events->have_posts() ) {
				$events->the_post();
				$events_html .= dnte_insights_render_event( get_the_ID() );
			}

			wp_reset_postdata();
		}

		// ── Right column: post carousel ────────────────────────────────────────
		$slides      = array();
		$post_cards  = array();

		if ( $post_count ) {
			$args = array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $post_count,
				'order'               => $order,
				'orderby'             => $orderby,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			);

			$tax_query = dnte_insights_tax_query( $atts['category'], 'category' );
			if ( $tax_query ) {
				$args['tax_query'] = $tax_query;
			}

			$posts = new WP_Query( $args );

			while ( $posts->have_posts() ) {
				$posts->the_post();
				$post_cards[] = dnte_insights_render_post( get_the_ID() );
			}

			wp_reset_postdata();

			// A slide holds `per_slide` posts; the last one may be short.
			$slides = array_chunk( $post_cards, $per_slide );
		}

		if ( ! $events_html && ! $slides ) {
			return '<p class="hli-empty">' . esc_html__( 'Nothing to show yet.', 'dentist-exchange' ) . '</p>';
		}

		dnte_insights_enqueue_assets( (bool) $slides );

		$classes = 'hli';
		if ( ! $events_html ) {
			$classes .= ' hli--no-events';
		}
		if ( ! $slides ) {
			$classes .= ' hli--no-posts';
		}

		$html = '<div class="' . esc_attr( $classes ) . '">';

		if ( $events_html ) {
			$html .= '<div class="hli__events">' . $events_html . '</div>';
		}

		if ( $slides ) {
			// The slide count sits on the wrapper so the progress bar can size its
			// first step in CSS, before the carousel script has run.
			$html .= '<div class="hli__posts" style="--hli-slide-count:' . count( $slides ) . '">';

			/*
			 * The bar is decorative — the dot pagination below is the real
			 * control and is what assistive tech announces, so this is hidden
			 * rather than duplicated as a progressbar role.
			 */
			$html .= '<div class="hli-progress" aria-hidden="true"><span class="hli-progress__fill"></span></div>';

			$html .= '<div class="hli-carousel swiper">';
			$html .= '<div class="swiper-wrapper">';

			foreach ( $slides as $slide ) {
				$html .= '<div class="swiper-slide"><div class="hli-slide">' . implode( '', $slide ) . '</div></div>';
			}

			$html .= '</div>'; // .swiper-wrapper
			$html .= '</div>'; // .swiper

			$html .= '<div class="hli-pagination"></div>';
			$html .= '</div>'; // .hli__posts
		}

		$html .= '</div>'; // .hli

		return $html;
	}
endif;
add_shortcode( 'latest_insights', 'dnte_latest_insights_shortcode' );
