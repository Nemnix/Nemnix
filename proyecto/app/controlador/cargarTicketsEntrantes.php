<?php

/**
 * Carga el listado de tickets entrantes del usuario tecnico.
 *
 * Valida la sesion y el rol tecnico antes de cargar los datos
 * y pasarlos a la vista listaTicketsEntrantes.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoTickets.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Validar sesión de técnico
if (!isset($_SESSION["cedula"]) || !isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true) {
    $mensaje = "Acceso Denegado: Debe iniciar sesión como técnico.";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioTecnico/listaTicketsEntrantes.php?error=" . urlencode($mensaje));
    exit;
}

$accesoTickets = new AccesoTickets($conexion);
$tickets = $accesoTickets->listarTickets();

$conectorPDO->desconectar();

require_once RUTA_VISTA . "/listaTicketsEntrantes.php";

?>
