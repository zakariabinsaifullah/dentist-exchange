/**
 * Annotation format — hand-drawn strokes over a heading selection.
 *
 * A RichText format rather than a block extension, because the design
 * annotates *part* of a heading ("Clear, flat-fee pricing. <em>Zero
 * commissions.</em>"). A block attribute could only ever apply to the whole
 * block.
 *
 * Each instance carries its own variant and colour as format attributes, so a
 * single heading can hold two differently styled annotations. (The sibling
 * highlight extension stores its colour as a *block* attribute instead, which
 * is why it is one colour per block — deliberately not reused here.)
 *
 * The stroke itself is a real inline <svg>, spliced in at render time by
 * dnte_render_annotation() in inc/annotations.php; the Format API cannot emit
 * child nodes into a RichText value without them becoming editable content.
 * The editor therefore previews the same artwork as a CSS mask — see
 * dnte_annotation_editor_preview_css().
 */

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { registerFormatType, applyFormat, removeFormat, useAnchor, isCollapsed } from '@wordpress/rich-text';
import { BlockControls, store as blockEditorStore } from '@wordpress/block-editor';
import { ToolbarButton, Popover, ColorPalette, Button, BaseControl } from '@wordpress/components';

import './style.scss';
import './editor.scss';

const FORMAT_NAME = 'dnte/annotation';
const BASE_CLASS = 'dnte-annotation';
const COLOR_VARIABLE = '--dnte-annotation-color';

// Format types have no native per-block restriction, so the toolbar button
// opts out for anything else.
const SUPPORTED_BLOCKS = ['core/heading', 'dnte/story-card'];

const annotationIcon = (
    <svg viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
        <path fill="currentColor" d="M7 5h10v1.6h-4.2V15h-1.6V6.6H7V5z" />
        <path
            fill="none"
            stroke="currentColor"
            strokeWidth="1.5"
            strokeLinecap="round"
            d="M5 18.2c3.2-1 6.4-1.4 9.6-1.2 1.6.1 3 .4 4.4.8"
        />
    </svg>
);

/**
 * Stroke variants, injected by dnte_enqueue_annotation_editor_assets() from
 * the .svg files in assets/svg/annotations/. Empty until artwork is added.
 *
 * @return {Array<{slug: string, label: string}>} Available variants.
 */
const getVariants = () => (Array.isArray(window.dnteAnnotations) ? window.dnteAnnotations : []);

/**
 * Reads the colour back out of the format's inline `style` attribute.
 *
 * @param {string} style Raw style attribute value.
 * @return {string} Stored colour, or '' when unset.
 */
const parseColor = style => {
    const match = new RegExp(`${COLOR_VARIABLE}:\\s*([^;]+)`).exec(style || '');
    return match ? match[1].trim() : '';
};

/**
 * Prefers a preset CSS variable over a raw hex, so annotations follow the
 * palette if it is ever retuned.
 *
 * @param {string} color   Colour as returned by ColorPalette.
 * @param {Array}  palette Theme palette entries.
 * @return {string} A CSS colour value.
 */
const toCssColor = (color, palette) => {
    const preset = palette.find(item => item.color === color);
    return preset ? `var(--wp--preset--color--${preset.slug})` : color;
};

/**
 * Inverse of `toCssColor` — ColorPalette needs a literal colour to mark the
 * matching swatch as selected.
 *
 * @param {string} stored  Stored CSS colour value.
 * @param {Array}  palette Theme palette entries.
 * @return {string|undefined} Literal colour.
 */
const fromCssColor = (stored, palette) => {
    const match = /^var\(--wp--preset--color--([\w-]+)\)$/.exec(stored || '');

    if (!match) {
        return stored || undefined;
    }

    const preset = palette.find(item => item.slug === match[1]);
    return preset ? preset.color : undefined;
};

