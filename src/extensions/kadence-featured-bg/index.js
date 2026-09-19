/**
 * Kadence RowLayout — Featured Image as Background
 *
 * Adds a "Use Featured Image as Background" toggle to kadence/rowlayout
 * blocks. When enabled, the current post's featured image is used as the
 * background image, overriding Kadence's default background image setting.
 */
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { InspectorAdvancedControls } from '@wordpress/block-editor';
import { PanelBody } from '@wordpress/components';
import { createHigherOrderComponent } from '@wordpress/compose';
import { useSelect } from '@wordpress/data';

import { NativeToggleControl } from '../../components';

import './style.scss';

const BLOCK_NAME = 'kadence/rowlayout';
const ATTRIBUTE = 'useFeaturedImageAsBg';

/**
 * Add the useFeaturedImageAsBg attribute to kadence/rowlayout.
 */
addFilter('blocks.registerBlockType', 'dnte/kadence-featured-bg-add-attribute', (settings, name) => {
    if (name !== BLOCK_NAME) {
        return settings;
    }

    return {
        ...settings,
        attributes: {
            ...settings.attributes,
            [ATTRIBUTE]: {
                type: 'boolean',
                default: false
            }
        }
    };
});

/**
 * Add "Use Featured Image as Background" toggle to kadence/rowlayout inspector.
 */
addFilter(
    'editor.BlockEdit',
    'dnte/kadence-featured-bg-add-inspector-controls',
    createHigherOrderComponent(BlockEdit => {
        return props => {
            const { name, attributes, setAttributes } = props;

            if (name !== BLOCK_NAME) {
                return <BlockEdit {...props} />;
            }

            const featuredImageId = useSelect(select => {
                const postId = select('core/editor').getCurrentPostId();
                const post = select('core').getEntityRecord('postType', 'post', postId);
                return post?.featured_media ?? 0;
            }, []);

            const hasFeaturedImage = featuredImageId > 0;

            return (
                <>
                    <BlockEdit {...props} />
                    <InspectorAdvancedControls>
                        <NativeToggleControl
                            label={__('Use Featured Image as Background', 'dentist-exchange')}
                            checked={!!attributes[ATTRIBUTE]}
                            onChange={value => setAttributes({ [ATTRIBUTE]: value })}
                        />
                        {!hasFeaturedImage && (
                            <p style={{ color: '#999', fontSize: '12px', marginTop: '4px' }}>
                                {__('No featured image set for this post.', 'dentist-exchange')}
                            </p>
                        )}
                    </InspectorAdvancedControls>
                </>
            );
        };
    })
);

/**
 * Apply featured image background in the editor preview.
 */
addFilter(
    'editor.BlockListBlock',
    'dnte/kadence-featured-bg-add-styles',
    createHigherOrderComponent(BlockListBlock => {
        return props => {
            const { name, attributes } = props;

            if (name !== BLOCK_NAME || !attributes[ATTRIBUTE]) {
                return <BlockListBlock {...props} />;
            }

            const featuredImageUrl = useSelect(select => {
                const postId = select('core/editor').getCurrentPostId();
                const post = select('core').getEntityRecord('postType', 'post', postId);
                if (!post || !post.featured_media) return '';
                const media = select('core').getMedia(post.featured_media);
                return media?.source_url ?? '';
            }, []);

            if (!featuredImageUrl) {
                const classes = [props.className, 'has-featured-image-bg'].filter(Boolean).join(' ');
                return <BlockListBlock {...props} className={classes} />;
            }

            const wrapperProps = {
                ...props.wrapperProps,
                style: {
                    ...props.wrapperProps?.style,
                    '--dnte-featured-bg-image': `url(${featuredImageUrl})`
                }
            };

            const classes = [props.className, 'has-featured-image-bg'].filter(Boolean).join(' ');

            return <BlockListBlock {...props} className={classes} wrapperProps={wrapperProps} />;
        };
    })
);
