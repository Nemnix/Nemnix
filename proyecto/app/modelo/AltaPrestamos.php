<?php

/**
 * Clase encargada de realizar operaciones de alta
 * relacionadas con los préstamos del sistema.
 */
class AltaPrestamos
{
    private PDO $conexion;
    private string $ultimoError = "";

    /**
     * Constructor parametrizado que recibe una conexión a la base de datos.
     * @param PDO $conexion La conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
     */
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * Registra un nuevo préstamo con su equipo asociado.
     *
     * @param string $cedula Cédula del técnico que registra el préstamo (FK a TECNICO).
     * @param string $nombreAlumno Nombre del alumno.
     * @param string $apellidoAlumno Apellido del alumno.
     * @param string $fechaInicio Fecha de inicio del préstamo.
     * @param string $fechaLimite Fecha límite del préstamo.
     * @param string $estadoPrestamo Estado del préstamo.
     * @param string $idEquipo ID del equipo a prestar.
     * @param string $modeloEquipo Modelo del equipo.
     *
     * @return bool TRUE si el registro se completa correctamente, FALSE en caso contrario.
     */
    public function registrarPrestamo(string $cedula, string $ciAlumno, string $nombreAlumno, string $apellidoAlumno, string $fechaInicio, string $fechaLimite, string $estadoPrestamo, string $idEquipo, string $modeloEquipo): bool
    {
        // Validar que los campos obligatorios no estén vacíos
        if (empty($ciAlumno) || empty($nombreAlumno) || empty($apellidoAlumno) || empty($fechaInicio) || empty($fechaLimite) || empty($estadoPrestamo) || empty($idEquipo) || empty($modeloEquipo)) {
            return false;
        }

        try {
            //Método que ejecuta de forma agrupada todas las instrucciones dirigidas a la base de datos
            //Si una instrucción falla, retorna excepción con la posibilidad de deshacer cambios con rollBack()
            $this->conexion->beginTransaction();

              $sqlAlumnoPrestamo = "INSERT INTO ALUMNO_PRESTAMO (ciAlumno, nombreAlumno, apellidoAlumno) VALUES (:ciAlumno, :nombreAlumno, :apellidoAlumno)";

            $consultaAlumnoPrestamo = $this->conexion->prepare($sqlAlumnoPrestamo);

            $consultaAlumnoPrestamo->execute([
                "ciAlumno" => $ciAlumno,
                "nombreAlumno" => $nombreAlumno,
                "apellidoAlumno" => $apellidoAlumno,
            ]);

            $sqlPrestamo = "INSERT INTO PRESTAMO (cedula, ciAlumno, estadoPrestamo, fechaInicio, fechaLimite) VALUES (:cedula, :ciAlumno, :estadoPrestamo, :fechaInicio, :fechaLimite)";

            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);

            $consultaPrestamo->execute([
                "cedula" => $cedula,
                "ciAlumno" => $ciAlumno,
                "estadoPrestamo" => $estadoPrestamo,
                "fechaInicio" => $fechaInicio,
                "fechaLimite" => $fechaLimite
            ]);

            // Obtener el ID del préstamo insertado
            $idPrestamo = $this->conexion->lastInsertId();

            // 2. Insertar en la tabla EQUIPO_PRESTAMO
            $sqlEquipoPrestamo = "INSERT INTO EQUIPO_PRESTAMO (ID_EP, ID_P, modelo) VALUES (:ID_EP, :ID_P, :modelo)";

            $consultaEquipoPrestamo = $this->conexion->prepare($sqlEquipoPrestamo);

            $consultaEquipoPrestamo->execute([
                "ID_EP" => $idEquipo,
                "ID_P" => $idPrestamo,
                "modelo" => $modeloEquipo
            ]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {

            //Verifica si se encuentra en una transacción
            if ($this->conexion->inTransaction()) {
                //Deshace los cambios causados por la excepción
                $this->conexion->rollBack();
            }

            // Registrar el error para debug
            $this->ultimoError = $error->getMessage();
            error_log("Error en AltaPrestamos: " . $this->ultimoError);

            return false;
        }
    }

    

    public function obtenerUltimoError(): string
    {
        return $this->ultimoError;
    }
}


?>