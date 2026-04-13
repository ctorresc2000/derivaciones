<div>
    <h1 class="text-3xl mt-4 mb-4">Cardex</h1>
    <hr class="mb-6">

    @livewire('tablas.entrevistas-table')

    <script>
        document.addEventListener('livewire:init', () => {
        Livewire.on('imprimirPDF', (event) => {
            // En Livewire v3 / PowerGrid v3, los parámetros vienen así:
            const data = Array.isArray(event) ? event[0] : event;
            const id = data.id;

            if (id) {
                // Abrir la ruta que definiste en web.php
                window.open('/entrevista/' + id + '/pdf', '_blank');
            } else {
                console.error("No se recibió el ID de la entrevista");
            }
        });
        });
    </script>

</div>
