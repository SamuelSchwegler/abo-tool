<x-app-layout>
    <x-slot name="header">
        {{ __('Home') }}
    </x-slot>

    <x-box>
        <p>Abonnieren Sie unser Bio-Gemüse, und wir liefern es Ihnen frisch vom Feld bis vor die Haustür. Jede Woche
            neu.</p>
    </x-box>
    <div class="mt-4 grid grid-cols-2 gap-4">
        @foreach($bundles as $bundle)
            @include('parts.bundle', $bundle)
        @endforeach
    </div>
</x-app-layout>
