/**
 * Kadence RowLayout — Torn Paper Dividers
 *
 * Adds "Enable top divider" and "Enable bottom divider" toggles to the
 * Advanced inspector panel of kadence/rowlayout. Each one paints the torn
 * paper strip across the full width of that edge of the row at a fixed
 * height.
 *
 * Modelled on src/extensions/kadence-featured-bg, including the pattern of
 * passing the asset URL through a CSS custom property rather than resolving
 * it in the bundle.
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorAdvancedControls } from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';

import { NativeToggleControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'kadence/rowlayout';
const TOP_ATTRIBUTE = 'enableTopDivider';
const BOTTOM_ATTRIBUTE = 'enableBottomDivider';

/**
 * Divider artwork URL, injected by dnte_enqueue_kadence_row_divider_editor_assets().
 * Only the editor preview needs it; the front end gets the same value from PHP.
 *
 * @return {string} Image URL, or '' when the asset is missing.
 */
const getDividerImage = () => window.dnteRowDivider?.image || '';

addFilter('blocks.registerBlockType', 'dnte/kadence-row-divider-add-attributes', (settings, name) => {
    if (name !== BLOCK_NAME) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [TOP_ATTRIBUTE]: {
                type: 'boolean',
                default: false
            },
            [BOTTOM_ATTRIBUTE]: {
                type: 'boolean',
                default: false
            }
        }
    };
});

addFilter(
    'editor.BlockEdit',
    'dnte/kadence-row-divider-add-inspector-controls',
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
                            label={__('Enable top divider', 'dentist-exchange')}
                            checked={!!attributes[TOP_ATTRIBUTE]}
                            onChange={value => setAttributes({ [TOP_ATTRIBUTE]: value })}
                        />
                        <NativeToggleControl
                            label={__('Enable bottom divider', 'dentist-exchange')}
                            checked={!!attributes[BOTTOM_ATTRIBUTE]}
                            onChange={value => setAttributes({ [BOTTOM_ATTRIBUTE]: value })}
                        />
                    </InspectorAdvancedControls>
                </>
            );
        };
    })
);

addFilter(
    'editor.BlockListBlock',
    'dnte/kadence-row-divider-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;
            const hasTop = !!attributes?.[TOP_ATTRIBUTE];
            const hasBottom = !!attributes?.[BOTTOM_ATTRIBUTE];

            if (name !== BLOCK_NAME || (!hasTop && !hasBottom)) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, hasTop && 'dnte-has-top-divider', hasBottom && 'dnte-has-bottom-divider']
                .filter(Boolean)
                .join(' ');

            const wrapperProps = {
                ...props.wrapperProps,
                style: {
                    ...props.wrapperProps?.style,
                    '--dnte-divider-image': `url(${getDividerImage()})`
                }
            };

            return <BlockListBlock {...props} className={classes} wrapperProps={wrapperProps} />;
        };
    })
);
