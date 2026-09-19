<?php
/**
 * Custom Block Registration
 *
 * Registers all custom block types from the build directory.
 *
 * WHY the manifest-collection APIs are intentionally avoided:
 * `wp_register_block_types_from_metadata_collection()` registers every entry in
 * the manifest without checking whether the block directory actually exists under
 * the supplied base path.  Our manifest includes entries that live outside
 * build/blocks/ (e.g. extensions), which causes WordPress to pass null to
 * dirname() when it cannot resolve the file paths — producing PHP deprecation
 * warnings.  Using a manual loop with an is_dir() guard is the safe alternative.
 *
 * @package Dentist_Exchange
 */

if ( ! function_exists( 'dnte_register_blocks' ) ) :
	/**
	 * Registers custom blocks from the build directory.
	 *
	 * Registration order:
	 * 1. If build/blocks exists, use the manifest for metadata caching (WP 6.5+)
	 *    then register only the blocks whose directories actually exist.
	 * 2. If no manifest, scan build/blocks/ directly.
	 * 3. If build/ does not exist at all, fall back to src/ for development.
	 */
	function dnte_register_blocks() {
		$build_dir     = __DIR__ . '/../build/blocks';
		$manifest_path = __DIR__ . '/../build/blocks-manifest.php';
		$src_dir       = __DIR__ . '/../src/blocks';

		$register = static function ( string $dir ): void {
			if ( file_exists( $dir . '/block.json' ) && file_exists( $dir . '/index.asset.php' ) ) {
				register_block_type( $dir );
			}
		};

		if ( ! is_dir( $build_dir ) ) {
			if ( is_dir( $src_dir ) ) {
				foreach ( glob( $src_dir . '/*', GLOB_ONLYDIR ) as $block_path ) {
					$register( $block_path );
				}
			}
			return;
		}

		if ( file_exists( $manifest_path ) ) {
			if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
				wp_register_block_metadata_collection( $build_dir, $manifest_path );
			}

			foreach ( array_keys( require $manifest_path ) as $block_slug ) {
				$block_dir = $build_dir . '/' . $block_slug;

				if ( is_dir( $block_dir ) ) {
					$register( $block_dir );
				}
			}

			return;
		}

		foreach ( glob( $build_dir . '/*', GLOB_ONLYDIR ) as $block_dir ) {
			$register( $block_dir );
		}
	}
endif;
add_action( 'init', 'dnte_register_blocks' );
