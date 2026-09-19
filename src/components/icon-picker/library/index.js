/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Modal, __experimentalScrollable as Scrollable } from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { getCategories, icons, searchIcons, getIconType } from '../../../utils/icons';
import { Header } from './header';
import { Sidebar } from './sidebar';
import { ContentIcons } from './content-icons';
import { ContentCustom } from './content-custom';
import { ContentMyIcons } from './content-my-icons';

import './editor.scss';

/**
 * Library Component
 * Main icon library modal component
 */
export default function Library({
    onClose,
    onIconSelect,
    onCustomSvgInsert,
    currentIconName = '',
    currentCustomSvg = '',
    currentIconSize = 64,
    currentStrokeWidth = 1,
    modalState,
    setModalState
}) {
    const { isOpen, activeTab } = modalState;

    // Get icon categories
    const categories = getCategories();

    // Preview state
    const [previewIconSize, setPreviewIconSize] = useState(currentIconSize);
    const [previewStrokeWidth, setPreviewStrokeWidth] = useState(currentStrokeWidth);
    const [tempCustomSvgCode, setTempCustomSvgCode] = useState(currentCustomSvg);

    // Icon library state
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [filteredIcons, setFilteredIcons] = useState(icons);

    // Handle tab changes
    const onTabChange = tabValue => {
        setModalState(prev => ({
            ...prev,
            activeTab: tabValue
        }));
        setSelectedCategory('all');
    };

    // Effect to filter icons when search term or category changes
    useEffect(() => {
        if (activeTab === 'library') {
            let result = searchIcons(searchTerm);

            if (selectedCategory !== 'all') {
                result = result.filter(icon => icon.categories.includes(selectedCategory));
            }

            setFilteredIcons(result);
        }
    }, [searchTerm, selectedCategory, activeTab]);

    // Reset state when modal closes
    useEffect(() => {
        if (!isOpen) {
            setSearchTerm('');
            setSelectedCategory('all');
        }
    }, [isOpen]);

    // Handle icon selection
    const handleIconSelect = iconData => {
        if (onIconSelect) {
            onIconSelect(iconData);
        }
        onClose();
    };

    // Handle custom SVG operations
    const clearCustomSVG = () => {
        setTempCustomSvgCode('');
    };

    const insertCustomSVG = () => {
        let finalSvgCode = tempCustomSvgCode;
        const type = getIconType(tempCustomSvgCode);

        // Apply stroke width modifications if it's a line icon
        if (type === 'line' && previewStrokeWidth !== 1) {
            finalSvgCode = tempCustomSvgCode.replace(/stroke-width="([^"]*)"/g, `stroke-width="${previewStrokeWidth}"`);
        }

        if (onCustomSvgInsert) {
            onCustomSvgInsert({
                customSvgCode: finalSvgCode,
                iconType: type,
                strokeWidth: type === 'line' ? previewStrokeWidth : 1
            });
        }
        onClose();
    };

    // A saved icon is inserted as a copy of its SVG, exactly like a pasted one.
    const handleMyIconSelect = icon => {
        if (onCustomSvgInsert) {
            onCustomSvgInsert({
                customSvgCode: icon.svg,
                iconType: icon.iconType || getIconType(icon.svg),
                strokeWidth: icon.strokeWidth || 1
            });
        }
        onClose();
    };

    if (!isOpen) {
        return null;
    }

    return (
        <Modal
            overlayClassName="dnte-modal__overlay"
            className={`dnte-modal is-${activeTab}`}
            title={__('Icons Library', 'dentist-exchange')}
            onRequestClose={onClose}
            isFullScreen={true}
            headerActions={<Header activeTab={activeTab} onTabChange={onTabChange} />}
        >
            <div className="dnte-modal__container">
                <Sidebar categories={categories} category={selectedCategory} setCategory={setSelectedCategory} activeTab={activeTab} />
                <div className="dnte-modal__content">
                    <Scrollable className="dnte-modal__scrollable">
                        {activeTab === 'library' && (
                            <ContentIcons
                                searchTerm={searchTerm}
                                setSearchTerm={setSearchTerm}
                                filteredIcons={filteredIcons}
                                currentIconName={currentIconName}
                                handleIconSelect={handleIconSelect}
                            />
                        )}
                        {activeTab === 'my-icons' && (
                            <ContentMyIcons
                                currentCustomSvg={currentCustomSvg}
                                onIconSelect={handleMyIconSelect}
                                onGoToCustomTab={() => onTabChange('custom')}
                            />
                        )}
                        {activeTab === 'custom' && (
                            <ContentCustom
                                tempCustomSvgCode={tempCustomSvgCode}
                                setTempCustomSvgCode={setTempCustomSvgCode}
                                previewIconSize={previewIconSize}
                                setPreviewIconSize={setPreviewIconSize}
                                previewStrokeWidth={previewStrokeWidth}
                                setPreviewStrokeWidth={setPreviewStrokeWidth}
                                currentCustomSvg={currentCustomSvg}
                                currentIconName={currentIconName}
                                insertCustomSVG={insertCustomSVG}
                                clearCustomSVG={clearCustomSVG}
                            />
                        )}
                    </Scrollable>
                </div>
            </div>
        </Modal>
    );
}
