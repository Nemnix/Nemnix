<?php

/**
 * Proporciona las operaciones de consulta y modificación de préstamos.
 */
class AccesoPrestamos
{
    /**
     * Conexión activa con la base de datos.
     *
     * @var PDO
     */
    private PDO $conexion;

    /**
     * Construye el acceso a los datos usando una conexión existente.
     *
     * @param PDO $conexion Conexión activa con la base de datos.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene los préstamos junto con los datos del alumno y del equipo.
     *
     * @return array<int, array<string, mixed>> Lista de préstamos encontrados.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarPrestamos(): array
    {
        $sql = "
        SELECT
            p.ID_P,
            e.ID_EP,
            a.ciAlumno,
            p.estadoPrestamo,
            p.fechaInicio,
            p.fechaLimite,
            a.nombreAlumno,
            a.apellidoAlumno,
            e.modelo
        FROM ALUMNO_PRESTAMO AS a
        INNER JOIN PRESTAMO AS p
            ON p.ciAlumno = a.ciAlumno
        INNER JOIN EQUIPO_PRESTAMO AS e
            ON e.ID_P = p.ID_P";

        $consulta = $this->conexion->query($sql);

        $prestamos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        

        return $prestamos;
    }

    /**
     * Modifica los datos del préstamo, su alumno y el modelo del equipo asociado.
     *
     * Todas las actualizaciones se ejecutan dentro de una única transacción.
     * Si una operación falla, se revierten los cambios realizados.
     *
     * @param string $idPrestamo Identificador del préstamo.
     * @param string $modelo Modelo actualizado del equipo.
     * @param string $estadoPrestamo Estado actualizado del préstamo.
     * @param string $nombreAlumno Nombre actualizado del alumno.
     * @param string $apellidoAlumno Apellido actualizado del alumno.
     * @param string $fechaInicio Fecha de inicio actualizada en formato válido para SQL.
     * @param string $fechaLimite Fecha límite actualizada en formato válido para SQL.
     * @param string $ciAlumno Cédula del alumno asociado al préstamo.
     * @return bool TRUE si la modificación se confirma; FALSE si ocurre un error.
     */
    public function modificarPrestamo(string $idPrestamo, string $modelo, string $estadoPrestamo, string $nombreAlumno, string $apellidoAlumno, string $fechaInicio, string $fechaLimite, string $ciAlumno): bool
    {
        try {
            $this->conexion->beginTransaction();

            $consultaAlumno = $this->conexion->prepare(
                "UPDATE ALUMNO_PRESTAMO SET nombreAlumno = :nombreAlumno, apellidoAlumno = :apellidoAlumno WHERE ciAlumno = :ciAlumno"
            );
            $consultaAlumno->execute([
                "nombreAlumno" => $nombreAlumno,
                "apellidoAlumno" => $apellidoAlumno,
                "ciAlumno" => $ciAlumno
            ]);

            $consultaPrestamo = $this->conexion->prepare(
                "UPDATE PRESTAMO SET ciAlumno = :ciAlumno, estadoPrestamo = :estadoPrestamo, fechaInicio = :fechaInicio, fechaLimite = :fechaLimite WHERE ID_P = :idPrestamo"
            );
            $consultaPrestamo->execute([
                "ciAlumno" => $ciAlumno,
                "estadoPrestamo" => $estadoPrestamo,
                "fechaInicio" => $fechaInicio,
                "fechaLimite" => $fechaLimite,
                "idPrestamo" => $idPrestamo
            ]);

            $consultaEquipo = $this->conexion->prepare(
                "UPDATE EQUIPO_PRESTAMO SET modelo = :modelo WHERE ID_P = :idPrestamo"
            );
            $consultaEquipo->execute([
                "modelo" => $modelo,
                "idPrestamo" => $idPrestamo
            ]);

            $this->conexion->commit();
            return true;
        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            error_log("Error al modificar el préstamo: " . $error->getMessage());
            return false;
        }
    }

 }
    ?>