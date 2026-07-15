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
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificacionDerivacion;
use App\Models\Falta;

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

    public $fechaintervencionMasiva;

    public $modalRedes = false;
    public $estudianteSeleccionadoRedes;
    public $red_id;
    public $observacion_red;
    public $mejorandoAcciones = false;
    public $mejorandoDetalle = false;

    public $anioParaPromocion;

    public $modalPromocionCurso=false;

    public $modalMasivo = false;
    public $detalleMasivo = '';
    public $tipoMasivo = ''; // Aquí guardaremos el ID de la via_ingreso_id o el nombre
    public $selectedIds = []; // IDs de las estudiantes seleccionadas

   // public $abrirModalPromocionCurso;

    public $nuevo_curso_id;
    public $modalPromocion = false;

    public $curso_origen = '';
    public $curso_destino = '';
    public $condicion = '';
    public $estudiantes_seleccionadas = [];
    public $seleccionarTodo = false;

    public $fechaDerivacion;

    public $modalApoderados=false;

    public $profesionales_derivados_ids = [];

    protected $listeners = ['abrirModalRedes','abrirModalApoderados'];


    #[\Livewire\Attributes\On('autorizarEdicionMasiva.estudianteTable')]
    public function autorizarEdicionMasiva()
    {
        $this->js('
            const ids = window.pgBulkActions.get("estudianteTable");
            if (ids.length > 0) {
                $wire.set("selectedIds", ids);
                $wire.procesarAutorizacionMasiva();
            } else {
                Swal.fire({
                    icon: "warning",
                    title: "Atención",
                    text: "Debes seleccionar al menos un estudiante en la tabla.",
                    confirmButtonColor: "#f59e0b",
                });
            }
        ');
    }

    public function procesarAutorizacionMasiva()
    {
        try {
            if (empty($this->selectedIds)) {
                return;
            }

            // Buscamos a todos los estudiantes seleccionados
            $estudiantes = Estudiante::whereIn('id', $this->selectedIds)->get();

            foreach ($estudiantes as $estudiante) {
                // Alternamos el valor: Si es SI lo cambia a NO. Si es NO (o está vacío), lo cambia a SI.
                $nuevoValor = ($estudiante->matricula === 'SI') ? 'NO' : 'SI';

                $estudiante->update([
                    'matricula' => $nuevoValor
                ]);
            }

            // Limpiamos las variables y desmarcamos los checkbox de la tabla
            $this->reset('selectedIds');
            $this->dispatch('limpiarSelecciones');
            $this->dispatch('refreshTable');

            // Mostramos mensaje de éxito
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Los permisos de edición fueron alternados correctamente.'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function updatedSeleccionarTodo($value)
    {
        if ($value) {
            // Si se marca, llenamos el arreglo con todos los IDs de la vista actual
            $this->estudiantes_seleccionadas = $this->estudiantes_pendientes->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            // Si se desmarca, vaciamos el arreglo
            $this->estudiantes_seleccionadas = [];
        }
    }

    public function updatedEstudiantesSeleccionadas()
    {
        // Verificamos si la cantidad de seleccionados es igual al total disponible
        $totalPendientes = $this->estudiantes_pendientes->count();

        if ($totalPendientes > 0 && count($this->estudiantes_seleccionadas) === $totalPendientes) {
            $this->seleccionarTodo = true; // Marca el checkbox principal automáticamente
        } else {
            $this->seleccionarTodo = false; // Lo desmarca si falta alguno
        }
    }

    #[\Livewire\Attributes\Computed]
    public function estudiantes_pendientes()
    {
        if (!$this->curso_origen) {
            return collect(); // Devuelve una colección vacía si no hay curso seleccionado
        }

        return Estudiante::query()
            ->where('curso_id', $this->curso_origen)
            ->where('estado', 'Activo')
            ->orderBy('apellido', 'asc')
            ->get();
    }

    public function promocionarSeleccionadas()
    {
        // 1. Validamos que el formulario esté completo
        $this->validate([
            'curso_origen' => 'required',
            'curso_destino' => 'required',
            'condicion' => 'required',
            'estudiantes_seleccionadas' => 'required|array|min:1',
        ], [
            'estudiantes_seleccionadas.min' => 'Debes seleccionar al menos una estudiante.'
        ]);

        // 2. EL CANDADO DE SEGURIDAD ANTI-MEZCLA
        // Solo verificamos si las estamos moviendo a un curso diferente (Las "Reprobadas" se quedan en el mismo)
        // if ($this->curso_origen !== $this->curso_destino) {

        //     // Contamos si hay estudiantes activas en el curso de destino
        //     $estudiantesEnDestino = Estudiante::where('curso_id', $this->curso_destino)
        //         ->where('estado', 'Activo')
        //         ->count();

        //     if ($estudiantesEnDestino > 0) {
        //         // Si hay estudiantes, detenemos el proceso y mostramos una alerta
        //         $this->dispatch('swal', [
        //             'icon' => 'error',
        //             'title' => 'Acción Bloqueada',
        //             'text' => "No puedes promover estudiantes a este curso. Actualmente hay {$estudiantesEnDestino} estudiantes matriculadas en el curso de destino. Debes promover o egresar a esas estudiantes primero."
        //         ]);
        //         return; // Cortamos la ejecución aquí
        //     }
        // }

        try {
            // 3. Aquí guardarías en tu tabla de Historial
            // (Asumo que tienes un modelo como HistorialEstudiante)
            /* foreach ($this->estudiantes_seleccionadas as $id) {
                \App\Models\HistorialEstudiante::create([
                    'estudiante_id' => $id,
                    'curso_id' => $this->curso_origen, // El curso en el que estaban
                    'condicion' => $this->condicion,
                    'anio' => $this->anioParaPromocion - 1
                ]);
            }
            */

            // 4. Actualizamos el curso masivamente en la base de datos
            Estudiante::whereIn('id', $this->estudiantes_seleccionadas)->update([
                'curso_id' => $this->curso_destino,
                'anio'=>$this->anioParaPromocion + 1
            ]);

            // 5. Mostramos éxito y limpiamos los checkboxes
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Promoción Exitosa',
                'text' => 'Las estudiantes seleccionadas han sido movidas al nuevo curso.'
            ]);

            // Limpiamos la selección para que no queden marcados en el próximo curso
            $this->reset('estudiantes_seleccionadas');

            // Recargamos la tabla principal de estudiantes por detrás
            $this->dispatch('refreshTable');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
        $this->modalPromocionCurso = false;
    }

    public function cerrarModalPromocionCurso()
    {
        $this->modalPromocionCurso = false;
        $this->reset(['curso_origen', 'curso_destino', 'condicion', 'estudiantes_seleccionadas']);
    }

    public function render()
    {
        return view('livewire.estudiante.estudiante-component', [
            // Enviamos la lista de redes a la vista
            'redes' => \App\Models\RedesApoyo::all(),
            'vias' => \App\Models\Viaingreso::all(),
        ]);
    }

    public function abrirModalPromocionCurso()
    {
        $this->modalPromocionCurso=true;
    }

    public function mount()
    {
        $this->anioParaPromocion=session('anio_activo', date('Y'));;
        $this->fecha_nacimiento = now()->format('Y-m-d');
        $this->cursos=Curso::all();
        $this->fechaintervencionMasiva = now()->format('Y-m-d');
        $this->profesionales=User::where('estado','Activo')
                                    ->where('tipo_profesional_id','<>',null)
                                    ->orderBy('name','asc')->get();


        $this->viaingresos=Viaingreso::orderBy('via_ingreso','asc')->get();
        //$this->motivos=Motivointervencion::orderBy('motivo','asc')->get();

        // 1. Traemos los motivos y los transformamos a un formato estándar
        $motivos = Motivointervencion::orderBy('motivo', 'asc')->get()->map(function ($item) {
            return (object) [
                'valor' => 'motivo_' . $item->id, // Prefijo para evitar choque de IDs
                'texto' => $item->motivo,
                'grupo' => 'Motivos de Intervención'
            ];
        });

        // 2. Traemos las faltas y las transformamos al mismo formato
        $faltas = Falta::orderBy('falta', 'asc')->get()->map(function ($item) {
            return (object) [
                'valor' => 'falta_' . $item->id, // Prefijo para evitar choque de IDs
                'texto' => $item->falta,
                'grupo' => 'Faltas Disciplinarias'
            ];
        });

        // 3. Unimos ambas colecciones en tu variable original
        $this->motivos = $motivos->merge($faltas);

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
            'anio' => now()->format('Y'),
            'matricula'=>"NO"
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

        if ($this->archivo_estudiante && !is_string($this->archivo_estudiante)) {

            // 1. Buscar y borrar archivo viejo físicamente y de la base de datos
            $documentoViejo = $estudiante->documents()->first();
            if ($documentoViejo) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($documentoViejo->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($documentoViejo->file_path);
                }
                $documentoViejo->delete();
            }

            // 2. Guardar el nuevo sin subcarpetas problemáticas
            $rutaGuardada = $this->archivo_estudiante->store("estudiantes", 'public');

            // 3. Crear el registro polimórfico
            $estudiante->documents()->create([
                'name'      => $this->archivo_estudiante->getClientOriginalName(),
                'file_path' => $rutaGuardada,
                'mime_type' => $this->archivo_estudiante->getClientMimeType(),
                'size'      => $this->archivo_estudiante->getSize(),
            ]);

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
        $this->fechaDerivacion=now()->format('Y-m-d');
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
        // 1. VALIDACIÓN ACTUALIZADA
        $this->validate([
            'motivo_derivacion' => 'required',
            'profesionales_derivados_ids' => 'required|array|min:1',
            'detalle_derivacion' => 'required',
            'archivo_adjunto.*' => 'nullable|file|max:10240',
        ], [
            'profesionales_derivados_ids.required' => 'Debes seleccionar al menos un profesional al cual derivar.',
        ]);

        try {
            // Traducimos el prefijo al texto real
            $textoMotivo = $this->motivo_derivacion;
            foreach ($this->motivos as $item) {
                if ($item->valor === $this->motivo_derivacion) {
                    $textoMotivo = $item->texto;
                    break;
                }
            }

            // Arreglo temporal para ir guardando las derivaciones creadas
            $derivacionesCreadas = [];

            // 2. CREAMOS UNA DERIVACIÓN INDEPENDIENTE POR CADA PROFESIONAL
            foreach ($this->profesionales_derivados_ids as $profesional_id) {
                $nuevaDerivacion = Derivarestudiante::create([
                    'estudiante_id' => $this->estudianteSeleccionado->id,
                    'user_id' => Auth::user()->id,
                    'motivo_derivacion' => $textoMotivo,
                    'profesional_derivado_id' => $profesional_id,
                    'detalle_derivacion' => $this->detalle_derivacion,
                    'fecha_derivacion' => $this->fechaDerivacion ?? now(),
                    'previos_derivacion' => $this->previos_derivacion,
                    'estado' => 'Pendiente',
                ]);

                $derivacionesCreadas[] = $nuevaDerivacion;
            }

            // 3. PROCESAMOS LOS ARCHIVOS Y LOS "CLONAMOS" A CADA DERIVACIÓN
            $coleccionArchivos = is_array($this->archivo_adjunto)
                ? $this->archivo_adjunto
                : ($this->archivo_adjunto ? [$this->archivo_adjunto] : []);

            if (count($coleccionArchivos) > 0) {
                foreach ($coleccionArchivos as $archivo) {
                    if ($archivo instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {

                        $contenido = file_get_contents($archivo->getRealPath());
                        $nombreOriginal = $archivo->getClientOriginalName();
                        $mime = $archivo->getClientMimeType();
                        $size = $archivo->getSize();

                        foreach ($derivacionesCreadas as $derivacion) {
                            $nombreUnico = uniqid() . '_' . $nombreOriginal;
                            $rutaGuardada = "documents/derivaciones/{$derivacion->id}/{$nombreUnico}";

                            \Illuminate\Support\Facades\Storage::disk('public')->put($rutaGuardada, $contenido);

                            $derivacion->documents()->create([
                                'name'      => $nombreOriginal,
                                'file_path' => $rutaGuardada,
                                'mime_type' => $mime,
                                'size'      => $size,
                            ]);
                        }
                    }
                }
            }

            // 4. ENVÍO DE NOTIFICACIONES POR CORREO
            $profesionalesDestino = \App\Models\User::whereIn('id', $this->profesionales_derivados_ids)->get();

            foreach ($profesionalesDestino as $profesional) {
                if (!empty($profesional->email)) {
                    Mail::to($profesional->email)->send(new NotificacionDerivacion($this->estudianteSeleccionado));
                }
            }

            // 5. FINALIZAMOS Y LIMPIAMOS
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Éxito',
                'text' => 'Derivaciones guardadas y correos enviados correctamente.',
            ]);

            $this->derivarModal = false;
            $this->reset(['motivo_derivacion', 'profesionales_derivados_ids', 'detalle_derivacion', 'previos_derivacion', 'archivo_adjunto']);
            $this->dispatch('refreshTable');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error al guardar', 'text' => $e->getMessage()]);
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

    public function abrirModalApoderados($estudianteId)
    {
        $this->estudianteSeleccionadoRedes = Estudiante::with('apoderados')->find($estudianteId);
        //dd($this->estudianteSeleccionadoRedes);
        $this->modalApoderados = true;
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

    // public function mejorarTextoIAdetalle()
    // {
    //     if (empty($this->detalle_derivacion)) return;
    //     $this->mejorandoDetalle = true;

    //     try {
    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
    //             'Content-Type' => 'application/json',
    //         ])->post('https://api.groq.com/openai/v1/chat/completions', [
    //             'model' => 'llama-3.3-70b-versatile', // Modelo actualizado y vigente
    //             'messages' => [
    //                 [
    //                     'role' => 'system',
    //                     'content' => 'Eres un corrector de estilo profesional para reportes escolares.'
    //                 ],
    //                 [
    //                     'role' => 'user',
    //                     'content' => 'Mejora la redacción y ortografía de este texto, manteniéndolo formal: ' . $this->detalle_derivacion
    //                 ]
    //             ],
    //             'temperature' => 0.5,
    //         ]);

    //         if ($response->successful()) {
    //             $this->detalle_derivacion = $response->json()['choices'][0]['message']['content'];
    //             $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
    //         } else {
    //             // Si Groq devuelve error, aquí veremos qué modelo sugiere usar
    //             $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido';
    //             throw new \Exception($errorDetail);
    //         }
    //     } catch (\Exception $e) {
    //         $this->dispatch('swal', ['icon' => 'error', 'title' => 'Fallo la IA', 'text' => $e->getMessage()]);
    //     }

    //     $this->mejorandoDetalle = false;
    // }

    public function mejorarTextoIAdetalle()
    {
        if (empty($this->detalle_derivacion)) return;
        $this->mejorandoDetalle = true;

        try {
            // 1. Preparamos el texto uniendo las instrucciones con tu variable
            $prompt = "Eres un corrector de estilo profesional para reportes escolares.\n\n" .
                      "Mejora la redacción y ortografía de este texto, manteniéndolo formal: " . $this->detalle_derivacion;

            // 2. Hacemos la petición a la API de Gemini (modelo 2.5 flash)
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.5, // Mantenemos tu configuración original
                ]
            ]);

            // 3. Verificamos la respuesta
            if ($response->successful()) {
                // Extracción de texto usando la estructura JSON específica de Gemini
                $data = $response->json();

                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $this->detalle_derivacion = $data['candidates'][0]['content']['parts'][0]['text'];
                    $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
                } else {
                    throw new \Exception('Gemini no devolvió ningún texto válido.');
                }
            } else {
                // Captura de errores específica de la API de Google
                $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido en el servidor de Gemini';
                throw new \Exception($errorDetail);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Fallo la IA', 'text' => $e->getMessage()]);
        }

        $this->mejorandoDetalle = false;
    }

    // public function mejorarTextoIAacciones()
    // {
    //     if (empty($this->previos_derivacion)) return;
    //     $this->mejorandoAcciones = true;

    //     try {
    //         $response = Http::withHeaders([
    //             'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
    //             'Content-Type' => 'application/json',
    //         ])->post('https://api.groq.com/openai/v1/chat/completions', [
    //             'model' => 'llama-3.3-70b-versatile', // Modelo actualizado y vigente
    //             'messages' => [
    //                 [
    //                     'role' => 'system',
    //                     'content' => 'Eres un corrector de estilo profesional para reportes escolares.'
    //                 ],
    //                 [
    //                     'role' => 'user',
    //                     'content' => 'Mejora la redacción y ortografía de este texto, manteniéndolo formal: ' . $this->previos_derivacion
    //                 ]
    //             ],
    //             'temperature' => 0.5,
    //         ]);

    //         if ($response->successful()) {
    //             $this->previos_derivacion = $response->json()['choices'][0]['message']['content'];
    //             $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
    //         } else {
    //             // Si Groq devuelve error, aquí veremos qué modelo sugiere usar
    //             $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido';
    //             throw new \Exception($errorDetail);
    //         }
    //     } catch (\Exception $e) {
    //         $this->dispatch('swal', ['icon' => 'error', 'title' => 'Fallo la IA', 'text' => $e->getMessage()]);
    //     }

    //     $this->mejorandoAcciones = false;
    // }

    public function mejorarTextoIAacciones()
    {
        if (empty($this->previos_derivacion)) return;
        $this->mejorandoAcciones = true;

        try {
            // 1. Preparamos el texto uniendo las instrucciones con tu variable
            $prompt = "Eres un corrector de estilo profesional para reportes escolares.\n\n" .
                      "Mejora la redacción y ortografía de este texto, manteniéndolo formal: " . $this->previos_derivacion  ;

            // 2. Hacemos la petición a la API de Gemini (modelo 2.5 flash)
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.5, // Mantenemos tu configuración original
                ]
            ]);

            // 3. Verificamos la respuesta
            if ($response->successful()) {
                // Extracción de texto usando la estructura JSON específica de Gemini
                $data = $response->json();

                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $this->previos_derivacion = $data['candidates'][0]['content']['parts'][0]['text'];
                    $this->dispatch('swal', ['icon' => 'success', 'title' => '¡Mejorado con éxito!']);
                } else {
                    throw new \Exception('Gemini no devolvió ningún texto válido.');
                }
            } else {
                // Captura de errores específica de la API de Google
                $errorDetail = $response->json()['error']['message'] ?? 'Error desconocido en el servidor de Gemini';
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
                    'fecha'          => $this->fechaintervencionMasiva,
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
            $this->dispatch('limpiarSelecciones');
            //$this->js('window.pgBulkActions.clearAll("estudianteTable")');
            //$this->dispatch('pg:clearAllSelections-estudianteTable');
            $this->dispatch('refreshTable'); // Para que la tabla muestre los nuevos cursos

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Cambio Exitoso!',
                'text' => 'Los cursos han sido actualizados correctamente.'
            ]);
            //$this->dispatch('refreshTable');

        } catch (\Exception $e) {
            $this->dispatch('swal', ['icon' => 'error', 'title' => 'Error', 'text' => $e->getMessage()]);
        }
    }

    public function desvincularApoderado($apoderadoId)
    {
        if ($this->estudianteSeleccionadoRedes) {

            // 1. Desvinculamos en la tabla pivote
            $this->estudianteSeleccionadoRedes->apoderados()->detach($apoderadoId);

            // 2. EL HELPER MANUAL DE AUDITORÍA
            $apoderado = \App\Models\Apoderado::find($apoderadoId);
            $nombreApoderado = $apoderado ? $apoderado->apoderado : "ID: {$apoderadoId}";

            activity()
                ->causedBy(auth()->user())
                ->performedOn($this->estudianteSeleccionadoRedes) // El sujeto es el Estudiante
                ->log("Se desvinculó al apoderado: {$nombreApoderado}");

            // 3. Recargamos la relación para que desaparezca visualmente del modal
            $this->estudianteSeleccionadoRedes->load('apoderados');

            // 4. Mostramos la alerta de éxito
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Desvinculado',
                'text' => 'El apoderado fue quitado de la ficha del estudiante.',
                'timer' => 1500
            ]);
        }

        $this->modalApoderados=false;
    }

}
