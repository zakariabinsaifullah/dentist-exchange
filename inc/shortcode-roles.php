<?php
/**
 * Open Roles Shortcode
 *
 * Renders the job board: a search bar, Job Type filter tabs and a grid of
 * role cards showing the symbolic icon, the role title and the job type with
 * its vacancy count.
 *
 * Usage: [opening_roles columns="4" per_page="12"]
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// Assets
// =============================================================================

if ( ! function_exists( 'dnte_roles_grid_enqueue_assets' ) ) :
	/**
	 * Enqueues the job board styles and the filtering script.
	 */
	function dnte_roles_grid_enqueue_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style(
			'dnte-roles-grid',
			get_theme_file_uri( 'assets/css/roles-grid.css' ),
			array(),
			$version
		);

		wp_enqueue_script(
			'dnte-roles-grid',
			get_theme_file_uri( 'assets/js/roles-grid.js' ),
			array(),
			$version,
			true
		);
	}
endif;


// =============================================================================
// Helpers
// =============================================================================

if ( ! function_exists( 'dnte_roles_inline_icon' ) ) :
	/**
	 * The search bar's own icons.
	 *
	 * These belong to the chrome rather than to any role, so they are inlined
	 * here instead of being uploaded.
	 *
	 * @param string $name One of: search, pin, briefcase, chevron.
	 * @return string SVG markup.
	 */
	function dnte_roles_inline_icon( $name ) {
		$icons = array(
			'search'    => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
			'pin'       => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
			'briefcase' => '<rect x="3" y="7.5" width="18" height="12.5" rx="2"/><path d="M9 7.5V6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1.5M3 12h18"/>',
			'chevron'   => '<path d="m6 9 6 6 6-6"/>',
		);

		if ( ! isset( $icons[ $name ] ) ) {
			return '';
		}

		return '<svg class="dnte-jobs__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" '
			. 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
			. $icons[ $name ] . '</svg>';
	}
endif;


