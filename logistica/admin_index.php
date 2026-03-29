<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] != 1) {
    header("Location: no_autorizado.php");
    exit();
}
include_once("class_index.php");
include("conexion.php");
$objeto=new index();
if(isset($_POST["buscar_viaje"]) && $_POST["viaje_oculto"]=="si")
{
    $valor = trim($_POST["escribir_viaje"]); 
   if (
        preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $valor) || // dd/mm/yyyy
        preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $valor)    // yyyy/mm/dd
    ) {
        $indicar = "fecha";
        $busqueda = $valor;
    }
    // Si no es fecha, revisar si es un estado de texto
    elseif (!is_numeric($valor)) {
        $indicar = "estado";

        if ($valor == "pendiente" || $valor == "pendientes") {
            $busqueda = 0;
        } elseif ($valor == "iniciado") {
            $busqueda = 1;
        } elseif ($valor == "finalizado") {
            $busqueda = 2;
        } else {
            $busqueda = 3;
        }
    }
    // Si no es fecha ni texto, entonces es un ID
    else {
        $indicar = "id";
        $busqueda = $valor;
    }


    $objeto->busqueda=$busqueda;
    $objeto->indicar=$indicar;
    $viaje=$objeto->buscar_viajes($conn);
    if(!$viaje)
    {
       echo "<div style='color:red; font-weight:bold; margin:10px 0;'>
             ❌ No se encontraron viaje/s con ese criterio.
          </div>";
    }
}
else
{
    $viaje=$objeto->traer_viajes($conn);
}
if(isset($_POST["buscar_empleado"])&& $_POST["empleado_oculto"]=="si")
{
    $objeto->dni=$_POST["escribir_empleado"];
    $empleado=$objeto->buscar_empleados($conn);
    if(!$empleado)
    {
        echo "<div style='color:red; font-weight:bold; margin:10px 0;'>
                ❌ No se encontraron Empleado/s con ese criterio.
            </div>";
    }
}
else
{
    $empleado=$objeto->traer_empleados($conn);
}
if(isset($_POST["buscar_camion"])&& $_POST["camion_oculto"]=="si")
{
    $objeto->busqueda=$_POST["escribir_camion"];
    if(strtolower($_POST["escribir_camion"]) == "en mantenimiento" ||strtolower($_POST["escribir_camion"]) == "mantenimiento" )
    {
        $objeto->busqueda=1;
    }
    if(strtolower($_POST["escribir_camion"]) == "activo")
    {
        $objeto->busqueda=0;
    }
    if(strtolower($_POST["escribir_camion"]) == "inactivo")
    {
        $objeto->busqueda=2;
    }
    $camion=$objeto->buscar_camiones($conn);
    if(!$camion)
    {
        echo "<div style='color:red; font-weight:bold; margin:10px 0;'>
                ❌ No se encontraron Camion/es con ese criterio.
            </div>";
    }
}
else
{
    $camion=$objeto->traer_camiones($conn);
}
if (isset($_POST["estado_camion"], $_POST["id_camion"])) {
        $nuevoEstado = intval($_POST["estado_camion"]);
        $id_camion = intval($_POST["id_camion"]);

        $stmt = $conn->prepare("UPDATE camiones SET estado = ? WHERE id_camion = ?");
        $stmt->bind_param("ii", $nuevoEstado, $id_camion);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
        exit();
    }
