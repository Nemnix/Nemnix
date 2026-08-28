<?php


/**
 * Ejecuta operaciones de baja para el inventario.
 */
class BajaInventario
{
    /**
     * Conexión activa con la base de datos.
     *
     * @var PDO
     */
    private PDO $conexion;


    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     *
     * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    private string $ultimoError = "";

    /**
     * Elimina un laboratorio y su equipo asociado si no tiene historial.
     *
     * @param string $idInventario Identificador del laboratorio.
     * @return bool TRUE si la operación se completa correctamente; FALSE si falla.
     */
    public function eliminarInventario(string $idInventario): bool
    {
        try {
            $this->conexion->beginTransaction();

            $consultaLaboratorio = $this->conexion->prepare(
                "SELECT 1 FROM LABORATORIO WHERE ID_L = :idLaboratorio"
            );
            $consultaLaboratorio->execute(["idLaboratorio" => $idInventario]);

            if ($consultaLaboratorio->fetchColumn() === false) {
                $this->ultimoError = "El laboratorio no existe.";
                $this->conexion->rollBack();
                return false;
            }

            $consultaDependencias = $this->conexion->prepare(
                "SELECT
                    (SELECT COUNT(*) FROM REGISTRO_DIARIO WHERE ID_L = :idRegistro) +
                    (SELECT COUNT(*) FROM SOLICITA WHERE ID_L = :idSolicitud) AS total"
            );
            $consultaDependencias->execute([
                "idRegistro" => $idInventario,
                "idSolicitud" => $idInventario
            ]);

            if ((int) $consultaDependencias->fetchColumn() > 0) {
                $this->ultimoError = "El laboratorio tiene solicitudes o registros asociados.";
                $this->conexion->rollBack();
                return false;
            }

            $consultaEquipo = $this->conexion->prepare(
                "DELETE FROM EQUIPO WHERE ID_L = :idLaboratorio"
            );
            $consultaEquipo->execute(["idLaboratorio" => $idInventario]);

            $consultaGestion = $this->conexion->prepare(
                "DELETE FROM GESTIONA WHERE ID_L = :idLaboratorio"
            );
            $consultaGestion->execute(["idLaboratorio" => $idInventario]);

            $consultaLaboratorio = $this->conexion->prepare(
                "DELETE FROM LABORATORIO WHERE ID_L = :idLaboratorio"
            );
            $consultaLaboratorio->execute(["idLaboratorio" => $idInventario]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            $this->ultimoError = $error->getMessage();
            return false;
        }
    }

    public function obtenerUltimoError(): string
    {
        return $this->ultimoError;
    }
}