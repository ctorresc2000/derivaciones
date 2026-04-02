<div class="w-full" wire:ignore>

    {!! $chart->container() !!}

    <script src="{{ $chart->cdn() }}"></script>
    {{ $chart->script() }}

</div>
