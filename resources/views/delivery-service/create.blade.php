<x-app-layout>
    <x-slot name="header">
        {{ __('Lieferdienste bearbeiten') }}
    </x-slot>
    <div class="grid grid-cols-5 gap-8">
        <div>
            @include('delivery-service.parts.side-nav')
        </div>
        <div class="col-span-2">
            <div class="box">
                <h3 class="title">Details Lieferzone</h3>
                <form method="POST" action="{{route('delivery-service.create')}}" class="grid grid-cols-2 gap-4">
                    @csrf
                    @method('POST')
                    <div class="col-span-2">
                        <x-input name="name" label="Bezeichnung"></x-input>
                    </div>
                    <div class="col-span-2">
                        <hr class="mb-2">
                        <x-button>Erstellen</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
