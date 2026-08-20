<?php


/**
 * Clase encargada de modificar los datos
 * relacionados con usuarios del sistema.
 */
class ModificarDatosUsuario
{
    private PDO $conexion;


    /**
     * Constructor parametrizado que recibe una conexión
     * a la base de datos.
     *
     * @param PDO $conexion Conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }


    /**
     * Modifica los datos de un usuario y su rol.
     *
     * @param string $idEquipoPrestamo ID del equipo prestado a modificar.
     * @param string $modelo Nuevo modelo.
     * @param string $estadoPrestamo Nuevo estado del préstamo.
     * @param string $nombreAlumno Nuevo nombre del alumno.
     * @param string $apellidoAlumno Nuevo apellido del alumno.
     * @param string $fechaInicio Nueva fecha de inicio.
     * @param string $fechaLimite Nueva fecha de fin.
     *
     * @return bool TRUE si la modificación se realiza correctamente, FALSE en caso contrario.
     */
   public function modificarPrestamo(string $idEquipoPrestamo, string $modelo, string $estadoPrestamo, string $nombreAlumno, string $apellidoAlumno, string $fechaInicio, string $fechaLimite, string $ciAlumno): bool
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
                "UPDATE PRESTAMO SET ciAlumno = :ciAlumno, estadoPrestamo = :estadoPrestamo, fechaInicio = :fechaInicio, fechaLimite = :fechaLimite WHERE ID_EP = :idEquipoPrestamo"
            );
            $consultaPrestamo->execute([
                "ciAlumno" => $ciAlumno,
                "estadoPrestamo" => $estadoPrestamo,
                "fechaInicio" => $fechaInicio,
                "fechaLimite" => $fechaLimite,
                "idPrestamo" => $idEquipoPrestamo
            ]);

            $consultaEquipo = $this->conexion->prepare(
                "UPDATE EQUIPO_PRESTAMO SET modelo = :modelo WHERE ID_EP = :idEquipoPrestamo"
            );
            $consultaEquipo->execute([
                "modelo" => $modelo,
                "idEquipoPrestamo" => $idEquipoPrestamo
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


    
