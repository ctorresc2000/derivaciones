<?php
namespace App\Livewire\Estudiante; // <-- Tiene que tener "\Estudiante" al final
use App\Livewire\Configuracion\ConfiguracionComponent;
use App\Livewire\Cursos\CursosComponent;
use App\Livewire\Estudiante\DerivacionComponent;
use App\Livewire\Estudiante\EstudianteComponent;
use App\Livewire\Estudiante\IntervencionpsicosocialComponent;
use App\Livewire\Ingresos\FaltasComponent;
use App\Livewire\Ingresos\IntervencionesComponent;
use App\Livewire\Ingresos\MedidasComponent;
use App\Livewire\Ingresos\MotivointervencionComponent;
use App\Livewire\Ingresos\TipointervencionComponent;
use App\Livewire\Ingresos\ViaingresoComponent;
use App\Livewire\User\ProfesionalesComponent;
use App\Livewire\User\TipoprofesionalComponent;
use App\Livewire\User\UserComponent;
use App\Livewire\Estudiante\HistorialComponent;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/usuarios', UserComponent::class)->name('usuarios');
Route::get('/profesionales', ProfesionalesComponent::class)->name('profesionales');
Route::get('/estudiantes', EstudianteComponent::class)->name('estudiantes');
Route::get('/cursos', CursosComponent::class)->name('cursos');
Route::get('/derivaciones/{id}', DerivacionComponent::class)->name('derivaciones');
Route::get('/intervencionpsicosocial/{id}', IntervencionpsicosocialComponent::class)->name('intervencionpsicosocial');
Route::get('/viaingreso', ViaingresoComponent::class)->name('viaingreso');
Route::get('/faltas', FaltasComponent::class)->name('faltas');
Route::get('/medidas', MedidasComponent::class)->name('medidas');
Route::get('/tipoprofesional', TipoprofesionalComponent::class)->name('tipoprofesional');
Route::get('/configuracion', ConfiguracionComponent::class)->name('configuracion');
Route::get('/motivointervencion', MotivointervencionComponent::class)->name('motivointervencion');
Route::get('/tipointervencion', TipointervencionComponent::class)->name('tipointervencion');
Route::get('/estudiantesderivados', EstudiantederivadoComponent::class)->name('estudiantesderivados');
Route::get('/intervenciones', IntervencionesComponent::class)->name('intervenciones');

Route::get('/estudiante/{id}/historial', HistorialComponent::class)->name('estudiante.historial');

require __DIR__.'/settings.php';
