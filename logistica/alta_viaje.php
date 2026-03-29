<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 1) {
    header("Location: no_autorizado.php");
    exit();
}
include("conexion.php");
include_once("class_alta_viaje.php");

if (isset($_GET['id_viaje'])) {
    $id_viaje = intval($_GET['id_viaje']); // siempre conviene castear a entero
    // Ahora $id_viaje tiene el valor que mandaste desde el botón
} else {
    $id_viaje = "";
}

if(!empty($id_viaje))
{
    $objeto = new class_alta_viaje();
    $objeto->id_viaje=$id_viaje;
    if($objeto->traer_viaje($conn))
    {
        $numero_viaje=$objeto->numero_viaje;
        $origen=$objeto->origen;
        $destino=$objeto->destino;
        $fecha=$objeto->fecha;
        $hora_entrega=$objeto->hora_entrega;
        $id_camion=$objeto->id_camion;
        $nombre_empresa=$objeto->nombre_empresa;
        $nombre_producto=$objeto->nombre_producto;
        $cantidad=$objeto->cantidad;
        $peso=$objeto->peso;
        $id_carga=$objeto->id_carga;
        $foto_entrega   = $objeto->foto_entrega ?? "";
    }
}
$objeto = new class_alta_viaje();
$info = $objeto->traer_info($conn);
if ($info) {
    // Asignar directo
    $camiones  = $info["camiones"];
    $empresas  = $info["empresas"];
    $productos = $info["productos"];
    $ubi       = $info["ubi"];
    $empleados = $info["empleados"];
} else {
    echo "<script>alert('no funciona');</script>";        
}
?>
<html>
    <head>
        <!-- SCRIPT para no perder diseño -->
        <script src="js/jquery-3.7.1.min.js"></script>
        <link rel="stylesheet" href="Estilos3.css">
        <script>
            $(function() {
                
                $("#form-alta").submit(function(e) {
                    e.preventDefault();
                    if (!validarFormulario()) return;
                    let formData = new FormData(this);
                    for (let [k, v] of formData.entries()) {
            console.log(k, "=", v);
        }

                    $.ajax({
                        url: "guardar_viaje.php",
                        type: "POST",
                        data: formData,
                        contentType: false, // 👈 necesario para enviar archivos
                        processData: false, // 👈 evita que jQuery procese el FormData
                        success: function(res) {
                            if (res === "creado") {
                                alert("Viaje creado correctamente");
                            } else if (res === "modificado") {
                                alert("Viaje modificado correctamente");
                            } else {
                                alert("Error: " + res);
                            }
                            $("#contenido-seccion").load("alta_viaje.php");
                        },
                            error: function(xhr, status, error) {
                                alert("Error en la petición: " + error);
                            }
                    });
                });
            });
        function validarFormulario() 
        {
            let id_camion = $("#id_camion").val();
            let id_empresa = $("#empresa").val();
            let numero_viaje = ($("#numero_viaje").val() || "").trim();
            let origen = ($("#origen").val() || "").trim();
            let destino = ($("#destino").val() || "").trim();
            let fecha = ($("#fecha").val() || "").trim();
            let hora_entrega = ($("#hora_entrega").val() || "").trim();

            // === VALIDACIONES ===

            // Campos vacíos
            if (!numero_viaje || !id_camion || !id_empresa ||
                !origen || !destino || !fecha || !hora_entrega) {
                alert("Por favor completá todos los campos obligatorios.");
                return false;
            }
            return true;
        }

        document.addEventListener("DOMContentLoaded", function() {
            const idviaje = document.getElementById("id_viaje").value.trim();
            const divCrear = document.getElementById("div_crear");
            const divModificar = document.getElementById("div_modificar");
            const divFoto = document.getElementById("div_foto");

            if (idviaje !== "") {
                divModificar.style.display = "block";
                divCrear.style.display = "none";
                divFoto.style.display = "block"; // 👈 solo si se modifica
            } else {
                divCrear.style.display = "block";
                divModificar.style.display = "none";
                divFoto.style.display = "none"; // 👈 ocultar al crear
            }
        });
        </script>
    </head>
    <header class="header">
            <div class="logo">
                <img src="imagenes/logo_usu.png" alt="Logo"style="border-radius: 50%;">
                <h2>Cuenta de <span style="color: #50b892ff"><?= $_SESSION['user'] ?></span></h2>
            </div>
            <div class="menu-toggle" id="menu-toggle">&#9776;</div>
  <ul class="nav-links">

    <!-- Ítem de notificación cargado desde otro archivo -->
    <li style="position: relative;">
      <?php include("notificacion.php"); ?>
    </li>

    <!-- Dropdown con opciones del usuario -->
    <li class="dropdown-usuario">
      <span class="usuario-toggle">
        Configuracion▾
      </span>

      <!-- Submenú del usuario -->
      <ul class="submenu-usuario">
        <li><a href="logout.php">Cerrar sesión</a></li>
      </ul>
    </li>
  </ul>
        </header>
