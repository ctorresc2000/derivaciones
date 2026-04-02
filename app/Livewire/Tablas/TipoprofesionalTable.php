<?php

namespace App\Livewire\Tablas;

use App\Models\Tipoprofesional;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TipoprofesionalTable extends PowerGridComponent
{
    public string $tableName = 'tipoprofesionalTable';

    public function setUp(): array
    {
        $this->showCheckBox();

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
        return Tipoprofesional::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('tipo');
    }

    public function columns(): array
    {
        return [

            Column::make('Id', 'id'),
                //->hidden(true),

            Column::make('Tipo Profesión', 'tipo')
                ->sortable()
                ->searchable()
                ->editOnClick()
                ->headerAttribute('class="w-64"')
                ->bodyAttribute('class="w-64"'),

            Column::action('Action')
                ->hidden(true),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    public function onUpdatedEditable($id, $field, $value): void
    {
        // 1. Buscamos el tipo antes de cambiarlo para saber su nombre viejo
        $tipoProfesional = Tipoprofesional::find($id);
        $nombreAntiguo = $tipoProfesional->tipo;

        // 2. Lo actualizamos con el nuevo valor
        $tipoProfesional->update([
            $field => $value
        ]);

        // 3. Si lo que editaste fue el nombre ("tipo"), actualizamos en cascada a los profesionales
        if ($field === 'tipo') {
            \App\Models\Profesional::where('tipo', $nombreAntiguo)->update([
                'tipo' => $value
            ]);
        }
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(Tipoprofesional $row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
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
