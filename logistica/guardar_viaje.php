<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 1) {
    header("Location: no_autorizado.php");
    exit();
}

include("conexion.php");
// SOLO si vino por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    // Datos que vienen del formulario
    $id_viaje     = isset($_POST['id_viaje']) ? trim($_POST['id_viaje']) : "";
    $id_camion    = $_POST['id_camion'];
    $id_carga     = $_POST['id_carga'];
    $numero_viaje = $_POST['numero_viaje'];
    $origen       = $_POST['origen'];
    $destino      = $_POST['destino'];
    $producto_txt = $_POST['producto']; // ahora texto libre
    $empresa_txt  = $_POST['empresa'];  // ahora texto libre
    $cantidad     = $_POST['cantidad'];
    $peso         = $_POST['peso'];
    $fecha        = $_POST['fecha'];
    $hora_entrega = $_POST['hora_entrega'];

     $foto_entrega = null;
    if (isset($_FILES['foto_entrega']) && $_FILES['foto_entrega']['error'] === UPLOAD_ERR_OK) {
        $dir_destino = "fotos_viajes/";
        if (!file_exists($dir_destino)) {
            mkdir($dir_destino, 0777, true);
        }

        $tmp_name = $_FILES['foto_entrega']['tmp_name'];
        $nombre_original = basename($_FILES['foto_entrega']['name']);
        $ext = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

        // Validar extensión
        $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $ext_permitidas)) {
            die("Formato de imagen no válido. Solo se permiten JPG, PNG, GIF o WEBP.");
        }

        // Validar tamaño (máx 5MB)
        if ($_FILES['foto_entrega']['size'] > 5 * 1024 * 1024) {
            die("El archivo es demasiado grande. Máximo 5 MB.");
        }

        // Nombre único
        $nombre_nuevo = uniqid("foto_", true) . "." . $ext;
        $ruta_final = $dir_destino . $nombre_nuevo;

        if (move_uploaded_file($tmp_name, $ruta_final)) {
            $foto_entrega = $ruta_final;
        } else {
            die("Error al subir la foto.");
        }
    }

    /* =====================================================
       1) Ver si el producto ya existe, si no lo creo
    ====================================================== */
    $stmtCheckProd = $conn->prepare("SELECT id_producto FROM productos WHERE nombre = ?");
    $stmtCheckProd->bind_param("s", $producto_txt);
    $stmtCheckProd->execute();
    $resProd = $stmtCheckProd->get_result();

    if ($resProd->num_rows > 0) 
    {
        $id_producto = $resProd->fetch_assoc()['id_producto'];
    } 
    else 
    {
        // Insertar producto nuevo
        $stmtInsertProd = $conn->prepare("INSERT INTO productos (nombre) VALUES (?)");
        $stmtInsertProd->bind_param("s", $producto_txt);
        $stmtInsertProd->execute();
        $id_producto = $conn->insert_id;
        $stmtInsertProd->close();
    }
    $stmtCheckProd->close();
    /* =====================================================
        2) Crear la carga en la tabla CARGAS
        ====================================================== */
    if($id_viaje=="")
    {
        $stmt_cargas = $conn->prepare("INSERT INTO cargas (id_producto, cantidad, peso) VALUES (?,?,?)");
        $stmt_cargas->bind_param("iid", $id_producto,$cantidad,$peso);
        if ($stmt_cargas->execute()) 
        {
            $id_carga = $conn->insert_id;
        } 
        else 
        {
            die("Error al crear carga: " . $conn->error);
        }
        $stmt_cargas->close(); 
    }
    else
    {
        $stmt_getCarga = $conn->prepare("SELECT id_carga FROM viajes WHERE id_viaje = ?");
        $stmt_getCarga->bind_param("i", $id_viaje);
        $stmt_getCarga->execute();
        $resCarga = $stmt_getCarga->get_result();
        if ($resCarga->num_rows > 0) 
        {
            $id_carga = $resCarga->fetch_assoc()['id_carga'];
            // Ahora actualizo la carga
            $stmt_updateCarga = $conn->prepare("UPDATE cargas SET id_producto = ?, cantidad = ?, peso = ? WHERE id_carga = ?");
            $stmt_updateCarga->bind_param("iidi", $id_producto, $cantidad, $peso, $id_carga);
            if (!$stmt_updateCarga->execute()) 
            {
                die("Error al actualizar carga: " . $conn->error);
            }
            $stmt_updateCarga->close();
        } 
        else 
        {
            die("No se encontró la carga asociada al viaje.");
        }
        $stmt_getCarga->close();
    }
    /* =====================================================
       3) Ver si la empresa ya existe, si no la creo
    ====================================================== */
    $stmtCheckEmp = $conn->prepare("SELECT id_empresa FROM empresas WHERE nombre = ?");
    $stmtCheckEmp->bind_param("s",$empresa_txt);
    $stmtCheckEmp->execute();
    $resEmp = $stmtCheckEmp->get_result();
    if ($resEmp->num_rows > 0) 
    {
        $id_empresa = $resEmp->fetch_assoc()['id_empresa'];
    } 
    else 
    {
        $stmtInsertEmp=$conn->prepare("INSERT INTO empresas (nombre) VALUES (?)");
        $stmtInsertEmp->bind_param("s",$empresa_txt);
        if ($stmtInsertEmp->execute()) 
        {
            $id_empresa = $conn->insert_id;
        } 
        else 
        {
            die("Error al crear empresa: " . $stmtInsertEmp->error);
        }
        $stmtInsertEmp->close();
    }
    $stmtCheckEmp->close();
    /* =====================================================
       4) Crear el viaje en la tabla VIAJES
    ====================================================== */
     if($id_viaje == "") 
    {
        $stmt = $conn->prepare("SELECT id_viaje FROM viajes WHERE numero_viaje = ?");
        $stmt->bind_param("i", $numero_viaje);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0)
        {
            echo "Viaje con número '$numero_viaje' encontrado (no repita los números del viaje)";
            exit; // detener el script aquí
        }
        $stmt->close();
        $stmtViaje = $conn->prepare("INSERT INTO viajes 
            (numero_viaje, id_camion, id_empresa, id_carga, origen, destino, fecha, hora_entrega, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
        $stmtViaje->bind_param("iiiissss", $numero_viaje, $id_camion, $id_empresa, $id_carga, $origen, $destino, $fecha, $hora_entrega);
        if (!$stmtViaje->execute()) {
            die("Error al crear viaje: " . $conn->error);
        }
        $stmtViaje->close();

        // Crear notificaciones
        $sqlpeon = "SELECT u.ID_USUARIO FROM usuarios AS u
                    INNER JOIN empleados AS e ON e.id_empleado = u.id_empleado
                    INNER JOIN camiones AS c ON e.id_camion = c.id_camion
                    WHERE e.id_camion = '$id_camion' AND e.rol = 0";
        $respeon = $conn->query($sqlpeon);

        $sqlcamion = "SELECT u.ID_USUARIO FROM usuarios AS u
                      INNER JOIN empleados AS e ON e.id_empleado = u.id_empleado
                      INNER JOIN camiones AS c ON e.id_camion = c.id_camion
                      WHERE e.id_camion = '$id_camion' AND e.rol = 1";
        $rescamion = $conn->query($sqlcamion);

        if ($respeon && $rescamion && $respeon->num_rows > 0 && $rescamion->num_rows > 0) {
            $id_usuario_peon = $respeon->fetch_assoc()['ID_USUARIO'];
            $id_usuario_camion = $rescamion->fetch_assoc()['ID_USUARIO'];
            $mensaje = "Se te asignó un nuevo viaje desde $origen hasta $destino para el $fecha a las $hora_entrega";
            $conn->query("INSERT INTO notificaciones (id_usuario, mensaje, leida) VALUES ('$id_usuario_peon', '$mensaje', 0)");
            $conn->query("INSERT INTO notificaciones (id_usuario, mensaje, leida) VALUES ('$id_usuario_camion', '$mensaje', 0)");
        }

        echo "creado";
        exit();
    } 
    else {
        // Si se sube una nueva foto, eliminar la anterior (opcional)
        if ($foto_entrega) 
        {
            $resFoto = $conn->query("SELECT foto_entrega FROM viajes WHERE id_viaje = $id_viaje");
            if ($resFoto && $resFoto->num_rows > 0) 
            {
                $vieja = $resFoto->fetch_assoc()['foto_entrega'];
                if (!empty($vieja) && file_exists($vieja)) 
                {
                    unlink($vieja);
                }
            }
            $stmtViaje = $conn->prepare("UPDATE viajes 
                SET id_camion = ?, id_empresa = ?, id_carga = ?, 
                    origen = ?, destino = ?, fecha = ?, hora_entrega = ?, foto_entrega = ? 
                WHERE id_viaje = ?");
            $stmtViaje->bind_param("iiisssssi",  $id_camion, $id_empresa, $id_carga, $origen, $destino, $fecha, $hora_entrega, $foto_entrega, $id_viaje);
        } else {
            $stmtViaje = $conn->prepare("UPDATE viajes 
                SET id_camion = ?, id_empresa = ?, id_carga = ?, 
                    origen = ?, destino = ?, fecha = ?, hora_entrega = ? 
                WHERE id_viaje = ?");
            $stmtViaje->bind_param("iiissssi",  $id_camion, $id_empresa, $id_carga, $origen, $destino, $fecha, $hora_entrega, $id_viaje);
        }

        if (!$stmtViaje->execute()) {
            die("Error al modificar viaje: " . $conn->error);
        }
        $stmtViaje->close();

        echo "modificado";
        exit();
    }
}
?>