<div class="contenedor-viaje">
    <h2 class="titulo-seccion">Alta de Viaje</h2>

    <!-- ================== FORMULARIO DE ALTA ================== -->
    <div class="card">
        <form id="form-alta" method="POST" enctype="multipart/form-data" >
            
            <label>Numero del viaje</label>
            <input type="number" name="numero_viaje" id="numero_viaje" <?php if(!empty($id_viaje)){echo "readonly";}else{echo "required";} ?> value="<?= htmlspecialchars($numero_viaje ?? '') ?>">

            <label>Camión:</label>
            <select name="id_camion" id="id_camion" required>
                <option value="" disabled selected hidden>Seleccione un Camion</option>
                <?php foreach ($camiones as $cam): ?>
                    <?php $selected = ($id_camion == $cam[0]) ? "selected" : "";?>
                <option value="<?= $cam[0] ?>" <?= $selected ?>><?= $cam[1]." ".$cam[2] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Origen:</label>
            <input list="lista_origenes" id="origen" name="origen" required value="<?= htmlspecialchars($origen ?? '') ?>">
            <datalist id="lista_origenes">
                <?php foreach ($ubi as $org): ?>
                    <option value="<?= $org[1] ?>">
                <?php endforeach; ?>
            </datalist>

            <label>Destino:</label>
            <input list="lista_destinos" id="destino" name="destino" required value="<?= htmlspecialchars($destino ?? '') ?>">
            <datalist id="lista_destinos">
                <?php foreach ($ubi as $dest): ?>
                    <option value="<?= $dest[0] ?>">
                <?php endforeach; ?>
            </datalist>

            <label>Empresa:</label>
            <input list="lista_empresas" id="empresa" name="empresa" required value="<?= htmlspecialchars($nombre_empresa ?? '') ?>">
            <datalist id="lista_empresas">
                <?php foreach ($empresas as $emp): ?>
                    <option value="<?= $emp[1] ?>">
                <?php endforeach; ?>
            </datalist>

            <label>Producto:</label>
            <input list="lista_productos" id="producto" name="producto" required value="<?= htmlspecialchars($nombre_producto ?? '') ?>">
            <datalist id="lista_productos">
                <?php foreach ($productos as $prod): ?>
                    <option value="<?= $prod[1] ?>">
                <?php endforeach; ?>
            </datalist>

            <label>Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" min="1" required value="<?= htmlspecialchars($cantidad ?? '') ?>">

            <label>Peso (kg):</label>
            <input type="number" id="peso" name="peso" step="0.01" required value="<?= htmlspecialchars($peso ?? '') ?>">

            <label>Fecha de salida:</label>
            <input type="date" id="fecha" name="fecha" required value="<?= htmlspecialchars($fecha ?? '') ?>">

            <label>Hora de entrega:</label>
            <input type="time" id="hora_entrega" name="hora_entrega" required value="<?= htmlspecialchars($hora_entrega ?? '') ?>">
            <?php if(!empty($id_viaje)): ?>
                <div id="div_foto" style="display:none;">
                    <label>Foto de la Entrega (opcional):</label>
                    <input type="file" id="fo" name="foto_entrega" accept="image/*">

                    <?php if (!empty($foto_entrega)): ?>
                        <div style="margin-top:10px;">
                            <p>Foto actual:</p>
                            <img src="<?= htmlspecialchars($foto_entrega) ?>" 
                                alt="Foto de entrega" 
                                style="max-width:250px; border-radius:10px; border:1px solid #ccc;">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div style="display:flex; gap:10px; align-items:center;">
                    <input type="hidden" name="id_viaje" id="id_viaje" value="<?= htmlspecialchars($id_viaje ?? '') ?>">
                    <input type="hidden" name="id_carga" id="id_carga" value="<?= htmlspecialchars($id_carga ?? '') ?>">
                    <div name="div_crear" id="div_crear" style="display:none">
                        <button type="submit" class="btn-confirmar btn-personal-aceptar">Crear Viaje</button>
                    </div>
                    <div name="div_modificar" id="div_modificar" style="display:none">
                    <input type="submit" class="btn-personal-aceptar" name="modificar" id="modificar" value="Modificar">
                    </div>
                    <button type="button" class="btn-personal-editar" id="volver" onclick="window.location='admin_index.php'">Volver</button>
                </div>
        </form>
    </div>
</html>


