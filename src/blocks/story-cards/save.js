/**
 * WordPress dependencies
 */
import { InnerBlocks } from '@wordpress/block-editor';

/**
 * Save function for the block.
 *
 * Only the cards are persisted. The wrapper, its lane variables and the
 * section's trailing arrow are all produced by render.php, so the artwork is
 * never frozen into post content.
 *
 * @return {WPElement} Element to render.
 */
export default function save() {
    return <InnerBlocks.Content />;
}
