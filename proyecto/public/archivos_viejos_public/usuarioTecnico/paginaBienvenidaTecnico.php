<?php
/*
 * ARCHIVO VIEJO (no se usa). Movido y comentado el 2026-10-07 (corrección 1 del profesor).
 * Ubicación original: public/usuarioTecnico/paginaBienvenidaTecnico.php
 * Reemplazado por: public/paginas/paginaBienvenidaTecnico.html
 * Todo el código está comentado para que no se ejecute. Dentro del código
 * los cierres de comentario internos (asterisco + barra) se cambiaron por '* /'.
 * ------------------------------------------------------------------------

<?php

/**
 * Punto de entrada publico de la pagina de bienvenida del usuario tecnico.
 *
 * Requiere sesion iniciada con rol tecnico; en caso contrario
 * redirige al login.
 * /

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
*/
