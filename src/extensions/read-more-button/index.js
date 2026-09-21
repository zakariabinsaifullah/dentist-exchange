/**
 * core/read-more — theme button styling, with an optional arrow.
 *
 * The block is dynamic, so the classes that do the work are added on the
 * server by dnte_render_read_more_button(). This file adds the toggle and
 * mirrors the same classes onto the editor preview, which the render filter
 * never reaches.
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';

import { NativeToggleControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'core/read-more';
const ATTRIBUTE = 'showArrow';

addFilter('blocks.registerBlockType', 'dnte/read-more-button-add-attribute', (settings, name) => {
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
    'dnte/read-more-button-add-inspector-controls',
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
                        <PanelBody title={__('Button', 'dentist-exchange')} initialOpen={false}>
                            <NativeToggleControl
                                label={__('Show arrow icon', 'dentist-exchange')}
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
    'dnte/read-more-button-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, 'wp-element-button', attributes?.[ATTRIBUTE] && 'dnte-has-arrow'].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} />;
        };
    })
);
