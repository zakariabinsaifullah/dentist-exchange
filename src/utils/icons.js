/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * WordPress icons
 */
import {
    addCard,
    addSubmenu,
    alignCenter,
    alignJustify,
    alignLeft,
    alignNone,
    alignRight,
    archive,
    arrowDown,
    arrowLeft,
    arrowRight,
    arrowUp,
    atSymbol,
    aspectRatio,
    audio,
    backup,
    blockDefault,
    blockTable,
    border,
    box,
    brush,
    bug,
    button,
    buttons,
    calendar,
    cancelCircleFilled,
    caption,
    capturePhoto,
    captureVideo,
    category,
    chartBar,
    check,
    chevronDown,
    chevronLeft,
    chevronRight,
    chevronRightSmall,
    chevronUp,
    chevronUpDown,
    classic,
    close,
    closeSmall,
    cloudUpload,
    cloud,
    code,
    cog,
    color,
    column,
    columns,
    comment,
    commentAuthorAvatar,
    commentAuthorName,
    commentContent,
    commentEditLink,
    commentReplyLink,
    copy,
    cover,
    create,
    crop,
    currencyDollar,
    currencyEuro,
    currencyPound,
    customPostType,
    desktop,
    dragHandle,
    drawerLeft,
    drawerRight,
    download,
    // edit,
    external,
    file,
    filter,
    flipHorizontal,
    flipVertical,
    formatBold,
    formatCapitalize,
    formatIndent,
    formatIndentRTL,
    formatItalic,
    formatListBullets,
    formatListBulletsRTL,
    formatListNumbered,
    formatListNumberedRTL,
    formatLTR,
    formatLowercase,
    formatOutdent,
    formatOutdentRTL,
    // formatRTL,
    formatStrikethrough,
    formatUnderline,
    formatUppercase,
    fullscreen,
    gallery,
    globe,
    grid,
    group,
    handle,
    heading,
    help,
    helpFilled,
    inbox,
    institution,
    home,
    html,
    image,
    info,
    insertAfter,
    insertBefore,
    justifyLeft,
    justifyCenter,
    justifyRight,
    justifySpaceBetween,
    justifyStretch,
    key,
    keyboardClose,
    keyboardReturn,
    layout,
    lifesaver,
    lineDashed,
    lineDotted,
    lineSolid,
    link,
    linkOff,
    list,
    listItem,
    listView,
    lock,
    lockOutline,
    lockSmall,
    login,
    loop,
    mapMarker,
    media,
    mediaAndText,
    megaphone,
    menu,
    mobile,
    more,
    moreHorizontal,
    moreVertical,
    moveTo,
    navigation,
    overlayText,
    pageBreak,
    customLink,
    page,
    pages,
    paragraph,
    payment,
    percent,
    positionCenter,
    positionLeft,
    positionRight,
    pencil,
    people,
    pin,
    plugins,
    plusCircleFilled,
    plusCircle,
    plus,
    post,
    postAuthor,
    postCategories,
    postContent,
    postComments,
    postCommentsCount,
    postCommentsForm,
    postDate,
    postExcerpt,
    postFeaturedImage,
    postList,
    postTerms,
    previous,
    next,
    preformatted,
    pullLeft,
    pullRight,
    pullquote,
    queryPagination,
    queryPaginationNext,
    queryPaginationNumbers,
    queryPaginationPrevious,
    quote,
    receipt,
    redo,
    removeBug,
    removeSubmenu,
    replace,
    reset,
    resizeCornerNE,
    reusableBlock,
    rotateLeft,
    rotateRight,
    row,
    rss,
    search,
    seen,
    separator,
    settings,
    shadow,
    share,
    shield,
    shortcode,
    shuffle,
    siteLogo,
    stack,
    starEmpty,
    starFilled,
    starHalf,
    store,
    stretchFullWidth,
    styles,
    shipping,
    stretchWide,
    subscript,
    superscript,
    swatch,
    symbol,
    symbolFilled,
    tableColumnAfter,
    tableColumnBefore,
    tableColumnDelete,
    tableRowAfter,
    tableRowBefore,
    tableRowDelete,
    table,
    tag,
    termDescription,
    footer,
    header,
    sidebar,
    textColor,
    tablet,
    tip,
    title,
    tool,
    trash,
    trendingDown,
    trendingUp,
    typography,
    undo,
    ungroup,
    unlock,
    unseen,
    update,
    upload,
    verse,
    video,
    // warning,
    widget,
    wordpress
} from '@wordpress/icons';

/**
 * Icon categories
 */
const categories = [
    {
        slug: 'all',
        name: __('All Icons', 'dentist-exchange')
    },
    {
        slug: 'arrows',
        name: __('Arrows', 'dentist-exchange')
    },
    {
        slug: 'editor',
        name: __('Editor', 'dentist-exchange')
    },
    {
        slug: 'actions',
        name: __('Actions', 'dentist-exchange')
    },
    {
        slug: 'devices',
        name: __('Devices', 'dentist-exchange')
    },
    {
        slug: 'interface',
        name: __('Interface', 'dentist-exchange')
    },
    {
        slug: 'media',
        name: __('Media', 'dentist-exchange')
    },
    {
        slug: 'format',
        name: __('Formatting', 'dentist-exchange')
    },
    {
        slug: 'layout',
        name: __('Layout', 'dentist-exchange')
    },
    {
        slug: 'tables',
        name: __('Tables', 'dentist-exchange')
    },
    {
        slug: 'posts',
        name: __('Posts & Pages', 'dentist-exchange')
    },
    {
        slug: 'comments',
        name: __('Comments', 'dentist-exchange')
    },
    {
        slug: 'ecommerce',
        name: __('E-Commerce', 'dentist-exchange')
    },
    {
        slug: 'misc',
        name: __('Miscellaneous', 'dentist-exchange')
    }
];

/**
 * Icon definitions with categories
 */
