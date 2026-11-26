<div>
<form wire:submit.prevent="save">

    <!-- Layout Template Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Layout Template</h3>
                <p class="settings-card-description">Choose the navigation structure for your app</p>
            </div>
        </div>

        <!-- Layout Template Selection -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <button type="button" wire:click="$set('layout_template', 'app_bar_drawer')"
                    class="flex flex-col items-center p-4 border-2 rounded-lg transition {{ $layout_template === 'app_bar_drawer' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_104'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_104)'%3e%3crect%20width='170'%20height='310'%20fill='%23F4F4F5'/%3e%3cg%20filter='url(%23filter0_dd_366_104)'%3e%3crect%20width='125'%20height='310'%20fill='%23FAFAFA'/%3e%3c/g%3e%3cpath%20d='M7%2027C7%2024.2386%209.23858%2022%2012%2022H102C104.761%2022%20107%2024.2386%20107%2027C107%2029.7614%20104.761%2032%20102%2032H12C9.23858%2032%207%2029.7614%207%2027Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M7%2050C7%2047.2386%209.23858%2045%2012%2045H102C104.761%2045%20107%2047.2386%20107%2050C107%2052.7614%20104.761%2055%20102%2055H12C9.23858%2055%207%2052.7614%207%2050Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M7%2073C7%2070.2386%209.23858%2068%2012%2068H102C104.761%2068%20107%2070.2386%20107%2073C107%2075.7614%20104.761%2078%20102%2078H12C9.23858%2078%207%2075.7614%207%2073Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M6%2096C6%2093.2386%208.23858%2091%2011%2091H101C103.761%2091%20106%2093.2386%20106%2096C106%2098.7614%20103.761%20101%20101%20101H11C8.23858%20101%206%2098.7614%206%2096Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M7%20119C7%20116.239%209.23858%20114%2012%20114H102C104.761%20114%20107%20116.239%20107%20119C107%20121.761%20104.761%20124%20102%20124H12C9.23858%20124%207%20121.761%207%20119Z'%20fill='%23D1D5DB'/%3e%3c/g%3e%3cdefs%3e%3cfilter%20id='filter0_dd_366_104'%20x='-4'%20y='-2'%20width='133'%20height='320'%20filterUnits='userSpaceOnUse'%20color-interpolation-filters='sRGB'%3e%3cfeFlood%20flood-opacity='0'%20result='BackgroundImageFix'/%3e%3cfeColorMatrix%20in='SourceAlpha'%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%20127%200'%20result='hardAlpha'/%3e%3cfeOffset%20dy='4'/%3e%3cfeGaussianBlur%20stdDeviation='2'/%3e%3cfeColorMatrix%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200.08%200'/%3e%3cfeBlend%20mode='normal'%20in2='BackgroundImageFix'%20result='effect1_dropShadow_366_104'/%3e%3cfeColorMatrix%20in='SourceAlpha'%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%20127%200'%20result='hardAlpha'/%3e%3cfeOffset/%3e%3cfeGaussianBlur%20stdDeviation='1'/%3e%3cfeColorMatrix%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200.04%200'/%3e%3cfeBlend%20mode='normal'%20in2='effect1_dropShadow_366_104'%20result='effect2_dropShadow_366_104'/%3e%3cfeBlend%20mode='normal'%20in='SourceGraphic'%20in2='effect2_dropShadow_366_104'%20result='shape'/%3e%3c/filter%3e%3c/defs%3e%3c/svg%3e"
                         alt="App Bar + Drawer" class="w-full rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">App Bar + Drawer</span>
            </button>

            <button type="button" wire:click="$set('layout_template', 'app_bar_tabs')"
                    class="flex flex-col items-center p-4 border-2 rounded-lg transition {{ $layout_template === 'app_bar_tabs' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_94'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_94)'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3crect%20y='277'%20width='170'%20height='33'%20fill='%23F4F4F5'/%3e%3cline%20y1='277.5'%20x2='170'%20y2='277.5'%20stroke='%23E5E7EB'/%3e%3ccircle%20cx='83.7879'%20cy='294.561'%20r='6.84848'%20fill='%23D1D5DB'/%3e%3cpath%20d='M29%20291.712C29%20289.503%2030.7909%20287.712%2033%20287.712H38.697C40.9061%20287.712%2042.697%20289.503%2042.697%20291.712V297.409C42.697%20299.618%2040.9061%20301.409%2038.697%20301.409H33C30.7909%20301.409%2029%20299.618%2029%20297.409V291.712Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M131.691%20289.147C132.453%20287.775%20134.426%20287.775%20135.188%20289.147L140.349%20298.438C141.09%20299.771%20140.126%20301.409%20138.601%20301.409H128.278C126.753%20301.409%20125.789%20299.771%20126.529%20298.438L131.691%20289.147Z'%20fill='%23D1D5DB'/%3e%3crect%20width='170'%20height='33'%20fill='%23F4F4F5'/%3e%3cpath%20d='M11%2014C11%2011.7909%2012.7909%2010%2015%2010H20.697C22.9061%2010%2024.697%2011.7909%2024.697%2014V19.697C24.697%2021.9061%2022.9061%2023.697%2020.697%2023.697H15C12.7909%2023.697%2011%2021.9061%2011%2019.697V14Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M35%2017C35%2014.2386%2037.2386%2012%2040%2012H130C132.761%2012%20135%2014.2386%20135%2017C135%2019.7614%20132.761%2022%20130%2022H40C37.2386%2022%2035%2019.7614%2035%2017Z'%20fill='%23D1D5DB'/%3e%3c/g%3e%3c/svg%3e"
                         alt="App Bar + Navigation Tabs" class="w-full rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">App Bar + Navigation Tabs</span>
            </button>

            <button type="button" wire:click="$set('layout_template', 'app_bar')"
                    class="flex flex-col items-center p-4 border-2 rounded-lg transition {{ $layout_template === 'app_bar' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_125'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_125)'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3crect%20width='170'%20height='33'%20fill='%23F4F4F5'/%3e%3cline%20y1='33.5'%20x2='170'%20y2='33.5'%20stroke='%23E5E7EB'/%3e%3cpath%20d='M11%2014C11%2011.7909%2012.7909%2010%2015%2010H20.697C22.9061%2010%2024.697%2011.7909%2024.697%2014V19.697C24.697%2021.9061%2022.9061%2023.697%2020.697%2023.697H15C12.7909%2023.697%2011%2021.9061%2011%2019.697V14Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M35%2017C35%2014.2386%2037.2386%2012%2040%2012H130C132.761%2012%20135%2014.2386%20135%2017C135%2019.7614%20132.761%2022%20130%2022H40C37.2386%2022%2035%2019.7614%2035%2017Z'%20fill='%23D1D5DB'/%3e%3c/g%3e%3c/svg%3e"
                         alt="App Bar" class="w-full rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">App Bar</span>
            </button>

            <button type="button" wire:click="$set('layout_template', 'blank')"
                    class="flex flex-col items-center p-4 border-2 rounded-lg transition {{ $layout_template === 'blank' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_134'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_134)'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/g%3e%3c/svg%3e"
                         alt="Blank" class="w-full rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">Blank</span>
            </button>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-6">
            <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                Progress Indicator
            </label>
            <div class="flex gap-4">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="progress_indicator" value="disable" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Disable</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="progress_indicator" value="linear" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Linear</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="progress_indicator" value="circular" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:bg-gray-700 dark:border-gray-600">
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Circular</span>
                </label>
            </div>
            <small class="helper-text">Loading indicator style shown when pages are loading</small>
        </div>

        <!-- Progress Indicator Color (only show when not disabled) -->
        @if($progress_indicator !== 'disable')
            <div class="mb-6" x-data="{ color: @entangle('progress_indicator_color').live }">
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Progress Indicator Color
                </label>
                <div class="flex items-center gap-3">
                    <input type="color" x-model="color"
                           class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                    <input type="text" x-model="color"
                           placeholder="#3b82f6"
                           class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                <small class="helper-text">Color of the loading progress indicator</small>
            </div>
        @endif

        <!-- App Bar Page Title (only show when layout is not blank) -->
        @if($layout_template !== 'blank')
            <div class="mb-6">
                <x-toggle
                    wire-model="show_app_bar_title"
                    :live="true"
                    label="Show current webpage title in the app bar"
                />
                <small class="helper-text">Display the current page's title in the top bar</small>
            </div>
        @endif

        <!-- Navigation Tab Colors (only show when layout is app_bar_tabs) -->
        @if($layout_template === 'app_bar_tabs')
            <div class="space-y-6">
                <!-- Navigation Tab Icon Colors -->
                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                        Navigation Tab Icon Colors
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Light Mode</label>
                            <div x-data="{ color: @entangle('nav_tab_icon_color_light').live }" class="flex items-center gap-3">
                                <input type="color" x-model="color"
                                       class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                                <input type="text" x-model="color"
                                       placeholder="#6B7280"
                                       class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Dark Mode</label>
                            <div x-data="{ color: @entangle('nav_tab_icon_color_dark').live }" class="flex items-center gap-3">
                                <input type="color" x-model="color"
                                       class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                                <input type="text" x-model="color"
                                       placeholder="#6B7280"
                                       class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <small class="helper-text">Color of inactive tab icons in light and dark mode</small>
                </div>

                <!-- Active Tab Colors -->
                <div>
                    <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                        Active Tab Colors
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Light Mode</label>
                            <div x-data="{ color: @entangle('active_tab_color_light').live }" class="flex items-center gap-3">
                                <input type="color" x-model="color"
                                       class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                                <input type="text" x-model="color"
                                       placeholder="#3B82F6"
                                       class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block mb-2 text-xs font-medium text-gray-700 dark:text-gray-300">Dark Mode</label>
                            <div x-data="{ color: @entangle('active_tab_color_dark').live }" class="flex items-center gap-3">
                                <input type="color" x-model="color"
                                       class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                                <input type="text" x-model="color"
                                       placeholder="#3B82F6"
                                       class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <small class="helper-text">Color of the selected tab in light and dark mode</small>
                </div>
            </div>
        @endif
    </div>

    <!-- Theme Colors Card -->
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v11a3 3 0 106 0V4a2 2 0 00-2-2H4zm1 14a1 1 0 100-2 1 1 0 000 2zm5-1.757l4.9-4.9a2 2 0 000-2.828L13.485 5.1a2 2 0 00-2.828 0L10 5.757v8.486zM16 18H9.071l6-6H16a2 2 0 012 2v2a2 2 0 01-2 2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Theme Colors</h3>
                <p class="settings-card-description">Primary colors that define your app's appearance</p>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Theme Color -->
            <div x-data="{ color: @entangle('theme_color').live }">
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Theme Color
                </label>
                <div class="flex items-center gap-3">
                    <input type="color" x-model="color"
                           class="h-12 w-24 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                    <input type="text" x-model="color"
                           placeholder="#3b82f6"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                </div>
                @error('theme_color')
                    <small class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</small>
                @enderror
                <small class="helper-text">Main color used throughout your app</small>

                <!-- Dynamic Colors Toggle (Android 12+) -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-toggle
                        wire-model="enable_dynamic_colors"
                        :live="true"
                        label="Enable Material You Dynamic Colors (Android 12+)"
                    />
                    <small class="helper-text">When enabled, your app will adapt colors from the user's wallpaper on Android 12+ devices. This overrides the theme color above.</small>
                </div>
            </div>

            <!-- Text Color (Light Mode) -->
            <div>
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Text Color (Light Mode)
                </label>
                <div x-data="{ color: @entangle('text_color_light') }" class="flex gap-3">
                    <input type="color" x-model="color" class="h-10 w-20 rounded cursor-pointer border border-gray-300 dark:border-gray-600">
                    <input type="text" x-model="color" placeholder="#000000"
                           class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                <small class="helper-text">Default text and icon color in light mode</small>
            </div>

            <!-- Text Color (Dark Mode) -->
            <div>
                <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">
                    Text Color (Dark Mode)
                </label>
                <div x-data="{ color: @entangle('text_color_dark') }" class="flex gap-3">
                    <input type="color" x-model="color" class="h-10 w-20 rounded cursor-pointer border border-gray-300 dark:border-gray-600">
                    <input type="text" x-model="color" placeholder="#FFFFFF"
                           class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                </div>
                <small class="helper-text">Default text and icon color in dark mode</small>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="flex items-center justify-between pt-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-hidden focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-3 text-center dark:bg-blue-600 dark:hover:bg-blue-700 transition-all">
            {{ __('Save') }}
        </button>
    </div>

</form>
</div>
