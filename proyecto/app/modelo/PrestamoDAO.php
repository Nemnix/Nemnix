<?php


class PrestamoDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista los préstamos activos con los datos del alumno y del equipo prestado.
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

    WHERE p.activo = 1
";

        $consulta = $this->conexion->query($sql);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarPrestamo(Prestamo $prestamo): bool {
        // Se toman los datos del objeto.
        $cedula = $prestamo->getCedula();
        $ciAlumno = $prestamo->getCiAlumno();
        $nombreAlumno = $prestamo->getNombreAlumno();
        $apellidoAlumno = $prestamo->getApellidoAlumno();
        $fechaInicio = $prestamo->getFechaInicio();
        $fechaLimite = $prestamo->getFechaLimite();
        $estado = $prestamo->getEstado();
        $idEquipo = $prestamo->getIdEquipo();
        $modelo = $prestamo->getModelo();

        try {
            $this->conexion->beginTransaction();

            // Se busca si el equipo ya está en otro préstamo (activo o dado de baja).
            $consultaExiste = $this->conexion->prepare("SELECT p.activo FROM EQUIPO_PRESTAMO AS e INNER JOIN PRESTAMO AS p ON p.ID_P = e.ID_P WHERE e.ID_EP = :idEquipo");
            $consultaExiste->execute(["idEquipo" => $idEquipo]);
            $activo = $consultaExiste->fetchColumn();

            if ($activo !== false && (int) $activo === 1) {
                // Está en un préstamo activo: es un duplicado.
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

            if ($activo === false) {
                // El equipo nunca se prestó: se crea.
                $sqlEquipo = "INSERT INTO EQUIPO_PRESTAMO (ID_EP, ID_P, modelo) VALUES (:idEquipo, :idPrestamo, :modelo)";
            } else {
                // El equipo estaba en un préstamo dado de baja: pasa al préstamo nuevo.
                $sqlEquipo = "UPDATE EQUIPO_PRESTAMO SET ID_P = :idPrestamo, modelo = :modelo WHERE ID_EP = :idEquipo";
            }
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

    public function modificarPrestamo(Prestamo $prestamo): bool {
        // Se toman los datos del objeto.
        $idPrestamo = $prestamo->getIdPrestamo();
        $modelo = $prestamo->getModelo();
        $estado = $prestamo->getEstado();
        $nombreAlumno = $prestamo->getNombreAlumno();
        $apellidoAlumno = $prestamo->getApellidoAlumno();
        $fechaInicio = $prestamo->getFechaInicio();
        $fechaLimite = $prestamo->getFechaLimite();

        try {
            $this->conexion->beginTransaction();

            // Solo se modifican préstamos existentes y activos. La cédula del alumno no se cambia.
            $consultaAlumno = $this->conexion->prepare("SELECT ciAlumno FROM PRESTAMO WHERE ID_P = :idPrestamo AND activo = 1");
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

        // Baja lógica: el préstamo queda inactivo y conserva su equipo y alumno.
        // Devuelve false si el préstamo no existe o ya estaba dado de baja.
        $sqlPrestamo = "UPDATE PRESTAMO SET activo = 0 WHERE ID_P = :idPrestamo AND activo = 1";
        $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
        $consultaPrestamo->execute(["idPrestamo" => $idPrestamo]);

        return $consultaPrestamo->rowCount() > 0;
    }
}
