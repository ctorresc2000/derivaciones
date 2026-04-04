<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial - {{ $estudiante->nombre }} {{ $estudiante->apellido }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #333; margin: -20px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; color: #1e293b; }

        .seccion-titulo {
            background-color: #f1f5f9;
            padding: 8px;
            font-weight: bold;
            border-left: 4px solid #2563eb;
            margin-bottom: 15px;
            margin-top: 20px;
            font-size: 12px;
            text-transform: uppercase;
        }

        /* Tabla de información técnica (Antecedentes e Historial) */
        .tabla-info-fija {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 10px;
        }

        .tabla-info-fija td {
            vertical-align: top;
            padding: 3px 5px;
            word-wrap: break-word;
            border: none !important;
        }

        .label {
            font-weight: bold;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 9px;
            width: 35%; /* Ancho fijo para las etiquetas */
        }

        /* Estructura de cada registro */
        .historial-item {
            border: 1px solid #cbd5e1;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .historial-header {
            background-color: #1e293b;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 11px;
        }

        .historial-body { padding: 12px; }

        .texto-detalle {
            background-color: #f8fafc;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            font-style: italic;
            margin-top: 5px;
            white-space: pre-line;
            color: #334155;
        }

        .badge {
            float: right;
            background-color: #3b82f6;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Historial de Estudiante</h1>
        <p>Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- SECCIÓN ANTECEDENTES (CORREGIDA) --}}
    <div class="seccion-titulo">Antecedentes del Estudiante</div>

    <table class="tabla-info-fija">
        <tr>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td class="label">Nombre:</td>
                        <td>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</td>
                    </tr>
                    <tr>
                        <td class="label">RUT:</td>
                        <td>{{ $estudiante->rut }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table width="100%">
                    <tr>
                        <td class="label">Nombre Social:</td>
                        <td>{{ $estudiante->social ?: 'No registrado' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Curso:</td>
                        <td>{{ $estudiante->curso->nombre ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="seccion-titulo">Registros de Intervención y Seguimiento</div>

    @if(count($historial) > 0)
        @foreach($historial as $item)
            <div class="historial-item">
                <div class="historial-header">
                    {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }} - {{ $item->tipo_registro }}
                    <span class="badge">{{ $item->estado }}</span>
                </div>

                <div class="historial-body">
                    <table class="tabla-info-fija">
                        <tr>
                            <td width="50%">
                                <table width="100%">
                                    <tr>
                                        <td class="label">Profesional:</td>
                                        <td>{{ $item->profesional }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Hora:</td>
                                        <td>{{ $item->hora }} hrs</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="50%">
                                <table width="100%">
                                    <tr>
                                        <td class="label">{{ $item->etiqueta_1 }}:</td>
                                        <td style="color: #475569;">{!! $item->valor_1 !!}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">{{ $item->etiqueta_2 }}:</td>
                                        <td style="color: #475569;">{!! $item->valor_2 !!}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="label" style="margin-top: 10px; margin-bottom: 5px;">Detalle / Observaciones:</div>
                    <div class="texto-detalle">
                        {{ $item->detalle }}
                    </div>
                    {{-- Sección de Entrevistas / Acciones de Derivación --}}
                    @if(isset($item->acciones) && count($item->acciones) > 0)
                        <div class="label" style="margin-top: 15px; margin-bottom: 5px; color: #2563eb;">Entrevistas y Acciones Realizadas:</div>

                        @foreach($item->acciones as $accion)
                            <div style="margin-bottom: 10px; padding: 8px; border: 1px solid #e2e8f0; border-left: 3px solid #2563eb; background-color: #ffffff;">
                                <table width="100%" style="border: none; margin-bottom: 0;">
                                    <tr>
                                        <td style="font-size: 10px; font-weight: bold; width: 20%;">
                                            {{ \Carbon\Carbon::parse($accion->fecha)->format('d/m/Y') }}
                                        </td>
                                        <td style="font-size: 10px; color: #64748b;">
                                            Acción: {{ $accion->nombre_accion ?? 'Entrevista/Seguimiento' }}
                                        </td>
                                    </tr>
                                </table>
                                <div style="font-size: 10px; margin-top: 4px; line-height: 1.2;">
                                    <strong>Descripción:</strong> {{ $accion->descripcion }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                    @if(!empty($item->conclusion))
                        <div class="label" style="margin-top: 10px; margin-bottom: 5px;">Conclusiones de la Derivación:</div>
                        <div class="texto-detalle" style="border-left: 4px solid #10b981; background-color: #f0fdf4;">
                            {{ $item->conclusion }}
                        </div>
                    @endif
                </div> {{-- Cierre de historial-body --}}
            </div> {{-- Cierre de historial-item --}}
        @endforeach
    @else
        <p style="text-align: center; color: #64748b; margin-top: 30px;">No existen registros en el historial.</p>
    @endif

</body>
</html>
