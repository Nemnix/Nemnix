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
            r.nombreAula,
            r.docente,
            r.asignatura
        FROM TICKET AS t
        LEFT JOIN REGISTRO_DIARIO AS r
            ON r.ID_R = t.ID_R
        ORDER BY r.fecha DESC, t.ID_T DESC";

        $consulta = $this->conexion->query($sql);

        $tickets = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $tickets;
    }
}

?>
