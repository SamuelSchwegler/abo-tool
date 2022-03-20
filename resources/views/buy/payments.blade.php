<x-app-layout>
    <x-slot name="header">
        Zahlungen
    </x-slot>
    <manage-payments :input_buys="{{$buys->toJson()}}"></manage-payments>
</x-app-layout>