const icons = [
    // Original icons
    {
        name: 'wordpress',
        title: __('WordPress', 'dentist-exchange'),
        icon: wordpress,
        categories: ['misc'],
        isDefault: true
    },
    {
        name: 'paragraph',
        title: __('Paragraph', 'dentist-exchange'),
        icon: paragraph,
        categories: ['editor']
    },
    {
        name: 'share',
        title: __('Share', 'dentist-exchange'),
        icon: share,
        categories: ['actions']
    },
    {
        name: 'star-filled',
        title: __('Star Filled', 'dentist-exchange'),
        icon: starFilled,
        categories: ['interface'],
        isDefault: true
    },
    {
        name: 'star-empty',
        title: __('Star Empty', 'dentist-exchange'),
        icon: starEmpty,
        categories: ['interface']
    },
    {
        name: 'check',
        title: __('Check', 'dentist-exchange'),
        icon: check,
        categories: ['interface'],
        isDefault: true
    },
    {
        name: 'menu',
        title: __('Menu', 'dentist-exchange'),
        icon: menu,
        categories: ['interface']
    },
    {
        name: 'help',
        title: __('Help', 'dentist-exchange'),
        icon: help,
        categories: ['interface']
    },
    {
        name: 'globe',
        title: __('Globe', 'dentist-exchange'),
        icon: globe,
        categories: ['misc']
    },
    {
        name: 'gallery',
        title: __('Gallery', 'dentist-exchange'),
        icon: gallery,
        categories: ['editor', 'media']
    },
    // {
    //     name: 'edit',
    //     title: __('Edit', 'dentist-exchange'),
    //     icon: edit,
    //     categories: ['actions']
    // },
    {
        name: 'cloud',
        title: __('Cloud', 'dentist-exchange'),
        icon: cloud,
        categories: ['misc']
    },
    {
        name: 'archive',
        title: __('Archive', 'dentist-exchange'),
        icon: archive,
        categories: ['misc']
    },
    {
        name: 'settings',
        title: __('Settings', 'dentist-exchange'),
        icon: settings,
        categories: ['interface']
    },
    {
        name: 'home',
        title: __('Home', 'dentist-exchange'),
        icon: home,
        categories: ['misc']
    },
    // {
    //     name: 'warning',
    //     title: __('Warning', 'dentist-exchange'),
    //     icon: warning,
    //     categories: ['interface']
    // },
    {
        name: 'info',
        title: __('Info', 'dentist-exchange'),
        icon: info,
        categories: ['interface']
    },
    {
        name: 'search',
        title: __('Search', 'dentist-exchange'),
        icon: search,
        categories: ['actions']
    },
    {
        name: 'calendar',
        title: __('Calendar', 'dentist-exchange'),
        icon: calendar,
        categories: ['misc']
    },
    {
        name: 'desktop',
        title: __('Desktop', 'dentist-exchange'),
        icon: desktop,
        categories: ['devices']
    },
    {
        name: 'mobile',
        title: __('Mobile', 'dentist-exchange'),
        icon: mobile,
        categories: ['devices']
    },
    {
        name: 'tablet',
        title: __('Tablet', 'dentist-exchange'),
        icon: tablet,
        categories: ['devices']
    },
    {
        name: 'download',
        title: __('Download', 'dentist-exchange'),
        icon: download,
        categories: ['actions'],
        isDefault: true
    },
    {
        name: 'pencil',
        title: __('Pencil', 'dentist-exchange'),
        icon: pencil,
        categories: ['actions']
    },
    {
        name: 'external',
        title: __('External', 'dentist-exchange'),
        icon: external,
        categories: ['interface']
    },
    {
        name: 'trash',
        title: __('Trash', 'dentist-exchange'),
        icon: trash,
        categories: ['actions']
    },
    {
        name: 'upload',
        title: __('Upload', 'dentist-exchange'),
        icon: upload,
        categories: ['actions']
    },
    {
        name: 'plus',
        title: __('Plus', 'dentist-exchange'),
        icon: plus,
        categories: ['interface']
    },
    {
        name: 'close',
        title: __('Close', 'dentist-exchange'),
        icon: close,
        categories: ['interface']
    },
    {
        name: 'update',
        title: __('Update', 'dentist-exchange'),
        icon: update,
        categories: ['actions']
    },
    {
        name: 'arrow-up',
        title: __('Arrow Up', 'dentist-exchange'),
        icon: arrowUp,
        categories: ['arrows']
    },
    {
        name: 'arrow-down',
        title: __('Arrow Down', 'dentist-exchange'),
        icon: arrowDown,
        categories: ['arrows']
    },
    {
        name: 'arrow-left',
        title: __('Arrow Left', 'dentist-exchange'),
        icon: arrowLeft,
        categories: ['arrows']
    },
    {
        name: 'arrow-right',
        title: __('Arrow Right', 'dentist-exchange'),
        icon: arrowRight,
        categories: ['arrows'],
        isDefault: true
    },

    // Additional icons - Arrows
    {
        name: 'chevron-down',
        title: __('Chevron Down', 'dentist-exchange'),
        icon: chevronDown,
        categories: ['arrows']
    },
    {
        name: 'chevron-up',
        title: __('Chevron Up', 'dentist-exchange'),
        icon: chevronUp,
        categories: ['arrows']
    },
    {
        name: 'chevron-left',
        title: __('Chevron Left', 'dentist-exchange'),
        icon: chevronLeft,
        categories: ['arrows']
    },
    {
        name: 'chevron-right',
        title: __('Chevron Right', 'dentist-exchange'),
        icon: chevronRight,
        categories: ['arrows'],
        isDefault: true
    },
    {
        name: 'chevron-right-small',
        title: __('Chevron Right Small', 'dentist-exchange'),
        icon: chevronRightSmall,
        categories: ['arrows']
    },
    {
        name: 'chevron-up-down',
        title: __('Chevron Up Down', 'dentist-exchange'),
        icon: chevronUpDown,
        categories: ['arrows']
    },
    {
        name: 'previous',
        title: __('Previous', 'dentist-exchange'),
        icon: previous,
        categories: ['arrows']
    },
    {
        name: 'next',
        title: __('Next', 'dentist-exchange'),
        icon: next,
        categories: ['arrows']
    },

    // Editor
    {
        name: 'heading',
        title: __('Heading', 'dentist-exchange'),
        icon: heading,
        categories: ['editor']
    },
    {
        name: 'quote',
        title: __('Quote', 'dentist-exchange'),
        icon: quote,
        categories: ['editor']
    },
    {
        name: 'list',
        title: __('List', 'dentist-exchange'),
        icon: list,
        categories: ['editor']
    },
    {
        name: 'list-item',
        title: __('List Item', 'dentist-exchange'),
        icon: listItem,
        categories: ['editor']
    },
    {
        name: 'code',
        title: __('Code', 'dentist-exchange'),
        icon: code,
        categories: ['editor']
    },
    {
        name: 'image',
        title: __('Image', 'dentist-exchange'),
        icon: image,
        categories: ['editor', 'media']
    },
    {
        name: 'video',
        title: __('Video', 'dentist-exchange'),
        icon: video,
        categories: ['editor', 'media']
    },
    {
        name: 'audio',
        title: __('Audio', 'dentist-exchange'),
        icon: audio,
        categories: ['editor', 'media']
    },
    {
        name: 'media',
        title: __('Media', 'dentist-exchange'),
        icon: media,
        categories: ['editor', 'media']
    },
    {
        name: 'cover',
        title: __('Cover', 'dentist-exchange'),
        icon: cover,
        categories: ['editor']
    },
    {
        name: 'verse',
        title: __('Verse', 'dentist-exchange'),
        icon: verse,
        categories: ['editor']
    },
    {
        name: 'preformatted',
        title: __('Preformatted', 'dentist-exchange'),
        icon: preformatted,
        categories: ['editor']
    },
    {
        name: 'shortcode',
        title: __('Shortcode', 'dentist-exchange'),
        icon: shortcode,
        categories: ['editor']
    },
    {
        name: 'html',
        title: __('HTML', 'dentist-exchange'),
        icon: html,
        categories: ['editor']
    },
    {
        name: 'pullquote',
        title: __('Pullquote', 'dentist-exchange'),
        icon: pullquote,
        categories: ['editor']
    },
    {
        name: 'button',
        title: __('Button', 'dentist-exchange'),
        icon: button,
        categories: ['editor']
    },
    {
        name: 'buttons',
        title: __('Buttons', 'dentist-exchange'),
        icon: buttons,
        categories: ['editor']
    },
    {
        name: 'media-and-text',
        title: __('Media & Text', 'dentist-exchange'),
        icon: mediaAndText,
        categories: ['editor', 'layout']
    },

    // Format
    {
        name: 'format-bold',
        title: __('Format Bold', 'dentist-exchange'),
        icon: formatBold,
        categories: ['format']
    },
    {
        name: 'format-italic',
        title: __('Format Italic', 'dentist-exchange'),
        icon: formatItalic,
        categories: ['format']
    },
    {
        name: 'format-strikethrough',
        title: __('Format Strikethrough', 'dentist-exchange'),
        icon: formatStrikethrough,
        categories: ['format']
    },
    {
        name: 'format-underline',
        title: __('Format Underline', 'dentist-exchange'),
        icon: formatUnderline,
        categories: ['format']
    },
    {
        name: 'format-capitalize',
        title: __('Format Capitalize', 'dentist-exchange'),
        icon: formatCapitalize,
        categories: ['format']
    },
    {
        name: 'format-uppercase',
        title: __('Format Uppercase', 'dentist-exchange'),
        icon: formatUppercase,
        categories: ['format']
    },
    {
        name: 'format-lowercase',
        title: __('Format Lowercase', 'dentist-exchange'),
        icon: formatLowercase,
        categories: ['format']
    },
    {
        name: 'format-ltr',
        title: __('Format LTR', 'dentist-exchange'),
        icon: formatLTR,
        categories: ['format']
    },
    // {
    //     name: 'format-rtl',
    //     title: __('Format RTL', 'dentist-exchange'),
    //     icon: formatRTL,
    //     categories: ['format']
    // },
    {
        name: 'format-indent',
        title: __('Format Indent', 'dentist-exchange'),
        icon: formatIndent,
        categories: ['format']
    },
    {
        name: 'format-indent-rtl',
        title: __('Format Indent RTL', 'dentist-exchange'),
        icon: formatIndentRTL,
        categories: ['format']
    },
    {
        name: 'format-outdent',
        title: __('Format Outdent', 'dentist-exchange'),
        icon: formatOutdent,
        categories: ['format']
    },
    {
        name: 'format-outdent-rtl',
        title: __('Format Outdent RTL', 'dentist-exchange'),
        icon: formatOutdentRTL,
        categories: ['format']
    },
    {
        name: 'format-list-bullets',
        title: __('Format List Bullets', 'dentist-exchange'),
        icon: formatListBullets,
        categories: ['format']
    },
    {
        name: 'format-list-bullets-rtl',
        title: __('Format List Bullets RTL', 'dentist-exchange'),
        icon: formatListBulletsRTL,
        categories: ['format']
    },
    {
        name: 'format-list-numbered',
        title: __('Format List Numbered', 'dentist-exchange'),
        icon: formatListNumbered,
        categories: ['format']
    },
    {
        name: 'format-list-numbered-rtl',
        title: __('Format List Numbered RTL', 'dentist-exchange'),
        icon: formatListNumberedRTL,
        categories: ['format']
    },
    {
        name: 'text-color',
        title: __('Text Color', 'dentist-exchange'),
        icon: textColor,
        categories: ['format']
    },
    {
        name: 'subscript',
        title: __('Subscript', 'dentist-exchange'),
        icon: subscript,
        categories: ['format']
    },
    {
        name: 'superscript',
        title: __('Superscript', 'dentist-exchange'),
        icon: superscript,
        categories: ['format']
    },

    // Layout
    {
        name: 'layouts',
        title: __('Layouts', 'dentist-exchange'),
        icon: (
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M20.0004 3.10668C20.5527 3.10668 21.0004 3.5544 21.0004 4.10668V20.1067C21.0004 20.659 20.5527 21.1067 20.0004 21.1067L14.6027 21.106L11.4287 3.10596L20.0004 3.10668Z"
                    fill="currentColor"
                />
                <path
                    d="M9.39751 3.10596L10.8416 11.2945L2.99951 12.678L3.00024 4.10668C3.00024 3.5544 3.44795 3.10668 4.00024 3.10668L9.39751 3.10596Z"
                    fill="currentColor"
                />
                <path
                    d="M11.1889 13.2642L12.5715 21.106L4.00024 21.1067C3.44795 21.1067 3.00024 20.659 3.00024 20.1067L2.99951 14.709L11.1889 13.2642Z"
                    fill="currentColor"
                />
            </svg>
        ),
        categories: ['layout']
    },
    {
        name: 'align-left',
        title: __('Align Left', 'dentist-exchange'),
        icon: alignLeft,
        categories: ['layout']
    },
    {
        name: 'align-center',
        title: __('Align Center', 'dentist-exchange'),
        icon: alignCenter,
        categories: ['layout']
    },
    {
        name: 'align-right',
        title: __('Align Right', 'dentist-exchange'),
        icon: alignRight,
        categories: ['layout']
    },
    {
        name: 'align-justify',
        title: __('Align Justify', 'dentist-exchange'),
        icon: alignJustify,
        categories: ['layout']
    },
    {
        name: 'align-none',
        title: __('Align None', 'dentist-exchange'),
        icon: alignNone,
        categories: ['layout']
    },
    {
        name: 'columns',
        title: __('Columns', 'dentist-exchange'),
        icon: columns,
        categories: ['layout']
    },
    {
        name: 'column',
        title: __('Column', 'dentist-exchange'),
        icon: column,
        categories: ['layout']
    },
    {
        name: 'row',
        title: __('Row', 'dentist-exchange'),
        icon: row,
        categories: ['layout']
    },
    {
        name: 'group',
        title: __('Group', 'dentist-exchange'),
        icon: group,
        categories: ['layout']
    },
    {
        name: 'ungroup',
        title: __('Ungroup', 'dentist-exchange'),
        icon: ungroup,
        categories: ['layout']
    },
    {
        name: 'stretch-wide',
        title: __('Stretch Wide', 'dentist-exchange'),
        icon: stretchWide,
        categories: ['layout']
    },
    {
        name: 'stretch-full-width',
        title: __('Stretch Full Width', 'dentist-exchange'),
        icon: stretchFullWidth,
        categories: ['layout']
    },
    {
        name: 'position-left',
        title: __('Position Left', 'dentist-exchange'),
        icon: positionLeft,
        categories: ['layout']
    },
    {
        name: 'position-center',
        title: __('Position Center', 'dentist-exchange'),
        icon: positionCenter,
        categories: ['layout']
    },
    {
        name: 'position-right',
        title: __('Position Right', 'dentist-exchange'),
        icon: positionRight,
        categories: ['layout']
    },
    {
        name: 'justify-left',
        title: __('Justify Left', 'dentist-exchange'),
        icon: justifyLeft,
        categories: ['layout']
    },
    {
        name: 'justify-center',
        title: __('Justify Center', 'dentist-exchange'),
        icon: justifyCenter,
        categories: ['layout']
    },
    {
        name: 'justify-right',
        title: __('Justify Right', 'dentist-exchange'),
        icon: justifyRight,
        categories: ['layout']
    },
    {
        name: 'justify-space-between',
        title: __('Justify Space Between', 'dentist-exchange'),
        icon: justifySpaceBetween,
        categories: ['layout']
    },
    {
        name: 'justify-stretch',
        title: __('Justify Stretch', 'dentist-exchange'),
        icon: justifyStretch,
        categories: ['layout']
    },
    {
        name: 'header',
        title: __('Header', 'dentist-exchange'),
        icon: header,
        categories: ['layout']
    },
    {
        name: 'footer',
        title: __('Footer', 'dentist-exchange'),
        icon: footer,
        categories: ['layout']
    },
    {
        name: 'sidebar',
        title: __('Sidebar', 'dentist-exchange'),
        icon: sidebar,
        categories: ['layout']
    },
    {
        name: 'layout',
        title: __('Layout', 'dentist-exchange'),
        icon: layout,
        categories: ['layout']
    },

    // Tables
    {
        name: 'table',
        title: __('Table', 'dentist-exchange'),
        icon: table,
        categories: ['tables']
    },
    {
        name: 'block-table',
        title: __('Block Table', 'dentist-exchange'),
        icon: blockTable,
        categories: ['tables']
    },
    {
        name: 'table-column-after',
        title: __('Table Column After', 'dentist-exchange'),
        icon: tableColumnAfter,
        categories: ['tables']
    },
    {
        name: 'table-column-before',
        title: __('Table Column Before', 'dentist-exchange'),
        icon: tableColumnBefore,
        categories: ['tables']
    },
    {
        name: 'table-column-delete',
        title: __('Table Column Delete', 'dentist-exchange'),
        icon: tableColumnDelete,
        categories: ['tables']
    },
    {
        name: 'table-row-after',
        title: __('Table Row After', 'dentist-exchange'),
        icon: tableRowAfter,
        categories: ['tables']
    },
    {
        name: 'table-row-before',
        title: __('Table Row Before', 'dentist-exchange'),
        icon: tableRowBefore,
        categories: ['tables']
    },
    {
        name: 'table-row-delete',
        title: __('Table Row Delete', 'dentist-exchange'),
        icon: tableRowDelete,
        categories: ['tables']
    },

    // Actions
    {
        name: 'copy',
        title: __('Copy', 'dentist-exchange'),
        icon: copy,
        categories: ['actions']
    },

    {
        name: 'redo',
        title: __('Redo', 'dentist-exchange'),
        icon: redo,
        categories: ['actions']
    },
    {
        name: 'undo',
        title: __('Undo', 'dentist-exchange'),
        icon: undo,
        categories: ['actions']
    },
    {
        name: 'create',
        title: __('Create', 'dentist-exchange'),
        icon: create,
        categories: ['actions']
    },
    {
        name: 'lock',
        title: __('Lock', 'dentist-exchange'),
        icon: lock,
        categories: ['actions']
    },
    {
        name: 'unlock',
        title: __('Unlock', 'dentist-exchange'),
        icon: unlock,
        categories: ['actions']
    },
    {
        name: 'lock-outline',
        title: __('Lock Outline', 'dentist-exchange'),
        icon: lockOutline,
        categories: ['actions']
    },
    {
        name: 'lock-small',
        title: __('Lock Small', 'dentist-exchange'),
        icon: lockSmall,
        categories: ['actions']
    },
    {
        name: 'login',
        title: __('Login', 'dentist-exchange'),
        icon: login,
        categories: ['actions']
    },
    {
        name: 'reset',
        title: __('Reset', 'dentist-exchange'),
        icon: reset,
        categories: ['actions']
    },
    {
        name: 'rotate-left',
        title: __('Rotate Left', 'dentist-exchange'),
        icon: rotateLeft,
        categories: ['actions']
    },
    {
        name: 'rotate-right',
        title: __('Rotate Right', 'dentist-exchange'),
        icon: rotateRight,
        categories: ['actions']
    },
    {
        name: 'flip-horizontal',
        title: __('Flip Horizontal', 'dentist-exchange'),
        icon: flipHorizontal,
        categories: ['actions']
    },
    {
        name: 'flip-vertical',
        title: __('Flip Vertical', 'dentist-exchange'),
        icon: flipVertical,
        categories: ['actions']
    },
    {
        name: 'replace',
        title: __('Replace', 'dentist-exchange'),
        icon: replace,
        categories: ['actions']
    },
    {
        name: 'fullscreen',
        title: __('Fullscreen', 'dentist-exchange'),
        icon: fullscreen,
        categories: ['actions']
    },

    // Interface
    {
        name: 'close-small',
        title: __('Close Small', 'dentist-exchange'),
        icon: closeSmall,
        categories: ['interface']
    },
    {
        name: 'more',
        title: __('More', 'dentist-exchange'),
        icon: more,
        categories: ['interface']
    },
    {
        name: 'more-horizontal',
        title: __('More Horizontal', 'dentist-exchange'),
        icon: moreHorizontal,
        categories: ['interface']
    },
    {
        name: 'more-vertical',
        title: __('More Vertical', 'dentist-exchange'),
        icon: moreVertical,
        categories: ['interface']
    },
    {
        name: 'star-half',
        title: __('Star Half', 'dentist-exchange'),
        icon: starHalf,
        categories: ['interface']
    },
    {
        name: 'help-filled',
        title: __('Help Filled', 'dentist-exchange'),
        icon: helpFilled,
        categories: ['interface']
    },
    {
        name: 'cog',
        title: __('Cog', 'dentist-exchange'),
        icon: cog,
        categories: ['interface'],
        isDefault: true
    },
    {
        name: 'plus-circle',
        title: __('Plus Circle', 'dentist-exchange'),
        icon: plusCircle,
        categories: ['interface']
    },
    {
        name: 'plus-circle-filled',
        title: __('Plus Circle Filled', 'dentist-exchange'),
        icon: plusCircleFilled,
        categories: ['interface']
    },
    {
        name: 'cancel-circle-filled',
        title: __('Cancel Circle Filled', 'dentist-exchange'),
        icon: cancelCircleFilled,
        categories: ['interface']
    },
    {
        name: 'plugins',
        title: __('Plugins', 'dentist-exchange'),
        icon: plugins,
        categories: ['interface']
    },
    {
        name: 'filter',
        title: __('Filter', 'dentist-exchange'),
        icon: filter,
        categories: ['interface']
    },
    {
        name: 'list-view',
        title: __('List View', 'dentist-exchange'),
        icon: listView,
        categories: ['interface']
    },
    {
        name: 'drag-handle',
        title: __('Drag Handle', 'dentist-exchange'),
        icon: dragHandle,
        categories: ['interface']
    },
    {
        name: 'handle',
        title: __('Handle', 'dentist-exchange'),
        icon: handle,
        categories: ['interface']
    },
    {
        name: 'seen',
        title: __('Seen', 'dentist-exchange'),
        icon: seen,
        categories: ['interface']
    },
    {
        name: 'unseen',
        title: __('Unseen', 'dentist-exchange'),
        icon: unseen,
        categories: ['interface']
    },
    {
        name: 'keyboard-close',
        title: __('Keyboard Close', 'dentist-exchange'),
        icon: keyboardClose,
        categories: ['interface']
    },
    {
        name: 'keyboard-return',
        title: __('Keyboard Return', 'dentist-exchange'),
        icon: keyboardReturn,
        categories: ['interface']
    },
    {
        name: 'styles',
        title: __('Styles', 'dentist-exchange'),
        icon: styles,
        categories: ['interface']
    },

    // Posts & Pages
    {
        name: 'page',
        title: __('Page', 'dentist-exchange'),
        icon: page,
        categories: ['posts']
    },
    {
        name: 'pages',
        title: __('Pages', 'dentist-exchange'),
        icon: pages,
        categories: ['posts']
    },
    {
        name: 'post',
        title: __('Post', 'dentist-exchange'),
        icon: post,
        categories: ['posts']
    },
    {
        name: 'post-author',
        title: __('Post Author', 'dentist-exchange'),
        icon: postAuthor,
        categories: ['posts']
    },
    {
        name: 'post-categories',
        title: __('Post Categories', 'dentist-exchange'),
        icon: postCategories,
        categories: ['posts']
    },
    {
        name: 'post-content',
        title: __('Post Content', 'dentist-exchange'),
        icon: postContent,
        categories: ['posts']
    },
    {
        name: 'post-date',
        title: __('Post Date', 'dentist-exchange'),
        icon: postDate,
        categories: ['posts']
    },
    {
        name: 'post-excerpt',
        title: __('Post Excerpt', 'dentist-exchange'),
        icon: postExcerpt,
        categories: ['posts']
    },
    {
        name: 'post-featured-image',
        title: __('Post Featured Image', 'dentist-exchange'),
        icon: postFeaturedImage,
        categories: ['posts', 'media']
    },
    {
        name: 'post-list',
        title: __('Post List', 'dentist-exchange'),
        icon: postList,
        categories: ['posts']
    },
    {
        name: 'post-terms',
        title: __('Post Terms', 'dentist-exchange'),
        icon: postTerms,
        categories: ['posts']
    },
    {
        name: 'page-break',
        title: __('Page Break', 'dentist-exchange'),
        icon: pageBreak,
        categories: ['posts', 'editor']
    },
    {
        name: 'custom-post-type',
        title: __('Custom Post Type', 'dentist-exchange'),
        icon: customPostType,
        categories: ['posts']
    },
    {
        name: 'term-description',
        title: __('Term Description', 'dentist-exchange'),
        icon: termDescription,
        categories: ['posts']
    },
    {
        name: 'category',
        title: __('Category', 'dentist-exchange'),
        icon: category,
        categories: ['posts']
    },
    {
        name: 'tag',
        title: __('Tag', 'dentist-exchange'),
        icon: tag,
        categories: ['posts']
    },
    {
        name: 'title',
        title: __('Title', 'dentist-exchange'),
        icon: title,
        categories: ['posts', 'editor']
    },

    // Comments
    {
        name: 'comment',
        title: __('Comment', 'dentist-exchange'),
        icon: comment,
        categories: ['comments']
    },
    {
        name: 'post-comments',
        title: __('Post Comments', 'dentist-exchange'),
        icon: postComments,
        categories: ['comments', 'posts']
    },
    {
        name: 'post-comments-count',
        title: __('Post Comments Count', 'dentist-exchange'),
        icon: postCommentsCount,
        categories: ['comments', 'posts']
    },
    {
        name: 'post-comments-form',
        title: __('Post Comments Form', 'dentist-exchange'),
        icon: postCommentsForm,
        categories: ['comments', 'posts']
    },
    {
        name: 'comment-author-avatar',
        title: __('Comment Author Avatar', 'dentist-exchange'),
        icon: commentAuthorAvatar,
        categories: ['comments']
    },
    {
        name: 'comment-author-name',
        title: __('Comment Author Name', 'dentist-exchange'),
        icon: commentAuthorName,
        categories: ['comments']
    },
    {
        name: 'comment-content',
        title: __('Comment Content', 'dentist-exchange'),
        icon: commentContent,
        categories: ['comments']
    },
    {
        name: 'comment-edit-link',
        title: __('Comment Edit Link', 'dentist-exchange'),
        icon: commentEditLink,
        categories: ['comments']
    },
    {
        name: 'comment-reply-link',
        title: __('Comment Reply Link', 'dentist-exchange'),
        icon: commentReplyLink,
        categories: ['comments']
    },

    // Media
    {
        name: 'caption',
        title: __('Caption', 'dentist-exchange'),
        icon: caption,
        categories: ['media']
    },
    {
        name: 'capture-photo',
        title: __('Capture Photo', 'dentist-exchange'),
        icon: capturePhoto,
        categories: ['media']
    },
    {
        name: 'capture-video',
        title: __('Capture Video', 'dentist-exchange'),
        icon: captureVideo,
        categories: ['media']
    },
    {
        name: 'aspect-ratio',
        title: __('Aspect Ratio', 'dentist-exchange'),
        icon: aspectRatio,
        categories: ['media']
    },
    {
        name: 'crop',
        title: __('Crop', 'dentist-exchange'),
        icon: crop,
        categories: ['media', 'actions']
    },
    {
        name: 'file',
        title: __('File', 'dentist-exchange'),
        icon: file,
        categories: ['media']
    },

    // E-commerce
    {
        name: 'currency-dollar',
        title: __('Currency Dollar', 'dentist-exchange'),
        icon: currencyDollar,
        categories: ['ecommerce']
    },
    {
        name: 'currency-euro',
        title: __('Currency Euro', 'dentist-exchange'),
        icon: currencyEuro,
        categories: ['ecommerce']
    },
    {
        name: 'currency-pound',
        title: __('Currency Pound', 'dentist-exchange'),
        icon: currencyPound,
        categories: ['ecommerce']
    },
    {
        name: 'payment',
        title: __('Payment', 'dentist-exchange'),
        icon: payment,
        categories: ['ecommerce']
    },
    {
        name: 'percent',
        title: __('Percent', 'dentist-exchange'),
        icon: percent,
        categories: ['ecommerce']
    },
    {
        name: 'receipt',
        title: __('Receipt', 'dentist-exchange'),
        icon: receipt,
        categories: ['ecommerce']
    },
    {
        name: 'shipping',
        title: __('Shipping', 'dentist-exchange'),
        icon: shipping,
        categories: ['ecommerce'],
        isDefault: true
    },
    {
        name: 'store',
        title: __('Store', 'dentist-exchange'),
        icon: store,
        categories: ['ecommerce']
    },
    {
        name: 'chart-bar',
        title: __('Chart Bar', 'dentist-exchange'),
        icon: chartBar,
        categories: ['ecommerce']
    },
    {
        name: 'trending-down',
        title: __('Trending Down', 'dentist-exchange'),
        icon: trendingDown,
        categories: ['ecommerce']
    },
    {
        name: 'trending-up',
        title: __('Trending Up', 'dentist-exchange'),
        icon: trendingUp,
        categories: ['ecommerce']
    },

    // Miscellaneous
    {
        name: 'at-symbol',
        title: __('At Symbol', 'dentist-exchange'),
        icon: atSymbol,
        categories: ['misc']
    },
    {
        name: 'backup',
        title: __('Backup', 'dentist-exchange'),
        icon: backup,
        categories: ['misc']
    },
    {
        name: 'block-default',
        title: __('Block Default', 'dentist-exchange'),
        icon: blockDefault,
        categories: ['misc']
    },
    {
        name: 'border',
        title: __('Border', 'dentist-exchange'),
        icon: border,
        categories: ['misc']
    },
    {
        name: 'box',
        title: __('Box', 'dentist-exchange'),
        icon: box,
        categories: ['misc']
    },
    {
        name: 'brush',
        title: __('Brush', 'dentist-exchange'),
        icon: brush,
        categories: ['misc']
    },
    {
        name: 'bug',
        title: __('Bug', 'dentist-exchange'),
        icon: bug,
        categories: ['misc']
    },
    {
        name: 'remove-bug',
        title: __('Remove Bug', 'dentist-exchange'),
        icon: removeBug,
        categories: ['misc']
    },
    {
        name: 'classic',
        title: __('Classic', 'dentist-exchange'),
        icon: classic,
        categories: ['misc']
    },
    {
        name: 'cloud-upload',
        title: __('Cloud Upload', 'dentist-exchange'),
        icon: cloudUpload,
        categories: ['misc', 'actions']
    },
    {
        name: 'color',
        title: __('Color', 'dentist-exchange'),
        icon: color,
        categories: ['misc']
    },
    {
        name: 'custom-link',
        title: __('Custom Link', 'dentist-exchange'),
        icon: customLink,
        categories: ['misc']
    },
    {
        name: 'drawer-left',
        title: __('Drawer Left', 'dentist-exchange'),
        icon: drawerLeft,
        categories: ['misc']
    },
    {
        name: 'drawer-right',
        title: __('Drawer Right', 'dentist-exchange'),
        icon: drawerRight,
        categories: ['misc']
    },
    {
        name: 'grid',
        title: __('Grid', 'dentist-exchange'),
        icon: grid,
        categories: ['misc', 'layout']
    },
    {
        name: 'inbox',
        title: __('Inbox', 'dentist-exchange'),
        icon: inbox,
        categories: ['misc']
    },
    {
        name: 'institution',
        title: __('Institution', 'dentist-exchange'),
        icon: institution,
        categories: ['misc']
    },
    {
        name: 'insert-after',
        title: __('Insert After', 'dentist-exchange'),
        icon: insertAfter,
        categories: ['misc', 'actions']
    },
    {
        name: 'insert-before',
        title: __('Insert Before', 'dentist-exchange'),
        icon: insertBefore,
        categories: ['misc', 'actions']
    },
    {
        name: 'key',
        title: __('Key', 'dentist-exchange'),
        icon: key,
        categories: ['misc']
    },
    {
        name: 'lifesaver',
        title: __('Lifesaver', 'dentist-exchange'),
        icon: lifesaver,
        categories: ['misc']
    },
    {
        name: 'line-dashed',
        title: __('Line Dashed', 'dentist-exchange'),
        icon: lineDashed,
        categories: ['misc']
    },
    {
        name: 'line-dotted',
        title: __('Line Dotted', 'dentist-exchange'),
        icon: lineDotted,
        categories: ['misc']
    },
    {
        name: 'line-solid',
        title: __('Line Solid', 'dentist-exchange'),
        icon: lineSolid,
        categories: ['misc']
    },
    {
        name: 'link',
        title: __('Link', 'dentist-exchange'),
        icon: link,
        categories: ['misc']
    },
    {
        name: 'link-off',
        title: __('Link Off', 'dentist-exchange'),
        icon: linkOff,
        categories: ['misc']
    },
    {
        name: 'loop',
        title: __('Loop', 'dentist-exchange'),
        icon: loop,
        categories: ['misc']
    },
    {
        name: 'map-marker',
        title: __('Map Marker', 'dentist-exchange'),
        icon: mapMarker,
        categories: ['misc']
    },
    {
        name: 'megaphone',
        title: __('Megaphone', 'dentist-exchange'),
        icon: megaphone,
        categories: ['misc']
    },
    {
        name: 'move-to',
        title: __('Move To', 'dentist-exchange'),
        icon: moveTo,
        categories: ['misc', 'actions']
    },
    {
        name: 'navigation',
        title: __('Navigation', 'dentist-exchange'),
        icon: navigation,
        categories: ['misc']
    },
    {
        name: 'overlay-text',
        title: __('Overlay Text', 'dentist-exchange'),
        icon: overlayText,
        categories: ['misc']
    },
    {
        name: 'people',
        title: __('People', 'dentist-exchange'),
        icon: people,
        categories: ['misc']
    },
    {
        name: 'pin',
        title: __('Pin', 'dentist-exchange'),
        icon: pin,
        categories: ['misc']
    },
    {
        name: 'pull-left',
        title: __('Pull Left', 'dentist-exchange'),
        icon: pullLeft,
        categories: ['misc', 'layout']
    },
    {
        name: 'pull-right',
        title: __('Pull Right', 'dentist-exchange'),
        icon: pullRight,
        categories: ['misc', 'layout']
    },
    {
        name: 'query-pagination',
        title: __('Query Pagination', 'dentist-exchange'),
        icon: queryPagination,
        categories: ['misc']
    },
    {
        name: 'query-pagination-next',
        title: __('Query Pagination Next', 'dentist-exchange'),
        icon: queryPaginationNext,
        categories: ['misc']
    },
    {
        name: 'query-pagination-numbers',
        title: __('Query Pagination Numbers', 'dentist-exchange'),
        icon: queryPaginationNumbers,
        categories: ['misc']
    },
    {
        name: 'query-pagination-previous',
        title: __('Query Pagination Previous', 'dentist-exchange'),
        icon: queryPaginationPrevious,
        categories: ['misc']
    },
    {
        name: 'resize-corner-ne',
        title: __('Resize Corner NE', 'dentist-exchange'),
        icon: resizeCornerNE,
        categories: ['misc']
    },
    {
        name: 'reusable-block',
        title: __('Reusable Block', 'dentist-exchange'),
        icon: reusableBlock,
        categories: ['misc', 'editor']
    },
    {
        name: 'rss',
        title: __('RSS', 'dentist-exchange'),
        icon: rss,
        categories: ['misc']
    },
    {
        name: 'separator',
        title: __('Separator', 'dentist-exchange'),
        icon: separator,
        categories: ['misc', 'editor']
    },
    {
        name: 'shadow',
        title: __('Shadow', 'dentist-exchange'),
        icon: shadow,
        categories: ['misc']
    },
    {
        name: 'shield',
        title: __('Shield', 'dentist-exchange'),
        icon: shield,
        categories: ['misc']
    },
    {
        name: 'shuffle',
        title: __('Shuffle', 'dentist-exchange'),
        icon: shuffle,
        categories: ['misc']
    },
    {
        name: 'site-logo',
        title: __('Site Logo', 'dentist-exchange'),
        icon: siteLogo,
        categories: ['misc']
    },
    {
        name: 'stack',
        title: __('Stack', 'dentist-exchange'),
        icon: stack,
        categories: ['misc']
    },
    {
        name: 'swatch',
        title: __('Swatch', 'dentist-exchange'),
        icon: swatch,
        categories: ['misc']
    },
    {
        name: 'symbol',
        title: __('Symbol', 'dentist-exchange'),
        icon: symbol,
        categories: ['misc']
    },
    {
        name: 'symbol-filled',
        title: __('Symbol Filled', 'dentist-exchange'),
        icon: symbolFilled,
        categories: ['misc']
    },
    {
        name: 'tip',
        title: __('Tip', 'dentist-exchange'),
        icon: tip,
        categories: ['misc']
    },
    {
        name: 'tool',
        title: __('Tool', 'dentist-exchange'),
        icon: tool,
        categories: ['misc']
    },
    {
        name: 'typography',
        title: __('Typography', 'dentist-exchange'),
        icon: typography,
        categories: ['misc', 'format']
    },
    {
        name: 'widget',
        title: __('Widget', 'dentist-exchange'),
        icon: widget,
        categories: ['misc']
    },
    {
        name: 'add-card',
        title: __('Add Card', 'dentist-exchange'),
        icon: addCard,
        categories: ['actions']
    },
    {
        name: 'add-submenu',
        title: __('Add Submenu', 'dentist-exchange'),
        icon: addSubmenu,
        categories: ['actions']
    },
    {
        name: 'remove-submenu',
        title: __('Remove Submenu', 'dentist-exchange'),
        icon: removeSubmenu,
        categories: ['actions']
    }
];

