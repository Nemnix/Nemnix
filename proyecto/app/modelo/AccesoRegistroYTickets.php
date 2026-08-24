<?php

/**
 * Clase de acceso a datos para `REGISTRO_DIARIO` y `TICKET`.
 * Provee métodos para listar registros diarios y tickets junto
 * con los datos del registro diario asociado (LEFT JOIN).
 */
class AccesoRegistroYTickets
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista los registros de la tabla REGISTRO_DIARIO.
     * @return array Array asociativo con los registros.
     */
    public function listarRegistros(): array
    {
        $sql = "
        SELECT
            ID_R,
            grupo,
            horaEntrada,
            horaFin,
            docente,
            asignatura,
            fecha,
            nombreAula,
            turno
        FROM REGISTRO_DIARIO
        ORDER BY fecha DESC, ID_R DESC";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista los tickets junto con los datos del registro diario asociado.
     * @return array Array asociativo con los tickets y campos del registro.
     */
    public function listarTicketsConRegistro(): array
    {
        $sql = "
        SELECT
            t.ID_T,
            t.ID_R,
            t.estado,
            t.nombreEquipo,
            t.nombreAlumno,
            t.descripcionT,
            t.prioridad,
            r.grupo,
            r.horaEntrada,
            r.horaFin,
            r.docente,
            r.asignatura,
            r.fecha AS fechaRegistro,
            r.nombreAula,
            r.turno
        FROM TICKET AS t
        LEFT JOIN REGISTRO_DIARIO AS r
            ON r.ID_R = t.ID_R
        ORDER BY r.fecha DESC, t.ID_T DESC";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
