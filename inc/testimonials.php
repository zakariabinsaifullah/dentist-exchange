<?php
/**
 * Testimonials
 *
 * Meta fields, admin UI and the [dnte_testimonials] shortcode for the
 * Testimonial post type registered in inc/post-types.php.
 *
 * A testimonial is the reviewer's name (post title), a designation and a
 * review message. It has no front-end single view; the shortcode is the only
 * place it is ever rendered.
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const DNTE_TESTIMONIAL_POST_TYPE = 'dnte-testimonial';
const DNTE_TESTIMONIAL_DESIGNATION_KEY = '_dnte_testimonial_designation';
const DNTE_TESTIMONIAL_MESSAGE_KEY = '_dnte_testimonial_message';
const DNTE_TESTIMONIAL_VARIANT_KEY = '_dnte_testimonial_variant';

/**
 * Card colours, in the order the design cycles them.
 *
 * White, black, blue, then repeat — so the fourth card is white again. The
 * blue is #2234b0, the theme's dnte-primary-800, sampled from the design.
 *
 * Tilt runs on a two-step alternation rather than this three-step one (see
 * assets/css/testimonials.css), so the two only realign every sixth card.
 */
const DNTE_TESTIMONIAL_VARIANT_CYCLE = array( 'white', 'black', 'blue' );


if ( ! function_exists( 'dnte_testimonial_variants' ) ) :
	/**
	 * The selectable card colours.
	 *
	 * @return array<string, string> Slug => label.
	 */
	function dnte_testimonial_variants() {
		return array(
			'white' => __( 'White', 'dentist-exchange' ),
			'black' => __( 'Black', 'dentist-exchange' ),
			'blue'  => __( 'Blue', 'dentist-exchange' ),
		);
	}
endif;


if ( ! function_exists( 'dnte_testimonial_variant_for_index' ) ) :
	/**
	 * The card colour for a position in the list.
	 *
	 * Colour is assigned by position rather than stored per testimonial, so the
	 * pattern in the design holds no matter how many testimonials exist. A
	 * testimonial can opt out with the Card Colour field, which is what
	 * `$override` carries.
	 *
	 * @param int    $index    Zero-based position in the rendered list.
	 * @param string $override Per-testimonial choice, or '' to follow the cycle.
	 * @return string Variant slug.
	 */
	function dnte_testimonial_variant_for_index( $index, $override = '' ) {
		$variants = dnte_testimonial_variants();

		if ( '' !== $override && isset( $variants[ $override ] ) ) {
			return $override;
		}

		$cycle = DNTE_TESTIMONIAL_VARIANT_CYCLE;

		return $cycle[ $index % count( $cycle ) ];
	}
endif;


if ( ! function_exists( 'dnte_testimonial_auto_index' ) ) :
	/**
	 * Where a testimonial falls in the shortcode's default ordering.
	 *
	 * Used by the admin list column to show which colour the automatic cycle
	 * lands on. It assumes the shortcode's own defaults (newest first) — a
	 * shortcode given `order` or `orderby` renders a different sequence, and
	 * the colour shown here is then indicative rather than exact.
	 *
	 * The whole ID list is fetched once per request; the list table calls this
	 * for every row.
	 *
	 * @param int $post_id Testimonial ID.
	 * @return int Zero-based position, or 0 when not found.
	 */
	function dnte_testimonial_auto_index( $post_id ) {
		static $order = null;

		if ( null === $order ) {
			$ids = get_posts(
				array(
					'post_type'        => DNTE_TESTIMONIAL_POST_TYPE,
					'post_status'      => 'publish',
					'posts_per_page'   => -1,
					'orderby'          => 'date',
					'order'            => 'DESC',
					'fields'           => 'ids',
					'no_found_rows'    => true,
					'suppress_filters' => false,
				)
			);

			$order = array_flip( $ids );
		}

		return isset( $order[ $post_id ] ) ? (int) $order[ $post_id ] : 0;
	}
