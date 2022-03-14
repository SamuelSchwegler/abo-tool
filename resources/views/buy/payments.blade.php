<x-app-layout>
    <x-slot name="header">
        Zahlungen
    </x-slot>
    <div class="grid grid-cols-1 gap-4">
        <div>
            <x-box>
                <x-slot name="title">Zahlungen</x-slot>
                <x-table>
                    <x-slot name="header">
                        <x-table-cell>ID</x-table-cell>
                        <x-table-cell>Status</x-table-cell>
                        <x-table-cell>Export</x-table-cell>
                    </x-slot>
                    @foreach($buys as $buy)
                        <tr>
                            <x-table-cell>{{$buy->id}}</x-table-cell>
                            <x-table-cell>{{$buy->paid}}</x-table-cell>
                            <x-table-cell>
                                <x-anchor-button target="_blank" href="{{route('buy.export.bill', $buy)}}" title="PDF Download">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </x-anchor-button>
                            </x-table-cell>
                        </tr>
                    @endforeach
                </x-table>
            </x-box>
        </div>
    </div>
</x-app-layout>
