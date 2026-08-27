<?php

/**
 * Procesa el alta de un registro diario enviada mediante POST.
 *
 * Valida la sesion del solicitante y delega el registro en AltaRegistro.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/altaRegistro.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";

    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

//Comprueba que haya un técnico logueado (cedula es NOT NULL y FK en la tabla PRESTAMO)
if (!isset($_SESSION["cedula"]) || !isset($_SESSION["solicitante"]) || $_SESSION["solicitante"] !== true) {
    $mensaje = "Debe iniciar sesión como técnico para registrar un préstamo.";

    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

//Recupera los datos del formulario, con variables nombradas según las columnas de PRESTAMO

$nombreLaboratorio = trim($_POST["preparacionLaboratorio"] ?? "");
$numLaboratorio = trim($_POST["numLabTaller"] ?? "");
$grupo = trim($_POST["grupoRegistroDiario"] ?? "");
$horaInicio = trim($_POST["horaEntrada"] ?? "");
$horaFin = trim($_POST["horaSalida"] ?? "");
$asignatura = trim($_POST["asignaturaRegistroDiario"] ?? "");
$fecha = trim($_POST["fechaRegistroDiario"] ?? "");
$turno = trim($_POST["turnoRegistroDiario"] ?? "");

// Validar que todos los campos obligatorios estén completos
if (empty($nombreLaboratorio) || empty($numLaboratorio) || empty($grupo) || empty($horaInicio) || empty($horaFin) || empty($asignatura) || empty($fecha) || empty($turno)) {
    $mensaje = "Error: Todos los campos son obligatorios.";
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

$altaRegistro = new AltaRegistro($conexion);

$$resultado = $altaRegistro->registrarRegistro(
    (string) $_SESSION["cedula"],
    $grupo,
    $horaInicio,
    $horaFin,
    $asignatura,
    $fecha,
    $nombreLaboratorio,
    $numLaboratorio,
    $turno
);

$conectorPDO->desconectar();


if (!$resultado) {
    $mensaje = "No se pudo registrar el registro: " . $altaRegistro->obtenerUltimoError();

    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Registro guardado exitosamente.";
header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?resultado=" . urlencode($mensaje));
exit;

?>