<?php
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Ticket.php";
require_once RUTA_MODELO . "/TicketDAO.php";
require_once RUTA_VISTA . "/RespuestaJson.php";
require_once RUTA_NUCLEO . "/Conexion.php";
require_once RUTA_NUCLEO . "/Sesion.php";
require_once RUTA_NUCLEO . "/Token.php";


class TicketControlador
{

    public function gestionar(string $metodo): void
    {
        // El solicitante levanta y borra tickets de sus registros; el técnico los ve todos y los modifica.
        Sesion::verificarAlgunRol(["tecnico", "solicitante"]);

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
            $dao = new TicketDAO($conexion);

            if ($_SESSION["tecnico"] ?? false) {
                // El técnico ve todos los tickets.
                $tickets = $dao->listarTickets();
            } else {
                // Cada solicitante ve solo los tickets de sus registros.
                $tickets = $dao->listarTicketsPorCedula($_SESSION["cedula"]);
            }
        } catch (PDOException $error) {
            error_log("Error en listarTickets: " . $error->getMessage());
            RespuestaJson::error("Error al listar tickets", 500);
        }
        RespuestaJson::exito($tickets);
    }

    private function alta(): void
    {
        // Solo el solicitante levanta tickets.
        Sesion::verificarRol("solicitante");

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        // La cédula sale de la sesión, no del cuerpo de la petición.
        $cedula = $_SESSION["cedula"];
        $idRegistro = trim($datos["ID_R"] ?? "");
        $tipoDispositivo = trim($datos["tipoDispositivo"] ?? "");
        $nombreDispositivo = trim($datos["nombreDispositivo"] ?? "");
        $nombreAlumno = trim($datos["nombreAlumno"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");
        $prioridad = trim($datos["prioridad"] ?? "");

        // El nombre del alumno es opcional (solo si el equipo es una máquina).
        if ($idRegistro === "" || $tipoDispositivo === "" || $nombreDispositivo === "" || $descripcion === "" || $prioridad === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idRegistro) || (int) $idRegistro <= 0) {
            RespuestaJson::error("Identificador de registro incorrecto", 422);
        }
        if (!in_array($prioridad, ["Alta", "Media", "Baja", "Sin definir"], true)) {
            RespuestaJson::error("Prioridad incorrecta", 422);
        }

        // Todo ticket nace abierto.
        $estado = "Abierto";

        try {
            $conexion = $this->conectar();
            $dao = new TicketDAO($conexion);
            // Se arma el ticket con los datos ya validados; el número de ticket lo calcula el DAO.
            $ticket = new Ticket((int) $idRegistro, 0, $estado, $tipoDispositivo, $nombreDispositivo, $nombreAlumno, $descripcion, $prioridad);
            $resultado = $dao->registrarTicket($ticket, $cedula);
        } catch (PDOException $error) {
            error_log("Error en alta de ticket: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El registro no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Ticket registrado exitosamente"], 201);
    }

    private function modificar(): void
    {
        // Solo el técnico modifica tickets.
        Sesion::verificarRol("tecnico");

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $idRegistro = trim($datos["ID_R"] ?? "");
        $idTicket = trim($datos["ID_T"] ?? "");
        $estado = trim($datos["estado"] ?? "");
        $tipoDispositivo = trim($datos["tipoDispositivo"] ?? "");
        $nombreEquipo = trim($datos["nombreEquipo"] ?? "");
        $nombreAlumno = trim($datos["nombreAlumno"] ?? "");
        $descripcion = trim($datos["descripcion"] ?? "");
        $prioridad = trim($datos["prioridad"] ?? "");

        if ($idRegistro === "" || $idTicket === "" || $estado === "" || $nombreEquipo === "" || $descripcion === "" || $prioridad === "") {
            RespuestaJson::error("Existen campos vacíos", 422);
        }
        if (!ctype_digit($idRegistro) || (int) $idRegistro <= 0 || !ctype_digit($idTicket) || (int) $idTicket <= 0) {
            RespuestaJson::error("Identificador de ticket incorrecto", 422);
        }
        if (!in_array($estado, ["Abierto", "Pendiente", "En proceso", "Cerrado"], true)) {
            RespuestaJson::error("Estado incorrecto", 422);
        }
        if (!in_array($prioridad, ["Alta", "Media", "Baja", "Sin definir"], true)) {
            RespuestaJson::error("Prioridad incorrecta", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new TicketDAO($conexion);
            // Se arma el ticket con los datos ya validados.
            $ticket = new Ticket((int) $idRegistro, (int) $idTicket, $estado, $tipoDispositivo, $nombreEquipo, $nombreAlumno, $descripcion, $prioridad);
            $resultado = $dao->modificarTicket($ticket);
        } catch (PDOException $error) {
            error_log("Error en modificación de ticket: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El ticket no existe", 404);
        }

        RespuestaJson::exito(["mensaje" => "Ticket modificado exitosamente"]);
    }

    private function baja(): void
    {
        // Solo el solicitante borra tickets, y solo de sus registros.
        Sesion::verificarRol("solicitante");

        $datos = json_decode(file_get_contents("php://input"), true) ?? [];

        $cedula = $_SESSION["cedula"];
        $idRegistro = trim($datos["ID_R"] ?? "");
        $idTicket = trim($datos["ID_T"] ?? "");

        if ($idRegistro === "" || $idTicket === "") {
            RespuestaJson::error("Falta el identificador del ticket", 422);
        }
        if (!ctype_digit($idRegistro) || (int) $idRegistro <= 0 || !ctype_digit($idTicket) || (int) $idTicket <= 0) {
            RespuestaJson::error("Identificador de ticket incorrecto", 422);
        }

        try {
            $conexion = $this->conectar();
            $dao = new TicketDAO($conexion);
            $resultado = $dao->eliminarTicket((int) $idRegistro, (int) $idTicket, $cedula);
        } catch (PDOException $error) {
            error_log("Error en baja de ticket: " . $error->getMessage());
            RespuestaJson::error("Error interno del servidor", 500);
        }

        if (!$resultado) {
            RespuestaJson::error("El ticket no existe o ya fue dado de baja", 404);
        }

        RespuestaJson::exito(["mensaje" => "Ticket eliminado exitosamente"]);
    }

    private function conectar(): PDO
    {
        return Conexion::conectar();
    }
}
