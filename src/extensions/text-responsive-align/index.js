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
import { NativeResponsiveControl, NativeToggleGroupControl } from '../../components';

import './style.scss';

const SUPPORTED_BLOCKS = ['core/heading', 'core/paragraph'];
const ATTRIBUTE = 'responsiveTextAlign';

// Desktop keeps using core's own `textAlign`, so only tablet and mobile are stored here.
const DEVICE_CLASS_PREFIX = {
    Tablet: 'has-text-align-tablet-',
    Mobile: 'has-text-align-mobile-'
};

const ALIGNMENTS = [
    { label: __('Default', 'dentist-exchange'), value: '' },
    { label: __('Left', 'dentist-exchange'), value: 'left' },
    { label: __('Center', 'dentist-exchange'), value: 'center' },
    { label: __('Right', 'dentist-exchange'), value: 'right' }
];

/**
 * Alignment to show for a device: tablet and mobile inherit from the next larger
 * screen until they are given an alignment of their own.
 */
const resolveAlign = (device, textAlign, aligns) => {
    if ('Tablet' === device) {
        return aligns?.Tablet ?? textAlign ?? '';
    }

    if ('Mobile' === device) {
        return aligns?.Mobile ?? aligns?.Tablet ?? textAlign ?? '';
    }

    return textAlign ?? '';
};

// Only breakpoints with an explicit alignment get a class; the rest keep the larger one's.
const getAlignClasses = aligns =>
    Object.entries(DEVICE_CLASS_PREFIX)
        .map(([device, prefix]) => (aligns?.[device] ? prefix + aligns[device] : ''))
        .filter(Boolean);

addFilter('blocks.registerBlockType', 'dnte/text-responsive-align-add-attribute', (settings, name) => {
    if (!SUPPORTED_BLOCKS.includes(name)) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            resMode: {
                type: 'string',
                default: 'Desktop'
            },
            [ATTRIBUTE]: {
                type: 'object',
                default: {}
            }
        }
    };
});

addFilter(
    'editor.BlockEdit',
    'dnte/text-responsive-align-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (!SUPPORTED_BLOCKS.includes(name)) {
                return <BlockEdit {...props} />;
            }

            const { resMode, textAlign } = attributes;
            const aligns = attributes[ATTRIBUTE];

            // Desktop writes core's `textAlign` so the block toolbar stays in sync.
            const onChange = align => {
                if ('Desktop' === resMode) {
                    setAttributes({ textAlign: align || undefined });
                    return;
                }

                setAttributes({ [ATTRIBUTE]: { ...aligns, [resMode]: align || undefined } });
            };

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorControls>
                        <PanelBody title={__('Responsive Alignment', 'dentist-exchange')} initialOpen={false}>
                            <NativeResponsiveControl label={__('Text Alignment', 'dentist-exchange')} props={props}>
                                <NativeToggleGroupControl
                                    value={resolveAlign(resMode, textAlign, aligns)}
                                    onChange={onChange}
                                    options={ALIGNMENTS}
                                    help={
                                        'Desktop' !== resMode && undefined === aligns?.[resMode]
                                            ? __(
                                                  'Inherited from the larger screen size. Change it to set an alignment just for this device.',
                                                  'dentist-exchange'
                                              )
                                            : undefined
                                    }
                                />
                            </NativeResponsiveControl>
                        </PanelBody>
                    </InspectorControls>
                </>
            );
        };
    })
);

addFilter(
    'editor.BlockListBlock',
    'dnte/text-responsive-align-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (!SUPPORTED_BLOCKS.includes(name)) {
                return <BlockListBlock {...props} />;
            }

            const alignClasses = getAlignClasses(attributes[ATTRIBUTE]);

            if (!alignClasses.length) {
                return <BlockListBlock {...props} />;
            }

            const classes = [props.className, ...alignClasses].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} />;
        };
    })
);
