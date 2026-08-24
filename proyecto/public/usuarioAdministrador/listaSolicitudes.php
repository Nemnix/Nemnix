<?php

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

require_once __DIR__ . "/../../app/controlador/cargarSolicita.php";

?>
