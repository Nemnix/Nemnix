<?php

class AccesoSolicita{
private PDO $conexion;

/**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
public function __construct (PDO $conexion) {
$this->conexion = $conexion;
    }

/**
     * Lista todos los registros de la tabla SOLICITA junto con los
     * datos del laboratorio y del solicitante asociados.
     * @return array Los registros de solicita, con los datos de laboratorio y solicitante.
     */
public function listarSolicita (): array {
$sql = "
        SELECT
        s.ID_L,
        s.cedula,
        s.descripcionSolicitud,
        s.Fecha,
        s.Gmail,

        l.cedulaA,
        l.nombreLaboratorio,
        l.numLaboratorio,

        so.*

        FROM SOLICITA AS s

        LEFT JOIN LABORATORIO AS l
            ON l.ID_L = s.ID_L

        LEFT JOIN SOLICITANTE AS so
            ON so.cedula = s.cedula";

$consulta = $this->conexion->query($sql);

$solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);

return $solicitudes;
    }
 }

?>