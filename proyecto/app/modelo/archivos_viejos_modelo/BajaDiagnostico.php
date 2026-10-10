<?php

/**
 * Ejecuta operaciones de baja para los diagnosticos.
 */
class BajaDiagnostico
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
     * Elimina un diagnostico identificado por su ID_D.
     *
     * @param int $idDiagnostico Identificador del diagnostico.
     * @return bool TRUE si se eliminó el diagnostico; FALSE si no existía o falló la operación.
     */
    public function eliminarDiagnostico(int $idDiagnostico): bool
    {
        try {
            $consulta = $this->conexion->prepare(
                "DELETE FROM DIAGNOSTICO WHERE ID_D = :idDiagnostico"
            );
            $consulta->execute([
                "idDiagnostico" => $idDiagnostico
            ]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $error) {
            error_log("Error al eliminar el diagnostico: " . $error->getMessage());
            return false;
        }
    }
}
