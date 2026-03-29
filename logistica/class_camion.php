<?php 
class camion
{
    var $id_camion;
    var $patente;
    var $modelo;
    var $seguro;
    var $fecha_seguro;
    var $vtv;

    function consultar($conn)
{
    $stmt = $conn->prepare("SELECT * FROM camiones WHERE patente = ?");
    $stmt->bind_param("s", $this->patente);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Encontrado → cargo datos
        $row = $result->fetch_assoc();
        $this->id_camion   = $row["id_camion"];
        $this->modelo      = $row["modelo"];
        $this->seguro      = $row["seguro"];
        $this->fecha_seguro= $row["fecha_seguro"];
        $this->vtv         = $row["vtv"];

        $stmt->close();
        return true; 
    } else {
        $stmt->close();
        return false; 
    }
}
    function crear_camion($conn)
    {
        $stmt=$conn->prepare("INSERT INTO camiones (patente,modelo,seguro,fecha_seguro,vtv,estado) VALUES (?,?,?,?,?, 0);");
        $stmt->bind_param("sssss",$this->patente,$this->modelo,$this->seguro,$this->fecha_seguro,$this->vtv);
        if($stmt->execute())
        {
            return true;
        }
        else
        {
            return false;
        }    
    }
    function traer_camion($conn)
    {
        $stmt=$conn->prepare("SELECT * FROM camiones WHERE id_camion = ?;");
        $stmt->bind_param("i",$this->id_camion);
        $stmt->execute();
        $result=$stmt->get_result();
        if($result && $result->num_rows>0)
        {   
            $row=$result->fetch_assoc();
            $this->patente=$row["patente"];
            $this->modelo=$row["modelo"];
            $this->seguro=$row["seguro"];
            $this->fecha_seguro=$row["fecha_seguro"];
            $this->vtv=$row["vtv"];
            return true;
        }
        else
        {
            return false;
        }
    }
    function modificar_camion($conn)
    {
        $stmt=$conn->prepare("UPDATE camiones SET patente = ?, modelo = ?, seguro = ?, fecha_seguro = ?, vtv = ? WHERE id_camion = ?");
        $stmt->bind_param("sssssi",$this->patente,$this->modelo,$this->seguro,$this->fecha_seguro,$this->vtv,$this->id_camion);
        if($stmt->execute())
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }
}
?>