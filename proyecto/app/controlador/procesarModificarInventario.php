<?php

/**
 * Procesa la modificación de un inventario enviada mediante POST.
 *
 * Valida la sesión del administrador, recoge los datos del formulario y delega
 * la actualización transaccional en AccesoInventario.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/accesoInventario.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["administrador"] ?? false) !== true) {
    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode("Acceso denegado."));
    exit;
}

$idLaboratorio = trim($_POST["ID_L"] ?? "");
$nombreLaboratorio = trim($_POST["nombreLaboratorio"] ?? "");
$numLaboratorio = trim($_POST["numLaboratorio"] ?? "");
$cantMonitores = trim($_POST["cantMonitores"] ?? "");
$cantTeclados = trim($_POST["cantTeclados"] ?? "");
$cantMouses = trim($_POST["cantMouses"] ?? "");
$cantComputadoras = trim($_POST["cantComputadoras"] ?? "");


if (!ctype_digit($idLaboratorio) || (int) $idLaboratorio <= 0 || $nombreLaboratorio === "" || $numLaboratorio === "" || $cantMonitores === "" || $cantTeclados === "" || $cantMouses === "" || $cantComputadoras === "") {
    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode("Todos los campos son obligatorios."));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();
$accesoInventario = new AccesoInventario($conexion);
$resultado = $accesoInventario->modificarInventario(
    (int) $idLaboratorio,
    $nombreLaboratorio,
    $numLaboratorio,
    (int) $cantMonitores,
    (int) $cantTeclados,
    (int) $cantMouses,
    (int) $cantComputadoras
);
$conectorPDO->desconectar();

$parametro = $resultado ? "resultado" : "error";
$mensaje = $resultado ? "Préstamo modificado exitosamente." : "No se pudo modificar el préstamo.";
header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?" . $parametro . "=" . urlencode($mensaje));
exit;
