// import editor style
import './editor.scss';

/**
 * WordPress Dependencies
 */
import {
    RichText,
    useBlockProps,
    MediaPlaceholder,
    __experimentalGetBorderClassesAndStyles as getBorderClassesAndStyles,
    __experimentalGetColorClassesAndStyles as getColorClassesAndStyles,
    __experimentalGetSpacingClassesAndStyles as getSpacingClassesAndStyles,
    __experimentalGetShadowClassesAndStyles as getShadowClassesAndStyles
} from '@wordpress/block-editor';
import { Fragment, useEffect, useRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

/**
 * External Dependencies
 */
import classNames from 'classnames';

/**
 * Internal Dependencies
 */
import Inspector from './inspector';

// block edit function
const Edit = props => {
    const { attributes, setAttributes, context, clientId } = props;
    const { image, showTitle, title, titleTag, showDesc, description, showBtn, btnLabel } = attributes;

    // Sync the parent's toggles into this item's own attributes so the
    // frontend (which never sees block context) renders the same thing.
    // WordPress rebuilds the `context` object on most editor interactions
    // even when the underlying values haven't changed, so this only calls
    // setAttributes when a value actually differs — otherwise the resulting
    // attribute update re-triggers this same effect and the item flickers.
    const nextShowTitle = context['dnte/showTitle'];
    const nextShowDesc = context['dnte/showDesc'];
    const nextShowBtn = context['dnte/showBtn'];
    const nextTitleTag = context['dnte/titleTag'];

    useEffect(() => {
        if (showTitle === nextShowTitle && showDesc === nextShowDesc && showBtn === nextShowBtn && titleTag === nextTitleTag) {
            return;
        }

        setAttributes({
            showTitle: nextShowTitle,
            showDesc: nextShowDesc,
            showBtn: nextShowBtn,
            titleTag: nextTitleTag
        });
    }, [nextShowTitle, nextShowDesc, nextShowBtn, nextTitleTag, showTitle, showDesc, showBtn, titleTag]);

    const borderProps = getBorderClassesAndStyles(attributes);
    const colorProps = getColorClassesAndStyles(attributes);
    const spacingProps = getSpacingClassesAndStyles(attributes);
    const shadowProps = getShadowClassesAndStyles(attributes);

    // The first item starts expanded, matching the frontend's default state.
    const isFirstItem = useSelect(select => select('core/block-editor').getBlockIndex(clientId) === 0, [clientId]);

    const blockProps = useBlockProps({
        className: classNames(colorProps.className, borderProps.className, spacingProps.className, shadowProps.className, {
            active: isFirstItem
        }),
        style: {
            ...borderProps.style,
            ...colorProps.style,
            ...spacingProps.style,
            ...shadowProps.style
        }
    });

    // Toggle the "active" (expanded) state when the item is clicked, so the
    // accordion behaves the same way in the editor as it does on the frontend.
    const itemRef = useRef(null);
    useEffect(() => {
        const item = itemRef.current;
        if (!item) {
            return;
        }
        const toggleActive = () => item.classList.toggle('active');
        item.addEventListener('click', toggleActive);
        return () => item.removeEventListener('click', toggleActive);
    }, []);

    return (
        <Fragment>
            <Inspector {...props} />
            <div {...blockProps} ref={itemRef}>
                <div className="img">
                    {image?.url ? (
                        <img src={image.url} alt={image.alt || ''} className="img-cover" />
                    ) : (
                        <MediaPlaceholder
                            labels={{ title: __('Accordion Image', 'dentist-exchange') }}
                            onSelect={media => setAttributes({ image: { id: media.id, url: media.url, alt: media.alt } })}
                            accept="image/*"
                            allowedTypes={['image']}
                        />
                    )}
                </div>
                <div className="info">
                    <div className="top-content">
                        {showTitle && (
                            <RichText
                                tagName={titleTag || 'h4'}
                                className="heading"
                                value={title}
                                onChange={value => setAttributes({ title: value })}
                                placeholder={__('Accordion title..', 'dentist-exchange')}
                            />
                        )}
                        {showDesc && (
                            <RichText
                                tagName="p"
                                className="description"
                                value={description}
                                onChange={value => setAttributes({ description: value })}
                                placeholder={__('Accordion description..', 'dentist-exchange')}
                            />
                        )}
                    </div>
                    {showBtn && (
                        <a href="#" className="btn" onClick={e => e.preventDefault()}>
                            {btnLabel}
                        </a>
                    )}
                </div>
            </div>
        </Fragment>
    );
};

export default Edit;
