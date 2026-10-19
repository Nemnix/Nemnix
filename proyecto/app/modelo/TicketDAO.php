<?php


class TicketDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista todos los tickets con los datos de su registro y laboratorio (técnico).
     * @return array Los tickets.
     * @throws PDOException Si la consulta no puede ejecutarse.
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

    ORDER BY r.fecha DESC, t.ID_T DESC
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista los tickets de los registros de un solicitante.
     * @param string $cedula Cédula del solicitante (sale de la sesión).
     * @return array Los tickets del solicitante.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarTicketsPorCedula(string $cedula): array
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
        l.nombreLaboratorio,
        l.numLaboratorio

    FROM TICKET AS t

    INNER JOIN REGISTRO_DIARIO AS r
        ON r.ID_R = t.ID_R

    LEFT JOIN LABORATORIO AS l
        ON l.ID_L = r.ID_L

    WHERE r.cedula = :cedula

    ORDER BY r.fecha DESC, t.ID_T DESC
";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarTicket(Ticket $ticket, string $cedula): bool {
        // Se toman los datos del objeto.
        $idRegistro = $ticket->getIdRegistro();
        $estado = $ticket->getEstado();
        $nombreEquipo = $ticket->getNombreEquipo();
        $nombreAlumno = $ticket->getNombreAlumno();
        $descripcion = $ticket->getDescripcion();
        $prioridad = $ticket->getPrioridad();

        try {
            $this->conexion->beginTransaction();

            // El ticket solo se puede levantar en un registro existente del propio solicitante.
            $consultaRegistro = $this->conexion->prepare("SELECT 1 FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro AND cedula = :cedula");
            $consultaRegistro->execute(["idRegistro" => $idRegistro, "cedula" => $cedula]);

            if ($consultaRegistro->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            // El número de ticket es el siguiente dentro del registro.
            // FOR UPDATE bloquea esas filas hasta el commit, para que dos altas a la vez no tomen el mismo número.
            $consultaSiguienteId = $this->conexion->prepare("SELECT COALESCE(MAX(ID_T), 0) + 1 FROM TICKET WHERE ID_R = :idRegistro FOR UPDATE");
            $consultaSiguienteId->execute(["idRegistro" => $idRegistro]);
            $idTicket = (int) $consultaSiguienteId->fetchColumn();

            $sqlTicket = "INSERT INTO TICKET (ID_R, ID_T, estado, nombreEquipo, nombreAlumno, descripcionT, prioridad) VALUES (:idRegistro, :idTicket, :estado, :nombreEquipo, :nombreAlumno, :descripcion, :prioridad)";
            $consultaTicket = $this->conexion->prepare($sqlTicket);
            $consultaTicket->execute([
                "idRegistro" => $idRegistro,
                "idTicket" => $idTicket,
                "estado" => $estado,
                "nombreEquipo" => $nombreEquipo,
                "nombreAlumno" => $nombreAlumno,
                "descripcion" => $descripcion,
                "prioridad" => $prioridad
            ]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }

    public function modificarTicket(Ticket $ticket): bool {
        // Se toman los datos del objeto.
        $idRegistro = $ticket->getIdRegistro();
        $idTicket = $ticket->getIdTicket();
        $estado = $ticket->getEstado();
        $nombreEquipo = $ticket->getNombreEquipo();
        $nombreAlumno = $ticket->getNombreAlumno();
        $descripcion = $ticket->getDescripcion();
        $prioridad = $ticket->getPrioridad();

        try {
            $this->conexion->beginTransaction();

            // Solo se modifican tickets existentes.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM TICKET WHERE ID_R = :idRegistro AND ID_T = :idTicket");
            $consultaExiste->execute(["idRegistro" => $idRegistro, "idTicket" => $idTicket]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlTicket = "UPDATE TICKET SET estado = :estado, nombreEquipo = :nombreEquipo, nombreAlumno = :nombreAlumno, descripcionT = :descripcion, prioridad = :prioridad WHERE ID_R = :idRegistro AND ID_T = :idTicket";
            $consultaTicket = $this->conexion->prepare($sqlTicket);
            $consultaTicket->execute([
                "estado" => $estado,
                "nombreEquipo" => $nombreEquipo,
                "nombreAlumno" => $nombreAlumno,
                "descripcion" => $descripcion,
                "prioridad" => $prioridad,
                "idRegistro" => $idRegistro,
                "idTicket" => $idTicket
            ]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }

    public function eliminarTicket(int $idRegistro, int $idTicket, string $cedula): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se eliminan tickets existentes de un registro del propio solicitante.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM TICKET AS t INNER JOIN REGISTRO_DIARIO AS r ON r.ID_R = t.ID_R WHERE t.ID_R = :idRegistro AND t.ID_T = :idTicket AND r.cedula = :cedula");
            $consultaExiste->execute(["idRegistro" => $idRegistro, "idTicket" => $idTicket, "cedula" => $cedula]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            // Se borran primero los diagnósticos del ticket y después el ticket.
            foreach (["DIAGNOSTICO", "TICKET"] as $tabla) {
                $consultaEliminar = $this->conexion->prepare("DELETE FROM $tabla WHERE ID_R = :idRegistro AND ID_T = :idTicket");
                $consultaEliminar->execute(["idRegistro" => $idRegistro, "idTicket" => $idTicket]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }
}
