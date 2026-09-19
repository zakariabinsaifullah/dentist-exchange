/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    withColors,
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients
} from '@wordpress/block-editor';

const OverlayBgControl = ( { clientId, attributes, setAttributes, overlayBgColor, setOverlayBgColor } ) => {
    const { overlayBgGradient } = attributes;

    const colorGradientSettings = useMultipleOriginColorsAndGradients();

    if ( ! colorGradientSettings.hasColorsOrGradients ) {
        return null;
    }

    const setting = {
        colorValue: overlayBgColor?.color,
        onColorChange: color => {
            setOverlayBgColor( color );
            // Clear gradient when a solid color is chosen.
            if ( color ) {
                setAttributes( { overlayBgGradient: undefined } );
            }
        },
        gradientValue: overlayBgGradient || undefined,
        onGradientChange: gradient => {
            // Clear solid color when a gradient is chosen.
            setAttributes( {
                overlayBgGradient: gradient || undefined,
                overlayBgColor: undefined,
                customOverlayBgColor: undefined
            } );
        },
        label: __( 'Overlay Background', 'dentist-exchange' ),
        isShownByDefault: true,
        enableAlpha: true,
        clearable: true,
        resetAllFilter: () => ( {
            overlayBgColor: undefined,
            customOverlayBgColor: undefined,
            overlayBgGradient: undefined
        } )
    };

    return (
        <ColorGradientSettingsDropdown
            __experimentalIsRenderedInSidebar
            settings={ [ setting ] }
            panelId={ clientId }
            { ...colorGradientSettings }
        />
    );
};

export default withColors( { overlayBgColor: 'background-color' } )( OverlayBgControl );
