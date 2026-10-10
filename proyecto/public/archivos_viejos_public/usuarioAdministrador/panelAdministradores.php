<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: public/usuarioAdministrador/panelAdministradores.php
 * Reemplazado por: public/paginas/panelAdministradores.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php

/**
 * Punto de entrada publico del panel de gestion de usuarios.
 *
 * Requiere sesion iniciada con rol administrador; en caso contrario
 * redirige al login con un mensaje de error. Delega la carga de datos
 * en el controlador cargarPanelAdministradores.
 * /

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

require_once __DIR__ . "/../../app/controlador/cargarPanelAdministradores.php";

?>
*/
