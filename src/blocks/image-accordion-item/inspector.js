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

/**
 * Strips every tag except <br>.
 *
 * The canvas fields already enforce this through RichText's
 * `allowedFormats={[]}`, but these inspector inputs write straight to the
 * attribute, so without this they would be a way around it — and the help
 * text below would be a lie.
 *
 * Requires a letter after the angle bracket so ordinary prose survives:
 * "a < b > c" is left alone, while <p>, </p> and <script> are removed. This
 * is an input guard for consistency, not a security boundary — WordPress's
 * own kses still governs what untrusted roles are allowed to save.
 *
 * @param {string} value Raw field value.
 * @return {string} Value with only line-break tags left intact.
 */
const allowOnlyLineBreaks = value => (value || '').replace(/<\/?(?!br\b)[a-zA-Z][^>]*>/gi, '');

const BR_HELP = __('Use <br> for a line break. Other HTML is removed.', 'dentist-exchange');

const EMPTY_IMAGE = { id: '', url: '', alt: '' };

/**
 * One image slot: preview, upload/replace and remove.
 *
 * @param {Object}   props
 * @param {string}   props.label    Field label.
 * @param {Object}   props.value    Current image ({id, url, alt}).
 * @param {Function} props.onChange Receives the new image object.
 * @param {string}   props.help     Optional help text.
 */
const ImageField = ({ label, value, onChange, help }) => (
    <div className="dnte-accordion-image">
        <p className="dnte-accordion-image__label">{label}</p>
        {value?.url && <img src={value.url} alt="" />}
        <MediaUploadCheck>
            <MediaUpload
                onSelect={media => onChange({ id: media.id, url: media.url, alt: media.alt })}
                allowedTypes={['image']}
                value={value?.id}
                render={({ open }) => (
                    <Button variant="secondary" onClick={open}>
                        {value?.url ? __('Replace Image', 'dentist-exchange') : __('Upload Image', 'dentist-exchange')}
                    </Button>
                )}
            />
        </MediaUploadCheck>
        {value?.url && (
            <Button variant="link" isDestructive onClick={() => onChange({ ...EMPTY_IMAGE })}>
                {__('Remove Image', 'dentist-exchange')}
            </Button>
        )}
        {help && <p className="dnte-accordion-image__help">{help}</p>}
    </div>
);

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const { image, imageTablet, imageMobile, showTitle, title, showDesc, description, showBtn, btnLabel, href, linkTarget } = attributes;

    return (
        <InspectorControls>
            <PanelBody title={__('Content', 'dentist-exchange')} initialOpen={true}>
                <ImageField
                    label={__('Image (Desktop)', 'dentist-exchange')}
                    value={image}
                    onChange={value => setAttributes({ image: value })}
                />
                <ImageField
                    label={__('Image (Tablet)', 'dentist-exchange')}
                    value={imageTablet}
                    onChange={value => setAttributes({ imageTablet: value })}
                    help={__('Used up to 781px. Falls back to the desktop image.', 'dentist-exchange')}
                />
                <ImageField
                    label={__('Image (Mobile)', 'dentist-exchange')}
                    value={imageMobile}
                    onChange={value => setAttributes({ imageMobile: value })}
                    help={__('Used up to 599px. Falls back to tablet, then desktop.', 'dentist-exchange')}
                />
                {showTitle && (
                    <NativeTextControl
                        label={__('Heading', 'dentist-exchange')}
                        value={title}
                        placeholder={__('Accordion title..', 'dentist-exchange')}
                        help={BR_HELP}
                        onChange={value => setAttributes({ title: allowOnlyLineBreaks(value) })}
                    />
                )}
                {showDesc && (
                    <NativeTextareaControl
                        label={__('Description', 'dentist-exchange')}
                        value={description}
                        placeholder={__('Accordion description..', 'dentist-exchange')}
                        help={BR_HELP}
                        onChange={value => setAttributes({ description: allowOnlyLineBreaks(value) })}
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
