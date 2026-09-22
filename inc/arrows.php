<?php
/**
 * Story Card Connectors
 *
 * The hand-drawn arrows that lead the eye from one Story Card to the next.
 *
 * Each arrow is a single filled path — a brush *silhouette*, not a stroked
 * line — so there is no centreline for stroke-dasharray to draw along. The
 * reveal is instead an angled mask wipe (see src/blocks/story-cards/style.scss),
 * and each arrow carries the one number that wipe needs: the direction its
 * stroke travels, tail to head, as a CSS gradient angle.
 *
 * The artwork is the single source of truth: drop a new SVG into
 * assets/svg/arrows/ and it appears in the editor's picker with no code change.
 * Only its sweep angle is worth adding below, and it falls back to a sensible
 * default without one.
 *
 * Mirrors the registry in inc/annotations.php, which solves the same problem
 * for heading strokes.
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Directory holding the arrow artwork, relative to the theme root.
 */
const DNTE_ARROW_DIR = 'assets/svg/arrows';

/**
 * Fallback sweep angle for an arrow with no entry in the map below.
 */
const DNTE_ARROW_DEFAULT_ANGLE = 180;

if ( ! function_exists( 'dnte_arrow_sweep_angles' ) ) :
	/**
	 * Direction each arrow's stroke travels, as a CSS gradient angle.
	 *
	 * CSS angles run clockwise from "to top" — 90 is to the right, 180 to the
	 * bottom, 270 to the left. The wipe reveals the arrow along this direction,
	 * so an arrow that appears to draw backwards just needs its angle turned
	 * 180 degrees.
	 *
	 * @return array<string,int> Slug => angle in degrees.
	 */
	function dnte_arrow_sweep_angles() {
		return apply_filters(
			'dnte_arrow_sweep_angles',
			array(
				'hook-down'     => 215, // Starts top right, hooks down and left.
				'squiggle-down' => 190, // Broken stroke running downward.
				'elbow-right'   => 110, // Drops, then turns and runs right.
				'hook-up'       => 325, // Mirror of hook-down, travelling up and left.
				'long-curve'    => 170, // The long trailing curve out of the section.
			)
		);
	}
endif;

if ( ! function_exists( 'dnte_arrow_variants' ) ) :
	/**
	 * Every arrow available to the Story Card block.
	 *
	 * Scanned once per request and cached in a static.
	 *
	 * @return array<string,array{label:string,path:string,angle:int}>
	 */
	function dnte_arrow_variants() {
		static $variants = null;

		if ( null !== $variants ) {
			return $variants;
		}

		$variants = array();
		$files    = glob( get_theme_file_path( DNTE_ARROW_DIR ) . '/*.svg' );

		if ( empty( $files ) ) {
			return $variants;
		}

		sort( $files );

		$angles = dnte_arrow_sweep_angles();

		foreach ( $files as $file ) {
			$base = basename( $file, '.svg' );
			$slug = sanitize_key( $base );

			// A filename that does not survive sanitize_key() could not be
			// matched back from a block attribute anyway, so skip it outright.
			if ( '' === $slug || $slug !== $base ) {
				continue;
			}

			$variants[ $slug ] = array(
				'label' => ucwords( str_replace( array( '-', '_' ), ' ', $slug ) ),
				'path'  => $file,
				'angle' => isset( $angles[ $slug ] ) ? (int) $angles[ $slug ] : DNTE_ARROW_DEFAULT_ANGLE,
			);
		}

		return $variants;
	}
endif;

if ( ! function_exists( 'dnte_arrow_svg' ) ) :
	/**
	 * The inline SVG markup for one arrow.
	 *
	 * The slug arrives from a block attribute, so it is sanitised and then
	 * looked up in the registry — never concatenated into a path. The files
	 * themselves are theme-owned and trusted, so they are returned as-is.
	 *
	 * @param string $slug Arrow slug.
	 * @return string SVG markup, or '' when the slug is unknown.
	 */
	function dnte_arrow_svg( $slug ) {
		static $cache = array();

		$slug = sanitize_key( (string) $slug );

		if ( isset( $cache[ $slug ] ) ) {
			return $cache[ $slug ];
		}

		$variants = dnte_arrow_variants();

		if ( ! isset( $variants[ $slug ] ) ) {
			return '';
		}

		$cache[ $slug ] = (string) file_get_contents( $variants[ $slug ]['path'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Theme-owned file, not a remote request.

		return $cache[ $slug ];
	}
endif;

if ( ! function_exists( 'dnte_arrow_angle' ) ) :
	/**
	 * Sweep angle for one arrow, in degrees.
	 *
	 * @param string $slug Arrow slug.
	 * @return int
	 */
	function dnte_arrow_angle( $slug ) {
		$variants = dnte_arrow_variants();
		$slug     = sanitize_key( (string) $slug );

		return isset( $variants[ $slug ] ) ? (int) $variants[ $slug ]['angle'] : DNTE_ARROW_DEFAULT_ANGLE;
	}
endif;

if ( ! function_exists( 'dnte_arrow_editor_data' ) ) :
	/**
	 * Hands the arrow registry to the editor so the picker can preview the real
	 * artwork rather than a hardcoded copy of it.
	 *
	 * The markup travels with it — five files of a few KB — because the picker
	 * renders each arrow inline, exactly as the frontend does.
	 */
	function dnte_arrow_editor_data() {
		$variants = dnte_arrow_variants();

		if ( empty( $variants ) ) {
			return;
		}

		$payload = array();

		foreach ( $variants as $slug => $variant ) {
			$payload[] = array(
				'slug'  => $slug,
				'label' => $variant['label'],
				'angle' => $variant['angle'],
				'svg'   => dnte_arrow_svg( $slug ),
			);
		}

		wp_add_inline_script(
			'dnte-story-card-editor-script',
			'window.dnteArrows = ' . wp_json_encode( $payload ) . ';',
			'before'
		);
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_arrow_editor_data' );
