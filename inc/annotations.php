<?php
/**
 * Heading Annotations
 *
 * Hand-drawn marker strokes (underlines, circles) drawn over a *selection*
 * inside a heading — not over the whole block. Authors apply them from the
 * block toolbar like bold or italic.
 *
 * The editor side is a RichText format (`dnte/annotation`, registered in
 * src/extensions/annotation/index.js) which saves markup like:
 *
 *     <span class="dnte-annotation" data-annotation="underline-1"
 *           style="--dnte-annotation-color:…">Zero commissions.</span>
 *
 * The Format API cannot emit child nodes into a RichText value — anything it
 * injected would become editable content and get saved into the post — so the
 * actual <svg> is appended here, at render time, by dnte_render_annotation().
 *
 * This lives in its own file rather than in inc/extensions.php because it
 * carries a registry and an HTML scanner; inc/my-icons.php sets the precedent
 * for a standalone subsystem file.
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Directory holding the stroke artwork, relative to the theme root.
 */
const DNTE_ANNOTATION_DIR = 'assets/svg/annotations';

/**
 * Class marking an annotated selection in saved post content.
 */
const DNTE_ANNOTATION_CLASS = 'dnte-annotation';


if ( ! function_exists( 'dnte_annotation_variants' ) ) :
	/**
	 * The available stroke variants, keyed by slug.
	 *
	 * Derived from the filenames in assets/svg/annotations/, so adding a new
	 * stroke is a drop-in .svg file with no code change. The slug prefix
	 * (`underline-`, `circle-`) selects the geometry in style.scss, so name
	 * files accordingly.
	 *
	 * Artwork must be normalised before it is dropped in:
	 *
	 * - `stroke="currentColor"` and `fill="none"` on every path, so the colour
	 *   picker drives it
	 * - `preserveAspectRatio="none"` and no width/height on the root, so the
	 *   stroke stretches to the phrase
	 * - NO `pathLength` and NO `vector-effect="non-scaling-stroke"` on the
	 *   paths — both interfere with the stroke dashing that drives the draw-in
	 *   animation (see the draw-in comment in style.scss)
	 *
	 * @return array<string, array{label: string, path: string}> Variants by slug.
	 */
	function dnte_annotation_variants() {
		static $variants = null;

		if ( null !== $variants ) {
			return $variants;
		}

		$variants = array();
		$files    = glob( get_theme_file_path( DNTE_ANNOTATION_DIR ) . '/*.svg' );

		if ( empty( $files ) ) {
			return $variants;
		}

		sort( $files );

		foreach ( $files as $file ) {
			$slug = sanitize_key( basename( $file, '.svg' ) );

			// A filename that does not survive sanitize_key() could not be
			// matched back from post content anyway, so skip it outright.
			if ( '' === $slug || $slug !== basename( $file, '.svg' ) ) {
				continue;
			}

			$variants[ $slug ] = array(
				'label' => ucwords( str_replace( array( '-', '_' ), ' ', $slug ) ),
				'path'  => $file,
			);
		}

		return $variants;
	}
endif;


