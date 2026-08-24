<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/altaDiagnostico.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

// Validar sesión de técnico
if (!isset($_SESSION["cedula"]) || !isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true) {
    $mensaje = "Debe iniciar sesión como técnico para registrar un diagnóstico.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

// Recoger datos del formulario
$ticket = trim($_POST['TicketAsociado'] ?? "");
$estado = trim($_POST['estado'] ?? "");
$fecha = trim($_POST['fechaDiagnostico'] ?? "");
$descripcion = trim($_POST['descripcionDiagnostico'] ?? "");

// Validación básica
if ($ticket === "") {
    $mensaje = "El ticket asociado es obligatorio.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

if (empty($estado) || empty($fecha) || empty($descripcion)) {
    $mensaje = "Todos los campos (estado, fecha y descripción) son obligatorios.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$idT = is_numeric($ticket) ? (int)$ticket : null;

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$altaDiagnostico = new AltaDiagnostico($conexion);

$resultado = $altaDiagnostico->registrarDiagnostico($idT, null, $estado, $fecha, $descripcion);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar el diagnóstico: " . $altaDiagnostico->obtenerUltimoError();
    header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Diagnóstico registrado correctamente.";
header("Location: /proyecto/public/usuarioTecnico/listaDiagnosticos.php?resultado=" . urlencode($mensaje));
exit;

?>