/**
 * custsom icons
 */
export const BrilliantIcon = (
    <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
            d="M18.3024 11.0626L15.8267 12.5194L10.5443 9.41094C8.96904 8.48402 7 9.64242 7 11.4967V4.41205C7 2.55775 8.96904 1.39934 10.5443 2.32626L18.3024 6.89152C19.8776 7.81844 19.8776 10.1357 18.3024 11.0626Z"
            fill="#DBEAFE"
        />
        <path
            d="M15.8267 12.5194L10.5443 15.6279C8.96904 16.5548 7 15.3964 7 13.5421V11.4967C7 9.64242 8.96904 8.48402 10.5443 9.41093L15.8267 12.5194Z"
            fill="#93C5FD"
        />
        <path
            d="M20.8182 19.6282L10.5443 25.6738C8.96904 26.6007 7 25.4418 7 23.588V13.5421C7 15.3964 8.96904 16.5548 10.5443 15.6279L15.8267 12.5194L20.8182 15.4566C22.3939 16.3835 22.3939 18.7013 20.8182 19.6282Z"
            fill="#2563EB"
        />
    </svg>
);

export const FilterIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
        <path d="M10 18H14V16H10V18ZM3 6V8H21V6H3ZM6 13H18V11H6V13Z"></path>
    </svg>
);

