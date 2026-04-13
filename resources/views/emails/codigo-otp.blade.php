<!DOCTYPE html>
<html>
<head>
    <style>
        .card { border: 1px solid #e2e8f0; padding: 20px; font-family: sans-serif; border-radius: 8px; }
        .code { font-size: 24px; font-weight: bold; color: #2563eb; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Código de Validación</h2>
        <p>Se ha solicitado un código para firmar una entrevista escolar.</p>
        <p>Su código de autorización es:</p>
        <div class="code">{{ $codigo }}</div>
        <p>Este código expirará en 15 minutos.</p>
    </div>
</body>
</html>
