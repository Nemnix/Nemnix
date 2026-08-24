<?php

class AccesoTickets
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista todos los tickets junto con sus datos de registro diario asociados.
     * @return array Lista de tickets con campos del ticket y del registro diario.
     */
    public function listarTickets(): array
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
            r.fecha AS fechaRegistro,
            r.asignatura,
            r.horaEntrada,
            r.horaFin,
            r.grupo,
            r.turno,
            l.nombreLaboratorio,
            l.numLaboratorio
        FROM TICKET AS t
        LEFT JOIN REGISTRO_DIARIO AS r
            ON r.ID_R = t.ID_R
        LEFT JOIN LABORATORIO AS l
            ON l.ID_L = r.ID_L
        ORDER BY r.fecha DESC, t.ID_T DESC";

        $consulta = $this->conexion->query($sql);

        $tickets = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $tickets;
    }

    /**
     * Lista todos los tickets de la tabla TICKET, sin datos de otras tablas.
     * Pensado para agruparse por ID_R del lado de PHP.
     *
     * @return array<int, array<string, mixed>> Lista de todos los tickets.
     */
    public function listarTodosLosTickets(): array
    {
        $sql = "
        SELECT
            ID_T,
            ID_R,
            estado,
            nombreEquipo,
            nombreAlumno,
            descripcionT,
            prioridad
        FROM TICKET
        ORDER BY ID_R ASC, ID_T ASC";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
