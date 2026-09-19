/**
 * The responsive plumbing is shared with the carousel: Desktop is the source of
 * truth and Tablet/Mobile inherit from it unless they carry their own value.
 * That fallback is resolved here in JS so the stylesheet only ever reads an
 * explicit per-breakpoint property.
 */
import { DEVICES, PREFIXES, resolveResponsive } from '../carousel/utils';

export { DEVICES, PREFIXES, resolveResponsive };

const VALIGN_MAP = {
    top: 'flex-start',
    middle: 'center',
    bottom: 'flex-end'
};

const HALIGN_MAP = {
    left: 'flex-start',
    center: 'center',
    right: 'flex-end'
};

/**
 * Emit one custom property per breakpoint for a responsive attribute, e.g.
 * `thumb-w` becomes `--dthumb-w`, `--tthumb-w` and `--mthumb-w`.
 *
 * @param {Object} values Per-device values.
 * @param {string} name   Property name, without the device prefix.
 * @param {string} unit   Appended to bare numbers.
 */
export const generateResponsiveVars = (values, name, unit = '') =>
    DEVICES.reduce((styles, device) => {
        const value = resolveResponsive(values, device);

        if (value === undefined || value === null || value === '') {
            return styles;
        }

        styles[`--${PREFIXES[device]}${name}`] = typeof value === 'number' ? `${value}${unit}` : `${value}`;

        return styles;
    }, {});

/**
 * Hero height and vertical alignment. Unlike the carousel, the alignment is
 * emitted whether or not the height is pinned — an adaptive hero still needs to
 * know where its content sits inside whatever height the image gives it.
 */
export const generateHeroStyles = (heightType, heights, vAligns) =>
    DEVICES.reduce((styles, device) => {
        const prefix = PREFIXES[device];
        const vAlign = VALIGN_MAP[resolveResponsive(vAligns, device)];

        if (resolveResponsive(heightType, device) === 'fixed') {
            const height = resolveResponsive(heights, device);

            if (height) {
                styles[`--${prefix}height`] = `${height}`;
            }
        }

        if (vAlign) {
            styles[`--${prefix}valign`] = vAlign;
        }

        return styles;
    }, {});

/**
 * Horizontal placement for the content column and the thumbnail strip.
 */
export const alignToFlex = align => HALIGN_MAP[align] || 'flex-start';

/**
 * Where the width-limited column sits inside the hero, expressed as the auto
 * margins that push it there. The thumbnail strip is not a flex item of the same
 * container as the content, so margins — not `align-items` — are what both can
 * share.
 */
const POSITION_MARGINS = {
    left: ['0', 'auto'],
    center: ['auto', 'auto'],
    right: ['auto', '0']
};

export const positionMargins = position => POSITION_MARGINS[position] || POSITION_MARGINS.left;

/**
 * The mid-size file WordPress already has, rather than the full-size original —
 * the strip is ~152px wide, so shipping four full-size images into it is waste.
 */
const derivedThumb = image =>
    image.sizes?.medium_large?.url || image.sizes?.medium?.url || image.sizes?.thumbnail?.url || image.url;

/**
 * Normalise a media library selection down to just what the block renders, so
 * the post content does not carry the whole attachment object around.
 *
 * A slide holds up to three images. Only the desktop one is required:
 *
 *   url        the background image, and the fallback for everything else
 *   mobileUrl  an optional swap below 768px, picked separately
 *   thumb      what the strip shows — `autoThumb` unless overridden
 *
 * `autoThumb` is kept alongside `thumb` so clearing a custom thumbnail can drop
 * straight back to the derived one without another trip to the media library.
 */
export const toSlide = image => ({
    id: image.id,
    url: image.url,
    alt: image.alt || '',
    autoThumb: derivedThumb(image),
    thumb: derivedThumb(image)
});

/**
 * Re-opening the gallery picker returns plain attachments, which know nothing
 * about the mobile and thumbnail overrides already attached to a slide. Matching
 * on attachment id carries those across so adding a fifth image cannot silently
 * reset the other four.
 */
export const mergeSelection = (images, existing = []) => {
    const previous = new Map(existing.map(slide => [slide.id, slide]));

    return (images || [])
        .filter(image => !!image.url)
        .map(image => {
            const prior = previous.get(image.id);
            const next = toSlide(image);

            if (!prior) {
                return next;
            }

            return {
                ...next,
                ...(prior.mobileId && { mobileId: prior.mobileId, mobileUrl: prior.mobileUrl }),
                ...(prior.thumbId && { thumbId: prior.thumbId, thumb: prior.thumb })
            };
        });
};

/**
 * What actually gets painted, once the optional slots have had their say.
 */
export const slideThumb = slide => slide.thumb || slide.autoThumb || slide.url;

/**
 * Options handed to Swiper on the front end. Kept in one place so `save.js` and
 * the editor preview cannot drift apart.
 */
export const buildOptions = ({ effect, speed, autoplay, delay, loop, thumbGaps }) => ({
    effect: effect || 'fade',
    speed: speed || 800,
    loop: !!loop,
    autoplay: autoplay ? { delay: delay || 5000 } : false,
    thumbGaps: {
        Desktop: resolveResponsive(thumbGaps, 'Desktop') ?? 20,
        Tablet: resolveResponsive(thumbGaps, 'Tablet') ?? 20,
        Mobile: resolveResponsive(thumbGaps, 'Mobile') ?? 12
    }
});
