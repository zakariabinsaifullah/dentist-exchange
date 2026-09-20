// import editor style
import './editor.scss';

/**
 * WordPress Dependencies
 */
import { useBlockProps, useInnerBlocksProps, BlockControls } from '@wordpress/block-editor';
import { Fragment, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { plus } from '@wordpress/icons';

/**
 * Internal Dependencies
 */
import Inspector from './inspector';

const TEMPLATE = [
    ['dnte/image-accordion-item', { title: __('Title 01', 'dentist-exchange') }],
    ['dnte/image-accordion-item', { title: __('Title 02', 'dentist-exchange') }],
    ['dnte/image-accordion-item', { title: __('Title 03', 'dentist-exchange') }]
];

// block edit function
const Edit = props => {
    const { attributes, setAttributes, clientId } = props;
    const { itemsGap, contentMargin, contentMaxWidth, contentVAlign } = attributes;

    // The control speaks in layout terms; the stylesheet needs flexbox values.
    const V_ALIGN_VALUES = {
        top: 'flex-start',
        center: 'center',
        bottom: 'flex-end'
    };

    // Only breakpoints with an explicit value are written out; style.scss
    // cascades each unset breakpoint up to the next larger one.
    //
    // These land on the parent wrapper, and the items read them from there —
    // the spacing between an item's title, description and button is one
    // setting for the whole accordion rather than per item.
    const cssCustomProperties = {
        ...(itemsGap?.Desktop && { '--dgap': `${itemsGap.Desktop}` }),
        ...(itemsGap?.Tablet && { '--tgap': `${itemsGap.Tablet}` }),
        ...(itemsGap?.Mobile && { '--mgap': `${itemsGap.Mobile}` }),
        ...(contentMargin?.Desktop && { '--dcm': `${contentMargin.Desktop}` }),
        ...(contentMargin?.Tablet && { '--tcm': `${contentMargin.Tablet}` }),
        ...(contentMargin?.Mobile && { '--mcm': `${contentMargin.Mobile}` }),
        ...(contentMaxWidth && { '--content-max-width': `${contentMaxWidth}` }),
        '--content-valign': V_ALIGN_VALUES[contentVAlign] || 'center'
    };

    useEffect(() => {
        setAttributes({ blockStyle: cssCustomProperties });
    }, [itemsGap, contentMargin, contentMaxWidth, contentVAlign]);

    const blockProps = useBlockProps({
        style: cssCustomProperties
    });

    const innerBlockProps = useInnerBlocksProps(
        { className: 'dnte-image-accordion' },
        {
            renderAppender: false,
            allowedBlocks: ['dnte/image-accordion-item'],
            template: TEMPLATE,
            templateLock: false
        }
    );

    const addItem = () => {
        const childBlocks = wp.data.select('core/block-editor').getBlocks(clientId);
        const newBlock = wp.blocks.createBlock('dnte/image-accordion-item', { title: __('New Item', 'dentist-exchange') });
        wp.data.dispatch('core/block-editor').insertBlocks(newBlock, childBlocks.length, clientId);
    };

    return (
        <Fragment>
            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton icon={plus} label={__('Add Item', 'dentist-exchange')} onClick={addItem} />
                </ToolbarGroup>
            </BlockControls>
            <Inspector {...props} />
            <div {...blockProps}>
                <div {...innerBlockProps} />
            </div>
        </Fragment>
    );
};

export default Edit;
