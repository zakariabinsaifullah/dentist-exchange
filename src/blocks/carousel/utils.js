export const DEVICES = ['Desktop', 'Tablet', 'Mobile'];

export const PREFIXES = {
    Desktop: 'd',
    Tablet: 't',
    Mobile: 'm'
};

const VALIGN_MAP = {
    top: 'flex-start',
    middle: 'center',
    bottom: 'flex-end'
};

/**
 * Desktop is the single source of truth: Tablet and Mobile fall straight back
 * to it unless they carry a value of their own.
 */
export const resolveResponsive = (values, device) => {
    const own = values?.[device];

    if (own !== undefined && own !== null && own !== '') {
        return own;
    }

    return device === 'Desktop' ? undefined : values?.Desktop;
};

/**
 * Turn the per-device height type, height and vertical align into custom
 * properties. The fallback is resolved here rather than in CSS, so every
 * breakpoint gets an explicit value and the stylesheet stays dumb.
 */
export const generateHeightStyles = (heightType, heights, vAligns) =>
    DEVICES.reduce((styles, device) => {
        if (resolveResponsive(heightType, device) !== 'fixed') {
            return styles;
        }

        const prefix = PREFIXES[device];
        const height = resolveResponsive(heights, device);
        const vAlign = VALIGN_MAP[resolveResponsive(vAligns, device)];

        if (height) {
            styles[`--${prefix}height`] = `${height}`;
        }

        if (vAlign) {
            styles[`--${prefix}valign`] = vAlign;
        }

        return styles;
    }, {});
