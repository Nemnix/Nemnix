<?php

/**
 * Punto de entrada publico de la vista global de incidencias (tickets).
 *
 * Requiere sesion iniciada con rol administrador; en caso contrario
 * redirige al login con un mensaje de error. Delega la carga de datos
 * en el controlador cargarIncidencias.
 */

session_start();

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

if (!isset($_SESSION["administrador"]) || $_SESSION["administrador"] !== true) {
    $mensaje = "Acceso Denegado: Rol incorrecto";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

require_once __DIR__ . "/../../app/controlador/cargarIncidencias.php";

?>