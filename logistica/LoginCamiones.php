<?php
// Iniciar la sesión para manejar usuarios logueados
session_start();

//conexion con la base de datos 
include("conexion.php");

// Variable para almacenar mensajes de error
$error = "";

// Verificar si el formulario fue enviado con método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Obtener los datos ingresados por el usuario
    $user = $_POST['user'];
    $password = $_POST['password'];

    // Preparar la consulta para evitar inyecciones SQL
    $sql = "SELECT id_usuario, usuario, password, tipo FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);

    // Verificar si la preparación fue exitosa
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Asignar el parámetro de usuario
    $stmt->bind_param("s", $user);
    $stmt->execute();

    // Obtener los resultados
    $resultado = $stmt->get_result();

    // Verificar si se encontró un usuario con ese nombre
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Validar contraseña ingresada (comparación simple, sin hash)
        if (password_verify($password, $usuario["password"])) {
            // Guardar datos del usuario en la sesión
            $_SESSION['id_usuario'] = $usuario["id_usuario"];
            $_SESSION['user'] = $usuario["usuario"];
            $_SESSION['tipo'] = $usuario["tipo"];

            // Redirigir según el tipo de usuario
            if ((int)$usuario["tipo"] === 1) {
                header("Location: admin_index.php");
            } else {
                header("Location: empleado.php");
            }
            exit(); // Detener ejecución del script después de redirigir
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }

    // Cerrar la consulta y conexión
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Hoja de estilos propia -->
    <link rel="stylesheet" href="login.css">

    <!-- Iconos de Font Awesome (correo, candado, etc.) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
</head>
<body>

    <!-- Encabezado con logo -->
    <header>
    <div class="container-logo">
        <img src="imagenes/logo_empresa.png" alt="logo" style="border-radius: 50%; width: 75px; height: 75px;">
    </div>
</header>s

    <!-- Formulario de inicio de sesión -->
    <form class="formulario" action="LoginCamiones.php" method="post" autocomplete="off">
        <h1>Login</h1>
        <div class="contenedor">

            <!-- Campo de usuario -->
            <div class="input-contenedor">
                <i class="fa-solid fa-envelope"></i>
                <input type="text" name="user" placeholder="Usuario" required />
            </div>

            <!-- Campo de contraseña -->
            <div class="input-contenedor">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="password" placeholder="Contraseña" required />
            </div>

            <!-- Mostrar mensaje de error si hay -->
            <?php if ($error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <!-- Botón para enviar el formulario -->
            <input type="submit" value="Login" class="button" />

            <div style="display:flex; justify-content:space-between; padding:8px;">
                <a href="registro.php" style="color:white; text-decoration:none;">Registrarse</a>
                <a href="olvido_contraseña.html" style="color:white; text-decoration:none;">Olvidaste tu contraseña?</a>
            </div>
        </div>
         
    </form>

</body>
</html>
