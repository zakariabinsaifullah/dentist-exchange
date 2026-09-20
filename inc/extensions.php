<?php
/**
 * Block Editor Extensions
 *
 * Enqueues assets for block editor extensions that live in build/extensions/.
 * Extensions are different from custom blocks — they extend existing blocks
 * rather than registering new block types.
 *
 * @package Dentist_Exchange
 */

if ( ! function_exists( 'dnte_enqueue_hover_color_editor_assets' ) ) :
	/**
	 * Enqueues the hover-color extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_hover_color_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/hover-color/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-hover-color-extension',
			get_theme_file_uri( 'build/extensions/hover-color/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/hover-color/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-hover-color-extension',
				get_theme_file_uri( 'build/extensions/hover-color/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_hover_color_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_hover_color_frontend_assets' ) ) :
	/**
	 * Enqueues the hover-color extension frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_hover_color_frontend_assets() {
		$asset_file  = get_theme_file_path( 'build/extensions/hover-color/index.asset.php' );
		$style_file  = get_theme_file_path( 'build/extensions/hover-color/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-hover-color-extension-style',
			get_theme_file_uri( 'build/extensions/hover-color/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_hover_color_frontend_assets' );


if ( ! function_exists( 'dnte_render_hover_color_attributes' ) ) :
	/**
	 * Injects hover-color CSS classes and custom properties into block HTML on the frontend.
	 *
	 * The editor applies these via the JS `editor.BlockListBlock` filter, which has no effect
	 * on saved frontend markup. This PHP `render_block` filter replicates that logic so the
	 * CSS variables and classes are present in the rendered HTML.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_hover_color_attributes( $block_content, $block ) {
		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$attrs = $block['attrs'] ?? array();

		// Colors use the WordPress preset-slug + custom-value pair (withColors model).
		$hover_text_color             = $attrs['hoverTextColor'] ?? '';
		$custom_hover_text_color      = $attrs['customHoverTextColor'] ?? '';
		$hover_background_color       = $attrs['hoverBackgroundColor'] ?? '';
		$custom_hover_background_color = $attrs['customHoverBackgroundColor'] ?? '';
		$hover_border_color           = $attrs['hoverBorderColor'] ?? '';
		$custom_hover_border_color    = $attrs['customHoverBorderColor'] ?? '';
		$hover_transition_duration    = $attrs['hoverTransitionDuration'] ?? 200;
		$hover_transition_timing      = $attrs['hoverTransitionTiming'] ?? 'cubic-bezier(0.4, 0, 0.2, 1)';

		$has_hover_text   = $hover_text_color || $custom_hover_text_color;
		$has_hover_bg     = $hover_background_color || $custom_hover_background_color;
		$has_hover_border = $hover_border_color || $custom_hover_border_color;

		if ( ! $has_hover_text && ! $has_hover_bg && ! $has_hover_border ) {
			return $block_content;
		}

		// Resolve a preset slug to its CSS var, else fall back to the custom value.
		$get_color_value = function ( $preset, $custom ) {
			if ( $preset ) {
				return 'var(--wp--preset--color--' . $preset . ')';
			}
			return $custom ?: '';
		};

		// Build CSS custom properties string.
		$css_vars = array();
		if ( $has_hover_text ) {
			$css_vars[] = '--hover-color:' . $get_color_value( $hover_text_color, $custom_hover_text_color );
		}
		if ( $has_hover_bg ) {
			$css_vars[] = '--hover-background-color:' . $get_color_value( $hover_background_color, $custom_hover_background_color );
		}
		if ( $has_hover_border ) {
			$css_vars[] = '--hover-br-color:' . $get_color_value( $hover_border_color, $custom_hover_border_color );
		}
		$css_vars[] = '--hover-transition-duration:' . intval( $hover_transition_duration ) . 'ms';
		$css_vars[] = '--hover-transition-timing:' . $hover_transition_timing;

		// Build class list (matches selectors in hover-color/style.scss).
		$new_classes = array();
		if ( $has_hover_text ) {
			$new_classes[] = 'has-hover__color';
		}
		if ( $has_hover_bg ) {
			$new_classes[] = 'has-hover__background-color';
		}
		if ( $has_hover_border ) {
			$new_classes[] = 'has-hover__border-color';
		}

		// Use WP_HTML_Tag_Processor (WP 6.2+) for safe attribute manipulation.
		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			// Append classes.
			foreach ( $new_classes as $class ) {
				$processor->add_class( $class );
			}

			// Merge CSS variables into the existing style attribute.
			$existing_style = $processor->get_attribute( 'style' ) ?? '';
			$new_style      = rtrim( $existing_style, '; ' );
			if ( $new_style ) {
				$new_style .= ';';
			}
			$new_style .= implode( ';', $css_vars );
			$processor->set_attribute( 'style', $new_style );

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_hover_color_attributes', 10, 2 );


// =============================================================================
// Group – Force Full Height Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_group_full_height_editor_assets' ) ) :
	/**
	 * Enqueues the group-full-height extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_group_full_height_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/group-full-height/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-group-full-height-extension',
			get_theme_file_uri( 'build/extensions/group-full-height/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/group-full-height/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-group-full-height-extension',
				get_theme_file_uri( 'build/extensions/group-full-height/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_group_full_height_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_group_full_height_frontend_assets' ) ) :
	/**
	 * Enqueues the group-full-height frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_group_full_height_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/group-full-height/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/group-full-height/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-group-full-height-extension-style',
			get_theme_file_uri( 'build/extensions/group-full-height/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_group_full_height_frontend_assets' );


if ( ! function_exists( 'dnte_render_group_full_height' ) ) :
	/**
	 * Injects the `has-force-full-height` class into core/group blocks on the frontend
	 * when the `forceFullHeight` attribute is enabled.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_group_full_height( $block_content, $block ) {
		if ( 'core/group' !== $block['blockName'] ) {
			return $block_content;
		}

		if ( empty( $block['attrs']['forceFullHeight'] ) ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'has-force-full-height' );
			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_group_full_height', 10, 2 );

// =============================================================================
// Group – Overlay Background Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_group_overlay_bg_editor_assets' ) ) :
	/**
	 * Enqueues the group-overlay-bg extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_group_overlay_bg_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/group-overlay-bg/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-group-overlay-bg-extension',
			get_theme_file_uri( 'build/extensions/group-overlay-bg/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/group-overlay-bg/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-group-overlay-bg-extension',
				get_theme_file_uri( 'build/extensions/group-overlay-bg/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_group_overlay_bg_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_group_overlay_bg_frontend_assets' ) ) :
	/**
	 * Enqueues the group-overlay-bg frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_group_overlay_bg_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/group-overlay-bg/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/group-overlay-bg/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-group-overlay-bg-extension-style',
			get_theme_file_uri( 'build/extensions/group-overlay-bg/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_group_overlay_bg_frontend_assets' );


if ( ! function_exists( 'dnte_render_group_overlay_bg' ) ) :
	/**
	 * Injects the `dnte-overlay-bg` class and `--dnte-overlay-bg` CSS custom property
	 * into core/group blocks on the frontend when an overlay background is set.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_group_overlay_bg( $block_content, $block ) {
		if ( 'core/group' !== $block['blockName'] ) {
			return $block_content;
		}

		$attrs                   = $block['attrs'] ?? array();
		$overlay_bg_color        = $attrs['overlayBgColor'] ?? '';
		$custom_overlay_bg_color = $attrs['customOverlayBgColor'] ?? '';
		$overlay_bg_gradient     = $attrs['overlayBgGradient'] ?? '';

		if ( ! $overlay_bg_color && ! $custom_overlay_bg_color && ! $overlay_bg_gradient ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		// Gradient wins; otherwise a preset slug resolves to its CSS var, else the custom value.
		if ( $overlay_bg_gradient ) {
			$bg_value = $overlay_bg_gradient;
		} elseif ( $overlay_bg_color ) {
			$bg_value = 'var(--wp--preset--color--' . $overlay_bg_color . ')';
		} else {
			$bg_value = $custom_overlay_bg_color;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'dnte-overlay-bg' );

			$existing_style = $processor->get_attribute( 'style' ) ?? '';
			$new_style      = rtrim( $existing_style, '; ' );
			if ( $new_style ) {
				$new_style .= ';';
			}
			$new_style .= '--dnte-overlay-bg:' . $bg_value;
			$processor->set_attribute( 'style', $new_style );

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_group_overlay_bg', 10, 2 );


// =============================================================================
// Group – Global Hover Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_group_global_hover_editor_assets' ) ) :
	/**
	 * Enqueues the group-global-hover extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_group_global_hover_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/group-global-hover/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-group-global-hover-extension',
			get_theme_file_uri( 'build/extensions/group-global-hover/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/group-global-hover/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-group-global-hover-extension',
				get_theme_file_uri( 'build/extensions/group-global-hover/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_group_global_hover_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_group_global_hover_frontend_assets' ) ) :
	/**
	 * Enqueues the group-global-hover frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_group_global_hover_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/group-global-hover/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/group-global-hover/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-group-global-hover-extension-style',
			get_theme_file_uri( 'build/extensions/group-global-hover/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_group_global_hover_frontend_assets' );


if ( ! function_exists( 'dnte_render_group_global_hover' ) ) :
	/**
	 * Injects `dnte-global-hover` class + CSS variables into core/group blocks on the
	 * frontend when the global hover feature is enabled.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_group_global_hover( $block_content, $block ) {
		if ( 'core/group' !== $block['blockName'] ) {
			return $block_content;
		}

		$attrs = $block['attrs'] ?? array();

		if ( empty( $attrs['globalHoverEnabled'] ) ) {
			return $block_content;
		}

		// Colors use the WordPress preset-slug + custom-value pair (withColors model).
		$bg_color        = $attrs['globalHoverBgColor'] ?? '';
		$custom_bg_color = $attrs['customGlobalHoverBgColor'] ?? '';
		$color           = $attrs['globalHoverColor'] ?? '';
		$custom_color    = $attrs['customGlobalHoverColor'] ?? '';

		$has_bg    = $bg_color || $custom_bg_color;
		$has_color = $color || $custom_color;

		if ( ! $has_bg && ! $has_color ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$css_vars = array();

		if ( $has_bg ) {
			$css_vars[] = '--dnte-ghover-bg:' . ( $bg_color ? 'var(--wp--preset--color--' . $bg_color . ')' : $custom_bg_color );
		}

		if ( $has_color ) {
			$css_vars[] = '--dnte-ghover-color:' . ( $color ? 'var(--wp--preset--color--' . $color . ')' : $custom_color );
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'dnte-global-hover' );

			$existing_style = $processor->get_attribute( 'style' ) ?? '';
			$new_style      = rtrim( $existing_style, '; ' );
			if ( $new_style ) {
				$new_style .= ';';
			}
			$new_style .= implode( ';', $css_vars );
			$processor->set_attribute( 'style', $new_style );

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_group_global_hover', 10, 2 );

// =============================================================================
// Heading & Paragraph – Max Width Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_text_max_width_editor_assets' ) ) :
	/**
	 * Enqueues the text-max-width extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_text_max_width_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/text-max-width/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-text-max-width-extension',
			get_theme_file_uri( 'build/extensions/text-max-width/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/text-max-width/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-text-max-width-extension',
				get_theme_file_uri( 'build/extensions/text-max-width/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_text_max_width_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_text_max_width_frontend_assets' ) ) :
	/**
	 * Enqueues the text-max-width frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_text_max_width_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/text-max-width/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/text-max-width/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-text-max-width-extension-style',
			get_theme_file_uri( 'build/extensions/text-max-width/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_text_max_width_frontend_assets' );


if ( ! function_exists( 'dnte_render_text_max_width' ) ) :
	/**
	 * Injects the `has-max-width` class and `--max-width` CSS custom property
	 * into supported blocks on the frontend when the maxWidth attribute is set.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_text_max_width( $block_content, $block ) {
		$supported = array( 'core/heading', 'core/paragraph' );

		if ( ! in_array( $block['blockName'], $supported, true ) ) {
			return $block_content;
		}

		$attrs = $block['attrs'] ?? array();

		if ( empty( $attrs['maxWidth'] ) ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'has-max-width' );

			$existing_style = $processor->get_attribute( 'style' ) ?? '';
			$new_style      = rtrim( $existing_style, '; ' );
			if ( $new_style ) {
				$new_style .= ';';
			}
			$new_style .= '--max-width:' . intval( $attrs['maxWidth'] ) . 'px';
			$processor->set_attribute( 'style', $new_style );

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_text_max_width', 10, 2 );


// =============================================================================
// Heading & Paragraph – Responsive Alignment Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_text_responsive_align_editor_assets' ) ) :
	/**
	 * Enqueues the text-responsive-align extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_text_responsive_align_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/text-responsive-align/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-text-responsive-align-extension',
			get_theme_file_uri( 'build/extensions/text-responsive-align/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/text-responsive-align/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-text-responsive-align-extension',
				get_theme_file_uri( 'build/extensions/text-responsive-align/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_text_responsive_align_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_text_responsive_align_frontend_assets' ) ) :
	/**
	 * Enqueues the text-responsive-align frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_text_responsive_align_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/text-responsive-align/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/text-responsive-align/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		wp_enqueue_style(
			'dnte-text-responsive-align-extension-style',
			get_theme_file_uri( 'build/extensions/text-responsive-align/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_text_responsive_align_frontend_assets' );


if ( ! function_exists( 'dnte_render_text_responsive_align' ) ) :
	/**
	 * Injects the tablet/mobile alignment classes into supported blocks on the frontend.
	 *
	 * Desktop alignment is left to core's own `has-text-align-*` class; only the
	 * breakpoints that were given their own alignment get a class here, so an unset
	 * breakpoint keeps whatever the larger one resolved to.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_text_responsive_align( $block_content, $block ) {
		$supported = array( 'core/heading', 'core/paragraph' );

		if ( ! in_array( $block['blockName'], $supported, true ) ) {
			return $block_content;
		}

		$aligns = $block['attrs']['responsiveTextAlign'] ?? array();

		if ( empty( $aligns ) || empty( $block_content ) ) {
			return $block_content;
		}

		$prefixes  = array(
			'Tablet' => 'has-text-align-tablet-',
			'Mobile' => 'has-text-align-mobile-',
		);
		$allowed   = array( 'left', 'center', 'right' );
		$new_classes = array();

		foreach ( $prefixes as $device => $prefix ) {
			$align = $aligns[ $device ] ?? '';
			if ( in_array( $align, $allowed, true ) ) {
				$new_classes[] = $prefix . $align;
			}
		}

		if ( empty( $new_classes ) ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			foreach ( $new_classes as $class ) {
				$processor->add_class( $class );
			}

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_text_responsive_align', 10, 2 );


// =============================================================================
// Button – Full Width Mobile Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_button_full_width_mobile_editor_assets' ) ) :
	/**
	 * Enqueues the button-full-width-mobile extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_button_full_width_mobile_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/button-full-width-mobile/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-button-full-width-mobile-extension',
			get_theme_file_uri( 'build/extensions/button-full-width-mobile/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/button-full-width-mobile/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-button-full-width-mobile-extension',
				get_theme_file_uri( 'build/extensions/button-full-width-mobile/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_button_full_width_mobile_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_button_full_width_mobile_frontend_assets' ) ) :
	/**
	 * Enqueues the button-full-width-mobile frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_button_full_width_mobile_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/button-full-width-mobile/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/button-full-width-mobile/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-button-full-width-mobile-extension-style',
			get_theme_file_uri( 'build/extensions/button-full-width-mobile/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_button_full_width_mobile_frontend_assets' );


if ( ! function_exists( 'dnte_render_button_full_width_mobile' ) ) :
	/**
	 * Injects the `has-full-width-mobile` class into core/button blocks on the frontend
	 * when the `fullWidthMobile` attribute is enabled.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_button_full_width_mobile( $block_content, $block ) {
		if ( 'core/button' !== $block['blockName'] ) {
			return $block_content;
		}

		if ( empty( $block['attrs']['fullWidthMobile'] ) ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'has-full-width-mobile' );
			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_button_full_width_mobile', 10, 2 );

// =============================================================================
// Button – Iconic Button Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_iconic_button_editor_assets' ) ) :
	/**
	 * Enqueues the iconic-button extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_iconic_button_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/iconic-button/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-iconic-button-extension',
			get_theme_file_uri( 'build/extensions/iconic-button/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/iconic-button/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-iconic-button-extension',
				get_theme_file_uri( 'build/extensions/iconic-button/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_iconic_button_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_iconic_button_frontend_assets' ) ) :
	/**
	 * Enqueues the iconic-button extension frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_iconic_button_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/iconic-button/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/iconic-button/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-iconic-button-extension-style',
			get_theme_file_uri( 'build/extensions/iconic-button/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_iconic_button_frontend_assets' );


if ( ! function_exists( 'dnte_iconic_button_svg_kses_args' ) ) :
	/**
	 * Allowed SVG tags/attributes for icons rendered inside the iconic-button markup.
	 *
	 * @return array Kses args for wp_kses().
	 */
	function dnte_iconic_button_svg_kses_args() {
		// Presentation attributes shared by most SVG shape elements — stroke-based
		// icon sets (Feather, Lucide, Heroicons outline, Tabler, etc.) rely on these
		// alongside fill, so omitting any of them renders the icon invisible.
		$presentation_attrs = array(
			'fill'             => true,
			'fill-rule'        => true,
			'fill-opacity'     => true,
			'clip-rule'        => true,
			'stroke'           => true,
			'stroke-width'     => true,
			'stroke-linecap'   => true,
			'stroke-linejoin'  => true,
			'stroke-dasharray' => true,
			'stroke-dashoffset' => true,
			'stroke-miterlimit' => true,
			'stroke-opacity'   => true,
			'opacity'          => true,
			'transform'        => true,
		);

		// Reference attributes needed for icons exported with <defs>/<clipPath>/<mask>
		// (a common Figma/Illustrator export pattern). Without these, wp_kses strips
		// the wrapping <defs>/<clipPath> tags but keeps their child shapes, which then
		// render as visible elements instead of staying invisible clip/mask definitions.
		$reference_attrs = array(
			'id'        => true,
			'clip-path' => true,
			'mask'      => true,
		);

		return array(
			'svg'       => array_merge(
				array(
					'class'           => true,
					'aria-hidden'     => true,
					'aria-labelledby' => true,
					'role'            => true,
					'xmlns'           => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true,
				),
				$presentation_attrs,
				$reference_attrs
			),
			'defs'      => array( 'id' => true ),
			'clippath'  => array( 'id' => true, 'clippathunits' => true ),
			'mask'      => array_merge(
				array(
					'id'          => true,
					'maskunits'   => true,
					'x'           => true,
					'y'           => true,
					'width'       => true,
					'height'      => true,
				),
				$presentation_attrs
			),
			'path'      => array_merge( array( 'd' => true ), $presentation_attrs, $reference_attrs ),
			'g'         => array_merge( $presentation_attrs, $reference_attrs ),
			'circle'    => array_merge(
				array(
					'cx' => true,
					'cy' => true,
					'r'  => true,
				),
				$presentation_attrs,
				$reference_attrs
			),
			'rect'      => array_merge(
				array(
					'x'      => true,
					'y'      => true,
					'width'  => true,
					'height' => true,
					'rx'     => true,
					'ry'     => true,
				),
				$presentation_attrs,
				$reference_attrs
			),
			'polygon'   => array_merge( array( 'points' => true ), $presentation_attrs, $reference_attrs ),
			'polyline'  => array_merge( array( 'points' => true ), $presentation_attrs, $reference_attrs ),
			'line'      => array_merge(
				array(
					'x1' => true,
					'y1' => true,
					'x2' => true,
					'y2' => true,
				),
				$presentation_attrs,
				$reference_attrs
			),
			'ellipse'   => array_merge(
				array(
					'cx' => true,
					'cy' => true,
					'rx' => true,
					'ry' => true,
				),
				$presentation_attrs,
				$reference_attrs
			),
		);
	}
