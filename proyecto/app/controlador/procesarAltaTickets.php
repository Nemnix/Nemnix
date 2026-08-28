<?php

/**
 * Procesa el alta de un ticket enviada mediante POST.
 *
 * Valida la sesion del solicitante y delega el registro en AltaTicket.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AltaTicket.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";

    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

if (!isset($_SESSION["cedula"]) || !isset($_SESSION["solicitante"]) || $_SESSION["solicitante"] !== true) {
    $mensaje = "Debe iniciar sesión como solicitante para registrar un ticket.";

    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$idRegistro = (int) trim($_POST["ID_R"] ?? "0");
$tipoDispositivo = trim($_POST["idDispositivo"] ?? "");
$nombreDispositivo = trim($_POST["nombreDispositivo"] ?? "");
$nombreAlumno = trim($_POST["nombreAlumno"] ?? "");
$descripcionT = trim($_POST["motivo_Ticket"] ?? "");
$prioridad = trim($_POST["prioridad"] ?? "");
$estado = "Abierto";

$nombreEquipo = trim($tipoDispositivo . " " . $nombreDispositivo);

if ($idRegistro <= 0 || empty($tipoDispositivo) || empty($nombreDispositivo) || empty($descripcionT) || empty($prioridad)) {
    $mensaje = "Error: Todos los campos son obligatorios.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);

$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";

    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$altaTicket = new AltaTicket($conexion);

$resultado = $altaTicket->registrarTicket(
    $idRegistro,
    $estado,
    $nombreEquipo,
    $nombreAlumno,
    $descripcionT,
    $prioridad
);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar el ticket: " . $altaTicket->obtenerUltimoError();

    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Ticket guardado exitosamente.";
header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?resultado=" . urlencode($mensaje));
exit;

?>
