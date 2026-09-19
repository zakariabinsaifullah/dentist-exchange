import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import { RenderIcon } from '../../helpers';

/**
 * v1 marked each item with an icon in a circle. The redesigned timeline uses a
 * plain dot on the rail instead, so the icon attributes are simply dropped and
 * the inner blocks carry over untouched.
 */
const v1 = {
    attributes: {
        iconName: {
            type: 'string'
        },
        iconType: {
            type: 'string',
            default: 'icon'
        },
        customSvgCode: {
            type: 'string'
        },
        iconSize: {
            type: 'number',
            default: 32
        },
        iconStyle: {
            type: 'string',
            default: 'solid'
        }
    },
    supports: {
        anchor: true,
        html: false
    },
    save({ attributes }) {
        const { iconName, customSvgCode, iconSize } = attributes;

        const blockProps = useBlockProps.save();

        return (
            <div {...blockProps}>
                <div className="timeline-icon-row">
                    <div className="timeline-icon">
                        <RenderIcon customSvgCode={customSvgCode} iconName={iconName} size={iconSize} />
                    </div>
                </div>
                <div className="timeline-content">
                    <InnerBlocks.Content />
                </div>
            </div>
        );
    },
    migrate() {
        return {};
    }
};

export default [v1];