if (isset($_POST["estado_viaje"], $_POST["id_viaje"])) {
        $nuevoEstado = intval($_POST["estado_viaje"]);
        $id_viaje = intval($_POST["id_viaje"]);

        $stmt = $conn->prepare("UPDATE viajes SET estado = ? WHERE id_viaje = ?");
        $stmt->bind_param("ii", $nuevoEstado, $id_viaje);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
        exit();
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8"> <!-- Soporte para acentos y caracteres especiales -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Adaptación a móviles -->
        <link rel="stylesheet" href="Estilos3.css">
        <link rel="stylesheet" href="admin.css">
        <script>
            function actualizarEstado(nuevoEstado, idViaje) {
    fetch("admin_index.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
            estado_viaje: nuevoEstado,
            id_viaje: idViaje
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ Estado del viaje actualizado correctamente.");
        } else {
            alert("❌ Error: " + (data.error || "No se pudo cambiar el estado del viaje"));
        }
    })
    .catch(err => alert("Error en la conexión: " + err));
}

            function cambiarEstadoCamion(nuevoEstado, idCamion) {
                fetch("admin_index.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({
                        estado_camion: nuevoEstado,
                        id_camion: idCamion
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("✅ Estado del camión actualizado correctamente.");
                        location.reload();
                    } else {
                        alert("❌ Error: " + (data.error || "No se pudo cambiar el estado del camión"));
                    }
                })
                .catch(err => alert("Error en la conexión: " + err));
            }
            document.addEventListener("DOMContentLoaded", function () {
                document.addEventListener("click", function(e) {
                    // Buscar si se hizo clic en una fila de la tabla (tr)
                    const fila = e.target.closest("tr");
                    if (!fila) return;

                    // Evitar abrir si se clickea dentro de un select o botón de editar
                    if (e.target.tagName === "SELECT" || e.target.classList.contains("btn-editar")) return;

                    // Buscar la fila siguiente (la de detalle)
                    let detalle = fila.nextElementSibling;
                    if (!detalle || detalle.tagName !== "TR") return;

                    // Buscar el panel interno
                    let panel = detalle.querySelector(".open_empleado, .open_viaje");
                    if (!panel) return;

                    // Alternar visibilidad
                    const oculto = getComputedStyle(panel).display === "none";
                    panel.style.display = oculto ? "block" : "none";
                });
            });
            function validar(event)
            {
                let boton = event.submitter.name;
                let viaje = document.getElementById("escribir_viaje").value.trim();
                let dni = document.getElementById("escribir_empleado").value.trim();
                let camion = document.getElementById("escribir_camion").value.trim();
                if(boton == "buscar_viaje")
                {
                    if(viaje == "")
                    {
                        alert("ingrese un valor para buscar");
                        document.getElementById("viaje_oculto").value = "no";
                        return false;
                    }
                    document.getElementById("viaje_oculto").value = "si";
                    return true;
                }
                if(boton == "buscar_empleado")
                {
                    if(dni == ""|| dni.length < 7 || dni.length > 9)
                    {
                        alert("El DNI debe tener entre 7 y 9 dígitos");
                        document.getElementById("empleado_oculto").value = "no";
                        return false;
                    }
                    document.getElementById("empleado_oculto").value = "si";
                    return true;
                }
                if(boton=="buscar_camion")
                {
                    if(camion=="")
                    {   
                        alert("ingresa correctamente el datos");
                        document.getElementById("camion_oculto").value = "no";
                        return false;
                    }
                    document.getElementById("camion_oculto").value = "si";
                    return true;
                }
            }
        </script>
    </head>
    <body>
        <header class="header">
            <div class="logo">
                <img src="imagenes/logo_usu.png" alt="Logo"style="border-radius: 50%;">
                <h2>Bienvenido <span style="color: #50b892ff;"><?= $_SESSION['user'] ?></span></h2>
            </div>
            <ul class="nav-links">
                <!-- Ítem de notificación cargado desde otro archivo -->
                <li style="position: relative;">
                <?php include("notificacion.php"); ?>
                </li>

                <!-- Dropdown con opciones del usuario -->
                <li class="dropdown-usuario">
                <span style="color: #50b892ff" class="usuario-toggle">
                    Configuracion▾
                </span>

                <!-- Submenú del usuario -->
                <ul class="submenu-usuario">
                    <li><a href="#" id="btn-estado-personal">Estado Personal</a></li>
                    <li><a href="logout.php">Cerrar sesión</a></li>
                </ul>
                </li>
            </ul>
        </header>
            <div  id="contenido-principal">
                <div class="contenedor-viaje">
                    <div class="card">
                        <form name="viajes" id="viajes" method="post" action="admin_index.php" onsubmit="return validar(event)">
                            <h2 class="titulo-seccion">Gestión de Viajes</h2>
                            <h4 for="escribir_viaje">Buscardor de Viajes (buscar solo por N° de viaje, Fecha o Estado)</h4><br>
                            <div class="buscador">
                                <input type="text" name="escribir_viaje" id="escribir_viaje" placeholder="Ingrese un dato...">
                                <input type="hidden" name="viaje_oculto" id="viaje_oculto">
                                <input type="submit" name="buscar_viaje" id="buscar_viaje" class="btn-aceptar" value="Buscar">
                            </div>
                            <div class="tabla-scroll">
                                <table class="tabla-viajes">
                                    <thead>
                                        <tr class="titulo-seccion">
                                            <th>N°viaje</th>
                                            <th>Empresa</th>
                                            <th>Destino</th>
                                            <th>Fecha</th>
                                            <th>Hora de Entrega</th>
                                            <th>Estado</th>
                                            <th><a href="alta_viaje.php" class="btn-crear">Crear Viaje</a></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            if(!empty($viaje))
                                            {
                                            foreach($viaje as $viajes)
                                                {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $viajes[13]; ?></td>
                                                            <td><?php echo $viajes[10]; ?></td>
                                                            <td><?php echo $viajes[1]; ?></td>
                                                            <td><?= date("d/m/Y", strtotime($viajes[2])); ?></td>
                                                            <td><?php echo $viajes[3]; ?>hs</td>
                                                            <td>
                                                                <div class="select-wrapper">
                                                                <select id="estado_viaje" class="select-estilo5" name="estado_viaje" data-estado="<?php echo $viajes[4]; ?>" onchange="actualizarEstado(this.value, <?php echo $viajes[0]; ?>)">
                                                                    <option value="0"<?php if($viajes[4] == 0) {echo "selected";} ?>>Pendiente</option>
                                                                    <option value="1"<?php if($viajes[4] == 1) {echo "selected";} ?>>Iniciado</option>
                                                                    <option value="2"<?php if($viajes[4] == 2) {echo "selected";} ?>>Finalizado</option>
                                                                </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="button" class="btn-editador" value="Editar" onclick="window.location.href='alta_viaje.php?editar_viaje=1&id_viaje=<?php echo $viajes[0]; ?>'">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="7">
                                                                <div class="open_viaje" style="display:none;">
                                                                    <p>Hora de salida del camion: <?php echo $viajes[5]; ?>hs</p> 
                                                                    <p>Hora de llegada del camion: <?php echo $viajes[6]; ?>hs</p>
                                                                    <p>Cargamento <?php echo $viajes[9];?></p> 
                                                                    <p>Cantidad: <?php echo $viajes[7]; ?></p>
                                                                    <p>Peso: <?php echo $viajes[8]; ?>KL</p>
                                                                    <p>camion:<?php echo  $viajes[11]." ".$viajes[12]; ?></p>
                                                                </div>
                                                            </td>
                                                        </tr> 
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                echo '<tr><td colspan="7">No hay viajes registrados</td></tr>';
                                            } 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                        <form name="empleados" id="empleados" method="post" action="admin_index.php" onsubmit="return validar(event)">
                            <br><h2 class="titulo-seccion">Empleados</h2>
                            <h4>Buscar solo por DNI del Empleado</h4><br>
                            <div class="buscador">
                                <input type="number" name="escribir_empleado" id="escribir_empleado" placeholder="Ingrese un dni...">
                                <input type="hidden" name="empleado_oculto" id="empleado_oculto">
                                <input type="submit" name="buscar_empleado" class="btn-aceptar" id="buscar_empleado" value="Buscar">
                            </div>
                            <div class="tabla-scroll">
                                    <table class="tabla-viajes">
                                        <thead>
                                            <tr class="titulo-seccion">
                                                <th>N° documento</th>
                                                <th>Apellido y Nombre</th>
                                                <th>Rol</th>
                                                <th><a href="form_empleados.php" class="btn-crear">Crear Empleado</a></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($empleado))
                                                {
                                                foreach($empleado as $empleados)
                                                    {
                                                        ?>
                                                            <tr>
                                                            <td><?php echo $empleados[1]; ?></td>
                                                            <td><?php echo $empleados[2]; ?></td>
                                                            <td><?php if( $empleados[3] == 0 ){ echo "Peon";}
                                                                            else { echo "Camionero";} ?></td>
                                                            <td>
                                                                <input type="button" class="btn-editador" value="Editar" onclick="window.location.href='form_empleados.php?editar_empleado=1&id_empleado=<?php echo $empleados[0]; ?>'">
                                                            </tr>
                                                            <tr>
                                                                <td colspan="6">
                                                                    
                                                                    <div class="open_viaje" style="display:none">
                                                                        <p>Nacionalidad: <?php echo $empleados[7]; ?></p>
                                                                        <p>Telefono: <?php echo $empleados[4]; ?></p>
                                                                        <p>Mail: <?php echo $empleados[5]; ?></p>
                                                                        <p>Direccion: <?php echo $empleados[6]; ?></p>
                                                                        <p>Camion en el que trabaja: <?php echo $empleados[8]."".$empleados[9]; ?></p> 
                                                                    </div>
                                                                </td>
                                                            </tr> 
                                                        <?php
                                                    }
                                                }
                                                else
                                                {
                                                    echo '<tr><td colspan="6">No hay empleados registrados</td></tr>';
                                                } 
                                            ?>
                                        </tbody>
                                    </table>
                            </div>
                        </form>
                        <form name="camiones" id="camiones" method="post" action="admin_index.php" onsubmit="return validar(event)">
                            <br><h2 class="titulo-seccion">Camiones</h2>
                            <h4>Buscar camion por patente o estado</h4><br>
                            <div class="buscador">
                                <input type="text" name="escribir_camion" id="escribir_camion" placeholder="Ingrese un dato...">
                                <input type="hidden" name="camion_oculto" id="camion_oculto">
                                <input type="submit" name="buscar_camion" class="btn-aceptar" id="buscar_camion" value="Buscar">
                            </div>    
                            
                                    <table class="tabla-viajes">
                                        <thead>
                                            <tr class="titulo-seccion">
                                                <th>Patente</th>
                                                <th>Modelo</th>
                                                <th>seguro</th>
                                                <th>Fecha de vencimiento del seguro</th>
                                                <th>fecha de vencimiento de la VTV</th>
                                                <th>Estado</th>
                                                <th><a href="form_camiones.php" class="btn-crear">Crear Camion</a></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                if(!empty($camion))
                                                {
                                                foreach($camion as $camiones)
                                                    {
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $camiones[1]; ?></td>
                                                                <td><?php echo $camiones[2]; ?></td>
                                                                <td><?php echo $camiones[3]; ?></td>
                                                                <td><?= date("d/m/Y", strtotime($camiones[4])); ?></td>
                                                                <td><?= date("d/m/Y", strtotime($camiones[5])); ?></td>
                                                                <td>
                                                                    <div class="select-wrapper">
                                                                    <select name="estado_camion" class="select-estilo5" onchange="cambiarEstadoCamion(this.value, <?php echo $camiones[0]; ?>)">
                                                                        <option value="0" <?php if($camiones[6] == 0) echo "selected"; ?>>Activo</option>
                                                                        <option value="1" <?php if($camiones[6] == 1) echo "selected"; ?>>En Mantenimiento</option>
                                                                        <option value="2" <?php if($camiones[6] == 2) echo "selected"; ?>>Inactivo</option>
                                                                    </select>
                                                                </div>
                                                                </td>
                                                                <td>
                                                                    <input type="button" class="btn-editador" value="Editar" onclick="window.location.href='form_camiones.php?editar_camion=1&id_camion=<?php echo $camiones[0]; ?>'">
                                                            </tr>
                                                        <?php
                                                    }
                                                }
                                                else
                                                {
                                                    echo '<tr><td colspan="7">No hay camiones registrados</td></tr>';
                                                } 
                                            ?>
                                        </tbody>
                                    </table>
                                
                            </form>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const btnEstado = document.getElementById("btn-estado-personal");
                        const contenedor = document.getElementById("contenido-principal");

                        btnEstado.addEventListener("click", function(e) {
                            e.preventDefault();
                            fetch("estado_personal.php")
                                .then(response => response.text())
                                .then(html => {
                                    contenedor.innerHTML = html;
                                    window.scrollTo(0, 0);
                                })
                                .catch(err => {
                                    contenedor.innerHTML = `<p style='color:red;'>Error al cargar el estado personal: ${err}</p>`;
                                });
                        });
                    });

                    function activarEdicion() {
                    document.getElementById('vista-personal').style.display = 'none';
                    document.getElementById('form-personal').style.display = 'block';
                    }

                    function cancelarEdicion() {
                    document.getElementById('form-personal').style.display = 'none';
                    document.getElementById('vista-personal').style.display = 'block';
                    }
                </script>
    </body>
</html>