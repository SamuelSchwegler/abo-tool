<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lieferungen') }}
        </h2>
    </x-slot>

    <div class="pt-12 pb-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-box>
                <p>Hier sehen Sie die kommenden Lieferungen...</p>
            </x-box>
        </div>
    </div>
    <div class="pb-12 mx-auto max-w-7xl lg:px-8">
        <div class="grid grid-cols-2 gap-4">
            <x-box>
                <x-slot name="title">Kommende Lieferungen</x-slot>
                @foreach($next_deliveries as $customer_delivery)
                    {{$customer_delivery->delivery->date->format('d.m.y H:i')}}<br>
                @endforeach
            </x-box>
            <x-box>
                <x-slot name="title">Meine Abos</x-slot>
                <x-table>
                    <x-slot name="header">
                        <x-table-cell>Abo</x-table-cell>
                        <x-table-cell>Intervall</x-table-cell>
                        <x-table-cell>Guthaben</x-table-cell>
                        <x-table-cell>davon geplant</x-table-cell>
                        <x-table-cell>voraussichtliches Ende</x-table-cell>
                    </x-slot>
                    <tr>
                        <x-table-cell>Gemüseabo gross</x-table-cell>
                        <x-table-cell>bla</x-table-cell>
                        <x-table-cell>6</x-table-cell>
                        <x-table-cell>3</x-table-cell>
                        <x-table-cell>28.04.2022</x-table-cell>
                    </tr>
                </x-table>
            </x-box>
        </div>
    </div>
</x-app-layout>
