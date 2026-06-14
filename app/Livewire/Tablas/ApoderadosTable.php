<?php

namespace App\Livewire\Tablas;

use App\Models\Apoderado;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ApoderadosTable extends PowerGridComponent
{
    public string $tableName = 'apoderadosTable';

    public array $detallesAbiertos = [];

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
                ->view('livewire.apoderado.detalles'),
        ];
    }

    public function datasource(): Builder
    {
        return Apoderado::query()->with('estudiantes');
    }

    public function relationSearch(): array
    {
        return [
            'estudiantes' => ['nombre'] // Cambiado a plural
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('apoderado')
            ->add('estudiante_id')
            ->add('estudiantes_nombres', function (Apoderado $model) {
                return $model->estudiantes->count() > 0
                    ? $model->estudiantes->pluck('nombre')->implode(', ')
                    : 'Sin estudiante asignado';
            })
            ->add('rut')
            ->add('direccion')
            ->add('telefono')
            ->add('correo')
            //->add('estado')
            ->add('estado_badge', function (Apoderado $model) {
            return $model->estado == 'Activo'
                ? '<span class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Activo</span>'
                : '<span class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Inactivo</span>';
            })
            ->add('tipo_apoderado')
            ->add('carnet')
            ->add('carnet_thumbnail', function ($model) {
                if (!empty($model->carnet)) {
                    $url = asset('storage/' . $model->carnet);
                    return '
                        <a href="' . $url . '" target="_blank" rel="noopener noreferrer">
                            <img src="' . $url . '" class="w-10 h-10 object-cover rounded-md shadow-sm hover:opacity-80 transition-opacity mx-auto" alt="Carnet">
                        </a>
                    ';
                }
                return '<span class="text-xs text-zinc-400">Sin foto</span>';
            })
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),
            Column::make('Apoderado', 'apoderado')
                ->sortable()
                ->searchable(),

            Column::make('Estudiantes', 'estudiantes_nombres')
                ->searchable()
                ->hidden(true),

            Column::make('Rut', 'rut')
                ->sortable()
                ->searchable(),

            Column::make('Direccion', 'direccion')
                ->sortable()
                ->searchable(),

            Column::make('Telefono', 'telefono')
                ->sortable()
                ->searchable(),

            Column::make('Correo', 'correo')
                ->sortable()
                ->searchable(),


            Column::make('Tipo apoderado', 'tipo_apoderado')
                ->sortable()
                ->searchable(),

            Column::make('Carnet', 'carnet_thumbnail')
                ->headerAttribute('text-center') // Centra el título
                ->bodyAttribute('text-center'),  // Centra la imagen en la celda

            Column::make('Estado', 'estado_badge')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at')
                ->hidden(true)
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

    #[\Livewire\Attributes\On('edit')]
    public function edit($id): void
    {
        $apoderado = Apoderado::find($id);

        if ($apoderado) {
            // 2. Alternamos el estado usando un operador ternario
            $apoderado->estado = ($apoderado->estado === 'Activo') ? 'Inactivo' : 'Activo';

            // 3. Guardamos los cambios
            $apoderado->save();

            // 4. Disparamos tu SweetAlert para confirmar la acción visualmente
            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => 'Estado Actualizado',
                'text'  => 'El apoderado ahora está ' . $apoderado->estado,
                'timer' => 1500
            ]);
        }
    }

    #[\Livewire\Attributes\On('redirigirAEstudiantes')]
    public function redirigirAEstudiantes($id)
    {
        // Redirigimos al usuario a la vista de estudiantes, enviando el ID en la URL
        return redirect()->to('/estudiantes?editarApo=' . $id);
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

    public function actions(Apoderado $row): array
    {

     // 1. Preguntamos: ¿El ID de este estudiante está en nuestra memoria de abiertos?
        $estaAbierto = in_array($row->id, $this->detallesAbiertos);

        // 2. Definimos el diseño basándonos en esa respuesta
        $icono = $estaAbierto ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
        $textoTooltip = $estaAbierto ? 'Ocultar Detalles' : 'Ver Estudiantes Asignados';
        $colorBoton = $estaAbierto ? 'bg-purple-600 hover:bg-purple-700' : 'bg-yellow-500 hover:bg-yellow-600';
        $botones=[];

        $botones[]=Button::add('modificar')
            ->slot('<i class="fa-solid fa-user-pen"></i>')
            ->tooltip('Editar Apoderado')
            ->class('p-2 rounded bg-blue-500 text-white hover:bg-blue-600')
            ->dispatch('datos',['rowId' => $row->id]);
            //->dispatch('redirigirAEstudiantes', ['id' => $row->id]);

        $botones[]=Button::add('edit')
            ->slot('<i class="fa-solid fa-user-xmark"></i>')
            ->tooltip('Desactivar Apoderado')
            ->class('p-2 rounded bg-red-500 text-white hover:bg-red-600')
            ->dispatch('edit', ['id' => $row->id]);

        $botones[]=
            Button::add('btn_detalle')
            ->slot('<i class="' . $icono . '"></i>')
            ->id('btn-detalle-' . $row->id)
            ->tooltip($textoTooltip)
            ->class('p-2 rounded text-white ' . $colorBoton)
            // Disparamos nuestro evento, exactamente igual a como lo haces con 'delete' o 'edit'
            ->dispatch('alternarDetalle', ['id' => $row->id]);

        $botones[]=Button::add('estudiante')
            ->slot('<i class="fa-solid fa-user-graduate"></i>')
            ->tooltip('Asignar Estudiante')
            ->class('p-2 rounded bg-green-500 text-white hover:bg-green-600')
            ->dispatch('abrirModalEstudiante', ['rowId' => $row->id]);

        return $botones;
    }

    public function desvincularEstudiante($apoderadoId, $estudianteId)
    {
        // 1. Buscamos al apoderado
        $apoderado = Apoderado::find($apoderadoId);

        if ($apoderado) {
            // 2. Usamos detach() para borrar la conexión en la tabla pivote
            $apoderado->estudiantes()->detach($estudianteId);

            // 3. Mostramos la alerta de éxito
            $this->dispatch('swal', [
                'icon'  => 'success',
                'title' => 'Desvinculado',
                'text'  => 'El estudiante ha sido removido de este apoderado.',
                'timer' => 1500
            ]);

            // Nota: Como PowerGrid es reactivo, al quitar el estudiante
            // la vista de detalles se actualizará automáticamente.
        }
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
