// import editor style
import './editor.scss';

/**
 * WordPress dependencies
 */
import { useBlockProps, MediaPlaceholder, BlockControls, MediaReplaceFlow } from '@wordpress/block-editor';
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { Fragment, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import Inspector from './inspector';

/**
 * Keeps only what the front end needs, so the post does not carry the whole
 * attachment object for every image.
 *
 * @param {Object} media Media object from the picker.
 * @return {Object} Trimmed image.
 */
const toImage = media => ({
    id: media.id,
    url: media.url,
    alt: media.alt || ''
});

const Edit = props => {
    const { attributes, setAttributes } = props;
    const { images, tallHeight, shortRatio, gap, radius, speed, direction, pauseOnHover } = attributes;

    const cssCustomProperties = {
        '--dnte-ticker-height': `${tallHeight}px`,
        '--dnte-ticker-short': `${shortRatio}`,
        '--dnte-ticker-gap': `${gap}px`,
        '--dnte-ticker-radius': `${radius}px`,
        '--dnte-ticker-speed': `${speed}s`
    };

    useEffect(() => {
        setAttributes({ blockStyle: cssCustomProperties });
    }, [tallHeight, shortRatio, gap, radius, speed]);

    const blockProps = useBlockProps({
        style: cssCustomProperties,
        className: classNames({
            [`is-direction-${direction}`]: direction,
            'is-pause-on-hover': pauseOnHover
        })
    });

    if (!images?.length) {
        return (
            <div {...blockProps}>
                <Inspector {...props} />
                <MediaPlaceholder
                    multiple
                    addToGallery={false}
                    labels={{
                        title: __('Ticker Gallery', 'dentist-exchange'),
                        instructions: __('Select the images to scroll.', 'dentist-exchange')
                    }}
                    accept="image/*"
                    allowedTypes={['image']}
                    onSelect={media => setAttributes({ images: media.map(toImage) })}
                />
            </div>
        );
    }

    return (
        <Fragment>
            <Inspector {...props} />
            <BlockControls>
                <ToolbarGroup>
                    <MediaReplaceFlow
                        multiple
                        addToGallery={false}
                        mediaIds={images.map(image => image.id)}
                        allowedTypes={['image']}
                        accept="image/*"
                        onSelect={media => setAttributes({ images: (Array.isArray(media) ? media : [media]).map(toImage) })}
                        name={__('Edit images', 'dentist-exchange')}
                    />
                    <ToolbarButton onClick={() => setAttributes({ images: [] })}>{__('Clear', 'dentist-exchange')}</ToolbarButton>
                </ToolbarGroup>
            </BlockControls>
            <div {...blockProps}>
                {/*
                 * One copy in the editor. The saved markup carries two so the
                 * marquee can loop seamlessly, but doubling it here would only
                 * make the canvas harder to work with.
                 */}
                <div className="dnte-ticker__track">
                    {images.map((image, i) => (
                        <div className={classNames('dnte-ticker__item', `is-v${(i % 4) + 1}`)} key={image.id || i}>
                            <img src={image.url} alt={image.alt || ''} />
                        </div>
                    ))}
                </div>
            </div>
        </Fragment>
    );
};

export default Edit;