if ( ! function_exists( 'dnte_roles_active_ids' ) ) :
	/**
	 * IDs of the roles that should be listed.
	 *
	 * A role is listed only when its Active toggle is on. The meta is absent
	 * on roles created before that toggle existed, which count as active.
	 *
	 * @param int $limit Maximum roles, or -1 for all.
	 * @param string $order Sort direction.
	 * @param string $orderby Sort field.
	 * @return int[] Post IDs.
	 */
	function dnte_roles_active_ids( $limit, $order, $orderby ) {
		$ids = get_posts(
			array(
				'post_type'        => 'open-role',
				'post_status'      => 'publish',
				'posts_per_page'   => $limit,
				'order'            => $order,
				'orderby'          => $orderby,
				'fields'           => 'ids',
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);

		return array_values( array_filter( $ids, 'dnte_open_role_is_active' ) );
	}
endif;


if ( ! function_exists( 'dnte_roles_render_card' ) ) :
	/**
	 * Renders one role card.
	 *
	 * The card carries its job types, keywords and location as data
	 * attributes; the filtering runs in the browser against those, so the
	 * tabs and the search respond without a round trip.
	 *
	 * @param int $post_id Role ID.
	 * @return string Markup.
	 */
	function dnte_roles_render_card( $post_id ) {
		$types = get_the_terms( $post_id, 'dnte-job-type' );
		$types = is_array( $types ) ? $types : array();

		$tags = get_the_terms( $post_id, 'dnte-role-tag' );
		$tags = is_array( $tags ) ? $tags : array();

		$vacancies = (int) get_post_meta( $post_id, 'dnte_role_vacancies', true );
		$location  = (string) get_post_meta( $post_id, 'dnte_role_location', true );
		$apply     = (string) get_post_meta( $post_id, 'dnte_role_apply_link', true );
		$icon_id   = (int) get_post_meta( $post_id, 'dnte_role_icon', true );
		$title     = get_the_title( $post_id );

		$type_names = wp_list_pluck( $types, 'name' );
		$type_slugs = wp_list_pluck( $types, 'slug' );

		/*
		 * "Clinical - 42 open", as two spans rather than one string. The
		 * separator lives in CSS because wptexturize rewrites " - " into an
		 * en dash, and the design uses a hyphen.
		 */
		$type_label = $type_names ? implode( ', ', $type_names ) : '';

		/* translators: %d: number of open positions. */
		$open_label = $vacancies > 0 ? sprintf( _n( '%d open', '%d open', $vacancies, 'dentist-exchange' ), $vacancies ) : '';

		// Keywords the search matches on, beyond the title.
		$keywords = implode( ' ', array_merge( wp_list_pluck( $tags, 'name' ), $type_names ) );

		/*
		 * A role with no Apply Link is a plain card, not an empty link.
		 *
		 * The escaping decides the tag, rather than the raw value: esc_url()
		 * returns '' for anything it will not allow through, so checking the
		 * raw string instead would render `<a href="">` — a link that looks
		 * clickable and reloads the page.
		 */
		$apply_url = '' !== $apply ? esc_url( $apply ) : '';
		$tag       = '' !== $apply_url ? 'a' : 'div';

		ob_start();
		?>
		<<?php echo esc_html( $tag ); ?>
			class="dnte-job"
			<?php if ( '' !== $apply_url ) : ?>
				<?php // Already escaped above. ?>
				href="<?php echo $apply_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
			<?php endif; ?>
			data-types="<?php echo esc_attr( implode( ' ', $type_slugs ) ); ?>"
			data-keywords="<?php echo esc_attr( strtolower( $title . ' ' . $keywords ) ); ?>"
			data-location="<?php echo esc_attr( strtolower( $location ) ); ?>"
		>
			<span class="dnte-job__icon" aria-hidden="true">
				<?php if ( $icon_id ) : ?>
					<?php echo wp_get_attachment_image( $icon_id, 'full', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
				<?php endif; ?>
			</span>
			<span class="dnte-job__title"><?php echo esc_html( $title ); ?></span>
			<?php if ( '' !== $type_label || '' !== $open_label ) : ?>
				<span class="dnte-job__meta">
					<?php if ( '' !== $type_label ) : ?>
						<span class="dnte-job__type"><?php echo esc_html( $type_label ); ?></span>
					<?php endif; ?>
					<?php if ( '' !== $open_label ) : ?>
						<span class="dnte-job__open"><?php echo esc_html( $open_label ); ?></span>
					<?php endif; ?>
				</span>
			<?php endif; ?>
		</<?php echo esc_html( $tag ); ?>>
		<?php
		return (string) ob_get_clean();
	}
endif;


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_opening_roles_shortcode' ) ) :
	/**
	 * Renders the job board.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Markup, or '' when there are no active roles.
	 */
	function dnte_opening_roles_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'columns'  => 4,
				'per_page' => -1,
				'order'    => 'DESC',
				'orderby'  => 'date',
				'search'   => 'yes',
				'tabs'     => 'yes',
			),
			$atts,
			'opening_roles'
		);

		$columns = min( 4, max( 1, (int) $atts['columns'] ) );
		$order   = 'ASC' === strtoupper( $atts['order'] ) ? 'ASC' : 'DESC';

		$ids = dnte_roles_active_ids( (int) $atts['per_page'], $order, sanitize_key( $atts['orderby'] ) );

		if ( ! $ids ) {
			return '';
		}

		dnte_roles_grid_enqueue_assets();

		// Only job types that actually have an active role behind them.
		$used_types = array();

		foreach ( $ids as $id ) {
			foreach ( (array) get_the_terms( $id, 'dnte-job-type' ) as $term ) {
				if ( $term instanceof WP_Term ) {
					$used_types[ $term->slug ] = $term->name;
				}
			}
		}

		ob_start();
		?>
		<div class="dnte-jobs" data-dnte-jobs>
			<?php if ( 'yes' === $atts['search'] ) : ?>
				<form class="dnte-jobs__search" role="search" novalidate>
					<div class="dnte-jobs__field">
						<?php echo dnte_roles_inline_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?>
						<input
							type="search"
							class="dnte-jobs__input"
							data-filter="keyword"
							placeholder="<?php esc_attr_e( 'Job title, keywords', 'dentist-exchange' ); ?>"
							aria-label="<?php esc_attr_e( 'Job title or keywords', 'dentist-exchange' ); ?>"
						/>
					</div>
					<div class="dnte-jobs__field">
						<?php echo dnte_roles_inline_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?>
						<input
							type="text"
							class="dnte-jobs__input"
							data-filter="location"
							placeholder="<?php esc_attr_e( 'City or postcode', 'dentist-exchange' ); ?>"
							aria-label="<?php esc_attr_e( 'City or postcode', 'dentist-exchange' ); ?>"
						/>
					</div>
					<?php
					/*
					 * Job Type, where the design had a radius selector. Radius
					 * needs coordinates per role to mean anything, and the CPT
					 * stores a free-text location — so this slot filters on
					 * data that actually exists. It stays in step with the
					 * tabs below, which filter the same field.
					 */
					?>
					<div class="dnte-jobs__field dnte-jobs__field--select">
						<?php echo dnte_roles_inline_icon( 'briefcase' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?>
						<select class="dnte-jobs__input" data-filter="type" aria-label="<?php esc_attr_e( 'Job type', 'dentist-exchange' ); ?>">
							<option value=""><?php esc_html_e( 'Job Type', 'dentist-exchange' ); ?></option>
							<?php foreach ( $used_types as $slug => $name ) : ?>
								<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $name ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo dnte_roles_inline_icon( 'chevron' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static markup. ?>
					</div>
					<button type="submit" class="dnte-jobs__submit"><?php esc_html_e( 'Find Jobs', 'dentist-exchange' ); ?></button>
				</form>
			<?php endif; ?>

			<?php if ( 'yes' === $atts['tabs'] && $used_types ) : ?>
				<div class="dnte-jobs__tabs" role="tablist">
					<button type="button" class="dnte-jobs__tab is-active" data-type="" role="tab" aria-selected="true">
						<?php esc_html_e( 'All Roles', 'dentist-exchange' ); ?>
					</button>
					<?php foreach ( $used_types as $slug => $name ) : ?>
						<button type="button" class="dnte-jobs__tab" data-type="<?php echo esc_attr( $slug ); ?>" role="tab" aria-selected="false">
							<?php echo esc_html( $name ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="dnte-jobs__grid" data-columns="<?php echo esc_attr( (string) $columns ); ?>">
				<?php
				foreach ( $ids as $id ) {
					echo dnte_roles_render_card( $id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped within.
				}
				?>
			</div>

			<p class="dnte-jobs__empty" hidden><?php esc_html_e( 'No roles match your search.', 'dentist-exchange' ); ?></p>
		</div>
		<?php
		return (string) ob_get_clean();
	}
endif;
add_shortcode( 'opening_roles', 'dnte_opening_roles_shortcode' );
