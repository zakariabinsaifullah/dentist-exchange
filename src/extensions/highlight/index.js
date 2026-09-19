/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { registerFormatType, applyFormat, removeFormat, useAnchor } from '@wordpress/rich-text';
import { BlockControls, store as blockEditorStore } from '@wordpress/block-editor';
import {
    Popover,
    Button,
    ToolbarButton,
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption
} from '@wordpress/components';
import { SVG, Path } from '@wordpress/primitives';

import './style.scss';
import './editor.scss';

const FORMAT_NAME = 'dnte/highlight';
const BASE_CLASS = 'dnte-highlight';

// Only these blocks expose the Highlight button. Format types have no native
// per-block restriction, so the toolbar button opts out for anything else.
const SUPPORTED_BLOCKS = ['core/heading', 'core/paragraph'];

const WEIGHTS = ['400', '600'];
const DEFAULT_WEIGHT = '400';

const weightClass = weight => `${BASE_CLASS}--${weight}`;

/**
 * Reads the active weight back off the format's `class` attribute.
 * The base class is consumed by the format parser, so `activeAttributes.class`
 * holds only the modifier (e.g. `dnte-highlight--600`).
 *
 * @param {Object} activeAttributes Attributes of the active format.
 * @return {string} One of WEIGHTS.
 */
const getActiveWeight = activeAttributes => {
    const classes = (activeAttributes?.class || '').split(' ');
    return WEIGHTS.find(weight => classes.includes(weightClass(weight))) || DEFAULT_WEIGHT;
};

const Edit = ({ value, onChange, isActive, activeAttributes, contentRef }) => {
    const [isOpen, setIsOpen] = useState(false);

    const isSupported = useSelect(select => SUPPORTED_BLOCKS.includes(select(blockEditorStore).getSelectedBlock()?.name), []);

    // `useAnchor` must run before any early return to keep hook order stable.
    const popoverAnchor = useAnchor({
        editableContentElement: contentRef?.current,
        settings: highlightFormat
    });

    if (!isSupported) {
        return null;
    }

    const activeWeight = getActiveWeight(activeAttributes);

    const applyWeight = weight => {
        onChange(
            applyFormat(value, {
                type: FORMAT_NAME,
                attributes: { class: weightClass(weight) }
            })
        );
    };

    const onToolbarClick = () => {
        // Clicking highlights the selection straight away; the popover is only
        // there to switch weight afterwards.
        if (!isActive) {
            applyWeight(DEFAULT_WEIGHT);
        }
        setIsOpen(true);
    };

    return (
        <>
            {/*
             * `other` renders as its own ToolbarGroup after the inline formats,
             * rather than being collapsed into core's "More" dropdown alongside
             * core/text-color (also labelled "Highlight").
             */}
            <BlockControls group="other">
                <ToolbarButton
                    icon="admin-appearance"
                    label={__('Highlight', 'dentist-exchange')}
                    onClick={onToolbarClick}
                    isActive={isActive}
                />
            </BlockControls>
            {isOpen && (
                <Popover
                    anchor={popoverAnchor}
                    onClose={() => setIsOpen(false)}
                    onFocusOutside={() => setIsOpen(false)}
                    placement="bottom-start"
                    className="dnte-highlight__popover"
                >
                    <div className="dnte-highlight__panel">
                        <ToggleGroupControl
                            label={__('Font weight', 'dentist-exchange')}
                            value={activeWeight}
                            onChange={applyWeight}
                            isBlock
                            __nextHasNoMarginBottom
                            __next40pxDefaultSize
                        >
                            {WEIGHTS.map(weight => (
                                <ToggleGroupControlOption key={weight} value={weight} label={weight} />
                            ))}
                        </ToggleGroupControl>
                        <Button
                            variant="tertiary"
                            isDestructive
                            disabled={!isActive}
                            __next40pxDefaultSize
                            onClick={() => {
                                onChange(removeFormat(value, FORMAT_NAME));
                                setIsOpen(false);
                            }}
                        >
                            {__('Remove highlight', 'dentist-exchange')}
                        </Button>
                    </div>
                </Popover>
            )}
        </>
    );
};

/**
 * Declared as a standalone object so `useAnchor` above can receive the same
 * settings WordPress registers the format with.
 */
const highlightFormat = {
    title: __('Highlight', 'dentist-exchange'),
    tagName: 'span',
    className: BASE_CLASS,
    // Mirrors core/text-color: `className` identifies the format, while the
    // `class` attribute carries the variable modifier.
    attributes: {
        class: 'class'
    },
    edit: Edit
};

registerFormatType(FORMAT_NAME, highlightFormat);
