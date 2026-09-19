import { InnerBlocks } from '@wordpress/block-editor';

/**
 * The markup lives in render.php.
 *
 * Only the inner blocks are written to the post, so the slides' content is
 * still real, editable block markup; PHP assembles the slider around it and
 * reads each child slide's attributes for the thumbnail strip — something a
 * parent's `save()` cannot do.
 */
export default function save() {
    return <InnerBlocks.Content />;
}
