<x-app-layout>
    <x-slot name="header">
        {{ __('Lieferungen') }}
    </x-slot>
    <x-box>
        <p>Hier sehen Sie die kommenden Lieferungen...</p>
    </x-box>
    <customer-orders :orders="{{$next_orders->toJson()}}" :input_product_balances="{{$product_balances->toJson()}}"></customer-orders>
</x-app-layout>
