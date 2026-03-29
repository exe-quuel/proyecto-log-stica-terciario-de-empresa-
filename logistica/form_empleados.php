<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo'] !== 1) {
    header("Location: no_autorizado.php");
    exit();
}
include_once("class_empleados.php");
include("conexion.php");
$id_empleado="";
$nacionalidad="";
$apellido_nombre="";
$tipo_documento="";
$numero_documento="";
$telefono="";
$mail="";
$direccion="";
$rol="";
$id_camion="";

$objeto=new empleados();
$camion=$objeto->traer_camiones($conn);

if(isset($_GET["id_empleado"]))
{
    
    $id_empleado=$_GET["id_empleado"];
    $objeto=new empleados();
    $objeto->id_empleado=$id_empleado;
    $result=$objeto->traer_empleado($conn);
    if($result)
    {
        $apellido_nombre=$objeto->apellido_nombre;
        $tipo_documento=$objeto->tipo_documento;
        $numero_documento=$objeto->numero_documento;
        $telefono=$objeto->telefono;
        $mail=$objeto->mail;
        $direccion=$objeto->direccion;
        $rol=$objeto->rol;
        $id_camion=$objeto->id_camion;
        $nacionalidad=$objeto->nacionalidad;
    }
    else
    {
        echo "<script> alert('NO se pudo encontrar al empleado')</script>";
    }

}

if(isset($_POST["crear"]) && $_POST["oculto"]=="si")
{
    
        $objeto=new empleados();
        $objeto->numero_documento=$_POST["numero_documento"];
        if ($objeto->consultar($conn)) 
        {
            // Ya existe → preguntar si modificar
            echo "<script>
                if(confirm('El empleado ya existe. ¿Desea modificarlo?')) 
                {
                    document.getElementById('div_modificar').style.display = 'block';
                    document.getElementById('div_crear').style.display = 'none';
                }
                else 
                {
                    window.location.href = 'form_empleados.php';
                }
                </script>";

                $apellido_nombre=$objeto->apellido_nombre;
                $tipo_documento=$objeto->tipo_documento;
                $numero_documento=$objeto->numero_documento;
                $telefono=$objeto->telefono;
                $mail=$objeto->mail;
                $direccion=$objeto->direccion;
                $rol=$objeto->rol;
                $id_camion=$objeto->id_camion;
                $nacionalidad=$objeto->nacionalidad;
        }
        else
        {
            $objeto->apellido_nombre=$_POST["apellido_nombre"];
            $objeto->tipo_documento=$_POST["tipo_doc"];
            $objeto->nacionalidad=$_POST["nacionalidad"];
            $objeto->telefono=$_POST["telefono"];
            $objeto->mail=$_POST["mail"];
            $objeto->direccion=$_POST["direccion"];
            $objeto->rol=$_POST["rol"];
            $objeto->id_camion=$_POST["camiones"];
            if($objeto->crear_empleado($conn))
            {
                echo "<script> alert('Empleado Creado con Exito')</script>";
            }
            else
            {
                echo "<script> alert('ERROR al crear al empleado')</script>";
            }
        }
    }

