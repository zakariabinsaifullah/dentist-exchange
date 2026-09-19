import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

/**
 * Only the slide's content is saved as markup. The images are read from the
 * block's attributes by the slider's PHP renderer, which is what lets the
 * thumbnail strip know about them — a parent's `save()` cannot reach into its
 * children's attributes, but `WP_Block::$inner_blocks` can.
 */
export default function save() {
    const blockProps = useBlockProps.save({ className: 'dnte-hero-slide-content' });
    const innerBlocksProps = useInnerBlocksProps.save(blockProps);

    return <div {...innerBlocksProps} />;
}
