<!DOCTYPE html>
<html>
<head>
    <style>
        .tabla-info { width: 100%; border-collapse: collapse; font-family: Arial, sans-serif; }
        .tabla-info td, .tabla-info th { border: 1px solid #e2e8f0; padding: 12px; text-align: left; }
        .header-tabla { background-color: #2563eb; color: white; font-weight: bold; }
        .label { background-color: #f8fafc; font-weight: bold; width: 30%; color: #475569; }
        .observaciones { background-color: #fffbeb; border: 1px solid #fef3c7; padding: 15px; margin-top: 10px; font-style: italic; }
    </style>
</head>
<body>
    <h2>Notificación de Registro: {{ $tipoRegistro }}</h2>
    <p>Se ha registrado una nueva actividad para el estudiante: <strong>{{ $estudiante->nombre }} {{ $estudiante->apellido }}</strong></p>

    <table style="width: 100%; border-collapse: collapse; font-family: sans-serif;">
        <thead>
            <tr style="background-color: #2563eb; color: white;">
                {{-- Cambiamos los encabezados dinámicamente --}}
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">
                    {{ str_contains($tipoRegistro, 'Psicosocial') ? 'Motivo' : 'Falta' }}
                </th>
                <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">
                    {{ str_contains($tipoRegistro, 'Psicosocial') ? 'Tipo de Atención' : 'Medida' }}
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($datosVista as $item)
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        {{-- Mostramos nombre_falta o nombre_motivo según exista en el array --}}
                        {{ $item['falta_nombre'] ?? $item['motivo_nombre'] ?? 'N/A' }}
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">
                        {{-- Mostramos nombre_medida o nombre_tipo --}}
                        {{ $item['medida_nombre'] ?? $item['tipo_nombre'] ?? 'N/A' }}

                        @if(!empty($item['detalle']))
                            <div style="font-size: 0.85em; color: #666; margin-top: 4px;">
                                <strong>Detalle:</strong> {{ $item['detalle'] }}
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Observaciones / Descripción:</h3>
    <div class="observaciones">
        {{-- Aquí mostramos el detalle principal --}}
        {{ $registro->descripcion ?? $registro->descripcion_derivacion ?? 'Sin observaciones detalladas.' }}
    </div>

    <p style="font-size: 12px; color: #64748b; margin-top: 20px;">
        Este es un correo automático, por favor no responder.
    </p>
</body>
</html>
