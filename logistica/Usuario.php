<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Conexión a la base de datos (la cambie con el archivo conexion.php ahora tenes que escribir $conn en logar de $conexion)
    include("conexion.php");

    // Recibir datos del formulario
    $usuario = $_POST['usuario'];
    $gmail = $_POST['gmail'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $tipo = $_POST['tipo']; // 0=Empleado, 1=Administrador

    // Validar contraseñas
    if ($password !== $confirm_password) {
        //exit("Las contraseñas no coinciden. <a href='registro.php'>Volver</a>");
        //este es para que cusndo no coinsidan las contraseñas puedas volver o ir directo al login
        echo "<script> if(confirm('La contraseña no coincide ¿Desesa intentarlo de nuevo?')) 
            {
                window.location.href = 'registro.php';
            }
            else 
            {
                window.location.href = 'LoginCamiones.php';
            }</script>";
    }

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows > 0) {
        exit("El usuario ya existe. <a href='registro.php'>Volver</a>");
    }

    // Crear empleado con datos mínimos
    $apellido_nombre = $usuario;
    $telefono = "0000000000";
    $direccion = "-";
    $nacionalidad = "-";
    $rol = 0; // peón por defecto
    $numero_documento = "00000000";
    $tipo_documento = 0;

    //en lugar de crear, busca al empleado y si existe relacionas el id de el empleado en el usuario nuevo,
    //si no indicas que el empleado no existe hable con el administrador parar crearlo 
    $stmt = $conn->prepare("INSERT INTO empleados (apellido_nombre, telefono, mail, direccion, nacionalidad, rol, numero_documento, tipo_documento) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiis", $apellido_nombre, $telefono, $gmail, $direccion, $nacionalidad, $rol, $numero_documento, $tipo_documento);
    if (!$stmt->execute()) {
        exit("Error al crear empleado: " . $conn->error);
    }

    $id_empleado = $stmt->insert_id;

    // Crear usuario con contraseña hash
    // verifica si la contraseña se mantiente igual que la que puso el usuario despues de encriptarla 
    // si no, la estas enviando encriptada a la base de datos y se gaurda asi y nesesitas colocar la 
    // contraceña encriptada para entrar 
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (id_empleado, usuario, password, tipo) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $id_empleado, $usuario, $hashedPassword, $tipo);

    if ($stmt->execute()) {
        echo "Usuario registrado correctamente.<script>window.location.href = 'LoginCamiones.php';</script> ";
        
    } else {
        echo "Error al crear usuario: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
