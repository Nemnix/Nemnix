<?php


class RegistroDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista los registros diarios del solicitante junto con su laboratorio.
     * @param string $cedula Cédula del solicitante (sale de la sesión).
     * @return array Los registros del solicitante.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarRegistros(string $cedula): array
    {
        $sql = "
    SELECT
        r.ID_R,
        r.fecha,
        l.nombreLaboratorio,
        l.numLaboratorio,
        r.horaEntrada,
        r.horaFin,
        r.grupo,
        r.asignatura,
        r.turno

    FROM REGISTRO_DIARIO AS r

    INNER JOIN LABORATORIO AS l
        ON l.ID_L = r.ID_L

    WHERE r.cedula = :cedula
";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarRegistro(Registro $registro): bool {
        // Se toman los datos del objeto.
        $cedula = $registro->getCedula();
        $idLaboratorio = $registro->getIdLaboratorio();
        $grupo = $registro->getGrupo();
        $horaEntrada = $registro->getHoraEntrada();
        $horaFin = $registro->getHoraFin();
        $asignatura = $registro->getAsignatura();
        $fecha = $registro->getFecha();
        $turno = $registro->getTurno();

        try {
            $this->conexion->beginTransaction();

            $sqlRegistro = "INSERT INTO REGISTRO_DIARIO (ID_L, cedula, grupo, horaEntrada, horaFin, asignatura, fecha, turno) VALUES (:idLaboratorio, :cedula, :grupo, :horaEntrada, :horaFin, :asignatura, :fecha, :turno)";
            $consultaRegistro = $this->conexion->prepare($sqlRegistro);
            $consultaRegistro->execute([
                "idLaboratorio" => $idLaboratorio,
                "cedula" => $cedula,
                "grupo" => $grupo,
                "horaEntrada" => $horaEntrada,
                "horaFin" => $horaFin,
                "asignatura" => $asignatura,
                "fecha" => $fecha,
                "turno" => $turno
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

    public function modificarRegistro(Registro $registro): bool {
        // Se toman los datos del objeto.
        $idRegistro = $registro->getIdRegistro();
        $cedula = $registro->getCedula();
        $idLaboratorio = $registro->getIdLaboratorio();
        $grupo = $registro->getGrupo();
        $horaEntrada = $registro->getHoraEntrada();
        $horaFin = $registro->getHoraFin();
        $asignatura = $registro->getAsignatura();
        $fecha = $registro->getFecha();
        $turno = $registro->getTurno();

        try {
            $this->conexion->beginTransaction();

            // Solo se modifican registros existentes del propio solicitante.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro AND cedula = :cedula");
            $consultaExiste->execute(["idRegistro" => $idRegistro, "cedula" => $cedula]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            // Cambia el laboratorio del registro, sin modificar el laboratorio en sí.
            $sqlRegistro = "UPDATE REGISTRO_DIARIO SET ID_L = :idLaboratorio, grupo = :grupo, horaEntrada = :horaEntrada, horaFin = :horaFin, asignatura = :asignatura, fecha = :fecha, turno = :turno WHERE ID_R = :idRegistro AND cedula = :cedula";
            $consultaRegistro = $this->conexion->prepare($sqlRegistro);
            $consultaRegistro->execute([
                "idLaboratorio" => $idLaboratorio,
                "grupo" => $grupo,
                "horaEntrada" => $horaEntrada,
                "horaFin" => $horaFin,
                "asignatura" => $asignatura,
                "fecha" => $fecha,
                "turno" => $turno,
                "idRegistro" => $idRegistro,
                "cedula" => $cedula
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

    public function eliminarRegistro(int $idRegistro, string $cedula): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se eliminan registros existentes del propio solicitante.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM REGISTRO_DIARIO WHERE ID_R = :idRegistro AND cedula = :cedula");
            $consultaExiste->execute(["idRegistro" => $idRegistro, "cedula" => $cedula]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            // Se borran primero los diagnósticos y los tickets del registro, y después el registro.
            foreach (["DIAGNOSTICO", "TICKET", "REGISTRO_DIARIO"] as $tabla) {
                $consultaEliminar = $this->conexion->prepare("DELETE FROM $tabla WHERE ID_R = :idRegistro");
                $consultaEliminar->execute(["idRegistro" => $idRegistro]);
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
