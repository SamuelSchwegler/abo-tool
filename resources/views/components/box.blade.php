<div class="box">
    @if(isset($title))
        <h3 class="title text-lg font-semibold">{{$title}}</h3>
    @endif
    {{$slot}}
</div>
