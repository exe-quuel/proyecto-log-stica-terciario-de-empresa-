<?php
session_start();

// Verifica que el usuario esté logueado y sea ADMIN
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !==1) {
    header("Location: LoginCamiones.php");
    exit();
}

include("conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Administrador</title>
    <link rel="stylesheet" href="Estilos3.css" />
    <script src="js/jquery-3.7.1.min.js"></script>
</head>
<body>

<header class="header">
  <a href="admin.php" class="logo" style="text-decoration: none; color: inherit;">
    <img src="logoEM.jpg" alt="Logo" />
    <h2>Administrador: <span style="color: #1a1aff;"><?= $_SESSION['user'] ?></span></h2>
  </a>

  <div class="menu-toggle" id="menu-toggle">&#9776;</div>
  <ul class="nav-links">

    <!-- Ícono de notificación -->
    <li><?php include("notificacion.php"); ?></li>

    <!-- Opciones del administrador -->
    <li class="dropdown-usuario">
      <span class="usuario-toggle">⚙️ Panel ▾</span>
      <ul class="submenu-usuario">
        <li><a href="#" id="btn-gestionar-camiones">Gestionar Camiones</a></li>
        <li><a href="#" id="btn-viajes">Viajes</a></li>
        <li><a href="#" id="btn-entregas">Verificar Entregas</a></li>
        <li><a href="#" id="btn-estados">Estados del Personal</a></li>
        <li><a href="#" id="btn-reportes">Reportes</a></li>
        <li><a href="logout.php">Cerrar sesión</a></li>
      </ul>
    </li>
  </ul>
</header>

<div id="contenido-seccion" class="contenedor"></div>

<script>
$(document).ready(function() {
    $("#btn-gestionar-camiones").click(function(e) {
        e.preventDefault();
        $("#contenido-seccion").load("camiones.php");
    });

    $("#btn-viajes").click(function(e) {
        e.preventDefault();
        $("#contenido-seccion").load("viajes_admin.php");
    });

    $("#btn-entregas").click(function(e) {
        e.preventDefault();
        $("#contenido-seccion").load("verificar_entregas.php");
    });

    $("#btn-estados").click(function(e) {
        e.preventDefault();
        $("#contenido-seccion").load("estado_personal_admin.php");
    });

    $("#btn-reportes").click(function(e) {
        e.preventDefault();
        $("#contenido-seccion").load("reportes.php");
    });
});
</script>

</body>
</html>
