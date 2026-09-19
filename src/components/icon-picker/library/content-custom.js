/**
 * WordPress dependencies
 */
import { __, sprintf } from '@wordpress/i18n';
import { TextareaControl, TextControl, Button, Flex, Notice, RangeControl, Icon } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { getIconByName, icons, getIconType } from '../../../utils/icons';
import { MY_ICONS_STORE } from '../../../store/my-icons';

/**
 * ContentCustom Component
 * Displays custom SVG editor with preview and controls
 */
export const ContentCustom = ({
    tempCustomSvgCode,
    setTempCustomSvgCode,
    previewIconSize,
    setPreviewIconSize,
    previewStrokeWidth,
    setPreviewStrokeWidth,
    currentCustomSvg,
    currentIconName,
    insertCustomSVG,
    clearCustomSVG
}) => {
    const tempIconType = getIconType(tempCustomSvgCode);

    // "Add to My Icons" state.
    const [iconLabel, setIconLabel] = useState('');
    const [notice, setNotice] = useState(null);

    const isSaving = useSelect(select => select(MY_ICONS_STORE).isSavingMyIcon(), []);
    const { saveIcon } = useDispatch(MY_ICONS_STORE);

    // The SVG is stored exactly as it is previewed, stroke width included.
    const getPreparedSvgCode = () => {
        if (tempIconType === 'line' && previewStrokeWidth !== 1) {
            return tempCustomSvgCode.replace(/stroke-width="([^"]*)"/g, `stroke-width="${previewStrokeWidth}"`);
        }

        return tempCustomSvgCode;
    };

    const addToMyIcons = async () => {
        setNotice(null);

        try {
            const saved = await saveIcon({
                label: iconLabel.trim(),
                svg: getPreparedSvgCode(),
                iconType: tempIconType,
                strokeWidth: tempIconType === 'line' ? previewStrokeWidth : 1
            });

            setIconLabel('');
            setNotice({
                status: 'success',
                message: sprintf(
                    /* translators: %s: icon name. */
                    __('“%s” was added to My Icons.', 'dentist-exchange'),
                    saved.label
                )
            });
        } catch (error) {
            setNotice({
                status: 'error',
                message: error?.message || __('The icon could not be saved.', 'dentist-exchange')
            });
        }
    };

    // Function to render the current icon preview
    const renderCurrentIcon = (size, customCode, customStroke) => {
        const svgCode = customCode !== null ? customCode : currentCustomSvg;
        const currentType = getIconType(svgCode);

        if (svgCode) {
            let finalSvgCode = svgCode;

            if (currentType === 'line' && customStroke !== null) {
                finalSvgCode = svgCode.replace(/stroke-width="([^"]*)"/g, `stroke-width="${customStroke}"`);
            }

            return (
                <div
                    className="dnte-custom-svg-container"
                    dangerouslySetInnerHTML={{ __html: finalSvgCode }}
                    style={{ width: `${size}px`, height: `${size}px` }}
                />
            );
        }

        const selectedIcon = getIconByName(currentIconName);
        if (selectedIcon) {
            return <Icon icon={selectedIcon.icon} size={size} />;
        }

        return <Icon icon={icons[0].icon} size={size} />;
    };

    return (
        <div className="dnte-modal__custom-svg">
            <div className="dnte-modal__custom-svg-editor">
                <TextareaControl
                    label={__('Custom SVG code', 'dentist-exchange')}
                    value={tempCustomSvgCode}
                    onChange={setTempCustomSvgCode}
                    help={__('Paste your custom SVG code here. It will override the selected icon.', 'dentist-exchange')}
                    rows={15}
                />
            </div>

            <div className="dnte-modal__custom-svg-preview">
                <h3>{__('Preview', 'dentist-exchange')}</h3>
                <div className="dnte-icon-preview-container">
                    {tempCustomSvgCode ? (
                        renderCurrentIcon(previewIconSize, tempCustomSvgCode, previewStrokeWidth)
                    ) : (
                        <div className="dnte-empty-preview">{__('Enter SVG code to see preview', 'dentist-exchange')}</div>
                    )}
                </div>

                <div className="dnte-modal__custom-svg-controls">
                    <RangeControl
                        label={__('Icon Size', 'dentist-exchange')}
                        value={previewIconSize}
                        onChange={setPreviewIconSize}
                        min={16}
                        max={256}
                        __next40pxDefaultSize
                    />

                    {tempIconType === 'line' && (
                        <RangeControl
                            label={__('Stroke Width', 'dentist-exchange')}
                            value={previewStrokeWidth}
                            onChange={setPreviewStrokeWidth}
                            min={0.5}
                            max={5}
                            step={0.1}
                            __next40pxDefaultSize
                        />
                    )}
                </div>

                <div className="dnte-modal__custom-svg-save">
                    <TextControl
                        label={__('Icon name', 'dentist-exchange')}
                        value={iconLabel}
                        onChange={setIconLabel}
                        placeholder={__('e.g. Arrow Badge', 'dentist-exchange')}
                        help={__('Name this icon to save it to My Icons and reuse it anywhere on the site.', 'dentist-exchange')}
                        __next40pxDefaultSize
                        __nextHasNoMarginBottom
                    />

                    {!!notice && (
                        <Notice status={notice.status} onRemove={() => setNotice(null)} isDismissible>
                            {notice.message}
                        </Notice>
                    )}
                </div>

                <div className="dnte-modal__custom-svg-buttons">
                    <Flex>
                        <Button
                            __next40pxDefaultSize
                            variant="secondary"
                            onClick={clearCustomSVG}
                            isDestructive
                            style={{ flex: '1', justifyContent: 'center' }}
                        >
                            {__('Clear', 'dentist-exchange')}
                        </Button>
                        <Button
                            __next40pxDefaultSize
                            variant="secondary"
                            onClick={addToMyIcons}
                            isBusy={isSaving}
                            disabled={!tempCustomSvgCode || !iconLabel.trim() || isSaving}
                            style={{ flex: '1', justifyContent: 'center' }}
                        >
                            {__('Add to My Icons', 'dentist-exchange')}
                        </Button>
                    </Flex>
                    <Button
                        __next40pxDefaultSize
                        variant="primary"
                        onClick={insertCustomSVG}
                        disabled={!tempCustomSvgCode}
                        style={{ width: '100%', justifyContent: 'center', marginTop: '8px' }}
                    >
                        {__('Insert Custom Icon', 'dentist-exchange')}
                    </Button>
                </div>
            </div>
        </div>
    );
};
