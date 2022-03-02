<div class="p-6 bg-white bg-white overflow-hidden shadow-sm sm:rounded-lg border-b border-gray-200">
    @if(isset($title))
        <h3 class="text-lg font-semibold">{{$title}}</h3>
    @endif
    {{$slot}}
</div>
