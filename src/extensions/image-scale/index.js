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
import { NativeToggleControl, NativeRangeControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'core/image';
const TOGGLE_ATTRIBUTE = 'isScaled';
const SCALE_ATTRIBUTE = 'imageScale';
const CLASS_NAME = 'has-scaled-image';
const DEFAULT_SCALE = 1.1;

/**
 * Add the `isScaled` / `imageScale` attributes to core/image.
 *
 * Replaces the old `is-style-scaled` block style variation, which was locked to
 * a single 1.1 scale.
 */
addFilter('blocks.registerBlockType', 'dnte/image-scale-add-attribute', (settings, name) => {
    if (name !== BLOCK_NAME) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [TOGGLE_ATTRIBUTE]: {
                type: 'boolean',
                default: false
            },
            [SCALE_ATTRIBUTE]: {
                type: 'number',
                default: DEFAULT_SCALE
            }
        }
    };
});

/**
 * Add the "Scaled Image" toggle, and the scale slider it reveals, to the
 * core/image inspector.
 */
addFilter(
    'editor.BlockEdit',
    'dnte/image-scale-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (name !== BLOCK_NAME) {
                return <BlockEdit {...props} />;
            }

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorControls>
                        <PanelBody title={__('Image Scale', 'dentist-exchange')}>
                            <NativeToggleControl
                                label={__('Scaled Image', 'dentist-exchange')}
                                checked={!!attributes[TOGGLE_ATTRIBUTE]}
                                onChange={value => setAttributes({ [TOGGLE_ATTRIBUTE]: value })}
                            />
                            {attributes[TOGGLE_ATTRIBUTE] && (
                                <NativeRangeControl
                                    label={__('Scale', 'dentist-exchange')}
                                    value={attributes[SCALE_ATTRIBUTE] ?? DEFAULT_SCALE}
                                    onChange={value => setAttributes({ [SCALE_ATTRIBUTE]: value })}
                                    min={1}
                                    max={3}
                                    step={0.01}
                                    resetFallbackValue={DEFAULT_SCALE}
                                />
                            )}
                        </PanelBody>
                    </InspectorControls>
                </>
            );
        };
    })
);

/**
 * Apply the class and the scale custom property in the editor preview.
 */
addFilter(
    'editor.BlockListBlock',
    'dnte/image-scale-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes[TOGGLE_ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const wrapperProps = {
                ...props.wrapperProps,
                style: {
                    ...props.wrapperProps?.style,
                    '--dnte-image-scale': attributes[SCALE_ATTRIBUTE] ?? DEFAULT_SCALE
                }
            };

            const classes = [props.className, CLASS_NAME].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} wrapperProps={wrapperProps} />;
        };
    })
);
