/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { createReduxStore, register, select as globalSelect } from '@wordpress/data';

/**
 * Data store for the site-wide "My Icons" library.
 *
 * Every block bundle imports this module, but the store lives on the shared
 * `wp.data` registry — so registering once is enough and an icon saved from one
 * block instantly shows up in every other picker on the screen.
 */
export const MY_ICONS_STORE = 'dnte/my-icons';

const REST_PATH = '/dnte/v1/my-icons';

const DEFAULT_STATE = {
    icons: null, // `null` until the first fetch resolves.
    isSaving: false,
    deleting: []
};

const reducer = (state = DEFAULT_STATE, action) => {
    switch (action.type) {
        case 'RECEIVE_ICONS':
            return { ...state, icons: action.icons };

        case 'ADD_ICON':
            return { ...state, icons: [...(state.icons || []), action.icon] };

        case 'REMOVE_ICON':
            return {
                ...state,
                icons: (state.icons || []).filter(icon => icon.id !== action.id)
            };

        case 'SET_SAVING':
            return { ...state, isSaving: action.isSaving };

        case 'SET_DELETING':
            return {
                ...state,
                deleting: action.isDeleting ? [...state.deleting, action.id] : state.deleting.filter(id => id !== action.id)
            };

        default:
            return state;
    }
};

const actions = {
    receiveIcons(icons) {
        return { type: 'RECEIVE_ICONS', icons };
    },

    /**
     * Persists a custom SVG to the site-wide library.
     *
     * @param {Object} icon             Icon to store.
     * @param {string} icon.label       Human readable name.
     * @param {string} icon.svg         Raw SVG markup (sanitised server side).
     * @param {string} icon.iconType    Either `fill` or `line`.
     * @param {number} icon.strokeWidth Stroke width for line icons.
     * @return {Object} The stored icon, as returned by the REST API.
     */
    saveIcon(icon) {
        return async ({ dispatch }) => {
            dispatch({ type: 'SET_SAVING', isSaving: true });

            try {
                const saved = await apiFetch({
                    path: REST_PATH,
                    method: 'POST',
                    data: icon
                });

                dispatch({ type: 'ADD_ICON', icon: saved });

                return saved;
            } finally {
                dispatch({ type: 'SET_SAVING', isSaving: false });
            }
        };
    },

    /**
     * Removes an icon from the site-wide library.
     *
     * @param {string} id Icon id.
     */
    deleteIcon(id) {
        return async ({ dispatch }) => {
            dispatch({ type: 'SET_DELETING', id, isDeleting: true });

            try {
                await apiFetch({
                    path: `${REST_PATH}/${id}`,
                    method: 'DELETE'
                });

                dispatch({ type: 'REMOVE_ICON', id });
            } finally {
                dispatch({ type: 'SET_DELETING', id, isDeleting: false });
            }
        };
    }
};

const selectors = {
    getMyIcons(state) {
        return state.icons || [];
    },
    hasLoadedMyIcons(state) {
        return null !== state.icons;
    },
    isSavingMyIcon(state) {
        return state.isSaving;
    },
    isDeletingMyIcon(state, id) {
        return state.deleting.includes(id);
    }
};

const resolvers = {
    getMyIcons() {
        return async ({ dispatch }) => {
            try {
                const icons = await apiFetch({ path: REST_PATH });
                dispatch.receiveIcons(Array.isArray(icons) ? icons : []);
            } catch {
                // A failed fetch should not leave the picker stuck on "loading".
                dispatch.receiveIcons([]);
            }
        };
    }
};

const store = createReduxStore(MY_ICONS_STORE, {
    reducer,
    actions,
    selectors,
    resolvers
});

// Each block is built as its own bundle, so guard against re-registering.
if (!globalSelect(MY_ICONS_STORE)) {
    register(store);
}

export default store;
