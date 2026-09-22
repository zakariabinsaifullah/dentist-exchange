// import editor style
import './editor.scss';

/**
 * WordPress Dependencies
 */
import { RichText, useBlockProps, MediaPlaceholder, BlockControls, MediaReplaceFlow } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * External Dependencies
 */
import classNames from 'classnames';

/**
 * Internal Dependencies
 */
import Inspector from './inspector';

/**
 * Looks one arrow up in the registry PHP hands to the editor.
 *
 * @param {string} slug Arrow slug.
 * @return {Object|null} The arrow, or null when unknown.
 */
export const getArrow = slug => (window.dnteArrows || []).find(arrow => arrow.slug === slug) || null;

// block edit function
const Edit = props => {
    const { attributes, setAttributes } = props;
    const {
        icon,
        badgeSide,
        image,
        heading,
        headingTag,
        description,
        lane,
        arrow,
        arrowTop,
        arrowLeft,
        arrowWidth,
        arrowRotate,
        arrowFlipX
    } = attributes;

    const selectedArrow = getArrow(arrow);

    const blockProps = useBlockProps({
        className: classNames('dnte-story-card', `has-badge-${badgeSide || 'left'}`, {
            [`is-lane-${lane}`]: 'auto' !== lane
        }),
        // No positioning in the canvas: the exact composition is applied by
        // view.js on the frontend only, and the editor keeps a simple column.
        style: undefined
    });

    return (
        <Fragment>
            <Inspector {...props} />

            {image?.url && (
                <BlockControls group="other">
                    <MediaReplaceFlow
                        mediaId={image?.id}
                        mediaURL={image?.url}
                        allowedTypes={['image']}
                        accept="image/*"
                        onSelect={media => setAttributes({ image: { id: media.id, url: media.url, alt: media.alt } })}
                        name={__('Replace image', 'dentist-exchange')}
                    />
                </BlockControls>
            )}

            <div {...blockProps}>
                {icon?.url && (
                    <span className="dnte-story-card__badge">
                        <img src={icon.url} alt="" />
                    </span>
                )}

                <figure className="dnte-story-card__media">
                    {image?.url ? (
                        <img className="dnte-story-card__image" src={image.url} alt={image.alt || ''} />
                    ) : (
                        <MediaPlaceholder
                            labels={{ title: __('Card Image', 'dentist-exchange') }}
                            onSelect={media => setAttributes({ image: { id: media.id, url: media.url, alt: media.alt } })}
                            accept="image/*"
                            allowedTypes={['image']}
                        />
                    )}
                </figure>

                <div className="dnte-story-card__content">
                    {/*
                     * Formats are left enabled here, unlike the accordion's
                     * fields: the design circles and underlines words in these
                     * headings, which is the theme's annotation format
                     * (src/extensions/annotation).
                     */}
                    <RichText
                        tagName={headingTag || 'h3'}
                        className="dnte-story-card__heading"
                        value={heading}
                        onChange={value => setAttributes({ heading: value })}
                        placeholder={__('Card heading…', 'dentist-exchange')}
                    />
                    <RichText
                        tagName="p"
                        className="dnte-story-card__desc"
                        value={description}
                        onChange={value => setAttributes({ description: value })}
                        placeholder={__('Card description…', 'dentist-exchange')}
                        allowedFormats={[]}
                        withoutInteractiveFormatting
                    />
                </div>

                {/*
                 * The connector previews the real artwork, fully drawn — the
                 * wipe only runs on the frontend, where view.js loads.
                 */}
                {selectedArrow && (
                    <span
                        className="dnte-story-card__connector"
                        style={{
                            '--dnte-arrow-top': `${arrowTop}%`,
                            '--dnte-arrow-left': `${arrowLeft}%`,
                            '--dnte-arrow-width': `${arrowWidth}%`,
                            '--dnte-arrow-rotate': `${arrowRotate}deg`,
                            '--dnte-arrow-flip': arrowFlipX ? '-1' : '1'
                        }}
                        aria-hidden="true"
                    >
                        <span className="dnte-story-card__connector-art" dangerouslySetInnerHTML={{ __html: selectedArrow.svg }} />
                    </span>
                )}
            </div>
        </Fragment>
    );
};

export default Edit;
