<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial - {{ $estudiante->nombre }} {{ $estudiante->apellido }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: -20px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e293b; }
        .header p { margin: 5px 0 0 0; font-size: 12px; color: #64748b; }

        .seccion-titulo { background-color: #f1f5f9; padding: 8px; font-weight: bold; border-left: 4px solid #3b82f6; margin-bottom: 10px; margin-top: 20px; font-size: 14px;}

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background-color: #f8fafc; font-size: 11px; text-transform: uppercase; color: #475569; width: 25%;}
        td { font-size: 12px; }

        .historial-item { border: 1px solid #cbd5e1; border-radius: 4px; margin-bottom: 15px; page-break-inside: avoid; }
        .historial-header { background-color: #f8fafc; padding: 8px; border-bottom: 1px solid #cbd5e1; font-weight: bold; font-size: 13px; }
        .historial-body { padding: 10px; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; background-color: #e2e8f0; color: #475569; float: right;}

        .detalles-grid { width: 100%; margin-bottom: 10px; border: none; }
        .detalles-grid td { border: none; padding: 2px 5px; font-size: 11px;}
        .label { font-weight: bold; color: #64748b; }

        .texto-detalle { background-color: #f8fafc; padding: 8px; border: 1px solid #e2e8f0; border-radius: 4px; font-style: italic;}
    </style>
</head>
<body>

    <div class="header">
        <h1>Historial de Estudiante</h1>
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="seccion-titulo">Antecedentes del Estudiante</div>
    <table>
        <tr>
            <th>Nombre Completo</th>
            <td>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
        </tr>
        <tr>
            <th>Nombre Social</th>
            <td>{{ $estudiante->social ?? 'No registrado' }}</td>
        </tr>
        <tr>
            <th>RUT</th>
            <td>{{ $estudiante->rut }}</td>
        </tr>
        <tr>
            <th>Curso Actual</th>
            <td>{{ $estudiante->curso->nombre_curso ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="seccion-titulo">Registros de Convivencia y Psicosocial</div>

    @if(count($historial) > 0)
        @foreach($historial as $item)
            <div class="historial-item">
                <div class="historial-header">
                    {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }} - {{ $item->tipo_registro }}
                    <span class="badge">{{ $item->estado }}</span>
                </div>
                <div class="historial-body">
                    <table class="detalles-grid">
                        <tr>
                            <td class="label" width="20%">Profesional:</td>
                            <td width="30%">{{ $item->profesional }}</td>
                            <td class="label" width="20%">{{ $item->etiqueta_1 }}:</td>
                            <td width="30%">{{ $item->valor_1 }}</td>
                        </tr>
                        <tr>
                            <td class="label">Hora:</td>
                            <td>{{ $item->hora }} hrs</td>
                            <td class="label">{{ $item->etiqueta_2 }}:</td>
                            <td>{{ $item->valor_2 }}</td>
                        </tr>
                    </table>

                    <div class="label" style="margin-bottom: 5px;">Detalle / Observaciones:</div>
                    <div class="texto-detalle">
                        {{ $item->detalle }}
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <p style="text-align: center; color: #64748b; margin-top: 30px;">No existen registros en el historial del estudiante.</p>
    @endif

</body>
</html>
