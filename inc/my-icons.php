<?php
/**
 * My Icons Library
 *
 * Stores custom SVG icons in a site-wide option so they can be reused from the
 * icon picker in every block that uses it. Icons are added from the "Custom SVG"
 * tab of the icon library modal and listed under its "My Icons" tab.
 *
 * All SVG markup is sanitised against an allow-list before it is stored, so a
 * saved icon can never carry scripts or event handlers into the editor.
 *
 * @package Dentist_Exchange
 */

if ( ! defined( 'DNTE_MY_ICONS_OPTION' ) ) {
	define( 'DNTE_MY_ICONS_OPTION', 'dnte_my_icons' );
}

if ( ! defined( 'DNTE_MY_ICONS_MAX' ) ) {
	define( 'DNTE_MY_ICONS_MAX', 500 );
}

if ( ! function_exists( 'dnte_my_icons_permissions_check' ) ) :
	/**
	 * Permission callback for every My Icons route.
	 *
	 * The library is only ever used from inside the block editor, so the same
	 * capability gates reading, adding and deleting.
	 *
	 * @return bool
	 */
	function dnte_my_icons_permissions_check() {
		return current_user_can( 'edit_posts' );
	}
endif;

if ( ! function_exists( 'dnte_get_my_icons' ) ) :
	/**
	 * Returns the stored icon library as a clean, sequential array.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function dnte_get_my_icons() {
		$icons = get_option( DNTE_MY_ICONS_OPTION, array() );

		if ( ! is_array( $icons ) ) {
			return array();
		}

		return array_values(
			array_filter(
				$icons,
				static function ( $icon ) {
					return is_array( $icon ) && ! empty( $icon['id'] ) && ! empty( $icon['svg'] );
				}
			)
		);
	}
endif;

/* -------------------------------------------------------------------------
 * SVG sanitisation
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'dnte_svg_allowed_elements' ) ) :
	/**
	 * Lower-cased list of SVG elements that may be stored.
	 *
	 * @return array<int, string>
	 */
	function dnte_svg_allowed_elements() {
		return array(
			'svg',
			'g',
			'defs',
			'symbol',
			'use',
			'switch',
			'title',
			'desc',
			'metadata',
			'style',
			'path',
			'circle',
			'ellipse',
			'line',
			'polyline',
			'polygon',
			'rect',
			'text',
			'tspan',
			'textpath',
			'marker',
			'lineargradient',
			'radialgradient',
			'stop',
			'clippath',
			'mask',
			'pattern',
			'filter',
			'feblend',
			'fecolormatrix',
			'fecomposite',
			'fedropshadow',
			'feflood',
			'fegaussianblur',
			'femerge',
			'femergenode',
			'feoffset',
		);
	}
endif;

