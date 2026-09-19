import { createHigherOrderComponent } from '@wordpress/compose';
import { addFilter } from '@wordpress/hooks';
import classnames from 'classnames';

import { softMinifyCssStrings, svgToBase64DataUrl } from '../../helpers';
import { allowedBlocks } from './allowed-blocks';

/**
 * Button Icon HOC - Updated to preserve existing classes
 */
const hangIconicButtonEditor = createHigherOrderComponent( BlockListBlock => {
    return props => {
        if ( ! allowedBlocks.includes( props.name ) ) {
            return <BlockListBlock { ...props } />;
        }

        const { attributes, clientId, className: existingClassName } = props;
        const {
            iconicButtonEnabled,
            iconicButtonIconName,
            iconicButtonCustomSvg,
            iconicButtonIcon,
            iconicButtonIconPosition,
            iconicButtonIconSize,
            iconicButtonIconGap
        } = attributes;

        if ( ! iconicButtonEnabled ) {
            return <BlockListBlock { ...props } />;
        }

        // Use custom SVG if available, or fallback to old attribute
        const iconSVG = iconicButtonCustomSvg || iconicButtonIcon;

        if ( ! iconSVG && ! iconicButtonIconName ) {
            return <BlockListBlock { ...props } />;
        }

        // unique class
        const uniqueClass = `dnte-icon-button-${ clientId.slice( 0, 8 ) }`;

        const btnIconClass = classnames( 'dnte-icon-button', uniqueClass, iconicButtonIconPosition );

        // Combine existing className with animation class
        const combinedClassName = existingClassName ? `${ existingClassName } ${ btnIconClass }` : btnIconClass;

        // If we have an SVG code, we use the mask approach
        let maskStyle = '';
        if ( iconSVG ) {
            if ( iconicButtonIconGap ) {
                maskStyle += `
                .dnte-icon-button.${ uniqueClass } .wp-block-button__link{
                    --dnte-icon-gap: ${ iconicButtonIconGap }!important;
                }`;
            }
            if ( iconicButtonIconSize ) {
                maskStyle += `
                .dnte-icon-button.${ uniqueClass } .wp-block-button__link::after{
                    --dnte-icon-size: ${ iconicButtonIconSize }!important;
                }`;
            }
            maskStyle += `
            .dnte-icon-button.${ uniqueClass } .wp-block-button__link::after{
                --dnte-icon-url: url("${ svgToBase64DataUrl( iconSVG ) }");
                display: inline-block;
            }`;
        }

        // Icon background + padding are intentionally not previewed here: the editor's
        // ::after is a CSS mask (its background-color paints the icon glyph itself), so
        // a background chip would need a second unmasked layer that never looks right
        // alongside the mask. Frontend rendering (style.scss + dnte_render_iconic_button)
        // uses a real <span> wrapping the SVG, where background/padding work correctly.

        return (
            <>
                { maskStyle && <style>{ softMinifyCssStrings( maskStyle ) }</style> }
                <BlockListBlock { ...props } className={ combinedClassName } />
            </>
        );
    };
}, 'hangIconicButtonEditor' );

addFilter( 'editor.BlockListBlock', 'dnte/iconic-button-editor', hangIconicButtonEditor );
