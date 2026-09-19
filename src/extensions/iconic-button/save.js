import { addFilter } from '@wordpress/hooks';
import { allowedBlocks } from './allowed-blocks';

/**
 * Add iconic button classes to saved block HTML for the frontend.
 *
 * @param {Object} props      Block save props.
 * @param {Object} blockType  Block type definition.
 * @param {Object} attributes Block attributes.
 * @return {Object} Modified props.
 */
function addIconicButtonSaveProps( props, blockType, attributes ) {
    if ( ! allowedBlocks.includes( blockType.name ) ) {
        return props;
    }

    const { iconicButtonEnabled, iconicButtonUniqueClass, iconicButtonIconPosition } = attributes;

    if ( ! iconicButtonEnabled || ! iconicButtonUniqueClass ) {
        return props;
    }

    const classes = [ 'dnte-icon-button', iconicButtonUniqueClass ];
    if ( iconicButtonIconPosition !== '' ) {
        classes.push( 'dnte-icon-before' );
    }

    props.className = ( ( props.className || '' ) + ' ' + classes.join( ' ' ) ).trim();
    return props;
}

addFilter( 'blocks.getSaveContent.extraProps', 'dnte/iconic-button-save-props', addIconicButtonSaveProps );
