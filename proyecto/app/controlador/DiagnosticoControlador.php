<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Diagnostico.php";
require_once RUTA_MODELO . "/DiagnosticoDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";


class DiagnosticoControlador
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
            $dao = new DiagnosticoDAO($conexion);
            $diagnosticos = $dao->listarDiagnosticos();
        } catch (PDOException $error) {
            error_log("Error en listarDiagnosticos: " . $error->getMessage());
            RespuestaJson::error("Error al listar diagnósticos", 500);
        }
        RespuestaJson::exito($diagnosticos);
    }

    private function alta(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        // La cédula del técnico sale de la sesión, no del cuerpo de la petición.
        $cedula = $_SESSION["cedula"];
        $idRegistro = trim($datos["ID_R"] ?? "");
        $idTicket = trim($datos["ID_T"] ?? "");
        $estado = trim($datos["estado"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");

        if ($idRegistro === "" || $idTicket === "" || $estado === "" || $fecha === "" || $descripcion === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idRegistro) || (int) $idRegistro <= 0 || !ctype_digit($idTicket) || (int) $idTicket <= 0) {
            RespuestaJson::error("Ticket incorrecto", 422);
        }
        if (!in_array($estado, ["En proceso", "Solucionado", "Requiere otro factor", "Requiere autorización"], true)) {
            RespuestaJson::error("Estado incorrecto", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fecha) === false) {
            RespuestaJson::error("Fecha incorrecta", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new DiagnosticoDAO($conexion);

            // El diagnóstico se hace sobre un ticket existente.
            if (!$dao->existeTicket((int) $idRegistro, (int) $idTicket)) {
                RespuestaJson::error("El ticket no existe", 422);
            }

            // Se arma el diagnóstico con los datos ya validados.
            $diagnostico = new Diagnostico(0, (int) $idRegistro, (int) $idTicket, $cedula, $estado, $fecha, $descripcion);
            $resultado = $dao->registrarDiagnostico($diagnostico);
        } catch (PDOException $error) {
            error_log("Error en alta de diagnóstico: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("Error al registrar el diagnóstico", 500);
        }

        RespuestaJson::exito(["mensaje" => "Diagnóstico registrado exitosamente"], 201);
    }

    private function modificar(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $idDiagnostico = trim($datos["ID_D"] ?? "");
        $estado = trim($datos["estado"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");

        if ($idDiagnostico === "" || $estado === "" || $fecha === "" || $descripcion === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idDiagnostico) || (int) $idDiagnostico <= 0) {
            RespuestaJson::error("Identificador de diagnóstico incorrecto", 422);
        }
        if (!in_array($estado, ["En proceso", "Solucionado", "Requiere otro factor", "Requiere autorización"], true)) {
            RespuestaJson::error("Estado incorrecto", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fecha) === false) {
            RespuestaJson::error("Fecha incorrecta", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new DiagnosticoDAO($conexion);
            // Se arma el diagnóstico con los datos ya validados. Al modificar solo se usan
            // el id, el estado, la fecha y la descripción; el ticket y el técnico no cambian.
            $diagnostico = new Diagnostico((int) $idDiagnostico, 0, 0, "", $estado, $fecha, $descripcion);
            $resultado = $dao->modificarDiagnostico($diagnostico);
        } catch (PDOException $error) {
            error_log("Error en modificación de diagnóstico: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El diagnóstico no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Diagnóstico modificado exitosamente"]);
    }

    private function baja(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $idDiagnostico = trim($datos["ID_D"] ?? "");

        if ($idDiagnostico === "") {
            RespuestaJson::error("Falta el identificador del diagnóstico", 422);
        }
        if (!ctype_digit($idDiagnostico) || (int) $idDiagnostico <= 0) {
            RespuestaJson::error("Identificador de diagnóstico incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new DiagnosticoDAO($conexion);
            $resultado = $dao->eliminarDiagnostico((int) $idDiagnostico);
        } catch (PDOException $error) {
            error_log("Error en baja de diagnóstico: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El diagnóstico no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Diagnóstico eliminado exitosamente"]);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}
