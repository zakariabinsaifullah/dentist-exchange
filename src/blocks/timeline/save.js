import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import classNames from 'classnames';
import { timelineGapProperties } from './gaps';

const Save = props => {
    const { attributes } = props;
    const { uniqueId, itemGaps, iconGaps } = attributes;

    const blockProps = useBlockProps.save({
        className: classNames(uniqueId),
        style: timelineGapProperties(itemGaps, iconGaps)
    });

    return (
        <div {...blockProps}>
            <div className="dnte-timeline">
                <InnerBlocks.Content />
            </div>
        </div>
    );
};

export default Save;