if(isset($_POST["modificar"]) && $_POST["oculto"]=="si")
{
    
        $objeto= new empleados();
        $objeto->id_empleado=$_POST["id_empleado"];
        $objeto->apellido_nombre=$_POST["apellido_nombre"];
        $objeto->tipo_documento=$_POST["tipo_doc"];
        $objeto->nacionalidad=$_POST["nacionalidad"];
        $objeto->telefono=$_POST["telefono"];
        $objeto->mail=$_POST["mail"];
        $objeto->direccion=$_POST["direccion"];
        $objeto->rol=$_POST["rol"];
        $objeto->id_camion=$_POST["camiones"];
        if($objeto->modificar_empleado($conn))
        {
            echo "<script> alert('Empleado Modificado con Exito')</script>";
        }
        else
        {
            echo "<script> alert('ERROR al Modificar al empleado')</script>";
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
            function confirmarCrear() 
            {
                const botonCrear = document.activeElement;
                if(botonCrear && botonCrear.name === "crear") 
                {
                    if(!confirm("¿Seguro que desea ingresar un nuevo empleado?")) 
                    {
                        document.getElementById("oculto").value = "no";
                        return false;
                    }
                    // Marcamos que realmente quiere crear
                    document.getElementById("oculto").value = "si";
                }
                else if(botonCrear && botonCrear.name === "modificar")
                {
                    if(!confirm("¿Seguro que desea modificar al empleado?")) 
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
                // Obtenemos el input oculto de id_empelado
                const idempleado = document.getElementById("id_empleado").value.trim();
                // Obtenemos los divs
                const divCrear = document.getElementById("div_crear");
                const divModificar = document.getElementById("div_modificar");
                if(idempleado !== "") 
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
            function validar(event) 
            {
                let boton = event.submitter.name;
                let apellido_nombre = document.getElementById("apellido_nombre").value.trim();

                let numero_documento= document.getElementById("numero_documento").value.trim();
                let tipo_doc= document.getElementById("tipo_doc").value.trim();
                let telefono= document.getElementById("telefono").value.trim();
                let mail= document.getElementById("mail").value.trim();
                let direccion= document.getElementById("direccion").value.trim();
                let rol= document.getElementById("rol").value.trim();
                let id_camion= document.getElementById("camiones").value.trim();
                if(boton == "crear"||boton=="modificar")
                {
                    if(apellido_nombre === "")
                    {
                        alert("Debe completar el apellido y nombre");
                        document.getElementById("oculto").value="no";
                        return false;
                    } 
                  if(numero_documento === "" || numero_documento.length < 7 )
                    {
                        alert("Debe completar el numero de documento de forma correcta");
                        document.getElementById("oculto").value="no";
                        return false;
                    } 
                  if(telefono === "" && mail === "" && direccion === "")
                    {
                        alert("Debe completar por lo menos uno de los tres (telefono, mail, direccion)");
                        document.getElementById("oculto").value="no";
                        return false;
                    }
                    if(tipo_doc === ""|| id_camion === ""|| rol === "")
                    {
                        let errores = [];

                        if (tipo_doc === "") {
                            errores.push("Tipo de documento");
                        }
                        if (id_camion === "") {
                            errores.push("Camión");
                        }
                        if (rol === "") {
                            errores.push("Rol");
                        }

                        if (errores.length > 0) {
                            alert("Debe seleccionar una opción en: " + errores.join(", "));
                            document.getElementById("oculto").value="no";
                            return false; // detener el submit si estás en un formulario
                        }
                    }
                    document.getElementById("oculto").value="si";
                    return true;
                }
            }
        </script>
    </head>
    <body>
        <div class="contenedor-personal">
            <form name="empleados" action="form_empleados.php" method="post" onsubmit="return validar(event) && confirmarCrear()">
                    <h1 class="titulo-personal">Formulario de Empleados</h1>
                        <div class="card-personal">
                            <input type="hidden" name="id_empleado" id="id_empleado" value="<?php echo $id_empleado; ?>">
                            <label for="apellido_nombre">Apellido y nombre</label>
                            <input type="text" name="apellido_nombre" id="apellido_nombre" value="<?php echo $apellido_nombre; ?>"><br>
                            <label for="dni">Numero de documento</label>
                            <select name="tipo_doc" id="tipo_doc">
                                <option value="" disabled selected hidden>Tipo de Documento</option>
                                <option value="0"<?php if($tipo_documento==0){echo "selected";} ?>>DNI</option>
                                <option value="1"<?php if($tipo_documento==1){echo "selected";} ?>>Pasaporte</option>
                                <option value="2"<?php if($tipo_documento==2){echo "selected";} ?>>CI</option>
                            </select>
                            <input type="number" name="numero_documento" id="numero_documento"<?php if($numero_documento != "") echo"readonly"; ?> value="<?php echo $numero_documento; ?>"><br>
                            <label for="nacionalidad">Nacionalidad</label>
                            <input type="text" id="nacionalidad" name="nacionalidad" list="nacionalidades" value="<?php echo $nacionalidad; ?>">
                            <datalist id="nacionalidades">
                                <option value="Argentina"></option>
                                <option value="Brasilera"></option>
                                <option value="Chilena"></option>
                                <option value="Uruguaya"></option>
                                <option value="Mexicana"></option>
                                <option value="Española"></option>
                                <option value="Italiana"></option>
                                <option value="Francesa"></option>
                                <option value="Alemana"></option>
                                <option value="Japonesa"></option>
                                <option value="China"></option>
                                <option value="Canadiense"></option>
                                <option value="Estadounidense"></option>
                                <option value="Colombiana"></option>
                                <option value="Peruana"></option>
                                <option value="Boliviana"></option>
                            </datalist>
                            <label for ="telefono">Telefono</label>
                            <input type="number" name="telefono" id="telefono"value="<?php echo $telefono; ?>">
                            <br>
                            <label for="mail">Mail</label>
                            <input type="text" name="mail" id="mail"value="<?php echo $mail; ?>">
                            <br>
                            <label for="direccion">Direccion</label>
                            <input type="text" name="direccion" id="direccion"value="<?php echo $direccion; ?>">
                            <br>
                            <div id="datosEmpleado">
                                <label for="rol">Rol del Empleado</label>
                                <select name="rol" id="rol">
                                    <option value=""disabled selected hidden>seleccione</option>
                                    <option value="1"<?php if($rol==1){echo "selected";}?>>Camionero</option>
                                    <option value="0"<?php if($rol==0){echo "selected";}?>>Peon</option>
                                </select>
                                <br>
                                <label for="camiones">Camion en el que trabaja el Empleado</label>
                                <select name="camiones" id="camiones">
                                    <option value=""disabled selected hidden>Seleccione un Camion</option>
                                    <?php 
                                        if(!empty($camion))
                                        {
                                            foreach($camion AS $camiones)
                                            {
                                                $selected = ($id_camion == $camiones[0]) ? "selected" : "";
                                                ?>
                                                    <option value="<?php echo $camiones[0]; ?>" <?php echo $selected; ?>>
                                                        <?php echo $camiones[1] . " " . $camiones[2]; ?>
                                                    </option>
                                                <?php
                                            }  
                                        }
                                        else
                                        {
                                            ?><option value="">No hay camiones registrados</option><?php
                                        }
                                    ?>
                                </select>
                                </div>
                            </div>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <input type="hidden" name="oculto" id="oculto">
                                <div name="div_crear" id="div_crear" style="display:none;">
                                    <input type="submit" class="btn-personal-aceptar" name="crear" id="crear" value="Crear">
                                </div>
                                <div name="div_modificar" id="div_modificar" style="display:none;">
                                    <input type="submit" class="btn-personal-aceptar" name="modificar" id="modificar" value="Modificar">
                                </div>
                                <button type="button" class="btn-personal-editar" id="volver" onclick="window.location='admin_index.php'">Volver</button>
                            </div>
            </form>
        </div>
    </body>  
</html>