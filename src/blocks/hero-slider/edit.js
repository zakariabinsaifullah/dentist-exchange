/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    useInnerBlocksProps,
    BlockControls,
    MediaUpload,
    MediaUploadCheck,
    MediaPlaceholder
} from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton, Placeholder, Button } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { createBlock } from '@wordpress/blocks';

/**
 * Internal Dependencies
 */
import './editor.scss';
import classNames from 'classnames';
import Inspector from './inspector';
import { RenderIcon } from '../../helpers';
import { generateBoxStyles, generateBorderWidth, generateBorderStyle, generateBorderColor } from '../../styles';
import { generateHeroStyles, generateResponsiveVars, mergeSelection, positionMargins, slideThumb } from './utils';

/**
 * The mockup's copy, so a freshly inserted block already reads like the design
 * instead of an empty box.
 */
const TEMPLATE = [
    ['core/heading', { level: 1, content: __('The financial foundation your mission is built on.', 'dentist-exchange') }],
    ['core/paragraph', { content: __('Audit, accounting, and tax. Built for nonprofits alone.', 'dentist-exchange') }],
    ['core/buttons', {}, [['core/button', { text: __("Let's Connect", 'dentist-exchange') }]]]
];

/* Per-slide mode holds nothing but slides; each carries its own content. */
const SLIDE_BLOCK = 'dnte/hero-slide';
const ALLOWED_WITH_SLIDES = [SLIDE_BLOCK];

/* How many empty slides to lay out when per-slide mode is first chosen. */
const DEFAULT_SLIDE_COUNT = 3;

