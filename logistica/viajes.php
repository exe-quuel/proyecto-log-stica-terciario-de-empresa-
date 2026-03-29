<?php
session_start();

//Solo permite acceso a empleados
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 0) {
    header("Location: no_autorizado.php");
    exit();
}

// Conexión a la base de datos y funciones extra
include("conexion.php");
include("funciones.php");

$idUsuario =(int)$_SESSION['id_usuario'];

// Obtener ID del empleado desde la tabla usuarios
$sqlEmpleado = "SELECT id_empleado FROM usuarios WHERE id_usuario = $idUsuario";
$resEmpleado = mysqli_query($conn, $sqlEmpleado);
$empleado = mysqli_fetch_assoc($resEmpleado);

if (!$empleado) {
    echo "Empleado no encontrado.";
    exit();
}

$idEmpleado = $empleado['id_empleado'];

// Buscar el camión asignado al empleado
$datosCamion = null;
$sqlCamion = "SELECT id_camion FROM empleados WHERE id_empleado = $idEmpleado";
$resCamion = mysqli_query($conn, $sqlCamion);
$rowCamion = mysqli_fetch_assoc($resCamion);

if ($rowCamion) {
    $idCamion = $rowCamion['id_camion'];
    $resDatosCamion = mysqli_query($conn, "SELECT * FROM camiones WHERE id_camion = '$idCamion'");
    $datosCamion = mysqli_fetch_assoc($resDatosCamion);
}

// Obtener viajes asignados al empleado
$resViajes = null;
if (isset($idCamion)) {
    $sqlViajes = "SELECT * FROM viajes WHERE id_camion = $idCamion";
    $resViajes = mysqli_query($conn, $sqlViajes);
}
?>
<html>
    <head>
        
        <link rel="stylesheet" href="empleados.css">
        <!-- Función JS opcional para volver a esta sección desde otro archivo -->
<script>
    function verViajes() {
        $("#contenido-seccion").load("viaje.php");
        const menu = document.getElementById('menuNotificaciones');
        if (menu) {
            menu.style.display = 'none';
        }
    }
