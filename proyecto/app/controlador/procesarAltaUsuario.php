<?php

/**
 * Procesa el alta de un usuario del sistema enviada mediante POST.
 *
 * Valida la sesion del administrador, valida y hashea la contraseña,
 * y delega el registro en AltaDatosUsuario.
 */

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/AltaDatosUsuario.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_SESSION["cedula"]) || ($_SESSION["administrador"] ?? false) !== true) {
    $mensaje = "Acceso denegado.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

//Recupera los datos provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$apellido = trim($_POST["apellido"] ?? "");

$clave = $_POST["clave"] ?? "";
$confirmarClave = $_POST["confirmarClave"] ?? "";

$rol = trim($_POST["rol"] ?? "");

//Sección que valida los datos recibidos del formulario
if ($cedula === "" || $nombre === "" || $apellido === "" || $clave === "" || $confirmarClave === "" || $rol === "") {
    $mensaje = "No se pudo registrar el usuario: existen campos vacíos.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

if (!preg_match("/^[1-9][0-9]{7}$/", $cedula)) {
    $mensaje = "No se pudo registrar el usuario: cédula incorrecta.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

if (strlen($clave) < 12) {
    $mensaje = "La contraseña debe contener al menos 12 caracteres.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

if ($clave !== $confirmarClave) {
    $mensaje = "Las contraseñas ingresadas no coinciden.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

//Hasheo de la contraseña para almacenar en la base de datos
$claveHash = password_hash($clave, PASSWORD_DEFAULT);

$conectorPDO = new ConectorPDO("localhost", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

if ($conexion === null) {
    $mensaje = "No se pudo establecer conexión con la base de datos.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

$altaDatosUsuario = new AltaDatosUsuario($conexion);
$resultado = $altaDatosUsuario->registrarUsuario($cedula, $nombre, $apellido, $claveHash, $rol);

$conectorPDO->desconectar();

if (!$resultado) {
    $mensaje = "No se pudo registrar el usuario.";
    header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?error=" . urlencode($mensaje));
    exit;
}

$mensaje = "Usuario ingresado exitosamente.";
header("Location: /proyecto/public/usuarioAdministrador/panelAdministradores.php?resultado=" . urlencode($mensaje));
exit;
