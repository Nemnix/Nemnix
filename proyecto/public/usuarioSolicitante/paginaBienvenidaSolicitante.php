<?php

/**
 * Punto de entrada publico de la pagina de bienvenida del usuario solicitante.
 *
 * Requiere sesion iniciada con rol solicitante; en caso contrario
 * redirige al login.
 */

session_start();

if (!isset($_SESSION["cedula"])) {
    header("Location: login.php");
    exit;
}

if ( !isset($_SESSION["solicitante"]) || $_SESSION["solicitante"] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../../app/vista/paginaBienvenidaSolicitante.php";

?>