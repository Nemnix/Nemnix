<?php

/**
 * Procesa la eliminación de un registro diario enviada mediante POST.
 *
 * Valida la sesión del solicitante y delega la eliminación de las relaciones
 * del registro en BajaRegistro.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/BajaRegistro.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["solicitante"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$idRegistro = trim($_POST["idRegistro"] ?? "");

if (!ctype_digit($idRegistro) || (int) $idRegistro <= 0) {
    $mensaje = "Identificador de registro inválido.";
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

$bajaRegistro = new BajaRegistro($conexion);
$resultado = $bajaRegistro->eliminarRegistro($idRegistro);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar el registro.";
    header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Registro eliminado exitosamente.";
header("Location: /proyecto/public/usuarioSolicitante/listaRegistroDiario.php?resultado=" . urlencode($mensaje));
exit;
