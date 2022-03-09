@props(['disabled' => false, 'label' => ''])

<div>
    @if($label !== '')
        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <div class="mt-1 relative rounded-md shadow-sm">
        <input type="text" {{ $disabled ? 'disabled' : '' }} {{$attributes->merge(['name' => '']) ?? ''}}
               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" />
    </div>
</div>
