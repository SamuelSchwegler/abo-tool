<x-app-layout>
    <x-slot name="header">
        {{ __('Lieferungen') }}
    </x-slot>


    <x-box>
        <p>Hier sehen Sie die kommenden Lieferungen...</p>
    </x-box>

    <div class="mt-4 grid grid-cols-2 gap-4">
        <manage-orders :orders="{{json_encode($next_orders)}}"></manage-orders>
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
</x-app-layout>