endif;


// =============================================================================
// Meta
// =============================================================================

if ( ! function_exists( 'dnte_register_testimonial_meta' ) ) :
	/**
	 * Registers the testimonial meta so it is sanitised consistently and
	 * deleted with the post.
	 */
	function dnte_register_testimonial_meta() {
		// The first argument is the post type: register_post_meta() sets
		// `object_subtype` from it and ignores any passed in $args, so naming
		// the type here is what scopes the meta to testimonials.
		$common = array(
			'single'        => true,
			'type'          => 'string',
			'show_in_rest'  => false,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		);

		register_post_meta(
			DNTE_TESTIMONIAL_POST_TYPE,
			DNTE_TESTIMONIAL_DESIGNATION_KEY,
			array_merge(
				$common,
				array(
					'description'       => __( 'Reviewer designation.', 'dentist-exchange' ),
					'sanitize_callback' => 'sanitize_text_field',
				)
			)
		);

		register_post_meta(
			DNTE_TESTIMONIAL_POST_TYPE,
			DNTE_TESTIMONIAL_MESSAGE_KEY,
			array_merge(
				$common,
				array(
					'description'       => __( 'Review message.', 'dentist-exchange' ),
					// Line breaks are meaningful here, so the message keeps them
					// and is escaped on output rather than stripped on input.
					'sanitize_callback' => 'sanitize_textarea_field',
				)
			)
		);

		register_post_meta(
			DNTE_TESTIMONIAL_POST_TYPE,
			DNTE_TESTIMONIAL_VARIANT_KEY,
			array_merge(
				$common,
				array(
					'description'       => __( 'Card colour override.', 'dentist-exchange' ),
					'sanitize_callback' => 'sanitize_key',
				)
			)
		);
	}
endif;
add_action( 'init', 'dnte_register_testimonial_meta' );


if ( ! function_exists( 'dnte_testimonial_meta_box' ) ) :
	/**
	 * Adds the testimonial details box to the edit screen.
	 */
	function dnte_testimonial_meta_box() {
		add_meta_box(
			'dnte-testimonial-details',
			__( 'Testimonial Details', 'dentist-exchange' ),
			'dnte_render_testimonial_meta_box',
			DNTE_TESTIMONIAL_POST_TYPE,
			'normal',
			'high'
		);
	}
endif;
add_action( 'add_meta_boxes', 'dnte_testimonial_meta_box' );


if ( ! function_exists( 'dnte_render_testimonial_meta_box' ) ) :
	/**
	 * Renders the designation, message and colour fields.
	 *
	 * @param WP_Post $post Current post.
	 */
	function dnte_render_testimonial_meta_box( $post ) {
		wp_nonce_field( 'dnte_save_testimonial', 'dnte_testimonial_nonce' );

		$designation = get_post_meta( $post->ID, DNTE_TESTIMONIAL_DESIGNATION_KEY, true );
		$message     = get_post_meta( $post->ID, DNTE_TESTIMONIAL_MESSAGE_KEY, true );
		$variant     = get_post_meta( $post->ID, DNTE_TESTIMONIAL_VARIANT_KEY, true );
		?>
		<p>
			<label for="dnte-testimonial-designation"><strong><?php esc_html_e( 'Designation', 'dentist-exchange' ); ?></strong></label><br />
			<input
				type="text"
				id="dnte-testimonial-designation"
				name="dnte_testimonial_designation"
				class="widefat"
				value="<?php echo esc_attr( $designation ); ?>"
				placeholder="<?php esc_attr_e( 'Customer', 'dentist-exchange' ); ?>"
			/>
			<span class="description"><?php esc_html_e( 'Shown under the reviewer name.', 'dentist-exchange' ); ?></span>
		</p>
		<p>
			<label for="dnte-testimonial-message"><strong><?php esc_html_e( 'Review Message', 'dentist-exchange' ); ?></strong></label><br />
			<textarea
				id="dnte-testimonial-message"
				name="dnte_testimonial_message"
				class="widefat"
				rows="6"
				placeholder="<?php esc_attr_e( 'What the reviewer said…', 'dentist-exchange' ); ?>"
			><?php echo esc_textarea( $message ); ?></textarea>
		</p>
		<p>
			<label for="dnte-testimonial-variant"><strong><?php esc_html_e( 'Card Colour', 'dentist-exchange' ); ?></strong></label><br />
			<select id="dnte-testimonial-variant" name="dnte_testimonial_variant">
				<option value=""><?php esc_html_e( 'Automatic (follows the design pattern)', 'dentist-exchange' ); ?></option>
				<?php foreach ( dnte_testimonial_variants() as $slug => $label ) : ?>
					<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $variant, $slug ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<span class="description">
				<?php esc_html_e( 'Leave automatic unless this testimonial must always be a particular colour. The quote icon follows the card colour either way.', 'dentist-exchange' ); ?>
			</span>
		</p>
		<?php
	}
