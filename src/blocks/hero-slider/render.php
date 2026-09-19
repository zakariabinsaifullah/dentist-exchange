<?php
/**
 * Hero Slider — server render.
 *
 * Rendered in PHP rather than saved as markup for one reason: a block's
 * `save()` cannot read its children's attributes, and in per-slide mode the
 * thumbnail strip needs each child slide's image. `WP_Block::$inner_blocks`
 * gives PHP both the attributes and the rendered content of every child, which
 * also lets the shared content and the slides live in the same inner-blocks
 * region and be told apart here by block name.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks, already rendered.
 * @var WP_Block $block      Block instance.
 *
 * @package Dentist_Exchange
 */

$dnte_mode      = isset( $attributes['contentMode'] ) ? $attributes['contentMode'] : 'fixed';
$dnte_per_slide = 'per-slide' === $dnte_mode;

// ── Gather the slides ────────────────────────────────────────────────────────
$dnte_slides = array();
$dnte_shared = '';

/*
 * The content layer is rebuilt from the inner blocks rather than taken from
 * `$content`. For a post saved before this block became dynamic, `$content`
 * still holds the whole legacy wrapper markup — slides, thumbnails and all —
 * which would end up nested inside the new content layer. Rendering the child
 * blocks directly takes only what they contain and leaves the stale wrapper
 * behind, so old posts come out right without being re-saved first.
 */
foreach ( $block->inner_blocks as $dnte_child ) {
	$dnte_is_slide = 'dnte/hero-slide' === $dnte_child->name;

	if ( $dnte_per_slide ) {
		if ( ! $dnte_is_slide || empty( $dnte_child->attributes['url'] ) ) {
			continue;
		}

		$dnte_attrs            = $dnte_child->attributes;
		$dnte_attrs['content'] = $dnte_child->render();
		$dnte_slides[]         = $dnte_attrs;
		continue;
	}

	/*
	 * Fixed mode. Slide blocks can survive a switch back from per-slide mode, so
	 * they are skipped here rather than having their content folded into the
	 * shared layer.
	 */
	if ( ! $dnte_is_slide ) {
		$dnte_shared .= $dnte_child->render();
	}
}

if ( ! $dnte_per_slide ) {
	$dnte_slides = isset( $attributes['slides'] ) && is_array( $attributes['slides'] ) ? $attributes['slides'] : array();
}

if ( ! $dnte_slides ) {
	return '';
}

// ── Options ──────────────────────────────────────────────────────────────────
$dnte_show_thumbs = ! isset( $attributes['showThumbs'] ) || $attributes['showThumbs'];
$dnte_show_arrows = ! isset( $attributes['showArrows'] ) || $attributes['showArrows'];

// A single image is a static hero — nothing for the strip or arrows to move to.
$dnte_has_thumbs = $dnte_show_thumbs && count( $dnte_slides ) > 1;
$dnte_has_arrows = $dnte_show_arrows && count( $dnte_slides ) > 1;

$dnte_autoplay = ! empty( $attributes['autoplay'] );

$dnte_options = array(
	'effect'    => isset( $attributes['effect'] ) ? $attributes['effect'] : 'fade',
	'speed'     => isset( $attributes['speed'] ) ? (int) $attributes['speed'] : 800,
	'loop'      => ! empty( $attributes['loop'] ),
	'autoplay'  => $dnte_autoplay ? array( 'delay' => isset( $attributes['delay'] ) ? (int) $attributes['delay'] : 5000 ) : false,
	'thumbGaps' => array(
		'Desktop' => 20,
		'Tablet'  => 20,
		'Mobile'  => 12,
	),
);

if ( isset( $attributes['thumbGaps'] ) && is_array( $attributes['thumbGaps'] ) ) {
	foreach ( array( 'Desktop', 'Tablet', 'Mobile' ) as $dnte_device ) {
		if ( isset( $attributes['thumbGaps'][ $dnte_device ] ) && '' !== $attributes['thumbGaps'][ $dnte_device ] ) {
			$dnte_options['thumbGaps'][ $dnte_device ] = (int) $attributes['thumbGaps'][ $dnte_device ];
		} elseif ( isset( $attributes['thumbGaps']['Desktop'] ) ) {
			$dnte_options['thumbGaps'][ $dnte_device ] = (int) $attributes['thumbGaps']['Desktop'];
		}
	}
}

