<div class="flex bg-white rounded-md shadow-md">
    <div class="flex-none w-64 relative">
        <img src="/tasche.jpg" alt="" class="absolute inset-0 w-full h-full object-cover rounded-l-md"/>
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
        @if($enable_buy ?? true)
            <div class="flex space-x-4 my-6 text-sm font-medium">
                <div class="flex-auto flex space-x-4">
                    <x-anchor-button href="{{route('buy.contact', $bundle)}}">
                        Bestellen
                    </x-anchor-button>
                </div>
            </div>
        @endif
    </form>
</div>
