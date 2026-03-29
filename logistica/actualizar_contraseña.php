<?php
include("conexion.php");

$id_usuario = $_POST['id_usuario'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);

// Actualiza la contraseña
$sql = "UPDATE usuarios SET password = ? WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $password, $id_usuario);
$stmt->execute();

if ($stmt->execute()) {
    // Borra el token
    $conn->query("DELETE FROM password_resets WHERE id_usuario = $id_usuario");
  // Mostrar mensaje y redirigir
    echo "<script>
            alert('Contraseña actualizada correctamente. Ya podés iniciar sesión.');
            window.location.href = 'LoginCamiones.php';
          </script>";
} else {
    echo "<script>
            alert('Ocurrió un error al actualizar la contraseña. Intenta nuevamente.');
            window.history.back();
          </script>";
}

$stmt->close();
$conn->close();
?>