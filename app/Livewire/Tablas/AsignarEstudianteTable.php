<?php

namespace App\Livewire\Tablas;

use App\Models\Estudiante;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class AsignarEstudianteTable extends PowerGridComponent
{
    public string $tableName = 'asignarEstudianteTable';

    public function setUp(): array
    {
        //$this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Estudiante::query()->with('curso')->where('estudiantes.estado','Activo');
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('apellido')
            ->add('rut')
            ->add('social')
            ->add('fecha_nacimiento_formatted', fn (Estudiante $model) => Carbon::parse($model->fecha_nacimiento)->format('d/m/Y'))
            ->add('domicilio')
            ->add('email')
            ->add('telefono')
            ->add('curso_id')
            ->add('curso_nombre', fn (Estudiante $model) => $model->curso ? $model->curso->curso : 'Sin curso asignado')
            ->add('observaciones')
            ->add('estado')
            ->add('estado_badge', function (Estudiante $model) {
            return $model->estado == 'Activo'
                ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Activo</span>'
                : '<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Inactivo</span>';
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')->hidden(true),
            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable(),

            Column::make('Apellido', 'apellido')
                ->sortable()
                ->searchable(),

            Column::make('Rut', 'rut')
                ->sortable()
                ->searchable(),


            Column::make('Curso', 'curso_nombre'),

            Column::make('Estado', 'estado_badge','estado')
                ->sortable()
                ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('fecha_nacimiento'),
        ];
    }


    public function actions(Estudiante $row): array
    {
        return [
            Button::add('asignar')
                ->slot('<i class="fa-solid fa-circle-check"></i> Asignar')
                ->tooltip('Asignar Estudiante')
                ->class('p-2 rounded bg-green-500 text-white hover:bg-green-600')
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('vincularEstudiante', ['estudianteId' => $row->id])
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
