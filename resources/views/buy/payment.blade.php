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
                <x-slot name="title">Bezahlung</x-slot>
                <p>Sie erhalten eine Email an {{$customer->user->email}} mit der Rechnung für Ihre Bestellung.</p>
                <form action="{{route('buy.payment', $buy)}}" method="POST">
                    @csrf
                    @method('POST')
                    <x-button>Weiterfahren</x-button>
                </form>
            </x-box>
        </div>
    </div>
</x-app-layout>
