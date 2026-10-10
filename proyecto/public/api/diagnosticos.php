<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/DiagnosticoControlador.php";

session_start();

$controlador = new DiagnosticoControlador();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
