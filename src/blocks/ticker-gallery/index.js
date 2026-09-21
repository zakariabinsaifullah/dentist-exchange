import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import save from './save';
import metadata from './block.json';

const inlineIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 -960 960 960" fill="currentColor">
        <path d="M120-200v-560h80v560h-80Zm160 0v-560h80v560h-80Zm200 0q-33 0-56.5-23.5T400-280v-400q0-33 23.5-56.5T480-760h280q33 0 56.5 23.5T840-680v400q0 33-23.5 56.5T760-200H480Zm0-80h280v-400H480v400Z" />
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
    save
});
