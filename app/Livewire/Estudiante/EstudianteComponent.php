<?php

namespace App\Livewire\Estudiante;

use App\Imports\EstudiantesImport;
// ELIMINAMOS: Archivosderivacion y Archivosestudiante
use App\Models\Curso;
use App\Models\Derivarestudiante;
use App\Models\Estudiante;
use App\Models\Motivointervencion;
use App\Models\User;
use App\Models\Viaingreso;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\WithFileUploads;
use App\Traits\HasDocuments;
use Illuminate\Support\Facades\Http;

class EstudianteComponent extends Component
{
    use WithFileUploads;
    use HasDocuments;

    public $name;
    public $email;
    public $social;
    public $curso_id;
    public $observaciones;
    public $estado = 'Activo';
    public $fecha_nacimiento;
    public $domicilio;
    public $telefono;
    public $rut;
    public $apellido;
    public $estudianteId;
    public $abrirModal = false;
    public $derivarModal=false;
    public $cursos=[];
    public $profesionales=[];
    public $estudianteParaDerivarId;
    public ?Estudiante $estudianteSeleccionado = null;
    public $motivo_derivacion;
    public $profesional_derivado_id;
    public $detalle_derivacion;
    public $adjunto_derivacion;
    public $archivo_adjunto=[];
    public $viaingresos;
    public $motivos;
    public $subirExcel;
    public $excelModal=false;
    public $previos_derivacion;
    public $archivo_estudiante;

    public $modalRedes = false;
    public $estudianteSeleccionadoRedes;
    public $red_id;
    public $observacion_red;
    public $mejorandoAcciones = false;
    public $mejorandoDetalle = false;

    public $modalMasivo = false;
    public $detalleMasivo = '';
    public $tipoMasivo = ''; // Aquí guardaremos el ID de la via_ingreso_id o el nombre
    public $selectedIds = []; // IDs de las estudiantes seleccionadas

    public $nuevo_curso_id;
    public $modalPromocion = false;

protected $listeners = ['abrirModalRedes'];


    public function render()
    {
        return view('livewire.estudiante.estudiante-component', [
            // Enviamos la lista de redes a la vista
            'redes' => \App\Models\RedesApoyo::all(),
            'vias' => \App\Models\Viaingreso::all(),
        ]);
    }

    public function mount(){
        $this->fecha_nacimiento = now()->format('Y-m-d');
        $this->cursos=Curso::all();
        $this->profesionales=User::where('estado','Activo')
                                    ->where('tipo_profesional_id','<>',null)
                                    ->orderBy('name','asc')->get();


        $this->viaingresos=Viaingreso::orderBy('via_ingreso','asc')->get();
        $this->motivos=Motivointervencion::orderBy('motivo','asc')->get();
    }

    public function importarExcel()
    {
        $this->validate([
            'subirExcel' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new EstudiantesImport, $this->subirExcel);

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Importación Exitosa!',
                'text' => 'Los estudiantes han sido cargados correctamente.',
                'timer' => 2000
            ]);

