/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { NativeRangeControl } from '../../components';

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const { stackRotate, stackOffsetX, stackOffsetY, stackZIndex } = attributes;

    return (
        <InspectorControls>
            <PanelBody title={__('Position', 'dentist-exchange')} initialOpen={true}>
                <NativeRangeControl
                    label={__('Rotate (deg)', 'dentist-exchange')}
                    value={stackRotate}
                    onChange={value => setAttributes({ stackRotate: value })}
                    min={-45}
                    max={45}
                    step={1}
                />
                <NativeRangeControl
                    label={__('Offset X (px)', 'dentist-exchange')}
                    value={stackOffsetX}
                    onChange={value => setAttributes({ stackOffsetX: value })}
                    min={-300}
                    max={300}
                    step={5}
                />
                <NativeRangeControl
                    label={__('Offset Y (px)', 'dentist-exchange')}
                    value={stackOffsetY}
                    onChange={value => setAttributes({ stackOffsetY: value })}
                    min={-300}
                    max={300}
                    step={5}
                />
                <NativeRangeControl
                    label={__('Z-Index', 'dentist-exchange')}
                    value={stackZIndex}
                    onChange={value => setAttributes({ stackZIndex: value })}
                    min={0}
                    max={20}
                    step={1}
                />
            </PanelBody>
        </InspectorControls>
    );
};

export default Inspector;
