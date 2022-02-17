<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Home') }}
        </h2>
    </x-slot>

    <div class="pt-12 pb-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p>Abonnieren Sie unser Bio-Gemüse, und wir liefern es Ihnen frisch vom Feld bis vor die Haustür. Jede Woche neu.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="pb-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-2 gap-4">
            @foreach($bundles as $bundle)
                <div class="flex bg-white rounded-md shadow-md">
                    <div class="flex-none w-64 relative">
                        <img src="/tasche.jpg" alt="" class="absolute inset-0 w-full h-full object-cover rounded-l-md" />
                    </div>
                    <form class="flex-auto p-6">
                        <div class="flex flex-wrap">
                            <h1 class="flex-auto text-lg font-semibold text-slate-900">
                                {{$bundle->name}}
                            </h1>
                            <div class="text-lg font-semibold text-slate-500">
                                {{$bundle->price}} CHF
                            </div>
                        </div>
                        <div class="flex space-x-4 my-6 text-sm font-medium">
                            <div class="flex-auto flex space-x-4">
                                <x-button>
                                    Bestellen
                                </x-button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