endif;


if ( ! function_exists( 'dnte_render_iconic_button' ) ) :
	/**
	 * Injects the icon SVG markup and size/gap/padding/background CSS custom
	 * properties into core/button blocks on the frontend when the iconic button
	 * feature is enabled.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_iconic_button( $block_content, $block ) {
		if ( 'core/button' !== ( $block['blockName'] ?? '' ) ) {
			return $block_content;
		}

		$attrs = $block['attrs'] ?? array();

		if ( empty( $attrs['iconicButtonEnabled'] ) || empty( $attrs['iconicButtonUniqueClass'] ) ) {
			return $block_content;
		}

		$icon_svg = ! empty( $attrs['iconicButtonCustomSvg'] ) ? $attrs['iconicButtonCustomSvg'] : ( $attrs['iconicButtonIcon'] ?? '' );

		if ( empty( $icon_svg ) ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		// Merge icon size/gap/padding/background CSS custom properties into the outer wrapper's style attribute.
		$css_vars = array();
		if ( ! empty( $attrs['iconicButtonIconSize'] ) ) {
			$css_vars[] = '--dnte-icon-size:' . esc_attr( $attrs['iconicButtonIconSize'] );
		}
		if ( ! empty( $attrs['iconicButtonIconGap'] ) ) {
			$css_vars[] = '--dnte-icon-gap:' . esc_attr( $attrs['iconicButtonIconGap'] );
		}
		if ( ! empty( $attrs['iconicButtonIconPadding'] ) ) {
			$css_vars[] = '--dnte-icon-padding:' . esc_attr( $attrs['iconicButtonIconPadding'] );
		}
		if ( ! empty( $attrs['iconicButtonIconBgColor'] ) ) {
			$css_vars[] = '--dnte-icon-bg-color:' . esc_attr( $attrs['iconicButtonIconBgColor'] );
		}

		if ( ! empty( $css_vars ) ) {
			$processor = new WP_HTML_Tag_Processor( $block_content );
			if ( $processor->next_tag() ) {
				$existing_style = $processor->get_attribute( 'style' ) ?? '';
				$new_style      = rtrim( $existing_style, '; ' );
				if ( $new_style ) {
					$new_style .= ';';
				}
				$new_style .= implode( ';', $css_vars );
				$processor->set_attribute( 'style', $new_style );
				$block_content = $processor->get_updated_html();
			}
		}

		// Build the icon markup and inject it after the link's inner content.
		// Before/after visual ordering is handled entirely by CSS (flex-direction: row-reverse
		// on the .dnte-icon-before class), so the DOM position is always icon-after-text.
		$icon_html = '<span class="dnte-icon-button-svg">' . wp_kses( $icon_svg, dnte_iconic_button_svg_kses_args() ) . '</span>';

		$block_content = preg_replace_callback(
			'/(<a[^>]*class="[^"]*wp-block-button__link[^"]*"[^>]*>)(.*?)(<\/a>)/s',
			function ( $matches ) use ( $icon_html ) {
				$content = trim( $matches[2] );
				return $matches[1] . $content . $icon_html . $matches[3];
			},
			$block_content
		);

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_iconic_button', 10, 2 );

// =============================================================================
// Kadence RowLayout — Featured Image as Background Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_kadence_featured_bg_editor_assets' ) ) :
	/**
	 * Enqueues the kadence-featured-bg extension script and editor stylesheet.
	 */
	function dnte_enqueue_kadence_featured_bg_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/kadence-featured-bg/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-kadence-featured-bg-extension',
			get_theme_file_uri( 'build/extensions/kadence-featured-bg/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/kadence-featured-bg/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-kadence-featured-bg-extension',
				get_theme_file_uri( 'build/extensions/kadence-featured-bg/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_kadence_featured_bg_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_kadence_featured_bg_frontend_assets' ) ) :
	/**
	 * Enqueues the kadence-featured-bg frontend stylesheet.
	 */
	function dnte_enqueue_kadence_featured_bg_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/kadence-featured-bg/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/kadence-featured-bg/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-kadence-featured-bg-extension-style',
			get_theme_file_uri( 'build/extensions/kadence-featured-bg/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_kadence_featured_bg_frontend_assets' );