if ( ! function_exists( 'dnte_svg_allowed_attributes' ) ) :
	/**
	 * Lower-cased list of attributes that may be stored.
	 *
	 * @return array<int, string>
	 */
	function dnte_svg_allowed_attributes() {
		return array(
			// Structure.
			'id',
			'class',
			'style',
			'xmlns',
			'xmlns:xlink',
			'version',
			'viewbox',
			'preserveaspectratio',
			'transform',
			'overflow',
			'role',
			'focusable',
			'aria-hidden',
			'aria-label',
			'xml:space',
			'data-name',
			// Geometry.
			'x',
			'y',
			'x1',
			'y1',
			'x2',
			'y2',
			'cx',
			'cy',
			'r',
			'rx',
			'ry',
			'dx',
			'dy',
			'd',
			'points',
			'width',
			'height',
			'offset',
			'pathlength',
			// Paint.
			'fill',
			'fill-opacity',
			'fill-rule',
			'stroke',
			'stroke-width',
			'stroke-linecap',
			'stroke-linejoin',
			'stroke-dasharray',
			'stroke-dashoffset',
			'stroke-opacity',
			'stroke-miterlimit',
			'opacity',
			'color',
			'stop-color',
			'stop-opacity',
			'paint-order',
			'vector-effect',
			'shape-rendering',
			'color-interpolation-filters',
			// References (validated separately).
			'clip-path',
			'clip-rule',
			'mask',
			'filter',
			'marker-start',
			'marker-mid',
			'marker-end',
			// Gradients, patterns, markers, filters.
			'gradientunits',
			'gradienttransform',
			'spreadmethod',
			'fr',
			'fx',
			'fy',
			'patternunits',
			'patterncontentunits',
			'patterntransform',
			'maskunits',
			'maskcontentunits',
			'clippathunits',
			'markerwidth',
			'markerheight',
			'markerunits',
			'refx',
			'refy',
			'orient',
			'filterunits',
			'primitiveunits',
			'stddeviation',
			'in',
			'in2',
			'result',
			'mode',
			'operator',
			'values',
			'type',
			'flood-color',
			'flood-opacity',
			// Text.
			'font-family',
			'font-size',
			'font-style',
			'font-weight',
			'letter-spacing',
			'word-spacing',
			'text-anchor',
			'text-decoration',
			'dominant-baseline',
			'alignment-baseline',
			'baseline-shift',
			'writing-mode',
		);
	}
endif;

if ( ! function_exists( 'dnte_sanitize_svg_css' ) ) :
	/**
	 * Strips anything that can reach the network or a script from CSS found in a
	 * `style` attribute or an inline `<style>` element.
	 *
	 * @param string $css Raw CSS.
	 * @return string Sanitised CSS.
	 */
	function dnte_sanitize_svg_css( $css ) {
		$css = (string) $css;

		// Remove at-rules that can load remote resources.
		$css = preg_replace( '/@(import|charset|namespace)[^;]*;?/i', '', $css );

		// Remove url() references unless they point at a fragment inside the SVG.
		$css = preg_replace_callback(
			'/url\(\s*([\'"]?)([^\'")]*)\1\s*\)/i',
			static function ( $matches ) {
				return 0 === strpos( trim( $matches[2] ), '#' ) ? $matches[0] : 'none';
			},
			$css
		);

		// Legacy IE script vectors and any leftover protocol handlers.
		$css = preg_replace( '/(expression|javascript\s*:|vbscript\s*:|behavior\s*:|-moz-binding)/i', '', $css );

		return trim( (string) $css );
	}
endif;

