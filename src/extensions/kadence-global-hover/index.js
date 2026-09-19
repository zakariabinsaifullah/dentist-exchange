/**
 * Kadence Column — Global Hover Effect
 *
 * Adds an "Enable Hover Gradient Border" toggle to the advanced inspector
 * controls of kadence/column blocks. When enabled, a `global-hover` class is
 * added to the block's container so it can be targeted for hover styling.
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorAdvancedControls } from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';

import { NativeToggleControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'kadence/column';
const ATTRIBUTE = 'globalHoverEffect';

/**
 * Add the globalHoverEffect attribute to kadence/column.
 */
addFilter('blocks.registerBlockType', 'dnte/kadence-global-hover-add-attribute', (settings, name) => {
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
 * Add "Enable Hover Gradient Border" toggle to the kadence/column advanced inspector.
 */
addFilter(
    'editor.BlockEdit',
    'dnte/kadence-global-hover-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (name !== BLOCK_NAME) {
                return <BlockEdit {...props} />;
            }

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorAdvancedControls>
                        <NativeToggleControl
                            label={__('Enable Hover Gradient Border', 'dentist-exchange')}
                            checked={!!attributes[ATTRIBUTE]}
                            onChange={value => setAttributes({ [ATTRIBUTE]: value })}
                        />
                    </InspectorAdvancedControls>
                </>
            );
        };
    })
);

/**
 * Apply the `global-hover` class in the editor preview.
 */
addFilter(
    'editor.BlockListBlock',
    'dnte/kadence-global-hover-add-class',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes[ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, 'global-hover'].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} />;
        };
    })
);
