/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    withColors,
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients
} from '@wordpress/block-editor';

const HoverColorsControls = ( {
    clientId,
    hoverTextColor,
    hoverBackgroundColor,
    hoverBorderColor,
    setHoverTextColor,
    setHoverBackgroundColor,
    setHoverBorderColor
} ) => {
    const colorGradientSettings = useMultipleOriginColorsAndGradients();

    if ( ! colorGradientSettings.hasColorsOrGradients ) {
        return null;
    }

    const colorSettings = [
        {
            colorValue: hoverTextColor?.color,
            onColorChange: setHoverTextColor,
            isShownByDefault: false,
            label: __( 'Hover Text', 'dentist-exchange' ),
            resetAllFilter: () => ( {
                hoverTextColor: undefined,
                customHoverTextColor: undefined
            } )
        },
        {
            colorValue: hoverBackgroundColor?.color,
            onColorChange: setHoverBackgroundColor,
            isShownByDefault: false,
            label: __( 'Hover Background', 'dentist-exchange' ),
            resetAllFilter: () => ( {
                hoverBackgroundColor: undefined,
                customHoverBackgroundColor: undefined
            } )
        },
        {
            colorValue: hoverBorderColor?.color,
            onColorChange: setHoverBorderColor,
            isShownByDefault: false,
            label: __( 'Hover Border', 'dentist-exchange' ),
            resetAllFilter: () => ( {
                hoverBorderColor: undefined,
                customHoverBorderColor: undefined
            } )
        }
    ];

    return (
        <>
            { colorSettings.map( setting => (
                <ColorGradientSettingsDropdown
                    key={ `hover-color-${ setting.label }` }
                    __experimentalIsRenderedInSidebar
                    settings={ [ setting ] }
                    panelId={ clientId }
                    { ...colorGradientSettings }
                    gradients={ [] }
                    disableCustomGradients
                />
            ) ) }
        </>
    );
};

export default withColors(
    { hoverTextColor: 'color' },
    { hoverBackgroundColor: 'background-color' },
    { hoverBorderColor: 'border-color' }
)( HoverColorsControls );
