<x-app-layout>
    <x-slot name="header">
        {{ __('Kontakt / Login') }}
    </x-slot>
    <div class="grid grid-cols-1 gap-4">
        <div>
            <x-box>
                <x-slot name="title">Danke für ihre Bestellung</x-slot>
                <p>Sobald wir ihre Zahlung erhalten haben, werden die Lieferfenster für Sie freigeschaltet.</p>
            </x-box>
        </div>
    </div>
</x-app-layout>
