<?php

namespace App\Livewire\Tablas;

use App\Models\Curso;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class CursosTable extends PowerGridComponent
{
    public string $tableName = 'cursosTable';

    public function setUp(): array
    {
        // $this->showCheckBox();

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
        return Curso::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('curso')
            ->add('descripcion')
            ->add('estado_badge', function (Curso $model) {
            return $model->estado == 'Activo'
                ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Activo</span>'
                : '<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Inactivo</span>';
        });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),

             Column::make('Curso', 'curso')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            Column::make('Descripcion', 'descripcion')
                ->sortable()
                ->searchable()
                ->editOnClick(),


            Column::make('Estado', 'estado_badge', 'estado')
                ->sortable()
                ->searchable(),


            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function onUpdatedEditable($id, $field, $value): void
    {
        Curso::query()
            ->find($id)
            ->update([
                $field => $value
            ]);
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        // 1. Primero buscamos y guardamos el registro en la variable
        $curso = Curso::find($rowId);
        if ($curso) {
            // 3. Actualizamos evaluando su propio estado actual (sin punto y coma al final de la línea)
            $curso->update([
                'estado' => $curso->estado === 'Activo' ? 'Inactivo' : 'Activo'
            ]);
        }
        $this->dispatch('refreshTable');
    }

    public function actions(Curso $row): array
    {
        return [
            Button::add('delete')
                ->slot('<i class="fa-solid fa-power-off"></i>')
                ->id()
                ->tooltip('Activar / Desactivar Curso')
                ->class('p-2 rounded bg-red-500 text-white hover:bg-red-600')
                ->dispatch('delete', ['rowId' => $row->id]),
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
