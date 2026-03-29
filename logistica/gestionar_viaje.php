<?php
session_start();

// Verificar si el usuario está logueado y es EMPLEADO
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 0) {
    header("Location: no_autorizado.php");
    exit();
}

// Conexión a la base de datos
include("conexion.php");

// Verificar que se haya enviado el formulario por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    $idViaje = intval($_POST['id_viaje']);
    $foto_entrega= null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) 
    {
        $dir_destino = "fotos_viajes/";
        if (!file_exists($dir_destino)) 
        {
            mkdir($dir_destino, 0777, true);
        }
        $tmp_name = $_FILES['foto']['tmp_name'];
        $nombre_original = basename($_FILES['foto']['name']);
        $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
        // Validar extensión
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $ext_permitidas)) 
        {
            die("Formato de imagen no válido. Solo se permiten JPG, PNG, GIF o WEBP.");
        }
        // Validar tamaño (máx 5MB)
        if ($_FILES['foto']['size'] > 5 * 1024 * 1024) 
        {
            die("El archivo es demasiado grande. Máximo 5 MB.");
        }
        // Nombre único
        $nombre_nuevo = uniqid("foto_", true) . "." . $ext;
        $ruta_final = $dir_destino . $nombre_nuevo;

        if (move_uploaded_file($tmp_name, $ruta_final)) 
        {
            $foto_entrega = $ruta_final;
        } else 
        {
            die("Error al subir la foto.");
        }
    }
    if ($foto_entrega) 
    {
        $resFoto = $conn->query("SELECT foto_entrega FROM viajes WHERE id_viaje = $idViaje");
        if ($resFoto && $resFoto->num_rows > 0) 
        {
            $vieja = $resFoto->fetch_assoc()['foto_entrega'];
            if (!empty($vieja) && file_exists($vieja)) 
            {
                unlink($vieja);
            }
        }
        $stmtViaje = $conn->prepare("UPDATE viajes SET  foto_entrega = ? WHERE id_viaje = ?");
        $stmtViaje->bind_param("si",$foto_entrega, $idViaje);
        if (!$stmtViaje->execute()) 
        {
            die("Error al ingresar la foto: " . $conn->error);
        }
        $stmtViaje->close();
        header("Location: empleado.php");
    }
}
        

// Después de procesar, redirigir al panel del empleado
header("Location: empleado.php");
exit();
?>
