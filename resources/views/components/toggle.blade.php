@props([
    'wireModel' => null,
    'id' => null,
    'label' => '',
    'disabled' => false,
    'checked' => false,
    'live' => false,
])

<label class="inline-flex items-center cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input
        type="checkbox"
        @if($id) id="{{ $id }}" @endif
        @if($wireModel && $live) wire:model.live="{{ $wireModel }}" @elseif($wireModel) wire:model="{{ $wireModel }}" @endif
        @if($checked) checked @endif
        @if($disabled) disabled @endif
        class="sr-only peer"
    >
    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-hidden peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600 dark:peer-checked:bg-blue-500"></div>
    @if($label)
        <span class="select-none ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">{{ $label }}</span>
    @endif
</label>
