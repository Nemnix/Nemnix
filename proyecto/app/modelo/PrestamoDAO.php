<?php


class PrestamoDAO {
    private PDO $conexion;

    public function __construct(PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Lista los préstamos activos con los datos del equipo y, si está prestado, del alumno.
     * @return array Los préstamos.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    public function listarPrestamos(): array
    {
        $sql = "
    SELECT
        p.ID_P,
        e.ID_EP,
        p.ciAlumno,
        p.estadoPrestamo,
        p.fechaInicio,
        p.fechaLimite,
        a.nombreAlumno,
        a.apellidoAlumno,
        e.modelo

    FROM PRESTAMO AS p

    INNER JOIN EQUIPO_PRESTAMO AS e
        ON e.ID_P = p.ID_P

    -- LEFT JOIN: un equipo Disponible no tiene alumno.
    LEFT JOIN ALUMNO_PRESTAMO AS a
        ON a.ciAlumno = p.ciAlumno

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

            // Un equipo Disponible no tiene alumno.
            if ($ciAlumno !== null) {
                $this->guardarAlumno($ciAlumno, $nombreAlumno, $apellidoAlumno);
            }

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

            // Solo se modifican préstamos existentes y activos.
            $consultaAlumno = $this->conexion->prepare("SELECT ciAlumno FROM PRESTAMO WHERE ID_P = :idPrestamo AND activo = 1");
            $consultaAlumno->execute(["idPrestamo" => $idPrestamo]);
            $ciAlumnoActual = $consultaAlumno->fetchColumn();

            if ($ciAlumnoActual === false) {
                $this->conexion->rollBack();
                return false;
            }

            if ($prestamo->getCiAlumno() === null) {
                // Pasa a Disponible (devolución): se quitan el alumno y las fechas.
                $ciAlumno = null;
            } else {
                // Mientras está prestado la cédula del alumno no cambia; si estaba Disponible, se toma la nueva.
                $ciAlumno = $ciAlumnoActual ?? $prestamo->getCiAlumno();
                $this->guardarAlumno($ciAlumno, $nombreAlumno, $apellidoAlumno);
            }

            $sqlPrestamo = "UPDATE PRESTAMO SET ciAlumno = :ciAlumno, nombreAlumno = :nombreAlumno, apellidoAlumno = :apellidoAlumno, estadoPrestamo = :estado, fechaInicio = :fechaInicio, fechaLimite = :fechaLimite WHERE ID_P = :idPrestamo";
            $consultaPrestamo = $this->conexion->prepare($sqlPrestamo);
            $consultaPrestamo->execute([
                "ciAlumno" => $ciAlumno,
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

    /**
     * Crea el alumno o, si ya tuvo un préstamo, actualiza su nombre y apellido.
     * Debe llamarse dentro de una transacción abierta.
     * @throws PDOException Si la consulta no puede ejecutarse.
     */
    private function guardarAlumno(string $ciAlumno, string $nombreAlumno, string $apellidoAlumno): void {
        $sqlAlumno = "INSERT INTO ALUMNO_PRESTAMO (ciAlumno, nombreAlumno, apellidoAlumno) VALUES (:ciAlumno, :nombreAlumno, :apellidoAlumno) ON DUPLICATE KEY UPDATE nombreAlumno = VALUES(nombreAlumno), apellidoAlumno = VALUES(apellidoAlumno)";
        $consultaAlumno = $this->conexion->prepare($sqlAlumno);
        $consultaAlumno->execute(["ciAlumno" => $ciAlumno, "nombreAlumno" => $nombreAlumno, "apellidoAlumno" => $apellidoAlumno]);
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
