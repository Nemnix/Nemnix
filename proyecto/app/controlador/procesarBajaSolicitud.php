<?php

/**
 * Procesa la eliminacion de una solicitud de laboratorio enviada mediante POST.
 *
 * Valida la sesion del solicitante y delega la eliminacion en BajaSolicitud.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/bajaSolicitudAdmin.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["solicitante"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$ID_L = trim($_POST["ID_L"] ?? "");

if (!ctype_digit($ID_L) || (int) $ID_L <= 0) {
    $mensaje = "Identificador de laboratorio inválido.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$bajaSolicitud = new BajaSolicitud($conexion);
$resultado = $bajaSolicitud->eliminarSolicitud($ID_L);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar la solicitud.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Solicitud eliminada exitosamente.";
header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?resultado=" . urlencode($mensaje));
exit;
