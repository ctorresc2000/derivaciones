<?php

namespace App\Livewire\Tablas;

//namespace App\Livewire\Examples\InputSelectTable;

use App\Models\User;
use App\Models\Curso;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowergrid\Detail;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;


final class UserTAble extends PowerGridComponent
{

    public string $tableName = 'userTable';

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
            // PowerGrid::detail()
            //     ->view('components.details')
            //     ->showCollapseIcon()
            //     ->params(['name' => 'Luan', 'email' => 'email']),
        ];
    }

    public function datasource(): Builder
    {
        return User::query()->with('tipoProfesional');
        //return User::query();
    }

    public function relationSearch(): array
    {
        return [];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('email')
            ->add('rol')
            ->add('created_at')
            ->add('nombre_tipo_profesional', function (User $model) {
                // Verificamos si tiene la relación asignada para no causar un error de "null"
                return $model->tipoProfesional ? $model->tipoProfesional->tipo : 'Sin asignar';
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->hidden(true),

            Column::make('Name', 'name')
                ->sortable()
                ->searchable()
                ->editOnClick()
                ->headerAttribute('class="w-64"')
                ->bodyAttribute('class="w-64"'),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Tipo Profesional', 'nombre_tipo_profesional')
                ->sortable()
                ->searchable(),

            Column::make('Rol', 'rol')
                ->sortable(),
                //->editOnClick(),



            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [

            Filter::select('rol')
                ->dataSource([
                    ['id' => 'Administrador', 'name' => 'Administrador'],
                    ['id' => 'Usuario', 'name' => 'Usuario'],
                ])
                ->optionLabel('name')
                ->optionValue('id'),

        ];
    }

    public function onUpdatedEditable($id, $field, $value): void
    {
        User::query()
            ->find($id)
            ->update([
                $field => $value
            ]);
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->dispatch('editUser',  $rowId);

    }

    #[\Livewire\Attributes\On('delete')]
    public function delete($rowId): void
    {

        $this->js('alert('.$rowId.')');
    }

    #[\Livewire\Attributes\On('password')]
    public function password($rowId): void
    {

        $this->dispatch('editPassword',  $rowId);
    }

    public function actions(User $row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="fa-solid fa-user-pen"></i>')
                ->id()
                ->tooltip('Editar Usuario')
                ->class('p-2 rounded bg-blue-500 text-white hover:bg-blue-600')
                ->dispatch('edit', ['rowId' => $row->id]),

            Button::add('delete')
                ->slot('<i class="fa-solid fa-trash"></i>')
                ->id()
                ->tooltip('Eliminar Usuario')
                ->class('p-2 rounded bg-red-500 text-white hover:bg-red-600')
                ->dispatch('delete', ['rowId' => $row->id]),

            Button::add('password')
                ->slot('<i class="fa-solid fa-key"></i>')
                ->id()
                ->tooltip('Editar Contraseña')
                ->class('p-2 rounded bg-green-500 text-white hover:bg-green-600')
                ->dispatch('password', ['rowId' => $row->id]),

            // Button::add('detail')
            //     ->slot('Detail')
            //     ->class('p-2 rounded bg-yellow-500 text-black hover:bg-yellow-600')
            //     ->toggleDetail($row->id),
        ];
    }




    protected $listeners = [
        'refreshTable' => '$refresh',
    ];

}
