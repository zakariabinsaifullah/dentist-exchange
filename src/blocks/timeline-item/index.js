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
        <path d="M240-440v-80h480v80z" />
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
