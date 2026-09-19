/**
 * WordPress Dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, InspectorControls, MediaUpload, MediaUploadCheck, MediaPlaceholder } from '@wordpress/block-editor';
import { PanelBody, Button } from '@wordpress/components';
import { closeSmall } from '@wordpress/icons';
import { useSelect } from '@wordpress/data';

/**
 * Internal Dependencies
 */
import './editor.scss';
import { toSlide } from '../hero-slider/utils';

/**
 * One of the slide's three image slots. Mirrors the parent's inspector list, so
 * a slide is set up the same way whichever mode the slider is in.
 */
const MediaSlot = ({ label, url, inheritedFrom, onSelect, onClear }) => (
    <div className="dnte-hero-slot">
        <div className="dnte-hero-slot__preview">{url ? <img src={url} alt="" /> : <span className="dnte-hero-slot__empty" />}</div>
        <div className="dnte-hero-slot__body">
            <span className="dnte-hero-slot__label">{label}</span>
            {!url && inheritedFrom && <span className="dnte-hero-slot__hint">{inheritedFrom}</span>}
        </div>
        <MediaUploadCheck>
            <MediaUpload
                onSelect={onSelect}
                allowedTypes={['image']}
                render={({ open }) => (
                    <Button variant="tertiary" size="small" onClick={open}>
                        {url ? __('Replace', 'dentist-exchange') : __('Set', 'dentist-exchange')}
                    </Button>
                )}
            />
        </MediaUploadCheck>
        {url && onClear && <Button size="small" icon={closeSmall} label={__('Clear', 'dentist-exchange')} onClick={onClear} />}
    </div>
);

/* Slides start empty; a slide with no content simply shows its background. */
const TEMPLATE = [];

const Edit = props => {
    const { attributes, setAttributes } = props;
    const { url, alt, mobileUrl, thumb, thumbId, autoThumb } = attributes;

    const deviceType = useSelect(select => select('core/editor')?.getDeviceType(), []);

    const previewUrl = 'Mobile' === deviceType ? mobileUrl || url : url;

    const blockProps = useBlockProps({
        className: 'dnte-hero-editor-slide'
    });

    const innerBlocksProps = useInnerBlocksProps(
        { className: 'dnte-hero-content-inner' },
        { template: TEMPLATE, templateLock: false }
    );

    if (!url) {
        return (
            <div {...blockProps}>
                <MediaPlaceholder
                    labels={{
                        title: __('Hero Slide', 'dentist-exchange'),
                        instructions: __('Pick this slide’s background image.', 'dentist-exchange')
                    }}
                    onSelect={image => setAttributes(toSlide(image))}
                    accept="image/*"
                    allowedTypes={['image']}
                />
            </div>
        );
    }

    return (
        <>
            <InspectorControls group="settings">
                <PanelBody title={__('Slide Images', 'dentist-exchange')} initialOpen={true}>
                    <MediaSlot label={__('Desktop', 'dentist-exchange')} url={url} onSelect={image => {
                        const next = toSlide(image);
                        // A hand-picked thumbnail outlives a change of background.
                        setAttributes(thumbId ? { ...next, thumb } : next);
                    }} />
                    <MediaSlot
                        label={__('Mobile', 'dentist-exchange')}
                        url={mobileUrl}
                        inheritedFrom={__('Uses the desktop image', 'dentist-exchange')}
                        onSelect={image => setAttributes({ mobileId: image.id, mobileUrl: image.url })}
                        onClear={() => setAttributes({ mobileId: undefined, mobileUrl: undefined })}
                    />
                    <MediaSlot
                        label={__('Thumbnail', 'dentist-exchange')}
                        url={thumbId ? thumb : undefined}
                        inheritedFrom={__('Cropped from the desktop image', 'dentist-exchange')}
                        onSelect={image => setAttributes({ thumbId: image.id, thumb: image.url })}
                        onClear={() => setAttributes({ thumbId: undefined, thumb: autoThumb || url })}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div className="dnte-hero-editor-slide__bg">
                    <img src={previewUrl} alt={alt || ''} />
                </div>
                <div className="dnte-hero-editor-slide__content">
                    <div {...innerBlocksProps} />
                </div>
            </div>
        </>
    );
};

export default Edit;
