<?php

namespace App\Livewire\Apoderado;

use App\Models\Apoderado;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Traits\HasDocuments;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;

class ApoderadoComponent extends Component
{
    use WithFileUploads;
    use HasDocuments;

    public $name;
    public $rut;
    public $email;
    public $domicilio;
    public $telefono;
    public $carnet_apoderado;
    public $tipo_apoderado;
    public $abrirModal=false;
    public $creando;
    public $apoderadoId;
    public $idestudianteSeleccionado;
    public $modalEstudiante=false;

    protected $listeners = ['datos','abrirModalEstudiante'];

    #[On('abrirModalEstudiante')]
    public function abrirModalEstudiante($rowId)
    {
        $this->idestudianteSeleccionado=Apoderado::find($rowId);

        $this->modalEstudiante=true;
    }

    #[\Livewire\Attributes\On('vincularEstudiante')]
    public function vincularEstudiante($estudianteId)
    {
        // 1. Verificamos que tengamos un apoderado cargado previamente
        // (Nota: Asegúrate de usar el nombre de la variable donde guardaste al apoderado
        // al abrir el modal. Si no la cambiaste, debe ser $this->idestudianteSeleccionado)

        if ($this->idestudianteSeleccionado) {

            // 2. LA MAGIA: Unimos al Apoderado con el Estudiante en la tabla pivote.
            // syncWithoutDetaching evita que se duplique si por error le das dos veces clic.
            $this->idestudianteSeleccionado->estudiantes()->syncWithoutDetaching([$estudianteId]);

            $estudianteAsignado = \App\Models\Estudiante::find($estudianteId);
            $nombreEstudiante = $estudianteAsignado ? "{$estudianteAsignado->nombre} {$estudianteAsignado->apellido}" : "ID: {$estudianteId}";

            activity()
                ->causedBy(auth()->user())
                ->performedOn($this->idestudianteSeleccionado) // El sujeto es el apoderado
                ->log("Vinculó a este apoderado con el estudiante: {$nombreEstudiante}");

            // 3. Mostramos el mensaje de éxito
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Asignado!',
                'text' => 'El estudiante fue vinculado correctamente al apoderado.',
                'timer' => 2000
            ]);

            // 4. Cerramos el modal de asignación y refrescamos la tabla de apoderados
            $this->modalEstudiante = false;
            $this->dispatch('refreshTable');
        }
    }

    public function desvincularEstudiante($apoderadoId, $estudianteId)
    {
        $apoderado = Apoderado::find($apoderadoId);

        if ($apoderado) {
            // 1. Quitamos la relación en la tabla pivote
            $apoderado->estudiantes()->detach($estudianteId);

            // 2. EL HELPER MANUAL DE AUDITORÍA
            $estudianteAsignado = \App\Models\Estudiante::find($estudianteId);
            $nombreEstudiante = $estudianteAsignado ? "{$estudianteAsignado->nombre} {$estudianteAsignado->apellido}" : "ID: {$estudianteId}";

            activity()
                ->causedBy(auth()->user())
                ->performedOn($apoderado) // El sujeto auditado es el apoderado
                ->log("Desvinculó a este apoderado del estudiante: {$nombreEstudiante}");

            // 3. Mostramos la alerta de éxito
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Desvinculado',
                'text' => 'El estudiante fue removido de este apoderado correctamente.',
                'timer' => 2000
            ]);

            // 4. Refrescamos la tabla (o la vista donde muestras los hijos del apoderado)
            $this->dispatch('refreshTable');
        }
    }

    public function modalApoderado()
    {
        $this->abrirModal=true;
        $this->creando=true;
    }

    public function cerrarModal()
    {
        $this->abrirModal=false;
    }

    public function guardarApoderado()
    {
        $this->validate([
            'name'=>'required',
            'rut'=>'required|unique:apoderados,rut',
            'email'=>'nullable',
            'domicilio'=>'required',
            'telefono'=>'required',
            'tipo_apoderado'=>'required',
            'carnet_apoderado' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $nuevoApoderado = Apoderado::create([
            'apoderado' => $this->name,
            'rut' => $this->rut,
            'direccion' => $this->domicilio,
            'telefono' => $this->telefono,
            'correo' => $this->email,
            'tipo_apoderado'=>$this->tipo_apoderado,
            'estado' => 'Activo',
        ]);

        // 👇 LO NUEVO: Guardado polimórfico del apoderado 👇
       if ($this->carnet_apoderado) {
            $rutaGuardada = $this->carnet_apoderado->store("apoderados", 'public');

            $nuevoApoderado->update([
                'carnet'=>$rutaGuardada,
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
        $this->reset('name','rut','email','domicilio','telefono','carnet_apoderado','tipo_apoderado');
    }

    #[On('datos')]
    public function datos($rowId)
    {
        $apoderado=Apoderado::find($rowId);
        $this->apoderadoId=$apoderado->id;
        $this->name = $apoderado->apoderado;
        $this->rut = $apoderado->rut;
        $this->email = $apoderado->correo;
        $this->domicilio = $apoderado->direccion;
        $this->telefono = $apoderado->telefono;
        $this->tipo_apoderado = $apoderado->tipo_apoderado;
        $this->carnet_apoderado = $apoderado->carnet;

        $this->creando=false;
        $this->abrirModal=true;

    }

    public function actualizarDatos()
    {
        $apoderadoEditado=Apoderado::find($this->apoderadoId);

        //dd($apoderadoEditado);

        $this->validate([
            'name'=>'required',
            'rut' => [
                'required',
                Rule::unique('apoderados', 'rut')->ignore($this->apoderadoId)
            ],
            'email'=>'nullable',
            'domicilio'=>'required',
            'telefono'=>'required',
            'tipo_apoderado'=>'required',
            // Descomentamos esto para permitir que se suban archivos al editar
            'carnet_apoderado' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $apoderadoEditado->update([
            'apoderado' => $this->name,
            'rut' => $this->rut,
            'direccion' => $this->domicilio,
            'telefono' => $this->telefono,
            'correo' => $this->email,
            'tipo_apoderado'=>$this->tipo_apoderado,
            'estado' => 'Activo',
        ]);

        // 👇 LO NUEVO: Lógica de actualización de imagen y borrado de basura 👇
        if ($this->carnet_apoderado && !is_string($this->carnet_apoderado)) {
            // 1. Borramos el archivo físico viejo para no dejar basura
            if (!empty($apoderadoEditado->carnet) && \Illuminate\Support\Facades\Storage::disk('public')->exists($apoderadoEditado->carnet)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($apoderadoEditado->carnet);
            }

            // 2. Guardamos el nuevo archivo en la ruta segura
            $rutaGuardada = $this->carnet_apoderado->store("apoderados", 'public');

            // 3. Actualizamos la BD
            $apoderadoEditado->update([
                'carnet' => $rutaGuardada
            ]);
        }

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Actualizado',
            'text'  => 'Los datos del apoderado se actualizaron correctamente.',
            'timer' => 1500
        ]);

        $this->abrirModal = false;
        $this->dispatch('refreshTable');
        $this->reset('name', 'rut', 'email', 'domicilio', 'telefono', 'carnet_apoderado', 'tipo_apoderado', 'apoderadoId');
    }

    public function render()
    {
        return view('livewire.apoderado.apoderado-component');
    }
}
