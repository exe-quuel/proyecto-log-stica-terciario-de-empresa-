<?php
include("conexion.php");

$token = $_GET['token'] ?? '';

$sql = "SELECT id_usuario, expiracion FROM password_resets WHERE token = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Token invalido o expirado.";
    exit;
}

$data = $result->fetch_assoc();
if (strtotime($data['expiracion']) < time()) {
    echo "El enlace ha expirado.";
    exit;
}

$id_usuario = $data['id_usuario'];
?>

<html>
<head>
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<header>
    <div class="container-logo">
        <img src="imagenes/logo_empresa.png" alt="logo" style="border-radius: 50%; width: 80px; height: 70px;">
    </div>
</header>

<form id="formRestablecer" class="formulario" method="POST" action="actualizar_contraseña.php">
    <h1>Restablecer Contraseña</h1>
    <div class="contenedor">
        <input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>">

        <!-- Nueva contraseña -->
        <div class="input-contenedor">
            <i class="fa-solid fa-key"></i>
            <input type="password" name="password" id="password" placeholder="Nueva contraseña" required />
            <i class="fa-solid fa-eye" id="togglePassword"></i>
        </div>
        <input type="submit" value="Cambiar contraseña" class="button" />
    </div>
</form>

<script>
    // Mostrar/ocultar nueva contraseña
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', () => {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        togglePassword.classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html>
