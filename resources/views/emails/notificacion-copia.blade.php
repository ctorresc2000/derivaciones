<!DOCTYPE html>
<html>
<head>
    <title>Notificación de Convivencia Escolar</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
        <h2 style="color: #2563eb;">Nueva {{ $tipoRegistro }}</h2>

        <p>Estimado/a Funcionario/a,</p>
        <p>Se le ha enviado una copia informativa sobre el siguiente registro:</p>

        <div style="background-color: #f8fafc; padding: 15px; border-left: 4px solid #2563eb; margin: 20px 0;">
            <p><strong>Estudiante:</strong> {{ $estudiante->nombre }} {{ $estudiante->apellido }}</p>
            <p><strong>Fecha:</strong> {{ now()->format('d-m-Y H:i') }}</p>

            {{-- Si es una Intervención, mostramos detalles específicos --}}
            @if($tipoRegistro == 'Intervención Espontánea')
                @php $detalle = collect($registro->detalles)->first(); @endphp
                <p><strong>Tipo de Falta:</strong> {{ data_get($detalle, 'falta.nombre') ?? 'No especificada' }}</p>
                <p><strong>Medida Aplicada:</strong> {{ data_get($detalle, 'medida.nombre') ?? 'No especificada' }}</p>
                <p><strong>Observación:</strong> {{ data_get($detalle, 'descripcion') ?? $registro->descripcion }}</p>
            @else
                {{-- Si es Derivación, mostramos lo básico --}}
                <p><strong>Motivo/Antecedentes:</strong> {{ $registro->previos_derivacion ?? 'Ver en sistema' }}</p>
            @endif
        </div>

        <p>Para más detalles y seguimiento, por favor ingrese al sistema.</p>
        <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
        <p style="font-size: 12px; color: #777;">Sistema de Convivencia LTSM - Enviado por: {{ auth()->user()->name }}</p>
    </div>
</body>
</html>