export const AccountIconCircle = (
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
        <path d="M240.92-268.31q51-37.84 111.12-59.77Q412.15-350 480-350t127.96 21.92q60.12 21.93 111.12 59.77 37.3-41 59.11-94.92Q800-417.15 800-480q0-133-93.5-226.5T480-800q-133 0-226.5 93.5T160-480q0 62.85 21.81 116.77 21.81 53.92 59.11 94.92ZM480.01-450q-54.78 0-92.39-37.6Q350-525.21 350-579.99t37.6-92.39Q425.21-710 479.99-710t92.39 37.6Q610-634.79 610-580.01t-37.6 92.39Q534.79-450 480.01-450ZM480-100q-79.15 0-148.5-29.77t-120.65-81.08q-51.31-51.3-81.08-120.65Q100-400.85 100-480t29.77-148.5q29.77-69.35 81.08-120.65 51.3-51.31 120.65-81.08Q400.85-860 480-860t148.5 29.77q69.35 29.77 120.65 81.08 51.31 51.3 81.08 120.65Q860-559.15 860-480t-29.77 148.5q-29.77 69.35-81.08 120.65-51.3 51.31-120.65 81.08Q559.15-100 480-100Zm0-60q54.15 0 104.42-17.42 50.27-17.43 89.27-48.73-39-30.16-88.11-47Q536.46-290 480-290t-105.77 16.65q-49.31 16.66-87.92 47.2 39 31.3 89.27 48.73Q425.85-160 480-160Zm0-350q29.85 0 49.92-20.08Q550-550.15 550-580t-20.08-49.92Q509.85-650 480-650t-49.92 20.08Q410-609.85 410-580t20.08 49.92Q450.15-510 480-510Zm0-70Zm0 355Z" />
    </svg>
);

