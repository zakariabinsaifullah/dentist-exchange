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

const BLOCK_NAME = 'core/button';
const ATTRIBUTE = 'fullWidthMobile';
const CLASS_NAME = 'has-full-width-mobile';

addFilter('blocks.registerBlockType', 'dnte/button-full-width-mobile-add-attribute', (settings, name) => {
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

addFilter(
    'editor.BlockEdit',
    'dnte/button-full-width-mobile-add-inspector-controls',
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
                                label={__('Full Width on mobile', 'dentist-exchange')}
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

addFilter(
    'editor.BlockListBlock',
    'dnte/button-full-width-mobile-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes[ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, CLASS_NAME].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} />;
        };
    })
);