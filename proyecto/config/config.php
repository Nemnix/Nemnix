<?php

/**
 * Configuracion global de la aplicacion.
 *
 * Define las constantes de ruta utilizadas por controladores y vistas
 * para ubicar los directorios app/modelo, app/controlador, app/vista y public.
 */

define("RUTA_RAIZ", dirname(__DIR__));

define("RUTA_APP", RUTA_RAIZ . "/app");
define("RUTA_MODELO", RUTA_APP . "/modelo");
define("RUTA_CONTROLADOR", RUTA_APP . "/controlador");
define("RUTA_VISTA", RUTA_APP . "/vista");
define("RUTA_NUCLEO", RUTA_APP . "/nucleo");

define("RUTA_PUBLIC", RUTA_RAIZ);
?>