<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Solicitud.php";
require_once RUTA_MODELO . "/SolicitudDAO.php";
require_once RUTA_MODELO . "/LaboratorioDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";


class SolicitudControlador
{

    public function gestionar(string $metodo): void
    {
        // El solicitante gestiona sus solicitudes y el administrador las ve todas y puede darlas de baja.
        Sesion::verificarAlgunRol(["administrador", "solicitante"]);

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
            $dao = new SolicitudDAO($conexion);

            if ($_SESSION["administrador"] ?? false) {
                // El administrador ve todas las solicitudes, o el resumen por laboratorio con ?resumen=1.
                if (isset($_GET["resumen"])) {
                    $solicitudes = [
                        "porLaboratorio" => $dao->listarSolicitudesPorLaboratorio(),
                        "pendientes" => $dao->contarSolicitudes()
                    ];
                } else {
                    $solicitudes = $dao->listarSolicitudes();
                }
            } else {
                // Cada solicitante ve solo sus solicitudes.
                $solicitudes = $dao->listarSolicitudesPorCedula($_SESSION["cedula"]);
            }
        } catch (PDOException $error) {
            error_log("Error en listarSolicitudes: " . $error->getMessage());
            RespuestaJson::error("Error al listar solicitudes", 500);
        }
        RespuestaJson::exito($solicitudes);
    }

    private function alta(): void
    {
        // Solo el solicitante da de alta solicitudes.
        Sesion::verificarRol("solicitante");

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        // La cédula sale de la sesión, no del cuerpo de la petición.
        $cedula = $_SESSION["cedula"];
        $nombreLaboratorio = trim($datos["nombreLaboratorio"] ?? "");
        $numLaboratorio = trim($datos["numLaboratorio"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $descripcionSolicitud = trim($datos["descripcionSolicitud"] ?? "");
        $gmail = trim($datos["gmail"] ?? "");

        if ($nombreLaboratorio === "" || $numLaboratorio === "" || $fecha === "" || $descripcionSolicitud === "" || $gmail === "") {
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
        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
            RespuestaJson::error("Gmail incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();

            // La solicitud se asocia a un laboratorio ya existente.
            $daoLaboratorio = new LaboratorioDAO($conexion);
            $idLaboratorio = $daoLaboratorio->buscarIdLaboratorio($nombreLaboratorio, (int) $numLaboratorio);

            if ($idLaboratorio === null) {
                RespuestaJson::error("El laboratorio no existe", 422);
            }

            $dao = new SolicitudDAO($conexion);
            $resultado = $dao->registrarSolicitud($cedula, $idLaboratorio, $fecha, $descripcionSolicitud, $gmail);
        } catch (PDOException $error) {
            error_log("Error en alta de solicitud: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("Ya existe una solicitud para ese laboratorio", 409);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud registrada exitosamente"], 201);
    }

    private function modificar(): void
    {
        // Solo el solicitante modifica sus solicitudes.
        Sesion::verificarRol("solicitante");

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $cedula = $_SESSION["cedula"];
        $idLaboratorioActual = trim($datos["ID_L"] ?? "");
        $nombreLaboratorio = trim($datos["nombreLaboratorio"] ?? "");
        $numLaboratorio = trim($datos["numLaboratorio"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $descripcionSolicitud = trim($datos["descripcionSolicitud"] ?? "");
        $gmail = trim($datos["gmail"] ?? "");

        if ($idLaboratorioActual === "" || $nombreLaboratorio === "" || $numLaboratorio === "" || $fecha === "" || $descripcionSolicitud === "" || $gmail === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idLaboratorioActual) || (int) $idLaboratorioActual <= 0) {
            RespuestaJson::error("Identificador de laboratorio incorrecto", 422);
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
        if (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
            RespuestaJson::error("Gmail incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();

            // La solicitud se asocia a un laboratorio ya existente.
            $daoLaboratorio = new LaboratorioDAO($conexion);
            $idLaboratorioNuevo = $daoLaboratorio->buscarIdLaboratorio($nombreLaboratorio, (int) $numLaboratorio);

            if ($idLaboratorioNuevo === null) {
                RespuestaJson::error("El laboratorio no existe", 422);
            }

            $dao = new SolicitudDAO($conexion);

            // Si cambia de laboratorio, no puede tener ya una solicitud para el nuevo.
            if ($idLaboratorioNuevo !== (int) $idLaboratorioActual && $dao->existeSolicitud($idLaboratorioNuevo, $cedula)) {
                RespuestaJson::error("Ya existe una solicitud para ese laboratorio", 409);
            }

            $resultado = $dao->modificarSolicitud((int) $idLaboratorioActual, $cedula, $idLaboratorioNuevo, $fecha, $descripcionSolicitud, $gmail);
        } catch (PDOException $error) {
            error_log("Error en modificación de solicitud: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("La solicitud no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud modificada exitosamente"]);
    }

    private function baja(): void
    {

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];
        $idLaboratorio = trim($datos["ID_L"] ?? "");

        // El administrador indica de qué solicitante es la solicitud; el solicitante solo borra las suyas.
        if ($_SESSION["administrador"] ?? false) {
            $cedula = trim($datos["cedula"] ?? "");
        } else {
            $cedula = $_SESSION["cedula"];
        }

        if ($idLaboratorio === "" || $cedula === "") {
            RespuestaJson::error("Faltan el laboratorio o la cédula de la solicitud", 422);
        }
        if (!ctype_digit($idLaboratorio) || (int) $idLaboratorio <= 0) {
            RespuestaJson::error("Identificador de laboratorio incorrecto", 422);
        }
        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("Cédula incorrecta", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new SolicitudDAO($conexion);
            $resultado = $dao->eliminarSolicitud((int) $idLaboratorio, $cedula);
        } catch (PDOException $error) {
            error_log("Error en baja de solicitud: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("La solicitud no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud eliminada exitosamente"]);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}
