<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: public/usuarioAdministrador/paginaBienvenidaAdministrador.php
 * Reemplazado por: public/paginas/paginaBienvenidaAdministrador.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php

/**
 * Punto de entrada publico de la pagina de bienvenida del usuario administrador.
 *
 * Requiere sesion iniciada con rol administrador; en caso contrario
 * redirige al login con un mensaje de error.
 * /

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION["cedula"])) {
    $mensaje = "Acceso Denegado: Sesión no iniciada";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

if ( !isset($_SESSION["administrador"]) || $_SESSION["administrador"] !== true ) {
    $mensaje = "Acceso Denegado: Rol incorrecto";
    header("Location: login.php?" . "error=" . $mensaje);
    exit;
}

require_once __DIR__ . "/../../app/vista/paginaBienvenidaAdministrador.php";

?>
*/
