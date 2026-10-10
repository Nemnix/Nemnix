<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/LaboratorioControlador.php";

session_start();

$controlador = new LaboratorioControlador();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
