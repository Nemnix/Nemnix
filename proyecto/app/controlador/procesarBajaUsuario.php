<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/BajaDatosUsuario.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["administrador"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

$cedula = trim($_POST["cedula"] ?? "");

if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
    $mensaje = "No se pudo eliminar el usuario: cédula incorrecta o usuario inexistente.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

$bajaDatosUsuario = new BajaDatosUsuario($conexion);
$resultado = $bajaDatosUsuario->eliminarUsuario($cedula);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo eliminar el usuario.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Usuario eliminado exitosamente.";
header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?resultado=" . urlencode($mensaje));
exit;
