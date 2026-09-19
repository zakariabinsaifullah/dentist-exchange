import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import classNames from 'classnames';
import { RenderIcon } from '../../helpers';

export default function save({ attributes }) {
    const {
        blockStyle,
        columns,
        gaps,
        showArrows,
        showPagination,
        loop,
        autoplay,
        delay,
        navType,
        navIconSize,
        navPosition,
        prevIconName,
        prevIconType,
        prevCustomSvg,
        nextIconName,
        nextIconType,
        nextCustomSvg
    } = attributes;

    const options = {
        loop,
        autoplay: autoplay ? { delay: delay || 3000 } : false,
        columns,
        gaps
    };

    return (
        <div
            {...useBlockProps.save({
                style: blockStyle,
                className: classNames({
                    outside: navType === 'outside' && showArrows,
                    [`nav-pos-${navPosition}`]: navPosition
                })
            })}
            data-options={JSON.stringify(options)}
        >
            <div className="swiper">
                <div className="swiper-wrapper">
                    <InnerBlocks.Content />
                </div>
            </div>
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
            {showPagination && <div className="swiper-pagination"></div>}
        </div>
    );
}
