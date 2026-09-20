/**
 * WordPress Dependencies
 */
import {
    useBlockProps,
    useInnerBlocksProps,
    ButtonBlockAppender,
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
    const { attributes, clientId } = props;
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

    // The inner blocks live directly inside `.swiper-slide`, matching save.js.
    //
    // The appender has to be rendered by hand with an explicit `rootClientId`:
    // Gutenberg calls `renderAppender` with no arguments, so the bare
    // `<InnerBlocks.ButtonBlockAppender />` this block used to pass never knew
    // which block to insert into and its inserter targeted the document root —
    // nothing could be added inside a slide. Passing it also keeps the "+"
    // permanently visible; the built-in appender only appears while the slide
    // itself is the selected block, which leaves a new slide looking empty.
    const innerBlocksProps = useInnerBlocksProps(blockProps, {
        template: [['core/paragraph']],
        templateLock: false,
        renderAppender: () => <ButtonBlockAppender rootClientId={clientId} />
    });

    return (
        <>
            <Inspector {...props} />
            <div {...innerBlocksProps} />
        </>
    );
};

export default Edit;
