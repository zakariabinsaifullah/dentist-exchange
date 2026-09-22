// import editor style
import './editor.scss';

/**
 * WordPress Dependencies
 */
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';

/**
 * Internal Dependencies
 */
import Inspector from './inspector';
import { getArrow } from '../story-card/edit';

const ALLOWED_BLOCKS = ['dnte/story-card'];

/**
 * The four-card composition of the design, measured off the artwork.
 *
 * Lanes run right, left, right, left — the right-hand card leads, and the
 * left-hand one below it sits a little lower. Every number is a percentage:
 * `offsetY` is the card's top, measured from the section's top as a share of
 * the section's *width* (the design's own coordinate system, applied by
 * view.js); the arrow's `left`/`width` are shares of the card's width, its
 * `top` of the card's height. The icon sits on whichever corner faces the
 * gutter, except on the last card, which the design puts on the left.
 */
const TEMPLATE = [
    [
        'dnte/story-card',
        { lane: 'right', offsetY: 0, badgeSide: 'left', arrow: 'hook-down', arrowLeft: 60, arrowTop: -29.2, arrowWidth: 49.3 }
    ],
    [
        'dnte/story-card',
        { lane: 'left', offsetY: 11.6, badgeSide: 'right', arrow: 'squiggle-down', arrowLeft: 104.7, arrowTop: -4.5, arrowWidth: 22 }
    ],
    [
        'dnte/story-card',
        { lane: 'right', offsetY: 64.2, badgeSide: 'left', arrow: 'elbow-right', arrowLeft: -70.7, arrowTop: 20.7, arrowWidth: 54 }
    ],
    [
        'dnte/story-card',
        { lane: 'left', offsetY: 113, badgeSide: 'left', arrow: 'hook-up', arrowLeft: 116, arrowTop: 28.5, arrowWidth: 49.3 }
    ]
];

// block edit function
const Edit = props => {
    const { attributes } = props;
    const { cardWidth, laneGap, trailingArrow, trailingLeft, trailingOffset, trailingWidth, trailingFlipX } = attributes;

    const trailing = getArrow(trailingArrow);

    const blockProps = useBlockProps({
        className: 'dnte-story-cards',
        style: {
            '--dnte-card-width': `${cardWidth}%`,
            '--dnte-lane-gap': `${laneGap}px`
        }
    });

    const innerBlocksProps = useInnerBlocksProps(blockProps, {
        allowedBlocks: ALLOWED_BLOCKS,
        template: TEMPLATE,
        orientation: 'vertical'
    });

    // The canvas previews the trailing arrow fully drawn; the wipe only runs on
    // the frontend, where view.js loads.
    const { children, ...rest } = innerBlocksProps;

    return (
        <Fragment>
            <Inspector {...props} />
            <div {...rest}>
                {children}
                {trailing && (
                    <span
                        className="dnte-story-cards__trailing"
                        style={{
                            '--dnte-trailing-left': `${trailingLeft}%`,
                            '--dnte-trailing-offset': `${trailingOffset}%`,
                            '--dnte-trailing-width': `${trailingWidth}%`,
                            '--dnte-trailing-flip': trailingFlipX ? '-1' : '1'
                        }}
                        aria-hidden="true"
                    >
                        <span className="dnte-story-card__connector-art" dangerouslySetInnerHTML={{ __html: trailing.svg }} />
                    </span>
                )}
            </div>
        </Fragment>
    );
};

export default Edit;
