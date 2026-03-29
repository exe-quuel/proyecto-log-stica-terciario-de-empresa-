<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>No autorizado</title>
    <link rel="stylesheet" href="Estilos3.css">
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        .contenedor {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .card {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 500px;
        }
        h1 {
            color: #cc0000;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 30px;
        }
        .btn-volver {
            background-color: #1a1aff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .btn-volver:hover {
            background-color: #0000cc;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="card">
            <h1>🚫 No Autorizado</h1>
            <p>Ingresó sin haber pasado por el inicio de sesión.<br>
            Por favor, vuelva al login para ingresar correctamente.</p>
            <button class="btn-volver" onclick="window.location.href='loginCamiones.php'">Volver al Login</button>
        </div>
    </div>
</body>
</html>