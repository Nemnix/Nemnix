<?php

/**
 * Procesa el inicio de sesion enviado mediante POST.
 *
 * Autentica la cédula y clave contra `AccesoDatosUsuario` a través de `Login`,
 * inicia la sesión y redirige a la página de bienvenida según el rol del usuario.
 *
 * Expected POST parameters:
 * - `cedula` (string): número de cédula del usuario.
 * - `clave` (string): contraseña en texto plano recibida desde el formulario.
 *
 * Session values set on success:
 * - `cedula` (string)
 * - `administrador` (bool)
 * - `tecnico` (bool)
 * - `solicitante` (bool)
 *
 * @package Proyecto\App\Controlador
 * @author Nahuel
 * @license MIT
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/Usuario.php";
require_once RUTA_MODELO . "/AccesoDatosUsuario.php";
require_once RUTA_MODELO . "/Login.php";





//Comprueba que el formulario haya sido enviado mediante POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

//Recupera las credenciales provenientes del formulario
$cedula = trim($_POST["cedula"] ?? "");
$clave = $_POST["clave"] ?? "";

$conectorPDO = new ConectorPDO(DB_HOST, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
    $login = new Login($accesoDatosUsuario);



$usuario = $login->autenticar($cedula, $clave);

$conectorPDO->desconectar();

//Si las credenciales no coinciden, muestra el error y detiene el proceso
if ($usuario === null) {
    $mensaje = "Acceso Denegado: La cédula o la contraseña son incorrectas.";
    header("Location: /proyecto/public/login.php?" . "error=" . $mensaje);
    exit;
}

session_start();
session_regenerate_id(true);

$_SESSION["cedula"] = $usuario->getCedula();
$_SESSION["administrador"] = $usuario->esAdministrador();
$_SESSION["tecnico"] = $usuario->esTecnico();
$_SESSION["solicitante"] = $usuario->esSolicitante();

if ($usuario->sinRoles()) {
    $mensaje = "Acceso Denegado: El usuario no tiene un rol asignado.";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

if ($usuario->esAdministrador() && $usuario->esTecnico()) {
    header("Location: ../public/panelRoles.php");
} elseif ($usuario->esTecnico()) {
    header("Location: usuarioTecnico/paginaBienvenidaTecnico.php");
} elseif ($usuario->esAdministrador()) {
    header("Location: usuarioAdministrador/paginaBienvenidaAdministrador.php");
} elseif ($usuario->esSolicitante()) {
    header("Location: usuarioSolicitante/paginaBienvenidaSolicitante.php");
}

exit;


?>