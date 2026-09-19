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
import { NativeToggleControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'core/group';
const ATTRIBUTE = 'forceFullHeight';
const CLASS_NAME = 'has-force-full-height';

/**
 * Add `forceFullHeight` attribute to core/group.
 */
addFilter('blocks.registerBlockType', 'dnte/group-full-height-add-attribute', (settings, name) => {
    if (name !== BLOCK_NAME) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [ATTRIBUTE]: {
                type: 'boolean',
                default: false
            }
        }
    };
});

/**
 * Add "Force full height" toggle to the core/group inspector (Dimensions panel).
 */
addFilter(
    'editor.BlockEdit',
    'dnte/group-full-height-add-inspector-controls',
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
                        <PanelBody>
                            <NativeToggleControl
                                label={__('Force full height', 'gl-layout-builder')}
                                checked={!!attributes[ATTRIBUTE]}
                                onChange={value => setAttributes({ [ATTRIBUTE]: value })}
                            />
                        </PanelBody>
                    </InspectorControls>
                </>
            );
        };
    })
);

/**
 * Apply the class and inline style in the editor preview.
 */
addFilter(
    'editor.BlockListBlock',
    'dnte/group-full-height-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes[ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const wrapperProps = {
                ...props.wrapperProps,
                style: {
                    ...props.wrapperProps?.style,
                    height: '100%'
                }
            };

            const classes = [props.className, CLASS_NAME].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} wrapperProps={wrapperProps} />;
        };
    })
);
