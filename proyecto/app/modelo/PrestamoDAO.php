<?php


class PrestamoDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista los préstamos con los datos del alumno y del equipo prestado.
     * @return array Los préstamos.
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
        ON e.ID_P = p.ID_P
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarPrestamo(string $cedula, string $ciAlumno, string $nombreAlumno, string $apellidoAlumno, string $fechaInicio, string $fechaLimite, string $estado, int $idEquipo, string $modelo): bool {
        try {
            $this->conexion->beginTransaction();

            // Se busca si el equipo ya está en otro préstamo.
            $consultaExiste = $this->conexion->prepare("SELECT 1 FROM EQUIPO_PRESTAMO WHERE ID_EP = :idEquipo");
            $consultaExiste->execute(["idEquipo" => $idEquipo]);

            if ($consultaExiste->fetchColumn() !== false) {
                // Existe: es un duplicado.
                $this->conexion->rollBack();
                return false;
            }

            // Si el alumno ya tuvo un préstamo, se actualizan su nombre y apellido.
            $sqlAlumno = "INSERT INTO ALUMNO_PRESTAMO (ciAlumno, nombreAlumno, apellidoAlumno) VALUES (:ciAlumno, :nombreAlumno, :apellidoAlumno) ON DUPLICATE KEY UPDATE nombreAlumno = VALUES(nombreAlumno), apellidoAlumno = VALUES(apellidoAlumno)";
            $consultaAlumno = $this->conexion->prepare($sqlAlumno);
            $consultaAlumno->execute(["ciAlumno" => $ciAlumno, "nombreAlumno" => $nombreAlumno, "apellidoAlumno" => $apellidoAlumno]);

            $sqlPrestamo = "INSERT INTO PRESTAMO (cedula, ciAlumno, nombreAlumno, apellidoAlumno, estadoPrestamo, fechaInicio, fechaLimite) VALUES (:cedula, :ciAlumno, :nombreAlumno, :apellidoAlumno, :estado, :fechaInicio, :fechaLimite)";
            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute([
                "cedula" => $cedula,
                "ciAlumno" => $ciAlumno,
                "nombreAlumno" => $nombreAlumno,
                "apellidoAlumno" => $apellidoAlumno,
                "estado" => $estado,
                "fechaInicio" => $fechaInicio,
                "fechaLimite" => $fechaLimite
            ]);

            $idPrestamo = $this->conexion->lastInsertId();

            $sqlEquipo = "INSERT INTO EQUIPO_PRESTAMO (ID_EP, ID_P, modelo) VALUES (:idEquipo, :idPrestamo, :modelo)";
            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute(["idEquipo" => $idEquipo, "idPrestamo" => $idPrestamo, "modelo" => $modelo]);

            $this->conexion->commit();

            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }

    public function modificarPrestamo(int $idPrestamo, string $modelo, string $estado, string $nombreAlumno, string $apellidoAlumno, string $fechaInicio, string $fechaLimite): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se modifican préstamos existentes. La cédula del alumno no se cambia.
            $consultaAlumno = $this->conexion->prepare("SELECT ciAlumno FROM PRESTAMO WHERE ID_P = :idPrestamo");
            $consultaAlumno->execute(["idPrestamo" => $idPrestamo]);
            $ciAlumno = $consultaAlumno->fetchColumn();

            if ($ciAlumno === false) {
                $this->conexion->rollBack();
                return false;
            }

            $sqlAlumno = "UPDATE ALUMNO_PRESTAMO SET nombreAlumno = :nombreAlumno, apellidoAlumno = :apellidoAlumno WHERE ciAlumno = :ciAlumno";
            $consultaActualizarAlumno = $this->conexion->prepare($sqlAlumno);
            $consultaActualizarAlumno->execute(["nombreAlumno" => $nombreAlumno, "apellidoAlumno" => $apellidoAlumno, "ciAlumno" => $ciAlumno]);

            $sqlPrestamo = "UPDATE PRESTAMO SET nombreAlumno = :nombreAlumno, apellidoAlumno = :apellidoAlumno, estadoPrestamo = :estado, fechaInicio = :fechaInicio, fechaLimite = :fechaLimite WHERE ID_P = :idPrestamo";
            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute([
                "nombreAlumno" => $nombreAlumno,
                "apellidoAlumno" => $apellidoAlumno,
                "estado" => $estado,
                "fechaInicio" => $fechaInicio,
                "fechaLimite" => $fechaLimite,
                "idPrestamo" => $idPrestamo
            ]);

            $sqlEquipo = "UPDATE EQUIPO_PRESTAMO SET modelo = :modelo WHERE ID_P = :idPrestamo";
            $consultaEquipo = $this->conexion->prepare($sqlEquipo);
            $consultaEquipo->execute(["modelo" => $modelo, "idPrestamo" => $idPrestamo]);

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }

    public function eliminarPrestamo(int $idPrestamo): bool {
        try {
            $this->conexion->beginTransaction();

            // Solo se eliminan préstamos existentes.
            $consultaAlumno = $this->conexion->prepare("SELECT ciAlumno FROM PRESTAMO WHERE ID_P = :idPrestamo");
            $consultaAlumno->execute(["idPrestamo" => $idPrestamo]);
            $ciAlumno = $consultaAlumno->fetchColumn();

            if ($ciAlumno === false) {
                $this->conexion->rollBack();
                return false;
            }

            // Se borra primero el equipo prestado y después el préstamo.
            foreach (["EQUIPO_PRESTAMO", "PRESTAMO"] as $tabla) {
                $consultaEliminar = $this->conexion->prepare("DELETE FROM $tabla WHERE ID_P = :idPrestamo");
                $consultaEliminar->execute(["idPrestamo" => $idPrestamo]);
            }

            // Si el alumno no tiene otros préstamos, también se borra.
            $consultaOtrosPrestamos = $this->conexion->prepare("SELECT COUNT(*) FROM PRESTAMO WHERE ciAlumno = :ciAlumno");
            $consultaOtrosPrestamos->execute(["ciAlumno" => $ciAlumno]);

            if ((int) $consultaOtrosPrestamos->fetchColumn() === 0) {
                $consultaEliminarAlumno = $this->conexion->prepare("DELETE FROM ALUMNO_PRESTAMO WHERE ciAlumno = :ciAlumno");
                $consultaEliminarAlumno->execute(["ciAlumno" => $ciAlumno]);
            }

            $this->conexion->commit();
            return true;

        } catch (PDOException $error) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            throw $error;
        }
    }
}
