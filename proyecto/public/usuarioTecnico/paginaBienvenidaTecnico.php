<?php

/**
 * Punto de entrada publico de la pagina de bienvenida del usuario tecnico.
 *
 * Requiere sesion iniciada con rol tecnico; en caso contrario
 * redirige al login.
 */

session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}

if ( !isset($_SESSION["tecnico"]) || $_SESSION["tecnico"] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../../app/vista/paginaBienvenidaTecnico.php";

?>