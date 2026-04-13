<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Entrevista</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #4a5568; padding-bottom: 10px; margin-bottom: 20px; }
        .section-title { background: #edf2f7; padding: 8px; font-weight: bold; text-transform: uppercase; font-size: 12px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 8px; border-bottom: 1px solid #eee; font-size: 13px; }
        .label { font-weight: bold; color: #4a5568; width: 30%; }
        .content { white-space: pre-line; margin-top: 10px; font-size: 13px; }
        .signature-container { margin-top: 50px; text-align: center; }
        .signature-box { border: 1px dashed #ccc; width: 300px; height: 150px; margin: 10px auto; padding: 10px; }
        .signature-img { max-width: 100%; max-height: 100%; }

        .verification-box {
            margin-top: 30px;
            border: 1px solid #d1d5db;
            border-radius: 5px;
            padding: 15px;
            background-color: #f9fafb;
        }
        .v-title {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .v-item {
            font-size: 12px;
            margin-bottom: 4px;
        }
        .v-label {
            font-weight: bold;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>REPORTE DE ENTREVISTA</h2>
        <p>Institución Educativa - Registro Académico</p>
    </div>

    <div class="section-title">Información General</div>
    <table>
        <tr>
            <td class="label">Estudiante:</td>
            <td>{{ $entrevista->estudiante->nombre }} {{ $entrevista->estudiante->apellido }}</td>
            <td class="label">Curso:</td>
            <td>{{ $entrevista->curso->curso }}</td>
        </tr>
        <tr>
            <td class="label">Fecha:</td>
            <td>{{ \Carbon\Carbon::parse($entrevista->fecha)->format('d/m/Y') }}</td>
            <td class="label">Entrevistador:</td>
            <td>{{ $entrevista->user->name }}</td>
        </tr>
        <tr>
            <td class="label">Tipo Entrevistado:</td>
            <td>{{ $entrevista->es_apoderado ? 'Apoderado (' . $entrevista->nombre_apoderado . ')' : 'Estudiante' }}</td>
            <td class="label">Motivo:</td>
            <td>{{ $entrevista->motivo }}</td>
        </tr>
    </table>

    <div class="section-title">Detalle de la Entrevista</div>
    <div class="content">
        {{ $entrevista->detalle }}
    </div>

    <div class="verification-box">
        <div class="v-title">Certificado de Autenticidad Digital</div>
        @if($entrevista->otp_verified_at)
            <div class="v-item">
                <span class="v-label">Estado:</span> CONFIRMADO VÍA OTP (One-Time Password)
            </div>
            <div class="v-item">
                <span class="v-label">Correo Validado:</span> {{ $entrevista->otp_email }}
            </div>
            <div class="v-item">
                <span class="v-label">Código de Seguridad:</span> {{ $entrevista->otp_codigo }}
            </div>
            <div class="v-item">
                <span class="v-label">Sello de Tiempo:</span> {{ $entrevista->otp_verified_at->format('d/m/Y H:i:s') }}
            </div>
            <p style="font-size: 9px; color: #9ca3af; margin-top: 10px;">
                Este documento cuenta con validación electrónica mediante envío de código único al correo proporcionado por el entrevistado.
            </p>
        @else
            <div class="v-item italic" style="color: #9ca3af;">
                Esta entrevista no cuenta con registro de validación electrónica OTP.
            </div>
        @endif
    </div>

        <div class="signature-container">
            <p><strong>Firma del Entrevistado</strong></p>
            <div class="signature-box">
                @if($entrevista->firma)
                    <img src="{{ $entrevista->firma }}" class="signature-img">
                @else
                    <p style="color: #ccc; margin-top: 60px;">Sin firma registrada</p>
                @endif
            </div>
            <p style="font-size: 10px;">Documento generado electrónicamente el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
    </body>
</html>
