<?php

/**
 * Punto de entrada publico que recibe el POST del formulario de login
 * y delega la autenticacion en el controlador correspondiente.
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../app/controlador/procesarLogin.php";

?>