<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Registro de Empleado</title>
  <link rel="stylesheet" href="login.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
  <header>
    <div class="container-logo">
      <img src="imagenes/logo_empresa.png" alt="logo" style="border-radius: 50%; width: 75px; height: 75px;">
    </div>
  </header>

  <form id="formRegistro" class="formulario" action="registro_process.php" method="POST">
    <h1>Registro de Usuario</h1>
    <div class="contenedor">
        <!-- Usuario -->
      <div class="input-contenedor">
        <i class="fa-solid fa-user"></i>
        <input type="text" name="usuario" placeholder="Nombre de usuario" required />
      </div>

      <!-- Correo -->
      <div class="input-contenedor">
        <i class="fa-solid fa-envelope"></i>
        <input type="email" name="mail" placeholder="Correo electrónico" required />
      </div>

      <!-- Contraseña -->
      <div class="input-contenedor">
        <i class="fa-solid fa-key"></i>
        <input type="password" name="password" id="password" placeholder="Contraseña" required />
        <i class="fa-solid fa-eye" id="togglePassword"></i>
      </div>

      <!-- Confirmar contraseña -->
      <div class="input-contenedor">
        <i class="fa-solid fa-key"></i>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar contraseña" required />
        <i class="fa-solid fa-eye" id="toggleConfirmPassword"></i>
      </div>

      <!-- reCAPTCHA -->
      <div style="margin:10px 0; text-align:center;">
        <div class="g-recaptcha" data-sitekey="6LdwqAMsAAAAAC1OZhKj6hNjDZo25-FkAmZY1omX"></div>
      </div>

      <div style="text-align:center; margin: 10px 0;">
        ¿Ya tienes una cuenta? 
        <a href="LoginCamiones.php" style="color:#00aaaa; text-decoration:none;">Inicia sesión</a>
      </div>

      <input type="hidden" name="action" value="crear">
      <input type="submit" value="Registrar" class="button" />
    </div>
  </form>

  <script>
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
