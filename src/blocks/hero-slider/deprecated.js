import { __, sprintf } from '@wordpress/i18n';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import classNames from 'classnames';

import { RenderIcon } from '../../helpers';
import { buildOptions, slideThumb } from './utils';
import metadata from './block.json';

/**
 * Version 1 — the block before it moved to server rendering.
 *
 * Kept verbatim so posts saved with the old static markup still validate in the
 * editor; they are silently upgraded to the dynamic form the next time the post
 * is saved. The front end never needs this: a dynamic block renders from its
 * attributes and ignores whatever markup is stored.
 */

const { contentMode, ...v1Attributes } = metadata.attributes;

const v1Save = ({ attributes }) => {
    const {
        slides = [],
        blockStyle,
        effect,
        speed,
        autoplay,
        delay,
        loop,
        thumbGaps,
        overlayColor,
        overlayGradient,
        contentAlign,
        showThumbs,
        thumbsAlign,
        showArrows,
        navIconSize,
        prevIconName,
        prevIconType,
        prevCustomSvg,
        nextIconName,
        nextIconType,
        nextCustomSvg
    } = attributes;

    // A single image is a static hero — the strip and the arrows have nothing to move to.
    const hasThumbs = showThumbs && slides.length > 1;
    const hasArrows = showArrows && slides.length > 1;
    const hasOverlay = !!overlayColor || !!overlayGradient;

    const options = buildOptions({ effect, speed, autoplay, delay, loop, thumbGaps });

    return (
        <div
            {...useBlockProps.save({
                style: blockStyle,
                className: classNames(`content-${contentAlign || 'left'}`, `thumbs-${thumbsAlign || 'left'}`, {
                    'has-thumbs': hasThumbs
                })
            })}
            data-options={JSON.stringify(options)}
        >
            <div className="dnte-hero-bg swiper">
                <div className="swiper-wrapper">
                    {slides.map((slide, index) => {
                        const image = (
                            <img
                                className="dnte-hero-image"
                                src={slide.url}
                                alt={slide.alt || ''}
                                loading={index === 0 ? 'eager' : 'lazy'}
                                decoding="async"
                            />
                        );

                        return (
                            <div className="swiper-slide" key={slide.id || index}>
                                {/**
                                 * A slide with a mobile image swaps it in through `<picture>`
                                 * rather than JavaScript, so the browser picks the source
                                 * during preload and only ever downloads the one it needs.
                                 * 767px is the theme's mobile ceiling.
                                 */}
                                {slide.mobileUrl ? (
                                    <picture className="dnte-hero-picture">
                                        <source media="(max-width: 767px)" srcSet={slide.mobileUrl} />
                                        {image}
                                    </picture>
                                ) : (
                                    image
                                )}
                            </div>
                        );
                    })}
                </div>
            </div>

            {hasOverlay && <div className="dnte-hero-overlay" aria-hidden="true" />}

            <div className="dnte-hero-content">
                <div className="dnte-hero-content-inner">
                    <InnerBlocks.Content />
                </div>
            </div>

            {hasThumbs && (
                <div className="dnte-hero-thumbs-wrap">
                    {hasArrows && (
                        <button type="button" className="dnte-nav swiper-custom-prev" aria-label={__('Previous image', 'dentist-exchange')}>
                            <RenderIcon customSvgCode={prevCustomSvg} iconName={prevIconName} iconType={prevIconType} size={navIconSize} />
                        </button>
                    )}

                    <div className="dnte-hero-thumbs swiper">
                        <div className="swiper-wrapper">
                            {slides.map((slide, index) => (
                                <div className="swiper-slide" key={slide.id || index}>
                                    <button
                                        type="button"
                                        className="dnte-hero-thumb"
                                        data-index={index}
                                        aria-label={sprintf(
                                            /* translators: %d: image number within the hero slider. */
                                            __('Show image %d', 'dentist-exchange'),
                                            index + 1
                                        )}
                                    >
                                        <img src={slideThumb(slide)} alt="" loading="lazy" decoding="async" />
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>

                    {hasArrows && (
                        <button type="button" className="dnte-nav swiper-custom-next" aria-label={__('Next image', 'dentist-exchange')}>
                            <RenderIcon customSvgCode={nextCustomSvg} iconName={nextIconName} iconType={nextIconType} size={navIconSize} />
                        </button>
                    )}
                </div>
            )}
        </div>
    );
};

export default [
    {
        attributes: v1Attributes,
        supports: metadata.supports,
        /**
         * Version 1 predates the mode switch, so it is fixed content by
         * definition. Naming it explicitly keeps these blocks away from the
         * "choose a mode" placeholder, which only greets genuinely new ones.
         */
        migrate: attributes => ({ ...attributes, contentMode: 'fixed' }),
        save: v1Save
    }
];
