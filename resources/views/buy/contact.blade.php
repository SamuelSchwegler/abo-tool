<x-app-layout>
    <x-slot name="header">
        {{ __('Kontakt / Login') }}
    </x-slot>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <x-box>
                <x-slot name="title">Ihre Bestellung</x-slot>
                @include('parts.bundle', [$bundle, 'enable_buy' => false])
            </x-box>
        </div>
        <div>
            <x-box>
                <x-slot name="title">Kontakt</x-slot>
                @include('parts.contact', $customer)
                <x-anchor-button href="{{route('buy.payment', $buy)}}">Weiterfahren</x-anchor-button>
            </x-box>
        </div>
    </div>
</x-app-layout>
