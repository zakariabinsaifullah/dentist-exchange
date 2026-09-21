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
 * External Dependencies
 */
import classNames from 'classnames';

// block save function
const save = props => {
    const { attributes } = props;
    const { image, itemStyle, showTitle, title, titleTag, showDesc, description, showBtn, btnLabel, href, linkTarget, linkRel } =
        attributes;

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
            {/*
             * Painted as a background rather than an <img> so the tablet and
             * mobile variants swap by media query — see style.scss. An <img>
             * carries its own accessible name, so where one was described the
             * role/label pair stands in for it; an undescribed image stays
             * decorative, as an empty alt would have been.
             */}
            <div className="img" style={itemStyle} {...(image?.alt ? { role: 'img', 'aria-label': image.alt } : {})} />

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
};

export default save;
