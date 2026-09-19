/**
 * WordPress dependencies
 */

import { addFilter } from '@wordpress/hooks';
import { allowedBlocks } from './allowed-blocks';

/**
 * Add Iconic Button attribute to block settings
 *
 * @param {Object} settings Block settings
 * @param {string} name     Block name
 * @return {Object} Modified settings
 */

function hangIconicButtonAttribute( settings, name ) {
    // Check if the block is in the allowed list
    if ( ! allowedBlocks.includes( name ) ) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            iconicButtonEnabled: {
                type: 'boolean',
                default: false
            },
            iconicButtonIconName: {
                type: 'string',
                default: ''
            },
            iconicButtonIcon: {
                type: 'string',
                default: ''
            },
            iconicButtonIconType: {
                type: 'string',
                default: ''
            },
            iconicButtonCustomSvg: {
                type: 'string',
                default: ''
            },
            iconicButtonIconPosition: {
                type: 'string',
                default: ''
            },
            iconicButtonIconSize: {
                type: 'string'
            },
            iconicButtonIconGap: {
                type: 'string'
            },
            iconicButtonUniqueClass: {
                type: 'string',
                default: ''
            },
            iconicButtonIconBgColor: {
                type: 'string'
            },
            iconicButtonIconPadding: {
                type: 'string'
            }
        }
    };
}

addFilter( 'blocks.registerBlockType', 'dnte/iconic-button-attribute', hangIconicButtonAttribute );
