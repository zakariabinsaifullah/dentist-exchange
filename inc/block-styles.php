<?php
/**
 * Core Block Styles
 *
 * Registers custom style variations for core (and third-party) blocks.
 *
 * @package Dentist_Exchange
 */

if ( ! function_exists( 'dnte_block_styles' ) ) :
	/**
	 * Registers all custom block style variations for the theme.
	 */
	function dnte_block_styles() {
		register_block_style(
			'core/group',
			array(
				'name'  => 'wrap-mobile',
				'label' => __( 'Wrap Mobile', 'dentist-exchange' ),
			)
		);


		register_block_style(
			'core/button',
			array(
				'name'  => 'alternative',
				'label' => __( 'Alternative', 'dentist-exchange' ),
			)
		);

		register_block_style(
			'core/button',
			array(
				'name'  => 'link',
				'label' => __( 'Link', 'dentist-exchange' ),
			)
		);
	}
endif;
add_action( 'init', 'dnte_block_styles' );
