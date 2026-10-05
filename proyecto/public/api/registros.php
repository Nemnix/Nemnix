<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/registroControlador.php";

session_start();

$controlador = new registroControlador();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
