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
use App\Livewire\Ingresos\RedesComponent;
use Illuminate\Support\Facades\Route;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\EntrevistaExportController;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Container\Attributes\Auth;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('/usuarios', UserComponent::class)->name('usuarios')->middleware('rol:Administrador');
Route::get('/profesionales', ProfesionalesComponent::class)->name('profesionales')->middleware('rol:Administrador');
Route::get('/estudiantes', EstudianteComponent::class)->name('estudiantes')->middleware('rol:Administrador,Usuario');
Route::get('/cursos', CursosComponent::class)->name('cursos')->middleware('rol:Administrador');
Route::get('/derivaciones/{id}', DerivacionComponent::class)->name('derivaciones')->middleware('rol:Administrador,Usuario');
Route::get('/intervencionpsicosocial/{id}', IntervencionpsicosocialComponent::class)->name('intervencionpsicosocial')->middleware('rol:Administrador,Usuario');
Route::get('/viaingreso', ViaingresoComponent::class)->name('viaingreso');
Route::get('/faltas', FaltasComponent::class)->name('faltas')->middleware('rol:Administrador');
Route::get('/medidas', MedidasComponent::class)->name('medidas')->middleware('rol:Administrador');
Route::get('/tipoprofesional', TipoprofesionalComponent::class)->name('tipoprofesional')->middleware('rol:Administrador');
Route::get('/configuracion', ConfiguracionComponent::class)->name('configuracion')->middleware('rol:Administrador');
Route::get('/motivointervencion', MotivointervencionComponent::class)->name('motivointervencion')->middleware('rol:Administrador');
Route::get('/tipointervencion', TipointervencionComponent::class)->name('tipointervencion')->middleware('rol:Administrador');
Route::get('/estudiantesderivados', EstudiantederivadoComponent::class)->name('estudiantesderivados')->middleware('rol:Administrador,Usuario');
Route::get('/intervenciones', IntervencionesComponent::class)->name('intervenciones')->middleware('rol:Administrador,Usuario');
Route::get('/redes', RedesComponent::class)->name('redes')->middleware('rol:Administrador');
Route::get('/entrevistas', EntrevistaComponent::class)->name('entrevistas')->middleware('rol:Administrador,Usuario');
Route::get('/cardex', CardexComponent::class)->name('cardex')->middleware('rol:Administrador,Usuario');


Route::get('/instalar', function () {
    Artisan::call('storage:link');
    Artisan::call('optimize:clear');
    return "✅ Enlace creado y Cache limpio.";
});

// Ruta para el historial del estudiante

Route::get('/estudiante/{id}/historial-pdf', function ($id) {
    $estudiante = Estudiante::with([
        'curso',
        'intervenciones.usuario.tipoProfesional',
        'intervenciones.detalles.falta',
        'intervenciones.detalles.medida',
        'intervenciones.detalles.motivo',
        'intervenciones.detalles.tipo',
        'intervenciones.viaIngreso',
        'derivaciones.user',
        'derivaciones.motivo',
        'derivaciones.acciones.usuario'
    ])->findOrFail($id);

    // 1. Definimos las variables de color
    $getColor = function($estado) {
        return match($estado) {
            'Abierta', 'Pendiente' => '#dcfce7',
            'Derivada', 'En Proceso' => '#dbeafe',
            'Concluida', 'Finalizada' => '#f1f5f9',
            default => '#f1f5f9',
        };
    };

    $getTextColor = function($estado) {
        return match($estado) {
            'Abierta', 'Pendiente' => '#166534',
            'Derivada', 'En Proceso' => '#1e40af',
            'Concluida', 'Finalizada' => '#334155',
            default => '#334155',
        };
    };

    // 2. IMPORTANTE: Agregamos "use ($getColor, $getTextColor)" en los maps
    $intervenciones = $estudiante->intervenciones->map(function ($item) use ($getColor, $getTextColor) {
        return [
            'fecha'        => $item->fecha->format('d/m/Y'),
            'via'          => $item->viaIngreso->via_ingreso ?? 'N/A',
            'profesional'  => $item->usuario->name ?? 'N/A',
            'area'         => $item->usuario->tipoProfesional->departamento ?? 'Convivencia',
            'descripcion'  => $item->descripcion,
            'estado'       => $item->estado,
            'bg_color'     => $getColor($item->estado),      // Ahora sí será visible
            'text_color'   => $getTextColor($item->estado),  // Ahora sí será visible
            'detalles'     => $item->detalles,
        ];
    })->sortByDesc('fecha');

    $derivaciones = $estudiante->derivaciones->map(function ($item) use ($getColor, $getTextColor) {
        return [
            'fecha'        => $item->created_at->format('d/m/Y'),
            'motivo'       => $item->motivo->motivo ?? 'General',
            'profesional'  => $item->user->name ?? 'N/A',
            'tipo'         => $item->tipo_intervencion,
            'detalle'      => $item->detalle_derivacion,
            'estado'       => $item->estado,
            'bg_color'     => $getColor($item->estado),      // Ahora sí será visible
            'text_color'   => $getTextColor($item->estado),  // Ahora sí será visible
            'acciones'     => $item->acciones,
        ];
    })->sortByDesc('fecha');

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.historial-estudiante', [
        'estudiante'     => $estudiante,
        'intervenciones' => $intervenciones,
        'derivaciones'   => $derivaciones
    ]);

    return $pdf->stream('Historial_'.$estudiante->apellido.'.pdf');
})->name('historial.pdf')->middleware('rol:Administrador,Usuario');

Route::get('/entrevista/{entrevista}/pdf', [EntrevistaExportController::class, 'download'])
    ->name('entrevista.pdf')->middleware('rol:Administrador,Usuario');

    // Ruta para el historial del estudiante en la vista Livewire


Route::get('/estudiante/{id}/historial', HistorialComponent::class)->name('estudiante.historial')->middleware('rol:Administrador,Usuario');

require __DIR__.'/settings.php';
