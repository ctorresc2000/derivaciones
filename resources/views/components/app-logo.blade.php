@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="{{ $configuracion ? $configuracion->nombre_institucion : 'Liceo por defecto' }}" {{ $attributes }}>
        {{-- Completamente limpio de tamaños --}}
        <x-slot name="logo" class="flex items-center justify-center bg-transparent">
            <x-app-logo-icon />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Liceo Técnico San Miguel" {{ $attributes }}>
        <x-slot name="logo" class="flex items-center justify-center bg-transparent">
            <x-app-logo-icon />
        </x-slot>
    </flux:brand>
@endif
