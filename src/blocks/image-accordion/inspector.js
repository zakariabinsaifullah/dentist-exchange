/**
 * WordPress dependencies
 */
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { NativeToggleControl, NativeSelectControl, NativeResponsiveControl, NativeUnitControl } from '../../components';

const HEADING_TAGS = [
    { label: __('H1', 'dentist-exchange'), value: 'h1' },
    { label: __('H2', 'dentist-exchange'), value: 'h2' },
    { label: __('H3', 'dentist-exchange'), value: 'h3' },
    { label: __('H4', 'dentist-exchange'), value: 'h4' },
    { label: __('H5', 'dentist-exchange'), value: 'h5' },
    { label: __('H6', 'dentist-exchange'), value: 'h6' },
    { label: __('Paragraph', 'dentist-exchange'), value: 'p' },
    { label: __('Div', 'dentist-exchange'), value: 'div' }
];

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const { showTitle, showDesc, showBtn, titleTag, itemsGap, resMode } = attributes;

    return (
        <InspectorControls>
            <PanelBody title={__('Settings', 'dentist-exchange')} initialOpen={true}>
                <NativeToggleControl
                    label={__('Show Title', 'dentist-exchange')}
                    checked={showTitle}
                    onChange={value => setAttributes({ showTitle: value })}
                />
                <NativeToggleControl
                    label={__('Show Description', 'dentist-exchange')}
                    checked={showDesc}
                    onChange={value => setAttributes({ showDesc: value })}
                />
                <NativeToggleControl
                    label={__('Show Button', 'dentist-exchange')}
                    checked={showBtn}
                    onChange={value => setAttributes({ showBtn: value })}
                />
                {showTitle && (
                    <NativeSelectControl
                        label={__('Select Title Tag', 'dentist-exchange')}
                        value={titleTag}
                        onChange={value => setAttributes({ titleTag: value })}
                        options={HEADING_TAGS}
                    />
                )}
                <NativeResponsiveControl label={__('Items Gap', 'dentist-exchange')} props={props}>
                    <NativeUnitControl
                        value={itemsGap?.[resMode]}
                        onChange={value => setAttributes({ itemsGap: { ...itemsGap, [resMode]: value } })}
                    />
                </NativeResponsiveControl>
            </PanelBody>
        </InspectorControls>
    );
};

export default Inspector;
