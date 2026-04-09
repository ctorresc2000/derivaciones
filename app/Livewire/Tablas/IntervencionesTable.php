<?php

namespace App\Livewire\Tablas;

use App\Models\Derivarestudiante;
use App\Models\Estudiante;
use App\Models\Intervencion;
use App\Models\User;
use id;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class IntervencionesTable extends PowerGridComponent
{
    public $conclusion;

    public array $detallesAbiertos = [];

    public string $tableName = 'intervencionesTable';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            PowerGrid::detail()
                    ->view('livewire.estudiante.detalle_intervencion'),
        ];


    }

    public function datasource(): Builder
    {
        $anioActivo = session('anio_activo', date('Y'));
        $user = auth()->user();

        return Intervencion::query()
            ->with(['user', 'estudiante', 'detalles.falta', 'detalles.medida','detalles.tipo', 'detalles.motivo'])
            ->whereYear('created_at', $anioActivo)
            ->when($user->rol !== 'Administrador', function ($query) use ($user) {
                // Si NO es Administrador, filtramos por su usuario_id
                $query->where('usuario_id', $user->id);
            });
            // Si ES Administrador, el 'when' se ignora y muestra todos los registros
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('estudiante_nombre', function (Intervencion $model) {
                // Usamos el operador nulo (?->) por si acaso el estudiante fue borrado
                return $model->estudiante?->nombre . ' ' . $model->estudiante?->apellido.' ('.$model->estudiante?->social.')';
            })
            ->add('usuario_nombre', function (Intervencion $model) {
                // Usamos el operador nulo (?->) por si acaso el estudiante fue borrado
                return $model->user?->name;
            })
            //->add('profesional_derivado_nombre', fn (Derivarestudiante $model) => $model->profesionalDerivado ? $model->profesionalDerivado->name : 'Sin asignar')

            ->add('via_ingreso', function (Intervencion $model) {
                // Usamos el operador nulo (?->) por si acaso el estudiante fue borrado
                return $model->viaIngreso?->via_ingreso;
            })
            ->add('via_ingreso_id')
            ->add('descripcion')
            ->add('fecha_formatted', fn (Intervencion $model) => Carbon::parse($model->fecha)->format('d/m/Y'))

            // ->add('estado_badge', function (Intervencion $model) {
            // return $model->estado == 'Abierta'
            //     ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Abierta</span>'
            //     : ($model->estado == 'Derivada'
            //         ? '<span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Derivada</span>'
            //         : '<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Concluida</span>') ;
            // })

            ->add('tipo_estado_dropdown', function (Intervencion $model) {

                $estadoSeguro = $model->estado ?: 'Abierta';

                return \Illuminate\Support\Facades\Blade::render('
                    {{-- Envolvemos en un DIV con wire:key para protegerlo del reciclaje de Livewire --}}
                    <div wire:key="estado-container-{{ $model->id }}" x-data="{ estado: \'{{ $estadoSeguro }}\' }">

                        {{-- Quitamos wire:change y usamos x-on:change para llamar a Livewire silenciosamente --}}
                        <select x-model="estado"
                                x-on:change="$wire.updateTipoEstado({{ $model->id }}, estado)"
                                :class="{
                                    \'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/50 dark:text-green-400 dark:border-green-800\': estado === \'Abierta\',
                                    \'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/50 dark:text-yellow-400 dark:border-yellow-800\': estado === \'Derivada\',
                                    \'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/50 dark:text-red-400 dark:border-red-800\': estado === \'Concluida\'
                                }"
                                class="block w-full min-w-[130px] rounded-full border text-center font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">

                            <option value="Abierta" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Abierta</option>
                            <option value="Derivada" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Derivada</option>
                            <option value="Concluida" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Concluida</option>
                        </select>
                    </div>
                ', [
                    'estadoSeguro' => $estadoSeguro,
                    'model' => $model
                ]);
            })

            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),
            Column::make('Fecha', 'fecha_formatted', 'fecha')
                ->sortable(),
            Column::make('Estudiante','estudiante_nombre', 'estudiante_id')
                ->sortable()
                ->searchable(),
            Column::make('Intervenido por','usuario_nombre', 'usuario_id')
                ->sortable()
                ->searchable(),
            // Column::make('Derivado a','profesional_derivado_nombre', 'profesional_derivado_id')
            //     ->sortable()
            //     ->searchable(),
            Column::make('Via ingreso','via_ingreso', 'via_ingreso_id')
                ->sortable()
                ->searchable(),
            // Column::make('Descripcion', 'descripcion')
            //     ->sortable()
            //     ->searchable(),


            // Column::make('Estado','estado_badge', 'estado')
            //     ->sortable(),

            Column::make('estado', 'tipo_estado_dropdown', 'estado')
                 ->sortable(),

            // Column::make('Created at', 'created_at')
            //     ->sortable()
            //     ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('fecha'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function updateTipoEstado($id, $value): void
    {
        // Si el valor viene vacío (Seleccionar...), lo guardamos como null
        $valorFinal = $value === '' ? null : $value;

        //dd($id, $valorFinal);

        Intervencion::query()->find($id)?->update([
            'estado' => $valorFinal
        ]);

        $this->dispatch('notificacion', mensaje: 'Estado actualizado correctamente');
        $this->dispatch('pg:eventRefresh-intervencionesTable');
        //$this->dispatch('refreshTable');
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

    public function actions(Intervencion $row): array
    {
        return [
            // Botón 1: Para abrir el modal de Seguimiento
            Button::add('edit')
                ->slot('<i class="fa-solid fa-clipboard-check"></i>')
                ->id()
                ->tooltip('Atender')
                ->class('p-2 rounded bg-blue-500 text-white hover:bg-blue-600')
                ->dispatch('abrirModal', ['rowId' => $row->id]),

            // Botón 2: Para abrir y cerrar los detalles usando la función nativa
            Button::add('toggleDetail')
                ->slot('<i class="fa-solid fa-eye"></i>')
                ->id('btn-detalle-' . $row->id)
                ->tooltip('Ver / Ocultar Observaciones')
                ->class('p-2 rounded text-white bg-yellow-500 hover:bg-yellow-600')
                ->toggleDetail($row->id),
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
