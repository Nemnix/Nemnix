<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Laboratorio.php";
require_once RUTA_MODELO . "/LaboratorioDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";


class LaboratorioControlador
{

    public function gestionar(string $metodo): void
    {
        Sesion::verificarRol("administrador");

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
            $dao = new LaboratorioDAO($conexion);
            $laboratorios = $dao->listarLaboratorios();
        } catch (PDOException $error) {
            error_log("Error en listarLaboratorios: " . $error->getMessage());
            RespuestaJson::error("Error al listar laboratorios", 500);
        }
        RespuestaJson::exito($laboratorios);
    }

    private function alta(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $nombreLaboratorio = trim($datos["nombreLaboratorio"] ?? "");
        $numLaboratorio = trim($datos["numLaboratorio"] ?? "");
        $cantMonitores = trim($datos["cantMonitores"] ?? "");
        $cantTeclados = trim($datos["cantTeclados"] ?? "");
        $cantMouses = trim($datos["cantMouses"] ?? "");
        $cantComputadoras = trim($datos["cantComputadoras"] ?? "");

        if ($nombreLaboratorio === "" || $numLaboratorio === "" || $cantMonitores === "" || $cantTeclados === "" || $cantMouses === "" || $cantComputadoras === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!in_array($nombreLaboratorio, ["Laboratorio", "Taller"], true)) {
            RespuestaJson::error("Tipo de aula incorrecto", 422);
        }
        if (!ctype_digit($numLaboratorio)) {
            RespuestaJson::error("Número de laboratorio incorrecto", 422);
        }
        if (!ctype_digit($cantMonitores) || !ctype_digit($cantTeclados) || !ctype_digit($cantMouses) || !ctype_digit($cantComputadoras)) {
            RespuestaJson::error("Las cantidades deben ser números enteros mayores o iguales a 0", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new LaboratorioDAO($conexion);
            $resultado = $dao->registrarLaboratorio($nombreLaboratorio, (int) $numLaboratorio, (int) $cantMonitores, (int) $cantTeclados, (int) $cantMouses, (int) $cantComputadoras);
        } catch (PDOException $error) {
            error_log("Error en alta de laboratorio: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("Ya existe un laboratorio con ese tipo y número", 409);
        }

        RespuestaJson::exito(["mensaje" => "Laboratorio registrado exitosamente"], 201);
    }

    private function modificar(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $idLaboratorio = trim($datos["ID_L"] ?? "");
        $nombreLaboratorio = trim($datos["nombreLaboratorio"] ?? "");
        $numLaboratorio = trim($datos["numLaboratorio"] ?? "");
        $cantMonitores = trim($datos["cantMonitores"] ?? "");
        $cantTeclados = trim($datos["cantTeclados"] ?? "");
        $cantMouses = trim($datos["cantMouses"] ?? "");
        $cantComputadoras = trim($datos["cantComputadoras"] ?? "");

        if ($idLaboratorio === "" || $nombreLaboratorio === "" || $numLaboratorio === "" || $cantMonitores === "" || $cantTeclados === "" || $cantMouses === "" || $cantComputadoras === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idLaboratorio) || (int) $idLaboratorio <= 0) {
            RespuestaJson::error("Identificador de laboratorio incorrecto", 422);
        }
        if (!in_array($nombreLaboratorio, ["Laboratorio", "Taller"], true)) {
            RespuestaJson::error("Tipo de aula incorrecto", 422);
        }
        if (!ctype_digit($numLaboratorio)) {
            RespuestaJson::error("Número de laboratorio incorrecto", 422);
        }
        if (!ctype_digit($cantMonitores) || !ctype_digit($cantTeclados) || !ctype_digit($cantMouses) || !ctype_digit($cantComputadoras)) {
            RespuestaJson::error("Las cantidades deben ser números enteros mayores o iguales a 0", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new LaboratorioDAO($conexion);

            // No puede quedar con el mismo tipo y número que otro laboratorio.
            if ($dao->existeOtroLaboratorio((int) $idLaboratorio, $nombreLaboratorio, (int) $numLaboratorio)) {
                RespuestaJson::error("Ya existe un laboratorio con ese tipo y número", 409);
            }

            $resultado = $dao->modificarLaboratorio((int) $idLaboratorio, $nombreLaboratorio, (int) $numLaboratorio, (int) $cantMonitores, (int) $cantTeclados, (int) $cantMouses, (int) $cantComputadoras);
        } catch (PDOException $error) {
            error_log("Error en modificación de laboratorio: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El laboratorio no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Laboratorio modificado exitosamente"]);
    }

    private function baja(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $idLaboratorio = trim($datos["ID_L"] ?? "");

        if ($idLaboratorio === "") {
            RespuestaJson::error("Falta el identificador del laboratorio", 422);
        }
        if (!ctype_digit($idLaboratorio) || (int) $idLaboratorio <= 0) {
            RespuestaJson::error("Identificador de laboratorio incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new LaboratorioDAO($conexion);

            // No se puede eliminar un laboratorio con historial de uso.
            if ($dao->tieneDependencias((int) $idLaboratorio)) {
                RespuestaJson::error("El laboratorio tiene registros o solicitudes asociados", 409);
            }

            $resultado = $dao->eliminarLaboratorio((int) $idLaboratorio);
        } catch (PDOException $error) {
            error_log("Error en baja de laboratorio: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El laboratorio no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Laboratorio eliminado exitosamente"]);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}
