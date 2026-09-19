/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { registerFormatType, applyFormat, removeFormat } from '@wordpress/rich-text';
import { BlockControls, store as blockEditorStore } from '@wordpress/block-editor';
import { ToolbarButton } from '@wordpress/components';

import './style.scss';
import './sidebar';

const FORMAT_NAME = 'dnte/highlight';
const BASE_CLASS = 'dnte-highlight';

// Only these blocks expose the Highlight button. Format types have no native
// per-block restriction, so the toolbar button opts out for anything else.
const SUPPORTED_BLOCKS = ['core/heading', 'core/paragraph'];

const Edit = ({ value, onChange, isActive }) => {
    const isSupported = useSelect(select => SUPPORTED_BLOCKS.includes(select(blockEditorStore).getSelectedBlock()?.name), []);

    if (!isSupported) {
        return null;
    }

    // `group="other"` renders as its own ToolbarGroup after the inline formats,
    // rather than being collapsed into core's "More" dropdown alongside
    // core/text-color (also labelled "Highlight").
    return (
        <BlockControls group="other">
            <ToolbarButton
                icon="admin-appearance"
                label={__('Highlight', 'dentist-exchange')}
                onClick={() =>
                    onChange(
                        isActive
                            ? removeFormat(value, FORMAT_NAME)
                            : applyFormat(value, { type: FORMAT_NAME })
                    )
                }
                isActive={isActive}
            />
        </BlockControls>
    );
};

const highlightFormat = {
    title: __('Highlight', 'dentist-exchange'),
    tagName: 'span',
    className: BASE_CLASS,
    edit: Edit
};

registerFormatType(FORMAT_NAME, highlightFormat);
