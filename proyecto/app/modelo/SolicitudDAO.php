<?php


class SolicitudDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista todas las solicitudes con su laboratorio y los datos del solicitante (administrador).
     * @return array Las solicitudes.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarSolicitudes(): array
    {
        $sql = "
    SELECT
        s.ID_L,
        s.cedula,
        l.nombreLaboratorio,
        l.numLaboratorio,
        s.fecha,
        s.descripcionSolicitud,
        s.gmail,
        u.nombre,
        u.apellido

    FROM SOLICITA AS s

    LEFT JOIN LABORATORIO AS l
        ON l.ID_L = s.ID_L

    LEFT JOIN USUARIO AS u
        ON u.cedula = s.cedula
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista las solicitudes de un solicitante.
     * @param string $cedula Cédula del solicitante (sale de la sesión).
     * @return array Las solicitudes del solicitante.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarSolicitudesPorCedula(string $cedula): array
    {
        $sql = "
    SELECT
        s.ID_L,
        l.nombreLaboratorio,
        l.numLaboratorio,
        s.fecha,
        s.descripcionSolicitud,
        s.gmail

    FROM SOLICITA AS s

    LEFT JOIN LABORATORIO AS l
        ON l.ID_L = s.ID_L

    WHERE s.cedula = :cedula
";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta las solicitudes por laboratorio (resumen del administrador).
     * @return array Tipo, número y cantidad de solicitudes de cada laboratorio.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarSolicitudesPorLaboratorio(): array
    {
        $sql = "
    SELECT
        l.nombreLaboratorio,
        l.numLaboratorio,
        COUNT(*) AS cantidadSolicitudes

    FROM SOLICITA AS s

    LEFT JOIN LABORATORIO AS l
        ON l.ID_L = s.ID_L

    GROUP BY l.ID_L, l.nombreLaboratorio, l.numLaboratorio
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta todas las solicitudes.
     * @return int La cantidad de solicitudes.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function contarSolicitudes(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM SOLICITA");

        return (int) $consulta->fetchColumn();
    }

    /**
     * Indica si el solicitante ya tiene una solicitud para ese laboratorio.
     * @param int $idLaboratorio Identificador del laboratorio.
     * @param string $cedula Cédula del solicitante.
     * @return bool TRUE si la solicitud existe.
     */
    public function existeSolicitud(int $idLaboratorio, string $cedula): bool {
        $sql = "SELECT 1 FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["idLaboratorio" => $idLaboratorio, "cedula" => $cedula]);

        return $consulta->fetchColumn() !== false;
    }

    public function registrarSolicitud(Solicitud $solicitud): bool {
        // Se toman los datos del objeto.
        $cedula = $solicitud->getCedula();
        $idLaboratorio = $solicitud->getIdLaboratorio();
        $fecha = $solicitud->getFecha();
        $descripcionSolicitud = $solicitud->getDescripcionSolicitud();
        $gmail = $solicitud->getGmail();

        try {
            $this->conexion->beginTransaction();

            // Se busca si el solicitante ya pidió ese laboratorio.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula");
            $consultaExiste->execute(["idLaboratorio" => $idLaboratorio, "cedula" => $cedula]);

            if ($consultaExiste->fetchColumn() !== false) {
                // Existe: es un duplicado.
                $this->conexion->rollBack();
                return false;
            }

            // Cada solicitud guarda su propio gmail de contacto.
            $sqlSolicitud = "INSERT INTO SOLICITA (cedula, ID_L, descripcionSolicitud, fecha, gmail) VALUES (:cedula, :idLaboratorio, :descripcionSolicitud, :fecha, :gmail)";
            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute([
                "cedula" => $cedula,
                "idLaboratorio" => $idLaboratorio,
                "descripcionSolicitud" => $descripcionSolicitud,
                "fecha" => $fecha,
                "gmail" => $gmail
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

    public function modificarSolicitud(int $idLaboratorioActual, Solicitud $solicitud): bool {
        // Se toman los datos del objeto.
        $cedula = $solicitud->getCedula();
        $idLaboratorioNuevo = $solicitud->getIdLaboratorio();
        $fecha = $solicitud->getFecha();
        $descripcionSolicitud = $solicitud->getDescripcionSolicitud();
        $gmail = $solicitud->getGmail();

        try {
            $this->conexion->beginTransaction();

            // Solo se modifican solicitudes existentes del propio solicitante.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula");
            $consultaExiste->execute(["idLaboratorio" => $idLaboratorioActual, "cedula" => $cedula]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            // Cambia el laboratorio de la solicitud, sin modificar el laboratorio en sí.
            $sqlSolicitud = "UPDATE SOLICITA SET ID_L = :idLaboratorioNuevo, descripcionSolicitud = :descripcionSolicitud, fecha = :fecha, gmail = :gmail WHERE ID_L = :idLaboratorioActual AND cedula = :cedula";
            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute([
                "idLaboratorioNuevo" => $idLaboratorioNuevo,
                "descripcionSolicitud" => $descripcionSolicitud,
                "fecha" => $fecha,
                "gmail" => $gmail,
                "idLaboratorioActual" => $idLaboratorioActual,
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

    public function eliminarSolicitud(int $idLaboratorio, string $cedula): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se eliminan solicitudes existentes.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula");
            $consultaExiste->execute(["idLaboratorio" => $idLaboratorio, "cedula" => $cedula]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            $consultaEliminar = $this->conexion->prepare("DELETE FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula");
            $consultaEliminar->execute(["idLaboratorio" => $idLaboratorio, "cedula" => $cedula]);

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
