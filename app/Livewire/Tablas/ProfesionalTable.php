<?php

namespace App\Livewire\Tablas;

use App\Models\Profesional;
use App\Models\Tipoprofesional; // <-- NECESARIO PARA TRAER LOS TIPOS
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class ProfesionalTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'profesionalTable';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            PowerGrid::exportable(fileName: $this->tableName)
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
        ];
    }

    public function datasource(): Builder
    {
        return Profesional::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        // Traemos todos los tipos desde la BD una sola vez para no hacer miles de consultas
        $tiposProfesionales = Tipoprofesional::all();

        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('tipo')
            // 👇 AQUÍ CREAMOS LA CELDA PERSONALIZADA CON EL SELECT INLINE 👇
            ->add('tipo_dropdown', function (Profesional $model) use ($tiposProfesionales) {

                // Generamos las etiquetas <option> dinámicamente
                $opcionesHTML = '<option value="" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Seleccionar...</option>';
                foreach ($tiposProfesionales as $tp) {
                    $opcionesHTML .= '<option value="' . $tp->tipo . '" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">' . $tp->tipo . '</option>';
                }

                return \Illuminate\Support\Facades\Blade::render('
                    <div x-data="{ selectedTipo: \'' . ($model->tipo ?? '') . '\' }">
                        <select wire:change="updateTipoProfesional(' . $model->id . ', $event.target.value)"
                                x-model="selectedTipo"
                                :class="{
                                    \'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900/50 dark:text-blue-400 dark:border-blue-800\': selectedTipo,
                                    \'bg-white text-gray-700 border-gray-300 dark:bg-neutral-800 dark:text-white dark:border-neutral-600\': !selectedTipo
                                }"
                                class="block w-full min-w-[150px] rounded-full border text-center font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">
                            ' . $opcionesHTML . '
                        </select>
                    </div>
                ');
            })
            ->add('observacion')
            ->add('estado_badge', function (Profesional $model) {
                return $model->estado == 'Activo'
                    ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Activo</span>'
                    : '<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Inactivo</span>';
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),

            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable()
                ->editOnClick()
                ->visibleInExport(true)
                ->headerAttribute('class="w-64"')
                ->bodyAttribute('class="w-64"'),

            // Cambiamos 'tipo' por 'tipo_dropdown' para mostrar la lista desplegable
            Column::make('Tipo', 'tipo_dropdown', 'tipo')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),

            Column::make('Tipo', 'tipo')
                ->hidden(true)
                ->visibleInExport(true),

            Column::make('Correo', 'email')
                ->sortable()
                ->searchable()
                ->editOnClick()
                ->headerAttribute('class="w-64"')
                ->bodyAttribute('class="w-64"'),

            Column::make('Observacion', 'observacion')
                ->sortable()
                ->searchable()
                ->editOnClick()
                ->headerAttribute('class="w-64"')
                ->bodyAttribute('class="w-64"'),

            Column::make('Estado', 'estado_badge', 'estado')
                ->sortable()
                ->searchable()
                ->visibleInExport(false),

            Column::make('Estado','estado')
                ->hidden(true)
                ->visibleInExport(true),

            Column::action('Action')
        ];
    }

    public function onUpdatedEditable($id, $field, $value): void
    {
        Profesional::query()
            ->find($id)
            ->update([
                $field => $value
            ]);
    }

    // 👇 LA FUNCIÓN PARA GUARDAR CUANDO SE CAMBIA EL SELECT 👇
    public function updateTipoProfesional($id, $value): void
    {
        $valorFinal = $value === '' ? null : $value;

        Profesional::query()->find($id)?->update([
            'tipo' => $valorFinal
        ]);

        $this->dispatch('notificacion', mensaje: 'Tipo de profesional actualizado correctamente');
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {
        $profesional = Profesional::find($rowId);
        if ($profesional) {
            $profesional->update([
                'estado' => $profesional->estado === 'Activo' ? 'Inactivo' : 'Activo'
            ]);
        }
        $this->dispatch('refreshTable');
    }

    public function actions(Profesional $row): array
    {
        return [
            Button::add('delete')
                ->slot('<i class="fa-solid fa-power-off"></i>')
                ->id()
                ->tooltip('Activar / Desactivar Estudiante')
                ->class('p-2 rounded bg-red-500 text-white hover:bg-red-600')
                ->dispatch('delete', ['rowId' => $row->id]),
        ];
    }

    protected $listeners = [
        'refreshTable' => '$refresh',
    ];
}