if ( ! function_exists( 'dnte_sanitize_svg_node' ) ) :
	/**
	 * Recursively removes disallowed elements and attributes from a DOM node.
	 *
	 * @param DOMNode $node Node to clean.
	 * @return void
	 */
	function dnte_sanitize_svg_node( DOMNode $node ) {
		$allowed_elements   = dnte_svg_allowed_elements();
		$allowed_attributes = dnte_svg_allowed_attributes();

		// Walk backwards so removals do not shift the live NodeList.
		for ( $i = $node->childNodes->length - 1; $i >= 0; $i-- ) {
			$child = $node->childNodes->item( $i );

			if ( ! $child instanceof DOMNode ) {
				continue;
			}

			// Drop comments, processing instructions and CDATA wrappers.
			if ( XML_COMMENT_NODE === $child->nodeType || XML_PI_NODE === $child->nodeType ) {
				$node->removeChild( $child );
				continue;
			}

			if ( XML_ELEMENT_NODE !== $child->nodeType ) {
				continue;
			}

			$tag = strtolower( $child->localName );

			if ( ! in_array( $tag, $allowed_elements, true ) ) {
				$node->removeChild( $child );
				continue;
			}

			if ( 'style' === $tag ) {
				$child->nodeValue = dnte_sanitize_svg_css( $child->textContent ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
				continue;
			}

			if ( $child->hasAttributes() ) {
				for ( $a = $child->attributes->length - 1; $a >= 0; $a-- ) {
					$attribute = $child->attributes->item( $a );
					$name      = strtolower( $attribute->nodeName );
					$value     = trim( $attribute->nodeValue ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					// Event handlers are never allowed.
					if ( 0 === strpos( $name, 'on' ) ) {
						$child->removeAttribute( $attribute->nodeName );
						continue;
					}

					// Links may only target a fragment inside the same document.
					if ( 'href' === $name || 'xlink:href' === $name ) {
						if ( 0 !== strpos( $value, '#' ) ) {
							$child->removeAttribute( $attribute->nodeName );
						}
						continue;
					}

					if ( 'style' === $name ) {
						$child->setAttribute( $attribute->nodeName, dnte_sanitize_svg_css( $value ) );
						continue;
					}

					if ( ! in_array( $name, $allowed_attributes, true ) ) {
						$child->removeAttribute( $attribute->nodeName );
						continue;
					}

					// Catch protocol handlers smuggled into an allowed attribute.
					if ( preg_match( '/(javascript|vbscript|data)\s*:/i', $value ) ) {
						$child->removeAttribute( $attribute->nodeName );
					}
				}
			}

			if ( $child->hasChildNodes() ) {
				dnte_sanitize_svg_node( $child );
			}
		}
	}
endif;

if ( ! function_exists( 'dnte_svg_restore_camel_case' ) ) :
	/**
	 * Restores camel-cased SVG element and attribute names.
	 *
	 * The HTML parser fallback lower-cases every name. Browsers correct that when
	 * the markup is re-parsed as HTML, but not when the icon is used as an XML
	 * data URI (for example the CSS mask on iconic buttons), so it is fixed here.
	 *
	 * @param string $markup Serialised SVG markup.
	 * @return string
	 */
	function dnte_svg_restore_camel_case( $markup ) {
		$elements = array(
			'lineargradient' => 'linearGradient',
			'radialgradient' => 'radialGradient',
			'clippath'       => 'clipPath',
			'textpath'       => 'textPath',
			'feblend'        => 'feBlend',
			'fecolormatrix'  => 'feColorMatrix',
			'fecomposite'    => 'feComposite',
			'fedropshadow'   => 'feDropShadow',
			'feflood'        => 'feFlood',
			'fegaussianblur' => 'feGaussianBlur',
			'femerge'        => 'feMerge',
			'femergenode'    => 'feMergeNode',
			'feoffset'       => 'feOffset',
		);

		foreach ( $elements as $lower => $proper ) {
			$markup = preg_replace( '#<(/?)' . $lower . '(?=[\s/>])#i', '<$1' . $proper, $markup );
		}

		$attributes = array(
			'viewbox'             => 'viewBox',
			'preserveaspectratio' => 'preserveAspectRatio',
			'gradientunits'       => 'gradientUnits',
			'gradienttransform'   => 'gradientTransform',
			'spreadmethod'        => 'spreadMethod',
			'patternunits'        => 'patternUnits',
			'patterncontentunits' => 'patternContentUnits',
			'patterntransform'    => 'patternTransform',
			'maskunits'           => 'maskUnits',
			'maskcontentunits'    => 'maskContentUnits',
			'clippathunits'       => 'clipPathUnits',
			'markerwidth'         => 'markerWidth',
			'markerheight'        => 'markerHeight',
			'markerunits'         => 'markerUnits',
			'refx'                => 'refX',
			'refy'                => 'refY',
			'filterunits'         => 'filterUnits',
			'primitiveunits'      => 'primitiveUnits',
			'stddeviation'        => 'stdDeviation',
			'pathlength'          => 'pathLength',
		);

		foreach ( $attributes as $lower => $proper ) {
			$markup = preg_replace( '/(\s)' . $lower . '(\s*=\s*")/i', '$1' . $proper . '$2', $markup );
		}

		return $markup;
	}
endif;

if ( ! function_exists( 'dnte_sanitize_svg_markup' ) ) :
	/**
	 * Sanitises pasted SVG markup down to a safe, storable `<svg>` element.
	 *
	 * @param string $svg Raw SVG markup.
	 * @return string Sanitised markup, or an empty string when the input is not usable SVG.
	 */
	function dnte_sanitize_svg_markup( $svg ) {
		$svg = trim( (string) $svg );

		if ( '' === $svg || ! class_exists( 'DOMDocument' ) ) {
			return '';
		}

		// Remove doctypes and entity declarations before parsing.
		$svg = preg_replace( '/<!DOCTYPE[^>]*>/i', '', $svg );
		$svg = preg_replace( '/<\?xml[^>]*\?>/i', '', $svg );
		$svg = preg_replace( '#<script\b.*?</script>#is', '', $svg );

		if ( false === stripos( $svg, '<svg' ) ) {
			return '';
		}

		$previous = libxml_use_internal_errors( true );
		$document = new DOMDocument();
		$document->preserveWhiteSpace = false; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		$loaded      = $document->loadXML( $svg, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );
		$used_parser = 'xml';

		if ( ! $loaded ) {
			// Fall back to the more forgiving HTML parser for hand-edited markup.
			$document    = new DOMDocument();
			$used_parser = 'html';
			$loaded      = $document->loadHTML(
				'<?xml encoding="utf-8" ?><body>' . $svg . '</body>',
				LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
			);
		}

		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		if ( ! $loaded ) {
			return '';
		}

		$root = $document->getElementsByTagName( 'svg' )->item( 0 );

		if ( ! $root instanceof DOMElement ) {
			return '';
		}

		// Clean the root element itself, then everything below it.
		$fragment = $document->createElement( 'wrapper' );
		$fragment->appendChild( $root->cloneNode( true ) );
		dnte_sanitize_svg_node( $fragment );

		$clean_root = $fragment->firstChild;

		if ( ! $clean_root instanceof DOMElement ) {
			return '';
		}

		$markup = $document->saveXML( $clean_root );

		if ( ! is_string( $markup ) ) {
			return '';
		}

		$markup = trim( $markup );

		if ( 'html' === $used_parser ) {
			$markup = dnte_svg_restore_camel_case( $markup );
		}

		// Serialising may or may not have emitted the namespace; never emit it twice.
		if ( ! preg_match( '/<svg[^>]*\sxmlns\s*=/i', $markup ) ) {
			$markup = preg_replace( '/<svg\b/i', '<svg xmlns="http://www.w3.org/2000/svg"', $markup, 1 );
		}

		return $markup;
	}
endif;

/* -------------------------------------------------------------------------
 * REST API
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'dnte_rest_get_my_icons' ) ) :
	/**
	 * GET /dnte/v1/my-icons
	 *
	 * @return WP_REST_Response
	 */
	function dnte_rest_get_my_icons() {
		return rest_ensure_response( dnte_get_my_icons() );
	}
endif;

if ( ! function_exists( 'dnte_rest_add_my_icon' ) ) :
	/**
	 * POST /dnte/v1/my-icons
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	function dnte_rest_add_my_icon( WP_REST_Request $request ) {
		$svg = dnte_sanitize_svg_markup( $request->get_param( 'svg' ) );

		if ( '' === $svg ) {
			return new WP_Error(
				'dnte_invalid_svg',
				__( 'That does not look like valid SVG markup, or everything in it was disallowed.', 'dentist-exchange' ),
				array( 'status' => 400 )
			);
		}

		$icons = dnte_get_my_icons();

		if ( count( $icons ) >= DNTE_MY_ICONS_MAX ) {
			return new WP_Error(
				'dnte_icon_limit_reached',
				sprintf(
					/* translators: %d: maximum number of saved icons. */
					__( 'The icon library is full (%d icons). Delete an icon before adding another.', 'dentist-exchange' ),
					DNTE_MY_ICONS_MAX
				),
				array( 'status' => 400 )
			);
		}

		foreach ( $icons as $existing ) {
			if ( isset( $existing['svg'] ) && $existing['svg'] === $svg ) {
				return new WP_Error(
					'dnte_duplicate_icon',
					sprintf(
						/* translators: %s: name of the icon already stored. */
						__( 'This icon is already saved as “%s”.', 'dentist-exchange' ),
						isset( $existing['label'] ) ? $existing['label'] : __( 'Untitled', 'dentist-exchange' )
					),
					array( 'status' => 409 )
				);
			}
		}

		$label = sanitize_text_field( (string) $request->get_param( 'label' ) );

		if ( '' === $label ) {
			$label = __( 'Custom Icon', 'dentist-exchange' );
		}

		$icon_type = 'line' === $request->get_param( 'iconType' ) ? 'line' : 'fill';

		$stroke_width = (float) $request->get_param( 'strokeWidth' );
		$stroke_width = ( $stroke_width >= 0.5 && $stroke_width <= 5 ) ? $stroke_width : 1;

		$icon = array(
			'id'          => wp_generate_uuid4(),
			'label'       => $label,
			'svg'         => $svg,
			'iconType'    => $icon_type,
			'strokeWidth' => $stroke_width,
			'created'     => time(),
			'author'      => get_current_user_id(),
		);

		$icons[] = $icon;

		update_option( DNTE_MY_ICONS_OPTION, $icons, false );

		return rest_ensure_response( $icon );
	}
