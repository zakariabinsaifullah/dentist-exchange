/**
 * WordPress dependencies
 */
import {
    RichText,
    useBlockProps,
    __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
    __experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles
} from '@wordpress/block-editor';

/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import metadata from './block.json';

const IMAGE_DEFAULT = {
    type: 'object',
    default: {
        id: '',
        url: '',
        alt: ''
    }
};

/**
 * v1 rendered the item's image as an <img> inside `.img`.
 *
 * It is now painted as a background instead, so the tablet and mobile
 * variants can be swapped by media query alone. That changes the saved
 * markup, so without this entry every existing item would fail validation.
 *
 * `migrate` seeds `itemStyle` from the existing image — the new save reads
 * the background from that custom property, so an item carried over without
 * it would render no image at all.
 */
const v1 = {
    attributes: {
        image: IMAGE_DEFAULT,
        showTitle: {
            type: 'boolean',
            default: true
        },
        title: {
            type: 'string'
        },
        titleTag: {
            type: 'string',
            default: 'h4'
        },
        showDesc: {
            type: 'boolean',
            default: true
        },
        description: {
            type: 'string'
        },
        showBtn: {
            type: 'boolean',
            default: true
        },
        btnLabel: {
            type: 'string',
            default: 'Show More'
        },
        href: {
            type: 'string',
            default: '#'
        },
        linkTarget: {
            type: 'string'
        },
        linkRel: {
            type: 'string'
        }
    },

    supports: metadata.supports,

    migrate(attributes) {
        const { image } = attributes;

        return {
            ...attributes,
            itemStyle: image?.url ? { '--dimg': `url("${image.url}")` } : {}
        };
    },

    save({ attributes }) {
        const { image, showTitle, title, titleTag, showDesc, description, showBtn, btnLabel, href, linkTarget, linkRel } = attributes;

        const borderProps = getBorderClassesAndStyles(attributes);
        const colorProps = getColorClassesAndStyles(attributes);
        const spacingProps = getSpacingClassesAndStyles(attributes);
        const shadowProps = getShadowClassesAndStyles(attributes);

        const blockProps = useBlockProps.save({
            className: classNames(colorProps.className, borderProps.className, spacingProps.className, shadowProps.className),
            style: {
                ...borderProps.style,
                ...colorProps.style,
                ...spacingProps.style,
                ...shadowProps.style
            }
        });

        return (
            <div {...blockProps}>
                <div className="img">
                    {image?.url && (
                        <img
                            src={image.url}
                            alt={image.alt || ''}
                            className={classNames('img-cover', { [`wp-image-${image.id}`]: image.id })}
                        />
                    )}
                </div>
                <div className="info">
                    <div className="top-content">
                        {showTitle && <RichText.Content tagName={titleTag || 'h4'} value={title} className="heading" />}
                        {showDesc && description && <RichText.Content tagName="p" value={description} className="description" />}
                    </div>
                    {showBtn && (
                        <a href={href || '#'} className="btn" {...(linkTarget && { target: linkTarget, rel: linkRel })}>
                            {btnLabel}
                        </a>
                    )}
                </div>
            </div>
        );
    }
};

export default [v1];
