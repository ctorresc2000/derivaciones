<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Derivación</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 20px; color: #333; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-top: 5px solid #4f46e5; }
        h2 { color: #1e293b; margin-top: 0; }
        p { line-height: 1.6; font-size: 15px; }
        .data-box { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 6px; margin-top: 20px; }
        .data-box ul { list-style: none; padding: 0; margin: 0; }
        .data-box li { margin-bottom: 10px; }
        .btn { display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: bold; margin-top: 25px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hola, tienes una nueva derivación asignada</h2>
        <p>Se te ha derivado a un nuevo estudiante en la plataforma escolar. Por favor, revisa el sistema para gestionar esta solicitud a la brevedad posible.</p>

        <div class="data-box">
            <ul>
                <li><strong>Estudiante:</strong> {{ $estudiante->nombre }} {{ $estudiante->apellido }}</li>
                <li><strong>RUT:</strong> {{ $estudiante->rut ?? 'No registrado' }}</li>
                <li><strong>Curso:</strong> {{ $estudiante->curso ? $estudiante->curso->curso : 'Sin curso' }}</li>
            </ul>
        </div>

        <a href="{{ route('estudiantesderivados') }}" class="btn">Ingresar al Sistema</a>

        <p style="font-size: 12px; color: #64748b; margin-top: 30px;">Este es un mensaje automático del Sistema de Derivaciones Escolares. Por favor, no respondas a este correo.</p>
    </div>
</body>
</html>
