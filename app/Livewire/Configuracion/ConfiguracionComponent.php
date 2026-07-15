<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use Livewire\WithFileUploads; // <- IMPORTANTE: Permite subir archivos
use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use App\Models\User;

use Spatie\Activitylog\Models\Activity;

class ConfiguracionComponent extends Component
{
    public $archivoLlenado;

    public $mesFiltro;
    public $usuarioFiltro = '';

    use WithFileUploads; // <- Habilitamos la subida de archivos

    // Declaramos las variables que hacen match con los wire:model de tu vista
    public $institucion;
    public $domicilio;
    public $telefono;
    public $email;
    public $logo;

    // Variable extra para mostrar el logo actual si es que ya hay uno guardado
    public $logo_actual;

    // La función mount() se ejecuta una sola vez al cargar la página
    public function mount()
    {
        // Buscamos si ya existe una configuración en la base de datos
        $config = Configuracion::first();

        $this->mesFiltro = date('Y-m');

        // Si existe, llenamos los campos del formulario con esos datos
        if ($config) {
            $this->institucion = $config->nombre_institucion;
            $this->domicilio   = $config->domicilio;
            $this->telefono    = $config->telefono;
            $this->email       = $config->email;
            $this->logo_actual = $config->logo; // Guardamos la ruta del logo existente
        }
    }

    public function guardar()
    {
        // 1. Validamos los datos (El logo debe ser una imagen de máximo 2MB)
        $this->validate([
            'institucion' => 'nullable|string|max:255',
            'domicilio'   => 'nullable|string|max:255',
            'telefono'    => 'nullable|string|max:255',
            'email'       => 'nullable|email|max:255',
            'logo'        => 'nullable|image|max:2048',
        ]);

        // 2. Preparamos los datos a guardar
        $datosParaGuardar = [
            'nombre_institucion' => $this->institucion,
            'domicilio'          => $this->domicilio,
            'telefono'           => $this->telefono,
            'email'              => $this->email,
        ];

        // Traemos el registro actual (si existe)
        $config = Configuracion::first();

        // 3. Lógica para procesar la imagen (Solo si el usuario subió una nueva)
        if ($this->logo) {
            // Si ya existía un logo anterior, lo borramos del servidor para no ocupar espacio basura
            if ($config && $config->logo) {
                Storage::disk('public')->delete($config->logo);
            }
            // Guardamos la nueva imagen en la carpeta "logos" dentro del disco public
            $rutaLogo = $this->logo->store('logos', 'public');
            $datosParaGuardar['logo'] = $rutaLogo;
        }

        // 4. EL TRUCO PRINCIPAL: Actualizamos o Creamos
        if ($config) {
            // Si ya existe, simplemente lo actualizamos
            $config->update($datosParaGuardar);
        } else {
            // Si no existe (tabla vacía), creamos el primer registro
            Configuracion::create($datosParaGuardar);
        }

        // 5. Limpiamos la caché global para que el menú y todo el sistema se actualicen de inmediato
        Cache::forget('configuracion_global');

        // 6. Reseteamos el input del archivo y actualizamos el logo actual visualmente
        if ($this->logo) {
            $this->logo_actual = $datosParaGuardar['logo'];
            $this->logo = null;
        }

        // 7. Disparamos la alerta de éxito
        $this->dispatch('swal', [
            'title' => 'Configuración actualizada',
            'icon' => 'success'
        ]);

        $this->js('window.location.reload();');
    }

    public function render()
    {
        // 1. Iniciamos la consulta base
        $query = Activity::with(['causer', 'subject'])->latest();

        // 2. Filtro por Mes
        if (!empty($this->mesFiltro)) {
            $partes = explode('-', $this->mesFiltro);
            if (count($partes) == 2) {
                $query->whereYear('created_at', $partes[0])
                      ->whereMonth('created_at', $partes[1]);
            }
        }

        // 3. NUEVO: Filtro por Usuario
        if (!empty($this->usuarioFiltro)) {
            $query->where('causer_id', $this->usuarioFiltro);
        }

        // 4. Traemos los registros filtrados
        $actividades = $query->take(300)->get();

        // 5. Traemos la lista de todos los usuarios para llenar el Select
        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('livewire.configuracion.configuracion-component', [
            'actividades' => $actividades,
            'usuarios'    => $usuarios // <-- Enviamos los usuarios a la vista
        ]);
    }

    public function importarLlenado()
    {
        // 1. Validamos que hayan subido un archivo Excel
        $this->validate([
            'archivoLlenado' => 'required|mimes:xlsx,xls,csv|max:5120', // Máximo 5MB
        ]);

        // 2. Ejecutamos nuestra importación Multi-Hoja
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\LlenadoImport, $this->archivoLlenado);

        // 3. Limpiamos el input
        $this->archivoLlenado = null;

        // 4. Mostramos el mensaje de éxito
        $this->dispatch('swal', [
            'title' => 'Datos base importados correctamente',
            'icon' => 'success'
        ]);
    }

    // #[On('guardarTextoManual')]
    // public function guardarTextoManual($texto) // <-- Cambiamos $data por $texto
    // {
    //     if (!empty($texto)) {
    //         // Guardamos el contenido en el archivo manual.txt
    //         Storage::disk('public')->put('manual.txt', $texto);

    //         $this->dispatch('swal', [
    //             'title' => 'Manual procesado',
    //             'text' => 'El contenido del PDF ha sido indexado correctamente para la IA.',
    //             'icon' => 'success'
    //         ]);
    //     }
    // }

    #[On('guardarTextoManual')]
    public function guardarTextoManual($texto)
    {
        if (!empty($texto)) {
            // 1. Quitamos saltos de línea, tabulaciones y espacios dobles
            // Esto reduce el tamaño un 30% sin perder información
            $textoLimpio = preg_replace('/\s+/', ' ', $texto);

            // 2. Recorte de seguridad para el Tier gratuito de Groq
            // 15,000 caracteres es ideal para no pasarse de los 6,000 TPM (Tokens per Minute)
            $textoOptimizado = mb_substr($textoLimpio, 0, 15000);

            // 3. Sobreescribimos el archivo viejo
            Storage::disk('public')->put('manual.txt', $textoOptimizado);

            $this->dispatch('swal', [
                'title' => 'Manual Optimizado',
                'text' => 'El texto ha sido reducido para cumplir con los límites de la IA gratuita.',
                'icon' => 'success'
            ]);
        }
    }
}
