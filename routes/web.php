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
use App\Models\Estudiante;
use Barryvdh\DomPDF\Facade\Pdf;

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

// Ruta para el historial del estudiante

Route::get('/estudiante/{id}/historial-pdf', function ($id) {
        // 1. Buscamos al estudiante con sus datos
        $estudiante = Estudiante::with([
            'intervenciones.usuario', 'intervenciones.detalles.falta', 'intervenciones.detalles.medida',
            'derivaciones.user', 'derivaciones.profesionalDerivado', 'derivaciones.motivo'
        ])->findOrFail($id);

        // 2. Preparamos las intervenciones
        $intervenciones = $estudiante->intervenciones->map(function ($item) {
            $detalle = $item->detalles->first();
            return (object) [
                'tipo_registro' => 'Intervención de convivencia escolar',
                'fecha' => $item->fecha_incidente ?? $item->created_at,
                'hora' => $item->created_at->format('H:i'),
                'profesional' => ($item->usuario->name ?? 'Usuario Desconocido'),
                'detalle' => $item->descripcion,
                'estado' => $item->estado,
                'etiqueta_1' => 'Tipo de Falta',
                'valor_1' => $detalle ? ($detalle->falta->falta ?? $detalle->falta->tipo_falta ?? 'No especificada') : 'No especificada',
                'etiqueta_2' => 'Tipo de Medida',
                'valor_2' => $detalle ? ($detalle->medida->medida ?? $detalle->tipo_medida ?? 'No especificada') : 'No especificada',
            ];
        });

        // 3. Preparamos las derivaciones
        $derivaciones = $estudiante->derivaciones->map(function ($item) {
            $nombreMotivo = $item->motivo->motivo ?? 'Motivo desconocido';
            return (object) [
                'tipo_registro' => 'Derivación: ' . $nombreMotivo,
                'fecha' => $item->fecha_derivacion,
                'hora' => $item->created_at->format('H:i'),
                'profesional' => ($item->profesionalDerivado->name ?? $item->user->name ?? 'Usuario Desconocido'),
                'detalle' => $item->detalle_derivacion,
                'estado' => $item->estado,
                'etiqueta_1' => 'Motivo Derivación',
                'valor_1' => $nombreMotivo,
                'etiqueta_2' => 'Tipo de Intervención',
                'valor_2' => $item->tipo_intervencion ?? 'Intervención Psicosocial',
            ];
        });

        // 4. Juntamos todo y lo ordenamos por fecha
        $historial = $intervenciones->concat($derivaciones)->sortByDesc(function ($item) {
            return \Carbon\Carbon::parse($item->fecha)->format('Y-m-d') . ' ' . $item->hora;
        })->values()->all();

        // 5. Generamos el PDF
        $pdf = Pdf::loadView('pdf.historial-estudiante', [
            'estudiante' => $estudiante,
            'historial' => $historial
        ]);

        // 👇 LA MAGIA ESTÁ AQUÍ: stream() hace que se visualice en lugar de descargar 👇
        return $pdf->stream('Historial_' . $estudiante->rut . '.pdf');

    })->name('historial.pdf');

    // Ruta para el historial del estudiante en la vista Livewire

Route::get('/estudiante/{id}/historial', HistorialComponent::class)->name('estudiante.historial');

require __DIR__.'/settings.php';
