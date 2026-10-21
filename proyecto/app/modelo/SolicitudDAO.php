<?php


class SolicitudDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista las solicitudes activas con su laboratorio y los datos del solicitante (administrador).
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
        s.hora,
        s.descripcionSolicitud,
        s.gmail,
        u.nombre,
        u.apellido

    FROM SOLICITA AS s

    LEFT JOIN LABORATORIO AS l
        ON l.ID_L = s.ID_L

    LEFT JOIN USUARIO AS u
        ON u.cedula = s.cedula

    WHERE s.activo = 1
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista las solicitudes activas de un solicitante.
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
        s.hora,
        s.descripcionSolicitud,
        s.gmail

    FROM SOLICITA AS s

    LEFT JOIN LABORATORIO AS l
        ON l.ID_L = s.ID_L

    WHERE s.cedula = :cedula
        AND s.activo = 1
";

        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["cedula" => $cedula]);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta las solicitudes activas por laboratorio (resumen del administrador).
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

    WHERE s.activo = 1

    GROUP BY l.ID_L, l.nombreLaboratorio, l.numLaboratorio
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta las solicitudes activas.
     * @return int La cantidad de solicitudes.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function contarSolicitudes(): int
    {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM SOLICITA WHERE activo = 1");

        return (int) $consulta->fetchColumn();
    }

    /**
     * Indica si el solicitante ya tiene una solicitud activa para ese laboratorio en esa fecha y hora.
     * @param int $idLaboratorio Identificador del laboratorio.
     * @param string $cedula Cédula del solicitante.
     * @param string $fecha Fecha de la solicitud.
     * @param string $hora Hora de la solicitud.
     * @return bool TRUE si la solicitud existe y está activa.
     */
    public function existeSolicitud(int $idLaboratorio, string $cedula, string $fecha, string $hora): bool {
        $sql = "SELECT 1 FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora AND activo = 1";
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute(["idLaboratorio" => $idLaboratorio, "cedula" => $cedula, "fecha" => $fecha, "hora" => $hora]);

        return $consulta->fetchColumn() !== false;
    }

    public function registrarSolicitud(Solicitud $solicitud): bool {
        // Se toman los datos del objeto.
        $cedula = $solicitud->getCedula();
        $idLaboratorio = $solicitud->getIdLaboratorio();
        $fecha = $solicitud->getFecha();
        $hora = $solicitud->getHora();
        $descripcionSolicitud = $solicitud->getDescripcionSolicitud();
        $gmail = $solicitud->getGmail();

        try {
            $this->conexion->beginTransaction();

            // Se busca si el solicitante ya pidió ese laboratorio para esa fecha y hora (activa o dada de baja).
            $consultaExiste = $this->conexion->prepare("SELECT activo FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora");
            $consultaExiste->execute(["idLaboratorio" => $idLaboratorio, "cedula" => $cedula, "fecha" => $fecha, "hora" => $hora]);
            $activo = $consultaExiste->fetchColumn();

            if ($activo === false) {
                // No existe: se crea. Cada solicitud guarda su propio gmail de contacto.
                $sqlSolicitud = "INSERT INTO SOLICITA (cedula, ID_L, descripcionSolicitud, fecha, hora, gmail, activo) VALUES (:cedula, :idLaboratorio, :descripcionSolicitud, :fecha, :hora, :gmail, 1)";
            } elseif ((int) $activo === 0) {
                // Existe dada de baja: se reactiva con los datos nuevos.
                $sqlSolicitud = "UPDATE SOLICITA SET descripcionSolicitud = :descripcionSolicitud, gmail = :gmail, activo = 1 WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora";
            } else {
                // Existe y está activa: es un duplicado.
                $this->conexion->rollBack();
                return false;
            }

            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute([
                "cedula" => $cedula,
                "idLaboratorio" => $idLaboratorio,
                "descripcionSolicitud" => $descripcionSolicitud,
                "fecha" => $fecha,
                "hora" => $hora,
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

    public function modificarSolicitud(int $idLaboratorioActual, string $fechaActual, string $horaActual, Solicitud $solicitud): bool {
        // Se toman los datos del objeto.
        $cedula = $solicitud->getCedula();
        $idLaboratorioNuevo = $solicitud->getIdLaboratorio();
        $fecha = $solicitud->getFecha();
        $hora = $solicitud->getHora();
        $descripcionSolicitud = $solicitud->getDescripcionSolicitud();
        $gmail = $solicitud->getGmail();

        try {
            $this->conexion->beginTransaction();

            // Solo se modifican solicitudes existentes y activas del propio solicitante.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora AND activo = 1");
            $consultaExiste->execute(["idLaboratorio" => $idLaboratorioActual, "cedula" => $cedula, "fecha" => $fechaActual, "hora" => $horaActual]);

            if ($consultaExiste->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            // Si ya hay una solicitud dada de baja con el laboratorio, fecha y hora nuevos,
            // se reactiva esa con los datos nuevos y se da de baja la actual (no pueden repetirse las claves).
            $consultaDadaDeBaja = $this->conexion->prepare("SELECT 1 FROM SOLICITA WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora AND activo = 0");
            $consultaDadaDeBaja->execute(["idLaboratorio" => $idLaboratorioNuevo, "cedula" => $cedula, "fecha" => $fecha, "hora" => $hora]);

            if ($consultaDadaDeBaja->fetchColumn() !== false) {
                $consultaReactivar = $this->conexion->prepare("UPDATE SOLICITA SET descripcionSolicitud = :descripcionSolicitud, gmail = :gmail, activo = 1 WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora");
                $consultaReactivar->execute([
                    "descripcionSolicitud" => $descripcionSolicitud,
                    "gmail" => $gmail,
                    "idLaboratorio" => $idLaboratorioNuevo,
                    "cedula" => $cedula,
                    "fecha" => $fecha,
                    "hora" => $hora
                ]);

                $consultaBajaActual = $this->conexion->prepare("UPDATE SOLICITA SET activo = 0 WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora");
                $consultaBajaActual->execute(["idLaboratorio" => $idLaboratorioActual, "cedula" => $cedula, "fecha" => $fechaActual, "hora" => $horaActual]);

                $this->conexion->commit();
                return true;
            }

            // Cambia el laboratorio de la solicitud, sin modificar el laboratorio en sí.
            $sqlSolicitud = "UPDATE SOLICITA SET ID_L = :idLaboratorioNuevo, descripcionSolicitud = :descripcionSolicitud, fecha = :fecha, hora = :hora, gmail = :gmail WHERE ID_L = :idLaboratorioActual AND cedula = :cedula AND fecha = :fechaActual AND hora = :horaActual";
            $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
            $consultaSolicitud->execute([
                "idLaboratorioNuevo" => $idLaboratorioNuevo,
                "descripcionSolicitud" => $descripcionSolicitud,
                "fecha" => $fecha,
                "hora" => $hora,
                "gmail" => $gmail,
                "idLaboratorioActual" => $idLaboratorioActual,
                "cedula" => $cedula,
                "fechaActual" => $fechaActual,
                "horaActual" => $horaActual
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

    public function eliminarSolicitud(int $idLaboratorio, string $cedula, string $fecha, string $hora): bool {

        // Baja lógica: la solicitud queda inactiva y se conserva en el historial.
        // Devuelve false si la solicitud no existe o ya estaba dada de baja.
        $sqlSolicitud = "UPDATE SOLICITA SET activo = 0 WHERE ID_L = :idLaboratorio AND cedula = :cedula AND fecha = :fecha AND hora = :hora AND activo = 1";
        $consultaSolicitud = $this->conexion->prepare($sqlSolicitud);
        $consultaSolicitud->execute(["idLaboratorio" => $idLaboratorio, "cedula" => $cedula, "fecha" => $fecha, "hora" => $hora]);

        return $consultaSolicitud->rowCount() > 0;
    }
}
