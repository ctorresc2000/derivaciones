<?php

namespace App\Livewire\User;

use App\Models\Tipoprofesional;
use App\Models\User;
use Flux\Flux;
use Livewire\Component;

class UserComponent extends Component
{
    public $name;
    public $email;
    public $rol;
    public $userId;
    //public $social;
    public $password;
    public $password_confirmation;
    public $abrirModal = false;
    public $cambiarPasword = false;
    public $tipo_de_profesional;
    public $tipo_profesionales=[];

    public function render()
    {
        return view('livewire.user.user-component');
    }

    public function mount()
    {
        $this->tipo_profesionales = Tipoprofesional::orderBy('tipo','asc')->get();
    }


    public function guardar()
    {
        $this->validate([
            'name'=>'required',
            //'social'=>'nullable',
            'email'=>'required|email|unique:users,email',
            'rol'=>'required',
            'tipo_de_profesional'=>'nullable',
            'password'=>'required|min:8|confirmed',
            'password_confirmation'=>'required|min:8'
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'rol' => $this->rol,
            'tipo_profesional_id' => $this->tipo_de_profesional,
            'password' => bcrypt($this->password),
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Guardado Exitósamente',
            'timer' => 1500
        ]);

        $this->abrirModal = false;
        $this->dispatch('refreshTable');
        $this->resetValidation();
        $this->reset('name', 'email', 'rol', 'password', 'password_confirmation','userId','tipo_de_profesional');
    }

    public function actualizar()
    {
        $this->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$this->userId,
            //'social'=>'nullable',
            'rol'=>'required',
            'tipo_de_profesional'=>'nullable',
            'password'=>'nullable|min:8|confirmed',
            'password_confirmation'=>'nullable|min:8'
        ]);

        $user = User::find($this->userId);
        $user->name = $this->name;
        $user->email = $this->email;
        //$user->social= $this->social;
        $user->tipo_profesional_id=$this->tipo_de_profesional;
        $user->rol = $this->rol;

        if ($this->password) {
            $user->password = bcrypt($this->password);
        }

        $user->save();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Actualizado Exitósamente',
            'timer' => 1500
        ]);

        $this->abrirModal = false;
        $this->dispatch('refreshTable');
        $this->resetValidation();
        $this->reset('name', 'email', 'rol', 'password', 'password_confirmation','userId','tipo_de_profesional');
    }

    #[\Livewire\Attributes\On('editPassword')]
    public function editPassword($rowId): void
    {
        $this->userId = $rowId;
        $this->cambiarPasword = true;
    }

    public function actualizarPassword()
    {
        $this->validate([
            'password'=>'required|min:8|confirmed',
            'password_confirmation'=>'required|min:8'
        ]);

        $user = User::find($this->userId);
        $user->password = bcrypt($this->password);
        $user->save();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Contraseña Actualizada Exitósamente',
            'timer' => 1500
        ]);

        $this->cambiarPasword = false;
        $this->dispatch('refreshTable');
        $this->resetValidation();
        $this->reset('password', 'password_confirmation','userId');
    }

    #[\Livewire\Attributes\On('edit ')]
    public function eliminar($id)
    {
        User::destroy($id);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Felicitaciones',
            'text' => 'Registro Eliminado Exitósamente',
            'timer' => 1500
        ]);

        $this->dispatch('refreshTable');
    }

    public function cerrarModal()
    {
        $this->cambiarPasword = false;
        $this->abrirModal = false;
        $this->resetValidation();
        $this->reset('name', 'email', 'rol', 'password', 'password_confirmation','userId','tipo_de_profesional');
    }

     #[\Livewire\Attributes\On('editUser')]
     public function editUser($rowId): void
     {
        //dd($rowId);
         $user = User::find($rowId);
         //dd($user);
         $this->userId = $rowId;
         $this->name = $user->name;
         $this->email = $user->email;
         //$this->social = $user->social;
         $this->rol = $user->rol;
         $this->tipo_de_profesional = $user->tipo_profesional_id;
         $this->abrirModal = true;
     }
}
