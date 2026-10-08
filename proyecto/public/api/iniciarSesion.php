<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/LoginControlador.php";

session_start();

$controlador = new LoginControlador();
$controlador->autenticar($_SERVER["REQUEST_METHOD"]);
?>