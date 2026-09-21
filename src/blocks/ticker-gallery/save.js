/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * The tilt/height cycle is applied as an explicit class from the image's
 * position *within its copy*, not with :nth-child across the whole track.
 *
 * The track holds the images twice and the marquee translates it by half its
 * width, so the two copies have to be identical for the loop to have no seam.
 * :nth-child would keep counting into the second copy and, unless the image
 * count happened to be a multiple of four, would style it differently — and
 * the loop point would visibly jump.
 *
 * @param {Object} image Image attribute ({id, url, alt}).
 * @param {number} index Position within the copy.
 * @param {string} keyPrefix Distinguishes the two copies.
 * @param {boolean} hidden Whether this copy is hidden from assistive tech.
 * @return {WPElement} Tile.
 */
const Tile = (image, index, keyPrefix, hidden) => (
    <div
        className={classNames('dnte-ticker__item', `is-v${(index % 4) + 1}`)}
        key={`${keyPrefix}-${image.id || index}`}
        {...(hidden ? { 'aria-hidden': 'true' } : {})}
    >
        <img
            src={image.url}
            alt={image.alt || ''}
            className={classNames({ [`wp-image-${image.id}`]: image.id })}
            loading="lazy"
            decoding="async"
        />
    </div>
);

const Save = ({ attributes }) => {
    const { images, blockStyle, direction, pauseOnHover } = attributes;

    const blockProps = useBlockProps.save({
        style: blockStyle,
        className: classNames({
            [`is-direction-${direction}`]: direction,
            'is-pause-on-hover': pauseOnHover
        })
    });

    if (!images?.length) {
        return null;
    }

    return (
        <div {...blockProps}>
            <div className="dnte-ticker__track">
                {images.map((image, i) => Tile(image, i, 'a', false))}
                {images.map((image, i) => Tile(image, i, 'b', true))}
            </div>
        </div>
    );
};

export default Save;
