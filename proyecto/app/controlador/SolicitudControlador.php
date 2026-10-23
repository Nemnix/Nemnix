<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Solicitud.php";
require_once RUTA_MODELO . "/SolicitudDAO.php";
require_once RUTA_MODELO . "/LaboratorioDAO.php";
require_once RUTA_MODELO . "/UsuarioDAO.php";
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
        $hora = trim($datos["hora"] ?? "");
        $descripcionSolicitud = trim($datos["descripcionSolicitud"] ?? "");

        if ($nombreLaboratorio === "" || $numLaboratorio === "" || $fecha === "" || $hora === "" || $descripcionSolicitud === "") {
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
        if (!$this->horaValida($hora)) {
            RespuestaJson::error("Hora incorrecta", 422);
        }

        try {
            $conexion = $this->conectar();

            // La solicitud se asocia a un laboratorio ya existente.
            $daoLaboratorio = new LaboratorioDAO($conexion);
            $idLaboratorio = $daoLaboratorio->buscarIdLaboratorio($nombreLaboratorio, (int) $numLaboratorio);

            if ($idLaboratorio === null) {
                RespuestaJson::error("El laboratorio no existe", 422);
            }

            // El gmail de contacto es el que tiene registrado el solicitante.
            $gmail = $this->buscarGmailSolicitante($conexion, $cedula);

            $dao = new SolicitudDAO($conexion);
            // Se arma la solicitud con los datos ya validados.
            $solicitud = new Solicitud($idLaboratorio, $cedula, $nombreLaboratorio, (int) $numLaboratorio, $fecha, $hora, $descripcionSolicitud, $gmail);
            $resultado = $dao->registrarSolicitud($solicitud);
        } catch (PDOException $error) {
            error_log("Error en alta de solicitud: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("Ya existe una solicitud para ese laboratorio en esa fecha y hora", 409);
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
        // Fecha y hora originales, que junto al laboratorio identifican la solicitud a modificar.
        $fechaActual = trim($datos["fechaActual"] ?? "");
        $horaActual = trim($datos["horaActual"] ?? "");
        $nombreLaboratorio = trim($datos["nombreLaboratorio"] ?? "");
        $numLaboratorio = trim($datos["numLaboratorio"] ?? "");
        $fecha = trim($datos["fecha"] ?? "");
        $hora = trim($datos["hora"] ?? "");
        $descripcionSolicitud = trim($datos["descripcionSolicitud"] ?? "");

        if ($idLaboratorioActual === "" || $fechaActual === "" || $horaActual === "" || $nombreLaboratorio === "" || $numLaboratorio === "" || $fecha === "" || $hora === "" || $descripcionSolicitud === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idLaboratorioActual) || (int) $idLaboratorioActual <= 0) {
            RespuestaJson::error("Identificador de laboratorio incorrecto", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fechaActual) === false || !$this->horaValida($horaActual)) {
            RespuestaJson::error("Fecha u hora de la solicitud incorrecta", 422);
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
        if (!$this->horaValida($hora)) {
            RespuestaJson::error("Hora incorrecta", 422);
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

            // Primero se comprueba que la solicitud a modificar exista y sea del solicitante.
            if (!$dao->existeSolicitud((int) $idLaboratorioActual, $cedula, $fechaActual, $horaActual)) {
                RespuestaJson::error("La solicitud no existe", 404);
            }

            // Si cambia de laboratorio, fecha u hora, no puede tener ya una solicitud con los datos nuevos.
            $cambiaClave = $idLaboratorioNuevo !== (int) $idLaboratorioActual || $fecha !== $fechaActual || substr($hora, 0, 5) !== substr($horaActual, 0, 5);
            if ($cambiaClave && $dao->existeSolicitud($idLaboratorioNuevo, $cedula, $fecha, $hora)) {
                RespuestaJson::error("Ya existe una solicitud para ese laboratorio en esa fecha y hora", 409);
            }

            // El gmail de contacto es el que tiene registrado el solicitante.
            $gmail = $this->buscarGmailSolicitante($conexion, $cedula);

            // Se arma la solicitud con los datos nuevos; el laboratorio actual la identifica.
            $solicitud = new Solicitud($idLaboratorioNuevo, $cedula, $nombreLaboratorio, (int) $numLaboratorio, $fecha, $hora, $descripcionSolicitud, $gmail);
            $resultado = $dao->modificarSolicitud((int) $idLaboratorioActual, $fechaActual, $horaActual, $solicitud);
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
        $fecha = trim($datos["fecha"] ?? "");
        $hora = trim($datos["hora"] ?? "");

        // El administrador indica de qué solicitante es la solicitud; el solicitante solo borra las suyas.
        if ($_SESSION["administrador"] ?? false) {
            $cedula = trim($datos["cedula"] ?? "");
        } else {
            $cedula = $_SESSION["cedula"];
        }

        if ($idLaboratorio === "" || $cedula === "" || $fecha === "" || $hora === "") {
            RespuestaJson::error("Faltan el laboratorio, la cédula, la fecha o la hora de la solicitud", 422);
        }
        if (!ctype_digit($idLaboratorio) || (int) $idLaboratorio <= 0) {
            RespuestaJson::error("Identificador de laboratorio incorrecto", 422);
        }
        if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
            RespuestaJson::error("Cédula incorrecta", 422);
        }
        if (DateTime::createFromFormat("Y-m-d", $fecha) === false || !$this->horaValida($hora)) {
            RespuestaJson::error("Fecha u hora de la solicitud incorrecta", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new SolicitudDAO($conexion);
            $resultado = $dao->eliminarSolicitud((int) $idLaboratorio, $cedula, $fecha, $hora);
        } catch (PDOException $error) {
            error_log("Error en baja de solicitud: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("La solicitud no existe o ya fue dada de baja", 404);
        }

        RespuestaJson::exito(["mensaje" => "Solicitud eliminada exitosamente"]);
    }

    /**
     * Devuelve el gmail registrado del solicitante.
     */
    private function buscarGmailSolicitante(PDO $conexion, string $cedula): ?string
    {
        $daoUsuario = new UsuarioDAO($conexion);
        return $daoUsuario->buscarUsuario($cedula)?->getGmail();
    }

    /**
     * Valida una hora en formato HH:MM o HH:MM:SS.
     */
    private function horaValida(string $hora): bool
    {
        return preg_match("/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/", $hora) === 1;
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}
