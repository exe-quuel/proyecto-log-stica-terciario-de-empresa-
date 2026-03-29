<?php 

?>
<html>
<head>
<meta charset="UTF-8" />
    <title>Recuperacion de contraseña</title>
    <link rel="stylesheet" href="login.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
    <header>
    <div class="container-logo">
        <img src="imagenes/logo_empresa.png" alt="logo" style="border-radius: 50%; width: 75px; height: 75px;">
    </div>
</header>
    <form method="POST" class="formulario" action="registro_process.php">
        <h2>Recuperar Contraseña</h2>
        <div class="contenedor">
            <div class="input-contenedor">
            <i class="fa-solid fa-user"></i>
             <input type="email" name="mail" id="mail" placeholder="ejemplo@gmali.com" value=""><br>
        </div>

             <div class="input-contenedor">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="password" id="password" placeholder="Contraseña" required />
                <i class="fa-solid fa-eye" id="togglePassword"></i>
            </div>

            <div class="input-contenedor">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar Contraseña" required />
                <i class="fa-solid fa-eye" id="toggleConfirmPassword"></i>
            </div>

            </div>
            <input type="hidden" name="action" value="modificar">
              <input type="submit" value="enviar" class="button" />
            </div>

            <button type="button" class="button" id="volver" onclick="window.location='LoginCamiones.php'">Volver</button>
        </div>
    </form>
    <script>
         // Mostrar/ocultar contraseña
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.classList.toggle('fa-eye-slash');
        });

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirm_password');
        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            toggleConfirmPassword.classList.toggle('fa-eye-slash');
        });     
</script>
</body>
</html>
