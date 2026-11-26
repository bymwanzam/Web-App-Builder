import './bootstrap';
import { initFlowbite } from 'flowbite';
import { initSidebar } from './sidebar.js';

// Alpine.js is already included and initialized by Livewire 3
// DO NOT initialize Alpine manually - it will create conflicts with Livewire

// Function to update active navigation state after SPA navigation
function updateNavigationActiveState() {
    const currentPath = window.location.pathname;

    // Get all navigation links in the sidebar (exclude logo by selecting only links inside <ul>)
    const navLinks = document.querySelectorAll('#sidebar-navigation ul a[wire\\:navigate]');

    navLinks.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        const isActive = linkPath === currentPath;

        // Get the parent li or the link itself
        const linkElement = link;
        const svgElement = link.querySelector('svg');

        if (isActive) {
            // Add active classes
            linkElement.classList.remove('text-gray-900', 'dark:text-white', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
            linkElement.classList.add('text-white', 'bg-blue-700');

            // Update SVG classes
            if (svgElement) {
                svgElement.classList.remove('text-gray-500', 'dark:text-gray-400', 'group-hover:text-gray-900', 'dark:group-hover:text-white');
            }
        } else {
            // Remove active classes
            linkElement.classList.remove('text-white', 'bg-blue-700');
            linkElement.classList.add('text-gray-900', 'dark:text-white', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');

            // Update SVG classes
            if (svgElement) {
                svgElement.classList.add('text-gray-500', 'transition', 'duration-75', 'dark:text-gray-400', 'group-hover:text-gray-900', 'dark:group-hover:text-white');
            }
        }
    });
}

// Function to initialize theme toggle
function initThemeToggle() {
    // Array of theme toggle configurations (main sidebar, app settings sidebar, and landing page)
    const toggleConfigs = [
        {
            btn: 'theme-toggle',
            darkIcon: 'theme-toggle-dark-icon',
            lightIcon: 'theme-toggle-light-icon'
        },
        {
            btn: 'theme-toggle-settings',
            darkIcon: 'theme-toggle-dark-icon-settings',
            lightIcon: 'theme-toggle-light-icon-settings'
        },
        {
            btn: 'theme-toggle-landing',
            darkIcon: 'theme-toggle-dark-icon-landing',
            lightIcon: 'theme-toggle-light-icon-landing'
        }
    ];

    toggleConfigs.forEach(config => {
        var themeToggleDarkIcon = document.getElementById(config.darkIcon);
        var themeToggleLightIcon = document.getElementById(config.lightIcon);
        var themeToggleBtn = document.getElementById(config.btn);

        // Only initialize theme toggle if elements exist
        if (themeToggleBtn && themeToggleDarkIcon && themeToggleLightIcon) {
            // Remove any existing event listeners by cloning the button
            var newThemeToggleBtn = themeToggleBtn.cloneNode(true);
            themeToggleBtn.parentNode.replaceChild(newThemeToggleBtn, themeToggleBtn);
            themeToggleBtn = newThemeToggleBtn;

            // Update icon references after cloning
            themeToggleDarkIcon = document.getElementById(config.darkIcon);
            themeToggleLightIcon = document.getElementById(config.lightIcon);

            // Reset both icons to hidden first
            themeToggleDarkIcon.classList.add('hidden');
            themeToggleLightIcon.classList.add('hidden');

            // Determine if dark mode should be active based on: localStorage > admin default > system
            function shouldUseDarkMode() {
                const savedTheme = localStorage.getItem('color-theme');
                if (savedTheme) {
                    return savedTheme === 'dark';
                }
                // Use admin default theme if set
                const defaultTheme = window.defaultTheme || 'system';
                if (defaultTheme === 'system') {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches;
                }
                return defaultTheme === 'dark';
            }

            // Change the icons inside the button based on current theme
            if (shouldUseDarkMode()) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            // Ensure dark class on <html> matches (critical for Livewire navigation)
            if (shouldUseDarkMode()) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            themeToggleBtn.addEventListener('click', function() {
                // Get fresh references to both sets of icons
                const allDarkIcons = document.querySelectorAll('[id^="theme-toggle-dark-icon"]');
                const allLightIcons = document.querySelectorAll('[id^="theme-toggle-light-icon"]');

                // Toggle all icons
                allDarkIcons.forEach(icon => icon.classList.toggle('hidden'));
                allLightIcons.forEach(icon => icon.classList.toggle('hidden'));

                // if set via local storage previously
                if (localStorage.getItem('color-theme')) {
                    if (localStorage.getItem('color-theme') === 'light') {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    }
                // if NOT set via local storage previously
                } else {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('color-theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('color-theme', 'dark');
                    }
                }
            });
        }
    });
}

