/**
 * WordPress dependencies
 */
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

// block save function
const Save = props => {
    const { attributes } = props;
    const { blockStyle } = attributes;

    const blockProps = useBlockProps.save({
        style: blockStyle
    });

    return (
        <div {...blockProps}>
            <div className="dnte-image-accordion">
                <InnerBlocks.Content />
            </div>
        </div>
    );
};

export default Save;
