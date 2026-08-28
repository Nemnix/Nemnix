<?php

/**
 * Punto de entrada publico que cierra la sesion del usuario actual.
 *
 * Destruye todos los datos de sesion y redirige al formulario de login.
 */

session_start();

$_SESSION = [];

session_destroy();

header("Location: login.php");
exit;

?>