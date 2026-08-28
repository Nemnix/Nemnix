<?php

/**
 * Procesa el alta de un laboratorio enviada mediante POST.
 *
 * Valida la sesion del administrador y delega el registro en AltaLaboratorios.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/altaLaboratorios.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $mensaje = "Acceso Denegado: Petición incorrecta";

    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

//Comprueba que haya un administrador logueado (cedula es NOT NULL y FK en la tabla LABORATORIO)
if (!isset($_SESSION["cedula"]) || !isset($_SESSION["administrador"]) || $_SESSION["administrador"] !== true) {
    $mensaje = "Debe iniciar sesión como administrador para registrar un laboratorio.";

    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

//Recupera los datos del formulario, con variables nombradas según las columnas de LABORATORIO
$cedula = $_SESSION["cedula"];
$idLaboratorio = "";
$nombreLaboratorio = trim($_POST["nombreLaboratorio"] ?? "");
$numLaboratorio = trim($_POST["numLaboratorio"] ?? "");
$cantMonitores = trim($_POST["cantMonitores"] ?? "");
$cantTeclados = trim($_POST["cantTeclados"] ?? "");
$cantMouses = trim($_POST["cantMouses"] ?? "");
$cantComputadoras = trim($_POST["cantComputadoras"] ?? "");


// Validar que todos los campos obligatorios estén completos
if (empty($nombreLaboratorio) || empty($numLaboratorio) || empty($cantMonitores) || empty($cantTeclados) || empty($cantMouses) || empty($cantComputadoras)) {
    $mensaje = "Error: Todos los campos son obligatorios.";
    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);

$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";

    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

$altaLaboratorios = new AltaLaboratorios($conexion);

$resultado = $altaLaboratorios->registrarLaboratorio($cedula, $idLaboratorio, $nombreLaboratorio, $numLaboratorio, $cantMonitores, $cantTeclados, $cantMouses, $cantComputadoras);

$conectorPDO->desconectar();


if (!$resultado) {
    $mensaje = "No se pudo registrar el laboratorio: " . $altaLaboratorios->obtenerUltimoError();

    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Laboratorio registrado exitosamente.";
header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?resultado=" . urlencode($mensaje));
exit;

?>
