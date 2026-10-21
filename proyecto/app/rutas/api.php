<?php

/**
 * Enrutador de la API.
 *
 * Lo incluye public/index.php, que ya definió $ruta y $metodo.
 * Cada ruta delega en el método correspondiente de su controlador.
 */

switch ($ruta) {
    case "login":
        require_once RUTA_CONTROLADOR . "/LoginControlador.php";
        (new LoginControlador())->autenticar($metodo);
        break;

    case "cerrarSesion":
        require_once RUTA_CONTROLADOR . "/LoginControlador.php";
        (new LoginControlador())->cerrarSesion($metodo);
        break;

    case "usuarios":
        require_once RUTA_CONTROLADOR . "/UsuarioControlador.php";
        (new UsuarioControlador())->gestionar($metodo);
        break;

    case "diagnosticos":
        require_once RUTA_CONTROLADOR . "/DiagnosticoControlador.php";
        (new DiagnosticoControlador())->gestionar($metodo);
        break;

    case "incidencias":
        require_once RUTA_CONTROLADOR . "/IncidenciaControlador.php";
        (new IncidenciaControlador())->gestionar($metodo);
        break;

    case "laboratorios":
        require_once RUTA_CONTROLADOR . "/LaboratorioControlador.php";
        (new LaboratorioControlador())->gestionar($metodo);
        break;

    case "prestamos":
        require_once RUTA_CONTROLADOR . "/PrestamoControlador.php";
        (new PrestamoControlador())->gestionar($metodo);
        break;

    case "registros":
        require_once RUTA_CONTROLADOR . "/RegistroControlador.php";
        (new RegistroControlador())->gestionar($metodo);
        break;

    case "solicitudes":
        require_once RUTA_CONTROLADOR . "/SolicitudControlador.php";
        (new SolicitudControlador())->gestionar($metodo);
        break;

    case "tickets":
        require_once RUTA_CONTROLADOR . "/TicketControlador.php";
        (new TicketControlador())->gestionar($metodo);
        break;

    default:
        RespuestaJson::error("Ruta no encontrada", 404);
}
