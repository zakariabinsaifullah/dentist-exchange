<?php
/**
 * Custom Post Types
 *
 * Registers the content types owned by this theme.
 *
 * @package Dentist_Exchange
 */

if ( ! function_exists( 'dnte_register_event_post_type' ) ) :
	/**
	 * Registers the Event post type.
	 *
	 * `show_in_rest` is required for the block editor to load; without it the
	 * `editor` support below falls back to the classic editor.
	 */
	function dnte_register_event_post_type() {
		$labels = array(
			'name'                     => _x( 'Events', 'post type general name', 'dentist-exchange' ),
			'singular_name'            => _x( 'Event', 'post type singular name', 'dentist-exchange' ),
			'menu_name'                => _x( 'Events', 'admin menu', 'dentist-exchange' ),
			'name_admin_bar'           => _x( 'Event', 'add new on admin bar', 'dentist-exchange' ),
			'add_new'                  => __( 'Add Event', 'dentist-exchange' ),
			'add_new_item'             => __( 'Add New Event', 'dentist-exchange' ),
			'new_item'                 => __( 'New Event', 'dentist-exchange' ),
			'edit_item'                => __( 'Edit Event', 'dentist-exchange' ),
			'view_item'                => __( 'View Event', 'dentist-exchange' ),
			'view_items'               => __( 'View Events', 'dentist-exchange' ),
			'all_items'                => __( 'All Events', 'dentist-exchange' ),
			'search_items'             => __( 'Search Events', 'dentist-exchange' ),
			'parent_item_colon'        => __( 'Parent Events:', 'dentist-exchange' ),
			'not_found'                => __( 'No events found.', 'dentist-exchange' ),
			'not_found_in_trash'       => __( 'No events found in Trash.', 'dentist-exchange' ),
			'archives'                 => __( 'Event Archives', 'dentist-exchange' ),
			'attributes'               => __( 'Event Attributes', 'dentist-exchange' ),
			'insert_into_item'         => __( 'Insert into event', 'dentist-exchange' ),
			'uploaded_to_this_item'    => __( 'Uploaded to this event', 'dentist-exchange' ),
			'featured_image'           => __( 'Event Image', 'dentist-exchange' ),
			'set_featured_image'       => __( 'Set event image', 'dentist-exchange' ),
			'remove_featured_image'    => __( 'Remove event image', 'dentist-exchange' ),
			'use_featured_image'       => __( 'Use as event image', 'dentist-exchange' ),
			'filter_items_list'        => __( 'Filter events list', 'dentist-exchange' ),
			'items_list_navigation'    => __( 'Events list navigation', 'dentist-exchange' ),
			'items_list'               => __( 'Events list', 'dentist-exchange' ),
			'item_published'           => __( 'Event published.', 'dentist-exchange' ),
			'item_updated'             => __( 'Event updated.', 'dentist-exchange' ),
			'item_scheduled'           => __( 'Event scheduled.', 'dentist-exchange' ),
			'item_reverted_to_draft'   => __( 'Event reverted to draft.', 'dentist-exchange' ),
			'item_link'                => _x( 'Event Link', 'navigation link block title', 'dentist-exchange' ),
			'item_link_description'    => _x( 'A link to an event.', 'navigation link block description', 'dentist-exchange' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Events organised by Dentist Exchange.', 'dentist-exchange' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_rest'       => true,
			'query_var'          => true,
			'rewrite'            => array(
				'slug'       => 'events',
				'with_front' => false,
			),
			'capability_type'    => 'post',
			'has_archive'        => 'events',
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-calendar-alt',
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		);

		register_post_type( 'event', $args );
	}
endif;
add_action( 'init', 'dnte_register_event_post_type' );


if ( ! function_exists( 'dnte_event_title_placeholder' ) ) :
	/**
	 * Replaces the "Add title" placeholder on the Event editing screen.
	 *
	 * Core passes this filter through to the block editor as
	 * `titlePlaceholder`, so the one filter covers both editors.
	 *
	 * @param string  $text Current placeholder text.
	 * @param WP_Post $post Post being edited.
	 * @return string
	 */
	function dnte_event_title_placeholder( $text, $post ) {
		if ( $post instanceof WP_Post && 'event' === $post->post_type ) {
			return __( 'Event title', 'dentist-exchange' );
		}

		return $text;
	}
endif;
add_filter( 'enter_title_here', 'dnte_event_title_placeholder', 10, 2 );


if ( ! function_exists( 'dnte_register_team_post_type' ) ) :
	/**
	 * Registers the Team post type.
	 *
	 * Team members have no front-end single view, so `publicly_queryable`
	 * and `query_var` stay off. `show_in_rest` keeps the block editor
	 * available in the admin.
	 */
	function dnte_register_team_post_type() {
		$labels = array(
			'name'                  => _x( 'Team Members', 'post type general name', 'dentist-exchange' ),
			'singular_name'         => _x( 'Team Member', 'post type singular name', 'dentist-exchange' ),
			'menu_name'             => _x( 'Team', 'admin menu', 'dentist-exchange' ),
			'name_admin_bar'        => _x( 'Team Member', 'add new on admin bar', 'dentist-exchange' ),
			'add_new'               => __( 'Add Team Member', 'dentist-exchange' ),
			'add_new_item'          => __( 'Add New Member', 'dentist-exchange' ),
			'new_item'              => __( 'New Team Member', 'dentist-exchange' ),
			'edit_item'             => __( 'Edit Team Member', 'dentist-exchange' ),
			'view_item'             => __( 'View Team Member', 'dentist-exchange' ),
			'view_items'            => __( 'View Team Members', 'dentist-exchange' ),
			'all_items'             => __( 'All Team Members', 'dentist-exchange' ),
			'search_items'          => __( 'Search Team Members', 'dentist-exchange' ),
			'parent_item_colon'     => __( 'Parent Team Members:', 'dentist-exchange' ),
			'not_found'             => __( 'No team members found.', 'dentist-exchange' ),
			'not_found_in_trash'    => __( 'No team members found in Trash.', 'dentist-exchange' ),
			'archives'              => __( 'Team Member Archives', 'dentist-exchange' ),
			'attributes'            => __( 'Team Member Attributes', 'dentist-exchange' ),
			'insert_into_item'      => __( 'Insert into team member', 'dentist-exchange' ),
			'uploaded_to_this_item' => __( 'Uploaded to this team member', 'dentist-exchange' ),
			'featured_image'        => __( 'Team Member Photo', 'dentist-exchange' ),
			'set_featured_image'    => __( 'Set team member photo', 'dentist-exchange' ),
			'remove_featured_image' => __( 'Remove team member photo', 'dentist-exchange' ),
			'use_featured_image'    => __( 'Use as team member photo', 'dentist-exchange' ),
			'filter_items_list'     => __( 'Filter team members list', 'dentist-exchange' ),
			'items_list_navigation' => __( 'Team members list navigation', 'dentist-exchange' ),
			'items_list'            => __( 'Team members list', 'dentist-exchange' ),
			'item_published'        => __( 'Team member published.', 'dentist-exchange' ),
			'item_updated'          => __( 'Team member updated.', 'dentist-exchange' ),
			'item_scheduled'        => __( 'Team member scheduled.', 'dentist-exchange' ),
			'item_reverted_to_draft' => __( 'Team member reverted to draft.', 'dentist-exchange' ),
			'item_link'             => _x( 'Team Member Link', 'navigation link block title', 'dentist-exchange' ),
			'item_link_description' => _x( 'A link to a team member.', 'navigation link block description', 'dentist-exchange' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Team members of Dentist Exchange.', 'dentist-exchange' ),
			'public'             => false,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => false,
			'show_in_rest'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 21,
			'menu_icon'          => 'dashicons-groups',
			'supports'           => array( 'title', 'thumbnail' ),
		);

		register_post_type( 'team', $args );
	}
endif;
add_action( 'init', 'dnte_register_team_post_type' );


if ( ! function_exists( 'dnte_team_title_placeholder' ) ) :
	/**
	 * Replaces the "Add title" placeholder on the Team editing screen.
	 *
	 * Core passes this filter through to the block editor as
	 * `titlePlaceholder`, so the one filter covers both editors.
	 *
	 * @param string  $text Current placeholder text.
	 * @param WP_Post $post Post being edited.
	 * @return string
	 */
	function dnte_team_title_placeholder( $text, $post ) {
		if ( $post instanceof WP_Post && 'team' === $post->post_type ) {
			return __( 'Enter name', 'dentist-exchange' );
		}

		return $text;
	}
endif;
add_filter( 'enter_title_here', 'dnte_team_title_placeholder', 10, 2 );


if ( ! function_exists( 'dnte_register_team_meta' ) ) :
	/**
	 * Registers the designation and short intro meta fields for the Team post type.
	 *
	 * `show_in_rest` exposes the fields through the REST API so they can be
	 * bound to blocks in the editor and queried from custom templates.
	 */
	function dnte_register_team_meta() {
		register_post_meta(
			'team',
			'dnte_designation',
			array(
				'type'              => 'string',
				'description'       => __( 'Job title or designation of the team member.', 'dentist-exchange' ),
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'show_in_rest'      => true,
			)
		);

		register_post_meta(
			'team',
			'dnte_short_intro',
			array(
				'type'              => 'string',
				'description'       => __( 'Short introduction of the team member.', 'dentist-exchange' ),
				'single'            => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'show_in_rest'      => true,
			)
		);
	}
endif;
add_action( 'init', 'dnte_register_team_meta' );


if ( ! function_exists( 'dnte_add_team_meta_box' ) ) :
	/**
	 * Adds the Team Details meta box to the Team editing screen.
	 */
	function dnte_add_team_meta_box() {
		add_meta_box(
			'dnte-team-details',
			__( 'Team Details', 'dentist-exchange' ),
			'dnte_render_team_meta_box',
			'team',
			'normal',
			'default'
		);
	}
endif;
add_action( 'add_meta_boxes', 'dnte_add_team_meta_box' );


if ( ! function_exists( 'dnte_render_team_meta_box' ) ) :
	/**
	 * Renders the designation and short intro fields inside the meta box.
	 *
	 * @param WP_Post $post Current post.
	 */
	function dnte_render_team_meta_box( $post ) {
		$designation = get_post_meta( $post->ID, 'dnte_designation', true );
		$short_intro = get_post_meta( $post->ID, 'dnte_short_intro', true );

		wp_nonce_field( 'dnte_save_team_meta', 'dnte_team_meta_nonce' );
		?>
		<p>
			<label for="dnte-designation"><?php esc_html_e( 'Designation', 'dentist-exchange' ); ?></label>
			<input
				type="text"
				id="dnte-designation"
				name="dnte_designation"
				value="<?php echo esc_attr( $designation ); ?>"
				class="widefat"
			/>
		</p>
		<p>
			<label for="dnte-short-intro"><?php esc_html_e( 'Short Intro', 'dentist-exchange' ); ?></label>
			<textarea
				id="dnte-short-intro"
				name="dnte_short_intro"
				class="widefat"
				rows="4"
			><?php echo esc_textarea( $short_intro ); ?></textarea>
		</p>
		<?php
	}
endif;


if ( ! function_exists( 'dnte_save_team_meta' ) ) :
	/**
	 * Saves the designation and short intro meta fields when a Team member is saved.
	 *
	 * @param int $post_id Post ID.
	 */
	function dnte_save_team_meta( $post_id ) {
		if (
			! isset( $_POST['dnte_team_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['dnte_team_meta_nonce'] ), 'dnte_save_team_meta' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['dnte_designation'] ) ) {
			update_post_meta( $post_id, 'dnte_designation', sanitize_text_field( wp_unslash( $_POST['dnte_designation'] ) ) );
		}

		if ( isset( $_POST['dnte_short_intro'] ) ) {
			update_post_meta( $post_id, 'dnte_short_intro', sanitize_textarea_field( wp_unslash( $_POST['dnte_short_intro'] ) ) );
		}
	}
endif;
add_action( 'save_post_team', 'dnte_save_team_meta' );


// ── ID column on the Team admin screen ────────────────────────────────────────

if ( ! function_exists( 'dnte_team_columns' ) ) :
	/**
	 * Adds an "ID" column to the Team Members list table.
	 *
	 * @param  array $columns Existing column definitions.
	 * @return array
	 */
	function dnte_team_columns( $columns ) {
		$columns['dnte_team_id'] = __( 'ID', 'dentist-exchange' );
		return $columns;
	}
endif;
add_filter( 'manage_team_posts_columns', 'dnte_team_columns' );


if ( ! function_exists( 'dnte_team_column_content' ) ) :
	/**
	 * Outputs the post ID for the custom column.
	 *
	 * @param  string $column_name Column key.
	 * @param  int    $post_id     Post ID.
	 * @return void
	 */
	function dnte_team_column_content( $column_name, $post_id ) {
		if ( 'dnte_team_id' === $column_name ) {
			echo esc_html( (string) $post_id );
		}
	}
endif;
add_action( 'manage_team_posts_custom_column', 'dnte_team_column_content', 10, 2 );


/**
 * Caps the ID column width on the Team Members and Partners list tables.
 */
function dnte_team_column_width() {
	?>
	<style>
		.wp-list-table .column-dnte_team_id,
		.wp-list-table .column-dnte_partner_id {
			width: 70px;
			max-width: 70px;
		}
	</style>
	<?php
}
add_action( 'admin_head-edit.php', 'dnte_team_column_width' );


if ( ! function_exists( 'dnte_register_partner_post_type' ) ) :
	/**
	 * Registers the Partner post type.
	 *
	 * Partners have no front-end single view, so `publicly_queryable`,
	 * `query_var` and `rewrite` stay off. `show_in_rest` keeps the block
	 * editor available in the admin.
	 */
	function dnte_register_partner_post_type() {
		$labels = array(
			'name'                   => _x( 'Partners', 'post type general name', 'dentist-exchange' ),
			'singular_name'          => _x( 'Partner', 'post type singular name', 'dentist-exchange' ),
			'menu_name'              => _x( 'Partners', 'admin menu', 'dentist-exchange' ),
			'name_admin_bar'         => _x( 'Partner', 'add new on admin bar', 'dentist-exchange' ),
			'add_new'                => __( 'Add Partner', 'dentist-exchange' ),
			'add_new_item'           => __( 'Add New Partner', 'dentist-exchange' ),
			'new_item'               => __( 'New Partner', 'dentist-exchange' ),
			'edit_item'              => __( 'Edit Partner', 'dentist-exchange' ),
			'view_item'              => __( 'View Partner', 'dentist-exchange' ),
			'view_items'             => __( 'View Partners', 'dentist-exchange' ),
			'all_items'              => __( 'All Partners', 'dentist-exchange' ),
			'search_items'           => __( 'Search Partners', 'dentist-exchange' ),
			'parent_item_colon'      => __( 'Parent Partners:', 'dentist-exchange' ),
			'not_found'              => __( 'No partners found.', 'dentist-exchange' ),
			'not_found_in_trash'     => __( 'No partners found in Trash.', 'dentist-exchange' ),
			'archives'               => __( 'Partner Archives', 'dentist-exchange' ),
			'attributes'             => __( 'Partner Attributes', 'dentist-exchange' ),
			'insert_into_item'       => __( 'Insert into partner', 'dentist-exchange' ),
			'uploaded_to_this_item'  => __( 'Uploaded to this partner', 'dentist-exchange' ),
			'featured_image'         => __( 'Avatar Photo', 'dentist-exchange' ),
			'set_featured_image'     => __( 'Set avatar photo', 'dentist-exchange' ),
			'remove_featured_image'  => __( 'Remove avatar photo', 'dentist-exchange' ),
			'use_featured_image'     => __( 'Use as avatar photo', 'dentist-exchange' ),
			'filter_items_list'      => __( 'Filter partners list', 'dentist-exchange' ),
			'items_list_navigation'  => __( 'Partners list navigation', 'dentist-exchange' ),
			'items_list'             => __( 'Partners list', 'dentist-exchange' ),
			'item_published'         => __( 'Partner published.', 'dentist-exchange' ),
			'item_updated'           => __( 'Partner updated.', 'dentist-exchange' ),
			'item_scheduled'         => __( 'Partner scheduled.', 'dentist-exchange' ),
			'item_reverted_to_draft' => __( 'Partner reverted to draft.', 'dentist-exchange' ),
			'item_link'              => _x( 'Partner Link', 'navigation link block title', 'dentist-exchange' ),
			'item_link_description'  => _x( 'A link to a partner.', 'navigation link block description', 'dentist-exchange' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Partners of Dentist Exchange.', 'dentist-exchange' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 23,
			'menu_icon'           => 'dashicons-networking',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		);

		register_post_type( 'partner', $args );
	}
endif;
add_action( 'init', 'dnte_register_partner_post_type' );


if ( ! function_exists( 'dnte_partner_title_placeholder' ) ) :
	/**
	 * Replaces the "Add title" placeholder on the Partner editing screen.
	 *
	 * Core passes this filter through to the block editor as
	 * `titlePlaceholder`, so the one filter covers both editors.
	 *
	 * @param string  $text Current placeholder text.
	 * @param WP_Post $post Post being edited.
	 * @return string
	 */
	function dnte_partner_title_placeholder( $text, $post ) {
		if ( $post instanceof WP_Post && 'partner' === $post->post_type ) {
			return __( 'Enter name', 'dentist-exchange' );
		}

		return $text;
	}
endif;
add_filter( 'enter_title_here', 'dnte_partner_title_placeholder', 10, 2 );


if ( ! function_exists( 'dnte_register_partner_meta' ) ) :
	/**
	 * Registers the designation meta field for the Partner post type.
	 *
	 * `show_in_rest` exposes the field through the REST API so it can be
	 * bound to blocks in the editor and queried from custom templates.
	 */
	function dnte_register_partner_meta() {
		register_post_meta(
			'partner',
			'dnte_partner_designation',
			array(
				'type'              => 'string',
				'description'       => __( 'Job title or designation of the partner.', 'dentist-exchange' ),
				'single'            => true,
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'show_in_rest'      => true,
			)
		);
	}
endif;
add_action( 'init', 'dnte_register_partner_meta' );


if ( ! function_exists( 'dnte_add_partner_meta_box' ) ) :
	/**
	 * Adds the Designation meta box to the Partner editing screen.
	 *
	 * The `side` context puts it in the right-hand sidebar, below the
	 * core document panels, in both editors.
	 */
	function dnte_add_partner_meta_box() {
		add_meta_box(
			'dnte-partner-details',
			__( 'Designation', 'dentist-exchange' ),
			'dnte_render_partner_meta_box',
			'partner',
			'side',
			'default'
		);
	}
endif;
add_action( 'add_meta_boxes', 'dnte_add_partner_meta_box' );


if ( ! function_exists( 'dnte_render_partner_meta_box' ) ) :
	/**
	 * Renders the designation field inside the meta box.
	 *
	 * @param WP_Post $post Current post.
	 */
	function dnte_render_partner_meta_box( $post ) {
		$designation = get_post_meta( $post->ID, 'dnte_partner_designation', true );

		wp_nonce_field( 'dnte_save_partner_meta', 'dnte_partner_meta_nonce' );
		?>
		<style>
			/* A label is inline by default, so the margin needs the block display to take effect. */
			#dnte-partner-details label {
				display: block;
				margin-bottom: 6px;
			}
		</style>
		<p>
			<label for="dnte-partner-designation"><?php esc_html_e( 'Designation', 'dentist-exchange' ); ?></label>
			<input
				type="text"
				id="dnte-partner-designation"
				name="dnte_partner_designation"
				value="<?php echo esc_attr( $designation ); ?>"
				class="widefat"
				placeholder="<?php esc_attr_e( 'e.g. Managing Partner', 'dentist-exchange' ); ?>"
			/>
		</p>
		<?php
	}
endif;


if ( ! function_exists( 'dnte_save_partner_meta' ) ) :
	/**
	 * Saves the designation meta field when a Partner is saved.
	 *
	 * @param int $post_id Post ID.
	 */
	function dnte_save_partner_meta( $post_id ) {
		if (
			! isset( $_POST['dnte_partner_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['dnte_partner_meta_nonce'] ), 'dnte_save_partner_meta' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( isset( $_POST['dnte_partner_designation'] ) ) {
			update_post_meta( $post_id, 'dnte_partner_designation', sanitize_text_field( wp_unslash( $_POST['dnte_partner_designation'] ) ) );
		}
	}
endif;
add_action( 'save_post_partner', 'dnte_save_partner_meta' );


// ── ID column on the Partners admin screen ────────────────────────────────────

if ( ! function_exists( 'dnte_partner_columns' ) ) :
	/**
	 * Adds an "ID" column to the Partners list table.
	 *
	 * @param  array $columns Existing column definitions.
	 * @return array
	 */
	function dnte_partner_columns( $columns ) {
		$columns['dnte_partner_id'] = __( 'ID', 'dentist-exchange' );
		return $columns;
	}
endif;
add_filter( 'manage_partner_posts_columns', 'dnte_partner_columns' );


if ( ! function_exists( 'dnte_partner_column_content' ) ) :
	/**
	 * Outputs the post ID for the custom column.
	 *
	 * @param  string $column_name Column key.
	 * @param  int    $post_id     Post ID.
	 * @return void
	 */
	function dnte_partner_column_content( $column_name, $post_id ) {
		if ( 'dnte_partner_id' === $column_name ) {
			echo esc_html( (string) $post_id );
		}
	}
endif;
add_action( 'manage_partner_posts_custom_column', 'dnte_partner_column_content', 10, 2 );


if ( ! function_exists( 'dnte_register_open_role_post_type' ) ) :
	/**
	 * Registers the Open Role post type for job listings.
	 *
	 * Roles have no front-end single view, so `publicly_queryable`, `query_var`
	 * and `rewrite` stay off and no archive is registered. `show_in_rest` keeps
	 * the block editor available in the admin.
	 */
	function dnte_register_open_role_post_type() {
		$labels = array(
			'name'                   => _x( 'Open Roles', 'post type general name', 'dentist-exchange' ),
			'singular_name'          => _x( 'Open Role', 'post type singular name', 'dentist-exchange' ),
			'menu_name'              => _x( 'Open Roles', 'admin menu', 'dentist-exchange' ),
			'name_admin_bar'         => _x( 'Open Role', 'add new on admin bar', 'dentist-exchange' ),
			'add_new'                => __( 'Add Open Role', 'dentist-exchange' ),
			'add_new_item'           => __( 'Add New Open Role', 'dentist-exchange' ),
			'new_item'               => __( 'New Open Role', 'dentist-exchange' ),
			'edit_item'              => __( 'Edit Open Role', 'dentist-exchange' ),
			'view_item'              => __( 'View Open Role', 'dentist-exchange' ),
			'view_items'             => __( 'View Open Roles', 'dentist-exchange' ),
			'all_items'              => __( 'All Open Roles', 'dentist-exchange' ),
			'search_items'           => __( 'Search Open Roles', 'dentist-exchange' ),
			'parent_item_colon'      => __( 'Parent Open Roles:', 'dentist-exchange' ),
			'not_found'              => __( 'No open roles found.', 'dentist-exchange' ),
			'not_found_in_trash'     => __( 'No open roles found in Trash.', 'dentist-exchange' ),
			'archives'               => __( 'Open Role Archives', 'dentist-exchange' ),
			'attributes'             => __( 'Open Role Attributes', 'dentist-exchange' ),
			'insert_into_item'       => __( 'Insert into open role', 'dentist-exchange' ),
			'uploaded_to_this_item'  => __( 'Uploaded to this open role', 'dentist-exchange' ),
			'filter_items_list'      => __( 'Filter open roles list', 'dentist-exchange' ),
			'items_list_navigation'  => __( 'Open roles list navigation', 'dentist-exchange' ),
			'items_list'             => __( 'Open roles list', 'dentist-exchange' ),
			'item_published'         => __( 'Open role published.', 'dentist-exchange' ),
			'item_updated'           => __( 'Open role updated.', 'dentist-exchange' ),
			'item_scheduled'         => __( 'Open role scheduled.', 'dentist-exchange' ),
			'item_reverted_to_draft' => __( 'Open role reverted to draft.', 'dentist-exchange' ),
			'item_link'              => _x( 'Open Role Link', 'navigation link block title', 'dentist-exchange' ),
			'item_link_description'  => _x( 'A link to an open role.', 'navigation link block description', 'dentist-exchange' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Open roles at Dentist Exchange.', 'dentist-exchange' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 22,
			'menu_icon'           => 'dashicons-businessperson',
			'supports'            => array( 'title' ),
		);

		register_post_type( 'open-role', $args );
	}
endif;
add_action( 'init', 'dnte_register_open_role_post_type' );


if ( ! function_exists( 'dnte_open_role_title_placeholder' ) ) :
	/**
	 * Replaces the "Add title" placeholder on the Open Role editing screen.
	 *
	 * Core passes this filter through to the block editor as
	 * `titlePlaceholder`, so the one filter covers both editors.
	 *
	 * @param string  $text Current placeholder text.
	 * @param WP_Post $post Post being edited.
	 * @return string
	 */
	function dnte_open_role_title_placeholder( $text, $post ) {
		if ( $post instanceof WP_Post && 'open-role' === $post->post_type ) {
			return __( 'Role title', 'dentist-exchange' );
		}

		return $text;
	}
endif;
add_filter( 'enter_title_here', 'dnte_open_role_title_placeholder', 10, 2 );


// ── Open Role meta fields ─────────────────────────────────────────────────────

if ( ! function_exists( 'dnte_open_role_type_options' ) ) :
	/**
	 * Employment type choices, keyed by the value stored in meta.
	 *
	 * @return array
	 */
	function dnte_open_role_type_options() {
		return array(
			'full-time'   => __( 'Full Time', 'dentist-exchange' ),
			'part-time'   => __( 'Part Time', 'dentist-exchange' ),
			'hourly'      => __( 'Hourly', 'dentist-exchange' ),
			'contractual' => __( 'Contractual', 'dentist-exchange' ),
			'internship'  => __( 'Internship', 'dentist-exchange' ),
		);
	}
endif;


if ( ! function_exists( 'dnte_open_role_nature_options' ) ) :
	/**
	 * Work arrangement choices, keyed by the value stored in meta.
	 *
	 * @return array
	 */
	function dnte_open_role_nature_options() {
		return array(
			'remote'   => __( 'Remote', 'dentist-exchange' ),
			'in-house' => __( 'In House', 'dentist-exchange' ),
			'hybrid'   => __( 'Hybrid', 'dentist-exchange' ),
		);
	}
endif;


if ( ! function_exists( 'dnte_register_open_role_meta' ) ) :
	/**
	 * Registers the Open Role meta fields.
	 *
	 * `show_in_rest` exposes the fields through the REST API so they can be
	 * bound to blocks in the editor and queried from custom templates.
	 */
	function dnte_register_open_role_meta() {
		$auth = function () {
			return current_user_can( 'edit_posts' );
		};

		$text_fields = array(
			'dnte_role_location'     => __( 'Where the role is based.', 'dentist-exchange' ),
			'dnte_role_salary_range' => __( 'Advertised salary range for the role.', 'dentist-exchange' ),
			'dnte_role_match_rate'   => __( 'Candidate match percentage, shown on role cards as "92% match".', 'dentist-exchange' ),
		);

		foreach ( $text_fields as $key => $description ) {
			register_post_meta(
				'open-role',
				$key,
				array(
					'type'              => 'string',
					'description'       => $description,
					'single'            => true,
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => $auth,
					'show_in_rest'      => true,
				)
			);
		}

		register_post_meta(
			'open-role',
			'dnte_role_type',
			array(
				'type'              => 'string',
				'description'       => __( 'Employment type of the role.', 'dentist-exchange' ),
				'single'            => true,
				'default'           => 'full-time',
				'sanitize_callback' => 'dnte_sanitize_open_role_type',
				'auth_callback'     => $auth,
				'show_in_rest'      => true,
			)
		);

		register_post_meta(
			'open-role',
			'dnte_role_nature',
			array(
				'type'              => 'string',
				'description'       => __( 'Work arrangement for the role.', 'dentist-exchange' ),
				'single'            => true,
				'default'           => 'remote',
				'sanitize_callback' => 'dnte_sanitize_open_role_nature',
				'auth_callback'     => $auth,
				'show_in_rest'      => true,
			)
		);

		register_post_meta(
			'open-role',
			'dnte_role_active',
			array(
				'type'          => 'boolean',
				'description'   => __( 'Whether the role is currently open.', 'dentist-exchange' ),
				'single'        => true,
				'default'       => true,
				'auth_callback' => $auth,
				'show_in_rest'  => true,
			)
		);

		register_post_meta(
			'open-role',
			'dnte_role_apply_link',
			array(
				'type'              => 'string',
				'description'       => __( 'URL applicants are sent to.', 'dentist-exchange' ),
				'single'            => true,
				'default'           => '',
				'sanitize_callback' => 'sanitize_url',
				'auth_callback'     => $auth,
				'show_in_rest'      => true,
			)
		);
	}
endif;
add_action( 'init', 'dnte_register_open_role_meta' );


if ( ! function_exists( 'dnte_sanitize_open_role_type' ) ) :
	/**
	 * Falls back to the default when the value is not a known type.
	 *
	 * @param  string $value Submitted value.
	 * @return string
	 */
	function dnte_sanitize_open_role_type( $value ) {
		return array_key_exists( $value, dnte_open_role_type_options() ) ? $value : 'full-time';
	}
endif;


if ( ! function_exists( 'dnte_sanitize_open_role_nature' ) ) :
	/**
	 * Falls back to the default when the value is not a known arrangement.
	 *
	 * @param  string $value Submitted value.
	 * @return string
	 */
	function dnte_sanitize_open_role_nature( $value ) {
		return array_key_exists( $value, dnte_open_role_nature_options() ) ? $value : 'remote';
	}
endif;


if ( ! function_exists( 'dnte_add_open_role_meta_box' ) ) :
	/**
	 * Adds the Role Details meta box to the Open Role editing screen.
	 */
	function dnte_add_open_role_meta_box() {
		add_meta_box(
			'dnte-open-role-details',
			__( 'Role Details', 'dentist-exchange' ),
			'dnte_render_open_role_meta_box',
			'open-role',
			'normal',
			'default'
		);
	}
endif;
add_action( 'add_meta_boxes', 'dnte_add_open_role_meta_box' );


if ( ! function_exists( 'dnte_render_open_role_meta_box' ) ) :
	/**
	 * Renders the Open Role fields inside the meta box.
	 *
	 * @param WP_Post $post Current post.
	 */
	function dnte_render_open_role_meta_box( $post ) {
		$location   = get_post_meta( $post->ID, 'dnte_role_location', true );
		$salary     = get_post_meta( $post->ID, 'dnte_role_salary_range', true );
		$type       = get_post_meta( $post->ID, 'dnte_role_type', true );
		$nature     = get_post_meta( $post->ID, 'dnte_role_nature', true );
		$match_rate = get_post_meta( $post->ID, 'dnte_role_match_rate', true );
		$apply_link = get_post_meta( $post->ID, 'dnte_role_apply_link', true );

		// A post saved before these fields existed has no stored value, and
		// register_post_meta defaults do not apply to an unsaved draft either.
		$type   = $type ? $type : 'full-time';
		$nature = $nature ? $nature : 'remote';

		/*
		 * The toggle defaults to on. `metadata_exists` distinguishes "never
		 * saved" (default to active) from "saved as off" (an empty string,
		 * which would otherwise look identical to a missing value).
		 */
		$active = metadata_exists( 'post', $post->ID, 'dnte_role_active' )
			? (bool) get_post_meta( $post->ID, 'dnte_role_active', true )
			: true;

		wp_nonce_field( 'dnte_save_open_role_meta', 'dnte_open_role_meta_nonce' );
		?>
		<p>
			<label for="dnte-role-active">
				<input
					type="checkbox"
					id="dnte-role-active"
					name="dnte_role_active"
					value="1"
					<?php checked( $active ); ?>
				/>
				<strong><?php esc_html_e( 'Role is active', 'dentist-exchange' ); ?></strong>
			</label>
			<br />
			<span class="description"><?php esc_html_e( 'Uncheck to close the role without deleting it.', 'dentist-exchange' ); ?></span>
		</p>
		<p>
			<label for="dnte-role-location"><?php esc_html_e( 'Location', 'dentist-exchange' ); ?></label>
			<input
				type="text"
				id="dnte-role-location"
				name="dnte_role_location"
				value="<?php echo esc_attr( $location ); ?>"
				class="widefat"
				placeholder="<?php esc_attr_e( 'e.g. Glendale, CA', 'dentist-exchange' ); ?>"
			/>
		</p>
		<p>
			<label for="dnte-role-salary-range"><?php esc_html_e( 'Salary Range', 'dentist-exchange' ); ?></label>
			<input
				type="text"
				id="dnte-role-salary-range"
				name="dnte_role_salary_range"
				value="<?php echo esc_attr( $salary ); ?>"
				class="widefat"
				placeholder="<?php esc_attr_e( 'e.g. $95,000 – $120,000', 'dentist-exchange' ); ?>"
			/>
		</p>
		<p>
			<label for="dnte-role-type"><?php esc_html_e( 'Type', 'dentist-exchange' ); ?></label>
			<select id="dnte-role-type" name="dnte_role_type" class="widefat">
				<?php foreach ( dnte_open_role_type_options() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $type, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="dnte-role-nature"><?php esc_html_e( 'Nature', 'dentist-exchange' ); ?></label>
			<select id="dnte-role-nature" name="dnte_role_nature" class="widefat">
				<?php foreach ( dnte_open_role_nature_options() as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $nature, $value ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="dnte-role-match-rate"><?php esc_html_e( 'Match Rate', 'dentist-exchange' ); ?></label>
			<input
				type="text"
				id="dnte-role-match-rate"
				name="dnte_role_match_rate"
				value="<?php echo esc_attr( $match_rate ); ?>"
				class="widefat"
				placeholder="<?php esc_attr_e( 'e.g. 92', 'dentist-exchange' ); ?>"
			/>
		</p>
		<p>
			<label for="dnte-role-apply-link"><?php esc_html_e( 'Apply Link', 'dentist-exchange' ); ?></label>
			<?php /* Deliberately type="text": type="url" makes the browser reject "#" and other scheme-less values. */ ?>
			<input
				type="text"
				id="dnte-role-apply-link"
				name="dnte_role_apply_link"
				value="<?php echo esc_url( $apply_link, array( 'http', 'https', 'mailto' ) ); ?>"
				class="widefat"
				placeholder="<?php esc_attr_e( 'https://example.com/apply, /careers/, or #apply', 'dentist-exchange' ); ?>"
			/>
		</p>
		<?php
	}
endif;


if ( ! function_exists( 'dnte_save_open_role_meta' ) ) :
	/**
	 * Saves the Open Role meta fields.
	 *
	 * @param int $post_id Post ID.
	 */
	function dnte_save_open_role_meta( $post_id ) {
		if (
			! isset( $_POST['dnte_open_role_meta_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['dnte_open_role_meta_nonce'] ), 'dnte_save_open_role_meta' )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$text_fields = array( 'dnte_role_location', 'dnte_role_salary_range', 'dnte_role_match_rate' );

		foreach ( $text_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}

		if ( isset( $_POST['dnte_role_type'] ) ) {
			update_post_meta( $post_id, 'dnte_role_type', dnte_sanitize_open_role_type( sanitize_key( wp_unslash( $_POST['dnte_role_type'] ) ) ) );
		}

		if ( isset( $_POST['dnte_role_nature'] ) ) {
			update_post_meta( $post_id, 'dnte_role_nature', dnte_sanitize_open_role_nature( sanitize_key( wp_unslash( $_POST['dnte_role_nature'] ) ) ) );
		}

		if ( isset( $_POST['dnte_role_apply_link'] ) ) {
			update_post_meta( $post_id, 'dnte_role_apply_link', sanitize_url( wp_unslash( $_POST['dnte_role_apply_link'] ) ) );
		}

		// An unchecked checkbox posts nothing, so absence means off. The nonce
		// check above guarantees this really is a submission of that form.
		update_post_meta( $post_id, 'dnte_role_active', isset( $_POST['dnte_role_active'] ) );
	}
endif;
add_action( 'save_post_open-role', 'dnte_save_open_role_meta' );


if ( ! function_exists( 'dnte_flush_rewrite_rules_on_activation' ) ) :
	/**
	 * Flushes rewrite rules once when the theme is activated, so event and
	 * event type permalinks resolve without a manual visit to
	 * Settings → Permalinks.
	 *
	 * Team and Open Role are not registered here: neither has rewrite rules.
	 */
	function dnte_flush_rewrite_rules_on_activation() {
		dnte_register_event_post_type();
		dnte_register_team_post_type();

		if ( function_exists( 'dnte_register_event_type_taxonomy' ) ) {
			dnte_register_event_type_taxonomy();
		}

		flush_rewrite_rules();
	}
endif;
add_action( 'after_switch_theme', 'dnte_flush_rewrite_rules_on_activation' );


// ── Active toggle column on the Open Roles admin screen ───────────────────────

if ( ! function_exists( 'dnte_open_role_is_active' ) ) :
	/**
	 * Whether a role is active.
	 *
	 * A role saved before this field existed has no stored value; those count
	 * as active, matching the field's default.
	 *
	 * @param  int $post_id Post ID.
	 * @return bool
	 */
	function dnte_open_role_is_active( $post_id ) {
		if ( ! metadata_exists( 'post', $post_id, 'dnte_role_active' ) ) {
			return true;
		}

		return (bool) get_post_meta( $post_id, 'dnte_role_active', true );
	}
endif;


if ( ! function_exists( 'dnte_open_role_columns' ) ) :
	/**
	 * Adds an "Active" column, placed just after the title.
	 *
	 * @param  array $columns Existing column definitions.
	 * @return array
	 */
	function dnte_open_role_columns( $columns ) {
		$reordered = array();

		foreach ( $columns as $key => $label ) {
			$reordered[ $key ] = $label;

			if ( 'title' === $key ) {
				$reordered['dnte_role_active'] = __( 'Active', 'dentist-exchange' );
			}
		}

		// If there was no title column to anchor to, fall back to appending.
		if ( ! isset( $reordered['dnte_role_active'] ) ) {
			$reordered['dnte_role_active'] = __( 'Active', 'dentist-exchange' );
		}

		return $reordered;
	}
endif;
add_filter( 'manage_open-role_posts_columns', 'dnte_open_role_columns' );


if ( ! function_exists( 'dnte_open_role_column_content' ) ) :
	/**
	 * Renders the toggle switch in the Active column.
	 *
	 * @param  string $column_name Column key.
	 * @param  int    $post_id     Post ID.
	 * @return void
	 */
	function dnte_open_role_column_content( $column_name, $post_id ) {
		if ( 'dnte_role_active' !== $column_name ) {
			return;
		}

		$active   = dnte_open_role_is_active( $post_id );
		$disabled = ! current_user_can( 'edit_post', $post_id );

		/* translators: %s: role title. */
		$label = sprintf( __( 'Toggle whether %s is active', 'dentist-exchange' ), get_the_title( $post_id ) );
		?>
		<button
			type="button"
			class="dnte-role-toggle"
			role="switch"
			aria-checked="<?php echo $active ? 'true' : 'false'; ?>"
			aria-label="<?php echo esc_attr( $label ); ?>"
			data-id="<?php echo esc_attr( (string) $post_id ); ?>"
			<?php disabled( $disabled ); ?>
		>
			<span class="dnte-role-toggle__track" aria-hidden="true">
				<span class="dnte-role-toggle__thumb"></span>
			</span>
		</button>
		<?php
	}
endif;
add_action( 'manage_open-role_posts_custom_column', 'dnte_open_role_column_content', 10, 2 );


if ( ! function_exists( 'dnte_open_role_admin_assets' ) ) :
	/**
	 * Loads the toggle script and styles on the Open Roles list table only.
	 *
	 * @param  string $hook Current admin page.
	 * @return void
	 */
	function dnte_open_role_admin_assets( $hook ) {
		global $typenow;

		if ( 'edit.php' !== $hook || 'open-role' !== $typenow ) {
			return;
		}

		wp_enqueue_style(
			'dnte-open-role-admin',
			get_theme_file_uri( 'assets/css/open-role-admin.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);

		wp_enqueue_script(
			'dnte-open-role-toggle',
			get_theme_file_uri( 'assets/js/open-role-toggle.js' ),
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);

		wp_add_inline_script(
			'dnte-open-role-toggle',
			'window.hangOpenRole = ' . wp_json_encode(
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'dnte_open_role_toggle' ),
				)
			) . ';',
			'before'
		);
	}
endif;
add_action( 'admin_enqueue_scripts', 'dnte_open_role_admin_assets' );


if ( ! function_exists( 'dnte_toggle_open_role_active_ajax' ) ) :
	/**
	 * Flips the active flag for a single role.
	 *
	 * @return void
	 */
	function dnte_toggle_open_role_active_ajax() {
		check_ajax_referer( 'dnte_open_role_toggle', 'nonce' );

		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( ! $post_id || 'open-role' !== get_post_type( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Unknown role.', 'dentist-exchange' ) ), 400 );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'You cannot edit this role.', 'dentist-exchange' ) ), 403 );
		}

		$active = isset( $_POST['active'] ) && '1' === $_POST['active'];

		update_post_meta( $post_id, 'dnte_role_active', $active );

		wp_send_json_success( array( 'active' => $active ) );
	}
endif;
add_action( 'wp_ajax_dnte_toggle_open_role_active', 'dnte_toggle_open_role_active_ajax' );
