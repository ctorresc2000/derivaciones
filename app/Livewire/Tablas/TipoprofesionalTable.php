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
            ->add('tipo')
            ->add('area_control_dropdown', function ($model) {
                return \Illuminate\Support\Facades\Blade::render('
                    {{-- Usamos Alpine.js para manejar el estado visual del dropdown --}}
                    <div x-data="{ selectedArea: \'{{ $model->departamento }}\' }">
                        <select wire:change="updateAreaControl({{ $model->id }}, $event.target.value)"
                                x-model="selectedArea"
                                :class="{
                                    \'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/50 dark:text-blue-400 dark:border-blue-800\': selectedArea === \'Convivencia\',
                                    \'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-900/50 dark:text-emerald-400 dark:border-emerald-800\': selectedArea === \'Psicosocial\',
                                    \'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-900/50 dark:text-purple-400 dark:border-purple-800\': selectedArea === \'Pedagógico\',
                                    \'bg-white text-gray-700 border-gray-300 dark:bg-neutral-800 dark:text-white dark:border-neutral-600\': !selectedArea
                                }"
                                class="block w-full min-w-[150px] rounded-full border text-center font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200 cursor-pointer">

                            <option value="" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Sin Asignar</option>
                            <option value="Convivencia" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Convivencia</option>
                            <option value="Psicosocial" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Psicosocial</option>
                            <option value="Pedagógico" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Pedagógico</option>
                        </select>
                    </div>
                ', ['model' => $model]);
            })
            ->add('departamento');
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

            Column::make('Departamento', 'area_control_dropdown')
                ->headerAttribute('class="w-64"')
                ->bodyAttribute('class="w-64"'),

            // Column::make('Departamento', 'departamento')
            //     ->sortable()
            //     ->searchable()
            //     ->headerAttribute('class="w-64"')
            //     ->bodyAttribute('class="w-64"'),

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
            \App\Models\Tipoprofesional::where('tipo', $nombreAntiguo)->update([
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

    public function updateAreaControl($id, $value)
    {
        // Buscas el modelo y actualizas el campo
        $profesional = TipoProfesional::find($id);
        $profesional->update(['departamento' => $value]);

        // Opcional: mostrar un mensaje de éxito
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Área Actualizada',
            'timer' => 1000
        ]);
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
