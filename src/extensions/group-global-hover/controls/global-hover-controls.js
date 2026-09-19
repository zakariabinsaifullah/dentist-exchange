/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    withColors,
    __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
    __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients
} from '@wordpress/block-editor';

const GlobalHoverControls = ( { clientId, globalHoverBgColor, setGlobalHoverBgColor, globalHoverColor, setGlobalHoverColor } ) => {
    const colorGradientSettings = useMultipleOriginColorsAndGradients();

    if ( ! colorGradientSettings.hasColorsOrGradients ) {
        return null;
    }

    const colorSettings = [
        {
            colorValue: globalHoverBgColor?.color,
            onColorChange: color => setGlobalHoverBgColor( color ),
            label: __( 'Hover Background', 'dentist-exchange' ),
            isShownByDefault: true,
            enableAlpha: true,
            clearable: true,
            resetAllFilter: () => ( {
                globalHoverBgColor: undefined,
                customGlobalHoverBgColor: undefined
            } )
        },
        {
            colorValue: globalHoverColor?.color,
            onColorChange: setGlobalHoverColor,
            label: __( 'Hover Color', 'dentist-exchange' ),
            isShownByDefault: true,
            enableAlpha: true,
            clearable: true,
            resetAllFilter: () => ( {
                globalHoverColor: undefined,
                customGlobalHoverColor: undefined
            } )
        }
    ];

    return (
        <>
            { colorSettings.map( setting => (
                <ColorGradientSettingsDropdown
                    key={ `global-hover-${ setting.label }` }
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

export default withColors( { globalHoverBgColor: 'background-color' }, { globalHoverColor: 'color' } )( GlobalHoverControls );
