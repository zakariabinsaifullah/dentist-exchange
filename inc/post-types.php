<?php
/**
 * Custom Post Types
 *
 * Registers the content types owned by this theme.
 *
 * @package Dentist_Exchange
 */

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

// ── Taxonomies ────────────────────────────────────────────────────────────────

if ( ! function_exists( 'dnte_register_open_role_taxonomies' ) ) :
	/**
	 * Registers Job Type and Tags for open roles.
	 *
	 * Job Type is hierarchical so it presents the checkbox UI of a fixed
	 * vocabulary (Clinical, Support Staff, Remote …) — it drives the filter
	 * tabs and the "Clinical - 42 open" line on the role cards.
	 *
	 * Tags is flat and behaves like keywords; the shortcode's search matches
	 * against it as well as the title.
	 *
	 * Neither has a public archive: open roles have no front-end views at all.
	 */
	function dnte_register_open_role_taxonomies() {
		register_taxonomy(
			'dnte-job-type',
			'open-role',
			array(
				'labels'            => array(
					'name'              => _x( 'Job Types', 'taxonomy general name', 'dentist-exchange' ),
					'singular_name'     => _x( 'Job Type', 'taxonomy singular name', 'dentist-exchange' ),
					'menu_name'         => __( 'Job Types', 'dentist-exchange' ),
					'all_items'         => __( 'All Job Types', 'dentist-exchange' ),
					'edit_item'         => __( 'Edit Job Type', 'dentist-exchange' ),
					'update_item'       => __( 'Update Job Type', 'dentist-exchange' ),
					'add_new_item'      => __( 'Add New Job Type', 'dentist-exchange' ),
					'new_item_name'     => __( 'New Job Type Name', 'dentist-exchange' ),
					'search_items'      => __( 'Search Job Types', 'dentist-exchange' ),
					'parent_item'       => __( 'Parent Job Type', 'dentist-exchange' ),
					'parent_item_colon' => __( 'Parent Job Type:', 'dentist-exchange' ),
					'not_found'         => __( 'No job types found.', 'dentist-exchange' ),
				),
				'hierarchical'      => true,
				'public'            => false,
				'publicly_queryable' => false,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_in_rest'      => false,
				'query_var'         => false,
				'rewrite'           => false,
			)
		);

		register_taxonomy(
			'dnte-role-tag',
			'open-role',
			array(
				'labels'            => array(
					'name'                       => _x( 'Tags', 'taxonomy general name', 'dentist-exchange' ),
					'singular_name'              => _x( 'Tag', 'taxonomy singular name', 'dentist-exchange' ),
					'menu_name'                  => __( 'Tags', 'dentist-exchange' ),
					'all_items'                  => __( 'All Tags', 'dentist-exchange' ),
					'edit_item'                  => __( 'Edit Tag', 'dentist-exchange' ),
					'add_new_item'               => __( 'Add New Tag', 'dentist-exchange' ),
					'new_item_name'              => __( 'New Tag Name', 'dentist-exchange' ),
					'search_items'               => __( 'Search Tags', 'dentist-exchange' ),
					'separate_items_with_commas' => __( 'Separate keywords with commas', 'dentist-exchange' ),
					'add_or_remove_items'        => __( 'Add or remove keywords', 'dentist-exchange' ),
					'not_found'                  => __( 'No tags found.', 'dentist-exchange' ),
				),
				'hierarchical'      => false,
				'public'            => false,
				'publicly_queryable' => false,
				'show_ui'           => true,
				'show_in_menu'      => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => false,
				'show_in_rest'      => false,
				'query_var'         => false,
				'rewrite'           => false,
			)
		);
	}
endif;
add_action( 'init', 'dnte_register_open_role_taxonomies' );


// ── Meta ──────────────────────────────────────────────────────────────────────

