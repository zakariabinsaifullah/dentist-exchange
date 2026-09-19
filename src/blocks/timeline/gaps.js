/**
 * Responsive gap helpers shared by the timeline's edit and save.
 *
 * The gaps no longer have inspector controls, but timelines saved earlier may
 * still carry values. Only breakpoints with an explicit value are written out;
 * the stylesheets cascade each unset breakpoint up to the next larger one.
 */

const DEVICE_PREFIX = {
    Desktop: 'd',
    Tablet: 't',
    Mobile: 'm'
};

const gapProperties = (gaps, name) =>
    Object.entries(DEVICE_PREFIX).reduce((properties, [device, prefix]) => {
        const value = gaps?.[device];

        if (undefined !== value && null !== value) {
            properties[`--${prefix}${name}`] = `${value}px`;
        }

        return properties;
    }, {});

export const timelineGapProperties = (itemGaps, iconGaps) => ({
    ...gapProperties(itemGaps, 'item-gap'),
    ...gapProperties(iconGaps, 'icon-gap')
});
