/**
 * WordPress dependencies
 */
import { InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const { image } = attributes;

    return (
        <InspectorControls>
            <PanelBody title={__('Content', 'dentist-exchange')} initialOpen={true}>
                <div className="dnte-accordion-image">
                    {image?.url && <img src={image.url} alt="" />}
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={media => setAttributes({ image: { id: media.id, url: media.url, alt: media.alt } })}
                            allowedTypes={['image']}
                            value={image?.id}
                            render={({ open }) => (
                                <Button variant="secondary" onClick={open}>
                                    {image?.url ? __('Replace Image', 'dentist-exchange') : __('Upload Image', 'dentist-exchange')}
                                </Button>
                            )}
                        />
                    </MediaUploadCheck>
                    {image?.url && (
                        <Button variant="link" isDestructive onClick={() => setAttributes({ image: { id: '', url: '', alt: '' } })}>
                            {__('Remove Image', 'dentist-exchange')}
                        </Button>
                    )}
                </div>
            </PanelBody>
        </InspectorControls>
    );
};

export default Inspector;