// ========================================
// App Settings: Keyboard Shortcuts
// ========================================

/**
 * Initialize app settings functionality (keyboard shortcuts)
 * Safe to call multiple times (idempotent)
 */
function initAppSettings() {
    // Only initialize on app editor page
    const isAppEditorPage = document.querySelector('[data-app-editor]') !== null;
    if (!isAppEditorPage) return;

    // Initialize keyboard shortcut
    initSaveShortcut();
}

/**
 * Initialize Cmd/Ctrl+S keyboard shortcut for saving
 * Uses stored handler reference for cleanup
 */
function initSaveShortcut() {
    // Remove existing listener to prevent accumulation
    if (window._appSettingsSaveHandler) {
        document.removeEventListener('keydown', window._appSettingsSaveHandler);
    }

    // Create and store new handler
    window._appSettingsSaveHandler = function(e) {
        // Double-check we're on the right page
        if (!document.querySelector('[data-app-editor]')) return;

        // Check for Cmd+S (Mac) or Ctrl+S (Windows/Linux)
        if ((e.metaKey || e.ctrlKey) && e.key === 's') {
            e.preventDefault(); // Prevent browser's default save dialog

            // Find and trigger the save button
            const saveButton = document.querySelector('button[type="submit"]');
            if (saveButton) {
                saveButton.click();
            }
        }
    };

    document.addEventListener('keydown', window._appSettingsSaveHandler);
}

// Hide preloader when page is fully loaded
window.addEventListener('load', function() {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.classList.add('opacity-0');
        setTimeout(() => {
            preloader.classList.add('hidden');
        }, 300);
    }
});

// Livewire navigation events for SPA-style page transitions
document.addEventListener('livewire:navigating', () => {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.classList.remove('hidden', 'opacity-0');
    }

    // Add transitioning class to main content for smooth fade-out
    const mainContent = document.getElementById('main-content');
    if (mainContent) {
        mainContent.classList.remove('page-ready');
        mainContent.classList.add('page-transitioning');
    }
});

document.addEventListener('livewire:navigated', () => {
    // Force theme reapplication immediately after navigation (defensive failsafe)
    // Priority: localStorage > admin default > system preference
    const savedTheme = localStorage.getItem('color-theme');
    let shouldBeDark = false;

    if (savedTheme === 'dark') {
        shouldBeDark = true;
    } else if (savedTheme === 'light') {
        shouldBeDark = false;
    } else {
        // No saved preference, check admin default
        const defaultTheme = window.defaultTheme || 'system';
        if (defaultTheme === 'system') {
            shouldBeDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        } else {
            shouldBeDark = defaultTheme === 'dark';
        }
    }

    if (shouldBeDark) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    // Fade in main content smoothly
    const mainContent = document.getElementById('main-content');
    if (mainContent) {
        // Small delay to ensure DOM is ready
        requestAnimationFrame(() => {
            mainContent.classList.remove('page-transitioning');
            mainContent.classList.add('page-ready');
        });
    }

    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.classList.add('opacity-0');
        setTimeout(() => {
            preloader.classList.add('hidden');
        }, 300);
    }

    // Update active navigation state
    updateNavigationActiveState();

    // Reinitialize Flowbite after Livewire navigation
    initFlowbite();

    // Reinitialize theme toggle after Livewire navigation
    initThemeToggle();

    // Reinitialize app settings after Livewire navigation
    initAppSettings();

    // Reinitialize ApexCharts if present on the page
    if (typeof ApexCharts !== 'undefined') {
        // Find all chart containers on the page
        const chartContainers = document.querySelectorAll('[data-chart-id]');

        chartContainers.forEach(container => {
            const chartId = container.getAttribute('data-chart-id');

            // Clear existing chart if any
            if (window[chartId]) {
                try {
                    window[chartId].destroy();
                } catch (e) {
                    console.log('Chart already destroyed or not initialized');
                }
            }

            // Get chart options from data attribute (set by Livewire component)
            const chartOptions = container.getAttribute('data-chart-options');
            if (chartOptions) {
                try {
                    const options = JSON.parse(chartOptions);
                    window[chartId] = new ApexCharts(container, options);
                    window[chartId].render();
                } catch (e) {
                    console.error('Error initializing chart:', e);
                }
            }
        });
    }
});

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flowbite components
    initFlowbite();

    // Initialize sidebar (removed - now uses Blade component <x-sidebar />)
    // initSidebar();

    // Set initial active state
    updateNavigationActiveState();

    // Initialize theme toggle
    initThemeToggle();

    // Initialize app settings (keyboard shortcuts)
    initAppSettings();
});

// Toast Notification Helper
window.showToast = function(message, type = 'error') {
    const toast = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');

    if (toast && toastMessage) {
        toastMessage.textContent = message;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
};
