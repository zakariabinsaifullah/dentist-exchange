<?php
/**
 * Social Share block render template.
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner block content.
 * @var WP_Block $block      Block instance.
 */

$show_copy_link = $attributes['showCopyLink'] ?? true;
$show_linkedin  = $attributes['showLinkedIn'] ?? true;
$show_twitter   = $attributes['showTwitter']  ?? true;
$show_facebook  = $attributes['showFacebook'] ?? true;
$block_style    = $attributes['blockStyle']   ?? [];
$justify_content = $attributes['justifyContent'] ?? '';

$post_url   = get_permalink();
$post_title = get_the_title();

$encoded_url   = rawurlencode( $post_url );
$encoded_title = rawurlencode( $post_title );

$linkedin_url = 'https://www.linkedin.com/sharing/share-offsite/?url=' . $encoded_url;
$twitter_url  = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
$facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;

// Build inline style string from blockStyle CSS custom properties.
$style_parts = [];
foreach ( $block_style as $property => $value ) {
	if ( ! empty( $value ) ) {
		$style_parts[] = esc_attr( $property ) . ':' . esc_attr( $value );
	}
}
$inline_style = implode( ';', $style_parts );

// Build class names with justification.
$extra_classes = ! empty( $justify_content ) ? 'justify-' . esc_attr( $justify_content ) : '';

$wrapper_attributes = get_block_wrapper_attributes(
    array_filter( [
        'class'   => $extra_classes,
        'style'   => ! empty( $inline_style ) ? $inline_style : null
    ] )
);
?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="dnte-social-share__icons">

		<?php if ( $show_copy_link ) : ?>
		<button
			class="dnte-social-share__item is-copy-link"
			aria-label="<?php esc_attr_e( 'Copy link', 'dentist-exchange' ); ?>"
			data-copy-url="<?php echo esc_attr( $post_url ); ?>"
		>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path fill="currentColor" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
			</svg>
		</button>
		<?php endif; ?>

		<?php if ( $show_linkedin ) : ?>
		<a
			class="dnte-social-share__item is-linkedin"
			href="<?php echo esc_url( $linkedin_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'dentist-exchange' ); ?>"
		>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path fill="currentColor" d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
			</svg>
		</a>
		<?php endif; ?>

		<?php if ( $show_twitter ) : ?>
		<a
			class="dnte-social-share__item is-twitter"
			href="<?php echo esc_url( $twitter_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Share on X', 'dentist-exchange' ); ?>"
		>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
			</svg>
		</a>
		<?php endif; ?>

		<?php if ( $show_facebook ) : ?>
		<a
			class="dnte-social-share__item is-facebook"
			href="<?php echo esc_url( $facebook_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Share on Facebook', 'dentist-exchange' ); ?>"
		>
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
				<path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
			</svg>
		</a>
		<?php endif; ?>

	</div>
</div>
