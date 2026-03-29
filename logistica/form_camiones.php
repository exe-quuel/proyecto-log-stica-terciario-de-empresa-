<?php 
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 1) {
    header("Location: no_autorizado.php");
    exit();
}
include_once("class_camion.php");
include("conexion.php");
$id_camion="";
$patente="";
$modelo="";
$seguro="";
$fecha_seguro="";
$vtv="";

if(isset($_GET["editar_camion"]))
{
    $id_camion=$_GET["id_camion"];
    $objeto= new camion();
    $objeto->id_camion=$id_camion;
    if($result=$objeto->traer_camion($conn))
    {
        $patente=$objeto->patente;
        $modelo=$objeto->modelo;
        $seguro=$objeto->seguro;
        $fecha_seguro=$objeto->fecha_seguro;
        $vtv=$objeto->vtv;
    }
    else
    {
        echo"no hay datos";
    }
}

if(isset($_POST["crear"])&& $_POST["oculto"]=="si")
{
    $objeto = new camion();
    $objeto->patente=strtoupper($_POST["patente"]);
    if ($objeto->consultar($conn)) 
    {
        // Ya existe → preguntar si modificar
        echo "<script>
            if(confirm('El camión ya existe. ¿Desea modificarlo?')) 
            {
                document.getElementById('div_modificar').style.display = 'block';
                document.getElementById('div_crear').style.display = 'none';
            }
            else 
            {
                window.location.href = 'form_camiones.php';
            }
            </script>";
            $id_camion=$objeto->id_camion;
            $patente=$objeto->patente;
            $modelo=$objeto->modelo;
            $seguro=$objeto->seguro;
            $fecha_seguro=$objeto->fecha_seguro;
            $vtv=$objeto->vtv;
    } 
    else 
    {
        // No existe → crear
        $objeto->modelo= $_POST["modelo"];
        $objeto->seguro= $_POST["seguro"];
        $objeto->fecha_seguro= $_POST["fecha_seguro"];
        $objeto->vtv= $_POST["vtv"];

        if ($objeto->crear_camion($conn)) 
        {
            echo " Se creó el camión";
        } 
        else    
        {
            echo " Error al crear";
        }
    }
}
if(isset($_POST["modificar"])&& $_POST["oculto"]=="si")
{
    $objeto= new camion();
    $objeto->id_camion=$_POST["id_camion"];
    $objeto->patente=strtoupper($_POST["patente"]);
    $objeto->modelo=$_POST["modelo"];
    $objeto->seguro=$_POST["seguro"];
    $objeto->fecha_seguro=$_POST["fecha_seguro"];
    $objeto->vtv=$_POST["vtv"];
    if($objeto->modificar_camion($conn))
    {
        echo "Se modifico";
    }
    else
    {
        echo "No se modifico";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
    <meta charset="UTF-8"> <!-- Soporte para acentos y caracteres especiales -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Adaptación a móviles -->
    <head>
        <header class="header">
            <div class="logo">
                <img src="imagenes/logo_usu.png" alt="Logo"style="border-radius: 50%;">
                <h2>Cuenta de <span style="color: #1a1aff;"><?= $_SESSION['user'] ?></span></h2>
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
        👤 <?= $_SESSION['user'] ?> ▾
      </span>

      <!-- Submenú del usuario -->
      <ul class="submenu-usuario">
        <li><a href="logout.php">Cerrar sesión</a></li>
      </ul>
    </li>
  </ul>
        </header>
        <link rel="stylesheet" href="Estilos3.css"/>
        <script>
            function validar(event) 
            {
                let boton = event.submitter.name;
                let patente = document.getElementById("patente").value.trim();
                let modelo = document.getElementById("modelo").value.trim();
                let regexPatente = /^[A-Z]{2,3}[0-9]{3}[A-Z]{0,2}$/i;
                if(boton == "crear"||boton=="modificar")
                {
                   if(patente === "") 
                    {
                        alert("La patente es obligatoria");
                        document.getElementById("oculto").value = "no";
                        return false; 
                    }
                    if(!regexPatente.test(patente)) 
                    {
                        alert("Formato de patente inválido. Ej: ABC123DE o CAR123");
                        document.getElementById("oculto").value = "no";
                        return false;
                    }
                    if(modelo === "") 
                    {
                            alert("El modelo es obligatorio");
                            document.getElementById("oculto").value = "no";
                        return false;
                    }
                    document.getElementById("oculto").value = "si";
                    return true; 
                }
            }
            function confirmarCrear() 
            {
                const botonCrear = document.activeElement;
                if(botonCrear && botonCrear.name === "crear") 
                {
                    if(!confirm("¿Seguro que desea crear el camión?")) 
                    {
                        document.getElementById("oculto").value = "no";
                        return false;
                    }
                    // Marcamos que realmente quiere crear
                    document.getElementById("oculto").value = "si";
                }
                else if(botonCrear && botonCrear.name === "modificar")
                {
                    if(!confirm("¿Seguro que desea modificar el camión?")) 
                    {
                        document.getElementById("oculto").value = "no";
                        return false;
                    }
                    // Marcamos que realmente quiere crear
                    document.getElementById("oculto").value = "si";
                }

                return true;
            }
            document.addEventListener("DOMContentLoaded", function() 
            {
                // Obtenemos el input oculto de id_camion
                const idCamion = document.getElementById("id_camion").value.trim();
                // Obtenemos los divs
                const divCrear = document.getElementById("div_crear");
                const divModificar = document.getElementById("div_modificar");
                if(idCamion !== "") 
                {
                    // Si tiene valor, mostrar modificar
                    divModificar.style.display = "block";
                    divCrear.style.display = "none";
                }
                else 
                {
                    // Si está vacío, mostrar crear
                    divCrear.style.display = "block";
                    divModificar.style.display = "none";
                }
            });
        </script>
    </head>
    <body>
        <div class="contenedor-personal">
            <h1 class="titulo-personal">Formulario de Camiones</h1>
            <form name="camiones" method="post" action="form_camiones.php" onsubmit="return validar(event) && confirmarCrear()">
                <div class="card-personal">    
                    <input type="hidden" name="id_camion" id="id_camion" value="<?php echo $id_camion; ?>">

                    <label for="patente">Patente</label>
                    <input type="text" name="patente" id="patente" placeholder="AB123CF o BRE254 para la patente..." value="<?php echo $patente; ?>"><br>

                    <label for="modelo">Modelo</label>
                    <input type="text" name="modelo" id="modelo" value="<?php echo $modelo; ?>"><br>

                    <label for="seguro">Seguro del camion</label>
                    <input type="text" name="seguro" id="seguro" value="<?php echo $seguro; ?>"><br>

                    <label for="fecha_seguro">Fecha de Vencimiento del Seguro</label>
                    <input type="date" name="fecha_seguro" id="fecha_seguro" value="<?php echo $fecha_seguro; ?>"><br>

                    <label for="vtv">Fecha de vencimiento de la VTV</label>
                    <input type="date" name="vtv" id="vtv"value="<?php echo $vtv; ?>">
                </div>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="hidden" name="oculto" id="oculto">
                    <div name="div_crear" id="div_crear" style="display:none">
                    <input type="submit" class="btn-personal-aceptar" name="crear" id="crear" value="Crear">
                    </div>
                    <div name="div_modificar" id="div_modificar" style="display:none">
                    <input type="submit" class="btn-personal-aceptar" name="modificar" id="modificar" value="Modificar">
                    </div>
                    <button type="button" class="btn-personal-editar" id="volver" onclick="window.location='admin_index.php'">Volver</button>
                </div>
            </form>
        </div>
    </body>
</html>