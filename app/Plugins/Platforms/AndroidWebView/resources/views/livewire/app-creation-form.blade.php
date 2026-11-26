<div class="space-y-6">
    <!-- Website URL -->
    <div>
        <label for="website-url" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Website URL</label>
        <input type="url" id="website-url" wire:model="url" placeholder="https://siteurl.com" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white @error('url') border-red-500 @enderror">
        @error('url') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
    </div>

    <!-- APP ID -->
    <div>
        <label for="package-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
            APP ID
            <span class="inline-flex items-center text-gray-400 dark:text-gray-500" title="Unique package identifier (cannot be changed after creation)">
                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
            </span>
        </label>
        <div class="relative">
            <input type="text"
                   id="package-name"
                   wire:model.live="package_name"
                   placeholder="com.example.app"
                   @if($auto_generate_package) disabled @endif
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pr-10 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white @error('package_name') border-red-500 @else @if($package_available === true) border-green-500 @elseif($package_available === false) border-red-500 @endif @enderror @if($auto_generate_package) opacity-75 cursor-not-allowed dark:opacity-60 @endif">

            <!-- Validation Icon -->
            @if(!$auto_generate_package && $package_name)
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    @if($package_available === true)
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    @elseif($package_available === false)
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </div>
            @endif
        </div>

        <!-- Auto-generate Checkbox -->
        <div class="flex items-center mt-2">
            <input type="checkbox"
                   id="auto-generate-package"
                   wire:model.live="auto_generate_package"
                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label for="auto-generate-package" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                Auto-generate from app name
            </label>
        </div>

        @error('package_name')
            <small class="text-red-600 dark:text-red-400 text-xs mt-1 block">{{ $message }}</small>
        @else
            <small class="text-gray-500 dark:text-gray-400 text-xs mt-1 block">
                Unique identifier in reverse domain format (e.g., com.company.app). Cannot be changed after creation.
            </small>
        @enderror
    </div>

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
                   class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white @error('theme_color') border-red-500 @enderror">
        </div>
        @error('theme_color')
            <small class="text-red-600 dark:text-red-400 text-xs mt-1 block">{{ $message }}</small>
        @else
            <small class="text-gray-500 dark:text-gray-400 text-xs mt-1 block">Main color used throughout your app. Text colors will use default black/white and can be customized later.</small>
        @enderror
    </div>

    <!-- Layout Template -->
    <div>
        <label class="block mb-3 text-sm font-medium text-gray-900 dark:text-white">Layout template</label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button type="button" wire:click="$set('layout_template', 'app_bar_drawer')" class="flex flex-col items-center p-3 border-2 rounded-lg transition {{ $layout_template === 'app_bar_drawer' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full h-40 mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_104'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_104)'%3e%3crect%20width='170'%20height='310'%20fill='%23F4F4F5'/%3e%3cg%20filter='url(%23filter0_dd_366_104)'%3e%3crect%20width='125'%20height='310'%20fill='%23FAFAFA'/%3e%3c/g%3e%3cpath%20d='M7%2027C7%2024.2386%209.23858%2022%2012%2022H102C104.761%2022%20107%2024.2386%20107%2027C107%2029.7614%20104.761%2032%20102%2032H12C9.23858%2032%207%2029.7614%207%2027Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M7%2050C7%2047.2386%209.23858%2045%2012%2045H102C104.761%2045%20107%2047.2386%20107%2050C107%2052.7614%20104.761%2055%20102%2055H12C9.23858%2055%207%2052.7614%207%2050Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M7%2073C7%2070.2386%209.23858%2068%2012%2068H102C104.761%2068%20107%2070.2386%20107%2073C107%2075.7614%20104.761%2078%20102%2078H12C9.23858%2078%207%2075.7614%207%2073Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M6%2096C6%2093.2386%208.23858%2091%2011%2091H101C103.761%2091%20106%2093.2386%20106%2096C106%2098.7614%20103.761%20101%20101%20101H11C8.23858%20101%206%2098.7614%206%2096Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M7%20119C7%20116.239%209.23858%20114%2012%20114H102C104.761%20114%20107%20116.239%20107%20119C107%20121.761%20104.761%20124%20102%20124H12C9.23858%20124%207%20121.761%207%20119Z'%20fill='%23D1D5DB'/%3e%3c/g%3e%3cdefs%3e%3cfilter%20id='filter0_dd_366_104'%20x='-4'%20y='-2'%20width='133'%20height='320'%20filterUnits='userSpaceOnUse'%20color-interpolation-filters='sRGB'%3e%3cfeFlood%20flood-opacity='0'%20result='BackgroundImageFix'/%3e%3cfeColorMatrix%20in='SourceAlpha'%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%20127%200'%20result='hardAlpha'/%3e%3cfeOffset%20dy='4'/%3e%3cfeGaussianBlur%20stdDeviation='2'/%3e%3cfeColorMatrix%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200.08%200'/%3e%3cfeBlend%20mode='normal'%20in2='BackgroundImageFix'%20result='effect1_dropShadow_366_104'/%3e%3cfeColorMatrix%20in='SourceAlpha'%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%20127%200'%20result='hardAlpha'/%3e%3cfeOffset/%3e%3cfeGaussianBlur%20stdDeviation='1'/%3e%3cfeColorMatrix%20type='matrix'%20values='0%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200%200.04%200'/%3e%3cfeBlend%20mode='normal'%20in2='effect1_dropShadow_366_104'%20result='effect2_dropShadow_366_104'/%3e%3cfeBlend%20mode='normal'%20in='SourceGraphic'%20in2='effect2_dropShadow_366_104'%20result='shape'/%3e%3c/filter%3e%3c/defs%3e%3c/svg%3e"
                         alt="App Bar + Drawer" class="w-full h-full object-contain rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">App Bar + Drawer</span>
            </button>

            <button type="button" wire:click="$set('layout_template', 'app_bar_tabs')" class="flex flex-col items-center p-3 border-2 rounded-lg transition {{ $layout_template === 'app_bar_tabs' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full h-40 mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_94'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_94)'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3crect%20y='277'%20width='170'%20height='33'%20fill='%23F4F4F5'/%3e%3cline%20y1='277.5'%20x2='170'%20y2='277.5'%20stroke='%23E5E7EB'/%3e%3ccircle%20cx='83.7879'%20cy='294.561'%20r='6.84848'%20fill='%23D1D5DB'/%3e%3cpath%20d='M29%20291.712C29%20289.503%2030.7909%20287.712%2033%20287.712H38.697C40.9061%20287.712%2042.697%20289.503%2042.697%20291.712V297.409C42.697%20299.618%2040.9061%20301.409%2038.697%20301.409H33C30.7909%20301.409%2029%20299.618%2029%20297.409V291.712Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M131.691%20289.147C132.453%20287.775%20134.426%20287.775%20135.188%20289.147L140.349%20298.438C141.09%20299.771%20140.126%20301.409%20138.601%20301.409H128.278C126.753%20301.409%20125.789%20299.771%20126.529%20298.438L131.691%20289.147Z'%20fill='%23D1D5DB'/%3e%3crect%20width='170'%20height='33'%20fill='%23F4F4F5'/%3e%3cpath%20d='M11%2014C11%2011.7909%2012.7909%2010%2015%2010H20.697C22.9061%2010%2024.697%2011.7909%2024.697%2014V19.697C24.697%2021.9061%2022.9061%2023.697%2020.697%2023.697H15C12.7909%2023.697%2011%2021.9061%2011%2019.697V14Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M35%2017C35%2014.2386%2037.2386%2012%2040%2012H130C132.761%2012%20135%2014.2386%20135%2017C135%2019.7614%20132.761%2022%20130%2022H40C37.2386%2022%2035%2019.7614%2035%2017Z'%20fill='%23D1D5DB'/%3e%3c/g%3e%3c/svg%3e"
                         alt="App Bar + Navigation Tabs" class="w-full h-full object-contain rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">App Bar + Navigation Tabs</span>
            </button>

            <button type="button" wire:click="$set('layout_template', 'app_bar')" class="flex flex-col items-center p-3 border-2 rounded-lg transition {{ $layout_template === 'app_bar' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full h-40 mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_125'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_125)'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3crect%20width='170'%20height='33'%20fill='%23F4F4F5'/%3e%3cline%20y1='33.5'%20x2='170'%20y2='33.5'%20stroke='%23E5E7EB'/%3e%3cpath%20d='M11%2014C11%2011.7909%2012.7909%2010%2015%2010H20.697C22.9061%2010%2024.697%2011.7909%2024.697%2014V19.697C24.697%2021.9061%2022.9061%2023.697%2020.697%2023.697H15C12.7909%2023.697%2011%2021.9061%2011%2019.697V14Z'%20fill='%23D1D5DB'/%3e%3cpath%20d='M35%2017C35%2014.2386%2037.2386%2012%2040%2012H130C132.761%2012%20135%2014.2386%20135%2017C135%2019.7614%20132.761%2022%20130%2022H40C37.2386%2022%2035%2019.7614%2035%2017Z'%20fill='%23D1D5DB'/%3e%3c/g%3e%3c/svg%3e"
                         alt="App Bar" class="w-full h-full object-contain rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">App Bar</span>
            </button>

            <button type="button" wire:click="$set('layout_template', 'blank')" class="flex flex-col items-center p-3 border-2 rounded-lg transition {{ $layout_template === 'blank' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <div class="w-full h-40 mb-3">
                    <img src="data:image/svg+xml,%3csvg%20width='170'%20height='310'%20viewBox='0%200%20170%20310'%20fill='none'%20xmlns='http://www.w3.org/2000/svg'%3e%3cmask%20id='mask0_366_134'%20style='mask-type:alpha'%20maskUnits='userSpaceOnUse'%20x='0'%20y='0'%20width='170'%20height='310'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/mask%3e%3cg%20mask='url(%23mask0_366_134)'%3e%3crect%20width='170'%20height='310'%20fill='%23FAFAFA'/%3e%3c/g%3e%3c/svg%3e"
                         alt="Blank" class="w-full h-full object-contain rounded" />
                </div>
                <span class="text-xs font-medium text-gray-900 dark:text-white text-center">Blank</span>
            </button>
        </div>
        <small class="text-gray-500 dark:text-gray-400 text-xs mt-2 block">Choose the navigation structure for your app</small>
    </div>
</div>
