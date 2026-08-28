<?php

/**
 * Proporciona las operaciones de consulta y modificación de solicitudes
 * de laboratorios del usuario solicitante.
 */
class AccesoSolicitudes
{
    private PDO $conexion;

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexion a la base de datos. PRECONDICION: No debe ser NULL.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Lista las solicitudes junto con los datos del laboratorio asociado.
     * @return array Las solicitudes con sus campos y los datos del laboratorio.
     */
    public function listarSolicitudes(): array
    {
        $sql = "
        SELECT
        s.ID_L,
        l.nombreLaboratorio,
        l.numLaboratorio,
        s.fecha,
        s.descripcionSolicitud,
        sol.gmail

        FROM SOLICITA AS s
        LEFT JOIN LABORATORIO AS l
            ON l.ID_L = s.ID_L
        LEFT JOIN SOLICITANTE AS sol
            ON sol.cedula = s.cedula";

        $consulta = $this->conexion->query($sql);

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $solicitudes;
    }

    /**
     * Modifica los datos del laboratorio y de la solicitud asociada.
     *
     * Todas las actualizaciones se ejecutan dentro de una única transacción.
     * Si una operación falla, se revierten los cambios realizados.
     *
     * @param string $ID_L Identificador del laboratorio.
     * @param string $cedula Cédula del solicitante asociado a la solicitud.
     * @param string $nombreLaboratorio Tipo de aula actualizado.
     * @param string $numLaboratorio Número de aula actualizado.
     * @param string $fecha Fecha actualizada en formato válido para SQL.
     * @param string $descripcionSolicitud Descripción actualizada de la solicitud.
     * @param string $gmail Correo del solicitante actualizado.
     * @return bool TRUE si la modificación se confirma; FALSE si ocurre un error.
     */
    public function modificarSolicitud(string $ID_L, string $cedula, string $nombreLaboratorio, string $numLaboratorio, string $fecha, string $descripcionSolicitud, string $gmail): bool
    {
        try {
            $this->conexion->beginTransaction();

            $consultaSolicitud = $this->conexion->prepare(
                "SELECT 1 FROM SOLICITA WHERE ID_L = :ID_L AND cedula = :cedula"
            );
            $consultaSolicitud->execute([
                "ID_L" => $ID_L,
                "cedula" => $cedula,
            ]);

            if ($consultaSolicitud->fetchColumn() === false) {
                $this->conexion->rollBack();
                return false;
            }

            $consultaLab = $this->conexion->prepare(
                "UPDATE LABORATORIO SET nombreLaboratorio = :nombreLaboratorio, numLaboratorio = :numLaboratorio WHERE ID_L = :ID_L"
            );
            $consultaLab->execute([
                "nombreLaboratorio" => $nombreLaboratorio,
                "numLaboratorio" => $numLaboratorio,
                "ID_L" => $ID_L,
            ]);

            $consultaSolicitud = $this->conexion->prepare(
                "UPDATE SOLICITA SET fecha = :fecha, descripcionSolicitud = :descripcionSolicitud WHERE ID_L = :ID_L AND cedula = :cedula"
            );
            $consultaSolicitud->execute([
                "fecha" => $fecha,
                "descripcionSolicitud" => $descripcionSolicitud,
                "ID_L" => $ID_L,
                "cedula" => $cedula,
            ]);

            $consultaSolicitante = $this->conexion->prepare(
                "UPDATE SOLICITANTE SET gmail = :gmail WHERE cedula = :cedula"
            );
            $consultaSolicitante->execute([
                "gmail" => $gmail,
                "cedula" => $cedula,
            ]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            error_log("Error al modificar la solicitud: " . $error->getMessage());
            return false;
        }
    }
}
?>