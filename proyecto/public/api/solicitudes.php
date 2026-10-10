<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/SolicitudControlador.php";

session_start();

$controlador = new SolicitudControlador();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
