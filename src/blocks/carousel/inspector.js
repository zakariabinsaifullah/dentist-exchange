import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import {
    Button,
    PanelBody,
    __experimentalBorderBoxControl as BorderBoxControl,
    __experimentalToggleGroupControl as ToggleGroupControl,
    __experimentalToggleGroupControlOption as ToggleGroupControlOption,
    __experimentalToolsPanel as ToolsPanel, // eslint-disable-line
    __experimentalToolsPanelItem as ToolsPanelItem // eslint-disable-line
} from '@wordpress/components';

import {
    NativeToggleGroupControl,
    NativeRangeControl,
    NativeToggleControl,
    PanelColorControl,
    NativeSelectControl,
    NativeResponsiveControl,
    NativeUnitControl,
    NativeIconPicker,
    NativeBoxControl,
    NativeBorderBoxControl
} from '../../components';
import { resolveResponsive } from './utils';

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

const Inspector = props => {
    const { attributes, setAttributes } = props;
    const {
        resMode,
        heightType,
        heights,
        vAligns,
        columns,
        gaps,
        autoplay,
        loop,
        showArrows,
        navType,
        showPagination,
        pnSize,
        paSize,
        pRadius,
        paRadius,
        pgap,
        paginationColor,
        npaginationHeight,
        apaginationHeight,
        delay,
        navColor,
        navbgColor,
        navBorderColor,
        navSize,
        navIconSize,
        navBorderRadius,
        navPadding,
        navBorder,
        navEdgeGap,
        navPosition,
        prevIconName,
        prevIconType,
        prevCustomSvg,
        nextIconName,
        nextIconType,
        nextCustomSvg
    } = attributes;

    // What the device currently in preview actually renders with.
    const currentHeightType = resolveResponsive(heightType, resMode) || 'adaptive';
    const currentHeight = resolveResponsive(heights, resMode) || '';
    const currentVAlign = resolveResponsive(vAligns, resMode) || 'top';

    return (
        <>
            <InspectorControls group="settings">
                <PanelBody title={__('General', 'dentist-exchange')} initialOpen={true}>
                    <NativeResponsiveControl label={__('Height Type', 'dentist-exchange')} props={props}>
                        <NativeToggleGroupControl
                            value={currentHeightType}
                            onChange={value => setAttributes({ heightType: { ...heightType, [resMode]: value } })}
                            options={[
                                { label: __('Adaptive', 'dentist-exchange'), value: 'adaptive' },
                                { label: __('Fixed', 'dentist-exchange'), value: 'fixed' }
                            ]}
                        />
                        <InheritReset
                            device={resMode}
                            value={heightType?.[resMode]}
                            onReset={() => setAttributes({ heightType: { ...heightType, [resMode]: undefined } })}
                        />
                    </NativeResponsiveControl>
                    {currentHeightType === 'fixed' && (
                        <>
                            <NativeResponsiveControl label={__('Height', 'dentist-exchange')} props={props}>
                                <NativeUnitControl
                                    label={__('Slider Height', 'dentist-exchange')}
                                    value={currentHeight}
                                    onChange={value => {
                                        const newHeights = { ...heights, [resMode]: value };
                                        setAttributes({ heights: newHeights });
                                    }}
                                />
                                <InheritReset
                                    device={resMode}
                                    value={heights?.[resMode]}
                                    onReset={() => setAttributes({ heights: { ...heights, [resMode]: undefined } })}
                                />
                            </NativeResponsiveControl>
                            <NativeResponsiveControl label={__('Vertical Align', 'dentist-exchange')} props={props}>
                                <NativeToggleGroupControl
                                    value={currentVAlign}
                                    onChange={value => setAttributes({ vAligns: { ...vAligns, [resMode]: value } })}
                                    options={[
                                        { label: __('Top', 'dentist-exchange'), value: 'top' },
                                        { label: __('Middle', 'dentist-exchange'), value: 'middle' },
                                        { label: __('Bottom', 'dentist-exchange'), value: 'bottom' }
                                    ]}
                                />
                                <InheritReset
                                    device={resMode}
                                    value={vAligns?.[resMode]}
                                    onReset={() => setAttributes({ vAligns: { ...vAligns, [resMode]: undefined } })}
                                />
                            </NativeResponsiveControl>
                        </>
                    )}
                </PanelBody>
                <PanelBody title={__('Slider Options', 'dentist-exchange')} initialOpen={false}>
                    <NativeResponsiveControl label={__('Columns', 'dentist-exchange')} props={props}>
                        <NativeRangeControl
                            value={columns[resMode]}
                            onChange={value => {
                                const newColumns = { ...columns, [resMode]: value };
                                setAttributes({ columns: newColumns });
                            }}
                            min={1}
                            max={6}
                            step={1}
                        />
                    </NativeResponsiveControl>
                    <NativeResponsiveControl label={__('Gaps', 'dentist-exchange')} props={props}>
                        <NativeRangeControl
                            value={gaps[resMode]}
                            onChange={value => {
                                const newGaps = { ...gaps, [resMode]: value };
                                setAttributes({ gaps: newGaps });
                            }}
                            min={0}
                            max={100}
                            step={1}
                        />
                    </NativeResponsiveControl>
                    <NativeToggleControl
                        label={__('Loop', 'dentist-exchange')}
                        checked={loop}
                        onChange={value => setAttributes({ loop: value })}
                    />
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
                            max={10000}
                            step={500}
                        />
                    )}
                    <NativeToggleControl
                        label={__('Show Arrows', 'dentist-exchange')}
                        checked={showArrows}
                        onChange={value => setAttributes({ showArrows: value })}
                    />
                    <NativeToggleControl
                        label={__('Show Pagination', 'dentist-exchange')}
                        checked={showPagination}
                        onChange={value => setAttributes({ showPagination: value })}
                    />
                    {showArrows && (
                        <NativeToggleGroupControl
                            label={__('Navigation Type', 'dentist-exchange')}
                            value={navType}
                            onChange={value => setAttributes({ navType: value })}
                            options={[
                                { label: __('Inside', 'dentist-exchange'), value: 'inside' },
                                { label: __('Outside', 'dentist-exchange'), value: 'outside' }
                            ]}
                        />
                    )}
                    {showArrows && (
                        <NativeSelectControl
                            label={__('Navigation Position', 'dentist-exchange')}
                            value={navPosition}
                            onChange={value => setAttributes({ navPosition: value })}
                            options={[
                                { label: __('Middle', 'dentist-exchange'), value: 'middle' },
                                { label: __('Bottom Right (Long Arrows)', 'dentist-exchange'), value: 'bottom-right-line' }
                            ]}
                        />
                    )}
                    {showArrows && (
                        <>
                            <NativeIconPicker
                                label={__('Previous Icon', 'dentist-exchange')}
                                onIconSelect={(iconName, iconType) => {
                                    setAttributes({
                                        prevIconName: iconName,
                                        prevIconType: iconType,
                                        prevCustomSvg: undefined
                                    });
                                }}
                                onCustomSvgInsert={({ customSvgCode, iconType }) => {
                                    setAttributes({
                                        prevCustomSvg: customSvgCode,
                                        prevIconType: iconType
                                    });
                                }}
                                iconName={prevIconName}
                                customSvgCode={prevCustomSvg}
                            />
                            <NativeIconPicker
                                label={__('Next Icon', 'dentist-exchange')}
                                onIconSelect={(iconName, iconType) => {
                                    setAttributes({
                                        nextIconName: iconName,
                                        nextIconType: iconType,
                                        nextCustomSvg: undefined
                                    });
                                }}
                                onCustomSvgInsert={({ customSvgCode, iconType }) => {
                                    setAttributes({
                                        nextCustomSvg: customSvgCode,
                                        nextIconType: iconType
                                    });
                                }}
                                iconName={nextIconName}
                                customSvgCode={nextCustomSvg}
                            />
                        </>
                    )}
                </PanelBody>
            </InspectorControls>
            <InspectorControls group="styles">
                {showPagination && (
                    <ToolsPanel
                        label={__('Pagination', 'dentist-exchange')}
                        resetAll={() =>
                            setAttributes({
                                pnSize: undefined,
                                paSize: undefined,
                                pRadius: undefined,
                                paRadius: undefined,
                                paginationColor: undefined,
                                pgap: undefined,
                                npaginationHeight: undefined,
                                apaginationHeight: undefined
                            })
                        }
                    >
                        <ToolsPanelItem
                            hasValue={() => !!pgap}
                            label={__('Gap', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    pgap: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Vertical Gap', 'dentist-exchange')}
                                value={pgap}
                                onChange={value => setAttributes({ pgap: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!pnSize || !!paSize}
                            label={__('Sizes', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    pnSize: undefined,
                                    paSize: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Normal Size', 'dentist-exchange')}
                                value={pnSize}
                                onChange={value => setAttributes({ pnSize: value })}
                            />
                            <NativeUnitControl
                                label={__('Active Size', 'dentist-exchange')}
                                value={paSize}
                                onChange={value => setAttributes({ paSize: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!npaginationHeight}
                            label={__('Height', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    npaginationHeight: undefined,
                                    apaginationHeight: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Normal Height', 'dentist-exchange')}
                                value={npaginationHeight}
                                onChange={value => setAttributes({ npaginationHeight: value })}
                            />
                            <NativeUnitControl
                                label={__('Active Height', 'dentist-exchange')}
                                value={apaginationHeight}
                                onChange={value => setAttributes({ apaginationHeight: value })}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!pRadius || !!paRadius}
                            label={__('Radius', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    pRadius: undefined,
                                    paRadius: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Normal Radius', 'dentist-exchange')}
                                value={pRadius}
                                onChange={value => setAttributes({ pRadius: value })}
                            />
                            <NativeUnitControl
                                label={__('Active Radius', 'dentist-exchange')}
                                value={paRadius}
                                onChange={value => setAttributes({ paRadius: value })}
                            />
                        </ToolsPanelItem>

                        <ToolsPanelItem
                            hasValue={() => !!paginationColor}
                            label={__('Color', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    paginationColor: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <PanelColorControl
                                label={__('Color', 'dentist-exchange')}
                                colorSettings={[
                                    {
                                        value: paginationColor,
                                        onChange: color => setAttributes({ paginationColor: color })
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
                                navbgColor: undefined,
                                navColor: undefined,
                                navEdgeGap: undefined,
                                navSize: undefined,
                                navIconSize: undefined,
                                navBorderColor: undefined,
                                navBorderRadius: undefined,
                                navPadding: undefined,
                                navBorder: undefined
                            })
                        }
                    >
                        <ToolsPanelItem
                            hasValue={() => !!navEdgeGap}
                            label={__('Edge Gap', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    navEdgeGap: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Edge Gap', 'dentist-exchange')}
                                value={navEdgeGap}
                                onChange={value => setAttributes({ navEdgeGap: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navSize}
                            label={__('Gap', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    navSize: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Size', 'dentist-exchange')}
                                value={navSize}
                                onChange={value => setAttributes({ navSize: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navIconSize}
                            label={__('Icon Size', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    navIconSize: undefined
                                });
                            }}
                            onSelect={() => {}}
                        >
                            <NativeUnitControl
                                label={__('Icon Size', 'dentist-exchange')}
                                value={navIconSize}
                                onChange={value => setAttributes({ navIconSize: value })}
                            />
                        </ToolsPanelItem>
                        <ToolsPanelItem
                            hasValue={() => !!navColor || !!navbgColor || !!navBorderColor}
                            label={__('Colors', 'dentist-exchange')}
                            onDeselect={() => {
                                setAttributes({
                                    navColor: undefined,
                                    navbgColor: undefined,
                                    navBorderColor: undefined
                                });
                            }}
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
                            onDeselect={() => {
                                setAttributes({
                                    navBorder: undefined
                                });
                            }}
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
                            onDeselect={() => {
                                setAttributes({
                                    navBorderRadius: undefined
                                });
                            }}
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
                            onDeselect={() => {
                                setAttributes({
                                    navPadding: undefined
                                });
                            }}
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