const Edit = ({ value, onChange, isActive, activeAttributes, contentRef }) => {
    const [isOpen, setIsOpen] = useState(false);

    const isSupported = useSelect(select => SUPPORTED_BLOCKS.includes(select(blockEditorStore).getSelectedBlock()?.name), []);

    // Same normalisation as src/extensions/highlight/sidebar.js — the palette
    // setting is either a flat array or split into theme/custom/default.
    const paletteColors = useSelect(select => {
        const settings = select(blockEditorStore).getSettings();
        const paletteSetting = settings?.color?.palette || settings?.__experimentalFeatures?.color?.palette;

        if (!paletteSetting) {
            return [];
        }

        if (Array.isArray(paletteSetting)) {
            return paletteSetting;
        }

        const { theme = [], custom = [], default: defaultColors = [] } = paletteSetting;
        return [...theme, ...custom, ...defaultColors];
    }, []);

    // Anchors the popover to the annotated text rather than the toolbar, the
    // same way core anchors the link editor. Must run before the early return
    // below to keep hook order stable.
    const popoverAnchor = useAnchor({
        editableContentElement: contentRef?.current,
        settings: annotationFormat
    });

    if (!isSupported) {
        return null;
    }

    const variants = getVariants();
    const activeVariant = activeAttributes?.variant || '';
    const activeColor = parseColor(activeAttributes?.style);

    // With nothing selected there is no range to annotate. Editing an existing
    // annotation from a collapsed caret is fine — applyFormat expands to the
    // format's own boundaries in that case.
    const isDisabled = isCollapsed(value) && !isActive;

    const apply = (variant, color) => {
        if (!variant) {
            return;
        }

        const attributes = { variant };
        const cssColor = color ? toCssColor(color, paletteColors) : '';

        if (cssColor) {
            attributes.style = `${COLOR_VARIABLE}:${cssColor}`;
        }

        onChange(applyFormat(value, { type: FORMAT_NAME, attributes }));
    };

    const remove = () => {
        onChange(removeFormat(value, FORMAT_NAME));
        setIsOpen(false);
    };

    return (
        <>
            {/*
             * `group="other"` renders as its own ToolbarGroup after the inline
             * formats instead of collapsing into core's "More" dropdown.
             */}
            <BlockControls group="other">
                <ToolbarButton
                    icon={annotationIcon}
                    label={__('Annotation', 'dentist-exchange')}
                    onClick={() => setIsOpen(open => !open)}
                    isActive={isActive}
                    disabled={isDisabled}
                    aria-expanded={isOpen}
                />
            </BlockControls>
            {isOpen && (
                <Popover
                    anchor={popoverAnchor}
                    className="dnte-annotation-popover"
                    placement="bottom"
                    focusOnMount="firstElement"
                    onClose={() => setIsOpen(false)}
                >
                    <div className="dnte-annotation-popover__inner">
                        {!variants.length && (
                            <p className="dnte-annotation-popover__empty">
                                {__('No stroke artwork found. Add .svg files to assets/svg/annotations/.', 'dentist-exchange')}
                            </p>
                        )}
                        {!!variants.length && (
                            <>
                                <BaseControl.VisualLabel>{__('Stroke', 'dentist-exchange')}</BaseControl.VisualLabel>
                                <div className="dnte-annotation-popover__grid">
                                    {variants.map(variant => (
                                        <button
                                            key={variant.slug}
                                            type="button"
                                            className={`dnte-annotation-swatch${activeVariant === variant.slug ? ' is-selected' : ''}`}
                                            data-annotation={variant.slug}
                                            aria-label={variant.label}
                                            aria-pressed={activeVariant === variant.slug}
                                            onClick={() => apply(variant.slug, activeColor)}
                                        />
                                    ))}
                                </div>
                                <BaseControl.VisualLabel>{__('Colour', 'dentist-exchange')}</BaseControl.VisualLabel>
                                <ColorPalette
                                    colors={paletteColors}
                                    value={fromCssColor(activeColor, paletteColors)}
                                    clearable
                                    onChange={color => apply(activeVariant || variants[0].slug, color)}
                                />
                            </>
                        )}
                        {isActive && (
                            <Button className="dnte-annotation-popover__remove" variant="tertiary" isDestructive onClick={remove}>
                                {__('Remove annotation', 'dentist-exchange')}
                            </Button>
                        )}
                    </div>
                </Popover>
            )}
        </>
    );
};

const annotationFormat = {
    title: __('Annotation', 'dentist-exchange'),
    tagName: 'span',
    className: BASE_CLASS,
    // Per-instance state. `variant` picks the artwork; `style` carries the
    // colour custom property that both the SVG and the editor preview read.
    attributes: {
        variant: 'data-annotation',
        style: 'style'
    },
    edit: Edit
};

registerFormatType(FORMAT_NAME, annotationFormat);
