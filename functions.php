<?php
/**
 * Dentist Exchange — Theme Functions
 *
 * This file is intentionally kept as a loader only.
 * All feature logic lives in the files under inc/.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 * @package Dentist_Exchange
 * @since   1.0
 */

// ── Theme setup ────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/setup.php' );      // Post formats, editor style

// ── Asset enqueueing ───────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/enqueue.php' );    // Frontend & block styles

// ── Content types ──────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/post-types.php' ); // Open Role post type

// ── Blocks ─────────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/blocks.php' );        // Custom block registration
require_once get_theme_file_path( 'inc/block-styles.php' );  // Core block style variations
require_once get_theme_file_path( 'inc/extensions.php' );    // Block editor extensions

// ── Icon library ───────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/my-icons.php' );      // Site-wide saved SVG icons

// ── Editor taxonomy ────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/categories.php' ); // Block & pattern categories

// ── Block bindings ─────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/bindings.php' );   // Dynamic block data sources

// ── Form panel ─────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/form.php' );       // Slide-in form panel & settings

// ── Shortcodes ─────────────────────────────────────────────────────────────────
require_once get_theme_file_path( 'inc/shortcode.php' );         // Posts grid shortcode
require_once get_theme_file_path( 'inc/shortcode-roles.php' );   // Opening roles shortcode
require_once get_theme_file_path( 'inc/shortcodes-page.php' );  // Appearance → Dentist Exchange reference page


/**
 * Set WordPress excerpt length to 25 words.
 */
function dnte_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'dnte_excerpt_length' );

/**
 * Customize excerpt more text.
 */
function dnte_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'dnte_excerpt_more' );