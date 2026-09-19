import { useBlockProps, BlockControls } from '@wordpress/block-editor';
import { JustifyToolbar } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
import Inspector from './inspector';
import './editor.scss';

export default function Edit(props) {
    const { attributes } = props;
    const { justifyContent } = attributes;

    const blockProps = useBlockProps({
        className: {
            [`justify-${justifyContent}`]: justifyContent
        }
    });

    return (
        <>
            <Inspector {...props} />
            <BlockControls group="block">
                <JustifyToolbar
                    allowedControls={['left', 'center', 'right']}
                    value={justifyContent}
                    onChange={value => props.setAttributes({ justifyContent: value })}
                />
            </BlockControls>
            <div {...blockProps}>
                <ServerSideRender block="dnte/social-share" attributes={props.attributes} />
            </div>
        </>
    );
}
