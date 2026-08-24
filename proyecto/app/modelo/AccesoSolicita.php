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

        l.nombreLaboratorio,
        l.numLaboratorio,

        u.nombre,
        u.apellido

        FROM SOLICITA AS s

        LEFT JOIN LABORATORIO AS l
            ON l.ID_L = s.ID_L

        LEFT JOIN USUARIO AS u
            ON u.cedula = s.cedula";

$consulta = $this->conexion->query($sql);

$solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);

return $solicitudes;
    }

    /**
     * Cuenta la cantidad total de solicitudes registradas.
     * @return int La cantidad de solicitudes.
     */
    public function contarSolicitudes (): int {
$sql = "SELECT COUNT(*) FROM SOLICITA";

$consulta = $this->conexion->query($sql);

return (int) $consulta->fetchColumn();
    }

    /**
     * Lista la cantidad de solicitudes agrupadas por laboratorio.
     * @return array Los laboratorios con su cantidad de solicitudes asociadas.
     */
    public function listarSolicitudesPorLaboratorio (): array {
$sql = "
        SELECT
        l.nombreLaboratorio,
        l.numLaboratorio,
        COUNT(*) AS cantidadSolicitudes

        FROM SOLICITA AS s

        LEFT JOIN LABORATORIO AS l
            ON l.ID_L = s.ID_L

        GROUP BY l.ID_L, l.nombreLaboratorio, l.numLaboratorio";

$consulta = $this->conexion->query($sql);

return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
 }

?>