            $this->dispatch('refreshTable');

        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error al importar',
                'text' => 'Hubo un problema: ' . $e->getMessage()
            ]);
        }

        $this->reset('subirExcel');
        $this->excelModal = false;
    }

    public function guardar()
    {
        $this->validate([
            'name'=>'required',
            'apellido'=>'required',
            'social'=>'nullable',
            'email'=>'required|email|unique:estudiantes,email',
            'curso_id'=>'required',
            'observaciones'=>'nullable',
            'estado'=>'required|in:Activo,Inactivo',
            'fecha_nacimiento'=>'required|date',
            'domicilio'=>'nullable',
            'telefono'=>'nullable',
            'rut'=>'required|unique:estudiantes,rut',
            'archivo_estudiante' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $nuevoEstudiante = Estudiante::create([
            'nombre' => $this->name,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'social' => $this->social,
            'curso_id' => $this->curso_id,
            'observaciones' => $this->observaciones,
            'estado' => $this->estado,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'domicilio' => $this->domicilio,
            'telefono' => $this->telefono,
            'rut' => $this->rut,
        ]);

        // 👇 LO NUEVO: Guardado polimórfico del estudiante 👇
        if ($this->archivo_estudiante) {
            // Se guarda en una carpeta con el ID del estudiante para mejor orden
            $rutaGuardada = $this->archivo_estudiante->store("documents/estudiantes/{$nuevoEstudiante->id}", 'public');

            $nuevoEstudiante->documents()->create([
                'name'      => $this->archivo_estudiante->getClientOriginalName(),
                'file_path' => $rutaGuardada,
                'mime_type' => $this->archivo_estudiante->getClientMimeType(),
                'size'      => $this->archivo_estudiante->getSize(),
            ]);
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Guardado Exitósamente',
            'timer' => 1500
        ]);

        $this->abrirModal = false;
        $this->dispatch('refreshTable');
        $this->resetValidation();
        $this->reset('name', 'social', 'apellido', 'email', 'curso_id', 'observaciones', 'estado', 'fecha_nacimiento', 'domicilio', 'telefono', 'rut','archivo_estudiante');
    }

    public function actualizar()
    {
        $this->validate([
            'name'=>'required',
            'apellido'=>'required',
            'social'=>'nullable',
            'email'=>'required|email|unique:estudiantes,email,' . $this->estudianteId,
            'curso_id'=>'required',
            'observaciones'=>'nullable',
            'estado'=>'required|in:Activo,Inactivo',
            'fecha_nacimiento'=>'required|date',
            'domicilio'=>'nullable',
            'telefono'=>'nullable',
            'rut'=>'required|unique:estudiantes,rut,' . $this->estudianteId,
            'archivo_estudiante' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $estudiante = Estudiante::find($this->estudianteId);
        $estudiante->nombre = $this->name;
        $estudiante->apellido = $this->apellido;
        $estudiante->email = $this->email;
        $estudiante->social = $this->social;
        $estudiante->curso_id = $this->curso_id;
        $estudiante->observaciones = $this->observaciones;
        $estudiante->estado = $this->estado;
        $estudiante->fecha_nacimiento = $this->fecha_nacimiento;
        $estudiante->domicilio = $this->domicilio;
        $estudiante->telefono = $this->telefono;
        $estudiante->rut = $this->rut;

        $estudiante->save();

        //dd("llega acá");

        if ($this->archivo_estudiante) {
            // 1. Guardar el archivo físicamente
            $rutaGuardada = $this->archivo_estudiante->store("documents/estudiantes/{$estudiante->id}", 'public');

            // 2. Crear el registro polimórfico (asumiendo que usas el modelo Document)
            $estudiante->documents()->create([
                'name'      => $this->archivo_estudiante->getClientOriginalName(),
                'file_path' => $rutaGuardada,
                'mime_type' => $this->archivo_estudiante->getClientMimeType(),
                'size'      => $this->archivo_estudiante->getSize(),
            ]);

            // 3. Limpiar la variable para que no se quede el archivo "pegado"
            $this->reset('archivo_estudiante');
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Actualizado Exitósamente',
            'timer' => 1500
        ]);

        $this->abrirModal = false;
        $this->dispatch('refreshTable');
        $this->resetValidation();
        $this->reset('name', 'social', 'apellido', 'email', 'curso_id', 'observaciones', 'estado', 'fecha_nacimiento', 'domicilio', 'telefono', 'rut','archivo_estudiante');
    }

    public function cerrarModal()
    {
        $this->abrirModal = false;
        $this->resetValidation();
        $this->reset('name', 'social', 'apellido', 'email', 'curso_id', 'observaciones', 'estado', 'fecha_nacimiento', 'domicilio', 'telefono', 'rut','archivo_estudiante');
    }

    #[\Livewire\Attributes\On('editEstudiante')]
     public function editEstudiante($rowId): void
     {
        $estudiante = Estudiante::find($rowId);
        $this->estudianteId = $estudiante->id;
        $this->name = $estudiante->nombre;
        $this->social = $estudiante->social;
        $this->apellido = $estudiante->apellido;
        $this->email = $estudiante->email;
        $this->curso_id = $estudiante->curso_id;
        $this->observaciones = $estudiante->observaciones;
        $this->estado = $estudiante->estado;
        $this->fecha_nacimiento = $estudiante->fecha_nacimiento;
        $this->domicilio = $estudiante->domicilio;
        $this->telefono = $estudiante->telefono;
        $this->rut = $estudiante->rut;

        $this->abrirModal = true;
     }

     #[On('abrirModalDerivacion')]
    public function abrirModalDerivacion($rowId)
    {
        $this->estudianteSeleccionado = Estudiante::with('curso')->find($rowId);
        $this->derivarModal = true;
    }

    public function cerrarModalDerivacion()
    {
        $this->derivarModal = false;
        $this->estudianteParaDerivarId = null;
    }

    public function cerrarModalExcel()
    {
        $this->excelModal = false;
    }


    public function guardarDerivacion()
    {
        $this->validate([
            'motivo_derivacion' => 'required',
            'profesional_derivado_id' => 'required',
            'detalle_derivacion' => 'required',
            'archivo_adjunto.*' => 'nullable|file|max:10240', // Validación para múltiples archivos
        ]);

        try {
            $nuevaDerivacion = Derivarestudiante::create([
                'estudiante_id' => $this->estudianteSeleccionado->id,
                'user_id' => Auth::user()->id,
                'motivo_derivacion' => $this->motivo_derivacion,
                'profesional_derivado_id' => $this->profesional_derivado_id,
                'detalle_derivacion' => $this->detalle_derivacion,
                'fecha_derivacion' => now(),
                'previos_derivacion' => $this->previos_derivacion,
                'estado' => 'Pendiente',
            ]);

            // Asegúrate de que se trate como array siempre
            $coleccionArchivos = is_array($this->archivo_adjunto)
                ? $this->archivo_adjunto
                : ($this->archivo_adjunto ? [$this->archivo_adjunto] : []);

            if (count($coleccionArchivos) > 0) {
                foreach ($coleccionArchivos as $archivo) {
                    // Verificamos que sea un archivo temporal válido antes de procesar
                    if ($archivo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        $rutaGuardada = $archivo->store("documents/derivaciones/{$nuevaDerivacion->id}", 'public');

                        $nuevaDerivacion->documents()->create([
                            'name'      => $archivo->getClientOriginalName(),
                            'file_path' => $rutaGuardada,
                            'mime_type' => $archivo->getClientMimeType(),
                            'size'      => $archivo->getSize(),
                        ]);
                    }
                }
            }

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Éxito',
                'text' => 'Derivación y archivos guardados correctamente',
            ]);

            $this->derivarModal = false;
            $this->reset(['motivo_derivacion', 'profesional_derivado_id', 'detalle_derivacion', 'previos_derivacion', 'archivo_adjunto']);
            $this->dispatch('refreshTable');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    // NUEVA FUNCIÓN MAESTRA
    public function procesarFormulario()
    {
        if ($this->estudianteId) {
            $this->actualizar();
        } else {
            $this->guardar();
        }
    }

    public function abrirModalRedes($estudianteId)
    {
        $this->estudianteSeleccionadoRedes = Estudiante::with('redes')->find($estudianteId);
        $this->modalRedes = true;
    }

    public function asignarRed()
    {
        $this->validate([
            'red_id' => 'required',
            'observacion_red' => 'required',
        ]);

        $this->estudianteSeleccionadoRedes->redes()->attach($this->red_id, [
            'observacion' => $this->observacion_red,
            'activo' => true
        ]);

        $this->reset(['red_id', 'observacion_red']);
        $this->estudianteSeleccionadoRedes->load('redes'); // Refrescar lista
        $this->modalRedes = false;
        $this->dispatch('swal', ['icon' => 'success', 'title' => 'Red asignada','timer'=>1500]);
    }

    public function desvincularRed($redId)
    {
        if ($this->estudianteSeleccionadoRedes) {
            $this->estudianteSeleccionadoRedes->redes()->detach($redId);
            $this->estudianteSeleccionadoRedes->load('redes'); // Refresca la lista en el modal
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Red eliminada',
                'text' => 'Se ha quitado el vínculo con la institución.',
                'timer' => 1500
            ]);
        }
        $this->modalRedes = false;
    }

    public function mejorarTextoIAdetalle()
    {
        if (empty($this->detalle_derivacion)) return;
        $this->mejorandoDetalle = true;

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
                        'content' => 'Mejora la redacción y ortografía de este texto, manteniéndolo formal: ' . $this->detalle_derivacion
                    ]
                ],
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $this->detalle_derivacion = $response->json()['choices'][0]['message']['content'];
                $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
            } else {
                // Si Groq devuelve error, aquí veremos qué modelo sugiere usar
                $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido';
                throw new \Exception($errorDetail);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Fallo la IA', 'text' => $e->getMessage()]);
        }

        $this->mejorandoDetalle = false;
    }

    public function mejorarTextoIAacciones()
    {
        if (empty($this->previos_derivacion)) return;
        $this->mejorandoAcciones = true;

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
                        'content' => 'Mejora la redacción y ortografía de este texto, manteniéndolo formal: ' . $this->previos_derivacion
                    ]
                ],
                'temperature' => 0.5,
            ]);

            if ($response->successful()) {
                $this->previos_derivacion = $response->json()['choices'][0]['message']['content'];
                $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
            } else {
                // Si Groq devuelve error, aquí veremos qué modelo sugiere usar
                $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido';
                throw new \Exception($errorDetail);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Fallo la IA', 'text' => $e->getMessage()]);
        }

        $this->mejorandoAcciones = false;
    }


    //Acciones Masivas
    #[On('idSeleccion')]
    public function idSeleccion($data)
    {
        // Obtenemos los IDs seleccionados de PowerGrid
        $this->selectedIds = $this->getCheckboxState();

        if (empty($this->selectedIds)) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No hay selección',
                'text' => 'Selecciona al menos una estudiante en la tabla.'
            ]);
            return;
        }

        $this->modalMasivo = true;
    }