endif;


if ( ! function_exists( 'dnte_save_testimonial_meta' ) ) :
	/**
	 * Saves the testimonial fields.
	 *
	 * @param int $post_id Post ID.
	 */
	function dnte_save_testimonial_meta( $post_id ) {
		if ( ! isset( $_POST['dnte_testimonial_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dnte_testimonial_nonce'] ) ), 'dnte_save_testimonial' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$designation = isset( $_POST['dnte_testimonial_designation'] )
			? sanitize_text_field( wp_unslash( $_POST['dnte_testimonial_designation'] ) )
			: '';

		$message = isset( $_POST['dnte_testimonial_message'] )
			? sanitize_textarea_field( wp_unslash( $_POST['dnte_testimonial_message'] ) )
			: '';

		$variant = isset( $_POST['dnte_testimonial_variant'] )
			? sanitize_key( wp_unslash( $_POST['dnte_testimonial_variant'] ) )
			: '';

		if ( ! isset( dnte_testimonial_variants()[ $variant ] ) ) {
			$variant = '';
		}

		update_post_meta( $post_id, DNTE_TESTIMONIAL_DESIGNATION_KEY, $designation );
		update_post_meta( $post_id, DNTE_TESTIMONIAL_MESSAGE_KEY, $message );
		update_post_meta( $post_id, DNTE_TESTIMONIAL_VARIANT_KEY, $variant );
	}
endif;
add_action( 'save_post_' . DNTE_TESTIMONIAL_POST_TYPE, 'dnte_save_testimonial_meta' );


if ( ! function_exists( 'dnte_testimonial_admin_columns' ) ) :
	/**
	 * Shows the designation in the list table, so reviewers are
	 * distinguishable at a glance.
	 *
	 * @param array $columns Existing columns.
	 * @return array Modified columns.
	 */
	function dnte_testimonial_admin_columns( $columns ) {
		$date = $columns['date'] ?? null;
		unset( $columns['date'] );

		$columns['dnte_designation'] = __( 'Designation', 'dentist-exchange' );
		$columns['dnte_colour']      = __( 'Card Colour', 'dentist-exchange' );

		if ( $date ) {
			$columns['date'] = $date;
		}

		return $columns;
	}
endif;
add_filter( 'manage_' . DNTE_TESTIMONIAL_POST_TYPE . '_posts_columns', 'dnte_testimonial_admin_columns' );