// ── Wrapper ──────────────────────────────────────────────────────────────────
$dnte_style = '';

if ( isset( $attributes['blockStyle'] ) && is_array( $attributes['blockStyle'] ) ) {
	foreach ( $attributes['blockStyle'] as $dnte_prop => $dnte_value ) {
		if ( '' === $dnte_value || null === $dnte_value ) {
			continue;
		}

		$dnte_style .= $dnte_prop . ':' . $dnte_value . ';';
	}
}

/*
 * The editor writes `--thumb-count` from its own slide list, which in per-slide
 * mode is not the source of truth. Rewriting it here keeps the strip's width
 * correct however the slides were authored.
 */
$dnte_style .= '--thumb-count:' . count( $dnte_slides ) . ';';

$dnte_classes = array(
	'content-' . ( isset( $attributes['contentAlign'] ) ? $attributes['contentAlign'] : 'left' ),
	'thumbs-' . ( isset( $attributes['thumbsAlign'] ) ? $attributes['thumbsAlign'] : 'left' ),
);

if ( $dnte_has_thumbs ) {
	$dnte_classes[] = 'has-thumbs';
}

if ( $dnte_per_slide ) {
	$dnte_classes[] = 'is-per-slide';
}

$dnte_wrapper = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $dnte_classes ),
		'style' => $dnte_style,
	)
);

// ── Helpers ──────────────────────────────────────────────────────────────────
if ( ! function_exists( 'dnte_hero_slide_image' ) ) :
	/**
	 * A slide's background image, wrapped in `<picture>` only when it carries a
	 * separate mobile file — so the browser downloads one image, never both.
	 *
	 * @param array $slide Slide data.
	 * @param int   $index Slide position.
	 * @return string
	 */
	function dnte_hero_slide_image( $slide, $index ) {
		$img = sprintf(
			'<img class="dnte-hero-image" src="%s" alt="%s" loading="%s" decoding="async"/>',
			esc_url( $slide['url'] ),
			esc_attr( isset( $slide['alt'] ) ? $slide['alt'] : '' ),
			0 === $index ? 'eager' : 'lazy'
		);

		if ( empty( $slide['mobileUrl'] ) ) {
			return $img;
		}

		return '<picture class="dnte-hero-picture">'
			. '<source media="(max-width: 767px)" srcset="' . esc_url( $slide['mobileUrl'] ) . '"/>'
			. $img
			. '</picture>';
	}
endif;

if ( ! function_exists( 'dnte_hero_slide_thumb' ) ) :
	/**
	 * What the strip shows for a slide: the hand-picked thumbnail, the derived
	 * crop, or the background image as a last resort.
	 *
	 * @param array $slide Slide data.
	 * @return string
	 */
	function dnte_hero_slide_thumb( $slide ) {
		foreach ( array( 'thumb', 'autoThumb', 'url' ) as $key ) {
			if ( ! empty( $slide[ $key ] ) ) {
				return $slide[ $key ];
			}
		}

		return '';
	}
endif;

if ( ! function_exists( 'dnte_hero_nav_button' ) ) :
	/**
	 * A prev/next control. The chevron itself is drawn in CSS, so nothing is
	 * emitted here unless the author supplied a custom SVG.
	 *
	 * @param string $direction  `prev` or `next`.
	 * @param string $custom_svg Optional inline SVG.
	 * @return string
	 */
	function dnte_hero_nav_button( $direction, $custom_svg ) {
		$label = 'prev' === $direction
			? __( 'Previous image', 'dentist-exchange' )
			: __( 'Next image', 'dentist-exchange' );

		$inner = '';

		if ( $custom_svg ) {
			$inner = '<div class="dnte-custom-svg-container">' . wp_kses( $custom_svg, dnte_hero_svg_allowed_html() ) . '</div>';
		}

		return sprintf(
			'<button type="button" class="dnte-nav swiper-custom-%s" aria-label="%s">%s</button>',
			esc_attr( $direction ),
			esc_attr( $label ),
			$inner
		);
	}
endif;

