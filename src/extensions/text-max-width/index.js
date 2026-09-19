/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import { NativeRangeControl } from '../../components';

import './style.scss';

const SUPPORTED_BLOCKS = ['core/heading', 'core/paragraph'];
const ATTRIBUTE = 'maxWidth';
const CLASS_NAME = 'has-max-width';

addFilter('blocks.registerBlockType', 'dnte/text-max-width-add-attribute', (settings, name) => {
    if (!SUPPORTED_BLOCKS.includes(name)) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [ATTRIBUTE]: {
                type: 'number',
                default: undefined
            }
        }
    };
});

addFilter(
    'editor.BlockEdit',
    'dnte/text-max-width-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (!SUPPORTED_BLOCKS.includes(name)) {
                return <BlockEdit {...props} />;
            }

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorControls>
                        <PanelBody title={__('Max Width', 'dentist-exchange')}>
                            <NativeRangeControl
                                label={__('Maximum width (px)', 'dentist-exchange')}
                                value={attributes[ATTRIBUTE]}
                                onChange={value => setAttributes({ [ATTRIBUTE]: value })}
                                min={200}
                                max={1600}
                                step={1}
                                resetFallbackValue={null}
                            />
                        </PanelBody>
                    </InspectorControls>
                </>
            );
        };
    })
);

addFilter(
    'editor.BlockListBlock',
    'dnte/text-max-width-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (!SUPPORTED_BLOCKS.includes(name) || !attributes[ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const wrapperProps = {
                ...props.wrapperProps,
                style: {
                    ...props.wrapperProps?.style,
                    maxWidth: attributes[ATTRIBUTE] + 'px'
                }
            };

            const classes = [props.className, CLASS_NAME].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} wrapperProps={wrapperProps} />;
        };
    })
);
