import {
    useBlockProps,
    InnerBlocks,
    __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
    __experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles
} from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { stackRotate, stackOffsetX, stackOffsetY, stackZIndex } = attributes;

    // Get block support props
    const borderProps = getBorderClassesAndStyles(attributes);
    const colorProps = getColorClassesAndStyles(attributes);
    const spacingProps = getSpacingClassesAndStyles(attributes);
    const shadowProps = getShadowClassesAndStyles(attributes);

    return (
        <div
            {...useBlockProps.save({
                className: 'swiper-slide',
                style: {
                    ...borderProps.style,
                    ...colorProps.style,
                    ...spacingProps.style,
                    ...shadowProps.style,
                    transform: `translate(${stackOffsetX || 0}px, ${stackOffsetY || 0}px) rotate(${stackRotate || 0}deg)`,
                    zIndex: stackZIndex || 0
                }
            })}
        >
            <InnerBlocks.Content />
        </div>
    );
}
