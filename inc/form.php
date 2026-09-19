<?php
/**
 * Contact Settings — admin page under Appearance menu.
 *
 * Options:
 *   dnte_phone_number        – Phone Number
 *   dnte_form_shortcode      – Form Shortcode
 *   dnte_form_title          – Panel heading
 *   dnte_form_description    – Panel description paragraph
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Enqueue front-end assets ───────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'dnte_form_panel_assets' );

function dnte_form_panel_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'dnte-form-panel',
		get_theme_file_uri( 'assets/css/form-panel.css' ),
		array(),
		$version
	);

	wp_enqueue_script(
		'dnte-form-panel',
		get_theme_file_uri( 'assets/js/form-panel.js' ),
		array(),
		$version,
		true
	);
}

// ── Inject panel HTML into every page footer ───────────────────────────────────

add_action( 'wp_footer', 'dnte_form_panel_html' );

function dnte_form_panel_html() {
	$phone       = get_option( 'dnte_phone_number', '' );
	$shortcode   = get_option( 'dnte_form_shortcode', '' );
	$title       = get_option( 'dnte_form_title', 'Contact us' );
	$description = get_option( 'dnte_form_description', '' );

	// Don't render the panel if neither option is set.
	if ( ! $phone && ! $shortcode ) {
		return;
	}
	?>
	<div id="dnte-form-overlay" class="dnte-form-overlay" aria-hidden="true"></div>

	<div
		id="dnte-contact"
		class="dnte-form-panel"
		role="dialog"
		aria-modal="true"
		aria-label="<?php echo esc_attr( $title ? $title : __( 'Contact us', 'dentist-exchange' ) ); ?>"
		aria-hidden="true"
	>
		<div class="dnte-form-panel__header">
			<?php if ( $phone ) : ?>
			<a href="tel:<?php echo esc_attr( preg_replace( '/[^\d+]/', '', $phone ) ); ?>" class="dnte-form-panel__phone">
				<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M3.62 7.79C5.06 10.62 7.38 12.93 10.21 14.38L12.41 12.18C12.68 11.91 13.08 11.82 13.43 11.94C14.55 12.31 15.76 12.51 17 12.51C17.55 12.51 18 12.96 18 13.51V17C18 17.55 17.55 18 17 18C7.61 18 0 10.39 0 1C0 0.45 0.45 0 1 0H4.5C5.05 0 5.5 0.45 5.5 1C5.5 2.25 5.7 3.45 6.07 4.57C6.18 4.92 6.1 5.31 5.82 5.59L3.62 7.79Z" fill="currentColor"/>
				</svg>
				<?php echo esc_html( $phone ); ?>
			</a>
			<?php else : ?>
			<span></span>
			<?php endif; ?>

			<button class="dnte-form-panel__close" aria-label="<?php esc_attr_e( 'Close form', 'dentist-exchange' ); ?>">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
				</svg>
			</button>
		</div>

		<div class="dnte-form-panel__body">
			<?php if ( $title ) : ?>
				<h2 class="dnte-form-panel__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="dnte-form-panel__desc"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>

			<?php if ( $shortcode ) : ?>
				<?php echo do_shortcode( $shortcode ); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
}

// ── Register settings ──────────────────────────────────────────────────────────

add_action( 'admin_init', 'dnte_form_register_settings' );

function dnte_form_register_settings() {
	register_setting(
		'dnte_form_group',
		'dnte_phone_number',
		array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '' )
	);

	register_setting(
		'dnte_form_group',
		'dnte_form_shortcode',
		array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => '' )
	);

	register_setting(
		'dnte_form_group',
		'dnte_form_title',
		array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field', 'default' => 'Contact us' )
	);

	register_setting(
		'dnte_form_group',
		'dnte_form_description',
		array( 'type' => 'string', 'sanitize_callback' => 'sanitize_textarea_field', 'default' => '' )
	);
}

// ── Add menu page under Appearance ────────────────────────────────────────────

add_action( 'admin_menu', 'dnte_form_add_menu' );

function dnte_form_add_menu() {
	add_theme_page(
		__( 'Form Settings', 'dentist-exchange' ),
		__( 'Form', 'dentist-exchange' ),
		'manage_options',
		'dnte-form',
		'dnte_form_render_page'
	);
}

// ── Enqueue admin assets on the Form Settings page ────────────────────────────

add_action( 'admin_enqueue_scripts', 'dnte_form_admin_assets' );

function dnte_form_admin_assets( $hook ) {
	if ( 'appearance_page_dnte-form' !== $hook ) {
		return;
	}

	wp_enqueue_script(
		'dnte-form-settings',
		get_theme_file_uri( 'assets/js/form-settings.js' ),
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}

// ── Render settings page ───────────────────────────────────────────────────────

function dnte_form_render_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Form Settings', 'dentist-exchange' ); ?></h1>

		<?php settings_errors( 'dnte_form_group' ); ?>

		<?php /* ── Trigger ID hint ── */ ?>
		<div style="
			background: #f0f6fc;
			border-left: 4px solid #2271b1;
			border-radius: 0 4px 4px 0;
			padding: 14px 18px;
			margin: 16px 0 24px;
			max-width: 600px;
		">
			<p style="margin: 0 0 8px; font-weight: 600; color: #1d2327;">
				<?php esc_html_e( 'How to open this form panel', 'dentist-exchange' ); ?>
			</p>
			<p style="margin: 0 0 10px; color: #3c434a; font-size: 13px;">
				<?php esc_html_e( 'Add the following ID as the href value on any link or button to open the slide-in form:', 'dentist-exchange' ); ?>
			</p>
			<div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
				<code id="dnte-trigger-id" style="
					background: #1d2327;
					color: #7dd3fc;
					padding: 6px 14px;
					border-radius: 4px;
					font-size: 14px;
					font-family: monospace;
					letter-spacing: 0.5px;
					user-select: all;
				">#dnte-contact</code>
				<button
					type="button"
					class="dnte-copy-button"
					data-copy="#dnte-contact"
					data-label="<?php esc_attr_e( 'Copy', 'dentist-exchange' ); ?>"
					data-copied="<?php esc_attr_e( '✓ Copied!', 'dentist-exchange' ); ?>"
					style="
						background: #2271b1;
						color: #fff;
						border: none;
						border-radius: 4px;
						padding: 5px 14px;
						font-size: 13px;
						cursor: pointer;
					"
				><?php esc_html_e( 'Copy', 'dentist-exchange' ); ?></button>
			</div>
			<p style="margin: 10px 0 0; color: #646970; font-size: 12px;">
				<?php esc_html_e( 'Example:', 'dentist-exchange' ); ?>
				<code style="background:#eee; padding: 2px 6px; border-radius: 3px;">&lt;a href="#dnte-contact"&gt;Get in Touch&lt;/a&gt;</code>
			</p>
		</div>

		<form method="post" action="options.php">
			<?php settings_fields( 'dnte_form_group' ); ?>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="dnte_phone_number">
							<?php esc_html_e( 'Phone Number', 'dentist-exchange' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							id="dnte_phone_number"
							name="dnte_phone_number"
							value="<?php echo esc_attr( get_option( 'dnte_phone_number' ) ); ?>"
							class="regular-text"
							placeholder="e.g. 818-408-7117"
						/>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="dnte_form_title">
							<?php esc_html_e( 'Form Title', 'dentist-exchange' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							id="dnte_form_title"
							name="dnte_form_title"
							value="<?php echo esc_attr( get_option( 'dnte_form_title', 'Contact us' ) ); ?>"
							class="regular-text"
							placeholder="<?php esc_attr_e( 'Contact us', 'dentist-exchange' ); ?>"
						/>
						<p class="description">
							<?php esc_html_e( 'Heading displayed at the top of the slide-in panel.', 'dentist-exchange' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="dnte_form_description">
							<?php esc_html_e( 'Form Description', 'dentist-exchange' ); ?>
						</label>
					</th>
					<td>
						<textarea
							id="dnte_form_description"
							name="dnte_form_description"
							class="regular-text"
							rows="4"
							placeholder="<?php esc_attr_e( 'We are here to help you...', 'dentist-exchange' ); ?>"
						><?php echo esc_textarea( get_option( 'dnte_form_description', '' ) ); ?></textarea>
						<p class="description">
							<?php esc_html_e( 'Short paragraph shown below the title inside the panel.', 'dentist-exchange' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="dnte_form_shortcode">
							<?php esc_html_e( 'Form Shortcode', 'dentist-exchange' ); ?>
						</label>
					</th>
					<td>
						<input
							type="text"
							id="dnte_form_shortcode"
							name="dnte_form_shortcode"
							value="<?php echo esc_attr( get_option( 'dnte_form_shortcode' ) ); ?>"
							class="regular-text"
						/>
						<p class="description">
							<?php esc_html_e( 'Enter the shortcode, e.g. [gravityform id="1" title="false"]', 'dentist-exchange' ); ?>
						</p>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
