<?php

/**
 * Procesa la eliminación de una solicitud de laboratorio enviada mediante POST.
 *
 * Valida la sesión del solicitante y delega la eliminación de la solicitud.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/bajaSolicitud.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["solicitante"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$ID_Lab = trim($_POST["ID_L"] ?? "");

if (!ctype_digit($ID_Lab) || (int) $ID_Lab <= 0) {
    $mensaje = "Identificador de laboratorio inválido.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$bajaSolicitud = new BajaSolicitud($conexion);
$resultado = $bajaSolicitud->eliminarSolicitud($ID_Lab);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar la solicitud.";
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Solicitud eliminada exitosamente.";
header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?resultado=" . urlencode($mensaje));
exit;