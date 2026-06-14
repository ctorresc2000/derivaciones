<?php

namespace App\Livewire\Tablas;

use App\Models\Curso;
use App\Models\Estado;
use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowergrid\Detail;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;


final class EstudianteTable extends PowerGridComponent
{

    //public $icono='fa-solid fa-eye';
    public string $tableName = 'estudianteTable';
    public $visible=true;
    public array $detallesAbiertos = [];
    public $data=[];

    //public string $tableName = 'estudiantes-tabla';

    public $iconoBorrar ="<i class='fa-solid fa-trash'></i>";

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
                //->showCheckBox(),
            PowerGrid::footer()
                ->showPerPage(40, [20, 40, 60, 100])
                ->showRecordCount(),

            PowerGrid::detail()
                ->view('livewire.estudiante.detalles'),
                //->showCollapseIcon()
                //->params(['obervacion' => 'Luan', 'email' => 'email']),
            // PowerGrid::responsive()
        ];
    }

    public function header(): array
    {
        return [
            Button::add('bulk-intervencion')
                // Este slot muestra el contador de seleccionados automáticamente
                ->slot('Intervención Masiva')// (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('bg-amber-500 text-white px-3 py-2 rounded-md text-sm font-semibold mr-4')
                // Usamos el punto y el nombre de la tabla
                ->dispatch('prepararMasivo.' . $this->tableName, []),

            // Button::add('bulk-curso')
            //     ->slot('Cambiar Curso año Siguiente ')//(<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)
            //     ->tooltip('UTILICE ESTE BOTÓN SOLAMENTE SI CAMBIA DE UN AÑO A OTRO.')
            //     ->class('bg-indigo-600 text-white px-3 py-2 rounded-md text-sm font-semibold')
            //     ->dispatch('abrirModalPromocion.' . $this->tableName, []),

             Button::add('autorizar-matricula')
                // Este slot muestra el contador de seleccionados automáticamente
                ->slot('Autorizar Edicion Apoderados')// (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('bg-green-500 text-white px-3 py-2 rounded-md text-sm font-semibold mr-4')
                // Usamos el punto y el nombre de la tabla
                ->dispatch('autorizarEdicionMasiva.' . $this->tableName, []),
               // ->dispatch('autorizar', ['rowId' => $row->id]),
        ];
    }


    public function datasource(): Builder
    {
        return Estudiante::query()->with('curso')->withCount('apoderados');;
    }

    public function relationSearch(): array
    {
        return [
            'estudiante' => [
                'nombre',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('apellido')
            ->add('social')
            ->add('rut')
            ->add('fecha_nacimiento_formatted', fn (Estudiante $model) => Carbon::parse($model->fecha_nacimiento)->format('d/m/Y'))
            ->add('domicilio')
            ->add('email')
            ->add('telefono')
            ->add('curso_nombre', fn (Estudiante $model) => $model->curso ? $model->curso->curso : 'Sin curso asignado')
            ->add('observaciones')
            ->add('estado_badge', function (Estudiante $model) {
            return $model->estado == 'Activo'
                ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Activo</span>'
                : '<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Inactivo</span>';
            })
            ->add('created_at')
            ->add('anio')
            ->add('matricula')
            ->add('mat_aut', function (Estudiante $model) {
            return $model->matricula == 'SI'
                ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">SI</span>'
                : '<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">NO</span>';
            });
    }


    public function columns(): array
    {
        return [
            Column::make('Rut', 'rut')
                ->sortable()
                // ->fixedOnResponsive()
                ->searchable(),
            Column::make('Id', 'id')
                ->hidden(true),
            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable()
                // ->fixedOnResponsive()
                ->editOnClick(),

            Column::make('Apellido', 'apellido')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Nombre Social', 'social')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Curso', 'curso_nombre', 'curso_id')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado_badge', 'estado')
                ->sortable()
                ->searchable(),
            Column::make('AÑO MAT.','anio'),
            Column::make('AUT. MATRICULA','mat_aut'),



            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('fecha_nacimiento'),
            Filter::select('curso_id') // El campo de la tabla estudiantes por el que vamos a filtrar
            ->dataSource(Curso::all()) // Obtenemos todos los cursos directamente de la BD
            ->optionLabel('curso') // Lo que ve el usuario (el nombre del curso)
            ->optionValue('id'),   // El valor que se usa internamente para filtrar (el ID)

            Filter::select('estado')
                ->dataSource([
                    ['id' => 'Activo', 'estado' => 'Activo'],
                    ['id' => 'Inactivo', 'estado' => 'Inactivo'],
                ])
                ->optionLabel('estado')
                ->optionValue('id'),


        ];
    }

    #[\Livewire\Attributes\On('limpiarSelecciones')]
    public function limpiarSeleccionesNativo(): void
    {
        // 1. Limpiamos la memoria interna de Livewire (Backend)
        if (property_exists($this, 'checkboxValues')) {
            $this->checkboxValues = [];
        }

        // 2. Limpiamos la memoria de Alpine.js en el navegador (Frontend)
        $this->js('if (window.pgBulkActions) { window.pgBulkActions.clearAll("' . $this->tableName . '"); }');
    }

    public function onUpdatedEditable($id, $field, $value): void
    {
        Estudiante::query()
            ->find($id)
            ->update([
                $field => $value
            ]);
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->dispatch('editEstudiante',  $rowId);
    }


    #[\Livewire\Attributes\On('toggleEstado')]
    public function toggleEstado($rowId): void
    {
        // 1. Buscamos al estudiante en la Base de Datos
        $estudiante = \App\Models\Estudiante::find($rowId);

        if ($estudiante) {
            // 2. Si está Activo, lo pasamos a Inactivo, y viceversa
            $estudiante->estado = ($estudiante->estado === 'Activo') ? 'Inactivo' : 'Activo';

            // 3. Guardamos los cambios en la Base de Datos
            $estudiante->save();

            // Nota: Al hacer save(), PowerGrid detectará el cambio y recargará la fila automáticamente.
        }
    }

    #[\Livewire\Attributes\On('alternarDetalle')]
    public function alternarDetalle($id): void
    {
        // 1. Verificamos si la fila ya estaba en nuestro arreglo de "abiertas"
        if (in_array($id, $this->detallesAbiertos)) {
            // Si ya estaba abierta, la sacamos del arreglo (la cerramos)
            $this->detallesAbiertos = array_diff($this->detallesAbiertos, [$id]);
        } else {
            // Si no estaba, metemos su ID al arreglo (la abrimos)
            $this->detallesAbiertos[] = $id;
        }

        // 2. Le decimos a PowerGrid que muestre o esconda la fila inferior
        $this->toggleDetail($id);
    }


    public function actions(Estudiante $row): array
    {
        // 1. Preguntamos: ¿El ID de este estudiante está en nuestra memoria de abiertos?
        $estaAbierto = in_array($row->id, $this->detallesAbiertos);

        // 2. Definimos el diseño basándonos en esa respuesta
        $icono = $estaAbierto ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        $textoTooltip = $estaAbierto ? 'Ocultar Detalles' : 'Ver más Información';
        $colorBoton = $estaAbierto ? 'bg-purple-600 hover:bg-purple-700' : 'bg-yellow-500 hover:bg-yellow-600';

        // 2. LÓGICA PARA EL BOTÓN ACTIVO/INACTIVO
        $esActivo = $row->estado === 'Activo';

        // Si está activo, el botón será rojo (para desactivar). Si está inactivo, será verde (para activar)
        $colorEstado = $esActivo ? 'bg-red-500 hover:bg-red-600' : 'bg-emerald-500 hover:bg-emerald-600';
        $iconoEstado = $esActivo ? 'fa-solid fa-user-xmark' : 'fa-solid fa-user-check';
        $textoEstado = $esActivo ? 'Desactivar Estudiante' : 'Activar Estudiante';


        $botones= [];

        if (Auth::user()->rol === "Administrador") {
            $botones[]=
                Button::add('edit')
                ->slot('<i class="fa-solid fa-pen-to-square"></i>')
                ->id('btn-edit-' . $row->id)
                ->tooltip('Editar estudiante')
                ->class('p-2 rounded bg-blue-500 text-white hover:bg-blue-600')
                ->dispatch('edit', ['rowId' => $row->id]);

            $botones[]=
                Button::add('toggle_estado')
                ->slot('<i class="' . $iconoEstado . '"></i>')
                ->id('btn-estado-' . $row->id)
                ->tooltip($textoEstado)
                ->class('p-2 rounded text-white ' . $colorEstado)
                ->dispatch('toggleEstado', ['rowId' => $row->id]);
        }

        $botones[]=
            Button::add('btn_detalle')
            ->slot('<i class="' . $icono . '"></i>')
            ->id('btn-detalle-' . $row->id)
            ->tooltip($textoTooltip)
            ->class('p-2 rounded text-white ' . $colorBoton)
            // Disparamos nuestro evento, exactamente igual a como lo haces con 'delete' o 'edit'
            ->dispatch('alternarDetalle', ['id' => $row->id]);

        if ($row->estado === "Activo" && Auth::user()->rol==="Administrador" || Auth::user()->tipoProfesional->departamento === "Convivencia") {
            $botones[]=Button::add('enviar')
            ->slot('<i class="fa-solid fa-scale-balanced"></i>')
            ->tooltip('Intervenir por Convivencia')
            ->class('p-2 rounded bg-green-500 text-white hover:bg-green-600')
            ->route('derivaciones', ['id' => $row->id]);
        }

        if ($row->estado === "Activo" && Auth::user()->rol==="Administrador" || Auth::user()->tipoProfesional->departamento === "Psicosocial") {
            $botones[]=Button::add('psicosocial')
                ->slot('<i class="fa-solid fa-hand-holding-heart"></i>')
                ->tooltip('Intervenir por Psicosocial')
                ->class('p-2 rounded bg-teal-500 text-white hover:bg-teal-600')
                ->route('intervencionpsicosocial', ['id' => $row->id]);
        }

        if ($row->estado === "Activo") {
            $botones[]=Button::add('estudianteDerivado')
                ->slot('<i class="fa-solid fa-file-export"></i> ')
                ->tooltip('Derivar')
                ->class('p-2 rounded bg-pink-500 text-white hover:bg-pink-600')
                ->dispatch('abrirModalDerivacion', ['rowId' => $row->id]); // AQUÍ DISPARAMOS EL EVENTO

        }

        $botones[]=Button::add('redes')
            ->slot('<i class="fa-solid fa-house-medical"></i>')
            ->tooltip('Redes de Apoyo')
            ->class('bg-emerald-500 text-white p-2 rounded-md')
            ->dispatch('abrirModalRedes', ['estudianteId' => $row->id]);

        $botones[]=Button::add('apoderados')
            ->slot('<i class="fa-solid fa-restroom"></i>'. ' (' . $row->apoderados_count.')')
            ->tooltip('Apoderados ')
            ->class('bg-violet-500 text-white p-2 rounded-md')
            ->dispatch('abrirModalApoderados', ['estudianteId' => $row->id]);

        $botones[]=Button::add('historial')
            ->slot('<i class="fa-solid fa-clock-rotate-left"></i> ')
            ->tooltip('Historial Estudiante')
            ->class('p-2 rounded bg-zinc-500 text-white hover:bg-zinc-600')
            ->route('estudiante.historial', ['id' => $row->id]); // <-- Cambiamos la ruta aquí



        return $botones;
    }



    protected $listeners = [
        'refreshTable' => '$refresh',

    ];


    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
