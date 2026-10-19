<?php


class DiagnosticoDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista todos los diagnósticos.
     * @return array Los diagnósticos con su ticket y registro.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarDiagnosticos(): array
    {
        $sql = "
    SELECT
        d.ID_D,
        d.ID_T,
        d.ID_R,
        d.fechaD,
        d.estadoD,
        d.descripcionD

    FROM DIAGNOSTICO AS d

    ORDER BY d.fechaD DESC, d.ID_D DESC
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Indica si existe el ticket que se quiere diagnosticar.
     * @param int $idRegistro Registro diario del ticket.
     * @param int $idTicket Número del ticket dentro del registro.
     * @return bool TRUE si el ticket existe.
     */
    public function existeTicket(int $idRegistro, int $idTicket): bool {
        $sql = "SELECT 1 FROM TICKET WHERE ID_R = :idRegistro AND ID_T = :idTicket";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["idRegistro" => $idRegistro, "idTicket" => $idTicket]);

        return $consulta->fetchColumn() !== false;
    }

    public function registrarDiagnostico(Diagnostico $diagnostico): bool {
        // Se toman los datos del objeto.
        $idRegistro = $diagnostico->getIdRegistro();
        $idTicket = $diagnostico->getIdTicket();
        $cedula = $diagnostico->getCedula();
        $estado = $diagnostico->getEstado();
        $fecha = $diagnostico->getFecha();
        $descripcion = $diagnostico->getDescripcion();

        try {
            $this->conexion->beginTransaction();

            $sqlDiagnostico = "INSERT INTO DIAGNOSTICO (ID_R, ID_T, cedula, estadoD, fechaD, descripcionD) VALUES (:idRegistro, :idTicket, :cedula, :estado, :fecha, :descripcion)";
            $consultaDiagnostico = $this->conexion->prepare($sqlDiagnostico);
            $consultaDiagnostico->execute([
                "idRegistro" => $idRegistro,
                "idTicket" => $idTicket,
                "cedula" => $cedula,
                "estado" => $estado,
                "fecha" => $fecha,
                "descripcion" => $descripcion
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

    public function modificarDiagnostico(Diagnostico $diagnostico): bool {
        // Se toman los datos del objeto.
        $idDiagnostico = $diagnostico->getIdDiagnostico();
        $estado = $diagnostico->getEstado();
        $fecha = $diagnostico->getFecha();
        $descripcion = $diagnostico->getDescripcion();

        try {
            $this->conexion->beginTransaction();

            // Solo se modifican diagnósticos existentes.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM DIAGNOSTICO WHERE ID_D = :idDiagnostico");
            $consultaExiste->execute(["idDiagnostico" => $idDiagnostico]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlDiagnostico = "UPDATE DIAGNOSTICO SET estadoD = :estado, fechaD = :fecha, descripcionD = :descripcion WHERE ID_D = :idDiagnostico";
            $consultaDiagnostico = $this->conexion->prepare($sqlDiagnostico);
            $consultaDiagnostico->execute([
                "estado" => $estado,
                "fecha" => $fecha,
                "descripcion" => $descripcion,
                "idDiagnostico" => $idDiagnostico
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

    public function eliminarDiagnostico(int $idDiagnostico): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se eliminan diagnósticos existentes.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM DIAGNOSTICO WHERE ID_D = :idDiagnostico");
            $consultaExiste->execute(["idDiagnostico" => $idDiagnostico]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            $consultaEliminar = $this->conexion->prepare("DELETE FROM DIAGNOSTICO WHERE ID_D = :idDiagnostico");
            $consultaEliminar->execute(["idDiagnostico" => $idDiagnostico]);

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
