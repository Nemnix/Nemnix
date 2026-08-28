
<?php

/**
 * Carga la lista de prestamos de equipos y la pasa a la vista inventarioPrestamos.
 */

require_once __DIR__ . "/../../config/config.php";
require_once __DIR__ . "/../../config/env.php";
require_once RUTA_MODELO . "/ConectorPDO.php";
require_once RUTA_MODELO . "/AccesoPrestamos.php";

$conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
$conexion = $conectorPDO->establecerConexion();

    $accesoPrestamos = new AccesoPrestamos($conexion);
    $prestamos = $accesoPrestamos->listarPrestamos();
    
$conectorPDO->desconectar();

require_once RUTA_VISTA . "/inventarioPrestamos.php";

?>
