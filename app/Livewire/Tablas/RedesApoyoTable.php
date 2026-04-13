<?php

namespace App\Livewire\Tablas;

use App\Models\RedesApoyo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RedesApoyoTable extends PowerGridComponent
{
    public string $tableName = 'redesApoyoTable';

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
        return RedesApoyo::query();
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
                ->add('contacto')
                ->add('telefono')
                ->add('email');
        }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hidden(true)
                ->sortable()
                ->searchable(),

            Column::make('Red de Apoyo', 'nombre')
                ->sortable()
                ->searchable()
                ->editOnClick(),

            // Column::make('Contacto', 'contacto')
            //     ->sortable()
            //     ->searchable()
            //     ->editOnClick(),

            // Column::make('Teléfono', 'telefono')
            //     ->sortable()
            //     ->searchable()
            //     ->editOnClick(),

            // Column::make('Email', 'email')
            //     ->sortable()
            //     ->searchable()
            //     ->editOnClick(),

            Column::action('Acciones')
                    ->hidden(true),

        ];
    }

    public function onUpdatedEditable($id, $field, $value): void
    {
        RedesApoyo::query()
            ->find($id)
            ->update([
                $field => $value
            ]);
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(RedesApoyo $row): array
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
