<?php
class AccesoSolicitudes
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listarSolicitudes(): array
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
            ON l.ID_L = s.ID_L";

        $consulta = $this->conexion->query($sql);

        $solicitudes = $consulta->fetchAll(PDO::FETCH_ASSOC);

        return $solicitudes;
    }

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
                "UPDATE SOLICITA SET fecha = :fecha, descripcionSolicitud = :descripcionSolicitud, gmail = :gmail WHERE ID_L = :ID_L"
            );
            $consultaSolicitud->execute([
                "fecha" => $fecha,
                "descripcionSolicitud" => $descripcionSolicitud,
                "gmail" => $gmail,
                "ID_L" => $ID_L,
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