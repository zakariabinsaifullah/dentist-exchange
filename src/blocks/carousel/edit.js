/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Internal Dependencies
 */
import './editor.scss';
import Inspector from './inspector';
import classNames from 'classnames';
import { RenderIcon } from '../../helpers';
import { generateBoxStyles, generateBorderWidth, generateBorderStyle, generateBorderColor } from '../../styles';
import { generateHeightStyles } from './utils';

// Block edit function
const Edit = props => {
    const { attributes, setAttributes, clientId, isSelected } = props;
    const {
        heightType,
        heights,
        vAligns,
        columns,
        gaps,
        resMode,
        showArrows,
        navType,
        paginationColor,
        npaginationHeight,
        apaginationHeight,
        pnSize,
        paSize,
        pRadius,
        paRadius,
        pgap,
        navColor,
        navbgColor,
        navBorderColor,
        navSize,
        navIconSize,
        navBorderRadius,
        navPadding,
        navBorder,
        navEdgeGap,
        navPosition,
        prevIconName,
        prevIconType,
        prevCustomSvg,
        nextIconName,
        nextIconType,
        nextCustomSvg
    } = attributes;
    // tab nav
    const navPaddingStyles = generateBoxStyles(navPadding);
    const navBorderWidth = generateBorderWidth(navBorder);
    const navBorderStyle = generateBorderStyle(navBorder);
    const navBorderColorValue = generateBorderColor(navBorder);
    const navBorderRadiusStyle = generateBoxStyles(navBorderRadius);

    // CSS Custom Properties
    const cssCustomProperties = {
        ...generateHeightStyles(heightType, heights, vAligns),
        ...(paginationColor && { '--pagination-color': paginationColor }),
        ...(apaginationHeight && { '--apagination-height': `${apaginationHeight}` }),
        ...(npaginationHeight && { '--npagination-height': `${npaginationHeight}` }),
        ...(pnSize && { '--psize': `${pnSize}` }),
        ...(paSize && { '--pasize': `${paSize}` }),
        ...(pRadius && { '--pradius': `${pRadius}` }),
        ...(paRadius && { '--paradius': `${paRadius}` }),
        ...(navBorderRadiusStyle && { '--nav-radius': navBorderRadiusStyle }),
        ...(navPaddingStyles && { '--nav-padding': navPaddingStyles }),
        ...(navBorderWidth && { '--nav-border-width': navBorderWidth }),
        ...(navBorderStyle && { '--nav-border-style': navBorderStyle }),
        ...(navBorderColorValue && { '--nborder-color': navBorderColorValue }),
        ...(!navBorderColorValue && navBorderColor && { '--nborder-color': navBorderColor }),
        ...(navSize && { '--nav-size': `${navSize}` }),
        ...(navIconSize && { '--nicon-size': `${navIconSize}` }),
        ...(navColor && { '--nav-color': navColor }),
        ...(navbgColor && { '--nav-bg': navbgColor }),
        ...(navEdgeGap && { '--nav-gap': `${navEdgeGap}` }),
        ...(pgap && { '--pgap': `${pgap}` })
    };

    // Update block style when CSS properties change
    useEffect(() => {
        setAttributes({
            blockStyle: cssCustomProperties
        });
    }, [
        heightType,
        heights,
        vAligns,
        navColor,
        navbgColor,
        paginationColor,
        navBorderRadius,
        navPadding,
        navBorder,
        npaginationHeight,
        apaginationHeight,
        pnSize,
        paSize,
        pRadius,
        paRadius,
        navBorderColor,
        navSize,
        navIconSize,
        navEdgeGap,
        pgap
    ]);

    // Inner blocks configuration
    const innerBlocksProps = useInnerBlocksProps(
        {
            className: classNames('dnte-editor-slides', {
                [`columns-${columns[resMode]}`]: columns[resMode],
                [`gap-${gaps[resMode]}`]: gaps[resMode]
            })
        },
        {
            allowedBlocks: ['dnte/slide'],
            template: [['dnte/slide'], ['dnte/slide']],
            templateLock: false
        }
    );

    // Block Props
    const blockProps = useBlockProps({
        style: cssCustomProperties,
        className: classNames({
            outside: navType === 'outside' && showArrows,
            [`nav-pos-${navPosition}`]: navPosition
        })
    });

    return (
        <>
            <BlockControls>
                <ToolbarGroup>
                    <ToolbarButton
                        icon="insert"
                        label={__('Add Slide', 'dentist-exchange')}
                        onClick={() => {
                            const innerBlocks = wp.data.select('core/block-editor').getBlocks(clientId);
                            const newBlock = wp.blocks.createBlock('dnte/slide');
                            wp.data.dispatch('core/block-editor').insertBlock(newBlock, innerBlocks.length, clientId);
                        }}
                    />
                </ToolbarGroup>
            </BlockControls>

            {isSelected && <Inspector {...props} />}

            <div {...blockProps}>
                <div {...innerBlocksProps} />
                {showArrows && (
                    <>
                        <div className="swiper-custom-prev dnte-nav">
                            <RenderIcon customSvgCode={prevCustomSvg} iconName={prevIconName} iconType={prevIconType} size={navIconSize} />
                        </div>
                        <div className="swiper-custom-next dnte-nav">
                            <RenderIcon customSvgCode={nextCustomSvg} iconName={nextIconName} iconType={nextIconType} size={navIconSize} />
                        </div>
                    </>
                )}
            </div>
        </>
    );
};

export default Edit;
