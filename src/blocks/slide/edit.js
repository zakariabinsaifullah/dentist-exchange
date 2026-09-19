/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import {
    useBlockProps,
    InnerBlocks,
    __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
    __experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles
} from '@wordpress/block-editor';

// Block edit function
const Edit = ({ attributes }) => {
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
            ...shadowProps.style
        }
    });

    return (
        <div {...blockProps}>
            <InnerBlocks renderAppender={() => <InnerBlocks.ButtonBlockAppender />} />
        </div>
    );
};

export default Edit;
