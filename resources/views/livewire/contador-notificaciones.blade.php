<div wire:poll.30s="actualizarConteo">
    @if($conteo > 0)
        <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 text-[9px] font-bold text-white bg-red-600 rounded-full shadow-sm">
            {{ $conteo }}
        </span>
    @endif
</div>
