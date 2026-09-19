import { __, sprintf } from '@wordpress/i18n';
import { InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import {
    Button,
    PanelBody,
    GradientPicker,
    __experimentalToolsPanel as ToolsPanel, // eslint-disable-line
    __experimentalToolsPanelItem as ToolsPanelItem // eslint-disable-line
} from '@wordpress/components';
import { chevronUp, chevronDown, closeSmall } from '@wordpress/icons';
import { useState } from '@wordpress/element';

import {
    NativeToggleGroupControl,
    NativeRangeControl,
    NativeToggleControl,
    PanelColorControl,
    NativeResponsiveControl,
    NativeUnitControl,
    NativeIconPicker,
    NativeBoxControl,
    NativeBorderBoxControl
} from '../../components';
import { resolveResponsive, toSlide, mergeSelection, slideThumb } from './utils';

/**
 * Tablet and Mobile inherit Desktop until they are given a value of their own,
 * so once one is pinned it needs a way back to the inherited value.
 */
const InheritReset = ({ device, value, onReset }) => {
    if (device === 'Desktop' || value === undefined || value === '') {
        return null;
    }

    return (
        <Button variant="link" onClick={onReset}>
            {__('Use desktop value', 'dentist-exchange')}
        </Button>
    );
};

/**
 * One of a slide's three image slots. Only the desktop slot is ever filled on
 * its own — the other two show what they are inheriting until they are set.
 */
const MediaSlot = ({ label, url, inheritedFrom, onSelect, onClear }) => (
    <div className="dnte-hero-slot">
        <div className="dnte-hero-slot__preview">
            {url ? <img src={url} alt="" /> : <span className="dnte-hero-slot__empty" />}
        </div>
        <div className="dnte-hero-slot__body">
            <span className="dnte-hero-slot__label">{label}</span>
            {!url && inheritedFrom && <span className="dnte-hero-slot__hint">{inheritedFrom}</span>}
        </div>
        <MediaUploadCheck>
            <MediaUpload
                onSelect={onSelect}
                allowedTypes={['image']}
                render={({ open }) => (
                    <Button variant="tertiary" size="small" onClick={open}>
                        {url ? __('Replace', 'dentist-exchange') : __('Set', 'dentist-exchange')}
                    </Button>
                )}
            />
        </MediaUploadCheck>
        {url && onClear && <Button size="small" icon={closeSmall} label={__('Clear', 'dentist-exchange')} onClick={onClear} />}
    </div>
);

/**
 * The media modal returns images in library order, not the order they were
 * clicked, so the running order is managed here instead. Each row opens up to
 * reveal the slide's mobile and thumbnail overrides.
 */
const SlideList = ({ slides, setAttributes }) => {
    const [openIndex, setOpenIndex] = useState(null);

    const update = (index, changes) => {
        setAttributes({
            slides: slides.map((slide, i) => (i === index ? { ...slide, ...changes } : slide))
        });
    };

    const move = (from, to) => {
        if (to < 0 || to > slides.length - 1) {
            return;
        }

        const next = [...slides];
        const [moved] = next.splice(from, 1);
        next.splice(to, 0, moved);
        setAttributes({ slides: next });
        setOpenIndex(null);
    };

    const remove = index => {
        setAttributes({ slides: slides.filter((slide, i) => i !== index) });
        setOpenIndex(null);
    };

    return (
        <ul className="dnte-hero-slide-list">
            {slides.map((slide, index) => {
                const isOpen = openIndex === index;

                return (
                    <li className="dnte-hero-slide-list__item" key={slide.id || index}>
                        <div className="dnte-hero-slide-list__row">
                            <img src={slideThumb(slide)} alt="" />
                            <span className="dnte-hero-slide-list__label">
                                {sprintf(
                                    /* translators: %d: image number within the hero slider. */
                                    __('Image %d', 'dentist-exchange'),
                                    index + 1
                                )}
                            </span>
                            <Button
                                size="small"
                                icon={chevronUp}
                                label={__('Move up', 'dentist-exchange')}
                                disabled={index === 0}
                                onClick={() => move(index, index - 1)}
                            />
                            <Button
                                size="small"
                                icon={chevronDown}
                                label={__('Move down', 'dentist-exchange')}
                                disabled={index === slides.length - 1}
                                onClick={() => move(index, index + 1)}
                            />
                            <Button
                                size="small"
                                icon={isOpen ? chevronUp : chevronDown}
                                label={isOpen ? __('Hide images', 'dentist-exchange') : __('Edit images', 'dentist-exchange')}
                                isPressed={isOpen}
                                onClick={() => setOpenIndex(isOpen ? null : index)}
                            />
                            <Button size="small" icon={closeSmall} label={__('Remove', 'dentist-exchange')} onClick={() => remove(index)} />
                        </div>

                        {isOpen && (
                            <div className="dnte-hero-slide-list__slots">
                                <MediaSlot
                                    label={__('Desktop', 'dentist-exchange')}
                                    url={slide.url}
                                    onSelect={image => {
                                        const next = toSlide(image);

                                        /**
                                         * `toSlide` rebuilds `thumb` from the new image, which is
                                         * right when the strip is following the desktop slot but
                                         * would silently throw away a thumbnail the author chose
                                         * by hand — so a custom one is carried across.
                                         */
                                        update(index, slide.thumbId ? { ...next, thumb: slide.thumb } : next);
                                    }}
                                />
                                <MediaSlot
                                    label={__('Mobile', 'dentist-exchange')}
                                    url={slide.mobileUrl}
                                    inheritedFrom={__('Uses the desktop image', 'dentist-exchange')}
                                    onSelect={image => update(index, { mobileId: image.id, mobileUrl: image.url })}
                                    onClear={() => update(index, { mobileId: undefined, mobileUrl: undefined })}
                                />
                                <MediaSlot
                                    label={__('Thumbnail', 'dentist-exchange')}
                                    url={slide.thumbId ? slide.thumb : undefined}
                                    inheritedFrom={__('Cropped from the desktop image', 'dentist-exchange')}
                                    onSelect={image => update(index, { thumbId: image.id, thumb: image.url })}
                                    onClear={() => update(index, { thumbId: undefined, thumb: slide.autoThumb || slide.url })}
                                />
                            </div>
                        )}
                    </li>
                );
            })}
        </ul>
    );
};

const Inspector = props => {
    const { attributes, setAttributes, onChangeMode } = props;
    const {
        slides = [],
        contentMode,
        resMode,
        heightType,
        heights,
        vAligns,
        effect,
        speed,
        autoplay,
        delay,
        loop,
        overlayColor,
        overlayGradient,
        overlayOpacity,
        contentWidths,
        contentAlign,
        contentPosition,
        contentPadding,
        constrainThumbs,
        showThumbs,
        thumbWidths,
        thumbHeights,
        thumbGaps,
        thumbsAlign,
        thumbsOffset,
        thumbRadius,
        thumbOpacity,
        thumbBorderColor,
        thumbBorderWidth,
        showArrows,
        navColor,
        navbgColor,
        navSize,
        navIconSize,
        navGap,
        navBorder,
        navBorderRadius,
        navPadding,
        prevIconName,
        prevCustomSvg,
        nextIconName,
        nextCustomSvg
    } = attributes;

    const perSlide = 'per-slide' === contentMode;

    /* The parent owns switching, so the placeholder and this toggle behave alike. */
    const switchMode = onChangeMode || (value => setAttributes({ contentMode: value }));

    // What the device currently in preview actually renders with.
    const currentHeightType = resolveResponsive(heightType, resMode) || 'adaptive';
    const currentHeight = resolveResponsive(heights, resMode) || '';
    const currentVAlign = resolveResponsive(vAligns, resMode) || 'middle';
    const currentContentWidth = resolveResponsive(contentWidths, resMode) || '';
    const currentThumbWidth = resolveResponsive(thumbWidths, resMode) || '';
    const currentThumbHeight = resolveResponsive(thumbHeights, resMode) || '';
    const currentThumbGap = resolveResponsive(thumbGaps, resMode) ?? 20;

    const setResponsive = (key, values, value) => setAttributes({ [key]: { ...values, [resMode]: value } });
    const resetResponsive = (key, values) => setAttributes({ [key]: { ...values, [resMode]: undefined } });

    return (
        <>
            <InspectorControls group="settings">
                <PanelBody title={__('Content', 'dentist-exchange')} initialOpen={true}>
                    <NativeToggleGroupControl
                        label={__('Content Mode', 'dentist-exchange')}
                        value={contentMode || 'fixed'}
                        onChange={switchMode}
                        options={[
                            { label: __('Fixed', 'dentist-exchange'), value: 'fixed' },
                            { label: __('Per slide', 'dentist-exchange'), value: 'per-slide' }
                        ]}
                    />
                    <p className="dnte-hero-mode-hint">
                        {perSlide
                            ? __('Each slide carries its own images and its own content.', 'dentist-exchange')
                            : __('One set of content stays in place while the background images slide behind it.', 'dentist-exchange')}
                    </p>
                </PanelBody>

                {!perSlide && (
                <PanelBody title={__('Images', 'dentist-exchange')} initialOpen={true}>
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={images => setAttributes({ slides: mergeSelection(images, slides) })}
                            allowedTypes={['image']}
                            multiple
                            gallery
                            value={slides.map(slide => slide.id)}
                            render={({ open }) => (
                                <Button variant="secondary" onClick={open} style={{ marginBottom: '12px' }}>
                                    {slides.length ? __('Add or remove images', 'dentist-exchange') : __('Select images', 'dentist-exchange')}
                                </Button>
                            )}
                        />
                    </MediaUploadCheck>
                    {!!slides.length && <SlideList slides={slides} setAttributes={setAttributes} />}
                </PanelBody>
                )}

                <PanelBody title={__('Layout', 'dentist-exchange')} initialOpen={false}>
                    <NativeResponsiveControl label={__('Height Type', 'dentist-exchange')} props={props}>
                        <NativeToggleGroupControl
                            value={currentHeightType}
                            onChange={value => setResponsive('heightType', heightType, value)}
                            options={[
                                { label: __('Adaptive', 'dentist-exchange'), value: 'adaptive' },
                                { label: __('Fixed', 'dentist-exchange'), value: 'fixed' }
                            ]}
                        />
                        <InheritReset
                            device={resMode}
                            value={heightType?.[resMode]}
                            onReset={() => resetResponsive('heightType', heightType)}
                        />
                    </NativeResponsiveControl>
                    {currentHeightType === 'fixed' && (
                        <NativeResponsiveControl label={__('Height', 'dentist-exchange')} props={props}>
                            <NativeUnitControl
                                label={__('Hero Height', 'dentist-exchange')}
                                value={currentHeight}
                                onChange={value => setResponsive('heights', heights, value)}
                                units={[
                                    { label: 'px', value: 'px' },
                                    { label: 'vh', value: 'vh' },
                                    { label: 'rem', value: 'rem' }
                                ]}
                            />
                            <InheritReset device={resMode} value={heights?.[resMode]} onReset={() => resetResponsive('heights', heights)} />
                        </NativeResponsiveControl>
                    )}
                    <NativeResponsiveControl label={__('Vertical Align', 'dentist-exchange')} props={props}>
                        <NativeToggleGroupControl
                            value={currentVAlign}
                            onChange={value => setResponsive('vAligns', vAligns, value)}
                            options={[
                                { label: __('Top', 'dentist-exchange'), value: 'top' },
                                { label: __('Middle', 'dentist-exchange'), value: 'middle' },
                                { label: __('Bottom', 'dentist-exchange'), value: 'bottom' }
                            ]}
                        />
                        <InheritReset device={resMode} value={vAligns?.[resMode]} onReset={() => resetResponsive('vAligns', vAligns)} />
                    </NativeResponsiveControl>
                    <NativeToggleGroupControl
                        label={__('Content Alignment', 'dentist-exchange')}
                        value={contentAlign}
                        onChange={value => setAttributes({ contentAlign: value })}
                        options={[
                            { label: __('Left', 'dentist-exchange'), value: 'left' },
                            { label: __('Center', 'dentist-exchange'), value: 'center' },
                            { label: __('Right', 'dentist-exchange'), value: 'right' }
                        ]}
                    />
                    <NativeResponsiveControl label={__('Content Width', 'dentist-exchange')} props={props}>
                        <NativeUnitControl
                            label={__('Max Width', 'dentist-exchange')}
                            value={currentContentWidth}
                            placeholder={__('Full', 'dentist-exchange')}
                            onChange={value => setResponsive('contentWidths', contentWidths, value)}
                            units={[
                                { label: 'px', value: 'px' },
                                { label: '%', value: '%' },
                                { label: 'rem', value: 'rem' }
                            ]}
                        />
                        <InheritReset
                            device={resMode}
                            value={contentWidths?.[resMode]}
                            onReset={() => resetResponsive('contentWidths', contentWidths)}
                        />
                    </NativeResponsiveControl>
                    {!!currentContentWidth && (
                        <>
                            <NativeToggleGroupControl
                                label={__('Content Position', 'dentist-exchange')}
                                value={contentPosition}
                                onChange={value => setAttributes({ contentPosition: value })}
                                options={[
                                    { label: __('Left', 'dentist-exchange'), value: 'left' },
                                    { label: __('Center', 'dentist-exchange'), value: 'center' },
                                    { label: __('Right', 'dentist-exchange'), value: 'right' }
                                ]}
                            />
                            <NativeToggleControl
                                label={__('Align thumbnails to content', 'dentist-exchange')}
                                help={__('Keeps the strip inside the same width so both share a left edge.', 'dentist-exchange')}
                                checked={constrainThumbs}
                                onChange={value => setAttributes({ constrainThumbs: value })}
                            />
                        </>
                    )}
                    <NativeBoxControl
                        label={__('Content Padding', 'dentist-exchange')}
                        value={contentPadding}
                        onChange={value => setAttributes({ contentPadding: value })}
                    />
                </PanelBody>

                <PanelBody title={__('Slider Options', 'dentist-exchange')} initialOpen={false}>
                    <NativeToggleGroupControl
                        label={__('Transition', 'dentist-exchange')}
                        value={effect}
                        onChange={value => setAttributes({ effect: value })}
                        options={[
                            { label: __('Fade', 'dentist-exchange'), value: 'fade' },
                            { label: __('Slide', 'dentist-exchange'), value: 'slide' }
                        ]}
                    />
                    <NativeRangeControl
                        label={__('Speed (ms)', 'dentist-exchange')}
                        value={speed}
                        onChange={value => setAttributes({ speed: value })}
                        min={200}
                        max={3000}
                        step={100}
                    />
                    <NativeToggleControl label={__('Loop', 'dentist-exchange')} checked={loop} onChange={value => setAttributes({ loop: value })} />
                    <NativeToggleControl
                        label={__('Autoplay', 'dentist-exchange')}
                        checked={autoplay}
                        onChange={value => setAttributes({ autoplay: value })}
                    />
                    {autoplay && (
                        <NativeRangeControl
                            label={__('Delay (ms)', 'dentist-exchange')}
                            value={delay}
                            onChange={value => setAttributes({ delay: value })}
                            min={1000}
                            max={12000}
                            step={500}
                        />
                    )}
                </PanelBody>

                <PanelBody title={__('Thumbnails', 'dentist-exchange')} initialOpen={false}>
                    <NativeToggleControl
                        label={__('Show Thumbnails', 'dentist-exchange')}
                        checked={showThumbs}
                        onChange={value => setAttributes({ showThumbs: value })}
                    />
                    {showThumbs && (
                        <>
                            <NativeResponsiveControl label={__('Thumbnail Size', 'dentist-exchange')} props={props}>
                                <NativeUnitControl
                                    label={__('Width', 'dentist-exchange')}
                                    value={currentThumbWidth}
                                    onChange={value => setResponsive('thumbWidths', thumbWidths, value)}
                                />
                                <NativeUnitControl
                                    label={__('Height', 'dentist-exchange')}
                                    value={currentThumbHeight}
                                    onChange={value => setResponsive('thumbHeights', thumbHeights, value)}
                                />
                                <InheritReset
                                    device={resMode}
                                    value={thumbWidths?.[resMode] || thumbHeights?.[resMode]}
                                    onReset={() =>
                                        setAttributes({
                                            thumbWidths: { ...thumbWidths, [resMode]: undefined },
                                            thumbHeights: { ...thumbHeights, [resMode]: undefined }
                                        })
                                    }
                                />
                            </NativeResponsiveControl>
                            <NativeResponsiveControl label={__('Gap', 'dentist-exchange')} props={props}>
                                <NativeRangeControl
                                    value={currentThumbGap}
                                    onChange={value => setResponsive('thumbGaps', thumbGaps, value)}
                                    min={0}
                                    max={60}
                                    step={1}
                                />
                                <InheritReset
                                    device={resMode}
                                    value={thumbGaps?.[resMode]}
                                    onReset={() => resetResponsive('thumbGaps', thumbGaps)}
                                />
                            </NativeResponsiveControl>
                            <NativeToggleGroupControl
                                label={__('Alignment', 'dentist-exchange')}
                                value={thumbsAlign}
                                onChange={value => setAttributes({ thumbsAlign: value })}
                                options={[
                                    { label: __('Left', 'dentist-exchange'), value: 'left' },
                                    { label: __('Center', 'dentist-exchange'), value: 'center' },
                                    { label: __('Right', 'dentist-exchange'), value: 'right' }
                                ]}
                            />
                            <NativeUnitControl
                                label={__('Bottom Spacing', 'dentist-exchange')}
                                value={thumbsOffset}
                                placeholder="0px"
                                onChange={value => setAttributes({ thumbsOffset: value })}
                            />
                        </>
                    )}
                </PanelBody>

                <PanelBody title={__('Navigation', 'dentist-exchange')} initialOpen={false}>
                    <NativeToggleControl
                        label={__('Show Arrows', 'dentist-exchange')}
                        checked={showArrows}
                        onChange={value => setAttributes({ showArrows: value })}
                    />
                    {showArrows && (
                        <>
                            <NativeIconPicker
                                label={__('Previous Icon', 'dentist-exchange')}
                                onIconSelect={(iconName, iconType) => {
                                    setAttributes({ prevIconName: iconName, prevIconType: iconType, prevCustomSvg: undefined });
                                }}
                                onCustomSvgInsert={({ customSvgCode, iconType }) => {
                                    setAttributes({ prevCustomSvg: customSvgCode, prevIconType: iconType });
                                }}
                                iconName={prevIconName}
                                customSvgCode={prevCustomSvg}
                            />
                            <NativeIconPicker
                                label={__('Next Icon', 'dentist-exchange')}
                                onIconSelect={(iconName, iconType) => {
                                    setAttributes({ nextIconName: iconName, nextIconType: iconType, nextCustomSvg: undefined });
                                }}
                                onCustomSvgInsert={({ customSvgCode, iconType }) => {
                                    setAttributes({ nextCustomSvg: customSvgCode, nextIconType: iconType });
                                }}
                                iconName={nextIconName}
                                customSvgCode={nextCustomSvg}
                            />
                        </>
                    )}
                </PanelBody>
            </InspectorControls>

            <InspectorControls group="styles">
                <ToolsPanel
                    label={__('Overlay', 'dentist-exchange')}
                    resetAll={() =>
                        setAttributes({
                            overlayColor: undefined,
                            overlayGradient: undefined,
                            overlayOpacity: 40
                        })
                    }
                >
                    <ToolsPanelItem
                        hasValue={() => !!overlayColor}
                        label={__('Color', 'dentist-exchange')}
                        onDeselect={() => setAttributes({ overlayColor: undefined })}
                        onSelect={() => {}}
                    >
                        <PanelColorControl
                            label={__('Overlay Color', 'dentist-exchange')}
                            colorSettings={[
                                {
                                    label: __('Color', 'dentist-exchange'),
                                    value: overlayColor,
                                    onChange: color => setAttributes({ overlayColor: color })
                                }
                            ]}
                        />
                    </ToolsPanelItem>
                    <ToolsPanelItem
                        hasValue={() => !!overlayGradient}
                        label={__('Gradient', 'dentist-exchange')}
                        onDeselect={() => setAttributes({ overlayGradient: undefined })}
                        onSelect={() => {}}
                    >
                        <GradientPicker
                            value={overlayGradient}
                            onChange={value => setAttributes({ overlayGradient: value })}
                            clearable
                            __nextHasNoMargin
                        />
                    </ToolsPanelItem>
                    <ToolsPanelItem
                        hasValue={() => overlayOpacity !== 40}
                        label={__('Opacity', 'dentist-exchange')}
                        onDeselect={() => setAttributes({ overlayOpacity: 40 })}
                        onSelect={() => {}}
                    >
                        <NativeRangeControl
                            label={__('Opacity (%)', 'dentist-exchange')}
                            value={overlayOpacity}
                            onChange={value => setAttributes({ overlayOpacity: value })}
                            min={0}
                            max={100}
                            step={1}
                        />
                    </ToolsPanelItem>
                </ToolsPanel>

                {showThumbs && (
                    <ToolsPanel
                        label={__('Thumbnails', 'dentist-exchange')}
                        resetAll={() =>
                            setAttributes({
                                thumbRadius: undefined,
                                thumbOpacity: 70,
                                thumbBorderColor: undefined,
                                thumbBorderWidth: undefined
                            })
                        }
                    >
                        <ToolsPanelItem
                            hasValue={() => !!thumbRadius}
                            label={__('Radius', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ thumbRadius: undefined })}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Radius', 'dentist-exchange')}
                                value={thumbRadius}
                                onChange={value => setAttributes({ thumbRadius: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => thumbOpacity !== 70}
                            label={__('Inactive Opacity', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ thumbOpacity: 70 })}
                            onSelect={() => {}}
                        >
                            <NativeRangeControl
                                label={__('Inactive Opacity (%)', 'dentist-exchange')}
                                value={thumbOpacity}
                                onChange={value => setAttributes({ thumbOpacity: value })}
                                min={10}
                                max={100}
                                step={1}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!thumbBorderColor || !!thumbBorderWidth}
                            label={__('Active Border', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ thumbBorderColor: undefined, thumbBorderWidth: undefined })}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Width', 'dentist-exchange')}
                                value={thumbBorderWidth}
                                placeholder="0px"
                                onChange={value => setAttributes({ thumbBorderWidth: value })}
                            />
                            <PanelColorControl
                                label={__('Active Border Color', 'dentist-exchange')}
                                colorSettings={[
                                    {
                                        label: __('Color', 'dentist-exchange'),
                                        value: thumbBorderColor,
                                        onChange: color => setAttributes({ thumbBorderColor: color })
                                    }
                                ]}
                            />
                        </ToolsPanelItem>
                    </ToolsPanel>
                )}

                {showArrows && (
                    <ToolsPanel
                        label={__('Navigation', 'dentist-exchange')}
                        resetAll={() =>
                            setAttributes({
                                navColor: undefined,
                                navbgColor: undefined,
                                navSize: undefined,
                                navIconSize: undefined,
                                navGap: undefined,
                                navBorder: undefined,
                                navBorderRadius: undefined,
                                navPadding: undefined
                            })
                        }
                    >
                        <ToolsPanelItem
                            hasValue={() => !!navSize || !!navIconSize}
                            label={__('Sizes', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ navSize: undefined, navIconSize: undefined })}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Button Size', 'dentist-exchange')}
                                value={navSize}
                                onChange={value => setAttributes({ navSize: value })}
                            />
                            <NativeUnitControl
                                label={__('Icon Size', 'dentist-exchange')}
                                value={navIconSize}
                                onChange={value => setAttributes({ navIconSize: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navGap}
                            label={__('Gap', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ navGap: undefined })}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Gap from Strip', 'dentist-exchange')}
                                value={navGap}
                                onChange={value => setAttributes({ navGap: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navColor || !!navbgColor}
                            label={__('Colors', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ navColor: undefined, navbgColor: undefined })}
                            onSelect={() => {}}
                        >
                            <PanelColorControl
                                label={__('Colors', 'dentist-exchange')}
                                colorSettings={[
                                    {
                                        label: __('Color', 'dentist-exchange'),
                                        value: navColor,
                                        onChange: color => setAttributes({ navColor: color })
                                    },
                                    {
                                        label: __('Background', 'dentist-exchange'),
                                        value: navbgColor,
                                        onChange: color => setAttributes({ navbgColor: color })
                                    }
                                ]}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navBorder}
                            label={__('Border', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ navBorder: undefined })}
                            onSelect={() => {}}
                        >
                            <NativeBorderBoxControl
                                label={__('Border', 'dentist-exchange')}
                                value={navBorder}
                                onChange={value => setAttributes({ navBorder: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navBorderRadius}
                            label={__('Radius', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ navBorderRadius: undefined })}
                            onSelect={() => {}}
                        >
                            <NativeBoxControl
                                label={__('Radius', 'dentist-exchange')}
                                value={navBorderRadius}
                                onChange={value => setAttributes({ navBorderRadius: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navPadding}
                            label={__('Padding', 'dentist-exchange')}
                            onDeselect={() => setAttributes({ navPadding: undefined })}
                            onSelect={() => {}}
                        >
                            <NativeBoxControl
                                label={__('Padding', 'dentist-exchange')}
                                value={navPadding}
                                onChange={value => setAttributes({ navPadding: value })}
                            />
                        </ToolsPanelItem>
                    </ToolsPanel>
                )}
            </InspectorControls>
        </>
    );
};

export default Inspector;