export const PremiumAccountIcon = (
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
            d="M11.5 2C12.8192 2 14.0571 2.24797 15.2129 2.74414C16.3686 3.2403 17.3736 3.91639 18.2285 4.77148C19.0836 5.62641 19.7597 6.6314 20.2559 7.78711C20.752 8.94294 21 10.1808 21 11.5C21 11.862 20.9788 12.2176 20.9414 12.5674C20.6865 12.8906 19.5 12.5 19.4922 11.8184C19.496 11.713 19.5 11.6069 19.5 11.5C19.5 9.28333 18.7204 7.39622 17.1621 5.83789C15.6038 4.27956 13.7167 3.5 11.5 3.5C9.28333 3.5 7.39622 4.27956 5.83789 5.83789C4.27956 7.39622 3.5 9.28333 3.5 11.5C3.5 12.5474 3.68152 13.5204 4.04492 14.4189C4.40842 15.3176 4.90177 16.1087 5.52344 16.792C6.37329 16.1615 7.299 15.6633 8.30078 15.2979C9.30261 14.9325 10.3692 14.75 11.5 14.75C11.7129 14.75 11.9235 14.7585 12.1318 14.7715C12.7983 14.8129 12.3206 16.2609 11.6191 16.252C11.5795 16.2515 11.5398 16.25 11.5 16.25C10.559 16.25 9.6773 16.3885 8.85547 16.666C8.03374 16.9437 7.30164 17.3377 6.6582 17.8467C7.30809 18.3682 8.05202 18.774 8.88965 19.0645C9.72745 19.3548 10.5975 19.5 11.5 19.5C11.6066 19.5 11.7127 19.4972 11.8184 19.4932C12.5674 19.4645 13.1257 20.8817 12.5674 20.9414C12.2176 20.9788 11.862 21 11.5 21C10.1808 21 8.94294 20.752 7.78711 20.2559C6.6314 19.7597 5.62641 19.0836 4.77148 18.2285C3.91639 17.3736 3.2403 16.3686 2.74414 15.2129C2.24797 14.0571 2 12.8192 2 11.5C2 10.1808 2.24797 8.94294 2.74414 7.78711C3.2403 6.6314 3.91639 5.62641 4.77148 4.77148C5.62641 3.91639 6.6314 3.2403 7.78711 2.74414C8.94294 2.24797 10.1808 2 11.5 2Z"
            fill="#000000"
        />
        <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M11.5 5.75C12.4129 5.75006 13.1828 6.06382 13.8096 6.69043C14.4364 7.31722 14.75 8.08712 14.75 9C14.7499 9.91289 14.4362 10.6828 13.8096 11.3096C13.1828 11.9364 12.4129 12.25 11.5 12.25C10.5871 12.2499 9.81721 11.9362 9.19043 11.3096C8.5636 10.6828 8.25 9.91288 8.25 9C8.25006 8.08711 8.56382 7.31721 9.19043 6.69043C9.81722 6.0636 10.5871 5.75 11.5 5.75ZM11.5 7.25C11.0025 7.25 10.5865 7.41729 10.252 7.75195C9.91729 8.08645 9.75 8.5025 9.75 9C9.75 9.4975 9.91729 9.91355 10.252 10.248C10.5865 10.5827 11.0025 10.75 11.5 10.75C11.9975 10.75 12.4135 10.5827 12.748 10.248C13.0827 9.91355 13.25 9.4975 13.25 9C13.25 8.5025 13.0827 8.08645 12.748 7.75195C12.4135 7.41729 11.9975 7.25 11.5 7.25Z"
            fill="#000000"
        />
        <path
            d="M16.823 17.8557L16.2828 17.3155C16.1648 17.1975 16.024 17.1385 15.8605 17.1385C15.6972 17.1385 15.5565 17.1975 15.4385 17.3155C15.3205 17.4333 15.2615 17.5714 15.2615 17.7297C15.2615 17.8881 15.3205 18.0262 15.4385 18.1442L16.3558 19.0615C16.4929 19.1987 16.6503 19.2672 16.828 19.2672C17.0055 19.2672 17.1628 19.1987 17.3 19.0615L19.5615 16.8405C19.6795 16.7225 19.7401 16.5833 19.7432 16.423C19.7464 16.2628 19.6858 16.1206 19.5615 15.9962C19.4435 15.8782 19.3012 15.8192 19.1345 15.8192C18.9678 15.8192 18.8256 15.8782 18.7078 15.9962L16.823 17.8557ZM17.5 22.0577C16.2513 22.0577 15.1892 21.6199 14.3135 20.7442C13.4378 19.8686 13 18.8064 13 17.5577C13 16.3091 13.4378 15.2469 14.3135 14.3712C15.1892 13.4956 16.2513 13.0577 17.5 13.0577C18.7487 13.0577 19.8108 13.4956 20.6865 14.3712C21.5622 15.2469 22 16.3091 22 17.5577C22 18.8064 21.5622 19.8686 20.6865 20.7442C19.8108 21.6199 18.7487 22.0577 17.5 22.0577Z"
            fill="#22C55E"
        />
    </svg>
);

