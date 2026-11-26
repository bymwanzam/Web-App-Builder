// Sidebar Component
import { Drawer } from 'flowbite';

export function initSidebar() {
    const pathname = window.location.pathname;
    const currentPage = pathname.split('/').filter(p => p).pop() || 'login';

    // All links use absolute paths from root for clean URLs
    const pathPrefix = '/';

    const sidebarHTML = `
        <!-- Mobile Sidebar Toggle -->
        <button data-drawer-target="sidebar-navigation" data-drawer-toggle="sidebar-navigation" aria-controls="sidebar-navigation" type="button" class="inline-flex items-center p-2 mt-5 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
            <span class="sr-only">Open sidebar</span>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
            </svg>
        </button>

        <!-- Sidebar Navigation -->
        <aside id="sidebar-navigation" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0" aria-label="Sidebar">
            <div class="h-full px-3 py-4 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700 flex flex-col">

                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto pb-4">
                <!-- Logo -->
                <a href="${pathPrefix}user/apps" class="flex items-center mb-8 px-2 group">
                    <div class="relative">
                        <div class="w-11 h-11 bg-gradient-to-br from-blue-500 via-blue-600 to-blue-700 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30 dark:shadow-blue-500/20 group-hover:shadow-blue-500/50 dark:group-hover:shadow-blue-500/30 transition-all duration-300 group-hover:scale-105">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.537 12.625a4.421 4.421 0 0 0 2.684 4.047 10.96 10.96 0 0 1-1.384 2.845c-.834 1.218-1.7 2.432-3.062 2.457-1.34.025-1.77-.794-3.3-.794-1.531 0-2.01.769-3.275.82-1.316.049-2.317-1.318-3.158-2.532-1.72-2.484-3.032-7.017-1.27-10.077A4.9 4.9 0 0 1 8.91 6.884c1.292-.025 2.51.869 3.3.869.789 0 2.27-1.075 3.828-.917a4.67 4.67 0 0 1 3.66 1.984 4.524 4.524 0 0 0-2.16 3.805m-2.52-7.432A4.4 4.4 0 0 0 16.06 2a4.482 4.482 0 0 0-2.945 1.516 4.185 4.185 0 0 0-1.061 3.093 3.708 3.708 0 0 0 2.967-1.416Z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                    </div>
                    <div class="ml-3">
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-400 dark:to-blue-600 bg-clip-text text-transparent">Appy</span>
                        <p class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-0.5">Build native apps</p>
                    </div>
                </a>

                <!-- Dashboard Section -->
                <div class="mb-2 px-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Dashboard</p>
                </div>

                <!-- Navigation Links -->
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="${pathPrefix}user/apps" class="flex items-center p-2 ${currentPage === 'apps' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'apps' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18.5A2.493 2.493 0 0 1 7.51 20H7.5a2.468 2.468 0 0 1-2.4-3.154 2.98 2.98 0 0 1-.85-5.274 2.468 2.468 0 0 1 .92-3.182 2.477 2.477 0 0 1 1.876-3.344 2.5 2.5 0 0 1 3.41-1.856A2.5 2.5 0 0 1 12 5.5m0 13v-13m0 13a2.493 2.493 0 0 0 4.49 1.5h.01a2.468 2.468 0 0 0 2.403-3.154 2.98 2.98 0 0 0 .847-5.274 2.468 2.468 0 0 0-.921-3.182 2.477 2.477 0 0 0-1.875-3.344A2.5 2.5 0 0 0 14.5 3 2.5 2.5 0 0 0 12 5.5m-8 5a2.5 2.5 0 0 1 3.48-2.3m-.28 8.551a3 3 0 0 1-2.953-5.185M20 10.5a2.5 2.5 0 0 0-3.481-2.3m.28 8.551a3 3 0 0 0 2.954-5.185"/>
                            </svg>
                            <span class="ms-3">Apps</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}notifications" class="flex items-center p-2 ${currentPage === 'notifications' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'notifications' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m10.827 5.465-.435-2.324m.435 2.324a5.338 5.338 0 0 1 6.033 4.333l.331 1.769c.44 2.345 2.383 2.588 2.6 3.761.11.586.22 1.171-.31 1.271l-12.7 2.377c-.529.099-.639-.488-.749-1.074C5.813 16.73 7.538 15.8 7.1 13.455c-.219-1.169.218 1.162-.33-1.769a5.338 5.338 0 0 1 4.058-6.221Zm-7.046 4.41c.143-1.877.822-3.461 2.086-4.856m2.646 13.633a3.472 3.472 0 0 0 6.728-.777l.09-.5-6.818 1.277Z"/>
                            </svg>
                            <span class="ms-3">Notifications</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}billing" class="flex items-center p-2 ${currentPage === 'billing' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'billing' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M4 5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H4Zm0 6h16v6H4v-6Z" clip-rule="evenodd"/>
                                <path fill-rule="evenodd" d="M5 14a1 1 0 0 1 1-1h2a1 1 0 1 1 0 2H6a1 1 0 0 1-1-1Zm5 0a1 1 0 0 1 1-1h5a1 1 0 1 1 0 2h-5a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ms-3">Billing</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}settings" class="flex items-center p-2 ${currentPage === 'settings' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'settings' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="square" stroke-linejoin="round" stroke-width="2" d="M10 19H5a1 1 0 0 1-1-1v-1a3 3 0 0 1 3-3h2m10 1a3 3 0 0 1-3 3m3-3a3 3 0 0 0-3-3m3 3h1m-4 3a3 3 0 0 1-3-3m3 3v1m-3-4a3 3 0 0 1 3-3m-3 3h-1m4-3v-1m-2.121 1.879-.707-.707m5.656 5.656-.707-.707m-4.242 0-.707.707m5.656-5.656-.707.707M12 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            <span class="ms-3">Settings</span>
                        </a>
                    </li>
                </ul>

                <!-- Administration Section -->
                <div class="mt-6 mb-2 px-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Administration</p>
                </div>

                <!-- Administration Links -->
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="${pathPrefix}admin/overview" class="flex items-center p-2 ${currentPage === 'overview' && pathname.includes('/admin/') ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'overview' && pathname.includes('/admin/') ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v15a1 1 0 0 0 1 1h15M8 16l2.5-5.5 3 3L17.273 7 20 9.667"/>
                            </svg>
                            <span class="ms-3">Overview</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}admin/users" class="flex items-center p-2 ${currentPage === 'users' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'users' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M4.5 17H4a1 1 0 0 1-1-1 3 3 0 0 1 3-3h1m0-3.05A2.5 2.5 0 1 1 9 5.5M19.5 17h.5a1 1 0 0 0 1-1 3 3 0 0 0-3-3h-1m0-3.05a2.5 2.5 0 1 0-2-4.45m.5 13.5h-7a1 1 0 0 1-1-1 3 3 0 0 1 3-3h3a3 3 0 0 1 3 3 1 1 0 0 1-1 1Zm-1-9.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0Z"/>
                            </svg>
                            <span class="ms-3">Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}admin/roles" class="flex items-center p-2 ${currentPage === 'roles' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'roles' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20a16.405 16.405 0 0 1-5.092-5.804A16.694 16.694 0 0 1 5 6.666L12 4l7 2.667a16.695 16.695 0 0 1-1.908 7.529A16.406 16.406 0 0 1 12 20Z"/>
                            </svg>
                            <span class="ms-3">Roles</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}admin/plans" class="flex items-center p-2 ${currentPage === 'plans' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'plans' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M8 7V6a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1M3 18v-7a1 1 0 0 1 1-1h11a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Zm8-3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                            </svg>
                            <span class="ms-3">Plans</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}admin/subscriptions" class="flex items-center p-2 ${currentPage === 'subscriptions' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'subscriptions' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8H5m12 0a1 1 0 0 1 1 1v2.6M17 8l-4-4M5 8a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.6M5 8l4-4 4 4m6 4h-4a2 2 0 1 0 0 4h4a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/>
                            </svg>
                            <span class="ms-3">Subscriptions</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}admin/transactions" class="flex items-center p-2 ${currentPage === 'transactions' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'transactions' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 18h14M5 18v3h14v-3M5 18l1-9h12l1 9M16 6v3m-4-3v3m-2-6h8v3h-8V3Zm-1 9h.01v.01H9V12Zm3 0h.01v.01H12V12Zm3 0h.01v.01H15V12Zm-6 3h.01v.01H9V15Zm3 0h.01v.01H12V15Zm3 0h.01v.01H15V15Z"/>
                            </svg>
                            <span class="ms-3">Transactions</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}admin/languages" class="flex items-center p-2 ${currentPage === 'languages' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'languages' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m13 19 3.5-9 3.5 9m-6.125-2h5.25M3 7h7m0 0h2m-2 0c0 1.63-.793 3.926-2.239 5.655M7.5 6.818V5m.261 7.655C6.79 13.82 5.521 14.725 4 15m3.761-2.345L5 10m2.761 2.655L10.2 15"/>
                            </svg>
                            <span class="ms-3">Languages</span>
                        </a>
                    </li>
                    <li>
                        <a href="${pathPrefix}admin/plugins" class="flex items-center p-2 ${currentPage === 'plugins' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'plugins' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.99999 10.8571 12 13.1428m-4.00001-2.2857L4 13.1428m3.99999-2.2857.00004-4.57139M12 13.1428v4.5715m0-4.5715-4.00001 2.2857M12 13.1428l4-2.2857m-4 2.2857V8.57143m0 4.57137 4 2.2858m-4 2.2857L7.99999 20M12 17.7143 16 20m-8.00001 0L4 17.7143v-4.5715M7.99999 20v-4.5715M4 13.1428l3.99999 2.2857M16 6.28571 12 4 8.00003 6.28571m7.99997 0v4.57139m0-4.57139-4 2.28572m4 2.28567 4 2.2858M8.00003 6.28571 12 8.57143m8 4.57147v4.5714L16 20m4-6.8571-4 2.2857M16 20v-4.5714"/>
                            </svg>
                            <span class="ms-3">Plugins</span>
                        </a>
                    </li>
                </ul>

                <!-- Documents Section -->
                <div class="mt-6 mb-2 px-2">
                    <p class="text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">Documents</p>
                </div>

                <!-- Documents Links -->
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="${pathPrefix}docs/api" class="flex items-center p-2 ${currentPage === 'api' ? 'text-white bg-blue-700' : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'} rounded-lg group">
                            <svg class="w-5 h-5 ${currentPage === 'api' ? '' : 'text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white'}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 8-4 4 4 4m8 0 4-4-4-4m-2-3-4 14"/>
                            </svg>
                            <span class="ms-3">API</span>
                        </a>
                    </li>
                </ul>
                </div>

                <!-- Bottom Actions (Always at bottom) -->
                <div class="pt-4 flex items-center justify-between">
                    <!-- Dark Mode Toggle -->
                    <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/>
                        </svg>
                    </button>

                    <!-- Logout Button -->
                    <a href="${pathPrefix}login" class="text-gray-500 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 focus:outline-none focus:ring-4 focus:ring-red-200 dark:focus:ring-red-900 rounded-lg text-sm p-2.5 transition-colors" title="Logout">
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H8m12 0-4 4m4-4-4-4M9 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h2"/>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>
    `;

    // Insert sidebar at the beginning of body
    const sidebarContainer = document.getElementById('sidebar-container');
    if (sidebarContainer) {
        sidebarContainer.innerHTML = sidebarHTML;

        // Initialize Flowbite drawer for mobile sidebar after DOM insertion
        // Wait for next tick to ensure DOM is updated
        setTimeout(() => {
            // Check if Flowbite is available and has Drawer component
            if (typeof Drawer !== 'undefined') {
                const sidebarElement = document.getElementById('sidebar-navigation');
                const toggleButton = document.querySelector('[data-drawer-toggle="sidebar-navigation"]');

                if (sidebarElement && toggleButton) {
                    // Initialize Flowbite Drawer
                    const drawer = new Drawer(sidebarElement, {
                        placement: 'left',
                        backdrop: true,
                        bodyScrolling: false,
                        edge: false,
                        edgeOffset: '',
                        backdropClasses: 'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-30'
                    });

                    // Attach click handler to toggle button
                    toggleButton.addEventListener('click', () => {
                        drawer.toggle();
                    });

                    // Close drawer when clicking backdrop
                    const backdrop = document.querySelector('[drawer-backdrop]');
                    if (backdrop) {
                        backdrop.addEventListener('click', () => {
                            drawer.hide();
                        });
                    }
                }
            }
        }, 0);
    }
}
