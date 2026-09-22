<?php
/**
 * Story Card block render template.
 *
 * Rendered in PHP rather than saved as markup so the connector arrow is inlined
 * from assets/svg/arrows/ at render time: editing an SVG there updates every
 * card on the site, and no artwork is frozen into post content.
 *
 * Every measurement here is a percentage rather than a pixel length, so the
 * composition in the design holds at any container width:
 *   - offsetY and the arrow's width are percentages of the section's width,
 *   - the arrow's left is a percentage of the card's width,
 *   - the arrow's top is a percentage of the card's height.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content.
 * @var WP_Block $block      Block instance.
 */

$icon        = $attributes['icon'] ?? array();
$icon_svg    = '';

/*
 * Inline the uploaded SVG rather than referencing it from an <img>: as part of
 * the document it can inherit currentColor and needs no extra request. The
 * file was sanitised on upload by dnte_sanitize_svg_upload(), and is passed
 * through kses again here in case it was uploaded before that filter existed.
 */
if ( ! empty( $icon['id'] ) ) {
	$icon_file = get_attached_file( (int) $icon['id'] );

	if ( $icon_file && 'svg' === strtolower( pathinfo( $icon_file, PATHINFO_EXTENSION ) ) && file_exists( $icon_file ) ) {
		$icon_svg = (string) file_get_contents( $icon_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local upload, not a remote request.
	}
}

$badge_side  = 'right' === ( $attributes['badgeSide'] ?? 'left' ) ? 'right' : 'left';
$image       = $attributes['image'] ?? array();
$heading     = $attributes['heading'] ?? '';
$heading_tag = $attributes['headingTag'] ?? 'h3';
$description = $attributes['description'] ?? '';
$lane        = $attributes['lane'] ?? 'auto';
$offset_y    = isset( $attributes['offsetY'] ) && is_numeric( $attributes['offsetY'] ) ? (float) $attributes['offsetY'] : null;

$arrow        = $attributes['arrow'] ?? '';
$arrow_markup = $arrow ? dnte_arrow_svg( $arrow ) : '';
$arrow_mobile = $attributes['arrowMobile'] ?? 'auto';

// Only h2–h6 and p may carry the heading; anything else falls back to h3.
$allowed_tags = array( 'h2', 'h3', 'h4', 'h5', 'h6', 'p' );
if ( ! in_array( $heading_tag, $allowed_tags, true ) ) {
	$heading_tag = 'h3';
}

$classes = array( 'dnte-story-card', 'has-badge-' . $badge_side );

if ( in_array( $lane, array( 'left', 'right' ), true ) ) {
	$classes[] = 'is-lane-' . $lane;
}

$styles = array();

if ( null !== $offset_y ) {
	/*
	 * The card's distance from the section's top, as a percentage of the
	 * section's *width* — that is the design's own coordinate system, so the
	 * whole composition scales instead of drifting when card heights change.
	 * It lands as a margin because percentage margins resolve against width;
	 * view.js positions the card absolutely and this margin becomes its top.
	 * A card without the attribute is placed by view.js below the previous
	 * card. In the no-JS fallback the cards render as a plain column and this
	 * is ignored.
	 */
	$styles[] = '--dnte-card-top:' . $offset_y . '%';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $classes ),
		'style' => implode( ';', $styles ),
	)
);

/*
 * The heading is RichText, so it may legitimately carry the theme's annotation
 * spans (inc/annotations.php) alongside line breaks — wp_kses_post keeps both
 * and strips anything else.
 */
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<?php if ( $icon_svg ) : ?>
		<span class="dnte-story-card__badge" aria-hidden="true">
			<?php echo wp_kses( $icon_svg, dnte_iconic_button_svg_kses_args() ); ?>
		</span>
	<?php elseif ( ! empty( $icon['url'] ) ) : ?>
		<span class="dnte-story-card__badge" aria-hidden="true">
			<?php // A raster icon has no markup to inline. ?>
			<img src="<?php echo esc_url( $icon['url'] ); ?>" alt="" />
		</span>
	<?php endif; ?>

	<?php if ( ! empty( $image['url'] ) ) : ?>
		<figure class="dnte-story-card__media">
			<img
				class="dnte-story-card__image"
				src="<?php echo esc_url( $image['url'] ); ?>"
				alt="<?php echo esc_attr( $image['alt'] ?? '' ); ?>"
				loading="lazy"
				decoding="async"
			/>
		</figure>
	<?php endif; ?>

	<div class="dnte-story-card__content">
		<?php if ( $heading ) : ?>
			<<?php echo esc_html( $heading_tag ); ?> class="dnte-story-card__heading">
				<?php echo wp_kses_post( $heading ); ?>
			</<?php echo esc_html( $heading_tag ); ?>>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<p class="dnte-story-card__desc"><?php echo wp_kses_post( $description ); ?></p>
		<?php endif; ?>
	</div>

	<?php
	if ( $arrow_markup ) :
		$connector_classes = array( 'dnte-story-card__connector' );

		// 'auto' leaves the breakpoint rule in style.scss to decide — which
		// drops every card's arrow on a phone and keeps only the section's
		// trailing one. These two force the issue for a single card.
		if ( 'show' === $arrow_mobile ) {
			$connector_classes[] = 'is-mobile-visible';
		} elseif ( 'hide' === $arrow_mobile ) {
			$connector_classes[] = 'is-mobile-hidden';
		}

		$connector_styles = array(
			'--dnte-arrow-top:' . (float) ( $attributes['arrowTop'] ?? 0 ) . '%',
			'--dnte-arrow-left:' . (float) ( $attributes['arrowLeft'] ?? 100 ) . '%',
			'--dnte-arrow-width:' . (float) ( $attributes['arrowWidth'] ?? 45 ) . '%',
			'--dnte-arrow-rotate:' . (float) ( $attributes['arrowRotate'] ?? 0 ) . 'deg',
			'--dnte-arrow-flip:' . ( ! empty( $attributes['arrowFlipX'] ) ? '-1' : '1' ),
			'--dnte-arrow-angle:' . dnte_arrow_angle( $arrow ) . 'deg',
		);
		?>
		<span
			class="<?php echo esc_attr( implode( ' ', $connector_classes ) ); ?>"
			style="<?php echo esc_attr( implode( ';', $connector_styles ) ); ?>"
			data-arrow-delay="<?php echo esc_attr( (int) ( $attributes['arrowDelay'] ?? 180 ) ); ?>"
			aria-hidden="true"
		>
			<span class="dnte-story-card__connector-art">
				<?php echo dnte_arrow_svg( $arrow ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Theme-owned artwork from assets/svg/arrows/. ?>
			</span>
		</span>
	<?php endif; ?>
</div>
