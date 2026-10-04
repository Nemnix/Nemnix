<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/registroDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";

class registroControlador
{
    public function gestionar(string $metodo): void
    {
        if (!isset($_SESSION["cedula"])) {
        RespuestaJson::error("Acceso denegado: sesión no iniciada", 401);
        }
        if(!($_SESSION["solicitante"] ?? false)) {
            RespuestaJson::error("Acceso denegado: rol incorrecto", 403);
        }
        match($metodo){
            "GET" => $this->listar(),
            "POST"=> $this->alta(),
            "PATCH" => $this->modificar(),
            "DELETE" => $this->baja(),
            default => RespuestaJson::error("Metodo no permitido", 405),
        };
    }
    public function listar(): void{
        $conexion = $this->conectar();
        $dao = new RegistrosDao($conexion);
        RespuestaJson::exito($dao->listarRegistros());
    }

    private function alta(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $ID_R = $datos["ID_R"] ?? "";
        $cedula = $datos["cedula"] ?? "";
        $nombreLaboratorio = $datos["nombreLaboratorio"] ?? "";
        $numLaboratorio = $datos["numLaboratorio"] ?? "";
        $ID_L = $datos["ID_L"] ?? "";
        $grupo = $datos["grupo"] ?? "";
        $horaEntrada = $datos["horaEntrada"] ?? "";
        $horaSalida = $datos["horaSalida"] ?? "";
        $asignatura = $datos["asignatura"] ?? "";
        $fecha = $datos["fecha"] ?? "";
        $turno = $datos["turno"] ?? "";

        if($cedula == "" || $nombreLaboratorio == "" || $numLaboratorio == "" || $ID_L == "" || $grupo == "" || $horaEntrada == "" || $horaSalida == "" || $asignatura == "" || $fecha == "" || $turno == ""){
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if(!preg_match("/^\d{8}$/", $cedula)) {
            RespuestaJson::error("Cédula inválida. Debe contener exactamente 8 dígitos.", 422);
        }
        $conexion = $this->conectar();
        $dao = new RegistrosDao($conexion);
        $resultado = $dao->registrarRegistro($cedula, $grupo, $horaEntrada, $horaSalida, $asignatura, $fecha, $nombreLaboratorio, $numLaboratorio, $turno);
        if($resultado){
            RespuestaJson::exito("Registro creado exitosamente");
        } else {
            RespuestaJson::error("Error al crear el registro", 500);
        }
        
    }
    private function modificar(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $ID_R = $datos["ID_R"] ?? "";
        $cedula = $datos["cedula"] ?? "";
        $nombreLaboratorio = $datos["nombreLaboratorio"] ?? "";
        $numLaboratorio = $datos["numLaboratorio"] ?? "";
        $ID_L = $datos["ID_L"] ?? "";
        $grupo = $datos["grupo"] ?? "";
        $horaEntrada = $datos["horaEntrada"] ?? "";
        $horaSalida = $datos["horaSalida"] ?? "";
        $asignatura = $datos["asignatura"] ?? "";
        $fecha = $datos["fecha"] ?? "";
        $turno = $datos["turno"] ?? "";

        if($cedula == "" || $nombreLaboratorio == "" || $numLaboratorio == "" || $ID_L == "" || $grupo == "" || $horaEntrada == "" || $horaSalida == "" || $asignatura == "" || $fecha == "" || $turno == ""){
            RespuestaJson::error("Existen campos vacíos", 422);
        }

        $conexion = $this->conectar();
        $dao = new RegistrosDao($conexion);
        $resultado = $dao->modificarRegistro($ID_R, $nombreLaboratorio, $numLaboratorio, $ID_L, $grupo, $horaEntrada, $horaSalida, $asignatura, $fecha, $turno);
        if($resultado){
            RespuestaJson::exito("Registro modificado exitosamente");
        } else {
            RespuestaJson::error("Error al modificar el registro", 400);
        }
    }
    private function baja(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $ID_R = $datos["ID_R"] ?? "";

        if($ID_R == ""){
            RespuestaJson::error("ID de registro no proporcionado", 422);
        }

        $conexion = $this->conectar();
        $dao = new RegistrosDao($conexion);
        $resultado = $dao->eliminarRegistro($ID_R);
        if($resultado){
            RespuestaJson::exito("Registro eliminado exitosamente");
        } else {
            RespuestaJson::error("Error al eliminar el registro", 400);
        }
    }
    private function conectar(): PDO
    {
        $conector = new ConectorPDO();
        return $conector->establecerConexion();
        if ($conexion === null) {
            RespuestaJson::error("Error al conectar con la base de datos", 500);
        }
        return $conexion;
    }
}