<?php
class index
{
    var $dni;
    var $id_camion;
    var $busqueda;
    var$patente;
    var $nuevo_estado;
    var $id_viaje;
    var $indicar;
    function traer_viajes($conn)
    {
        $stmt=$conn->prepare("SELECT v.*,p.nombre,c.cantidad,c.peso, ca.patente ,ca.modelo ,e.nombre AS nombre_empresa
       FROM viajes AS v 
       INNER JOIN cargas AS c ON v.id_carga = c.id_carga 
       INNER JOIN productos AS p ON c.id_producto = p.id_producto 
       INNER JOIN empresas AS e ON v.id_empresa = e.id_empresa 
       INNER JOIN camiones AS ca ON v.id_camion = ca.id_camion 
       ORDER BY fecha DESC LIMIT 10;");
        $stmt->execute();
        $resultado = $stmt->get_result();
        $viajes=[];
        while($row = $resultado->fetch_assoc())
        {
            $viajes[]=[
                $row["id_viaje"],//0
                $row["destino"],//1
                $row["fecha"],//2
                $row["hora_entrega"],//3
                $row["estado"],//4
                $row["hora_inicio"],//5
                $row["hora_fin"],//6
                $row["cantidad"],//7
                $row["peso"],//8
                $row["nombre"],//9
                $row["nombre_empresa"],//10
                $row["patente"],//11
                $row["modelo"],//12
                $row["numero_viaje"]//13
            ];
        }
        return $viajes;
    }
    function traer_empleados($conn)
    {   
        $stmt=$conn->prepare("SELECT e.*,c.modelo,c.patente
        FROM empleados AS e 
        INNER JOIN camiones AS c ON e.id_camion = c.id_camion
        ORDER BY e.id_empleado DESC
        LIMIT 10");
        $empleados=[];
        $stmt->execute();
        $resultado = $stmt->get_result();
        while($row = $resultado->fetch_assoc())
        {
            $empleados[]=[
                $row["id_empleado"],//0
                $row["numero_documento"],//1
                $row["apellido_nombre"],//2
                $row["rol"],//3
                $row["telefono"],//4
                $row["mail"],//5
                $row["direccion"],//6
                $row["nacionalidad"],//7
                $row["modelo"],//8
                $row["patente"],//9
            ];
        }
        return $empleados;
    }
    function traer_camiones($conn)
    {
        $stmt=$conn->prepare("SELECT * FROM camiones LIMIT 5");
        $stmt->execute();
        $resultado=$stmt->get_result();
        while($row = $resultado->fetch_assoc())
        {
            $camiones[]=[
                $row["id_camion"],//0
                $row["patente"],//1
                $row["modelo"],//2
                $row["seguro"],//3
                $row["fecha_seguro"],//4
                $row["vtv"],//5
                $row["estado"]//6
            ];
        }
        return $camiones;
    }
    function buscar_camiones($conn)
    {   
        $busqueda = trim($this->busqueda);
        if(is_numeric($busqueda)) {
            // Buscar por estado exacto
            $stmt = $conn->prepare("SELECT * FROM camiones WHERE estado = ?");
            $stmt->bind_param("i", $busqueda);
        } else {
            // Buscar por patente con LIKE
            $like = "%$busqueda%";
            $stmt = $conn->prepare("SELECT * FROM camiones WHERE patente LIKE ?");
            $stmt->bind_param("s", $like);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $camiones=[];
        if($resultado->num_rows > 0)
        {
            while($row=$resultado->fetch_assoc())
            {
                $camiones[]=
                [
                $row["id_camion"],//0
                $row["patente"],//1
                $row["modelo"],//2
                $row["seguro"],//3
                $row["fecha_seguro"],//4
                $row["vtv"],//5
                $row["estado"],//6
            ];
            }
            return $camiones;
        }
    }
    function buscar_empleados($conn)
    {
        $stmt=$conn->prepare("SELECT e.*, c.modelo, c.patente 
                                  FROM empleados AS e 
                                  INNER JOIN camiones AS c ON e.id_camion = c.id_camion 
                                  WHERE numero_documento = ?");
        $stmt->bind_param("i",$this->dni);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if($resultado->num_rows > 0) 
        {   
            while($row=$resultado->fetch_assoc())
            {
               $dato[]=
                [
                $row["id_empleado"],//0
                $row["numero_documento"],//1
                $row["apellido_nombre"],//2
                $row["rol"],//3
                $row["telefono"],//4
                $row["mail"],//5
                $row["direccion"],//6
                $row["nacionalidad"],//7
                $row["modelo"],//8
                $row["patente"],//9
                ]; 
            }
            
            return $dato;
        } 
        else 
        {
        return false;
        }

    }
    function buscar_viajes($conn)
    {
        $dato=trim($this->busqueda);
        $sql = "SELECT v.*,ca.patente,ca.modelo,c.cantidad,c.peso,p.nombre AS nombre_producto, e.nombre AS nombre_empresa
        FROM viajes AS v 
        INNER JOIN camiones AS ca ON v.id_camion = ca.id_camion
        INNER JOIN cargas AS c ON v.id_carga = c.id_carga
        INNER JOIN productos AS p ON c.id_producto = p.id_producto
        INNER JOIN empresas AS e ON v.id_empresa = e.id_empresa
        WHERE ";
        $tipo = "s"; // por defecto string
    if ($this->indicar == "estado") {
        $sql .= " v.estado = ?";
    } elseif ($this->indicar == "fecha") {
        $fecha = $dato;

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha)) {
            // dd/mm/yyyy -> yyyy-mm-dd
            [$d, $m, $y] = explode('/', $fecha);
            $dato = "$y-$m-$d";
        } elseif (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $fecha)) {
            // yyyy/mm/dd -> yyyy-mm-dd
            $dato = str_replace('/', '-', $fecha);
        }
        $sql .= " v.fecha = ?";
    } else {
        $sql .= " v.numero_viaje = ?";
        $tipo = "i";
        $dato = (int)$dato;
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }
        $viajes = [];
        $stmt->bind_param($tipo,$dato);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if($resultado->num_rows > 0)
        {
            while($row=$resultado->fetch_assoc())
            {
                    $viajes[]=
                    [
                    $row["id_viaje"],//0
                    $row["destino"],//1
                    $row["fecha"],//2
                    $row["hora_entrega"],//3
                    $row["estado"],//4
                    $row["hora_inicio"],//5
                    $row["hora_fin"],//6
                    $row["cantidad"],//7
                    $row["peso"],//8
                    $row["nombre_producto"],//9
                    $row["nombre_empresa"],//10
                    $row["patente"],//11
                    $row["modelo"],//12
                    $row["numero_viaje"]
                    ];
            }
            return $viajes;
        }
        else
        {
            return false;
        }
    }
    public function estado_camion_cambio($conn) 
    {
        $stmt = $conn->prepare("UPDATE camiones SET estado = ? WHERE id_camion = ?");
        $stmt->bind_param("ii", $this->nuevo_estado, $this->id_camion);
        return $stmt->execute();
    }
    public function estado_viaje_cambio($conn) 
    {
        $estado = $this->nuevo_estado;
        if($estado==1)
        {
            $stmt = $conn->prepare("UPDATE viajes SET estado = ?, hora_inicio = NOW() WHERE id_viaje = ?");
            $stmt->bind_param("ii", $estado, $this->id_viaje);
            return $stmt->execute();
        }
        else if($estado==2)
        {
            $stmt = $conn->prepare("UPDATE viajes SET estado = ?, hora_fin = NOW() WHERE id_viaje = ?");
            $stmt->bind_param("ii", $estado, $this->id_viaje);
            return $stmt->execute();
        }
        else
        {
            $stmt = $conn->prepare("UPDATE viajes SET estado = ? WHERE id_viaje = ?");
            $stmt->bind_param("ii", $estado, $this->id_viaje);
            return $stmt->execute();
        }
        
    }
}
?>