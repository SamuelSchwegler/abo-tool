<x-app-layout>
    <x-slot name="header">
        {{ __('Lieferdienste bearbeiten') }}
    </x-slot>
    <div class="grid grid-cols-5 gap-8">
        <div>
            @include('delivery-service.parts.side-nav')
        </div>
        <div class="col-span-2">
            <delivery-service-edit :service="{{json_encode($serviceResource)}}"></delivery-service-edit>
        </div>
        <div class="col-span-2">
            <postcode-management :service="{{json_encode($serviceResource)}}"></postcode-management>
        </div>
    </div>
</x-app-layout>
