<?php
/**
 * Dentist Exchange — Shortcodes Reference Page
 *
 * Adds an admin page under Appearance that showcases the shortcodes
 * bundled with this theme, each with a one-click copy button.
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ── Register the shortcode catalogue ──────────────────────────────────────────

if ( ! function_exists( 'dnte_get_shortcodes' ) ) :
	/**
	 * Returns the list of theme shortcodes to display on the reference page.
	 *
	 * Each entry: label, tag, description, one or more copy-ready examples,
	 * and an optional list of attributes (name => description).
	 *
	 * Examples run from the bare tag — every attribute is optional on both
	 * shortcodes — to a form spelling out each attribute at its default.
	 */
	function dnte_get_shortcodes() {
		return array(
			array(
				'title'       => __( 'Opening Roles', 'dentist-exchange' ),
				'tag'         => 'opening_roles',
				'description' => __( 'Renders open roles as a card grid: title, match percentage, Apply button, location, posting date, work arrangement, salary and employment type. Only roles switched on in the Active column of All Open Roles are listed.', 'dentist-exchange' ),
				'examples'    => array(
					array(
						'label' => __( 'Basic usage', 'dentist-exchange' ),
						'note'  => __( 'Every attribute is optional — this shows up to 10 active roles in 2 columns, newest first.', 'dentist-exchange' ),
						'code'  => '[opening_roles]',
					),
					array(
						'label' => __( 'All optional attributes', 'dentist-exchange' ),
						'note'  => __( 'Each attribute shown at its default value, except <code>type</code> and <code>nature</code>.', 'dentist-exchange' ),
						'code'  => '[opening_roles columns="2" per_page="10" type="full-time" nature="remote" order="DESC" orderby="date"]',
					),
				),
				'attrs'       => array(
					array( 'name' => 'columns',  'default' => '2',     'desc' => __( 'Columns on desktop, 1&ndash;4. Drops to a single column below 900px.', 'dentist-exchange' ) ),
					array( 'name' => 'per_page', 'default' => '10',    'desc' => __( 'How many roles to show, 1&ndash;50.', 'dentist-exchange' ) ),
					array( 'name' => 'type',     'default' => '(all)', 'desc' => __( 'Employment type: <code>full-time</code>, <code>part-time</code>, <code>hourly</code>, <code>contractual</code> or <code>internship</code>.', 'dentist-exchange' ) ),
					array( 'name' => 'nature',   'default' => '(all)', 'desc' => __( 'Work arrangement: <code>remote</code>, <code>in-house</code> or <code>hybrid</code>.', 'dentist-exchange' ) ),
					array( 'name' => 'order',    'default' => 'DESC',  'desc' => __( 'Sort direction &mdash; <code>ASC</code> or <code>DESC</code>.', 'dentist-exchange' ) ),
					array( 'name' => 'orderby',  'default' => 'date',  'desc' => __( 'Any WP_Query orderby value, such as <code>title</code> or <code>menu_order</code>.', 'dentist-exchange' ) ),
				),
			),
		);
	}
endif;

// ── Add page under Appearance ─────────────────────────────────────────────────

add_action( 'admin_menu', 'dnte_shortcodes_add_menu' );

if ( ! function_exists( 'dnte_shortcodes_add_menu' ) ) :
	function dnte_shortcodes_add_menu() {
		add_theme_page(
			__( 'Dentist Exchange Shortcodes', 'dentist-exchange' ),
			__( 'Dentist Exchange', 'dentist-exchange' ),
			'edit_theme_options',
			'dnte-shortcodes',
			'dnte_shortcodes_render_page'
		);
	}
endif;

// ── Enqueue admin assets on the Shortcodes page ────────────────────────────────

add_action( 'admin_enqueue_scripts', 'dnte_shortcodes_admin_assets' );