if ( ! function_exists( 'dnte_register_open_role_meta' ) ) :
	/**
	 * Registers the open role meta.
	 *
	 * Job type, salary, match rate and work arrangement used to live here;
	 * job type is now a taxonomy and the others were dropped.
	 */
	function dnte_register_open_role_meta() {
		$auth = function () {
			return current_user_can( 'edit_posts' );
		};

		register_post_meta(
			'open-role',
			'dnte_role_location',
			array(
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => false,
				'description'       => __( 'City or postcode the role is based in.', 'dentist-exchange' ),
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth,
			)
		);

		register_post_meta(
			'open-role',
			'dnte_role_vacancies',
			array(
				'single'            => true,
				'type'              => 'integer',
				'show_in_rest'      => false,
				'default'           => 0,
				'description'       => __( 'How many positions are open for this role.', 'dentist-exchange' ),
				'sanitize_callback' => 'absint',
				'auth_callback'     => $auth,
			)
		);

		register_post_meta(
			'open-role',
			'dnte_role_icon',
			array(
				'single'            => true,
				'type'              => 'integer',
				'show_in_rest'      => false,
				'default'           => 0,
				'description'       => __( 'Attachment ID of the symbolic icon.', 'dentist-exchange' ),
				'sanitize_callback' => 'absint',
				'auth_callback'     => $auth,
			)
		);

		register_post_meta(
			'open-role',
			'dnte_role_apply_link',
			array(
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => false,
				'description'       => __( 'Where the Apply button sends candidates.', 'dentist-exchange' ),

				/*
				 * Stored as plain text, not run through sanitize_url, so a
				 * relative path, an anchor or a mailto: can be entered without
				 * being silently emptied. Escaping still happens on output,
				 * where esc_url() drops anything dangerous.
				 */
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth,
			)
		);

		register_post_meta(
			'open-role',
			'dnte_role_active',
			array(
				'single'            => true,
				'type'              => 'boolean',
				'show_in_rest'      => false,
				'default'           => true,
				'description'       => __( 'Whether the role is listed by the shortcode.', 'dentist-exchange' ),
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => $auth,
			)
		);
	}
endif;
add_action( 'init', 'dnte_register_open_role_meta' );


// ── Edit screen ───────────────────────────────────────────────────────────────

if ( ! function_exists( 'dnte_add_open_role_meta_box' ) ) :
	/**
	 * Adds the open role details box.
	 */
	function dnte_add_open_role_meta_box() {
		add_meta_box(
			'dnte-open-role-details',
			__( 'Role Details', 'dentist-exchange' ),
			'dnte_render_open_role_meta_box',
			'open-role',
			'normal',
			'high'
		);
	}
endif;
add_action( 'add_meta_boxes', 'dnte_add_open_role_meta_box' );


if ( ! function_exists( 'dnte_render_open_role_meta_box' ) ) :
	/**
	 * Renders the open role fields.
	 *
	 * @param WP_Post $post Current post.
	 */
	function dnte_render_open_role_meta_box( $post ) {
		wp_nonce_field( 'dnte_save_open_role', 'dnte_open_role_nonce' );

		$location   = get_post_meta( $post->ID, 'dnte_role_location', true );
		$vacancies  = get_post_meta( $post->ID, 'dnte_role_vacancies', true );
		$icon_id    = (int) get_post_meta( $post->ID, 'dnte_role_icon', true );
		$apply_link = get_post_meta( $post->ID, 'dnte_role_apply_link', true );

		// A brand new draft has no meta row yet, and register_post_meta
		// defaults do not apply to one, so active starts checked.
		$active = metadata_exists( 'post', $post->ID, 'dnte_role_active' )
			? (bool) get_post_meta( $post->ID, 'dnte_role_active', true )
			: true;

		$icon_url = $icon_id ? wp_get_attachment_url( $icon_id ) : '';
		?>
		<p>
			<label>
				<input type="checkbox" name="dnte_role_active" value="1" <?php checked( $active ); ?> />
				<strong><?php esc_html_e( 'Role is active', 'dentist-exchange' ); ?></strong>
			</label>
			<span class="description"><?php esc_html_e( 'Only active roles are listed by the shortcode.', 'dentist-exchange' ); ?></span>
		</p>

		<p>
			<label for="dnte-role-vacancies"><strong><?php esc_html_e( 'Vacancies', 'dentist-exchange' ); ?></strong></label><br />
			<input
				type="number"
				id="dnte-role-vacancies"
				name="dnte_role_vacancies"
				class="small-text"
				min="0"
				step="1"
				value="<?php echo esc_attr( '' === $vacancies ? '0' : (string) (int) $vacancies ); ?>"
			/>
			<span class="description"><?php esc_html_e( 'Shown on the card as "42 open".', 'dentist-exchange' ); ?></span>
		</p>

		<p>
			<label for="dnte-role-location"><strong><?php esc_html_e( 'Location', 'dentist-exchange' ); ?></strong></label><br />
			<input
				type="text"
				id="dnte-role-location"
				name="dnte_role_location"
				class="widefat"
				value="<?php echo esc_attr( $location ); ?>"
				placeholder="<?php esc_attr_e( 'City or postcode', 'dentist-exchange' ); ?>"
			/>
			<span class="description"><?php esc_html_e( 'Matched against the "City or postcode" search field.', 'dentist-exchange' ); ?></span>
		</p>

		<p>
			<strong><?php esc_html_e( 'Symbolic Icon', 'dentist-exchange' ); ?></strong><br />
			<span class="dnte-role-icon-preview" style="display:inline-block;min-width:48px;min-height:48px;margin:6px 0;">
				<?php if ( $icon_url ) : ?>
					<img src="<?php echo esc_url( $icon_url ); ?>" alt="" style="max-width:48px;max-height:48px;" />
				<?php endif; ?>
			</span><br />
			<input type="hidden" id="dnte-role-icon" name="dnte_role_icon" value="<?php echo esc_attr( (string) $icon_id ); ?>" />
			<button type="button" class="button dnte-role-icon-select">
				<?php echo $icon_id ? esc_html__( 'Replace Icon', 'dentist-exchange' ) : esc_html__( 'Upload Icon', 'dentist-exchange' ); ?>
			</button>
			<button type="button" class="button-link dnte-role-icon-remove" style="<?php echo $icon_id ? '' : 'display:none;'; ?>color:#b32d2e;">
				<?php esc_html_e( 'Remove', 'dentist-exchange' ); ?>
			</button>
			<br />
			<span class="description"><?php esc_html_e( 'An image or an SVG. SVG uploads are cleaned of scripts and event handlers first.', 'dentist-exchange' ); ?></span>
		</p>

		<p>
			<label for="dnte-role-apply-link"><strong><?php esc_html_e( 'Apply Link', 'dentist-exchange' ); ?></strong></label><br />
			<?php // `text`, not `url`, so the browser does not refuse to save a relative path or an anchor. ?>
			<input
				type="text"
				id="dnte-role-apply-link"
				name="dnte_role_apply_link"
				class="widefat"
				value="<?php echo esc_attr( $apply_link ); ?>"
				placeholder="https://"
			/>
		</p>
		<?php
	}
