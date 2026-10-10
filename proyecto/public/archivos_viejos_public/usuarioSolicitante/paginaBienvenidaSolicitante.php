<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: public/usuarioSolicitante/paginaBienvenidaSolicitante.php
 * Reemplazado por: public/paginas/paginaBienvenidaSolicitante.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php

/**
 * Punto de entrada publico de la pagina de bienvenida del usuario solicitante.
 *
 * Requiere sesion iniciada con rol solicitante; en caso contrario
 * redirige al login.
 * /

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
*/
