<?php

namespace App\Livewire\Apoderado;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Estudiante;
use App\Models\Apoderado;
use Illuminate\Support\Facades\Storage;

class PortalApoderadoComponent extends Component
{
    use WithFileUploads;

    // Paso 1: Login Estudiante
    public $rutEstudiante = '';
    public $estudiante = null;
    public $paso = 1; // Controla qué pantalla se ve

    // Paso 2: Formulario Apoderados
    public $apoderados = [];

    public function ingresar()
    {
        $this->validate([
            'rutEstudiante' => 'required|string'
        ]);

        $this->estudiante = Estudiante::with('apoderados')->where('rut', $this->rutEstudiante)->first();

        if (!$this->estudiante) {
            $this->addError('rutEstudiante', 'No se encontró ningún estudiante con este RUT.');
            return;
        }

        if ($this->estudiante->matricula=="NO") {
            $this->addError('rutEstudiante', 'No está autorozado para agregar o quitar apoderados, solicite autorización.');
            $this->reset('rutEstudiante');
            return;
        }

        // Si el estudiante ya tiene apoderados registrados, los cargamos
        if ($this->estudiante->apoderados->count() > 0) {
            foreach ($this->estudiante->apoderados as $apo) {
                $this->apoderados[] = [
                    'id'        => $apo->id, // Guardamos el ID para saber que estamos editando
                    'rut'       => $apo->rut,
                    'apoderado' => $apo->apoderado,
                    'telefono'  => $apo->telefono,
                    'correo'    => $apo->correo,
                    'direccion' => $apo->direccion,
                    'carnet'    => $apo->carnet, // Nueva foto si se desea cambiar
                ];
            }
        } else {
            // Si no tiene, agregamos un formulario en blanco
            $this->agregarApoderado();
        }

        $this->paso = 2; // Avanzamos al formulario
    }

    public function agregarApoderado()
    {
        $this->apoderados[] = [
            'id'        => null,
            'rut'       => '',
            'apoderado' => '',
            'telefono'  => '',
            'correo'    => '',
            'direccion' => '',
            'carnet'    => null
        ];
    }

    public function removerApoderado($index)
    {
        $idApoderado = $this->apoderados[$index]['id'] ?? null;

        if ($idApoderado) {
            // 1. En lugar de borrar el registro, SOLO CORTAMOS LA RELACIÓN en la tabla pivote
            $this->estudiante->apoderados()->detach($idApoderado);

            // Nota: Ya no borramos el archivo físico (carnet) ni hacemos ->delete() al modelo,
            // porque ese apoderado podría estar vinculado a otro hermano/estudiante.

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Desvinculado',
                'text' => 'El apoderado fue quitado de la ficha de este estudiante.',
                'timer' => 1500
            ]);
        }

        // 2. Lo quitamos visualmente del formulario en la pantalla
        unset($this->apoderados[$index]);
        $this->apoderados = array_values($this->apoderados);
    }

    // Esta función se ejecuta cuando el usuario escribe el RUT del apoderado
    public function buscarApoderadoBD($index)
    {
        $rut = $this->apoderados[$index]['rut'];

        if (!empty($rut)) {
            // Buscamos si existe ALGÚN apoderado con ese RUT en toda la base de datos
            $apoderadoExistente = Apoderado::where('rut', $rut)->latest()->first();

            if ($apoderadoExistente) {
                // Autocompletamos los datos (pero el ID lo dejamos null a menos que ya sea de este estudiante, para evitar conflictos de 1 a N)
                $this->apoderados[$index]['apoderado'] = $apoderadoExistente->apoderado;
                $this->apoderados[$index]['telefono']  = $apoderadoExistente->telefono;
                $this->apoderados[$index]['correo']    = $apoderadoExistente->correo;
                $this->apoderados[$index]['direccion'] = $apoderadoExistente->direccion;
                $this->apoderados[$index]['carnet']    = $apoderadoExistente->carnet;

                $this->dispatch('alerta', ['icon' => 'info', 'title' => 'Encontrado', 'text' => 'Datos autocompletados.']);
            }
        }
    }

    public function guardar()
    {
        // 1. Definimos las reglas base (los campos de texto)
        $rules = [
            'apoderados.*.rut'       => 'required|string',
            'apoderados.*.apoderado' => 'required|string',
            'apoderados.*.telefono'  => 'required|string',
            'apoderados.*.correo'    => 'required|email',
            'apoderados.*.direccion' => 'required|string',
        ];

        // 2. Definimos los mensajes base
        $messages = [
            'apoderados.*.rut.required'       => 'El RUT es obligatorio.',
            'apoderados.*.apoderado.required' => 'El nombre completo es obligatorio.',
            'apoderados.*.telefono.required'  => 'El teléfono es obligatorio.',
            'apoderados.*.correo.required'    => 'El correo es obligatorio.',
            'apoderados.*.correo.email'       => 'El correo no es válido.',
            'apoderados.*.direccion.required' => 'La dirección es obligatoria.',
        ];

        // 3. VALIDACIÓN DINÁMICA DEL CARNET
        foreach ($this->apoderados as $datos) {

            // 1. Preparamos los datos base
            $datosGuardar = [
                'rut'           => $datos['rut'],
                'apoderado'     => $datos['apoderado'],
                'telefono'      => $datos['telefono'],
                'correo'        => $datos['correo'],
                'direccion'     => $datos['direccion'],
                'estado'        => 'Activo'
            ];

            if (isset($datos['carnet']) && is_string($datos['carnet'])) {
                $datosGuardar['carnet'] = $datos['carnet'];
            }

            // 2. LA SOLUCIÓN: Usamos el ID para actualizar a la persona correcta
            if (!empty($datos['id'])) {
                // Es un apoderado que ya existía en la BD, lo actualizamos directamente
                $apoderado = Apoderado::find($datos['id']);
                if ($apoderado) {
                    $apoderado->update($datosGuardar);
                }
            } else {
                // Es un apoderado "nuevo" (agregado con el botón "+"), buscamos por RUT
                $apoderado = Apoderado::updateOrCreate(
                    ['rut' => $datos['rut']],
                    $datosGuardar
                );
            }

            // 3. LA MAGIA DE LA TABLA PIVOTE
            if (isset($apoderado)) {
                $this->estudiante->apoderados()->syncWithoutDetaching([$apoderado->id]);

                // 4. Lógica de Imágenes
                if (isset($datos['carnet']) && !is_string($datos['carnet'])) {
                    if (!empty($apoderado->carnet) && Storage::disk('public')->exists($apoderado->carnet)) {
                        Storage::disk('public')->delete($apoderado->carnet);
                    }
                    $rutaGuardada = $datos['carnet']->store("apoderados", 'public');
                    $apoderado->update([
                        'carnet' => $rutaGuardada
                    ]);
                }
            }
        }
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Apoderado Guardado Exitósamente',
            'timer' => 3500
        ]);

        $this->reset('rutEstudiante','estudiante','apoderados');

        $this->paso = 1;
    }

    public function render()
    {
        return view('livewire.apoderado.portal-apoderado-component')->layout('layouts.auth.guest');
    }
}
