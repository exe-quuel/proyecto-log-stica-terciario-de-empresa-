<?php
$host = "localhost";
$usuario = "root";
$clave = ""; // si tenés contraseña, ponela acá
$bd = "logistica";

// Crear conexión
$conn = new mysqli($host, $usuario, $clave, $bd);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
