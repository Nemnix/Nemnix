<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Registro.php";
require_once RUTA_MODELO . "/RegistroDAO.php";
require_once RUTA_MODELO . "/LaboratorioDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";

class RegistroControlador
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
            "PUT" => $this->modificar(),
            "DELETE" => $this->baja(),
            default => RespuestaJson::error("Método no permitido", 405),
        };
    }

    private function listar(): void
    {
        try {
            $conexion = $this->conectar();
            $dao = new RegistroDAO($conexion);
            // Cada solicitante ve solo sus registros.
            $registros = $dao->listarRegistros($_SESSION["cedula"]);
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
        $nombreLaboratorio = trim($datos["nombreLaboratorio"] ?? "");
        $numLaboratorio = trim($datos["numLaboratorio"] ?? "");
        $grupo = trim($datos["grupo"] ?? "");
        $horaEntrada = trim($datos["horaEntrada"] ?? "");
        $horaSalida = trim($datos["horaSalida"] ?? "");
        $asignatura = trim($datos["asignatura"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $turno = trim($datos["turno"] ?? "");

        if($nombreLaboratorio === "" || $numLaboratorio === "" || $grupo === "" || $horaEntrada === "" || $horaSalida === "" || $asignatura === "" || $fecha === "" || $turno === ""){
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!in_array($nombreLaboratorio, ["Laboratorio", "Taller"], true)) {
            RespuestaJson::error("Tipo de aula incorrecto", 422);
        }
        if (!ctype_digit($numLaboratorio)) {
            RespuestaJson::error("Número de laboratorio incorrecto", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fecha) === false) {
            RespuestaJson::error("Fecha incorrecta", 422);
        }
        if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $horaEntrada) || !preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $horaSalida)) {
            RespuestaJson::error("Hora incorrecta", 422);
        }
        if (!in_array($turno, ["Matutino", "Vespertino", "Nocturno"], true)) {
            RespuestaJson::error("Turno incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();

            // El registro se asocia a un laboratorio ya existente.
            $daoLaboratorio = new LaboratorioDAO($conexion);
            $idLaboratorio = $daoLaboratorio->buscarIdLaboratorio($nombreLaboratorio, (int) $numLaboratorio);

            if ($idLaboratorio === null) {
                RespuestaJson::error("El laboratorio no existe", 422);
            }

            $dao = new RegistroDAO($conexion);
            // Se arma el registro con los datos ya validados.
            $registro = new Registro(0, $cedula, $idLaboratorio, $grupo, $horaEntrada, $horaSalida, $asignatura, $fecha, $turno);
            $resultado = $dao->registrarRegistro($registro);
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

        $cedula = $_SESSION["cedula"];
        $ID_R = trim($datos["ID_R"] ?? "");
        $nombreLaboratorio = trim($datos["nombreLaboratorio"] ?? "");
        $numLaboratorio = trim($datos["numLaboratorio"] ?? "");
        $grupo = trim($datos["grupo"] ?? "");
        $horaEntrada = trim($datos["horaEntrada"] ?? "");
        $horaSalida = trim($datos["horaSalida"] ?? "");
        $asignatura = trim($datos["asignatura"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $turno = trim($datos["turno"] ?? "");

        if($ID_R === "" || $nombreLaboratorio === "" || $numLaboratorio === "" || $grupo === "" || $horaEntrada === "" || $horaSalida === "" || $asignatura === "" || $fecha === "" || $turno === ""){
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($ID_R) || (int) $ID_R <= 0) {
            RespuestaJson::error("Identificador de registro incorrecto", 422);
        }
        if (!in_array($nombreLaboratorio, ["Laboratorio", "Taller"], true)) {
            RespuestaJson::error("Tipo de aula incorrecto", 422);
        }
        if (!ctype_digit($numLaboratorio)) {
            RespuestaJson::error("Número de laboratorio incorrecto", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fecha) === false) {
            RespuestaJson::error("Fecha incorrecta", 422);
        }
        if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $horaEntrada) || !preg_match("/^[0-2][0-9]:[0-5][0-9]$/", $horaSalida)) {
            RespuestaJson::error("Hora incorrecta", 422);
        }
        if (!in_array($turno, ["Matutino", "Vespertino", "Nocturno"], true)) {
            RespuestaJson::error("Turno incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();

            // El registro se asocia a un laboratorio ya existente.
            $daoLaboratorio = new LaboratorioDAO($conexion);
            $idLaboratorio = $daoLaboratorio->buscarIdLaboratorio($nombreLaboratorio, (int) $numLaboratorio);

            if ($idLaboratorio === null) {
                RespuestaJson::error("El laboratorio no existe", 422);
            }

            $dao = new RegistroDAO($conexion);
            // Se arma el registro con los datos ya validados.
            $registro = new Registro((int) $ID_R, $cedula, $idLaboratorio, $grupo, $horaEntrada, $horaSalida, $asignatura, $fecha, $turno);
            $resultado = $dao->modificarRegistro($registro);
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

        $cedula = $_SESSION["cedula"];
        $ID_R = trim($datos["ID_R"] ?? "");

        if($ID_R === ""){
            RespuestaJson::error("ID de registro no proporcionado", 422);
        }
        if (!ctype_digit($ID_R) || (int) $ID_R <= 0) {
            RespuestaJson::error("Identificador de registro incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new RegistroDAO($conexion);
            $resultado = $dao->eliminarRegistro((int) $ID_R, $cedula);
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
