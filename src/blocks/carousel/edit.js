/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    useInnerBlocksProps,
    BlockControls,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles
} from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal Dependencies
 */
import './editor.scss';
import Inspector from './inspector';
import classNames from 'classnames';
import { generateHeightStyles } from './utils';

// Block edit function
const Edit = props => {
    const { attributes, setAttributes, clientId, isSelected } = props;
    const { heightType, heights, vAligns, gaps, resMode, visibleItems, overflowVisible } = attributes;

    const colorProps = getColorClassesAndStyles(attributes);
    const spacingProps = getSpacingClassesAndStyles(attributes);

    // CSS Custom Properties
    const cssCustomProperties = {
        ...generateHeightStyles(heightType, heights, vAligns)
    };

    // Update block style when CSS properties change
    useEffect(() => {
        setAttributes({
            blockStyle: cssCustomProperties
        });
    }, [heightType, heights, vAligns]);

    // Inner blocks configuration — the carousel is always in Rotate Stack mode.
    const innerBlocksProps = useInnerBlocksProps(
        {
            className: classNames('dnte-editor-slides', 'is-stack', {
                [`visible-${visibleItems?.[resMode]}`]: visibleItems?.[resMode],
                [`gap-${gaps[resMode]}`]: gaps[resMode]
            })
        },
        {
            allowedBlocks: ['dnte/slide'],
            template: [['dnte/slide'], ['dnte/slide']],
            templateLock: false
        }
    );

    // Block Props
    const blockProps = useBlockProps({
        style: { ...cssCustomProperties, ...colorProps.style, ...spacingProps.style },
        className: classNames(colorProps.className, spacingProps.className, 'is-stack', {
            'has-visible-overflow': overflowVisible
        })
    });

    return (
        <>
            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton
                        icon="insert"
                        label={__('Add Slide', 'dentist-exchange')}
                        onClick={() => {
                            const innerBlocks = wp.data.select('core/block-editor').getBlocks(clientId);
                            const newBlock = wp.blocks.createBlock('dnte/slide');
                            wp.data.dispatch('core/block-editor').insertBlock(newBlock, innerBlocks.length, clientId);
                        }}
                    />
                </ToolbarGroup>
            </BlockControls>

            {isSelected && <Inspector {...props} />}

            <div {...blockProps}>
                <div {...innerBlocksProps} />
            </div>
        </>
    );
};

export default Edit;