if ( ! function_exists( 'dnte_annotation_markup' ) ) :
	/**
	 * The inline <svg> for a variant, ready to splice into block markup.
	 *
	 * The slug arrives from post content, so it is sanitised and then looked up
	 * in the registry — it is never concatenated into a path. The files
	 * themselves are theme-owned and trusted, so they need no wp_kses() pass.
	 * (dnte_iconic_button_svg_kses_args() in inc/extensions.php is the helper to
	 * reach for if a variant ever accepts user-supplied SVG.)
	 *
	 * @param string $slug Variant slug from the `data-annotation` attribute.
	 * @return string SVG markup, or '' for an unknown slug or unreadable file.
	 */
	function dnte_annotation_markup( $slug ) {
		static $cache = array();

		$slug = sanitize_key( (string) $slug );

		if ( isset( $cache[ $slug ] ) ) {
			return $cache[ $slug ];
		}

		$variants = dnte_annotation_variants();

		if ( ! isset( $variants[ $slug ] ) ) {
			$cache[ $slug ] = '';
			return '';
		}

		$svg = file_get_contents( $variants[ $slug ]['path'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file, not a remote request.

		if ( false === $svg ) {
			$cache[ $slug ] = '';
			return '';
		}

		// Strip any XML prolog / doctype so the markup can be spliced inline.
		$svg = preg_replace( '/<\?xml.*?\?>|<!DOCTYPE.*?>/is', '', $svg );
		$svg = trim( $svg );

		// Tag the root element so the stylesheet can target it, and hide it from
		// assistive tech — the stroke is decorative, the text carries meaning.
		$processor = new WP_HTML_Tag_Processor( $svg );
		if ( $processor->next_tag( array( 'tag_name' => 'SVG' ) ) ) {
			$processor->set_attribute( 'class', 'dnte-annotation__stroke' );
			$processor->set_attribute( 'aria-hidden', 'true' );
			$processor->set_attribute( 'focusable', 'false' );
			$svg = $processor->get_updated_html();
		}

		$cache[ $slug ] = $svg;

		return $svg;
	}
endif;


if ( ! function_exists( 'dnte_annotation_close_tag_offset' ) ) :
	/**
	 * Byte offset of the `</span>` that closes the span opening at $open_end.
	 *
	 * A lazy regex (`<span[^>]*dnte-annotation[^>]*>(.*?)</span>`) would stop at
	 * the first close tag, which is wrong the moment an author bolds or links
	 * part of an annotated phrase and nests a span inside it. So walk forward
	 * counting depth instead.
	 *
	 * @param string $html     Block markup.
	 * @param int    $open_end Offset just past the opening span's `>`.
	 * @return int|false Offset of the matching `</span>`, or false if unbalanced.
	 */
	function dnte_annotation_close_tag_offset( $html, $open_end ) {
		$depth  = 1;
		$cursor = $open_end;

		while ( $depth > 0 ) {
			$next_open  = stripos( $html, '<span', $cursor );
			$next_close = stripos( $html, '</span', $cursor );

			if ( false === $next_close ) {
				return false;
			}

			if ( false !== $next_open && $next_open < $next_close ) {
				++$depth;
				$cursor = $next_open + 5;
				continue;
			}

			--$depth;
			$cursor = $next_close + 6;

			if ( 0 === $depth ) {
				return $next_close;
			}
		}

		return false;
	}
endif;


if ( ! function_exists( 'dnte_render_annotation' ) ) :
	/**
	 * Appends the stroke <svg> inside every annotated span in a heading.
	 *
	 * The SVG goes in as the span's last child, absolutely positioned against
	 * it (see style.scss) — no extra wrapper element is needed.
	 *
	 * WP_HTML_Tag_Processor locates the opening tags and reads their attributes,
	 * but it has no HTML-insertion API in stable WP, so the splice itself is
	 * manual. Insertions run back to front so earlier offsets stay valid.
	 *
	 * @param string $block_content The rendered block HTML.
	 * @param array  $block         The block data including attributes.
	 * @return string Modified block HTML.
	 */
	function dnte_render_annotation( $block_content, $block ) {
		// Blocks whose text can carry an annotation. The scanner below is
		// block-agnostic — it only looks for the annotation span — so adding a
		// block here is all it takes. Mirrors SUPPORTED_BLOCKS in
		// src/extensions/annotation/index.js, which gates the toolbar button.
		$supported = array( 'core/heading', 'dnte/story-card' );

		if ( empty( $block_content ) || ! in_array( $block['blockName'] ?? '', $supported, true ) ) {
			return $block_content;
		}

		if ( false === strpos( $block_content, DNTE_ANNOTATION_CLASS ) ) {
			return $block_content;
		}

		$insertions = array();
		$offset     = 0;

		// The Tag Processor does not expose byte offsets, and we need them in
		// order to splice, so the scan is manual — but each opening tag it
		// finds is handed back to the Tag Processor to read its attributes,
		// which gets class-list and attribute-quoting edge cases right.
		while ( false !== ( $open_start = stripos( $block_content, '<span', $offset ) ) ) {
			$tag_end = strpos( $block_content, '>', $open_start );

			if ( false === $tag_end ) {
				break;
			}

			$open_end = $tag_end + 1;
			$tag      = substr( $block_content, $open_start, $open_end - $open_start );
			$slug     = '';

			$processor = new WP_HTML_Tag_Processor( $tag );
			if ( $processor->next_tag() && $processor->has_class( DNTE_ANNOTATION_CLASS ) ) {
				$slug = (string) $processor->get_attribute( 'data-annotation' );
			}

			if ( '' === $slug ) {
				$offset = $open_end;
				continue;
			}

			$close_at = dnte_annotation_close_tag_offset( $block_content, $open_end );

			if ( false === $close_at ) {
				break;
			}

			$svg = dnte_annotation_markup( $slug );

			if ( '' !== $svg ) {
				$insertions[] = array( $close_at, $svg );
			}

			// Skip past this annotation entirely — they do not nest.
			$offset = $close_at;
		}

		if ( empty( $insertions ) ) {
			return $block_content;
		}

		foreach ( array_reverse( $insertions ) as $insertion ) {
			list( $at, $svg ) = $insertion;
			$block_content    = substr_replace( $block_content, $svg, $at, 0 );
		}

		// Only pages that actually use an annotation pay for the scroll script.
		if ( ! is_admin() && wp_script_is( 'dnte-annotation-view', 'registered' ) ) {
			wp_enqueue_script( 'dnte-annotation-view' );
		}

		return $block_content;
	}
endif;
add_filter( 'render_block', 'dnte_render_annotation', 10, 2 );


if ( ! function_exists( 'dnte_register_annotation_view_script' ) ) :
	/**
	 * Registers (but does not enqueue) the scroll-draw script, so
	 * dnte_render_annotation() can pull it in only when a stroke is rendered.
	 */
	function dnte_register_annotation_view_script() {
		$asset_file = get_theme_file_path( 'build/extensions/annotation/view.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets = require $asset_file;

		wp_register_script(
			'dnte-annotation-view',
			get_theme_file_uri( 'build/extensions/annotation/view.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);
	}
endif;
add_action( 'init', 'dnte_register_annotation_view_script' );


if ( ! function_exists( 'dnte_enqueue_annotation_preview_styles' ) ) :
	/**
	 * Enqueues the editor preview stylesheet and its generated mask CSS under
	 * the given handle.
	 *
	 * This has to land in two separate documents, which is why the handle is a
	 * parameter: the admin page, for the stroke swatches in the toolbar
	 * popover, and the editor canvas — which is an iframe, and which
	 * `enqueue_block_editor_assets` does not reach. Without the canvas copy the
	 * `::after` preview stays at its `content: none` default and no stroke
	 * shows on the blocks themselves.
	 *
	 * @param string $handle Style handle to register under.
	 */
	function dnte_enqueue_annotation_preview_styles( $handle ) {
		$editor_css = get_theme_file_path( 'build/extensions/annotation/index.css' );

		if ( ! file_exists( $editor_css ) ) {
			return;
		}

		wp_enqueue_style(
			$handle,
			get_theme_file_uri( 'build/extensions/annotation/index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);

		$preview_css = dnte_annotation_editor_preview_css();

		if ( '' !== $preview_css ) {
			wp_add_inline_style( $handle, $preview_css );
		}
	}
endif;


if ( ! function_exists( 'dnte_enqueue_annotation_frontend_assets' ) ) :
	/**
	 * Enqueues the annotation stylesheet.
	 * Runs on `enqueue_block_assets` (editor + front end).
	 */
	function dnte_enqueue_annotation_frontend_assets() {
		$style_file = get_theme_file_path( 'build/extensions/annotation/style-index.css' );

		if ( ! file_exists( $style_file ) ) {
			return;
		}

		wp_enqueue_style(
			'dnte-annotation-extension-style',
			get_theme_file_uri( 'build/extensions/annotation/style-index.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);

		// This hook is the one that reaches inside the editor canvas iframe, so
		// it is where the canvas gets its stroke previews.
		if ( is_admin() ) {
			dnte_enqueue_annotation_preview_styles( 'dnte-annotation-extension-canvas' );
		}
	}
endif;
add_action( 'enqueue_block_assets', 'dnte_enqueue_annotation_frontend_assets' );


if ( ! function_exists( 'dnte_enqueue_annotation_editor_assets' ) ) :
	/**
	 * Enqueues the annotation format script and editor stylesheet, plus the
	 * two things the editor needs that only PHP can supply.
	 *
	 * The editor cannot render the injected <svg> (see the file header), so it
	 * previews each stroke with `mask-image` + `currentColor` instead. Both the
	 * variant list and that CSS are generated from the same .svg files, keeping
	 * the artwork the single source of truth — the editor hardcodes nothing.
	 *
	 * Runs on `enqueue_block_editor_assets` (editor only).
	 */
	function dnte_enqueue_annotation_editor_assets() {
		$asset_file = get_theme_file_path( 'build/extensions/annotation/index.asset.php' );

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$assets   = require $asset_file;
		$variants = dnte_annotation_variants();

		wp_enqueue_script(
			'dnte-annotation-extension',
			get_theme_file_uri( 'build/extensions/annotation/index.js' ),
			$assets['dependencies'],
			wp_get_theme()->get( 'Version' ),
			true
		);

		// Admin-document copy, for the swatches in the toolbar popover.
		dnte_enqueue_annotation_preview_styles( 'dnte-annotation-extension' );

		$list = array();
		foreach ( $variants as $slug => $variant ) {
			$list[] = array(
				'slug'  => $slug,
				'label' => $variant['label'],
			);
		}

		wp_add_inline_script(
			'dnte-annotation-extension',
			'window.dnteAnnotations = ' . wp_json_encode( $list ) . ';',
			'before'
		);
	}
endif;
add_action( 'enqueue_block_editor_assets', 'dnte_enqueue_annotation_editor_assets' );


if ( ! function_exists( 'dnte_annotation_editor_preview_css' ) ) :
	/**
	 * CSS painting each stroke as a mask, for the editor canvas and the
	 * variant swatches in the toolbar popover.
	 *
	 * A mask filled with `currentColor` reproduces the artwork exactly, minus
	 * the draw-in animation — which the editor does not need.
	 *
	 * @return string Inline CSS, or '' when there is no artwork.
	 */
	function dnte_annotation_editor_preview_css() {
		$rules = array();

		foreach ( dnte_annotation_variants() as $slug => $variant ) {
			$svg = file_get_contents( $variant['path'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file, not a remote request.

			if ( false === $svg ) {
				continue;
			}

			// A mask is a shape, not a colour, so bake the stroke to a flat
			// black before encoding — the colour comes from the mask's
			// background-color at paint time.
			$svg = str_replace( 'currentColor', '#000', $svg );
			$uri = 'data:image/svg+xml;utf8,' . rawurlencode( trim( $svg ) );

			$rules[] = sprintf(
				'.dnte-annotation[data-annotation="%1$s"]::after{--dnte-annotation-mask:url("%2$s");}',
				esc_attr( $slug ),
				$uri
			);
			$rules[] = sprintf(
				'.dnte-annotation-swatch[data-annotation="%1$s"]::after{--dnte-annotation-mask:url("%2$s");}',
				esc_attr( $slug ),
				$uri
			);
		}

		return implode( "\n", $rules );
	}
endif;
