<?php

/**
 * Carga registros diarios y/o tickets segun el parametro GET "tipo"
 * ("registros", "tickets" o "ambos") y muestra la vista correspondiente.
 *
 * Requiere sesion iniciada.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoRegistroYTickets.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Validar sesión de usuario
if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

// Determinar qué listar: 'registros', 'tickets' o 'ambos'
$tipo = $_GET['tipo'] ?? 'registros';

$conectorPDO = new ConectorPDO("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

$acceso = new AccesoRegistroYTickets($conexion);

if ($tipo === 'tickets') {
    $tickets = $acceso->listarTicketsConRegistro();
    $conectorPDO->desconectar();
    require_once RUTA_VISTA . "/listaTicketsEntrantes.php";
    exit;
} elseif ($tipo === 'ambos') {
    $registros = $acceso->listarRegistros();
    $tickets = $acceso->listarTicketsConRegistro();
    $conectorPDO->desconectar();
    // Cargar una vista que muestre ambos (si existe) o la de registros por defecto
    if (file_exists(RUTA_VISTA . "/listaRegistroDiario.php")) {
        require_once RUTA_VISTA . "/listaRegistroDiario.php";
    } else {
        require_once RUTA_VISTA . "/listaTicketsEntrantes.php";
    }
    exit;
} else {
    // por defecto listar registros diarios
    $registros = $acceso->listarRegistros();
    $conectorPDO->desconectar();
    require_once RUTA_VISTA . "/listaRegistroDiario.php";
    exit;
}

?>
