import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    RichText,
    useBlockProps,
    __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
    __experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles
} from '@wordpress/block-editor';
import { Icon } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { getIconByName } from '../../utils/icons';

/**
 * Save function for the block.
 *
 * @param {Object}   props
 * @param {Object}   props.attributes - Block attributes.
 * @param {string}   props.className  - Additional class name.
 * @return {WPElement} Element to render.
 */
export default function save({ attributes, className }) {
    const {
        iconName,
        iconSize,
        customSvgCode,
        iconType,
        style,
        justifyContent,
        tagName: Tag,
        href,
        linkTarget,
        linkRel,
        sizes,
        showTitle,
        heading,
        headingTag,
        titleFontWeight,
        showDesc,
        description,
        descTag,
        showButton,
        buttonText,
        buttonUrl,
        buttonLinkTarget,
        buttonLinkRel,
        buttonIconName,
        buttonCustomSvgCode,
        buttonIconType,
        blockStyle,
        iconVerticalAlign
    } = attributes;

    const buttonIcon = getIconByName(buttonIconName);
    const hasButtonIcon = !!(buttonCustomSvgCode || buttonIcon);

    // `wp-element-button` is what theme.json's `styles.elements.button`
    // compiles against, so this picks up the theme's default button exactly
    // — background, radius, padding, type, hover and focus — with no styles
    // of our own duplicating it.
    const button = showButton && (
        <a className="wp-element-button icon-button" {...(buttonUrl && { href: buttonUrl, target: buttonLinkTarget, rel: buttonLinkRel })}>
            <RichText.Content tagName="span" value={buttonText} className="icon-button__text" />
            {hasButtonIcon && (
                <span className={classNames('icon-button__icon', `is-${buttonIconType}`)}>
                    {buttonCustomSvgCode ? (
                        <span dangerouslySetInnerHTML={{ __html: buttonCustomSvgCode }} />
                    ) : (
                        <Icon icon={buttonIcon.icon} size={24} />
                    )}
                </span>
            )}
        </a>
    );

    // Title, description and button are independent toggles, so the content
    // area renders for any one of them on its own. Shared between the
    // custom-SVG and icon-library branches below.
    const content = (showTitle || showDesc || showButton) && (
        <div className="icon-content">
            {showTitle && <RichText.Content tagName={headingTag} value={heading} className="icon-heading" />}
            {showDesc && <RichText.Content tagName={descTag} value={description} className="icon-description" />}
            {button}
        </div>
    );

    // Get block support props
    const borderProps = getBorderClassesAndStyles(attributes);
    const colorProps = getColorClassesAndStyles(attributes);
    const spacingProps = getSpacingClassesAndStyles(attributes);
    const shadowProps = getShadowClassesAndStyles(attributes);

    // Outer wrapper block props (only className for alignment or custom classes)
    const blockProps = useBlockProps.save({
        style: blockStyle,
        className: classNames(className, {
            [`is-${iconType}`]: iconType,
            [`justify-${justifyContent}`]: justifyContent,
            // Absent unless a weight is chosen, so blocks saved before this
            // existed keep exactly the markup they had.
            'has-title-weight': !!titleFontWeight
        })
    });

    // Inner icon container classes and styles
    const iconClasses = classNames(
        'icon-container',
        colorProps.className,
        borderProps.className,
        spacingProps.className,
        shadowProps.className,
        {
            'no-border-radius': style?.border?.radius === 0,
            'has-padding': style?.spacing?.padding && Object.keys(style.spacing.padding).length > 0
        }
    );

    // Only breakpoints with an explicit size are written out; style.scss falls
    // back to the next larger breakpoint for the rest.
    const iconStyle = {
        ...borderProps.style,
        ...colorProps.style,
        ...spacingProps.style,
        ...shadowProps.style,
        ...(sizes?.Desktop && { '--dsize': `${sizes.Desktop}px` }),
        ...(sizes?.Tablet && { '--tsize': `${sizes.Tablet}px` }),
        ...(sizes?.Mobile && { '--msize': `${sizes.Mobile}px` })
    };

    // Render custom SVG if available
    if (customSvgCode) {
        return (
            <Tag {...blockProps} {...(href && { href, target: linkTarget, rel: linkRel })}>
                <div
                    className={classNames('dnte-icon-block-wrapper', {
                        [`icon-valign-${iconVerticalAlign}`]: iconVerticalAlign
                    })}
                >
                    <div className={iconClasses} style={iconStyle} dangerouslySetInnerHTML={{ __html: customSvgCode }} />
                    {content}
                </div>
            </Tag>
        );
    }

    // Fallback to default icon
    const selectedIcon = getIconByName(iconName);
    if (!selectedIcon) {
        return null;
    }

    return (
        <Tag {...blockProps} {...(href && { href, target: linkTarget, rel: linkRel })}>
            <div
                className={classNames('dnte-icon-block-wrapper', {
                    [`icon-valign-${iconVerticalAlign}`]: iconVerticalAlign
                })}
            >
                <div className={iconClasses} style={iconStyle}>
                    <Icon icon={selectedIcon.icon} size={iconSize} />
                </div>
                {content}
            </div>
        </Tag>
    );
}
