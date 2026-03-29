<?php
function notificar($idUsuario, $mensaje, $conn) {
    $mensaje = mysqli_real_escape_string($conn, $mensaje);
    $sql = "INSERT INTO notificaciones (id_usuario, mensaje) VALUES ($idUsuario, '$mensaje')";
    mysqli_query($conn, $sql);
}
