<?php 
class empleados
{
    var $id_empleado;
    var $id_camion;
    var $apellido_nombre;
    var $tipo_documento;
    var $numero_documento;
    var $rol;
    var $direccion;
    var $telefono;
    var $mail;
    var $nacionalidad;

    function traer_camiones($conn)
    {
        $stmt=$conn->prepare("SELECT id_camion, patente, modelo FROM camiones");
        $stmt->execute();
        $resultado=$stmt->get_result();
        $camion = [];
        if($resultado && $resultado->num_rows>0)
        {
            while($row=$resultado->fetch_assoc())
            {
                $camion[]=[
                    $row["id_camion"],
                    $row["patente"],
                    $row["modelo"]
                ];
            }
            return $camion;
        }
        else
        {
            return false;
        }
    }
    function traer_empleado($conn)
    {
        $stmt=$conn->prepare("SELECT * FROM empleados WHERE id_empleado = ?");
        $stmt->bind_param("i",$this->id_empleado);
        $stmt->execute();
        $resultado=$stmt->get_result();
        if($resultado && $resultado->num_rows>0)
        {
            $row=$resultado->fetch_assoc();
            $this->apellido_nombre=$row["apellido_nombre"];
            $this->nacionalidad=$row["nacionalidad"];
            $this->tipo_documento=$row["tipo_documento"];
            $this->numero_documento=$row["numero_documento"];
            $this->telefono=$row["telefono"];
            $this->mail=$row["mail"];
            $this->direccion=$row["direccion"];
            $this->rol=$row["rol"];
            $this->id_camion=$row["id_camion"];
            return true;
        }
        else
        {
            return false;
        }

    }
    function consultar($conn)
    {
        $stmt=$conn->prepare("SELECT * FROM empleados WHERE numero_documento = ?");
        $stmt->bind_param("i",$this->numero_documento);
        $stmt->execute();
        $result=$stmt->get_result();
        if($result && $result->num_rows>0)
        {
            $row=$result->fetch_assoc();
            $this->apellido_nombre=$row["apellido_nombre"];
            $this->nacionalidad=$row["nacionalidad"];
            $this->tipo_documento=$row["tipo_documento"];
            $this->numero_documento=$row["numero_documento"];
            $this->telefono=$row["telefono"];
            $this->mail=$row["mail"];
            $this->direccion=$row["direccion"];
            $this->rol=$row["rol"];
            $this->id_camion=$row["id_camion"];
            $stmt->close();
            return true;
        }
        else
        {
            $stmt->close();
            return false;
        }

    }
    function crear_empleado($conn)
    {
        $stmt=$conn->prepare("INSERT INTO empleados (apellido_nombre,tipo_documento,numero_documento,nacionalidad,telefono,mail,direccion,rol,id_camion) VALUES (?,?,?,?,?,?,?,?,?);");
        $stmt->bind_param("siissssii",$this->apellido_nombre,$this->tipo_documento,$this->numero_documento,$this->nacionalidad,$this->telefono,$this->mail,$this->direccion,$this->rol,$this->id_camion);
        if($stmt->execute())
        {
            return true;
        }
        else
        {
            return false;
        }

    }
    
    function modificar_empleado($conn)
{
    $stmt = $conn->prepare("UPDATE empleados SET 
        apellido_nombre = ?, 
        tipo_documento = ?, 
        telefono = ?, 
        mail = ?, 
        direccion = ?, 
        nacionalidad = ?, 
        rol = ?, 
        id_camion = ? 
        WHERE id_empleado = ?");

    $stmt->bind_param(
        "sisssssii",
        $this->apellido_nombre,
        $this->tipo_documento,
        $this->telefono,
        $this->mail,
        $this->direccion,
        $this->nacionalidad,
        $this->rol,
        $this->id_camion,
        $this->id_empleado
    );

    if($stmt->execute())
    {
        $stmt->close();
        return true;
    }
    else
    {
        $stmt->close();
        return false;
    }
}
function modificar_empleado_admin($conn)
{
    $stmt = $conn->prepare("UPDATE empleados SET 
        apellido_nombre = ?, 
        tipo_documento = ?, 
        telefono = ?, 
        mail = ?, 
        direccion = ?, 
        nacionalidad = ? 
        WHERE id_empleado = ?");

    $stmt->bind_param(
        "siissssi",
        $this->apellido_nombre,
        $this->tipo_documento,
        $this->telefono,
        $this->mail,
        $this->direccion,
        $this->nacionalidad,
        $this->id_empleado
    );

    if($stmt->execute())
    {
        $stmt->close();
        return true;
    }
    else
    {
        $stmt->close();
        return false;
    }
}
}
?>