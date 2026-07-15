<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Blade; // <--- 1. Importar Blade
use Illuminate\Support\Facades\Auth;  // <--- 2. Importar Auth

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Preguntamos: ¿Ya existe la tabla configuracions en la base de datos?
        if (Schema::hasTable('configuracions')) {

            // Si existe, traemos el registro
            $configuracion = Configuracion::first();

            // Y lo compartimos con TODAS las vistas
            View::share('configuracion', $configuracion);

        } else {
            // Si no existe (ej. recién instalando), pasamos null para que no falle
            View::share('configuracion', null);
        }


        // 3. Crear la directiva personalizada
        Blade::if('rol', function (...$roles) {
            // Verificamos que el usuario esté logueado
            if (Auth::check()) {
                // Comparamos el rol del usuario (asumiendo que tu columna se llama 'rol')
                // con el arreglo de roles que enviaste desde la vista
                return in_array(Auth::user()->rol, $roles);
            }
            return false;
        });

    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
