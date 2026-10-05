<?php
require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_CONTROLADOR . "/usuariosControlador.php";

session_start();

$controlador = new UsuarioController();
$controlador->gestionar($_SERVER["REQUEST_METHOD"]);