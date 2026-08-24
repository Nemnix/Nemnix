<?php

/**
 * Ejecuta operaciones de baja para las solicitudes de laboratorio.
 */
class BajaSolicitud
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


    /**
     * Elimina las solicitudes asociadas a un laboratorio.
     *
     * @param string $ID_Lab Identificador del laboratorio.
     * @return bool TRUE si la operación se completa correctamente; FALSE si falla.
     */
    public function eliminarSolicitud(string $ID_Lab): bool
    {

        try {
            $this->conexion->beginTransaction();

            $consultaSolicita = $this->conexion->prepare(
                "DELETE FROM SOLICITA WHERE ID_L = :ID_Lab"
            );
            $consultaSolicita->execute(["ID_Lab" => $ID_Lab]);

            if ($consultaSolicita->rowCount() === 0) {
                $this->conexion->rollBack();
                return false;
            }
            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
    if ($this->conexion->inTransaction()) {
        $this->conexion->rollBack();
    }
    error_log("Error en eliminarSolicitud: " . $error->getMessage());
    return false;
        }
    }
}
