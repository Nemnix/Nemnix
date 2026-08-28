<?php

/**
 * Procesa la eliminación de un inventario enviada mediante POST.
 *
 * Valida la sesión del técnico y delega la eliminación de las relaciones
 * del inventario en BajaInventario.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/BajaInventario.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["administrador"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

$idInventario = trim($_POST["idInventario"] ?? "");

if (!ctype_digit($idInventario) || (int) $idInventario <= 0) {
    $mensaje = "Identificador de inventario inválido.";
    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}


$bajaInventario = new BajaInventario($conexion);
$resultado = $bajaInventario->eliminarInventario($idInventario);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar el inventario: " . $bajaInventario->obtenerUltimoError();
    header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Inventario eliminado exitosamente.";
header("Location: /proyecto/public/usuarioAdministrador/listaInventario.php?resultado=" . urlencode($mensaje));
exit;