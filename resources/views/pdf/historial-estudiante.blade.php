<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial - {{ $estudiante->nombre }} {{ $estudiante->apellido }}</title>
    <style>
        @page { margin: 1.5cm; } /* Un poco más de margen para encuadernación */
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; line-height: 1.3; font-size: 11px; }

        /* Evitar que las tarjetas se corten en la página */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 15px;
            overflow: hidden;
            break-inside: avoid; /* CRÍTICO: Evita cortes bruscos */
        }

        /* Tablas más limpias */
        .tabla-acciones { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .tabla-acciones th { background-color: #f1f5f9; padding: 4px; font-size: 8px; border: 1px solid #e2e8f0; }
        .tabla-acciones td { padding: 4px; font-size: 9px; border: 1px solid #f1f5f9; }

        /* Asegurar que el título de sección no quede huérfano al final de la página */
        .seccion-titulo {
            background: #2563eb; color: white; padding: 6px 10px; border-radius: 4px;
            font-size: 12px; font-weight: bold; margin-top: 25px; margin-bottom: 10px;
            text-transform: uppercase; break-after: avoid;
        }

        /* Ajuste para evitar desbordamiento */
        table { width: 100%; table-layout: fixed; }
        td { word-wrap: break-word; }
    </style>
</head>
<body>

    <div class="header">
        <h1>HISTORIAL ESTUDIANTIL</h1>
        <p>Fecha de reporte: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="estudiante-info">
        <table width="100%">
            <tr>
                <td><strong>Estudiante:</strong> {{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
                <td><strong>RUT:</strong> {{ $estudiante->rut }}</td>
            </tr>
            <tr>
                <td><strong>Curso:</strong> {{ $estudiante->curso->curso ?? 'N/A' }}</td>
                <td><strong>Estado:</strong> Activo</td>
            </tr>
        </table>
    </div>

    <div class="seccion-titulo">1. Intervenciones Registradas</div>
    @foreach($intervenciones as $reg)
        <div class="card">
            <div class="card-header">
                <strong>{{ $reg['fecha'] }}</strong> | <span style="color: #2563eb;">{{ $reg['via'] }}</span>
            </div>
            <div class="card-body">
                <div><strong>Profesional:</strong> {{ $reg['profesional'] }} ({{ $reg['area'] }})</div>
                @foreach($reg['detalles'] as $det)
                    <div class="sub-registro">
                        <span class="label-sub">Detalle:</span>
                        <span class="valor-sub">
                            {{ $det->motivo->motivo ?? ($det->falta->falta ?? 'N/A') }} ||
                            {{ $det->tipointervencion->tipo ?? ($det->medida->medida ?? 'N/A') }}
                        </span>
                    </div>
                @endforeach
                <div class="descripcion-box">"{{ $reg['descripcion'] }}"</div>
            </div>
            @if(count($reg['acciones'] ?? []) > 0)
                <div class="accion-titulo" style="color: #2563eb; margin-top: 15px; border-bottom: 1px solid #dbeafe; padding-bottom: 5px;">
                    Seguimiento de Intervención:
                </div>

                <div style="margin-top: 10px; margin-left: 5px;">
                    @foreach($reg['acciones'] as $accion)
                        <div style="margin-bottom: 12px; padding-left: 15px; border-left: 2px solid #3b82f6;">
                            <div style="font-size: 9px; font-weight: bold; color: #64748b;">
                                {{ \Carbon\Carbon::parse($accion->fecha)->format('d/m/Y') }}
                                <span style="color: #3b82f6; margin-left: 10px;">{{ $accion->usuario->name ?? 'N/A' }}</span>
                            </div>
                            <div style="font-size: 10px; color: #334155; margin-top: 2px;">
                                {{ $accion->descripcion }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    <div style="page-break-before: always;"></div>
    <div class="seccion-titulo">2. Derivaciones y Seguimiento</div>
    @foreach($derivaciones as $der)
        <div class="card" style="border-left: 4px solid #f97316;">
            <div class="card-header">
                <table width="100%">
                    <tr>
                        <td><strong>{{ $der['fecha'] }}</strong></td>
                        <td align="right">
                            <span class="badge">{{ $der['estado'] }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-body">
                <table width="100%">
                    <tr>
                        <td><strong>Derivado a:</strong> {{ $der['profesional_derivado'] }}</td>
                        <td align="right"><strong>Derivado por:</strong> {{ $der['profesional'] }}</td>
                    </tr>
                </table>
                <p><strong>Detalle:</strong> {{ $der['detalle'] }}</p>
                <p><strong>Conclusiones:</strong> {{ $der['conclusiones'] }}</p>

                @if(count($reg['acciones'] ?? []) > 0)
                    <div class="accion-titulo" style="color: #2563eb; margin-top: 15px; border-bottom: 1px solid #dbeafe; padding-bottom: 5px;">
                        Seguimiento de Intervención:
                    </div>

                    <div style="margin-top: 10px; margin-left: 5px;">
                        @foreach($reg['acciones'] as $accion)
                            <div style="margin-bottom: 12px; padding-left: 15px; border-left: 2px solid #3b82f6;">
                                <div style="font-size: 9px; font-weight: bold; color: #64748b;">
                                    {{ \Carbon\Carbon::parse($accion->fecha)->format('d/m/Y') }}
                                    <span style="color: #3b82f6; margin-left: 10px;">{{ $accion->usuario->name ?? 'N/A' }}</span>
                                </div>
                                <div style="font-size: 10px; color: #334155; margin-top: 2px;">
                                    {{ $accion->descripcion }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</body>
</html>
