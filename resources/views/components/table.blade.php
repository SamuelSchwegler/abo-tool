<table class="table-auto">
    @if(isset($header))
        <thead class="font-bold">
            {{$header}}
        </thead>
    @endif
    <tbody class="text-slate-500">
    {{$slot}}
    </tbody>
</table>
