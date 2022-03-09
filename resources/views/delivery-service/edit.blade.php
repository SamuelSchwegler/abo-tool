<x-app-layout>
    <x-slot name="header">
        {{ __('Lieferzonen bearbeiten') }}
    </x-slot>
    <div class="grid grid-cols-5 gap-8">
        <div>
            <x-box>
                <ul>
                    @foreach($services as $s)
                        <li class="btn mb-2 @if($s->id === $service->id) bg-violet @endif">
                            <a class="block w-full" href="{{route('delivery-service.edit', $s)}}">{{$s->name}}</a>
                        </li>
                    @endforeach
                </ul>
            </x-box>
        </div>
        <div class="col-span-2">
            <postcode-management :service="{{json_encode($serviceResource)}}"></postcode-management>
        </div>
        <div class="col-span-2">
            <delivery-service-edit :service="{{json_encode($serviceResource)}}"></delivery-service-edit>
        </div>
    </div>
</x-app-layout>