if ( ! function_exists( 'dnte_hero_svg_allowed_html' ) ) :
	/**
	 * The SVG subset allowed through `wp_kses` for author-supplied icons.
	 *
	 * @return array
	 */
	function dnte_hero_svg_allowed_html() {
		$attrs = array(
			'class'           => true,
			'style'           => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'd'               => true,
			'points'          => true,
			'x'               => true,
			'y'               => true,
			'x1'              => true,
			'x2'              => true,
			'y1'              => true,
			'y2'              => true,
			'cx'              => true,
			'cy'              => true,
			'r'               => true,
			'rx'              => true,
			'ry'              => true,
			'width'           => true,
			'height'          => true,
			'transform'       => true,
		);

		return array(
			'svg'      => array_merge(
				$attrs,
				array(
					'xmlns'       => true,
					'viewbox'     => true,
					'aria-hidden' => true,
					'focusable'   => true,
				)
			),
			'g'        => $attrs,
			'path'     => $attrs,
			'circle'   => $attrs,
			'rect'     => $attrs,
			'polygon'  => $attrs,
			'polyline' => $attrs,
			'ellipse'  => $attrs,
			'line'     => $attrs,
			'defs'     => $attrs,
			'title'    => array(),
		);
	}
endif;

// ── Output ───────────────────────────────────────────────────────────────────
$dnte_has_overlay = ! empty( $attributes['overlayColor'] ) || ! empty( $attributes['overlayGradient'] );

ob_start();
?>
<div <?php echo $dnte_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built by core. ?> data-options="<?php echo esc_attr( wp_json_encode( $dnte_options ) ); ?>">
	<div class="dnte-hero-bg swiper">
		<div class="swiper-wrapper">
			<?php foreach ( $dnte_slides as $dnte_index => $dnte_slide ) : ?>
				<div class="swiper-slide">
					<?php echo dnte_hero_slide_image( $dnte_slide, $dnte_index ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper. ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $dnte_has_overlay ) : ?>
		<div class="dnte-hero-overlay" aria-hidden="true"></div>
	<?php endif; ?>

	<?php if ( $dnte_per_slide ) : ?>
		<?php
		/*
		 * Every slide gets a content layer, shown and hidden in step with the
		 * carousel. A slide left empty simply shows its background image.
		 */
		?>
		<div class="dnte-hero-content">
			<div class="dnte-hero-content-stack">
			<?php foreach ( $dnte_slides as $dnte_index => $dnte_slide ) : ?>
				<div class="dnte-hero-content-slide<?php echo 0 === $dnte_index ? ' is-active' : ''; ?>" data-index="<?php echo (int) $dnte_index; ?>">
					<div class="dnte-hero-content-inner">
						<?php echo isset( $dnte_slide['content'] ) ? $dnte_slide['content'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered blocks. ?>
					</div>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
	<?php else : ?>
		<div class="dnte-hero-content">
			<div class="dnte-hero-content-inner"><?php echo $dnte_shared; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rendered blocks. ?></div>
		</div>
	<?php endif; ?>

	<?php if ( $dnte_has_thumbs ) : ?>
		<div class="dnte-hero-thumbs-wrap">
			<?php
			if ( $dnte_has_arrows ) {
				echo dnte_hero_nav_button( 'prev', isset( $attributes['prevCustomSvg'] ) ? $attributes['prevCustomSvg'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			}
			?>

			<div class="dnte-hero-thumbs swiper">
				<div class="swiper-wrapper">
					<?php foreach ( $dnte_slides as $dnte_index => $dnte_slide ) : ?>
						<div class="swiper-slide">
							<button
								type="button"
								class="dnte-hero-thumb"
								data-index="<?php echo (int) $dnte_index; ?>"
								aria-label="<?php echo esc_attr( sprintf( /* translators: %d: image number within the hero slider. */ __( 'Show image %d', 'dentist-exchange' ), $dnte_index + 1 ) ); ?>"
							>
								<img src="<?php echo esc_url( dnte_hero_slide_thumb( $dnte_slide ) ); ?>" alt="" loading="lazy" decoding="async"/>
							</button>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<?php
			if ( $dnte_has_arrows ) {
				echo dnte_hero_nav_button( 'next', isset( $attributes['nextCustomSvg'] ) ? $attributes['nextCustomSvg'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in helper.
			}
			?>
		</div>
	<?php endif; ?>
</div>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Assembled above.