export const AccountIcon = (
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
        <path d="M480-492.31q-57.75 0-98.87-41.12Q340-574.56 340-632.31q0-57.75 41.13-98.87 41.12-41.13 98.87-41.13 57.75 0 98.87 41.13Q620-690.06 620-632.31q0 57.75-41.13 98.88-41.12 41.12-98.87 41.12ZM180-248.46v-28.16q0-29.38 15.96-54.42 15.96-25.04 42.66-38.5 59.3-29.07 119.65-43.61 60.35-14.54 121.73-14.54t121.73 14.54q60.35 14.54 119.65 43.61 26.7 13.46 42.66 38.5Q780-306 780-276.62v28.16q0 25.3-17.73 43.04-17.73 17.73-43.04 17.73H240.77q-25.31 0-43.04-17.73Q180-223.16 180-248.46Zm60 .77h480v-28.93q0-12.15-7.04-22.5-7.04-10.34-19.11-16.88-51.7-25.46-105.42-38.58Q534.7-367.69 480-367.69q-54.7 0-108.43 13.11-53.72 13.12-105.42 38.58-12.07 6.54-19.11 16.88-7.04 10.35-7.04 22.5v28.93Zm240-304.62q33 0 56.5-23.5t23.5-56.5q0-33-23.5-56.5t-56.5-23.5q-33 0-56.5 23.5t-23.5 56.5q0 33 23.5 56.5t56.5 23.5Zm0-80Zm0 384.62Z" />
    </svg>
);

