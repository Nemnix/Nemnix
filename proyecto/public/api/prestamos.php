<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/PrestamoControlador.php";

session_start();

$controlador = new PrestamoControlador();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
