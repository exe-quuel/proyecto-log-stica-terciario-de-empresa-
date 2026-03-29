<?php
session_start();
// Verifica que haya un usuario logueado y que sea tipo EMPLEADO
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 0) {
    // Si no cumple, lo redirige al login
    header("Location: no_autorizado.php");
    exit();
}

// Incluye el archivo de conexión a la base de datos
include("conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Camionero</title>

    <!-- Enlace al CSS personalizado -->
    <link rel="stylesheet" href="Estilos3.css" />

    <!-- jQuery para manejar los botones sin recargar la página -->
    <script src="js/jquery-3.7.1.min.js"></script>
</head>
<body>

 
<header class="header">
  <div class="logo">
                <img src="imagenes/logo_usu.png" alt="Logo"style="border-radius: 50%;">
                <h2>Bienvenido <span style="color: #50b892ff;"><?= $_SESSION['user'] ?></span></h2>
            </div>

  <!-- Ícono de menú hamburguesa para responsive -->
  <ul class="nav-links">

    <!-- Ítem de notificación cargado desde otro archivo -->
    <li style="position: relative;">
      <?php include("notificacion.php"); ?>
    </li>

    <!-- Dropdown con opciones del usuario -->
    <li class="dropdown-usuario">
      <span class="usuario-toggle">
         Configuracion▾
      </span>

      <!-- Submenú del usuario -->
      <ul class="submenu-usuario">
        <li><a href="viajes.php" id="btn-viaje">Viaje</a></li>
        <li><a href="estado_personal.php" id="btn-estado">Estado Personal</a></li>
        <li><a href="logout.php">Cerrar sesión</a></li>
      </ul>
    </li>
  </ul>
</header>

 <!-- Contenedor donde se cargarán los contenidos  -->
<div id="contenido-seccion" class="contenedor"></div>
<script>
$(document).ready(function() {
$("#contenido-seccion").load("viajes.php");
    // Cargar viajes.php cuando se hace clic en "Viaje"
    $("#btn-viaje").click(function(e) {
        e.preventDefault();
        $("#contenido-seccion").load("viajes.php");
    });

    // Cargar estado_personal.php cuando se hace clic en "Estado Personal"
    $("#btn-estado").click(function(e) {
        e.preventDefault();
        $("#contenido-seccion").load("estado_personal.php");
    });
});
</script>


</body>
</html>
