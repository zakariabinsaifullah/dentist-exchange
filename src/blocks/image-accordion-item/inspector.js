/**
 * WordPress dependencies
 */
import { InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { NativeTextControl, NativeTextareaControl, NativeToggleControl } from '../../components';

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const { image, showTitle, title, showDesc, description, showBtn, btnLabel, href, linkTarget } = attributes;

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
                {showTitle && (
                    <NativeTextControl
                        label={__('Heading', 'dentist-exchange')}
                        value={title}
                        placeholder={__('Accordion title..', 'dentist-exchange')}
                        onChange={value => setAttributes({ title: value })}
                    />
                )}
                {showDesc && (
                    <NativeTextareaControl
                        label={__('Description', 'dentist-exchange')}
                        value={description}
                        placeholder={__('Accordion description..', 'dentist-exchange')}
                        onChange={value => setAttributes({ description: value })}
                    />
                )}
                {showBtn && (
                    <>
                        <NativeTextControl
                            label={__('Button Label', 'dentist-exchange')}
                            value={btnLabel}
                            placeholder={__('Show More', 'dentist-exchange')}
                            onChange={value => setAttributes({ btnLabel: value })}
                        />
                        <NativeTextControl
                            label={__('Button Link', 'dentist-exchange')}
                            value={href}
                            placeholder="https://"
                            help={__('Only applies on the live site — the editor preview link stays disabled.', 'dentist-exchange')}
                            onChange={value => setAttributes({ href: value })}
                        />
                        <NativeToggleControl
                            label={__('Open in new tab', 'dentist-exchange')}
                            checked={linkTarget === '_blank'}
                            onChange={value =>
                                setAttributes({
                                    linkTarget: value ? '_blank' : '',
                                    linkRel: value ? 'noreferrer noopener' : ''
                                })
                            }
                        />
                    </>
                )}
            </PanelBody>
        </InspectorControls>
    );
};

export default Inspector;
