<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/TicketControlador.php";

session_start();

$controlador = new TicketControlador();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);
