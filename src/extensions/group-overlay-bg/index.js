/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { createHigherOrderComponent } from '@wordpress/compose';

/**
 * Internal dependencies
 */
import OverlayBgControl from './controls/overlay-bg-control';

import './style.scss';

const BLOCK_NAME = 'core/group';

/**
 * Add overlay background attributes to core/group.
 */
addFilter( 'blocks.registerBlockType', 'dnte/group-overlay-bg-add-attributes', ( settings, name ) => {
    if ( name !== BLOCK_NAME ) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            overlayBgColor: {
                type: 'string'
            },
            customOverlayBgColor: {
                type: 'string'
            },
            overlayBgGradient: {
                type: 'string'
            }
        }
    };
} );

/**
 * Add "Overlay Background" control to core/group inspector (Color panel).
 */
addFilter(
    'editor.BlockEdit',
    'dnte/group-overlay-bg-add-inspector-controls',
    createHigherOrderComponent( BlockEdit => {
        return props => {
            const { name, attributes, setAttributes, clientId } = props;

            if ( name !== BLOCK_NAME ) {
                return <BlockEdit { ...props } />;
            }

            return (
                <>
                    <BlockEdit { ...props } />
                    <InspectorControls group="color">
                        <OverlayBgControl attributes={ attributes } setAttributes={ setAttributes } clientId={ clientId } />
                    </InspectorControls>
                </>
            );
        };
    } )
);

/**
 * Apply `dnte-overlay-bg` class and `--dnte-overlay-bg` CSS variable in the editor preview.
 */
addFilter(
    'editor.BlockListBlock',
    'dnte/group-overlay-bg-add-styles',
    createHigherOrderComponent( BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if ( name !== BLOCK_NAME ) {
                return <BlockListBlock { ...props } />;
            }

            const { overlayBgColor, customOverlayBgColor, overlayBgGradient } = attributes;

            const hasOverlay = overlayBgColor || customOverlayBgColor || overlayBgGradient;

            if ( ! hasOverlay ) {
                return <BlockListBlock { ...props } />;
            }

            const bgValue = overlayBgGradient
                ? overlayBgGradient
                : overlayBgColor
                ? `var(--wp--preset--color--${ overlayBgColor })`
                : customOverlayBgColor || '';

            const wrapperProps = {
                ...props.wrapperProps,
                style: {
                    ...props.wrapperProps?.style,
                    '--dnte-overlay-bg': bgValue
                }
            };

            const classes = [ props.className, 'dnte-overlay-bg' ].filter( Boolean ).join( ' ' );

            return <BlockListBlock { ...props } className={ classes } wrapperProps={ wrapperProps } />;
        };
    } )
);
