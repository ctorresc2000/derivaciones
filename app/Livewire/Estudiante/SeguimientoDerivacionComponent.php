<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\AccionDerivacion;
use Illuminate\Support\Facades\Http;

class SeguimientoDerivacionComponent extends Component
{
    public $abrirModal = false;

    public $derivacion_id;
    public $descripcion_accion;
    public $historialAcciones = [];
    public $mejorandoSeguimiento = false;

    // Atrapamos el evento desde estudiantederivadoTable
    #[\Livewire\Attributes\On('abrirModalSeguimiento')]
    public function cargarDatosModal($rowId)
    {
        $this->derivacion_id = is_array($rowId) ? $rowId['rowId'] : $rowId;
        $this->descripcion_accion = '';

        $this->cargarHistorial();
        $this->abrirModal = true;
    }

    public function cargarHistorial()
    {
        $this->historialAcciones = AccionDerivacion::with('usuario')
            ->where('derivarestudiante_id', $this->derivacion_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
    }

    public function guardarAccion()
    {
        $this->validate([
            'descripcion_accion' => 'required|min:5',
        ]);

        AccionDerivacion::create([
            'derivarestudiante_id' => $this->derivacion_id,
            'user_id' => auth()->id(),
            'fecha' => now(),
            'descripcion' => $this->descripcion_accion,
        ]);

        // Limpiamos y recargamos la lista visual
        $this->descripcion_accion = '';
        $this->cargarHistorial();

        // Opcional: Refrescar la tabla de fondo
        $this->dispatch('pg:eventRefresh-estudiantederivadoTable');
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Excelente',
            'text' => 'Acción guardada en el historial',
            'timer' => 1500
        ]);
    }

    public function mejorarTextoIAseguimiento()
    {
        if (empty($this->descripcion_accion)) return;
        $this->mejorandoSeguimiento = true;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => 'llama-3.3-70b-versatile', // Modelo actualizado y vigente
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Eres un corrector de estilo profesional para reportes escolares.'
                    ],
                    [
                        'role' => 'user',
                        'content' => 'Mejora la redacción y ortografía de este texto, manteniéndolo formal: ' . $this->descripcion_accion
                    ]
                ],
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $this->descripcion_accion = $response->json()['choices'][0]['message']['content'];
                //$this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
            } else {
                // Si Groq devuelve error, aquí veremos qué modelo sugiere usar
                $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido';
                throw new \Exception($errorDetail);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Fallo la IA', 'text' => $e->getMessage()]);
        }

        $this->mejorandoSeguimiento = false;
    }

    public function render()
    {
        return view('livewire.estudiante.seguimiento-derivacion-component');
    }
}
