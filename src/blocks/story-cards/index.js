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
        <path d="M120-160v-280h280v280H120Zm0-360v-280h280v280H120Zm440 360v-280h280v280H560Zm0-360v-280h280v280H560ZM200-520h120v-120H200v120Zm440 360h120v-120H640v120ZM200-240h120v-120H200v120Zm440-360h120v-120H640v120Z" />
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
