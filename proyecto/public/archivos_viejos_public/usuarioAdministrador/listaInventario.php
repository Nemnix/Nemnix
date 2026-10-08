<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: public/usuarioAdministrador/listaInventario.php
 * Reemplazado por: public/paginas/listaInventario.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/**
 * Punto de entrada publico que muestra el inventario tecnologico
 * dentro del panel del usuario administrador.
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

require_once __DIR__ . "/../../app/controlador/cargarInventario.php";

?>
*/