export const Refresh = (
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
        <path d="M481.54-180q-125.63 0-212.81-87.17-87.19-87.17-87.19-212.77 0-125.6 87.19-212.83Q355.91-780 481.54-780q70.15 0 132.77 31.19 62.61 31.2 104.15 88.04V-750q0-12.75 8.63-21.37 8.63-8.63 21.38-8.63 12.76 0 21.37 8.63 8.62 8.62 8.62 21.37v178.46q0 15.37-10.39 25.76-10.4 10.39-25.76 10.39H563.85q-12.75 0-21.38-8.62-8.62-8.63-8.62-21.39 0-12.75 8.62-21.37 8.63-8.61 21.38-8.61h128q-31.62-57.93-87.7-91.27Q548.08-720 481.54-720q-100 0-170 70t-70 170q0 100 70 170t170 70q69.73 0 127.67-36.62 57.94-36.61 87.79-97.69 5.69-10.92 16.73-15.27 11.04-4.34 22.5-.5 12.23 3.85 17.12 16 4.88 12.16-.81 23.08-37.16 77.69-109.7 124.34Q570.3-180 481.54-180Z" />
    </svg>
);

export const ArrowBack = (
    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#000000">
        <path d="m294.92-450 206.77 206.77q8.92 8.92 8.81 20.88-.12 11.96-9.42 21.27-9.31 8.69-21.08 9-11.77.31-21.08-9L205.31-454.69q-5.62-5.62-7.92-11.85-2.31-6.23-2.31-13.46t2.31-13.46q2.3-6.23 7.92-11.85l253.61-253.61q8.31-8.31 20.58-8.5 12.27-.19 21.58 8.5 9.3 9.31 9.3 21.38 0 12.08-9.3 21.39L294.92-510H750q12.77 0 21.38 8.62Q780-492.77 780-480t-8.62 21.38Q762.77-450 750-450H294.92Z" />
    </svg>
);

