import { __experimentalUnitControl as UnitControl } from '@wordpress/components';
import classnames from 'classnames';

const NativeUnitControl = ({
    label,
    value,
    onChange,
    mb = '',
    placeholder = '',
    units = [
        {
            label: 'px',
            value: 'px'
        },
        {
            label: 'em',
            value: 'em'
        },
        {
            label: 'rem',
            value: 'rem'
        }
    ],
    disableUnits = false,
    labelPosition = 'edge'
}) => {
    return (
        <div
            className={classnames('native-control-wrapper', {
                [`mb-0`]: labelPosition === 'edge' && ! mb,
            })}
            {...(labelPosition === 'edge' && {
                style: {
                    '--max-width': '80px'
                }
            })}
        >
            <UnitControl
                label={label}
                value={value}
                onChange={value => onChange(value)}
                labelPosition={labelPosition}
                placeholder={placeholder}
                __next40pxDefaultSize
                units={units}
                disableUnits={disableUnits}
            />
        </div>
    );
};
export default NativeUnitControl;
