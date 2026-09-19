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
