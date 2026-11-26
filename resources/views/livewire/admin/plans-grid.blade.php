<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    @foreach($plans as $plan)
        <div class="bg-white border border-gray-200 rounded-lg shadow-xs p-6 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                @if($plan->status === 'active')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Active</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">Inactive</span>
                @endif
            </div>
            <div class="mb-4">
                <span class="text-4xl font-bold text-gray-900 dark:text-white">${{ number_format($plan->price, 0) }}</span>
                <span class="text-gray-500 dark:text-gray-400">/month</span>
            </div>
            <ul class="space-y-2 mb-6">
                @foreach($plan->all_features as $feature)
                    @if($feature['included'])
                        <li class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature['name'] }}
                        </li>
                    @else
                        <li class="flex items-center text-sm text-gray-400 dark:text-gray-500">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature['name'] }}
                        </li>
                    @endif
                @endforeach
            </ul>
            <div class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                <p><span class="font-semibold text-gray-900 dark:text-white">{{ $plan->subscriber_count }}</span> subscribers</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="$dispatch('edit-plan', { id: {{ $plan->id }} })" class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-700 rounded-lg hover:bg-blue-800">
                    Edit
                </button>
                @if(!$plan->is_default)
                    <button wire:click="$dispatch('delete-plan', { id: {{ $plan->id }}, name: '{{ addslashes($plan->name) }}' })" class="px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 dark:bg-gray-700 dark:text-red-400 dark:border-red-600 dark:hover:bg-red-900/20">
                        Delete
                    </button>
                @endif
            </div>
        </div>
    @endforeach
</div>
