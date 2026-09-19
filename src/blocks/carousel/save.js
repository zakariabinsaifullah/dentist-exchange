import {
    useBlockProps,
    InnerBlocks,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles
} from '@wordpress/block-editor';
import classNames from 'classnames';

export default function save({ attributes }) {
    const { blockStyle, gaps, loop, autoplay, delay, visibleItems } = attributes;

    // The carousel is always in Rotate Stack mode, with no navigation arrows.
    const options = {
        loop,
        autoplay: autoplay ? { delay: delay || 3000 } : false,
        gaps,
        visibleItems
    };

    const colorProps = getColorClassesAndStyles(attributes);
    const spacingProps = getSpacingClassesAndStyles(attributes);

    return (
        <div
            {...useBlockProps.save({
                style: {
                    ...blockStyle,
                    ...colorProps.style,
                    ...spacingProps.style
                },
                className: classNames(colorProps.className, spacingProps.className, 'is-stack')
            })}
            data-options={JSON.stringify(options)}
        >
            <div className="swiper">
                <div className="swiper-wrapper">
                    <InnerBlocks.Content />
                </div>
            </div>
            <div className="swiper-pagination"></div>
        </div>
    );
}
