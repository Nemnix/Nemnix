<?php

/**
 * Carga las variables definidas en el archivo .env (ubicado en la raiz del
 * proyecto) usando la libreria phpdotenv, y las expone como constantes
 * globales para la conexion a la base de datos.
 *
 * Este archivo debe ubicarse en la misma carpeta que config.php
 * (ej: /config/env.php) y ser incluido antes de instanciar ConectorPDO.
 */

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

if (!defined('DB_HOST')) {
    define('DB_HOST', $_ENV['DB_HOST']);
}
if (!defined('DB_PUERTO')) {
    define('DB_PUERTO', $_ENV['DB_PUERTO']);
}
if (!defined('DB_USUARIO')) {
    define('DB_USUARIO', $_ENV['DB_USUARIO']);
}
if (!defined('DB_CLAVE')) {
    define('DB_CLAVE', $_ENV['DB_CLAVE']);
}
if (!defined('DB_NOMBRE')) {
    define('DB_NOMBRE', $_ENV['DB_NOMBRE']);
}

if (!defined('JWT_SECRET')) {
    define('JWT_SECRET', $_ENV['JWT_SECRET']);
}
if (!defined('JWT_EXPIRA')) {
    define('JWT_EXPIRA', (int) $_ENV['JWT_EXPIRA']);
}

?>