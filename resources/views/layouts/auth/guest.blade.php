<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-7xl flex-col gap-2">

                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <div class="flex items-center justify-center mb-2">
                        <x-app-logo-icon class="h-36 w-auto object-contain fill-current text-black dark:text-white" />
                    </div>

                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('swal', (event) => {
                    // Livewire 3 envía los datos en un array, por lo que tomamos el índice 0
                    const data = event[0];

                    Swal.fire({
                        icon: data.icon,
                        title: data.title,
                        text: data.text,
                        timer: data.timer,
                        showConfirmButton: false // Oculta el botón de "OK" para que se cierre solo
                    });
                });
            });
        </script>
    </body>
</html>
