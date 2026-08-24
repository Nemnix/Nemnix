<?php

/**
 * Procesa la eliminación de un ticket enviada mediante POST.
 *
 * Valida la sesión del solicitante y delega la eliminación en BajaTicket.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/BajaTicket.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["solicitante"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$idRegistro = (int) trim($_POST["ID_R"] ?? "0");
$idTicket = (int) trim($_POST["ID_T"] ?? "0");

if ($idRegistro <= 0 || $idTicket <= 0) {
    $mensaje = "Identificador de ticket inválido.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$bajaTicket = new BajaTicket($conexion);
$resultado = $bajaTicket->eliminarTicket($idRegistro, $idTicket);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar el ticket.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Ticket eliminado correctamente.";
header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?resultado=" . urlencode($mensaje));
exit;
