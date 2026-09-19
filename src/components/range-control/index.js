import { RangeControl } from '@wordpress/components';
import classnames from 'classnames';

const NativeRangeControl = ({ label, value, onChange, min, max, step, resetFallbackValue }) => {
    const showReset = resetFallbackValue !== undefined;
    return (
        <div className="native-control-wrapper native-range-control">
            {showReset && (
                <button
                    type="button"
                    title="Reset"
                    className={classnames('reset', {
                        'is-disabled': value === resetFallbackValue || value === undefined || value === null
                    })}
                    onClick={() => onChange(resetFallbackValue)}
                >
                    <span className="dashicons dashicons-image-rotate" />
                </button>
            )}

            <RangeControl
                label={label}
                value={value}
                onChange={v => onChange(v)}
                min={min}
                max={max}
                step={step}
                __next40pxDefaultSize
                __nextHasNoMarginBottom
            />
        </div>
    );
};

export default NativeRangeControl;