if ( ! function_exists( 'dnte_render_kadence_featured_bg' ) ) :
	/**
	 * Injects the featured image as background on kadence/rowlayout blocks
	 * on the frontend when the useFeaturedImageAsBg attribute is enabled.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_kadence_featured_bg( $block_content, $block ) {
		if ( 'kadence/rowlayout' !== $block['blockName'] ) {
			return $block_content;
		}

		$attrs = $block['attrs'] ?? array();

		if ( empty( $attrs['useFeaturedImageAsBg'] ) ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		// Get the current post's featured image.
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $block_content;
		}

		$thumbnail_id = get_post_thumbnail_id( $post_id );
		if ( ! $thumbnail_id ) {
			return $block_content;
		}

		$image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );
		if ( ! $image_url ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'has-featured-image-bg' );

			$existing_style = $processor->get_attribute( 'style' ) ?? '';
			$new_style      = rtrim( $existing_style, '; ' );
			if ( $new_style ) {
				$new_style .= ';';
			}
			$new_style .= '--dnte-featured-bg-image:url(' . esc_url( $image_url ) . ')';
			$processor->set_attribute( 'style', $new_style );

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_kadence_featured_bg', 10, 2 );

// =============================================================================
// Kadence Column — Global Hover Effect Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_kadence_global_hover_editor_assets' ) ) :
	/**
	 * Enqueues the kadence-global-hover extension script and editor stylesheet.
	 */
	function dnte_enqueue_kadence_global_hover_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/kadence-global-hover/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-kadence-global-hover-extension',
			get_theme_file_uri( 'build/extensions/kadence-global-hover/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/kadence-global-hover/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-kadence-global-hover-extension',
				get_theme_file_uri( 'build/extensions/kadence-global-hover/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_kadence_global_hover_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_kadence_global_hover_frontend_assets' ) ) :
	/**
	 * Enqueues the kadence-global-hover frontend stylesheet.
	 */
	function dnte_enqueue_kadence_global_hover_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/kadence-global-hover/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/kadence-global-hover/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_style(
			'dnte-kadence-global-hover-extension-style',
			get_theme_file_uri( 'build/extensions/kadence-global-hover/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_kadence_global_hover_frontend_assets' );


if ( ! function_exists( 'dnte_render_kadence_global_hover' ) ) :
	/**
	 * Injects the `global-hover` class into kadence/column blocks on the frontend
	 * when the globalHoverEffect attribute is enabled.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_kadence_global_hover( $block_content, $block ) {
		if ( 'kadence/column' !== $block['blockName'] ) {
			return $block_content;
		}

		if ( empty( $block['attrs']['globalHoverEffect'] ) ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'global-hover' );
			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_kadence_global_hover', 10, 2 );


// =============================================================================
// Image – Scaled Image Extension
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_image_scale_editor_assets' ) ) :
	/**
	 * Enqueues the image-scale extension script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_image_scale_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/image-scale/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-image-scale-extension',
			get_theme_file_uri( 'build/extensions/image-scale/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/image-scale/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-image-scale-extension',
				get_theme_file_uri( 'build/extensions/image-scale/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_image_scale_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_image_scale_frontend_assets' ) ) :
	/**
	 * Enqueues the image-scale frontend stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_image_scale_frontend_assets() {
		$style_file = get_theme_file_path( 'build/extensions/image-scale/style-index.css' );

		if ( ! file_exists( $style_file ) ) {
			return;
		}

		wp_enqueue_style(
			'dnte-image-scale-extension-style',
			get_theme_file_uri( 'build/extensions/image-scale/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_image_scale_frontend_assets' );


if ( ! function_exists( 'dnte_render_image_scale' ) ) :
	/**
	 * Injects the `has-scaled-image` class and `--dnte-image-scale` custom
	 * property into core/image blocks on the frontend when the `isScaled`
	 * attribute is enabled.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_image_scale( $block_content, $block ) {
		if ( 'core/image' !== $block['blockName'] ) {
			return $block_content;
		}

		if ( empty( $block['attrs']['isScaled'] ) ) {
			return $block_content;
		}

		if ( empty( $block_content ) ) {
			return $block_content;
		}

		$scale = isset( $block['attrs']['imageScale'] ) ? (float) $block['attrs']['imageScale'] : 1.1;

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$processor->add_class( 'has-scaled-image' );

			$existing_style = $processor->get_attribute( 'style' ) ?? '';
			$new_style      = rtrim( $existing_style, '; ' );
			if ( $new_style ) {
				$new_style .= ';';
			}
			$new_style .= '--dnte-image-scale:' . $scale;
			$processor->set_attribute( 'style', $new_style );

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_image_scale', 10, 2 );


// =============================================================================
// Heading & Paragraph – Highlight Format
// =============================================================================

if ( ! function_exists( 'dnte_enqueue_highlight_editor_assets' ) ) :
	/**
	 * Enqueues the highlight format script and editor stylesheet.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_highlight_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/highlight/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-highlight-extension',
			get_theme_file_uri( 'build/extensions/highlight/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		$editor_css = get_theme_file_path( 'build/extensions/highlight/index.css' );
		if ( file_exists( $editor_css ) ) {
			wp_enqueue_style(
				'dnte-highlight-extension',
				get_theme_file_uri( 'build/extensions/highlight/index.css' ),
				array(),
				wp_get_theme()->get( 'Version' )
			);
		}
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_highlight_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_highlight_frontend_assets' ) ) :
	/**
	 * Enqueues the highlight format stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_highlight_frontend_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/highlight/index.asset.php' );
		$style_file = get_theme_file_path( 'build/extensions/highlight/style-index.css' );

		if ( ! file_exists( $asset_file ) || ! file_exists( $style_file ) ) {
			return;
		}

		wp_enqueue_style(
			'dnte-highlight-extension-style',
			get_theme_file_uri( 'build/extensions/highlight/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_highlight_frontend_assets' );


if ( ! function_exists( 'dnte_render_highlight_attributes' ) ) :
	/**
	 * Injects the highlight CSS custom properties (`--dnte-highlight-*`) into
	 * core/heading and core/paragraph blocks on the frontend when the
	 * `highlightFontFamily` / `highlightColor` attributes are set.
	 *
	 * A solid highlight colour also sets `--dnte-highlight-gradient: none` so
	 * it replaces the default gradient fill.
	 *
	 * The editor applies the same variables via the JS `editor.BlockListBlock`
	 * filter, which has no effect on saved frontend markup, so this PHP
	 * `render_block` filter replicates it for the rendered HTML.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_highlight_attributes( $block_content, $block ) {
		$supported = array( 'core/heading', 'core/paragraph' );

		if ( ! in_array( $block['blockName'], $supported, true ) ) {
			return $block_content;
		}

		$attrs       = $block['attrs'] ?? array();
		$font_family = $attrs['highlightFontFamily'] ?? '';
		$color       = $attrs['highlightColor'] ?? '';

		if ( ( empty( $font_family ) && empty( $color ) ) || empty( $block_content ) ) {
			return $block_content;
		}

		$css_vars = array();
		// Values are passed raw: WP_HTML_Tag_Processor::set_attribute() escapes
		// attribute values itself, so pre-escaping would double-encode quotes
		// (e.g. font stacks) into invalid CSS.
		if ( ! empty( $font_family ) ) {
			$css_vars[] = '--dnte-highlight-font-family:' . $font_family;
		}
		if ( ! empty( $color ) ) {
			$css_vars[] = '--dnte-highlight-color:' . $color;
			$css_vars[] = '--dnte-highlight-gradient:none';
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );
		if ( $processor->next_tag() ) {
			$existing_style = $processor->get_attribute( 'style' ) ?? '';
			$new_style      = rtrim( $existing_style, '; ' );
			if ( $new_style ) {
				$new_style .= ';';
			}
			$new_style .= implode( ';', $css_vars );
			$processor->set_attribute( 'style', $new_style );

			return $processor->get_updated_html();
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_highlight_attributes', 10, 2 );


// =============================================================================
// Kadence RowLayout — Torn Paper Dividers Extension
// =============================================================================

if ( ! function_exists( 'dnte_kadence_row_divider_image_uri' ) ) :
	/**
	 * URL of the torn paper divider artwork.
	 *
	 * The source asset was an SVG, but it carried no vector path — it was a
	 * base64 PNG inside an SVG wrapper (a Figma export), so the PNG is stored
	 * directly: same pixels, ~25% smaller, and cacheable as a normal image.
	 *
	 * @return string Image URL, or '' when the file is missing.
	 */
	function dnte_kadence_row_divider_image_uri() {
		$path = get_theme_file_path( 'assets/images/dividers/paper-tear.png' );

		if ( ! file_exists( $path ) ) {
			return '';
		}

		return get_theme_file_uri( 'assets/images/dividers/paper-tear.png' );
	}
endif;


if ( ! function_exists( 'dnte_enqueue_kadence_row_divider_editor_assets' ) ) :
	/**
	 * Enqueues the kadence-row-divider extension script.
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_kadence_row_divider_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/kadence-row-divider/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_enqueue_script(
			'dnte-kadence-row-divider-extension',
			get_theme_file_uri( 'build/extensions/kadence-row-divider/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		// The editor preview needs the artwork URL; PHP owns the asset path so
		// the bundle never has to resolve it.
		wp_add_inline_script(
			'dnte-kadence-row-divider-extension',
			'window.dnteRowDivider = ' . wp_json_encode( array( 'image' => dnte_kadence_row_divider_image_uri() ) ) . ';',
			'before'
		);
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_kadence_row_divider_editor_assets' );


if ( ! function_exists( 'dnte_enqueue_kadence_row_divider_frontend_assets' ) ) :
	/**
	 * Enqueues the kadence-row-divider stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_kadence_row_divider_frontend_assets() {
		$style_file = get_theme_file_path( 'build/extensions/kadence-row-divider/style-index.css' );

		if ( ! file_exists( $style_file ) ) {
			return;
		}

		wp_enqueue_style(
			'dnte-kadence-row-divider-extension-style',
			get_theme_file_uri( 'build/extensions/kadence-row-divider/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_kadence_row_divider_frontend_assets' );


if ( ! function_exists( 'dnte_render_kadence_row_divider' ) ) :
	/**
	 * Adds the divider classes and artwork URL to kadence/rowlayout blocks on
	 * the front end when either divider toggle is enabled.
	 *
	 * The editor applies the same classes through the JS
	 * `editor.BlockListBlock` filter, which has no effect on rendered markup,
	 * so this replicates it for the front end.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including name and attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_kadence_row_divider( $block_content, $block ) {
		if ( 'kadence/rowlayout' !== ( $block['blockName'] ?? '' ) || empty( $block_content ) ) {
			return $block_content;
		}

		$attrs      = $block['attrs'] ?? array();
		$has_top    = ! empty( $attrs['enableTopDivider'] );
		$has_bottom = ! empty( $attrs['enableBottomDivider'] );

		if ( ! $has_top && ! $has_bottom ) {
			return $block_content;
		}

		$image_url = dnte_kadence_row_divider_image_uri();

		if ( '' === $image_url ) {
			return $block_content;
		}

		$processor = new WP_HTML_Tag_Processor( $block_content );

		if ( ! $processor->next_tag() ) {
			return $block_content;
		}

		if ( $has_top ) {
			$processor->add_class( 'dnte-has-top-divider' );
		}

		if ( $has_bottom ) {
			$processor->add_class( 'dnte-has-bottom-divider' );
		}

		// Values are passed raw: WP_HTML_Tag_Processor::set_attribute() escapes
		// attribute values itself, so pre-escaping would double-encode.
		$existing_style = $processor->get_attribute( 'style' ) ?? '';
		$new_style      = rtrim( $existing_style, '; ' );

		if ( $new_style ) {
			$new_style .= ';';
		}

		$new_style .= '--dnte-divider-image:url(' . esc_url_raw( $image_url ) . ')';
		$processor->set_attribute( 'style', $new_style );

		return $processor->get_updated_html();
	}
endif;
add_filter( 'render_block', 'dnte_render_kadence_row_divider', 10, 2 );
