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
    const { image, imageTablet, imageMobile, itemStyle, showTitle, title, titleTag, showDesc, description, showBtn, btnLabel } = attributes;

    // Only breakpoints with their own image are written out; style.scss
    // cascades each unset one up to the next larger — mobile falls back to
    // tablet, tablet to desktop.
    const imageCustomProperties = {
        ...(image?.url && { '--dimg': `url("${image.url}")` }),
        ...(imageTablet?.url && { '--timg': `url("${imageTablet.url}")` }),
        ...(imageMobile?.url && { '--mimg': `url("${imageMobile.url}")` })
    };

    useEffect(() => {
        setAttributes({ itemStyle: imageCustomProperties });
    }, [image?.url, imageTablet?.url, imageMobile?.url]);

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
                {/*
                 * The image is painted as a background so the tablet and
                 * mobile variants can be swapped by media query alone. The
                 * editor canvas is desktop-width, so it previews --dimg.
                 */}
                <div className="img" style={imageCustomProperties}>
                    {!image?.url && (
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
                        {/*
                         * `allowedFormats={[]}` permits no inline formats at
                         * all — no bold, italic, links or pasted markup — while
                         * line breaks stay enabled, so Shift+Enter is the one
                         * piece of HTML these fields accept. Anything pasted in
                         * is stripped to plain text plus its breaks.
                         */}
                        {showTitle && (
                            <RichText
                                tagName={titleTag || 'h4'}
                                className="heading"
                                value={title}
                                onChange={value => setAttributes({ title: value })}
                                placeholder={__('Accordion title..', 'dentist-exchange')}
                                allowedFormats={[]}
                                withoutInteractiveFormatting
                            />
                        )}
                        {showDesc && (
                            <RichText
                                tagName="p"
                                className="description"
                                value={description}
                                onChange={value => setAttributes({ description: value })}
                                placeholder={__('Accordion description..', 'dentist-exchange')}
                                allowedFormats={[]}
                                withoutInteractiveFormatting
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
