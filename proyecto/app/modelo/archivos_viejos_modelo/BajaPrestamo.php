<?php


/**
 * Ejecuta operaciones de baja para los préstamos.
 */
class BajaPrestamo
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
     * Elimina un préstamo y el equipo asociado.
     *
     * El equipo se elimina antes que el préstamo para respetar la clave foránea
      * de EQUIPO_PRESTAMO hacia PRESTAMO. El alumno se elimina únicamente si
      * no tiene otros préstamos asociados.
     *
     * @param string $idPrestamo Identificador del préstamo.
     * @return bool TRUE si la operación se completa correctamente; FALSE si falla.
     */
    public function eliminarPrestamo(string $idPrestamo): bool
    {

        try {
            $this->conexion->beginTransaction();

            $consultaAlumno = $this->conexion->prepare(
                "SELECT ciAlumno FROM PRESTAMO WHERE ID_P = :idPrestamo"
            );
            $consultaAlumno->execute(["idPrestamo" => $idPrestamo]);
            $ciAlumno = $consultaAlumno->fetchColumn();

            if ($ciAlumno === false) {
                $this->conexion->rollBack();
                return false;
            }

            $consultaEquipo = $this->conexion->prepare(
                "DELETE FROM EQUIPO_PRESTAMO WHERE ID_P = :idPrestamo"
            );
            $consultaEquipo->execute(["idPrestamo" => $idPrestamo]);

            $consultaPrestamo = $this->conexion->prepare(
                "DELETE FROM PRESTAMO WHERE ID_P = :idPrestamo"
            );
            $consultaPrestamo->execute(["idPrestamo" => $idPrestamo]);

            $consultaOtrosPrestamos = $this->conexion->prepare(
                "SELECT COUNT(*) FROM PRESTAMO WHERE ciAlumno = :ciAlumno"
            );
            $consultaOtrosPrestamos->execute(["ciAlumno" => $ciAlumno]);

            if ((int) $consultaOtrosPrestamos->fetchColumn() === 0) {
                $consultaAlumno = $this->conexion->prepare(
                    "DELETE FROM ALUMNO_PRESTAMO WHERE ciAlumno = :ciAlumno"
                );
                $consultaAlumno->execute(["ciAlumno" => $ciAlumno]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }

            return false;
        }
    }
}