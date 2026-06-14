<!DOCTYPE html>
<html>
<head>
    <style>
        .card { border: 1px solid #e2e8f0; padding: 20px; font-family: sans-serif; border-radius: 8px; }
        .code { font-size: 24px; font-weight: bold; color: #2563eb; letter-spacing: 2px; }
        .fondo-celeste {
            background-color: #e0f2fe; /* Celeste claro */
            padding: 15px;
            border-radius: 6px;
            color: #0c4a6e; /* Azul oscuro para que el texto resalte */
            margin-bottom: 20px;
            border: 1px solid #bae6fd; /* Un borde celeste un poco más oscuro */
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Código de Validación</h2>
        <p>Se ha solicitado un código para firmar una entrevista escolar.</p><br><br>
        <p>Detalle de Entrevista</p><br>
        <div class="fondo-celeste">"{{$detalle}}"</div>
        <p>Su código de autorización es:</p>
        <div class="code">{{ $codigo }}</div>
        <p>Este código expirará en 30 minutos.</p>
    </div>
</body>
</html>
