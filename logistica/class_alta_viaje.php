<?php
class  class_alta_viaje
{
    var $id_viaje;
    var $numero_viaje;
    var $fecha;
    var $hora_entrega;
    var $origen;
    var $destino;
    var $id_camion;
    var $nombre_empresa;
    var $nombre_producto;
    var $cantidad;
    var $peso;
    var $id_carga;
    var $foto_entrega;
    function traer_info($conn)
    {
        $camiones=[];
        $productos=[];
        $empresas=[];
        $ubi=[];
        $empleados=[];

        $stmt=$conn->prepare("SELECT id_camion, patente, modelo FROM camiones WHERE estado = 0");
        $stmt->execute();
        $result=$stmt->get_result();
        while($row = $result->fetch_assoc())
        {
            $camiones[]=[
                $row["id_camion"],//0
                $row["patente"],//1
                $row["modelo"],//2
            ];
        }

        $stmt=$conn->prepare("SELECT id_empresa, nombre AS nombre_empresa FROM empresas");
        $stmt->execute();
        $result=$stmt->get_result();
        while($row = $result->fetch_assoc())
        {
            $empresas[]=[
                $row["id_empresa"],//0
                $row["nombre_empresa"],//1
            ];
        }

        $stmt=$conn->prepare("SELECT id_producto, nombre AS nombre_producto FROM productos");
        $stmt->execute();
        $result=$stmt->get_result();
        while($row = $result->fetch_assoc())
        {
            $productos[]=[
                $row["id_producto"],//0
                $row["nombre_producto"],//1
            ];
        }

        $stmt=$conn->prepare("SELECT DISTINCT destino, origen FROM viajes");
        $stmt->execute();
        $result=$stmt->get_result();
        while($row = $result->fetch_assoc())
        {
            $ubi[]=[
                $row["destino"],//0
                $row["origen"],//1
            ];
        }
        $stmt=$conn->prepare("SELECT DISTINCT id_empleado, apellido_nombre, rol FROM empleados");
        $stmt->execute();
        $result=$stmt->get_result();
        while($row = $result->fetch_assoc())
        {
            $empleados[]=[
                $row["id_empleado"],
                $row["apellido_nombre"],
                $row["rol"]
            ];
        }

        return 
        [
            "camiones"  => $camiones,
            "empresas"  => $empresas,
            "productos" => $productos,
            "ubi"       => $ubi,
            "empleados" => $empleados
        ];
    }
    function traer_viaje($conn)
    {
        $stmt=$conn->prepare("SELECT v.*,p.nombre AS nombre_producto,c.cantidad, c.peso, e.nombre AS nombre_empresa FROM viajes AS v
        INNER JOIN cargas AS c ON v.id_carga = c.id_carga
        INNER JOIN productos AS p ON c.id_producto = p.id_producto 
        INNER JOIN empresas AS e ON v.id_empresa=e.id_empresa 
         WHERE id_viaje = ?");
        $stmt->bind_param("i",$this->id_viaje);
        $stmt->execute();
        $result=$stmt->get_result();
        if($result && $result->num_rows>0)
        {
            $row=$result->fetch_assoc();
            $this->numero_viaje=$row["numero_viaje"];
            $this->origen=$row["origen"];
            $this->destino=$row["destino"];
            $this->fecha=$row["fecha"];
            $this->hora_entrega=$row["hora_entrega"];
            $this->id_camion = $row["id_camion"];
            $this->nombre_empresa=$row["nombre_empresa"];
            $this->nombre_producto=$row["nombre_producto"];
            $this->cantidad=$row["cantidad"];
            $this->peso=$row["peso"];
            $this->id_carga=$row["id_carga"];
            $this->foto_entrega=$row["foto_entrega"];
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>