if ( ! function_exists( 'dnte_testimonial_admin_column_content' ) ) :
	/**
	 * Fills the custom list table column.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post ID.
	 */
	function dnte_testimonial_admin_column_content( $column, $post_id ) {
		if ( 'dnte_designation' === $column ) {
			echo esc_html( get_post_meta( $post_id, DNTE_TESTIMONIAL_DESIGNATION_KEY, true ) );
			return;
		}

		if ( 'dnte_colour' !== $column ) {
			return;
		}

		$override = (string) get_post_meta( $post_id, DNTE_TESTIMONIAL_VARIANT_KEY, true );
		$index    = dnte_testimonial_auto_index( $post_id );
		$variant  = dnte_testimonial_variant_for_index( $index, $override );
		$labels   = dnte_testimonial_variants();

		$swatch = array(
			'white' => '#ffffff',
			'black' => '#000000',
			'blue'  => '#2234b0',
		);

		printf(
			'<span style="display:inline-block;width:14px;height:14px;border-radius:3px;border:1px solid #c3c4c7;background:%1$s;vertical-align:-2px;margin-right:6px;"></span>%2$s',
			esc_attr( $swatch[ $variant ] ?? '#ffffff' ),
			esc_html( $labels[ $variant ] ?? $variant )
		);

		if ( '' === $override ) {
			echo ' <span style="color:#646970;">' . esc_html__( '(automatic)', 'dentist-exchange' ) . '</span>';
		}
	}
endif;
add_action( 'manage_' . DNTE_TESTIMONIAL_POST_TYPE . '_posts_custom_column', 'dnte_testimonial_admin_column_content', 10, 2 );


// =============================================================================
// Settings
// =============================================================================

const DNTE_TESTIMONIAL_OPTION = 'dnte_testimonial_settings';


if ( ! function_exists( 'dnte_testimonial_settings' ) ) :
	/**
	 * Carousel settings, with defaults filled in.
	 *
	 * These are the shortcode's defaults; an attribute written into the
	 * shortcode still wins over them.
	 *
	 * @return array{autoplay: bool, speed: int, loop: bool, pagination: bool}
	 */
	function dnte_testimonial_settings() {
		$saved = get_option( DNTE_TESTIMONIAL_OPTION, array() );

		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		return array(
			'autoplay'   => ! empty( $saved['autoplay'] ),
			'speed'      => isset( $saved['speed'] ) ? max( 1000, (int) $saved['speed'] ) : 5000,

			/*
			 * Off by default. Looping makes Swiper ignore centeredSlidesBounds,
			 * which is what keeps the first card flush with the left edge, so
			 * the default favours the design's framing over endless scrolling.
			 */
			'loop'       => ! empty( $saved['loop'] ),
			'pagination' => ! isset( $saved['pagination'] ) ? true : ! empty( $saved['pagination'] ),
		);
	}
endif;


if ( ! function_exists( 'dnte_testimonial_sanitize_settings' ) ) :
	/**
	 * Sanitises the settings form.
	 *
	 * @param mixed $input Raw submission.
	 * @return array Clean settings.
	 */
	function dnte_testimonial_sanitize_settings( $input ) {
		$input = is_array( $input ) ? $input : array();

		return array(
			'autoplay'   => ! empty( $input['autoplay'] ) ? 1 : 0,
			'speed'      => isset( $input['speed'] ) ? max( 1000, (int) $input['speed'] ) : 5000,
			'loop'       => ! empty( $input['loop'] ) ? 1 : 0,
			'pagination' => ! empty( $input['pagination'] ) ? 1 : 0,
		);
	}
endif;


if ( ! function_exists( 'dnte_testimonial_register_settings' ) ) :
	/**
	 * Registers the settings store.
	 */
	function dnte_testimonial_register_settings() {
		register_setting(
			'dnte_testimonial_settings_group',
			DNTE_TESTIMONIAL_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => 'dnte_testimonial_sanitize_settings',
				'default'           => array(),
			)
		);
	}
endif;
add_action( 'admin_init', 'dnte_testimonial_register_settings' );


if ( ! function_exists( 'dnte_testimonial_settings_menu' ) ) :
	/**
	 * Adds the settings screen under the Testimonials menu.
	 */
	function dnte_testimonial_settings_menu() {
		add_submenu_page(
			'edit.php?post_type=' . DNTE_TESTIMONIAL_POST_TYPE,
			__( 'Testimonial Settings', 'dentist-exchange' ),
			__( 'Settings', 'dentist-exchange' ),
			'manage_options',
			'dnte-testimonial-settings',
			'dnte_testimonial_render_settings_page'
		);
	}
