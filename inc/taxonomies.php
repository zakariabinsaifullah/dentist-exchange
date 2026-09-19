<?php
/**
 * Custom Taxonomies
 *
 * Registers the taxonomies owned by this theme.
 *
 * @package Dentist_Exchange
 */

if ( ! function_exists( 'dnte_register_event_type_taxonomy' ) ) :
	/**
	 * Registers the Type taxonomy for the Event post type.
	 *
	 * Hierarchical, so it presents the checkbox UI that categories use rather
	 * than the free-form tag input.
	 */
	function dnte_register_event_type_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Types', 'taxonomy general name', 'dentist-exchange' ),
			'singular_name'              => _x( 'Type', 'taxonomy singular name', 'dentist-exchange' ),
			'menu_name'                  => __( 'Types', 'dentist-exchange' ),
			'all_items'                  => __( 'All Types', 'dentist-exchange' ),
			'edit_item'                  => __( 'Edit Type', 'dentist-exchange' ),
			'view_item'                  => __( 'View Type', 'dentist-exchange' ),
			'update_item'                => __( 'Update Type', 'dentist-exchange' ),
			'add_new_item'               => __( 'Add New Type', 'dentist-exchange' ),
			'new_item_name'              => __( 'New Type Name', 'dentist-exchange' ),
			'parent_item'                => __( 'Parent Type', 'dentist-exchange' ),
			'parent_item_colon'          => __( 'Parent Type:', 'dentist-exchange' ),
			'search_items'               => __( 'Search Types', 'dentist-exchange' ),
			'popular_items'              => __( 'Popular Types', 'dentist-exchange' ),
			'separate_items_with_commas' => __( 'Separate types with commas', 'dentist-exchange' ),
			'add_or_remove_items'        => __( 'Add or remove types', 'dentist-exchange' ),
			'choose_from_most_used'      => __( 'Choose from the most used types', 'dentist-exchange' ),
			'not_found'                  => __( 'No types found.', 'dentist-exchange' ),
			'no_terms'                   => __( 'No types', 'dentist-exchange' ),
			'back_to_items'              => __( '&larr; Go to Types', 'dentist-exchange' ),
			'item_link'                  => _x( 'Type Link', 'navigation link block title', 'dentist-exchange' ),
			'item_link_description'      => _x( 'A link to a type.', 'navigation link block description', 'dentist-exchange' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Groups events by type.', 'dentist-exchange' ),
			'public'             => true,
			'publicly_queryable' => true,
			'hierarchical'       => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'show_tagcloud'      => false,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'         => 'event-type',
				'with_front'   => false,
				'hierarchical' => true,
			),
		);

		register_taxonomy( 'event-type', array( 'event' ), $args );
	}
endif;
add_action( 'init', 'dnte_register_event_type_taxonomy' );
