/**
 * WordPress Dependencies
 */
import {
    useBlockProps,
    InnerBlocks,
    __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
    __experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles
} from '@wordpress/block-editor';

/**
 * Internal Dependencies
 */
import Inspector from './inspector';

// Block edit function
const Edit = props => {
    const { attributes } = props;
    const { stackRotate, stackOffsetX, stackOffsetY, stackZIndex } = attributes;

    // Get block support props
    const borderProps = getBorderClassesAndStyles(attributes);
    const colorProps = getColorClassesAndStyles(attributes);
    const spacingProps = getSpacingClassesAndStyles(attributes);
    const shadowProps = getShadowClassesAndStyles(attributes);

    const blockProps = useBlockProps({
        className: 'swiper-slide',
        style: {
            ...borderProps.style,
            ...colorProps.style,
            ...spacingProps.style,
            ...shadowProps.style,
            transform: `translate(${stackOffsetX || 0}px, ${stackOffsetY || 0}px) rotate(${stackRotate || 0}deg)`,
            zIndex: stackZIndex || 0
        }
    });

    return (
        <>
            <Inspector {...props} />
            <div {...blockProps}>
                <InnerBlocks renderAppender={() => <InnerBlocks.ButtonBlockAppender />} />
            </div>
        </>
    );
};

export default Edit;
