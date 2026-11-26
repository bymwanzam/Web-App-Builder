<div class="bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Plan Configuration</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure default plan settings for new users</p>
    </div>
    <form wire:submit="save" class="p-6 space-y-4">
        <!-- Default Plan -->
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Default Plan</label>
            <select wire:model="default_plan_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                <option value="">None (require subscription)</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ number_format($plan->price, 2) }}/month</option>
                @endforeach
            </select>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Plan assigned to new users. Select "None" to require subscription before use.</p>
            @error('default_plan_id') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
        </div>

        @if(config('app.demo'))
            <button type="button" disabled title="Disabled in demo mode" class="text-white bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 cursor-not-allowed opacity-50">
                {{ __('Save') }}
            </button>
        @else
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-hidden dark:focus:ring-blue-800">
                {{ __('Save') }}
            </button>
        @endif
    </form>
</div>
