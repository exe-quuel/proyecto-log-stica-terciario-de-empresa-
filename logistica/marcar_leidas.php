<?php
session_start();

// Incluye la conexión a la base de datos
include("conexion.php");

// Verifica si hay un usuario logueado
if (!isset($_SESSION['id_usuario'])) {
    exit(); // Si no hay sesión, no hace nada
}

// Obtener el ID del usuario desde la sesión
$idUsuario = $_SESSION['id_usuario'];

// Marcar como leídas todas las notificaciones de ese usuario
$sql = "UPDATE notificaciones SET leida = 1 WHERE id_usuario = $idUsuario";
mysqli_query($conn, $sql);

// Redirigir a la página anterior (de donde vino el usuario)
header("Location: empleado.php");
exit();
?>
