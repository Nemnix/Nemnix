<?php


class IncidenciaDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Cuenta las incidencias (tickets) registradas en cada laboratorio.
     * @return array Tipo, número y cantidad de incidencias de cada laboratorio.
     * @throws PDOException Si la consulta no puede ejecutarse.
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

    WHERE t.activo = 1

    GROUP BY l.ID_L, l.nombreLaboratorio, l.numLaboratorio
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta las incidencias de prioridad alta que no están cerradas.
     * @return int La cantidad de incidencias.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function contarIncidenciasPrioridadAltaSinResolver(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM TICKET WHERE prioridad = 'Alta' AND estado <> 'Cerrado' AND activo = 1");

        return (int) $consulta->fetchColumn();
    }

    /**
     * Cuenta las incidencias que no están cerradas.
     * @return int La cantidad de incidencias.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function contarIncidenciasSinResolver(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM TICKET WHERE estado <> 'Cerrado' AND activo = 1");

        return (int) $consulta->fetchColumn();
    }

    /**
     * Cuenta los diagnósticos que requieren autorización.
     * @return int La cantidad de diagnósticos.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function contarIncidenciasRequierenAutorizacion(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM DIAGNOSTICO WHERE estadoD = 'Requiere autorización' AND activo = 1");

        return (int) $consulta->fetchColumn();
    }

    /**
     * Cuenta todas las incidencias registradas.
     * @return int La cantidad de incidencias.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function contarIncidencias(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM TICKET WHERE activo = 1");

        return (int) $consulta->fetchColumn();
    }

    /**
     * Métricas de cada laboratorio con incidencias: incidencias activas, total,
     * el equipo con más tickets y el turno con más reportes.
     * @return array Las métricas de cada laboratorio.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarMetricasPorLaboratorio(): array
    {
        $sql = "
    SELECT
        l.nombreLaboratorio,
        l.numLaboratorio,
        SUM(t.estado <> 'Cerrado') AS incidenciasActivas,

        (SELECT t2.nombreEquipo
            FROM TICKET AS t2
            INNER JOIN REGISTRO_DIARIO AS r2
                ON r2.ID_R = t2.ID_R
            WHERE r2.ID_L = l.ID_L
                AND t2.activo = 1
            GROUP BY t2.nombreEquipo
            ORDER BY COUNT(*) DESC, t2.nombreEquipo
            LIMIT 1) AS equipoMasFallos,

        (SELECT r3.turno
            FROM TICKET AS t3
            INNER JOIN REGISTRO_DIARIO AS r3
                ON r3.ID_R = t3.ID_R
            WHERE r3.ID_L = l.ID_L
                AND t3.activo = 1
            GROUP BY r3.turno
            ORDER BY COUNT(*) DESC, r3.turno
            LIMIT 1) AS turnoMasReportes,

        COUNT(*) AS totalIncidencias

    FROM TICKET AS t

    INNER JOIN REGISTRO_DIARIO AS r
        ON r.ID_R = t.ID_R

    INNER JOIN LABORATORIO AS l
        ON l.ID_L = r.ID_L

    WHERE t.activo = 1

    GROUP BY l.ID_L, l.nombreLaboratorio, l.numLaboratorio
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Suma el equipamiento de todos los laboratorios.
     * @return array Total de computadoras y total de periféricos (monitores, teclados y mouses).
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function obtenerTotalesEquipo(): array
    {
        $sql = "
    SELECT
        COALESCE(SUM(cantPC), 0) AS totalComputadoras,
        COALESCE(SUM(cantMonitor + cantTeclado + cantMouse), 0) AS totalPerifericos

    FROM EQUIPO
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}
