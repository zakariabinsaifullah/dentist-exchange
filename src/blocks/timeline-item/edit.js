import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const INNER_TEMPLATE = [['core/paragraph']];

const Edit = () => {
    const blockProps = useBlockProps();

    const innerBlockProps = useInnerBlocksProps({ className: 'timeline-content' }, { template: INNER_TEMPLATE, templateLock: false });

    return (
        <div {...blockProps}>
            <div className="timeline-marker" aria-hidden="true">
                <span className="timeline-dot" />
            </div>
            <div {...innerBlockProps} />
        </div>
    );
};

export default Edit;
