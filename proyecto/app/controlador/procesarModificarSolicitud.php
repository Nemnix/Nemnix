<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/accesoSolicitudes.php";

session_start();
error_log("POST recibido: " . print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["solicitante"] ?? false) !== true) {
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode("Acceso denegado."));
    exit;
}

$ID_L = trim($_POST["ID_L"] ?? "");
$nombreLaboratorio = trim($_POST["preparacionLaboratorio"] ?? "");
$numLaboratorio = trim($_POST["numLabTaller"] ?? "");
$fecha = trim($_POST["fechaLaboratorio"] ?? "");
$descripcionSolicitud = trim($_POST["motivoSolicitudes"] ?? "");
$gmail = trim($_POST["mailDocente"] ?? "");

if (!ctype_digit($ID_L) || (int) $ID_L <= 0) {
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode("Identificador de laboratorio inválido."));
    exit;
}

if ($nombreLaboratorio === "" || $numLaboratorio === "" || $fecha === "" || $descripcionSolicitud === "" || $gmail === "") {
    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode("Todos los campos son obligatorios."));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();
$accesoSolicitudes = new AccesoSolicitudes($conexion);
$resultado = $accesoSolicitudes->modificarSolicitud($ID_L, $nombreLaboratorio, $numLaboratorio, $fecha, $descripcionSolicitud, $gmail);
$conectorPDO->desconectar();

$parametro = $resultado ? "resultado" : "error";
$mensaje = $resultado ? "Solicitud modificada exitosamente." : "No se pudo modificar la solicitud.";
header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?" . $parametro . "=" . urlencode($mensaje));
exit;