endif;


if ( ! function_exists( 'dnte_save_open_role_meta' ) ) :
	/**
	 * Saves the open role fields.
	 *
	 * @param int $post_id Post ID.
	 */
	function dnte_save_open_role_meta( $post_id ) {
		if ( ! isset( $_POST['dnte_open_role_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dnte_open_role_nonce'] ) ), 'dnte_save_open_role' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		update_post_meta(
			$post_id,
			'dnte_role_location',
			isset( $_POST['dnte_role_location'] ) ? sanitize_text_field( wp_unslash( $_POST['dnte_role_location'] ) ) : ''
		);

		update_post_meta(
			$post_id,
			'dnte_role_vacancies',
			isset( $_POST['dnte_role_vacancies'] ) ? absint( wp_unslash( $_POST['dnte_role_vacancies'] ) ) : 0
		);

		update_post_meta(
			$post_id,
			'dnte_role_icon',
			isset( $_POST['dnte_role_icon'] ) ? absint( wp_unslash( $_POST['dnte_role_icon'] ) ) : 0
		);

		update_post_meta(
			$post_id,
			'dnte_role_apply_link',
			isset( $_POST['dnte_role_apply_link'] ) ? sanitize_text_field( wp_unslash( $_POST['dnte_role_apply_link'] ) ) : ''
		);

		update_post_meta( $post_id, 'dnte_role_active', isset( $_POST['dnte_role_active'] ) );
	}
endif;

add_action( 'save_post_open-role', 'dnte_save_open_role_meta' );


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


// =============================================================================
// Testimonials
// =============================================================================

