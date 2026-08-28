<?php

/**
 * Proporciona operaciones de modificación para REGISTRO_DIARIO y TICKET.
 */
class ModificarRegistroYTickets
{
    private PDO $conexion;
    private string $ultimoError = "";

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Modifica un registro en REGISTRO_DIARIO.
     * @param int $idR Identificador del registro diario.
     * @param string $grupo Grupo actualizado.
     * @param string $horaEntrada Hora de entrada actualizada.
     * @param string $horaFin Hora de salida actualizada.
     * @param string $docente Docente actualizado.
     * @param string $asignatura Asignatura actualizada.
     * @param string $fecha Fecha actualizada en formato válido para SQL.
     * @param string $nombreAula Aula actualizada.
     * @param string $turno Turno actualizado.
     * @return bool TRUE si se actualizó el registro; FALSE en caso contrario.
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
     * @param int $idT Identificador del ticket dentro del registro.
     * @param int $idR Identificador del registro diario asociado.
     * @param string $estado Estado actualizado del ticket.
     * @param string $nombreEquipo Nombre o número del dispositivo actualizado.
     * @param string $nombreAlumno Nombre del alumno actualizado.
     * @param string $descripcionT Descripción/motivo del ticket actualizado.
     * @param string $prioridad Prioridad actualizada.
     * @return bool TRUE si se actualizó el ticket; FALSE en caso contrario.
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

    /**
     * Devuelve el último error registrado por una operación de modificación.
     * @return string Mensaje del último error; cadena vacía si no hubo error.
     */
    public function obtenerUltimoError(): string
    {
        return $this->ultimoError;
    }
}

?>