if ( ! function_exists( 'dnte_shortcodes_admin_assets' ) ) :
	function dnte_shortcodes_admin_assets( $hook ) {
		if ( 'appearance_page_dnte-shortcodes' !== $hook ) {
			return;
		}

		wp_enqueue_script(
			'dnte-shortcodes-copy',
			get_theme_file_uri( 'assets/js/shortcodes-copy.js' ),
			array(),
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
endif;

// ── Render the page ───────────────────────────────────────────────────────────

if ( ! function_exists( 'dnte_shortcodes_render_page' ) ) :
	function dnte_shortcodes_render_page() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		$shortcodes = dnte_get_shortcodes();
		?>
		<div class="wrap psr-wrap">

			<style>
				.psr-wrap { max-width: 960px; }
				.psr-header {
					display: flex;
					align-items: center;
					gap: 14px;
					margin: 24px 0 32px;
				}
				.psr-header__logo {
					width: 30px;
					height: 30px;
					display: flex;
					align-items: center;
					justify-content: center;
					flex-shrink: 0;
				}
				.psr-header__logo svg { display: block; }
				.psr-header__text h1 {
					margin: 0;
					font-size: 22px;
					font-weight: 600;
					line-height: 1.2;
					color: #1d2327;
				}
				.psr-header__text p {
					margin: 4px 0 0;
					color: #646970;
					font-size: 13px;
				}

				.psr-grid {
					display: flex;
					flex-direction: column;
					gap: 24px;
				}

				.psr-card {
					background: #fff;
					border: 1px solid #e2e4e7;
					border-radius: 12px;
					overflow: hidden;
				}
				.psr-card__head {
					padding: 20px 24px 16px;
					border-bottom: 1px solid #f0f0f0;
				}
				.psr-card__title-row {
					display: flex;
					align-items: center;
					gap: 10px;
					margin-bottom: 6px;
				}
				.psr-card__title {
					font-size: 16px;
					font-weight: 600;
					color: #1d2327;
					margin: 0;
				}
				.psr-card__badge {
					font-size: 11px;
					font-weight: 500;
					background: #f0f0f1;
					color: #646970;
					padding: 2px 8px;
					border-radius: 20px;
					font-family: monospace;
					letter-spacing: 0;
				}
				.psr-card__desc {
					margin: 0;
					color: #646970;
					font-size: 13px;
					line-height: 1.6;
				}
				.psr-card__desc code {
					background: #f6f7f7;
					padding: 1px 5px;
					border-radius: 3px;
					font-size: 12px;
					color: #2c3338;
				}

				.psr-card__body { padding: 20px 24px; }

				.psr-example-label {
					font-size: 11px;
					font-weight: 600;
					text-transform: uppercase;
					letter-spacing: .06em;
					color: #646970;
					margin: 0 0 8px;
				}
				.psr-example-note {
					margin: -4px 0 8px;
					color: #646970;
					font-size: 12px;
					line-height: 1.6;
				}
				.psr-example-note code {
					background: #f6f7f7;
					padding: 1px 5px;
					border-radius: 3px;
					font-size: 11px;
					color: #2c3338;
				}
				.psr-example-row {
					display: flex;
					align-items: stretch;
					gap: 0;
					border: 1px solid #e2e4e7;
					border-radius: 8px;
					overflow: hidden;
					margin-bottom: 20px;
				}
				.psr-example-row:last-of-type { margin-bottom: 24px; }
				.psr-example-code {
					flex: 1;
					background: #f6f7f7;
					padding: 12px 16px;
					margin: 0;
					font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
					font-size: 13px;
					color: #2c3338;
					white-space: pre-wrap;
					word-break: break-all;
					border: none;
					line-height: 1.6;
				}
				.psr-copy-btn {
					flex-shrink: 0;
					padding: 0 16px;
					background: #fff;
					border: none;
					border-left: 1px solid #e2e4e7;
					cursor: pointer;
					font-size: 12px;
					font-weight: 500;
					color: #2271b1;
					display: flex;
					align-items: center;
					gap: 6px;
					transition: background .15s, color .15s;
					white-space: nowrap;
				}
				.psr-copy-btn:hover { background: #f0f6fc; }
				.psr-copy-btn.copied { color: #00a32a; }
				.psr-copy-btn svg { flex-shrink: 0; }

				.psr-attrs-label {
					font-size: 11px;
					font-weight: 600;
					text-transform: uppercase;
					letter-spacing: .06em;
					color: #646970;
					margin: 0 0 10px;
				}
				.psr-attrs {
					border: 1px solid #e2e4e7;
					border-radius: 8px;
					overflow: hidden;
				}
				.psr-attr {
					display: grid;
					grid-template-columns: 160px 100px 1fr;
					gap: 0;
					border-bottom: 1px solid #f0f0f0;
				}
				.psr-attr:last-child { border-bottom: none; }
				.psr-attr__name,
				.psr-attr__default,
				.psr-attr__desc {
					padding: 10px 14px;
					font-size: 13px;
					line-height: 1.5;
				}
				.psr-attr__name {
					font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
					font-size: 12px;
					color: #9333ea;
					background: #faf5ff;
					font-weight: 500;
					border-right: 1px solid #f0f0f0;
				}
				.psr-attr__default {
					font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
					font-size: 12px;
					color: #646970;
					background: #fafafa;
					border-right: 1px solid #f0f0f0;
				}
				.psr-attr__desc { color: #3c434a; }
				.psr-attr__desc code {
					background: #f6f7f7;
					padding: 1px 5px;
					border-radius: 3px;
					font-size: 12px;
					color: #2c3338;
				}

				.psr-attr-head {
					display: grid;
					grid-template-columns: 160px 100px 1fr;
					background: #f6f7f7;
					border-bottom: 1px solid #e2e4e7;
				}
				.psr-attr-head span {
					padding: 7px 14px;
					font-size: 11px;
					font-weight: 600;
					text-transform: uppercase;
					letter-spacing: .06em;
					color: #646970;
				}
				.psr-attr-head span:not(:last-child) {
					border-right: 1px solid #e2e4e7;
				}

				.psr-footer {
					margin-top: 32px;
					padding: 16px 20px;
					background: #f6f7f7;
					border: 1px solid #e2e4e7;
					border-radius: 10px;
					font-size: 13px;
					color: #646970;
					line-height: 1.6;
				}
				.psr-footer strong { color: #1d2327; }
			</style>

			<div class="psr-header">
				<div class="psr-header__logo">
					<svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" fill="#8fa4b7"/>
					</svg>
				</div>
				<div class="psr-header__text">
					<h1><?php esc_html_e( 'Dentist Exchange Shortcodes', 'dentist-exchange' ); ?></h1>
					<p><?php esc_html_e( 'All shortcodes available in this theme. Click Copy to grab the code.', 'dentist-exchange' ); ?></p>
				</div>
			</div>

			<div class="psr-grid">
				<?php foreach ( $shortcodes as $sc ) : ?>
				<div class="psr-card">
					<div class="psr-card__head">
						<div class="psr-card__title-row">
							<h2 class="psr-card__title"><?php echo esc_html( $sc['title'] ); ?></h2>
							<span class="psr-card__badge"><?php echo esc_html( '[' . $sc['tag'] . ']' ); ?></span>
						</div>
						<p class="psr-card__desc"><?php echo wp_kses( $sc['description'], array( 'code' => array() ) ); ?></p>
					</div>

					<div class="psr-card__body">
						<?php foreach ( $sc['examples'] as $example ) : ?>
						<p class="psr-example-label"><?php echo esc_html( $example['label'] ); ?></p>
						<?php if ( ! empty( $example['note'] ) ) : ?>
						<p class="psr-example-note"><?php echo wp_kses( $example['note'], array( 'code' => array() ) ); ?></p>
						<?php endif; ?>
						<div class="psr-example-row">
							<pre class="psr-example-code"><?php echo esc_html( $example['code'] ); ?></pre>
							<button
								type="button"
								class="psr-copy-btn"
								data-code="<?php echo esc_attr( $example['code'] ); ?>"
							>
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
									<path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
								</svg>
								<?php esc_html_e( 'Copy', 'dentist-exchange' ); ?>
							</button>
						</div>
						<?php endforeach; ?>

						<?php if ( ! empty( $sc['attrs'] ) ) : ?>
						<p class="psr-attrs-label"><?php esc_html_e( 'Attributes', 'dentist-exchange' ); ?></p>
						<div class="psr-attrs">
							<div class="psr-attr-head">
								<span><?php esc_html_e( 'Attribute', 'dentist-exchange' ); ?></span>
								<span><?php esc_html_e( 'Default', 'dentist-exchange' ); ?></span>
								<span><?php esc_html_e( 'Description', 'dentist-exchange' ); ?></span>
							</div>
							<?php foreach ( $sc['attrs'] as $attr ) : ?>
							<div class="psr-attr">
								<div class="psr-attr__name"><?php echo esc_html( $attr['name'] ); ?></div>
								<div class="psr-attr__default"><?php echo esc_html( $attr['default'] ); ?></div>
								<div class="psr-attr__desc"><?php echo wp_kses( $attr['desc'], array( 'code' => array() ) ); ?></div>
							</div>
							<?php endforeach; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="psr-footer">
				<strong><?php esc_html_e( 'Tip:', 'dentist-exchange' ); ?></strong>
				<?php esc_html_e( 'Shortcodes can be placed in any post, page, or widget that supports shortcodes. In the block editor, use the Shortcode block.', 'dentist-exchange' ); ?>
			</div>

		</div>
		<?php
	}
endif;
