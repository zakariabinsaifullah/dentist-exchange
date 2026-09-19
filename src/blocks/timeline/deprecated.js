import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import classNames from 'classnames';

/**
 * v1 stored a single, non-responsive "Gap (icon → content)" value in
 * `contentGap` and printed it as `--item-gap`. It becomes the desktop entry of
 * the responsive `iconGaps` object.
 */
const v1 = {
    attributes: {
        uniqueId: {
            type: 'string'
        },
        contentGap: {
            type: 'number'
        }
    },
    save({ attributes }) {
        const { uniqueId, contentGap } = attributes;

        const blockProps = useBlockProps.save({
            className: classNames(uniqueId),
            style: {
                ...(contentGap && { '--item-gap': `${contentGap}px` })
            }
        });

        return (
            <div {...blockProps}>
                <div className="dnte-timeline">
                    <InnerBlocks.Content />
                </div>
            </div>
        );
    },
    migrate({ uniqueId, contentGap }) {
        return {
            uniqueId,
            ...(contentGap && { iconGaps: { Desktop: contentGap } })
        };
    }
};

export default [v1];