const Edit = props => {
    const { attributes, setAttributes, isSelected, clientId } = props;
    const {
        slides = [],
        contentMode,
        blockStyle,
        heightType,
        heights,
        vAligns,
        overlayColor,
        overlayGradient,
        overlayOpacity,
        contentWidths,
        contentAlign,
        contentPosition,
        contentPadding,
        constrainThumbs,
        showThumbs,
        thumbWidths,
        thumbHeights,
        thumbGaps,
        thumbsAlign,
        thumbsOffset,
        thumbRadius,
        thumbOpacity,
        thumbBorderColor,
        thumbBorderWidth,
        showArrows,
        navColor,
        navbgColor,
        navSize,
        navIconSize,
        navGap,
        navBorder,
        navBorderRadius,
        navPadding,
        prevIconName,
        prevIconType,
        prevCustomSvg,
        nextIconName,
        nextIconType,
        nextCustomSvg
    } = attributes;

    const [activeIndex, setActiveIndex] = useState(0);
    const deviceType = useSelect(select => select('core/editor')?.getDeviceType(), []);

    const { innerCount, slideCount } = useSelect(
        select => {
            const inner = select('core/block-editor').getBlocks(clientId);

            return {
                innerCount: inner.length,
                slideCount: inner.filter(block => SLIDE_BLOCK === block.name).length
            };
        },
        [clientId]
    );

    const { insertBlocks, replaceInnerBlocks } = useDispatch('core/block-editor');

    /**
     * A slider saved before the mode existed has no `contentMode`, and one that
     * already holds images was plainly built as a fixed hero — either way it
     * should carry on rather than be sent back to the placeholder.
     */
    const mode = contentMode || (slides.length ? 'fixed' : '');
    const perSlide = 'per-slide' === mode;

    const chooseMode = next => {
        setAttributes({ contentMode: next });

        // Per-slide mode is meaningless empty, so it arrives ready to fill in.
        if ('per-slide' !== next || slideCount) {
            return;
        }

        const newSlides = Array.from({ length: DEFAULT_SLIDE_COUNT }, () => createBlock(SLIDE_BLOCK));

        /*
         * `replaceInnerBlocks` rather than `insertBlocks` while the slider is
         * still empty. Until its inner-block list settings are registered — which
         * only happens once the inner blocks actually render — `canInsertBlockType`
         * rejects a child that declares a `parent`, and `insertBlocks` fails
         * silently. Replacing skips that check; once there are blocks to append
         * to, the settings exist and inserting behaves.
         */
        if (innerCount) {
            insertBlocks(newSlides, undefined, clientId, false);
        } else {
            replaceInnerBlocks(clientId, newSlides, false);
        }
    };

    // Removing images can leave the preview pointing past the end of the list.
    useEffect(() => {
        if (activeIndex > slides.length - 1) {
            setActiveIndex(0);
        }
    }, [slides.length]);

    const navPaddingStyles = generateBoxStyles(navPadding);
    const navBorderWidth = generateBorderWidth(navBorder);
    const navBorderStyle = generateBorderStyle(navBorder);
    const navBorderColorValue = generateBorderColor(navBorder);
    const navBorderRadiusStyle = generateBoxStyles(navBorderRadius);
    const contentPaddingStyles = generateBoxStyles(contentPadding);
    const [contentMl, contentMr] = positionMargins(contentPosition);

    // CSS Custom Properties
    const cssCustomProperties = {
        ...generateHeroStyles(heightType, heights, vAligns),
        ...generateResponsiveVars(contentWidths, 'content-w'),
        ...generateResponsiveVars(thumbWidths, 'thumb-w'),
        ...generateResponsiveVars(thumbHeights, 'thumb-h'),
        ...generateResponsiveVars(thumbGaps, 'thumb-gap', 'px'),

        /* The stylesheet works the strip's width out from this — see style.scss. */
        '--thumb-count': `${slides.length || 1}`,
        ...(overlayColor && { '--hero-overlay-color': overlayColor }),
        ...(overlayGradient && { '--hero-overlay-image': overlayGradient }),
        ...(overlayOpacity !== undefined && { '--hero-overlay-opacity': `${overlayOpacity / 100}` }),
        ...(contentPaddingStyles && { '--content-padding': contentPaddingStyles }),

        /* Auto margins place the limited column; see `positionMargins`. */
        '--content-ml': contentMl,
        '--content-mr': contentMr,

        /* Opting out lets the strip run the full width while the copy stays capped. */
        ...(constrainThumbs === false && { '--thumbs-max': 'none' }),
        ...(thumbRadius && { '--thumb-radius': `${thumbRadius}` }),
        ...(thumbOpacity !== undefined && { '--thumb-opacity': `${thumbOpacity / 100}` }),
        ...(thumbBorderColor && { '--thumb-border-color': thumbBorderColor }),
        ...(thumbBorderWidth && { '--thumb-border-width': `${thumbBorderWidth}` }),
        ...(thumbsOffset && { '--thumbs-offset': `${thumbsOffset}` }),
        ...(navBorderRadiusStyle && { '--nav-radius': navBorderRadiusStyle }),
        ...(navPaddingStyles && { '--nav-padding': navPaddingStyles }),
        ...(navBorderWidth && { '--nav-border-width': navBorderWidth }),
        ...(navBorderStyle && { '--nav-border-style': navBorderStyle }),
        ...(navBorderColorValue && { '--nborder-color': navBorderColorValue }),
        ...(navSize && { '--nav-size': `${navSize}` }),
        ...(navIconSize && { '--nicon-size': `${navIconSize}` }),
        ...(navColor && { '--nav-color': navColor }),
        ...(navbgColor && { '--nav-bg': navbgColor }),
        ...(navGap && { '--nav-gap': `${navGap}` })
    };

    /**
     * Mirror the resolved properties onto an attribute so `save.js` can emit them.
     *
     * Comparing the serialised form rather than listing every source attribute as
     * a dependency keeps this honest: `setAttributes` only fires when a property
     * genuinely changed, so merely opening a post no longer marks it as dirty.
     * The key order is stable because the object is always built the same way.
     */
    const serializedStyle = JSON.stringify(cssCustomProperties);

    useEffect(() => {
        if (JSON.stringify(blockStyle) !== serializedStyle) {
            setAttributes({ blockStyle: cssCustomProperties });
        }
    }, [serializedStyle]);

    const hasThumbs = showThumbs && slides.length > 1;
    const hasArrows = showArrows && slides.length > 1;
    const hasOverlay = !!overlayColor || !!overlayGradient;
    const activeSlide = slides[activeIndex];

    const onSelectImages = images => {
        setAttributes({ slides: mergeSelection(images, slides) });
    };

    /**
     * Follow the editor's own device preview so a mobile-only image can be
     * checked without leaving the canvas.
     */
    const previewUrl = activeSlide && (deviceType === 'Mobile' ? activeSlide.mobileUrl || activeSlide.url : activeSlide.url);

    const step = direction => {
        const next = (activeIndex + direction + slides.length) % slides.length;
        setActiveIndex(next);
    };

    const blockProps = useBlockProps({
        style: cssCustomProperties,
        className: classNames(`content-${contentAlign || 'left'}`, `thumbs-${thumbsAlign || 'left'}`, {
            'has-thumbs': hasThumbs,
            'is-empty': !slides.length
        })
    });

    /**
     * The inner blocks region does double duty.
     *
     * In fixed mode it is the shared content, exactly as before. In per-slide
     * mode it holds the slide blocks too, and PHP tells the two apart by block
     * name at render time — a block only gets one inner-blocks region, so this
     * is what makes a shared layer and per-slide layers coexist.
     */
    const innerBlocksProps = useInnerBlocksProps(
        { className: perSlide ? 'dnte-hero-editor-blocks' : 'dnte-hero-content-inner' },
        perSlide
            ? { allowedBlocks: ALLOWED_WITH_SLIDES, templateLock: false }
            : { template: TEMPLATE, templateLock: false }
    );

    /*
     * Nothing chosen yet: the two modes produce different inner blocks, so the
     * decision is made once up front rather than by switching a populated block
     * back and forth.
     */
    if (!mode) {
        return (
            <div {...blockProps}>
                <Placeholder
                    label={__('Hero Slider', 'dentist-exchange')}
                    instructions={__('Choose how the content behaves as the background images change.', 'dentist-exchange')}
                    className="dnte-hero-mode-placeholder"
                >
                    <Button variant="primary" onClick={() => chooseMode('fixed')}>
                        {__('Fixed content', 'dentist-exchange')}
                    </Button>
                    <Button variant="secondary" onClick={() => chooseMode('per-slide')}>
                        {__('Content per slide', 'dentist-exchange')}
                    </Button>
                </Placeholder>
            </div>
        );
    }

    if (perSlide) {
        return (
            <>
                {isSelected && <Inspector {...props} onChangeMode={chooseMode} />}
                <div {...blockProps}>
                    <div {...innerBlocksProps} />
                </div>
            </>
        );
    }

    if (!slides.length) {
        return (
            <>
                {isSelected && <Inspector {...props} onChangeMode={chooseMode} />}
                <div {...blockProps}>
                    <MediaPlaceholder
                        labels={{
                            title: __('Hero Slider', 'dentist-exchange'),
                            instructions: __(
                                'Pick the background images. The content stays put while the images slide behind it.',
                                'dentist-exchange'
                            )
                        }}
                        onSelect={onSelectImages}
                        accept="image/*"
                        allowedTypes={['image']}
                        multiple
                    />
                </div>
            </>
        );
    }

    return (
        <>
            <BlockControls>
                <ToolbarGroup>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={onSelectImages}
                            allowedTypes={['image']}
                            multiple
                            gallery
                            value={slides.map(slide => slide.id)}
                            render={({ open }) => (
                                <ToolbarButton icon="format-gallery" label={__('Edit images', 'dentist-exchange')} onClick={open} />
                            )}
                        />
                    </MediaUploadCheck>
                </ToolbarGroup>
            </BlockControls>

            {isSelected && <Inspector {...props} onChangeMode={chooseMode} />}

            <div {...blockProps}>
                <div className="dnte-hero-bg dnte-hero-editor-bg">
                    {previewUrl && <img className="dnte-hero-image" src={previewUrl} alt={activeSlide.alt || ''} />}
                </div>

                {hasOverlay && <div className="dnte-hero-overlay" aria-hidden="true" />}

                <div className="dnte-hero-content">
                    <div {...innerBlocksProps} />
                </div>

                {hasThumbs && (
                    <div className="dnte-hero-thumbs-wrap">
                        {hasArrows && (
                            <button
                                type="button"
                                className="dnte-nav swiper-custom-prev"
                                aria-label={__('Previous image', 'dentist-exchange')}
                                onClick={() => step(-1)}
                            >
                                <RenderIcon
                                    customSvgCode={prevCustomSvg}
                                    iconName={prevIconName}
                                    iconType={prevIconType}
                                    size={navIconSize}
                                />
                            </button>
                        )}

                        <div className="dnte-hero-thumbs dnte-hero-editor-thumbs">
                            <div className="dnte-hero-editor-track">
                                {slides.map((slide, index) => (
                                    <button
                                        type="button"
                                        key={slide.id || index}
                                        className={classNames('dnte-hero-thumb', { 'is-active': index === activeIndex })}
                                        onClick={() => setActiveIndex(index)}
                                    >
                                        <img src={slideThumb(slide)} alt="" />
                                    </button>
                                ))}
                            </div>
                        </div>

                        {hasArrows && (
                            <button
                                type="button"
                                className="dnte-nav swiper-custom-next"
                                aria-label={__('Next image', 'dentist-exchange')}
                                onClick={() => step(1)}
                            >
                                <RenderIcon
                                    customSvgCode={nextCustomSvg}
                                    iconName={nextIconName}
                                    iconType={nextIconType}
                                    size={navIconSize}
                                />
                            </button>
                        )}
                    </div>
                )}
            </div>
        </>
    );
};

export default Edit;
