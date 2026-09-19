/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Flex, FlexBlock, Button } from '@wordpress/components';

/**
 * Header Component
 * Displays tab navigation for the icon library modal
 */
export const Header = ({ activeTab, onTabChange }) => {
    // Define tabs
    const tabs = [
        { label: __('Library', 'dentist-exchange'), value: 'library' },
        { label: __('My Icons', 'dentist-exchange'), value: 'my-icons' },
        { label: __('Custom SVG', 'dentist-exchange'), value: 'custom' }
    ];

    return (
        <Flex gap={2}>
            <FlexBlock>
                <div className="dnte-modal__tabs">
                    {tabs.map(tab => (
                        <Button
                            key={tab.value}
                            className={`dnte-modal__tabs-button ${activeTab === tab.value ? 'is-selected' : ''}`}
                            onClick={() => onTabChange(tab.value)}
                            disabled={tab.disabled}
                            __nextHasNoMarginBottom
                            __next40pxDefaultSize
                        >
                            {tab.label}
                        </Button>
                    ))}
                </div>
            </FlexBlock>
        </Flex>
    );
};
