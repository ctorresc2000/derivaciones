<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial - {{ $estudiante->nombre }} {{ $estudiante->apellido }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; color: #1e293b; line-height: 1.4; font-size: 11px; }

        /* Cabecera */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2563eb; padding-bottom: 10px; }
        .estudiante-info { background: #f8fafc; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #e2e8f0; }

        /* Títulos de Sección */
        .seccion-titulo {
            background: #2563eb; color: white; padding: 8px 15px;
            border-radius: 5px; font-size: 13px; font-weight: bold;
            margin-top: 30px; margin-bottom: 15px; text-transform: uppercase;
        }

        /* Tarjeta de Registro */
        .card { border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 15px; overflow: hidden; page-break-inside: avoid; }
        .card-header { background: #f1f5f9; padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .card-body { padding: 12px; }

        /* Badges */
        .badge { padding: 2px 8px; border-radius: 4px; font-size: 9px; font-weight: bold; text-transform: uppercase; }

        /* Subtabla de Detalles */
        .sub-registro {
            background: #f8fafc; border: 1px solid #edf2f7;
            padding: 8px; margin-top: 8px; border-radius: 5px;
        }
        .label-sub { font-size: 8px; color: #64748b; font-weight: bold; text-transform: uppercase; display: block; }
        .valor-sub { font-size: 10px; font-weight: bold; color: #334155; }

        .descripcion-box {
            margin-top: 10px; padding: 10px; background: #fff;
            border-left: 3px solid #cbd5e1; italic; color: #475569;
        }

        .tabla-acciones {
        width: 100%; border-collapse: collapse; margin-top: 10px;
        background-color: #ffffff; border: 1px solid #e2e8f0;
    }
    .tabla-acciones th {
        background-color: #f8fafc; text-align: left; padding: 5px;
        font-size: 8px; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;
    }
    .tabla-acciones td {
        padding: 6px 5px; font-size: 9px; border-bottom: 1px solid #f1f5f9; vertical-align: top;
    }
    .accion-titulo { font-size: 9px; font-weight: bold; color: #f97316; margin-top: 12px; margin-bottom: 4px; text-transform: uppercase; }
</style>
    </style>
</head>
<body>

    <div class="header">
        <h1>HISTORIAL </h1>{{--ACADÉMICO Y CONDUCTUAL --}}
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
                <td><strong>Estado Estudiante:</strong> Activo</td>
            </tr>
        </table>
    </div>

    <div class="seccion-titulo">1. Intervenciones Registradas</div>

    @forelse($intervenciones as $reg)
        <div class="card">
            <div class="card-header">
                <table width="100%">
                    <tr>
                        <td width="20%"><strong>{{ $reg['fecha'] }}</strong></td>
                        <td width="50%">
                            <span style="color: #2563eb; font-weight: bold;">{{ $reg['via'] }}</span>
                            {{-- <small>{{ $reg['area'] }}</small> --}}
                        </td>
                        <td width="30%" align="right">
                            {{-- <span class="badge" style="background-color: {{ $reg['bg_color'] }}; color: {{ $reg['text_color'] }};">
                                {{ $reg['estado'] }}
                            </span> --}}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-body">
                <div style="margin-bottom: 5px;"><strong>Profesional:</strong> {{ $reg['profesional'] }}</div>

                @foreach($reg['detalles'] as $det)
                    <div class="sub-registro">
                        <table width="100%">
                            <tr>
                                <td width="50%">
                                    @if($det->falta)
                                        <span class="label-sub">Falta:</span>
                                        <span class="valor-sub">{{ $det->falta->falta }}</span>
                                    @elseif($det->motivo)
                                        <span class="label-sub">Motivo:</span>
                                        <span class="valor-sub">{{ $det->motivo->motivo }}</span>
                                    @endif
                                </td>
                                <td width="50%">
                                    @if($det->medida)
                                        <span class="label-sub">Medida:</span>
                                        <span class="valor-sub">{{ $det->medida->medida }}</span>
                                    @elseif($det->tipo)
                                        <span class="label-sub">Tipo Atención:</span>
                                        <span class="valor-sub">{{ $det->tipo->tipo }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if($det->detalle)
                            <tr>
                                <td colspan="2" style="padding-top: 5px;">
                                    <span class="label-sub">Observación de Registro:</span>
                                    <span style="font-size: 10px;">{{ $det->detalle }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                @endforeach

                <div class="descripcion-box">
                    <strong>Descripción General:</strong><br>
                    <em>"{{ $reg['descripcion'] }}"</em>
                </div>
            </div>
        </div>
    @empty
        <p style="text-align: center; color: #64748b;">No hay intervenciones registradas.</p>
    @endforelse

    <div style="page-break-before: always;"></div>
    <div class="seccion-titulo">2. Derivaciones y Seguimiento de Acciones</div>

    @forelse($derivaciones as $der)
        <div class="card" style="border-left: 4px solid #f97316;">
            <div class="card-header" style="background: #fff7ed;">
                <table width="100%">
                    <tr>
                        <td width="20%"><strong>{{ $der['fecha'] }}</strong></td>
                        <td width="50%"><strong>{{ $der['motivo'] }}</strong></td>
                        <td width="30%" align="right">
                            <span class="badge" style="background-color: {{ $der['bg_color'] }}; color: {{ $der['text_color'] }};">
                                {{ $der['estado'] }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-body">
                <table width="100%">
                    <tr>
                        <td><strong>Tipo:</strong> {{ $der['tipo'] }}</td>
                        <td align="right"><strong>Derivado por:</strong> {{ $der['profesional'] }}</td>
                    </tr>
                </table>
                <div style="margin-top: 8px; font-size: 10px; color: #475569; padding: 8px; background: #fafafa; border-radius: 4px;">
                    <strong>Detalle inicial de derivación:</strong><br>
                    {{ $der['detalle'] }}
                </div>

                @if(count($der['acciones']) > 0)
                    <div class="accion-titulo">Acciones y Atenciones Realizadas:</div>
                    <table class="tabla-acciones">
                        <thead>
                            <tr>
                                <th width="15%">Fecha</th>
                                <th width="25%">Profesional</th>
                                <th width="60%">Descripción de la Atención</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($der['acciones'] as $accion)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($accion->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ $accion->usuario->name ?? 'N/A' }}</td>
                                    <td>{{ $accion->descripcion }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="margin-top: 10px; font-size: 9px; color: #94a3b8; italic;">
                        No se han registrado acciones de seguimiento para esta derivación aún.
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p style="text-align: center; color: #64748b;">No hay derivaciones registradas.</p>
    @endforelse
</body>
</html>
