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

    /**
     * Actualiza un registro en la tabla REGISTRO_DIARIO.
     */
    public function actualizarRegistro(int $idR, string $grupo, string $horaEntrada, string $horaFin, string $docente, string $asignatura, string $fecha, string $nombreAula, string $turno): bool
    {
        try {
            $sql = "UPDATE REGISTRO_DIARIO SET grupo = :grupo, horaEntrada = :horaEntrada, horaFin = :horaFin, docente = :docente, asignatura = :asignatura, fecha = :fecha, nombreAula = :nombreAula, turno = :turno WHERE ID_R = :idR";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'grupo' => $grupo,
                'horaEntrada' => $horaEntrada,
                'horaFin' => $horaFin,
                'docente' => $docente,
                'asignatura' => $asignatura,
                'fecha' => $fecha,
                'nombreAula' => $nombreAula,
                'turno' => $turno,
                'idR' => $idR
            ]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error actualizarRegistro: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un ticket en la tabla TICKET.
     */
    public function actualizarTicket(int $idT, int $idR, string $estado, string $nombreEquipo, string $nombreAlumno, string $descripcionT, string $prioridad): bool
    {
        try {
            $sql = "UPDATE TICKET SET estado = :estado, nombreEquipo = :nombreEquipo, nombreAlumno = :nombreAlumno, descripcionT = :descripcionT, prioridad = :prioridad WHERE ID_T = :idT AND ID_R = :idR";
            $consulta = $this->conexion->prepare($sql);
            $consulta->execute([
                'estado' => $estado,
                'nombreEquipo' => $nombreEquipo,
                'nombreAlumno' => $nombreAlumno,
                'descripcionT' => $descripcionT,
                'prioridad' => $prioridad,
                'idT' => $idT,
                'idR' => $idR
            ]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error actualizarTicket: " . $e->getMessage());
            return false;
        }
    }
}

?>