/**
 * Export icon categories and icons
 */
export { categories, icons };

/**
 * Helper function to get icon by name
 */
export const getIconByName = name => {
    return icons.find(icon => icon.name === name);
};

/**
 * Helper function to get icons by category
 */
export const getIconsByCategory = category => {
    if (category === 'all') {
        return icons;
    }

    return icons.filter(icon => icon.categories.includes(category));
};

/**
 * Helper function to search icons by term
 */
export const searchIcons = searchTerm => {
    if (!searchTerm) {
        return icons;
    }

    const lowerSearchTerm = searchTerm.toLowerCase();
    return icons.filter(icon => icon.name.toLowerCase().includes(lowerSearchTerm) || icon.title.toLowerCase().includes(lowerSearchTerm));
};

/**
 * Returns categories with icon counts
 */
export const getCategories = () => {
    return categories.map(cat => ({
        ...cat,
        count: cat.slug === 'all' ? icons.length : icons.filter(icon => icon.categories.includes(cat.slug)).length
    }));
};

/**
 * Utility function to detect if an icon is "fill" or "line" type.
 * Works with both WordPress icons and custom SVG strings.
 *
 * @param {(Object|string)} icon - Icon object from @wordpress/icons or custom SVG string
 * @return {string} - Returns "fill" or "line" based on icon analysis
 */
export const getIconType = icon => {
    // Default to "fill" if we can't determine
    let iconType = 'fill';

    try {
        // Handle different input types
        let svgString = '';

        if (typeof icon === 'string') {
            // Custom SVG input as string
            svgString = icon;
        } else if (typeof icon === 'object') {
            // WordPress icon object
            if (icon.src) {
                svgString = icon.src;
            } else if (Array.isArray(icon)) {
                // Handle array notation that WordPress sometimes uses for icons
                // Convert the array to a string representation
                svgString = Array.isArray(icon) ? icon.join('') : icon;
            }
        }

        if (!svgString) {
            return iconType; // Return default "line" if we couldn't extract SVG
        }

        // Check for fill attributes or fill style that would indicate a "fill" type icon
        const hasFillAttribute =
            svgString.includes('fill="') && !svgString.includes('fill="none"') && !svgString.includes('fill="transparent"');

        const hasFillStyle =
            svgString.includes('style=') &&
            svgString.includes('fill:') &&
            !svgString.includes('fill:none') &&
            !svgString.includes('fill:transparent');

        // Check for filled shapes that would indicate a "fill" type
        const hasFilledShapes =
            svgString.includes('<rect') ||
            svgString.includes('<circle') ||
            svgString.includes('<ellipse') ||
            svgString.includes('<polygon') ||
            svgString.includes('<path');

        // If it has explicit fill attributes/styles that aren't "none" or has typical filled shapes
        if ((hasFillAttribute || hasFillStyle) && hasFilledShapes) {
            iconType = 'fill';
        }

        // Additional check for stroke attributes that would indicate a "line" type
        const hasStrokeAttributes =
            svgString.includes('stroke="') && !svgString.includes('stroke="none"') && !svgString.includes('stroke-width="0"');

        const hasStrokeStyle = svgString.includes('style=') && svgString.includes('stroke:') && !svgString.includes('stroke:none');

        // If it has stroke attributes but no fill, it's likely a line icon
        if ((hasStrokeAttributes || hasStrokeStyle) && !hasFillAttribute && !hasFillStyle) {
            iconType = 'line';
        }
    } catch (error) {
        console.error('Error detecting icon type:', error);
    }

    return iconType;
};
