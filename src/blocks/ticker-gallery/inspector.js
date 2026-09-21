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
    const { tallHeight, shortRatio, gap, radius, speed, direction, pauseOnHover, images } = attributes;

    return (
        <InspectorControls>
            <PanelBody title={__('Ticker', 'dentist-exchange')} initialOpen={true}>
                <NativeSelectControl
                    label={__('Direction', 'dentist-exchange')}
                    value={direction}
                    onChange={value => setAttributes({ direction: value })}
                    options={[
                        { label: __('Left', 'dentist-exchange'), value: 'left' },
                        { label: __('Right', 'dentist-exchange'), value: 'right' }
                    ]}
                />
                <RangeControl
                    label={__('Duration (seconds)', 'dentist-exchange')}
                    value={speed}
                    onChange={value => setAttributes({ speed: value })}
                    min={5}
                    max={180}
                    step={1}
                    help={__('How long one full pass takes. Higher is slower.', 'dentist-exchange')}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
                <NativeToggleControl
                    label={__('Pause on hover', 'dentist-exchange')}
                    checked={pauseOnHover}
                    onChange={value => setAttributes({ pauseOnHover: value })}
                />
            </PanelBody>

            <PanelBody title={__('Layout', 'dentist-exchange')} initialOpen={false}>
                <RangeControl
                    label={__('Tall image height (px)', 'dentist-exchange')}
                    value={tallHeight}
                    onChange={value => setAttributes({ tallHeight: value })}
                    min={120}
                    max={600}
                    step={4}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
                <RangeControl
                    label={__('Short image ratio', 'dentist-exchange')}
                    value={shortRatio}
                    onChange={value => setAttributes({ shortRatio: value })}
                    min={0.5}
                    max={1}
                    step={0.01}
                    help={__('Height of the tilted images relative to the tall ones.', 'dentist-exchange')}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
                <RangeControl
                    label={__('Gap (px)', 'dentist-exchange')}
                    value={gap}
                    onChange={value => setAttributes({ gap: value })}
                    min={0}
                    max={120}
                    step={2}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
                <RangeControl
                    label={__('Corner radius (px)', 'dentist-exchange')}
                    value={radius}
                    onChange={value => setAttributes({ radius: value })}
                    min={0}
                    max={64}
                    step={1}
                    __next40pxDefaultSize
                    __nextHasNoMarginBottom
                />
                {images?.length > 0 && images.length % 4 !== 0 && (
                    <p style={{ color: '#646970', fontSize: '12px', marginTop: '12px' }}>
                        {__(
                            'The tilt pattern repeats every 4 images. With a multiple of 4 the rhythm never repeats a shape back to back.',
                            'dentist-exchange'
                        )}
                    </p>
                )}
            </PanelBody>
        </InspectorControls>
    );
};

export default Inspector;
