/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { NativeSelectControl, NativeToggleControl } from '../../components';

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const {
        cardWidth,
        laneGap,
        mobileGap,
        animate,
        revealStagger,
        trailingArrow,
        trailingLeft,
        trailingOffset,
        trailingWidth,
        trailingDelay,
        trailingFlipX
    } = attributes;

    const arrows = window.dnteArrows || [];
    const arrowOptions = [
        { label: __('None', 'dentist-exchange'), value: '' },
        ...arrows.map(item => ({ label: item.label, value: item.slug }))
    ];

    return (
        <InspectorControls>
            <PanelBody title={__('Layout', 'dentist-exchange')} initialOpen={true}>
                <RangeControl
                    label={__('Card Width (%)', 'dentist-exchange')}
                    value={cardWidth}
                    onChange={value => setAttributes({ cardWidth: value ?? 40 })}
                    min={25}
                    max={70}
                    step={1}
                    help={__(
                        'Share of the section each card takes. The two lanes sit either side of the gutter the arrows live in.',
                        'dentist-exchange'
                    )}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
                <RangeControl
                    label={__('Gap Between Cards (px)', 'dentist-exchange')}
                    value={laneGap}
                    onChange={value => setAttributes({ laneGap: value ?? 0 })}
                    min={0}
                    max={200}
                    step={4}
                    help={__(
                        'Baseline spacing before each card’s own Vertical Offset is applied. The design needs none — the offsets carry the whole stagger.',
                        'dentist-exchange'
                    )}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
                <RangeControl
                    label={__('Gap on Mobile (px)', 'dentist-exchange')}
                    value={mobileGap}
                    onChange={value => setAttributes({ mobileGap: value ?? 70 })}
                    min={0}
                    max={160}
                    step={2}
                    help={__('Spacing between the stacked cards below 900px, where the offsets no longer apply.', 'dentist-exchange')}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
            </PanelBody>

            <PanelBody title={__('Trailing Arrow', 'dentist-exchange')} initialOpen={false}>
                <NativeSelectControl
                    label={__('Arrow', 'dentist-exchange')}
                    value={trailingArrow}
                    onChange={value => setAttributes({ trailingArrow: value })}
                    options={arrowOptions}
                    help={__(
                        'The long curve running out of the bottom of the section. It belongs to the section rather than to a card, and it is the one arrow kept on a phone.',
                        'dentist-exchange'
                    )}
                />

                {trailingArrow && (
                    <>
                        <RangeControl
                            label={__('Position — Left (%)', 'dentist-exchange')}
                            value={trailingLeft}
                            onChange={value => setAttributes({ trailingLeft: value ?? 41 })}
                            min={-40}
                            max={140}
                            step={1}
                            __next40pxDefaultSize
                            __nextHasNoMarginBottom
                        />
                        <RangeControl
                            label={__('Position — From Bottom (%)', 'dentist-exchange')}
                            value={trailingOffset}
                            onChange={value => setAttributes({ trailingOffset: value ?? -13 })}
                            min={-60}
                            max={40}
                            step={1}
                            help={__('Negative pulls the arrow up over the last card.', 'dentist-exchange')}
                            __next40pxDefaultSize
                            __nextHasNoMarginBottom
                        />
                        <RangeControl
                            label={__('Width (%)', 'dentist-exchange')}
                            value={trailingWidth}
                            onChange={value => setAttributes({ trailingWidth: value ?? 20 })}
                            min={4}
                            max={80}
                            step={1}
                            __next40pxDefaultSize
                            __nextHasNoMarginBottom
                        />
                        <NativeToggleControl
                            label={__('Flip Horizontally', 'dentist-exchange')}
                            checked={trailingFlipX}
                            onChange={value => setAttributes({ trailingFlipX: value })}
                            help={__('Mirrors the curve for compositions that run the other way.', 'dentist-exchange')}
                        />
                        <RangeControl
                            label={__('Draw Delay (ms)', 'dentist-exchange')}
                            value={trailingDelay}
                            onChange={value => setAttributes({ trailingDelay: value ?? 180 })}
                            min={0}
                            max={1200}
                            step={10}
                            __next40pxDefaultSize
                            __nextHasNoMarginBottom
                        />
                    </>
                )}
            </PanelBody>

            <PanelBody title={__('Scroll Animation', 'dentist-exchange')} initialOpen={false}>
                <NativeToggleControl
                    label={__('Reveal on Scroll', 'dentist-exchange')}
                    checked={animate}
                    onChange={value => setAttributes({ animate: value })}
                    help={__(
                        'Cards fade and rise into place one at a time, each arrow drawing in behind its card. Frontend only.',
                        'dentist-exchange'
                    )}
                />
                {animate && (
                    <RangeControl
                        label={__('Minimum Gap Between Cards (ms)', 'dentist-exchange')}
                        value={revealStagger}
                        onChange={value => setAttributes({ revealStagger: value ?? 120 })}
                        min={0}
                        max={600}
                        step={10}
                        help={__(
                            'Holds cards apart when several enter the screen at once, so a fast scroll still reveals them in order rather than together.',
                            'dentist-exchange'
                        )}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />
                )}
            </PanelBody>
        </InspectorControls>
    );
};

export default Inspector;