endif;

if ( ! function_exists( 'dnte_rest_delete_my_icon' ) ) :
	/**
	 * DELETE /dnte/v1/my-icons/<id>
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error
	 */
	function dnte_rest_delete_my_icon( WP_REST_Request $request ) {
		$id    = (string) $request->get_param( 'id' );
		$icons = dnte_get_my_icons();

		$remaining = array_values(
			array_filter(
				$icons,
				static function ( $icon ) use ( $id ) {
					return $icon['id'] !== $id;
				}
			)
		);

		if ( count( $remaining ) === count( $icons ) ) {
			return new WP_Error(
				'dnte_icon_not_found',
				__( 'That icon is no longer in the library.', 'dentist-exchange' ),
				array( 'status' => 404 )
			);
		}

		update_option( DNTE_MY_ICONS_OPTION, $remaining, false );

		return rest_ensure_response(
			array(
				'deleted' => true,
				'id'      => $id,
			)
		);
	}
endif;

if ( ! function_exists( 'dnte_register_my_icons_routes' ) ) :
	/**
	 * Registers the My Icons REST routes.
	 *
	 * @return void
	 */
	function dnte_register_my_icons_routes() {
		register_rest_route(
			'dnte/v1',
			'/my-icons',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => 'dnte_rest_get_my_icons',
					'permission_callback' => 'dnte_my_icons_permissions_check',
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => 'dnte_rest_add_my_icon',
					'permission_callback' => 'dnte_my_icons_permissions_check',
					'args'                => array(
						'svg'         => array(
							'type'     => 'string',
							'required' => true,
						),
						'label'       => array(
							'type' => 'string',
						),
						'iconType'    => array(
							'type' => 'string',
							'enum' => array( 'fill', 'line' ),
						),
						'strokeWidth' => array(
							'type' => 'number',
						),
					),
				),
			)
		);

		register_rest_route(
			'dnte/v1',
			'/my-icons/(?P<id>[A-Za-z0-9\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => 'dnte_rest_delete_my_icon',
					'permission_callback' => 'dnte_my_icons_permissions_check',
					'args'                => array(
						'id' => array(
							'type'     => 'string',
							'required' => true,
						),
					),
				),
			)
		);
	}
endif;
add_action( 'rest_api_init', 'dnte_register_my_icons_routes' );
