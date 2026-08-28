<?php

/**
 * Procesa el alta de una solicitud de laboratorio enviada mediante POST.
 *
 * Valida la sesion del solicitante y delega el registro en AltaSolicitud.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/altaSolicitud.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";

    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

//Comprueba que haya un técnico logueado (cedula es NOT NULL y FK en la tabla PRESTAMO)
if (!isset($_SESSION["cedula"]) || !isset($_SESSION["solicitante"]) || $_SESSION["solicitante"] !== true) {
    $mensaje = "Debe iniciar sesión como solicitante para registrar una solicitud.";

    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

//Recupera los datos del formulario, con variables nombradas según las columnas de PRESTAMO

$cedulaTecnico = $_SESSION["cedula"];
$nombreLab  = trim($_POST["nombreLaboratorio"] ?? "");
$numLaboratorio  = trim($_POST["numLaboratorio"] ?? "");
$fechaLab = trim($_POST["fecha"] ?? "");
$descripcionLab = trim($_POST["descripcionSolicitud"] ?? "");
$gmailDocente    = trim($_POST["gmail"] ?? "");

// Validar que todos los campos obligatorios estén completos
if (empty($numLaboratorio) || empty($nombreLab) || empty($fechaLab) || empty($descripcionLab) || empty($gmailDocente)) {
    $mensaje = "Error: Todos los campos son obligatorios.";
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

$altaSolicitud = new AltaSolicitud($conexion);

$resultado = $altaSolicitud->registrarSolicitud($cedulaTecnico, $nombreLab, $numLaboratorio, $fechaLab, $descripcionLab, $gmailDocente);

$conectorPDO->desconectar();


if (!$resultado) {
    $mensaje = "No se pudo registrar la solicitud: " . $altaSolicitud->obtenerUltimoError();

    header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Solicitud registrada exitosamente.";
header("Location: /proyecto/public/usuarioSolicitante/solicitudesLabs.php?resultado=" . urlencode($mensaje));
exit;

?>
