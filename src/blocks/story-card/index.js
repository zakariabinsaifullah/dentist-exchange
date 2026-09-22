import { registerBlockType } from '@wordpress/blocks';
import './style.scss';

/**
 * Internal dependencies
 */
import Edit from './edit';
import metadata from './block.json';

const inlineIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 -960 960 960" fill="currentColor">
        <path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm80-120h400v-60H280v60Zm0-140h400v-60H280v60Zm0-140h200v-200H280v200Z" />
    </svg>
);

registerBlockType(metadata.name, {
    icon: inlineIcon,

    /**
     * @see ./edit.js
     *
     * There is no save function: the block is rendered by render.php so the
     * connector artwork can be inlined from the theme at render time.
     */
    edit: Edit,
    save: () => null
});
