<x-box>
    <x-slot name="title">Kontakt</x-slot>
    <div class="grid gap-4 grid-cols-2 pb-4">
        <div>
            <x-label for="first_name" :value="__('Vorname')" />
            <x-input name="first_name" value="{{$customer->first_name}}"></x-input>
        </div>
        <div>
            <x-label for="last_name" :value="__('Nachname')" />
            <x-input name="last_name" value="{{$customer->last_name}}"></x-input>
        </div>
        <div>
            <x-label for="company_name" :value="__('Firma')" />
            <x-input name="company_name" value="{{$customer->company_name}}"></x-input>
        </div>
        <div>
            <x-label for="phone" :value="__('Telefon')" />
            <x-input name="phone" value="{{$customer->phone}}"></x-input>
        </div>
    </div>
    <div>
        <contact></contact>
    </div>
</x-box>
