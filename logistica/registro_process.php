<?php
session_start();
include("conexion.php");
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // === reCAPTCHA ===
    $recaptcha_secret = '6LdwqAMsAAAAAPO36k4ZTzZG1F4C5UFI0qJYm2r2'; // Reemplaza con tu secret key de Google
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $response_keys = json_decode($response, true);

    if (!$response_keys['success']) {
        echo "<script>alert('❌ Por favor verifica que no eres un robot.'); window.history.back();</script>";
        exit();
    }

    // === Datos del formulario ===
    $mail = trim($_POST['mail']);
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $password_confirm = $_POST['confirm_password'];

    // === Validar contraseñas ===
    if ($password !== $password_confirm) {
        echo "<script>alert('❌ Las contraseñas no coinciden'); window.history.back();</script>";
        exit();
    }

    // === Verificar si el empleado existe (registrado por el admin) ===
    $stmt = $conn->prepare("SELECT id_empleado FROM empleados WHERE mail = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 0) {
        echo "<script>alert('❌ No estás registrado como empleado. Contacta al administrador.'); window.history.back();</script>";
        exit();
    }

    $id_empleado = $resultado->fetch_assoc()['id_empleado'];
    $stmt->close();

    // === Verificar si ya tiene usuario ===
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE id_empleado = ?");
    $stmt->bind_param("i", $id_empleado);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>alert('❌ Ya existe un usuario registrado para este empleado'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // === Verificar si el nombre de usuario ya está usado ===
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>alert('❌ El nombre de usuario ya está registrado'); window.history.back();</script>";
        exit();
    }
    $stmt->close();

    // === Crear usuario ===
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $tipo = 0; // empleado

    $stmt = $conn->prepare("INSERT INTO usuarios (id_empleado, usuario, password, tipo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $id_empleado, $usuario, $hashedPassword, $tipo);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Usuario registrado correctamente'); window.location.href='LoginCamiones.php';</script>";
    } else {
        echo "<script>alert('❌ Error al crear el usuario: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

