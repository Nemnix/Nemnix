<?php

/**
 * Proporciona las operaciones de consulta de incidencias (tickets) para el panel administrador.
 */
class AccesoIncidencias
{
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista la cantidad de incidencias (tickets) agrupadas por laboratorio.
     * @return array Los laboratorios con su cantidad de incidencias asociadas.
     */
    public function listarIncidenciasPorLaboratorio(): array
    {
        $sql = "
        SELECT
        l.nombreLaboratorio,
        l.numLaboratorio,
        COUNT(*) AS cantidadIncidencias

        FROM TICKET AS t

        LEFT JOIN REGISTRO_DIARIO AS r
            ON r.ID_R = t.ID_R

        LEFT JOIN LABORATORIO AS l
            ON l.ID_L = r.ID_L

        GROUP BY l.ID_L, l.nombreLaboratorio, l.numLaboratorio";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta las incidencias de prioridad alta que no están cerradas.
     * @return int La cantidad de incidencias con prioridad alta sin resolver.
     */
    public function contarIncidenciasPrioridadAltaSinResolver(): int
    {
        $sql = "SELECT COUNT(*) FROM TICKET WHERE prioridad = 'Alta' AND estado <> 'Cerrado'";

        $consulta = $this->conexion->query($sql);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Cuenta las incidencias que aún no están cerradas.
     * @return int La cantidad de incidencias sin resolver.
     */
    public function contarIncidenciasSinResolver(): int
    {
        $sql = "SELECT COUNT(*) FROM TICKET WHERE estado <> 'Cerrado'";

        $consulta = $this->conexion->query($sql);

        return (int) $consulta->fetchColumn();
    }

    /**
     * Cuenta los diagnósticos que requieren autorización.
     * @return int La cantidad de incidencias que requieren autorización.
     */
    public function contarIncidenciasRequierenAutorizacion(): int
    {
        $sql = "SELECT COUNT(*) FROM DIAGNOSTICO WHERE estadoD = 'Requiere autorización'";

        $consulta = $this->conexion->query($sql);

        return (int) $consulta->fetchColumn();
    }
}

?>
