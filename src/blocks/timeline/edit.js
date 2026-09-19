import { useBlockProps, useInnerBlocksProps, BlockControls } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { plus } from '@wordpress/icons';
import classNames from 'classnames';
import { timelineGapProperties } from './gaps';

const TEMPLATE = [
    ['dnte/timeline-item', {}],
    ['dnte/timeline-item', {}],
    ['dnte/timeline-item', {}]
];

const Edit = props => {
    const { attributes, clientId } = props;
    const { uniqueId, itemGaps, iconGaps } = attributes;

    const blockProps = useBlockProps({
        className: classNames(uniqueId),
        style: timelineGapProperties(itemGaps, iconGaps)
    });

    const innerBlockProps = useInnerBlocksProps(
        { className: 'dnte-timeline' },
        {
            allowedBlocks: ['dnte/timeline-item'],
            template: TEMPLATE,
            templateLock: false,
            renderAppender: false
        }
    );

    const addItem = () => {
        const childBlocks = wp.data.select('core/block-editor').getBlocks(clientId);
        const newBlock = wp.blocks.createBlock('dnte/timeline-item', {});
        wp.data.dispatch('core/block-editor').insertBlocks(newBlock, childBlocks.length, clientId);
    };

    return (
        <Fragment>
            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton icon={plus} label={__('Add Timeline Item', 'dentist-exchange')} onClick={addItem} />
                </ToolbarGroup>
            </BlockControls>
            <div {...blockProps}>
                <div {...innerBlockProps} />
            </div>
        </Fragment>
    );
};

export default Edit;
