<?php

/**
 * Ejecuta operaciones de baja para los tickets.
 */
class BajaTicket
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
     * Elimina un ticket identificado por su clave compuesta (ID_R, ID_T).
     *
     * @param int $idRegistro Identificador del registro diario asociado.
     * @param int $idTicket Identificador del ticket dentro del registro.
     * @return bool TRUE si se eliminó el ticket; FALSE si no existía o falló la operación.
     */
    public function eliminarTicket(int $idRegistro, int $idTicket): bool
    {
        try {
            $consulta = $this->conexion->prepare(
                "DELETE FROM TICKET WHERE ID_R = :idRegistro AND ID_T = :idTicket"
            );
            $consulta->execute([
                "idRegistro" => $idRegistro,
                "idTicket" => $idTicket
            ]);

            return $consulta->rowCount() > 0;
        } catch (PDOException $error) {
            error_log("Error al eliminar el ticket: " . $error->getMessage());
            return false;
        }
    }
}
