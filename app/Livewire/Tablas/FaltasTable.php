<?php

namespace App\Livewire\Tablas;

use App\Models\Falta;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class FaltasTable extends PowerGridComponent
{
    public string $tableName = 'faltasTable';

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
        return Falta::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('falta')
            ->add('tipo_falta')
            ->add('tipo_falta_dropdown', function (Falta $model) {
                return \Illuminate\Support\Facades\Blade::render('
                    {{-- Usamos Alpine.js para escuchar el valor y cambiar el color en vivo --}}
                    <div x-data="{ selectedFalta: \'{{ $model->tipo_falta }}\' }">
                        <select wire:change="updateTipoFalta({{ $model->id }}, $event.target.value)"
                                x-model="selectedFalta"
                                :class="{
                                    \'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/50 dark:text-green-400 dark:border-green-800\': selectedFalta === \'No Aplica\',
                                    \'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/50 dark:text-yellow-400 dark:border-yellow-800\': selectedFalta === \'Leve\',
                                    \'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-900/50 dark:text-orange-400 dark:border-orange-800\': selectedFalta === \'Grave\',
                                    \'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/50 dark:text-red-400 dark:border-red-800\': selectedFalta === \'Gravísima\',
                                    \'bg-white text-gray-700 border-gray-300 dark:bg-neutral-800 dark:text-white dark:border-neutral-600\': !selectedFalta
                                }"
                                class="block w-full min-w-[130px] rounded-full border text-center font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">

                            {{-- Forzamos el fondo de las opciones a blanco/oscuro para que la lista no se manche con los colores --}}
                            <option value="" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Seleccionar...</option>
                            <option value="No Aplica" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">No Aplica</option>
                            <option value="Leve" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Leve</option>
                            <option value="Grave" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Grave</option>
                            <option value="Gravísima" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Gravísima</option>
                        </select>
                    </div>
                ', ['model' => $model]);
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),
            Column::make('Falta', 'falta')
                ->sortable()
                ->editOnClick()
                ->searchable(),

            // Column::make('Tipo Falta', 'tipo_falta')
            //     ->sortable()
            //     ->editOnClick()
            //     ->searchable(),

            Column::make('Tipo Falta', 'tipo_falta_dropdown', 'tipo_falta')
                ->sortable()
                ->searchable(),

            Column::action('Action')
                ->hidden(true),
        ];
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

    public function onUpdatedEditable($id, $field, $value): void
    {
        Falta::query()
            ->find($id)
            ->update([
                $field => $value
            ]);
    }

    public function updateTipoFalta($id, $value): void
    {
        // Si el valor viene vacío (Seleccionar...), lo guardamos como null
        $valorFinal = $value === '' ? null : $value;

        Falta::query()->find($id)?->update([
            'tipo_falta' => $valorFinal
        ]);

        $this->dispatch('notificacion', mensaje: 'Tipo de falta actualizado correctamente');
    }

    public function actions(Falta $row): array
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
