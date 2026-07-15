<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Entrevista;

class CamaraMovilComponent extends Component
{
    use WithFileUploads;

    public $entrevista;
    public $fotos_movil = [];

    public function mount($id)
    {
        // Buscamos la entrevista. Si no existe, dará error 404.
        $this->entrevista = Entrevista::with('estudiante')->findOrFail($id);
    }

    public function subirFotos()
    {
        $this->validate([
            'fotos_movil.*' => 'image|max:10240', // Máximo 10MB, forzamos a que sean imágenes
        ]);

        if (!empty($this->fotos_movil)) {
            foreach ($this->fotos_movil as $foto) {
                // Guardamos en la misma carpeta que las de escritorio
                $rutaGuardada = $foto->store("documents/entrevistas/{$this->entrevista->id}", 'public');

                $this->entrevista->documents()->create([
                    'name'      => 'Foto_Movil_' . now()->format('Ymd_His') . '.' . $foto->getClientOriginalExtension(),
                    'file_path' => $rutaGuardada,
                    'mime_type' => $foto->getClientMimeType(),
                    'size'      => $foto->getSize(),
                ]);
            }

            $this->reset('fotos_movil');

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Excelente!',
                'text' => 'Las fotos se subieron correctamente a la entrevista.',
            ]);
        }
    }

    public function render()
    {
        // Retornamos la vista. Puedes usar un layout vacío si quieres que parezca una App nativa
        return view('livewire.estudiante.camara-movil-component');
    }
}
