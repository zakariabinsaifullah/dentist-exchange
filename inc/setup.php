<?php
/**
 * Theme Setup
 *
 * Handles core theme setup tasks that run on the `after_setup_theme` hook:
 * post format support and editor stylesheet registration.
 *
 * @package Dentist_Exchange
 */

if ( ! function_exists( 'dnte_post_format_setup' ) ) :
	/**
	 * Declares support for the post formats used by this theme.
	 */
	function dnte_post_format_setup() {
		add_theme_support(
			'post-formats',
			array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' )
		);
	}
endif;
add_action( 'after_setup_theme', 'dnte_post_format_setup' );


if ( ! function_exists( 'dnte_editor_style' ) ) :
	/**
	 * Loads the editor stylesheet so the editing canvas matches the front end.
	 */
	function dnte_editor_style() {
		add_editor_style( 'assets/css/editor-style.css' );
	}
endif;
add_action( 'after_setup_theme', 'dnte_editor_style' );