</script>
</head>
<body>
<div class="contenedor-general">
    <h2 class="titulo-seccion">Camión Asignado</h2>

    <!-- Información del camión -->
    <section class="info-camion">
        <?php if ($datosCamion): ?>
            <div class="camion-datos">
                <ul>
                    <li><strong>Patente:</strong> <?= $datosCamion['patente']." ".$datosCamion['modelo'] ?></li>
                    <li><strong>Seguro:</strong> <?= $datosCamion['fecha_seguro']." ".$datosCamion['seguro'] ?></li>
                    <li><strong>VTV:</strong> <?= $datosCamion['vtv']?></li>
                </ul>
            </div>
            <div class="camion-imagen">
                <div class="texto-estado">
                <?php 
                    if($datosCamion['estado']==0)
                    {
                        echo"<img src='imagenes/camion verde.png' alt='Camión activo' class='icono-camion'>";
                        echo"<span class='texto-estado activo'>Activo</span>";
                    }
                    elseif($datosCamion['estado']==1)
                    {
                        echo"<img src='imagenes/camion gris.png' alt='Camión mantenimiento' class='icono-camion'>";
                        echo"<span class='texto-estado matntenimiento'>En Mantenimineto</span>";
                    }
                    else
                    {
                        echo"<img src='imagenes/camion rojo.png' alt='Camión inactivo' class='icono-camion'>";
                        echo"<span class='texto-estado inactivo'>Inactivo</span>"; 
                    }
                ?>
                </div>
            </div>
        <?php else: ?>
            <p>No tenés un camión asignado.</p>
        <?php endif; ?>
    </section>
    <section class="viajes-lista">
        <h2 class="titulo-seccion">Panel de Viajes</h2>
        <?php if (mysqli_num_rows($resViajes) > 0): ?>
            <div class="tabla-scroll">
                <table class="tabla-viaje">
                    <thead>
                        <tr>
                            <th>Viaje</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Fecha y Hora de Entrega</th>
                            <th>nombre del producto</th>
                            <th>Cantidad</th>
                            <th>Peso</th>
                            <th>Inicio</th>
                            <th>Fin</th> 
                            <th>Estado del viaje</th>
                            <th>Foto de la Entrega</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($viaje = mysqli_fetch_assoc($resViajes)): ?>
                            <?php
                            $idViaje=$viaje['id_viaje'];
                            $numero_viaje=$viaje['numero_viaje'];
                            $fecha_hora_entrega= date("d/m/Y", strtotime($viaje['fecha'])). " " .$viaje['hora_entrega'];
                            $idcarga = $viaje['id_carga'];
                            $origen = $viaje['origen'];
                            $destino = $viaje['destino'];
                            $estado = $viaje['estado'];
                            $hora_inicio = $viaje['hora_inicio'];
                            $hora_fin = $viaje['hora_fin'];
                            $foto_entrega = $viaje['foto_entrega'];

                            // Obtener datos de carga relacionados al viaje
                            $sqlCarga = "SELECT c.peso,c.cantidad, p.nombre AS nombre_producto 
                                         FROM cargas AS c
                                         INNER JOIN productos AS p ON c.id_producto = p.id_producto 
                                         WHERE c.id_carga = $idcarga LIMIT 1";
                            $resCarga = mysqli_query($conn, $sqlCarga);
                            $carga = mysqli_fetch_assoc($resCarga);

                            // Asignar valores, o dejar vacíos si no hay carga
                            if ($carga) {
                                $peso = $carga['peso'];
                                $cantidad = $carga['cantidad'];
                                $nombre_producto = $carga['nombre_producto'];
                            } else {
                                $peso = "";
                                $cantidad = "";
                                $nombre_producto = "";
                            }

                            // Obtener estado del viaje
                            $estadoViaje = "0";
                            $sqlEstado = "SELECT estado FROM viajes WHERE id_viaje = $idViaje";
                            $resEstado = mysqli_query($conn, $sqlEstado);
                            if ($filaEstado = mysqli_fetch_assoc($resEstado)) {
                                $estadoViaje = $filaEstado['estado'];
                            

                            // Crear notificación automática si es un viaje nuevo pendiente
                            $sqlCheck = "SELECT id_notificacion FROM notificaciones 
                                        WHERE id_usuario = $idUsuario 
                                        AND mensaje LIKE '%#VIAJE{$idViaje}%'";
                            $resCheck = mysqli_query($conn, $sqlCheck);}

                            if (mysqli_num_rows($resCheck) == 0 && $estadoViaje === "0") {
                                // Mensaje visible para el usuario, sin ID
                                $mensaje = "Tenés un nuevo viaje pendiente desde $origen. #VIAJE{$idViaje}";

                                // La parte "#VIAJE{id}" queda oculta, pero sirve como identificador técnico
                                notificar($idUsuario, $mensaje, $conn);
                            }
                            ?>
                            <tr>
                                <td><?= $numero_viaje ?></td>
                                <td><?= $origen ?></td>
                                <td><?= $destino ?></td>
                                <td><?= $fecha_hora_entrega ?></td>
                                <td><?= $nombre_producto ?></td>
                                <td><?= $cantidad ?></td>
                                <td><?= $peso."kl" ?></td>
                                <td><?= $hora_inicio ?></td>
                                <td><?= $hora_fin ?></td>
                                <td><?php if($estado==0){echo "Pendiente";}
                                else if($estado==1){echo"iniciado";}
                                else if($estado==2){echo"Finalizado";}
                                else{echo"Rechazado";} ?></td>
                                <td>
                                    <?php if(empty($foto_entrega)) 
                                        {?>
                                    <!-- Formulario para subir una foto del viaje -->
                                    <form action="gestionar_viaje.php" method="POST" enctype="multipart/form-data" style="display:inline;">
                                        <input type="hidden" name="id_viaje" value="<?= $idViaje ?>">
                                        
                                        <label for="foto_<?= $idViaje ?>"></label>
                                        <input type="file" name="foto" id="foto_<?= $idViaje ?>" accept="image/*" required>

                                        <button type="submit" class="btn-aceptar">Subir Foto</button>
                                    </form>

                                    <?php }else{ ?>
                                            <img src="<?= htmlspecialchars($foto_entrega) ?>" 
                                            alt="Foto de entrega"
                                            style="max-width:120px; border-radius:6px; border:1px solid #ccc;">
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No tenés viajes asignados.</p>
                <?php endif; ?>
            </section>
        </div>
    </body>
</html>
