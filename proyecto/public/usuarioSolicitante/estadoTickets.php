<?php
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