//    #[On('prepararMasivo.estudianteTable')]
//     public function prepararMasivo()
//     {
//         // Usamos JS para obtener los IDs que PowerGrid tiene guardados en el navegador
//         // y se los devolvemos a otra función de este mismo componente
//         $this->js('
//             const ids = window.pgBulkActions.get("estudianteTable");
//             if (ids.length > 0) {
//                 $wire.asignarIdsYAbrirModal(ids);
//             } else {
//                 alert("Por favor, selecciona al menos una estudiante");
//             }
//         ');
//     }

    // Esta función recibirá los IDs desde el JS de arriba
    public function asignarIdsYAbrirModal($ids)
    {
        $this->selectedIds = $ids;
        $this->modalMasivo = true;
    }



    public function guardarIntervencionMasiva()
    {
        $this->validate([
            'detalleMasivo' => 'required|min:5',
            'tipoMasivo' => 'required', // via_ingreso_id
        ]);

        try {
            $batchId = (string) \Illuminate\Support\Str::uuid();

            foreach ($this->selectedIds as $estudianteId) {
                \App\Models\Intervencion::create([
                    'estudiante_id'  => $estudianteId,
                    'usuario_id'     => auth()->id(),
                    'via_ingreso_id' => $this->tipoMasivo, // Convivencia o Psicosocial
                    'descripcion'    => $this->detalleMasivo,
                    'fecha'          => now()->format('Y-m-d'),
                    'estado'         => 'Concluida', // Al ser masiva, suele ser una actividad terminada
                    'batch_id'       => $batchId,
                ]);
            }

            $this->modalMasivo = false;


            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Proceso Exitoso',
                'text' => 'Se registraron ' . count($this->selectedIds) . ' intervenciones.'
            ]);

            $this->reset(['detalleMasivo', 'selectedIds','tipoMasivo']);
            //$this->clearCheckBox(); // Limpiar la tabla PowerGrid
            //$this->dispatch('pg:clearAllSelections-estudianteTable');
            // 2. Limpiamos la memoria de JS de PowerGrid
            $this->js('window.pgBulkActions.clearAll("estudianteTable")');

            // 3. Forzamos a la tabla a desmarcar visualmente los inputs (Evento nativo)
            $this->dispatch('pg:clearAllSelections-estudianteTable');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    #[On('checkboxChanged-estudianteTable')]
    public function actualizarSeleccion($ids)
    {
        // Forzamos a que siempre sea un array, si llega null o vacío, será []
        $this->selectedIds = is_array($ids) ? $ids : [];
    }

    #[On('prepararMasivo.estudianteTable')]
    public function prepararMasivo()
    {
        $this->js('
            const ids = window.pgBulkActions.get("estudianteTable");
            if (ids.length > 0) {
                $wire.set("selectedIds", ids);
                $wire.set("modalMasivo", true);
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Atención",
                    text: "Debes seleccionar al menos una estudiante para realizar la Intervención Masiva",
                    confirmButtonColor: "#4f46e5", // Color índigo para combinar con tu botón
                });
            }
        ');
    }

