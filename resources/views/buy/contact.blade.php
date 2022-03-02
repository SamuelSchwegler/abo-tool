<x-app-layout>
    <x-slot name="header">
        {{ __('Kontakt / Login') }}
    </x-slot>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-box>
                <x-slot name="title">Ihre Bestellung</x-slot>
                @include('parts.bundle', $bundle)
            </x-box>
        </div>
        <div>
            @include('parts.contact', $customer)
        </div>
    </div>
</x-app-layout>
