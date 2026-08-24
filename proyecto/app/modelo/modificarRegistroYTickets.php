<?php

class ModificarRegistroYTickets
{
    private PDO $conexion;
    private string $ultimoError = "";

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Modifica un registro en REGISTRO_DIARIO.
     */
    public function modificarRegistro(int $idR, string $grupo, string $horaEntrada, string $horaFin, string $docente, string $asignatura, string $fecha, string $nombreAula, string $turno): bool
    {
        try {
            $sql = "UPDATE REGISTRO_DIARIO SET grupo = :grupo, horaEntrada = :horaEntrada, horaFin = :horaFin, docente = :docente, asignatura = :asignatura, fecha = :fecha, nombreAula = :nombreAula, turno = :turno WHERE ID_R = :idR";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
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

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            error_log("Error ModificarRegistroYTickets::modificarRegistro - " . $this->ultimoError);
            return false;
        }
    }

    /**
     * Modifica un ticket en TICKET (clave compuesta ID_T, ID_R).
     */
    public function modificarTicket(int $idT, int $idR, string $estado, string $nombreEquipo, string $nombreAlumno, string $descripcionT, string $prioridad): bool
    {
        try {
            $sql = "UPDATE TICKET SET estado = :estado, nombreEquipo = :nombreEquipo, nombreAlumno = :nombreAlumno, descripcionT = :descripcionT, prioridad = :prioridad WHERE ID_T = :idT AND ID_R = :idR";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                'estado' => $estado,
                'nombreEquipo' => $nombreEquipo,
                'nombreAlumno' => $nombreAlumno,
                'descripcionT' => $descripcionT,
                'prioridad' => $prioridad,
                'idT' => $idT,
                'idR' => $idR
            ]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->ultimoError = $e->getMessage();
            error_log("Error ModificarRegistroYTickets::modificarTicket - " . $this->ultimoError);
            return false;
        }
    }

    public function obtenerUltimoError(): string
    {
        return $this->ultimoError;
    }
}

?>