if ( ! function_exists( 'dnte_register_testimonial_post_type' ) ) :
	/**
	 * Registers the Testimonial post type.
	 *
	 * A testimonial is three fields: the reviewer's name (the post title), a
	 * designation and a review message. It is only ever shown through the
	 * [dnte_testimonials] shortcode, so like Open Roles it has no front-end
	 * single view — `public`, `publicly_queryable`, `query_var`, `rewrite` and
	 * `has_archive` are all off.
	 *
	 * `show_in_rest` is false so WordPress falls back to the classic editor
	 * screen. With only `title` support the block editor would present an
	 * empty canvas, whereas the classic screen puts the title and the two meta
	 * fields together on one page.
	 */
	function dnte_register_testimonial_post_type() {
		$labels = array(
			'name'                  => _x( 'Testimonials', 'post type general name', 'dentist-exchange' ),
			'singular_name'         => _x( 'Testimonial', 'post type singular name', 'dentist-exchange' ),
			'menu_name'             => _x( 'Testimonials', 'admin menu', 'dentist-exchange' ),
			'name_admin_bar'        => _x( 'Testimonial', 'add new on admin bar', 'dentist-exchange' ),
			'add_new'               => __( 'Add Testimonial', 'dentist-exchange' ),
			'add_new_item'          => __( 'Add New Testimonial', 'dentist-exchange' ),
			'new_item'              => __( 'New Testimonial', 'dentist-exchange' ),
			'edit_item'             => __( 'Edit Testimonial', 'dentist-exchange' ),
			'view_item'             => __( 'View Testimonial', 'dentist-exchange' ),
			'all_items'             => __( 'All Testimonials', 'dentist-exchange' ),
			'search_items'          => __( 'Search Testimonials', 'dentist-exchange' ),
			'not_found'             => __( 'No testimonials found.', 'dentist-exchange' ),
			'not_found_in_trash'    => __( 'No testimonials found in Trash.', 'dentist-exchange' ),
			'filter_items_list'     => __( 'Filter testimonials list', 'dentist-exchange' ),
			'items_list_navigation' => __( 'Testimonials list navigation', 'dentist-exchange' ),
			'items_list'            => __( 'Testimonials list', 'dentist-exchange' ),
			'item_published'        => __( 'Testimonial published.', 'dentist-exchange' ),
			'item_updated'          => __( 'Testimonial updated.', 'dentist-exchange' ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'Reviews shown through the testimonials shortcode.', 'dentist-exchange' ),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'show_in_rest'        => false,
			'query_var'           => false,
			'rewrite'             => false,
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-format-quote',
			'capability_type'     => 'post',
			'supports'            => array( 'title', 'page-attributes' ),
		);

		register_post_type( 'dnte-testimonial', $args );
	}
endif;
add_action( 'init', 'dnte_register_testimonial_post_type' );


// =============================================================================
// Open Role — icon picker & SVG uploads
// =============================================================================

if ( ! function_exists( 'dnte_open_role_icon_assets' ) ) :
	/**
	 * Loads the media frame and the icon picker on the open role edit screen.
	 *
	 * @param string $hook Current admin page.
	 */
	function dnte_open_role_icon_assets( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		if ( 'open-role' !== get_post_type() ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'dnte-open-role-icon',
			get_theme_file_uri( 'assets/js/open-role-icon.js' ),
			array( 'jquery' ),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
endif;
add_action( 'admin_enqueue_scripts', 'dnte_open_role_icon_assets' );


if ( ! function_exists( 'dnte_allow_svg_upload' ) ) :
	/**
	 * Permits SVG uploads for users who can already publish unfiltered markup.
	 *
	 * WordPress blocks image/svg+xml because an SVG is a script carrier. The
	 * capability check plus the sanitiser below are what make this safe: the
	 * file is rewritten on upload with scripts, event handlers and external
	 * references removed.
	 *
	 * @param array $mimes Allowed mime types.
	 * @return array Filtered mime types.
	 */
	function dnte_allow_svg_upload( $mimes ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $mimes;
		}

		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';

		return $mimes;
	}
endif;
add_filter( 'upload_mimes', 'dnte_allow_svg_upload' );


if ( ! function_exists( 'dnte_sanitize_svg_upload' ) ) :
	/**
	 * Rewrites an uploaded SVG through the theme's sanitiser before it lands
	 * in the media library.
	 *
	 * Runs on every upload, so an SVG that slips past `upload_mimes` — through
	 * a plugin, say — is still cleaned. Reuses dnte_sanitize_svg_markup() from
	 * inc/my-icons.php, which strips scripts, event handlers and external
	 * references. An unparseable file is rejected outright.
	 *
	 * @param array $file Upload array.
	 * @return array Possibly rejected upload array.
	 */
	function dnte_sanitize_svg_upload( $file ) {
		if ( empty( $file['tmp_name'] ) || empty( $file['type'] ) ) {
			return $file;
		}

		if ( 'image/svg+xml' !== $file['type'] ) {
			return $file;
		}

		if ( ! function_exists( 'dnte_sanitize_svg_markup' ) ) {
			$file['error'] = __( 'SVG uploads are unavailable: the sanitiser is missing.', 'dentist-exchange' );
			return $file;
		}

		$raw = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local temp file.

		if ( false === $raw ) {
			$file['error'] = __( 'The SVG could not be read.', 'dentist-exchange' );
			return $file;
		}

		$clean = dnte_sanitize_svg_markup( $raw );

		if ( '' === $clean ) {
			$file['error'] = __( 'That SVG could not be sanitised, so it was not uploaded.', 'dentist-exchange' );
			return $file;
		}

		file_put_contents( $file['tmp_name'], $clean ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_put_contents -- Local temp file.

		return $file;
	}
endif;
add_filter( 'wp_handle_upload_prefilter', 'dnte_sanitize_svg_upload' );
