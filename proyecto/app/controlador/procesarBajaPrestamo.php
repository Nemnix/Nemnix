<?php

/**
 * Procesa la eliminación de un préstamo enviada mediante POST.
 *
 * Valida la sesión del técnico y delega la eliminación de las relaciones
 * del préstamo en BajaPrestamo.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/BajaPrestamo.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["tecnico"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

$idPrestamo = trim($_POST["idPrestamo"] ?? "");

if (!ctype_digit($idPrestamo) || (int) $idPrestamo <= 0) {
    $mensaje = "Identificador de préstamo inválido.";
    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

$bajaPrestamo = new BajaPrestamo($conexion);
$resultado = $bajaPrestamo->eliminarPrestamo($idPrestamo);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar el préstamo.";
    header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Préstamo eliminado exitosamente.";
header("Location: /proyecto/public/usuarioTecnico/inventarioPrestamos.php?resultado=" . urlencode($mensaje));
exit;