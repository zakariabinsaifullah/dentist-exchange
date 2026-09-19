import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import deprecated from './deprecated';
import metadata from './block.json';

const inlineIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="#1f1f1f">
        <path d="M300-160q-58 0-99-41t-41-99 41-99 99-41h440q58 0 99 41t41 99-41 99-99 41zm0-80h440q25 0 42.5-17.5T800-300t-17.5-42.5T740-360H300q-25 0-42.5 17.5T240-300t17.5 42.5T300-240m-80-280q-58 0-99-41t-41-99 41-99 99-41h440q58 0 99 41t41 99-41 99-99 41zm0-80h440q25 0 42.5-17.5T720-660t-17.5-42.5T660-720H220q-25 0-42.5 17.5T160-660t17.5 42.5T220-600m220-60" />
    </svg>
);

registerBlockType(metadata.name, {
    icon: inlineIcon,
    /**
     * @see ./edit.js
     */
    edit: Edit,

    /**
     * @see ./save.js
     */
    save,

    /**
     * @see ./deprecated.js
     */
    deprecated
});
