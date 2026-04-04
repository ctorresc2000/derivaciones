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
    // 1. Buscamos al estudiante con todas sus relaciones necesarias
    $estudiante = Estudiante::with([
        'intervenciones.usuario.tipoprofesional',
        'intervenciones.detalles.falta',
        'intervenciones.detalles.medida',
        'derivaciones.user',
        'derivaciones.profesionalDerivado',
        'derivaciones.motivo',
        'derivaciones.acciones',
    ])->findOrFail($id);

    // 2. Preparamos las intervenciones de forma dinámica
    $intervenciones = $estudiante->intervenciones->map(function ($item) {
        // Detectar área (Slug). Si no existe, usamos 'Convivencia' por defecto
        $area = $item->usuario->tipoprofesional->departamento ?? 'Convivencia';

        // Extraemos todos los nombres de faltas y medidas de la colección de detalles
        $nombresFaltas = $item->detalles->map(fn($d) => $d->falta->falta ?? $d->falta->nombre ?? null)->filter()->unique()->implode(' • ');
        $nombresMedidas = $item->detalles->map(fn($d) => $d->medida->medida ?? $d->medida->nombre ?? null)->filter()->unique()->implode(' • ');

        // Configuración de etiquetas según el área profesional
        $etiqueta_1 = 'Falta(s)';
        $valor_1 = $nombresFaltas ?: 'Sin registro';
        $etiqueta_2 = 'Medida(s)';
        $valor_2 = $nombresMedidas ?: 'Sin registro';

        if ($area === 'Psicosocial') {
            $primerDetalle = $item->detalles->first();
            $etiqueta_1 = 'Motivo';
            $valor_1 = $primerDetalle->motivo ?? 'Apoyo General';
            $etiqueta_2 = 'Atención';
            $valor_2 = $primerDetalle->tipo_atencion ?? 'Individual';
        } elseif ($area === 'Pedagógico') {
            $etiqueta_1 = 'Área';
            $valor_1 = 'Apoyo Pedagógico';
            $etiqueta_2 = 'Registros';
            $valor_2 = $item->detalles->count() . ' detalle(s)';
        }

        return (object) [
            'tipo_registro' => 'Intervención: ' . $area,
            'fecha' => $item->fecha_incidente ?? $item->created_at,
            'hora' => $item->created_at->format('H:i'),
            'profesional' => ($item->usuario->name ?? 'Usuario Desconocido'),
            'detalle' => $item->descripcion ?? 'Sin observaciones.',
            'estado' => $item->estado ?? 'Finalizado',
            'etiqueta_1' => $etiqueta_1,
            'valor_1' => $valor_1,
            'etiqueta_2' => $etiqueta_2,
            'valor_2' => $valor_2,
        ];
    });

    // 3. Preparamos las derivaciones (Mantenemos tu lógica que ya funcionaba)
    $derivaciones = $estudiante->derivaciones->map(function ($item) {
        $nombreMotivo = $item->motivo->motivo ?? 'Motivo desconocido';
        return (object) [
            'tipo_registro' => 'Derivación: ' . $nombreMotivo,
            'fecha' => $item->fecha_derivacion,
            'hora' => $item->created_at->format('H:i'),
            'profesional' => ($item->profesionalDerivado->name ?? $item->user->name ?? 'Usuario Desconocido'),
            'detalle' => $item->detalle_derivacion,
            'conclusion' => $item->conclusiones ?? 'Sin conclusión registrada.',
            'acciones' => $item->acciones,
            'estado' => $item->estado,
            'etiqueta_1' => 'Motivo Derivación',
            'valor_1' => $nombreMotivo,
            'etiqueta_2' => 'Tipo de Intervención',
            'valor_2' => $item->tipo_intervencion ?? 'Intervención Psicosocial',
        ];
    });

    //dd($derivaciones);

    // 4. Unimos y ordenamos por fecha y hora descendente
    $historial = $intervenciones->concat($derivaciones)->sortByDesc(function ($item) {
        return \Carbon\Carbon::parse($item->fecha)->format('Y-m-d') . ' ' . $item->hora;
    })->values()->all();

    // 5. Generar PDF
    $pdf = Pdf::loadView('pdf.historial-estudiante', [
        'estudiante' => $estudiante,
        'historial' => $historial
    ]);

    return $pdf->stream('Historial_' . $estudiante->rut . '.pdf');
    })->name('historial.pdf');

    // Ruta para el historial del estudiante en la vista Livewire


Route::get('/estudiante/{id}/historial', HistorialComponent::class)->name('estudiante.historial');

require __DIR__.'/settings.php';
