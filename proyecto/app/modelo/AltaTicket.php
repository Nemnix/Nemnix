<?php

/**
 * Clase encargada de realizar el alta de tickets asociados
 * a un registro diario.
 */
class AltaTicket
{
    private PDO $conexion;
    private string $ultimoError = "";

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Registra un nuevo ticket para un registro diario existente.
     *
     * ID_T no es autoincremental: se calcula como el siguiente número
     * de ticket dentro del registro diario indicado.
     *
     * @param int $idRegistro Identificador del registro diario (FK a REGISTRO_DIARIO).
     * @param string $estado Estado inicial del ticket.
     * @param string $nombreEquipo Nombre o número del dispositivo.
     * @param string $nombreAlumno Nombre del alumno que utilizó el equipo.
     * @param string $descripcionT Descripción/motivo del ticket.
     * @param string $prioridad Prioridad del ticket.
     *
     * @return bool TRUE si el ticket se registra correctamente, FALSE en caso contrario.
     */
    public function registrarTicket(int $idRegistro, string $estado, string $nombreEquipo, string $nombreAlumno, string $descripcionT, string $prioridad): bool
    {
        if ($idRegistro <= 0 || empty($estado) || empty($nombreEquipo) || empty($descripcionT) || empty($prioridad)) {
            $this->ultimoError = "Todos los campos son obligatorios.";
            return false;
        }

        try {
            $this->conexion->beginTransaction();

            $consultaRegistro = $this->conexion->prepare(
                "SELECT ID_R FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro"
            );
            $consultaRegistro->execute(["idRegistro" => $idRegistro]);

            if ($consultaRegistro->fetchColumn() === false) {
                $this->conexion->rollBack();
                $this->ultimoError = "El registro diario indicado no existe.";
                return false;
            }

            $consultaSiguienteId = $this->conexion->prepare(
                "SELECT COALESCE(MAX(ID_T), 0) + 1 FROM TICKET WHERE ID_R = :idRegistro"
            );
            $consultaSiguienteId->execute(["idRegistro" => $idRegistro]);
            $idTicket = (int) $consultaSiguienteId->fetchColumn();

            $sqlTicket = "INSERT INTO TICKET (ID_R, ID_T, estado, nombreEquipo, nombreAlumno, descripcionT, prioridad) VALUES (:ID_R, :ID_T, :estado, :nombreEquipo, :nombreAlumno, :descripcionT, :prioridad)";
            $consultaTicket = $this->conexion->prepare($sqlTicket);
            $consultaTicket->execute([
                "ID_R" => $idRegistro,
                "ID_T" => $idTicket,
                "estado" => $estado,
                "nombreEquipo" => $nombreEquipo,
                "nombreAlumno" => $nombreAlumno,
                "descripcionT" => $descripcionT,
                "prioridad" => $prioridad
            ]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            $this->ultimoError = $error->getMessage();
            error_log("Error en AltaTicket: " . $this->ultimoError);

            return false;
        }
    }

    public function obtenerUltimoError(): string
    {
        return $this->ultimoError;
    }
}
