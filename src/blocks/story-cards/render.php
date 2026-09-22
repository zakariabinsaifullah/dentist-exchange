<?php
/**
 * Story Cards block render template.
 *
 * Dynamic for one reason: the trailing arrow — the long curve that runs out of
 * the bottom of the section in the design — belongs to the section rather than
 * to any card. The last card already carries its own connector, and anchoring
 * the trailer here is also what lets the narrow-screen rule keep exactly this
 * one arrow and drop the rest.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content (the cards).
 * @var WP_Block $block      Block instance.
 */

$card_width = (float) ( $attributes['cardWidth'] ?? 40 );
$lane_gap   = (float) ( $attributes['laneGap'] ?? 0 );
$animate    = ! empty( $attributes['animate'] );
$stagger    = (int) ( $attributes['revealStagger'] ?? 120 );

$trailing        = $attributes['trailingArrow'] ?? '';
$trailing_markup = $trailing ? dnte_arrow_svg( $trailing ) : '';

$styles = array(
	'--dnte-card-width:' . $card_width . '%',
	'--dnte-lane-gap:' . $lane_gap . 'px',
	'--dnte-mobile-gap:' . (float) ( $attributes['mobileGap'] ?? 70 ) . 'px',
);

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class'         => 'dnte-story-cards',
		'style'         => implode( ';', $styles ),
		// view.js reads these; it does nothing at all when animation is off.
		'data-animate'  => $animate ? 'true' : 'false',
		'data-stagger'  => (string) $stagger,
	)
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner blocks, already rendered and escaped. ?>

	<?php
	if ( $trailing_markup ) :
		/*
		 * Anchored to the bottom of the section: `top: 100%` plus a percentage
		 * margin, which — like every other measurement in this block —
		 * resolves against the section's width, so the trailer keeps its place
		 * in the composition at any size.
		 */
		$trailing_styles = array(
			'--dnte-trailing-left:' . (float) ( $attributes['trailingLeft'] ?? 41 ) . '%',
			'--dnte-trailing-offset:' . (float) ( $attributes['trailingOffset'] ?? -13 ) . '%',
			'--dnte-trailing-width:' . (float) ( $attributes['trailingWidth'] ?? 20 ) . '%',
			'--dnte-trailing-flip:' . ( ! empty( $attributes['trailingFlipX'] ) ? '-1' : '1' ),
			'--dnte-arrow-angle:' . dnte_arrow_angle( $trailing ) . 'deg',
		);
		?>
		<span
			class="dnte-story-cards__trailing"
			style="<?php echo esc_attr( implode( ';', $trailing_styles ) ); ?>"
			data-arrow-delay="<?php echo esc_attr( (int) ( $attributes['trailingDelay'] ?? 180 ) ); ?>"
			aria-hidden="true"
		>
			<span class="dnte-story-card__connector-art">
				<?php echo $trailing_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme-owned artwork from assets/svg/arrows/. ?>
			</span>
		</span>
	<?php endif; ?>
</div>
