<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Prestamo.php";
require_once RUTA_MODELO . "/PrestamoDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";


class PrestamoControlador
{

    public function gestionar(string $metodo): void
    {
        Sesion::verificarRol("tecnico");

        if ($metodo !== "GET") {
            Token::verificarCSRF();
        }

        match ($metodo) {
            "GET" => $this->listar(),
            "POST" => $this->alta(),
            "PUT" => $this->modificar(),
            "DELETE" => $this->baja(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    private function listar(): void
    {
        try {
            $conexion = $this->conectar();
            $dao = new PrestamoDAO($conexion);
            $prestamos = $dao->listarPrestamos();
        } catch (PDOException $error) {
            error_log("Error en listarPrestamos: " . $error->getMessage());
            RespuestaJson::error("Error al listar préstamos", 500);
        }
        RespuestaJson::exito($prestamos);
    }

    private function alta(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        // La cédula del técnico sale de la sesión, no del cuerpo de la petición.
        $cedula = $_SESSION["cedula"];
        $idEquipo = trim($datos["ID_EP"] ?? "");
        $modelo = trim($datos["modelo"] ?? "");
        $estado = trim($datos["estado"] ?? "");
        $ciAlumno = trim($datos["ciAlumno"] ?? "");
        $nombreAlumno = trim($datos["nombreAlumno"] ?? "");
        $apellidoAlumno = trim($datos["apellidoAlumno"] ?? "");
        $fechaInicio = trim($datos["fechaInicio"] ?? "");
        $fechaLimite = trim($datos["fechaLimite"] ?? "");

        if ($idEquipo === "" || $modelo === "" || $estado === "" || $ciAlumno === "" || $nombreAlumno === "" || $apellidoAlumno === "" || $fechaInicio === "" || $fechaLimite === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idEquipo) || (int) $idEquipo <= 0) {
            RespuestaJson::error("Identificador de equipo incorrecto", 422);
        }
        if (!preg_match("/^[1-9][0-9]{7}$/", $ciAlumno)) {
            RespuestaJson::error("Cédula del alumno incorrecta", 422);
        }
        if (!in_array($estado, ["Disponible", "Prestamo en fecha", "VENCIDO"], true)) {
            RespuestaJson::error("Estado incorrecto", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fechaInicio) === false || DateTime::createFromFormat("Y-m-d", $fechaLimite) === false) {
            RespuestaJson::error("Fecha incorrecta", 422);
        }
        if ($fechaLimite < $fechaInicio) {
            RespuestaJson::error("La fecha de devolución no puede ser anterior a la de inicio", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new PrestamoDAO($conexion);
            // Se arma el préstamo con los datos ya validados.
            $prestamo = new Prestamo(0, (int) $idEquipo, $modelo, $cedula, $ciAlumno, $nombreAlumno, $apellidoAlumno, $estado, $fechaInicio, $fechaLimite);
            $resultado = $dao->registrarPrestamo($prestamo);
        } catch (PDOException $error) {
            error_log("Error en alta de préstamo: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("Ese equipo ya está en otro préstamo", 409);
        }

        RespuestaJson::exito(["mensaje" => "Préstamo registrado exitosamente"], 201);
    }

    private function modificar(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        // La cédula del alumno y el equipo no se modifican.
        $idPrestamo = trim($datos["ID_P"] ?? "");
        $modelo = trim($datos["modelo"] ?? "");
        $estado = trim($datos["estado"] ?? "");
        $nombreAlumno = trim($datos["nombreAlumno"] ?? "");
        $apellidoAlumno = trim($datos["apellidoAlumno"] ?? "");
        $fechaInicio = trim($datos["fechaInicio"] ?? "");
        $fechaLimite = trim($datos["fechaLimite"] ?? "");

        if ($idPrestamo === "" || $modelo === "" || $estado === "" || $nombreAlumno === "" || $apellidoAlumno === "" || $fechaInicio === "" || $fechaLimite === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idPrestamo) || (int) $idPrestamo <= 0) {
            RespuestaJson::error("Identificador de préstamo incorrecto", 422);
        }
        if (!in_array($estado, ["Disponible", "Prestamo en fecha", "VENCIDO"], true)) {
            RespuestaJson::error("Estado incorrecto", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fechaInicio) === false || DateTime::createFromFormat("Y-m-d", $fechaLimite) === false) {
            RespuestaJson::error("Fecha incorrecta", 422);
        }
        if ($fechaLimite < $fechaInicio) {
            RespuestaJson::error("La fecha de devolución no puede ser anterior a la de inicio", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new PrestamoDAO($conexion);
            // Se arma el préstamo con los datos ya validados. Al modificar no se usan
            // el equipo, el técnico ni la cédula del alumno, porque no cambian.
            $prestamo = new Prestamo((int) $idPrestamo, 0, $modelo, "", "", $nombreAlumno, $apellidoAlumno, $estado, $fechaInicio, $fechaLimite);
            $resultado = $dao->modificarPrestamo($prestamo);
        } catch (PDOException $error) {
            error_log("Error en modificación de préstamo: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El préstamo no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Préstamo modificado exitosamente"]);
    }

    private function baja(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $idPrestamo = trim($datos["ID_P"] ?? "");

        if ($idPrestamo === "") {
            RespuestaJson::error("Falta el identificador del préstamo", 422);
        }
        if (!ctype_digit($idPrestamo) || (int) $idPrestamo <= 0) {
            RespuestaJson::error("Identificador de préstamo incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new PrestamoDAO($conexion);
            $resultado = $dao->eliminarPrestamo((int) $idPrestamo);
        } catch (PDOException $error) {
            error_log("Error en baja de préstamo: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El préstamo no existe o ya fue dado de baja", 404);
        }

        RespuestaJson::exito(["mensaje" => "Préstamo eliminado exitosamente"]);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}