endif;
add_action( 'admin_menu', 'dnte_testimonial_settings_menu' );


if ( ! function_exists( 'dnte_testimonial_render_settings_page' ) ) :
	/**
	 * Renders the carousel settings form.
	 */
	function dnte_testimonial_render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = dnte_testimonial_settings();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Testimonial Settings', 'dentist-exchange' ); ?></h1>
			<p>
				<?php esc_html_e( 'Defaults for the testimonials carousel. An attribute written into the shortcode overrides whatever is set here.', 'dentist-exchange' ); ?>
				<code>[dnte_testimonials]</code>
			</p>
			<form method="post" action="options.php">
				<?php settings_fields( 'dnte_testimonial_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Autoplay', 'dentist-exchange' ); ?></th>
						<td>
							<label>
								<input
									type="checkbox"
									name="<?php echo esc_attr( DNTE_TESTIMONIAL_OPTION ); ?>[autoplay]"
									value="1"
									<?php checked( $settings['autoplay'] ); ?>
								/>
								<?php esc_html_e( 'Advance the cards automatically', 'dentist-exchange' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Always off for visitors who have asked their system for reduced motion.', 'dentist-exchange' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="dnte-testimonial-speed"><?php esc_html_e( 'Autoplay Speed', 'dentist-exchange' ); ?></label>
						</th>
						<td>
							<input
								type="number"
								id="dnte-testimonial-speed"
								name="<?php echo esc_attr( DNTE_TESTIMONIAL_OPTION ); ?>[speed]"
								value="<?php echo esc_attr( (string) $settings['speed'] ); ?>"
								min="1000"
								step="500"
								class="small-text"
							/>
							<?php esc_html_e( 'milliseconds', 'dentist-exchange' ); ?>
							<p class="description"><?php esc_html_e( 'How long each card is held. Minimum 1000.', 'dentist-exchange' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Loop', 'dentist-exchange' ); ?></th>
						<td>
							<label>
								<input
									type="checkbox"
									name="<?php echo esc_attr( DNTE_TESTIMONIAL_OPTION ); ?>[loop]"
									value="1"
									<?php checked( $settings['loop'] ); ?>
								/>
								<?php esc_html_e( 'Wrap around from the last card to the first', 'dentist-exchange' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Off by default. Looping makes the deck start part-way in rather than flush with the left edge, because an endless track has no start or end to align to.', 'dentist-exchange' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Pagination', 'dentist-exchange' ); ?></th>
						<td>
							<label>
								<input
									type="checkbox"
									name="<?php echo esc_attr( DNTE_TESTIMONIAL_OPTION ); ?>[pagination]"
									value="1"
									<?php checked( $settings['pagination'] ); ?>
								/>
								<?php esc_html_e( 'Show the dots beneath the carousel', 'dentist-exchange' ); ?>
							</label>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
endif;


// =============================================================================
// Shortcode
// =============================================================================

if ( ! function_exists( 'dnte_testimonials_shortcode' ) ) :
	/**
	 * Renders the testimonials carousel.
	 *
	 * Usage: [dnte_testimonials count="-1" order="DESC" orderby="date" autoplay="yes" speed="5000"]
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Markup, or '' when there is nothing to show.
	 */
	function dnte_testimonials_shortcode( $atts ) {
		// Settings supply the defaults; a shortcode attribute overrides them.
		$settings = dnte_testimonial_settings();

		$atts = shortcode_atts(
			array(
				'count'      => -1,
				'order'      => 'DESC',
				'orderby'    => 'date',
				'autoplay'   => $settings['autoplay'] ? 'yes' : 'no',
				'speed'      => $settings['speed'],
				'loop'       => $settings['loop'] ? 'yes' : 'no',
				'pagination' => $settings['pagination'] ? 'yes' : 'no',
			),
			$atts,
			'dnte_testimonials'
		);

		$query = new WP_Query(
			array(
				'post_type'              => DNTE_TESTIMONIAL_POST_TYPE,
				'post_status'            => 'publish',
				'posts_per_page'         => (int) $atts['count'],
				'order'                  => 'ASC' === strtoupper( $atts['order'] ) ? 'ASC' : 'DESC',
				'orderby'                => sanitize_key( $atts['orderby'] ),
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
			)
		);

		if ( ! $query->have_posts() ) {
			return '';
		}

		dnte_enqueue_testimonial_assets();

		$options = wp_json_encode(
			array(
				'autoplay' => 'yes' === $atts['autoplay'],
				'delay'    => max( 1000, (int) $atts['speed'] ),
				'loop'     => 'yes' === $atts['loop'],
			)
		);

		ob_start();
		?>
		<div class="dnte-testimonials">
			<div class="swiper dnte-testimonials__swiper" data-dnte-testimonials="<?php echo esc_attr( $options ); ?>">
				<div class="swiper-wrapper">
					<?php
					$index = 0;
					while ( $query->have_posts() ) :
						$query->the_post();
						$post_id = get_the_ID();

						$variant = dnte_testimonial_variant_for_index(
							$index,
							(string) get_post_meta( $post_id, DNTE_TESTIMONIAL_VARIANT_KEY, true )
						);

						$message     = (string) get_post_meta( $post_id, DNTE_TESTIMONIAL_MESSAGE_KEY, true );
						$designation = (string) get_post_meta( $post_id, DNTE_TESTIMONIAL_DESIGNATION_KEY, true );
						?>
						<div class="swiper-slide dnte-testimonial is-<?php echo esc_attr( $variant ); ?>">
							<figure class="dnte-testimonial__card">
								<span class="dnte-testimonial__quote" aria-hidden="true"></span>
								<?php if ( '' !== $message ) : ?>
									<blockquote class="dnte-testimonial__message">
										<?php echo nl2br( esc_html( $message ) ); ?>
									</blockquote>
								<?php endif; ?>
								<figcaption class="dnte-testimonial__author">
									<span class="dnte-testimonial__name"><?php the_title(); ?></span>
									<?php if ( '' !== $designation ) : ?>
										<span class="dnte-testimonial__designation"><?php echo esc_html( $designation ); ?></span>
									<?php endif; ?>
								</figcaption>
							</figure>
						</div>
						<?php
						++$index;
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
			<?php if ( 'yes' === $atts['pagination'] ) : ?>
				<div class="dnte-testimonials__pagination swiper-pagination"></div>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}
endif;
add_shortcode( 'dnte_testimonials', 'dnte_testimonials_shortcode' );


if ( ! function_exists( 'dnte_enqueue_testimonial_assets' ) ) :
	/**
	 * Enqueues the carousel assets, and hands the stylesheet the quote glyph
	 * URL so the badge can be tinted per card colour rather than shipping a
	 * separate icon for each.
	 */
	function dnte_enqueue_testimonial_assets() {
		$version = wp_get_theme()->get( 'Version' );

		wp_enqueue_style( 'dnte-swiper-style' );
		wp_enqueue_script( 'dnte-swiper-script' );

		wp_enqueue_style(
			'dnte-testimonials',
			get_theme_file_uri( 'assets/css/testimonials.css' ),
			array( 'dnte-swiper-style' ),
			$version
		);

		wp_enqueue_script(
			'dnte-testimonials',
			get_theme_file_uri( 'assets/js/testimonials.js' ),
			array( 'dnte-swiper-script' ),
			$version,
			true
		);

		$glyph = get_theme_file_uri( 'assets/images/testimonials/quote-glyph.png' );

		wp_add_inline_style(
			'dnte-testimonials',
			'.dnte-testimonials{--dnte-testimonial-quote:url("' . esc_url_raw( $glyph ) . '");}'
		);
	}
endif;
