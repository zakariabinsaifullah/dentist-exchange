import { ToggleControl } from '@wordpress/components';

const NativeToggleControl = ({ label, checked, onChange, help }) => {
    return <ToggleControl label={label} checked={checked} onChange={onChange} help={help} __nextHasNoMarginBottom />;
};

export default NativeToggleControl;