// Listener para Cambio de Curso (Promoción)
    #[On('abrirModalPromocion.estudianteTable')]
    public function abrirModalPromocion()
    {
        $this->js('
            const ids = window.pgBulkActions.get("estudianteTable");
            if (ids.length > 0) {
                $wire.set("selectedIds", ids); // Sincroniza los IDs manualmente
                $wire.set("modalPromocion", true); // Abre el modal
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Atención",
                    text: "Debes seleccionar al menos una estudiante para realizar el cambio de curso",
                    confirmButtonColor: "#4f46e5", // Color índigo para combinar con tu botón
                });
            }
        ');
    }

    public function cambiarCursoMasivo()
    {
        $this->validate([
            'nuevo_curso_id' => 'required'
        ]);

        try {
            if (empty($this->selectedIds)) {
                $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => 'No hay estudiantes seleccionadas.']);
                return;
            }

            // Actualización masiva en la base de datos
            Estudiante::whereIn('id', $this->selectedIds)->update([
                'curso_id' => $this->nuevo_curso_id
            ]);

            $this->modalPromocion = false;
            $this->reset(['nuevo_curso_id', 'selectedIds']);

            // Limpiamos los checks de la tabla
            $this->js('window.pgBulkActions.clearAll("estudianteTable")');
            $this->dispatch('pg:clearAllSelections-estudianteTable');
            $this->dispatch('refreshTable'); // Para que la tabla muestre los nuevos cursos

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Cambio Exitoso!',
                'text' => 'Los cursos han sido actualizados correctamente.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

}
