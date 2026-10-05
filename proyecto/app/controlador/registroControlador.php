<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/registroDao.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

class registroControlador
{
    public function gestionar(string $metodo): void
    {
        Sesion::verificarRol("solicitante");

        if ($metodo !== "GET") {
            Token::verificarCSRF();
        }

        match($metodo){
            "GET" => $this->listar(),
            "POST"=> $this->alta(),
            "PATCH" => $this->modificar(),
            "DELETE" => $this->baja(),
            default => RespuestaJson::error("Metodo no permitido", 405),
        };
    }

    private function listar(): void
    {
        try {
            $conexion = $this->conectar();
            $dao = new RegistrosDao($conexion);
            $registros = $dao->listarRegistros();
        } catch (PDOException $error) {
            error_log("Error en listarRegistros: " . $error->getMessage());
            RespuestaJson::error("Error al listar registros", 500);
        }
        RespuestaJson::exito($registros);
    }

    private function alta(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        // La cédula sale de la sesión, no del cuerpo de la petición.
        $cedula = $_SESSION["cedula"];
        $nombreLaboratorio = $datos["nombreLaboratorio"] ?? "";
        $numLaboratorio = $datos["numLaboratorio"] ?? "";
        $grupo = $datos["grupo"] ?? "";
        $horaEntrada = $datos["horaEntrada"] ?? "";
        $horaSalida = $datos["horaSalida"] ?? "";
        $asignatura = $datos["asignatura"] ?? "";
        $fecha = $datos["fecha"] ?? "";
        $turno = $datos["turno"] ?? "";

        if($nombreLaboratorio == "" || $numLaboratorio == "" || $grupo == "" || $horaEntrada == "" || $horaSalida == "" || $asignatura == "" || $fecha == "" || $turno == ""){
            RespuestaJson::error("Existen campos vacíos", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new RegistrosDao($conexion);
            $resultado = $dao->registrarRegistro($cedula, $grupo, $horaEntrada, $horaSalida, $asignatura, $fecha, $nombreLaboratorio, $numLaboratorio, $turno);
        } catch (PDOException $error) {
            error_log("Error en alta de registro: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("Error al crear el registro", 500);
        }

        RespuestaJson::exito(["mensaje" => "Registro creado exitosamente"], 201);
    }

    private function modificar(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $ID_R = $datos["ID_R"] ?? "";
        $nombreLaboratorio = $datos["nombreLaboratorio"] ?? "";
        $numLaboratorio = $datos["numLaboratorio"] ?? "";
        $grupo = $datos["grupo"] ?? "";
        $horaEntrada = $datos["horaEntrada"] ?? "";
        $horaSalida = $datos["horaSalida"] ?? "";
        $asignatura = $datos["asignatura"] ?? "";
        $fecha = $datos["fecha"] ?? "";
        $turno = $datos["turno"] ?? "";

        if($ID_R == "" || $nombreLaboratorio == "" || $numLaboratorio == "" || $grupo == "" || $horaEntrada == "" || $horaSalida == "" || $asignatura == "" || $fecha == "" || $turno == ""){
            RespuestaJson::error("Existen campos vacíos", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new RegistrosDao($conexion);
            $resultado = $dao->modificarRegistro($ID_R, $nombreLaboratorio, $numLaboratorio, $grupo, $horaEntrada, $horaSalida, $asignatura, $fecha, $turno);
        } catch (PDOException $error) {
            error_log("Error en modificación de registro: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El registro no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Registro modificado exitosamente"]);
    }

    private function baja(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $ID_R = $datos["ID_R"] ?? "";

        if($ID_R == ""){
            RespuestaJson::error("ID de registro no proporcionado", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new RegistrosDao($conexion);
            $resultado = $dao->eliminarRegistro($ID_R);
        } catch (PDOException $error) {
            error_log("Error en baja de registro: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El registro no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Registro eliminado exitosamente"]);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}
