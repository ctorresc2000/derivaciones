<?php

namespace App\Livewire\Tablas;

use App\Models\Derivarestudiante;
use App\Models\Estudiante;
use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class estudiantederivadoTable extends PowerGridComponent
{
    public array $detallesAbiertos = [];
    public $conclusion;
    public string $tableName = 'estudiantederivadoTable';

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
                ->view('livewire.estudiante.detalle_derivacion'),
        ];
    }

    public function datasource(): Builder
    {
        $anioActivo = session('anio_activo', date('Y'));

        return Derivarestudiante::query()
            ->with(['user', 'estudiante', 'profesional','motivo'])
            ->whereYear('created_at', $anioActivo);
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('fecha_derivacion_formatted', fn (Derivarestudiante $model) => Carbon::parse($model->fecha_derivacion)->format('d/m/Y'))
            ->add('user_id')
            ->add('estudiante_id')
            ->add('motivo_derivacion')
            ->add('motivo_nombre', function (Derivarestudiante $model) {
                // Usamos el operador nulo (?->) por si acaso el estudiante fue borrado
                return $model->motivo?->motivo ?? 'Sin motivo';
            })
            ->add('previos_derivacion')
            ->add('profesional_derivado_id')
            ->add('detalle_derivacion')
            //->add('estado')

            ->add('tipo_estado_dropdown', function (Derivarestudiante $model) {

                $estadoSeguro = $model->estado ?: 'Abierta';

                return \Illuminate\Support\Facades\Blade::render('
                    {{-- Envolvemos en un DIV con wire:key para protegerlo del reciclaje de Livewire --}}
                    <div wire:key="estado-container-{{ $model->id }}" x-data="{ estado: \'{{ $estadoSeguro }}\' }">

                        {{-- Quitamos wire:change y usamos x-on:change para llamar a Livewire silenciosamente --}}
                        <select x-model="estado"
                                x-on:change="$wire.updateTipoEstado({{ $model->id }}, estado)"
                                :class="{
                                    \'bg-green-100 text-green-800 border-green-300 dark:bg-green-900/50 dark:text-green-400 dark:border-green-800\': estado === \'Pendiente\',
                                    \'bg-yellow-100 text-yellow-800 border-yellow-300 dark:bg-yellow-900/50 dark:text-yellow-400 dark:border-yellow-800\': estado === \'En Proceso\',
                                    \'bg-red-100 text-red-800 border-red-300 dark:bg-red-900/50 dark:text-red-400 dark:border-red-800\': estado === \'Cerrado\'
                                }"
                                class="block w-full min-w-[130px] rounded-full border text-center font-semibold text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">

                            <option value="Pendiente" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Pendiente</option>
                            <option value="En Proceso" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">En Proceso</option>
                            <option value="Cerrado" class="bg-white text-gray-900 dark:bg-neutral-800 dark:text-white">Cerrado</option>
                        </select>
                    </div>
                ', [
                    'estadoSeguro' => $estadoSeguro,
                    'model' => $model
                ]);
            })

            ->add('created_at')
            ->add('estudiante_nombre', function (Derivarestudiante $model) {
                // Usamos el operador nulo (?->) por si acaso el estudiante fue borrado
                return $model->estudiante?->nombre . ' ' . $model->estudiante?->apellido.' ('.$model->estudiante?->social.')';
            })
            ->add('usuario_nombre', function (Derivarestudiante $model) {
                // Usamos el operador nulo (?->) por si acaso el estudiante fue borrado
                return $model->user?->name;
            })
            ->add('profesional_nombre', function (Derivarestudiante $model) {
                // Entramos a la relación "profesional" y pedimos la columna "name" de la tabla users
                return $model->profesional ? $model->profesional->name : 'Sin asignar';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),

            Column::make('Fecha derivacion', 'fecha_derivacion_formatted', 'fecha_derivacion')
                ->sortable(),

            Column::make('Derivado Por','usuario_nombre' ,'user_id'),

            Column::make('Estudiante','estudiante_nombre', 'estudiante_id'),

            // Column::make('Motivo derivacion', 'motivo_derivacion')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Motivo derivacion', 'motivo_nombre')
                ->sortable()
                ->searchable(),

            Column::make('Previos derivacion', 'previos_derivacion')
                ->hidden(true)
                ->sortable()
                ->searchable(),

            Column::make('Profesional derivado','profesional_nombre'),

            Column::make('Detalle derivacion', 'detalle_derivacion')
                ->hidden(true)
                ->sortable()
                ->searchable(),
            Column::make('estado', 'tipo_estado_dropdown', 'estado')
                 ->sortable(),

            // Column::make('Estado', 'estado')
            //     ->sortable()
            //     ->searchable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('fecha_derivacion_formatted', 'fecha_derivacion'),
        ];
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

        Derivarestudiante::query()->find($id)?->update([
            'estado' => $valorFinal
        ]);

        $this->dispatch('notificacion', mensaje: 'Estado actualizado correctamente');
        $this->dispatch('pg:eventRefresh-estudiantederivadoTable');
        //$this->dispatch('refreshTable');
    }

    public function actions(Derivarestudiante $row): array
    {
        // 1. Preguntamos: ¿El ID de este estudiante está en nuestra memoria de abiertos?
        $estaAbierto = in_array($row->id, $this->detallesAbiertos);

        // 2. Definimos el diseño basándonos en esa respuesta
        $icono = $estaAbierto ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        $textoTooltip = $estaAbierto ? 'Ocultar Detalles' : 'Ver más Información';
        $colorBoton = $estaAbierto ? 'bg-purple-600 hover:bg-purple-700' : 'bg-yellow-500 hover:bg-yellow-600';

        return [
            // Button::add('edit')
            //     ->slot('Edit: '.$row->id)
            //     ->id()
            //     ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
            //     ->dispatch('edit', ['rowId' => $row->id]),

            // BOTÓN DE SEGUIMIENTO (Abre el modal)
            Button::add('seguimiento')
                ->slot('<i class="fa-solid fa-notes-medical"></i>') // Ícono médico/seguimiento
                ->id()
                ->tooltip('Agregar Seguimiento')
                ->class('p-2 rounded bg-blue-500 text-white hover:bg-blue-600')
                ->dispatch('abrirModalSeguimiento', ['rowId' => $row->id]),

            Button::add('btn_detalle')
                ->slot('<i class="' . $icono . '"></i>')
                ->id('btn-detalle-' . $row->id)
                ->tooltip($textoTooltip)
                ->class('p-2 rounded text-white ' . $colorBoton)
                // Disparamos nuestro evento, exactamente igual a como lo haces con 'delete' o 'edit'
                ->dispatch('alternarDetalle', ['id' => $row->id]),
        ];
    }

    #[On('guardarConclusion')]
    public function guardarConclusion($id, $texto = '')
    {
        $estudianteDerivado = Derivarestudiante::find($id);
        $estudianteDerivado->conclusiones= $texto;
        $estudianteDerivado->save();


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
