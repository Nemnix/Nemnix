<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: public/usuarioSolicitante/estadoTickets.php
 * Reemplazado por: public/paginas/estadoTickets.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php

/**
 * Punto de entrada publico del estado de tickets del usuario solicitante.
 *
 * Requiere sesion iniciada con rol solicitante; en caso contrario
 * redirige al login con un mensaje de error. Delega la carga de datos
 * en el controlador cargarEstadoTickets.
 * /

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

if (!isset($_SESSION["solicitante"]) || $_SESSION["solicitante"] !== true) {
    $mensaje = "Acceso Denegado: Rol incorrecto";
    header("Location:  /proyecto/public/login.php?error=" . urlencode($mensaje));
    exit;
}

require_once __DIR__ . "/../../app/controlador/cargarEstadoTickets.php";

?>
*/
