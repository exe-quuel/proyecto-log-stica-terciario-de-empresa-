<?php
session_start();
// Verifica que el usuario esté logueado y sea EMPLEADO
if (!isset($_SESSION['id_usuario'])) 
{
    header("Location: no_autorizado.php");
    exit();
}
include("conexion.php"); // Conexión a la base de datos
// Obtener el ID del usuario desde la sesión
$idUsuario = $_SESSION['id_usuario'];
$tipoUsuario = $_SESSION['tipo'];
$pagina_volver = ($tipoUsuario == 0) ? 'empleado.php' : 'admin_index.php';
// Buscar el ID_EMPLEADO relacionado con el ID_USUARIO
$query = "SELECT ID_EMPLEADO FROM usuarios WHERE ID_USUARIO = $idUsuario";
$res = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($res);
if (!$row) 
{
    echo "Empleado no encontrado.";
    exit();
}
$idEmpleado = $row['ID_EMPLEADO'];
// Si se envió el formulario para actualizar los datos personales
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['guardar_personal'])) 
{
    // Limpiar los datos recibidos
    $apellido_nombre = mysqli_real_escape_string($conn, $_POST['apellido_nombre']);
    $telefono = mysqli_real_escape_string($conn, $_POST['telefono']);
    $mail = mysqli_real_escape_string($conn, $_POST['mail']);
    $direccion = mysqli_real_escape_string($conn, $_POST['direccion']);
    // Consulta para actualizar los datos en la tabla empleados
    $update = "UPDATE empleados 
               SET apellido_nombre='$apellido_nombre', TELEFONO = '$telefono',
                   MAIL = '$mail', DIRECCION = '$direccion'
               WHERE ID_EMPLEADO = $idEmpleado";
    // Ejecutar la actualización
    if (!mysqli_query($conn, $update)) 
    {
        die(" Error al actualizar: " . mysqli_error($conn));
    }
    header("Location: $pagina_volver");
    exit();
}

// Obtener los datos personales actualizados desde la base
$queryEmp = "SELECT * FROM empleados WHERE ID_EMPLEADO = $idEmpleado";
$resEmp = mysqli_query($conn, $queryEmp);
$empleado = mysqli_fetch_assoc($resEmp);

?>
<link rel="stylesheet" href="Estilos3.css"/>
<!-- Script para mostrar u ocultar el formulario -->
<script>
function activarEdicion() {
    document.getElementById("vista-personal").style.display = "none";
    document.getElementById("form-personal").style.display = "block";
}

function cancelarEdicion() {
    document.getElementById("form-personal").style.display = "none";
    document.getElementById("vista-personal").style.display = "block";
}
</script>
<!-- Contenido principal de la vista -->
<?php if ($empleado): ?>
    <div class="contenedor-personal">
        <h2 class="titulo-personal">Estado Personal</h2>
        <!-- Mostrar mensaje de éxito si fue seteado desde otra página -->
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alerta-exito">
                <?= $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
            </div>
        <?php endif; ?>
        <div class="card-personal">
            <h3>Mi Información Personal</h3>
            <!-- Vista normal (solo lectura) -->
            <div id="vista-personal">
                <table class="tabla-personal">
                    <tr><th>Apellido y Nombre</th><td><?= $empleado['apellido_nombre'] ?></td></tr>
                    <tr><th>Teléfono</th><td><?= $empleado['telefono'] ?></td></tr>
                    <tr><th>Mail</th><td><?= $empleado['mail'] ?></td></tr>
                    <tr><th>Dirección</th><td><?= $empleado['direccion'] ?></td></tr>
                    <tr><th>Tipo y N° Documento</th><td><?= $empleado['numero_documento'] ?></td></tr>
                </table>
                <!-- Botón para habilitar el modo edición -->
                <button class="btn-personal-editar" onclick="activarEdicion()">Editar</button>
                <button type="button" class="btn-personal-editar" id="volver" onclick="window.location='<?= $pagina_volver ?>'">Volver</button>
            </div>
            <!-- Formulario para editar datos personales -->
            <div id="form-personal" style="display: none;">
                <form method="POST" action="estado_personal.php" class="form-editar">
                    <table class="tabla-personal">
                        <tr>
                            <th>Apellido y Nombre</th>
                            <td><input type="text" name="apellido_nombre" value="<?= $empleado['apellido_nombre'] ?>" required></td>
                        </tr>
                        <tr>
                            <th>Teléfono</th>
                            <td><input type="text" name="telefono" value="<?= $empleado['telefono'] ?>"></td>
                        </tr>
                        <tr>
                            <th>Mail</th>
                            <td><input type="email" name="mail" value="<?= $empleado['mail'] ?>" required></td>
                        </tr>
                        <tr>
                            <th>Dirección</th>
                            <td><input type="text" name="direccion" value="<?= $empleado['direccion'] ?>"></td>
                        </tr>
                        <tr>
                            <th>Tipo y N° Documento</th>
                            <td><input type="text" value="<?= $empleado['numero_documento'] ?>" disabled></td>
                        </tr>
                    </table>
                    <!-- Botones para enviar o cancelar edición -->
                    <button type="submit" name="guardar_personal" class="btn-personal-aceptar">Aceptar</button>
                    <button type="button" onclick="cancelarEdicion()" class="btn-personal-cancelar">Cancelar</button>
                    <button type="button" class="btn-personal-editar" id="volve" onclick="window.location='<?= $pagina_volver ?>'">Volver</button>            
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No se encontró la información del empleado.</p>
<?php endif; ?>         

