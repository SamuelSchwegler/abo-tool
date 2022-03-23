<x-box>
    <x-slot name="title">Lieferservice</x-slot>
    <ul>
        @foreach($services as $s)
            <li class="btn mb-2 @if(isset($service) && $s->id === $service->id) bg-violet @endif">
                <a class="block w-full" href="{{route('delivery-service.edit', $s)}}">{{$s->name}}</a>
            </li>
        @endforeach
        <li class="btn @if(!isset($service)) bg-violet @endif">
            <a class="block w-full" href="{{route('delivery-service.create')}}">Hinzufügen</a>
        </li>
    </ul>
</x-box>
