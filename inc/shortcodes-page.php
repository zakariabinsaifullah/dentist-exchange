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
				'description' => __( 'Renders the job board: a search bar, Job Type tabs and a grid of role cards showing the symbolic icon, the role title and its job type with the vacancy count. Only roles switched on in the Active column of All Open Roles are listed. Add roles under <code>Open Roles</code>, and their categories under <code>Open Roles &rarr; Job Types</code>.', 'dentist-exchange' ),
				'examples'    => array(
					array(
						'label' => __( 'Basic usage', 'dentist-exchange' ),
						'note'  => __( 'Every attribute is optional — this shows all active roles in 4 columns, newest first, with the search bar and tabs.', 'dentist-exchange' ),
						'code'  => '[opening_roles]',
					),
					array(
						'label' => __( 'Just the grid', 'dentist-exchange' ),
						'note'  => __( 'Drops the search bar and the tabs, for a section that only lists roles.', 'dentist-exchange' ),
						'code'  => '[opening_roles search="no" tabs="no" per_page="8"]',
					),
					array(
						'label' => __( 'All optional attributes', 'dentist-exchange' ),
						'note'  => __( 'Each attribute shown at its default value.', 'dentist-exchange' ),
						'code'  => '[opening_roles columns="4" per_page="-1" order="DESC" orderby="date" search="yes" tabs="yes"]',
					),
				),
				'attrs'       => array(
					array( 'name' => 'columns',  'default' => '4',    'desc' => __( 'Columns on desktop, 1&ndash;4. Drops to 2 below 1024px and to 1 below 600px.', 'dentist-exchange' ) ),
					array( 'name' => 'per_page', 'default' => '-1',   'desc' => __( 'How many roles to show. <code>-1</code> shows every active one.', 'dentist-exchange' ) ),
					array( 'name' => 'order',    'default' => 'DESC', 'desc' => __( 'Sort direction &mdash; <code>ASC</code> or <code>DESC</code>.', 'dentist-exchange' ) ),
					array( 'name' => 'orderby',  'default' => 'date', 'desc' => __( 'Any WP_Query orderby value. Use <code>menu_order</code> with each role&rsquo;s Order field to sequence them by hand.', 'dentist-exchange' ) ),
					array( 'name' => 'search',   'default' => 'yes',  'desc' => __( 'Show the search bar &mdash; <code>yes</code> or <code>no</code>. It filters by keyword (title and Tags), by city and by job type.', 'dentist-exchange' ) ),
					array( 'name' => 'tabs',     'default' => 'yes',  'desc' => __( 'Show the Job Type tabs &mdash; <code>yes</code> or <code>no</code>. Only job types that have an active role behind them appear.', 'dentist-exchange' ) ),
				),
			),
			array(
				'title'       => __( 'Posts Grid', 'dentist-exchange' ),
				'tag'         => 'dnte_posts_grid',
				'description' => __( 'Renders posts as a three-column grid of cards: featured image, a category pill beside the date, the title, the excerpt and a Learn More button. Category tabs and pagination filter the grid in place, without reloading the page. The card text is white, so place this on a dark section.', 'dentist-exchange' ),
				'examples'    => array(
					array(
						'label' => __( 'Basic usage', 'dentist-exchange' ),
						'note'  => __( 'Every attribute is optional — this shows the 6 newest posts with tabs for every category that has posts in it.', 'dentist-exchange' ),
						'code'  => '[dnte_posts_grid]',
					),
					array(
						'label' => __( 'Only certain categories', 'dentist-exchange' ),
						'note'  => __( 'Limits both the posts and the tabs to the categories you name, by slug or by ID.', 'dentist-exchange' ),
						'code'  => '[dnte_posts_grid per_page="9" categories="buying-a-practice,valuation"]',
					),
					array(
						'label' => __( 'Tabs somewhere else on the page', 'dentist-exchange' ),
						'note'  => __( 'Give the grid an <code>id</code> and it renders without tabs; a separate [dnte_posts_tabs] block with a matching <code>for</code> then drives it. Useful when the tabs belong in their own row above a full-width grid.', 'dentist-exchange' ),
						'code'  => '[dnte_posts_grid id="blog" per_page="6"]',
					),
					array(
						'label' => __( 'All optional attributes', 'dentist-exchange' ),
						'note'  => __( 'Each attribute shown at its default value.', 'dentist-exchange' ),
						'code'  => '[dnte_posts_grid per_page="6" post_type="post" categories="" id=""]',
					),
				),
				'attrs'       => array(
					array( 'name' => 'per_page',   'default' => '6',    'desc' => __( 'Posts per page, up to 50. Anything beyond that count is reached through the pagination beneath the grid.', 'dentist-exchange' ) ),
					array( 'name' => 'post_type',  'default' => 'post', 'desc' => __( 'Which post type to list. The tabs follow that type&rsquo;s own hierarchical taxonomy.', 'dentist-exchange' ) ),
					array( 'name' => 'categories', 'default' => '',     'desc' => __( 'Comma-separated term slugs or IDs. Leave empty for every category that has posts in it.', 'dentist-exchange' ) ),
					array( 'name' => 'id',         'default' => '',     'desc' => __( 'Set this to move the tabs out of the grid and into a [dnte_posts_tabs] block with the same value in its <code>for</code> attribute.', 'dentist-exchange' ) ),
				),
			),
			array(
				'title'       => __( 'Posts Tabs', 'dentist-exchange' ),
				'tag'         => 'dnte_posts_tabs',
				'description' => __( 'The category tabs on their own, for driving a [dnte_posts_grid] placed elsewhere on the page. Only needed when the two have to sit in separate blocks &mdash; a grid without an <code>id</code> already draws its own tabs.', 'dentist-exchange' ),
				'examples'    => array(
					array(
						'label' => __( 'Paired with a grid', 'dentist-exchange' ),
						'note'  => __( 'The <code>for</code> here must match the <code>id</code> on the grid, and <code>categories</code> and <code>post_type</code> must match what the grid was given.', 'dentist-exchange' ),
						'code'  => '[dnte_posts_tabs for="blog"]',
					),
					array(
						'label' => __( 'All optional attributes', 'dentist-exchange' ),
						'note'  => __( 'Only <code>for</code> is required — without it nothing renders.', 'dentist-exchange' ),
						'code'  => '[dnte_posts_tabs for="blog" post_type="post" categories=""]',
					),
				),
				'attrs'       => array(
					array( 'name' => 'for',        'default' => '',     'desc' => __( 'Required. The <code>id</code> of the grid these tabs control.', 'dentist-exchange' ) ),
					array( 'name' => 'post_type',  'default' => 'post', 'desc' => __( 'Must match the grid&rsquo;s <code>post_type</code>.', 'dentist-exchange' ) ),
					array( 'name' => 'categories', 'default' => '',     'desc' => __( 'Must match the grid&rsquo;s <code>categories</code>, so both show the same set of tabs.', 'dentist-exchange' ) ),
				),
			),
			array(
				'title'       => __( 'Testimonials', 'dentist-exchange' ),
				'tag'         => 'dnte_testimonials',
				'description' => __( 'Renders published testimonials as a swipeable deck of tilted cards, each showing the quote icon, the review message, the reviewer name and their designation. Add entries under <code>Testimonials</code> in the admin menu. Autoplay, speed, loop and pagination default to whatever is set in <code>Testimonials &rarr; Settings</code>; the attributes below override them per shortcode.', 'dentist-exchange' ),
				'examples'    => array(
					array(
						'label' => __( 'Basic usage', 'dentist-exchange' ),
						'note'  => __( 'Every attribute is optional — this shows all published testimonials, newest first, autoplaying.', 'dentist-exchange' ),
						'code'  => '[dnte_testimonials]',
					),
					array(
						'label' => __( 'A fixed set, in the order you arranged them', 'dentist-exchange' ),
						'note'  => __( 'Pair <code>orderby="menu_order"</code> with the Order field on each testimonial to control the sequence by hand.', 'dentist-exchange' ),
						'code'  => '[dnte_testimonials count="6" order="ASC" orderby="menu_order"]',
					),
					array(
						'label' => __( 'Overriding the settings for one carousel', 'dentist-exchange' ),
						'note'  => __( 'Autoplay this instance regardless of what <code>Testimonials &rarr; Settings</code> says.', 'dentist-exchange' ),
						'code'  => '[dnte_testimonials autoplay="yes" speed="4000" loop="yes" pagination="yes"]',
					),
				),
				'attrs'       => array(
					array( 'name' => 'count',      'default' => '-1',         'desc' => __( 'How many testimonials to show. <code>-1</code> shows every published one.', 'dentist-exchange' ) ),
					array( 'name' => 'order',      'default' => 'DESC',       'desc' => __( 'Sort direction &mdash; <code>ASC</code> or <code>DESC</code>.', 'dentist-exchange' ) ),
					array( 'name' => 'orderby',    'default' => 'date',       'desc' => __( 'Any WP_Query orderby value. Use <code>menu_order</code> to order them by hand, or <code>rand</code> to shuffle.', 'dentist-exchange' ) ),
					array( 'name' => 'autoplay',   'default' => '(settings)', 'desc' => __( 'Advance on its own &mdash; <code>yes</code> or <code>no</code>. Off by default, and always off for visitors who have asked for reduced motion.', 'dentist-exchange' ) ),
					array( 'name' => 'speed',      'default' => '(settings)', 'desc' => __( 'Milliseconds each card is held when autoplaying. Values below 1000 are raised to 1000.', 'dentist-exchange' ) ),
					array( 'name' => 'loop',       'default' => '(settings)', 'desc' => __( 'Wrap around from the last card to the first &mdash; <code>yes</code> or <code>no</code>.', 'dentist-exchange' ) ),
					array( 'name' => 'pagination', 'default' => '(settings)', 'desc' => __( 'Show the dots beneath the carousel &mdash; <code>yes</code> or <code>no</code>.', 'dentist-exchange' ) ),
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
