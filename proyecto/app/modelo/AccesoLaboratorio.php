<?php

class AccesoLaboratorio{
private PDO $conexion;

/**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
public function __construct (PDO $conexion) {
$this->conexion = $conexion;
    }

/**
     * Lista todos los registros de la tabla LABORATORIO.
     * @return array Los registros de laboratorio, con todos sus atributos.
     */
public function listarLaboratorio (): array {
$sql = "SELECT * FROM LABORATORIO";

$consulta = $this->conexion->query($sql);

$laboratorios = $consulta->fetchAll(PDO::FETCH_ASSOC);

return $laboratorios;
    }
 }

?>