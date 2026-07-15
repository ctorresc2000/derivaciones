<?php

namespace App\Livewire\Tablas;

use App\Models\Curso;
use App\Models\Entrevista;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class EntrevistasTable extends PowerGridComponent
{
    public array $detallesAbiertos = [];
    public string $tableName = 'entrevistasTable';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),

             PowerGrid::detail()
                ->view('livewire.estudiante.detalle_entrevista'),
        ];
    }

    public function datasource(): Builder
    {
        //return Entrevista::query()->with(['estudiante', 'curso', 'user'])->orderBy('fecha', 'desc');

       return Entrevista::query()
            // Join con Cursos
            ->join('cursos', 'entrevistas.curso_id', '=', 'cursos.id')
            // Join con Estudiantes
            ->join('estudiantes', 'entrevistas.estudiante_id', '=', 'estudiantes.id')
            // UN SOLO select con todos los alias necesarios
            ->select([
                'entrevistas.*',
                'cursos.curso as curso_nombre_orden',
                // Concatenamos para ordenar alfabéticamente por apellido
                \DB::raw('CONCAT(estudiantes.apellido, " ", estudiantes.nombre) as estudiante_orden')
            ])
            ->with(['user']) // Las relaciones simples se mantienen con with
            ->orderBy('entrevistas.fecha', 'desc');
    }

    public function relationSearch(): array
    {
        return [
            'estudiante' => [ // Este es el nombre de la relación definida en tu modelo Entrevista
                'nombre',    // Campo en la tabla 'estudiantes'
                'apellido',  // Campo en la tabla 'estudiantes'
            ],
            'curso' => [
                'entrevistas.curso_id   ',
            ],
            'user' => [
                'name',
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')

            ->add('tipo_entrevistado', function (Entrevista $model) {
                return $model->es_apoderado ? 'Apoderado' : 'Estudiante';
            })

            ->add('tipo_entrevistado', function (Entrevista $model) {
                return $model->es_apoderado ? 'Apoderado' : 'Estudiante';
            })
            // Extraemos el nombre del estudiante
            ->add('estudiante_nombre', function (Entrevista $model) {
                return $model->estudiante ? ($model->estudiante->nombre . ' ' . $model->estudiante->apellido) : 'N/A';
            })
            // Extraemos el nombre del curso
            ->add('curso_nombre_orden')
            ->add('curso_nombre', function (Entrevista $model) {
                return $model->curso ? $model->curso->curso : 'N/A'; // Asegúrate que el campo en BD sea 'curso' o 'nombre'
            })
            // Extraemos el nombre del usuario (entrevistador)
            ->add('user_nombre', function (Entrevista $model) {
                return $model->user ? $model->user->name : 'N/A';
            })
            ->add('es_apoderado')
            ->add('nombre_apoderado')
            ->add('motivo')
            ->add('detalle')
            ->add('fecha_formatted', fn (Entrevista $model) => Carbon::parse($model->fecha)->format('d/m/Y'))
            ->add('firma')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),

            Column::make('Fecha', 'fecha_formatted', 'fecha')
                ->sortable(),

            // Esta es la nueva columna
            Column::make('Entrevistado','tipo_entrevistado', 'es_apoderado') // El campo booleano de tu tabla
                ->sortable()
                ->searchable()
                ->headerAttribute('class', 'hidden md:table-cell')
                ->bodyAttribute('class', 'hidden md:table-cell'),

            Column::make('Estudiante', 'estudiante_orden') // Sin el espacio al final
                ->sortable()
                ->searchable(),

            Column::make('Curso', 'curso_nombre','curso_nombre_orden') // Sin el espacio al final
                ->sortable()
                ->searchable(),

            Column::make('Entrevistador', 'user_nombre','user_id') // Sin el espacio al final
                ->sortable()
                ->searchable()
                ->headerAttribute('class', 'hidden md:table-cell')
                ->bodyAttribute('class', 'hidden md:table-cell'),

            Column::make('Nombre apoderado', 'nombre_apoderado')
                ->sortable()
                ->searchable()->headerAttribute('class', 'hidden md:table-cell')
                ->bodyAttribute('class', 'hidden md:table-cell'),

            Column::make('Motivo', 'motivo')
                ->sortable()
                ->searchable()
                ->headerAttribute('class', 'hidden md:table-cell')
                ->bodyAttribute('class', 'hidden md:table-cell'),


            // Column::make('Detalle', 'detalle')
            //     ->sortable()
            //     ->searchable(),



            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::boolean('es_apoderado')
                ->label('Apoderado', 'Estudiante'),
            Filter::datepicker('fecha')
                ->params([
                    'enableTime' => false,
                ]),

            // 3. Filtro de Curso: Usamos 'entrevistas.curso_id' para evitar ambigüedad
            Filter::select('curso_nombre', 'entrevistas.curso_id')
                ->dataSource(Curso::all())
                ->optionLabel('curso')
                ->optionValue('id'),
            Filter::select('user_id') // El campo de la tabla estudiantes por el que vamos a filtrar
                ->dataSource(User::all()) // Obtenemos todos los cursos directamente de la BD
                ->optionLabel('name') // Lo que ve el usuario (el nombre del usuario)
                ->optionValue('id'),   // El valor que se usa internamente para filtrar (el ID),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
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

    public function actions(Entrevista $row): array
    {
         $estaAbierto = in_array($row->id, $this->detallesAbiertos);

        $icono = $estaAbierto ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        $textoTooltip = $estaAbierto ? 'Ocultar Detalles' : 'Ver más Información';
        $colorBoton = $estaAbierto ? 'bg-purple-600 hover:bg-purple-700' : 'bg-yellow-500 hover:bg-yellow-600';

        return [

            Button::add('btn_detalle')
                ->slot('<i class="' . $icono . '"></i>')
                ->id('btn-detalle-' . $row->id)
                ->tooltip($textoTooltip)
                ->class('p-2 rounded text-white ' . $colorBoton)
                // Disparamos nuestro evento, exactamente igual a como lo haces con 'delete' o 'edit'
                ->dispatch('alternarDetalle', ['id' => $row->id]),

            Button::add('print')
                ->slot('<i class="fa-solid fa-print"></i>')
                ->id('btn-print-' . $row->id)
                ->tooltip('Imprimir PDF')
                ->class('p-2 rounded text-white bg-blue-500 hover:bg-blue-600')
                // Cambiamos el dispatch por url()
                ->dispatch('imprimirPDF', ['id' => $row->id]),

            Button::add('arvhivos')
                ->slot('<i class="fa-solid fa-camera"></i>')
                ->id('btn-print-' . $row->id)
                ->tooltip('Subir Imágenes')
                ->class('p-2 rounded text-white bg-green-500 hover:bg-green-600')
                // Cambiamos el dispatch por url()
                ->route('entrevistas.camara', ['id' => $row->id]),

            // Button::add('edit')
            //     ->slot('Edit: '.$row->id)
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id])
        ];
    }



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
