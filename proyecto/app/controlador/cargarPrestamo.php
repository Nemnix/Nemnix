
<?php

require_once __DIR__ . "/../../config/config.php";
require_once RUTA_MODELO . "/conectorPDO.php";
require_once RUTA_MODELO . "/accesoPrestamos.php";

$conectorPDO = new ConectorPDO ("localhost:3306", "Tecnoemmn", "123", "db nemnix");
$conexion = $conectorPDO->establecerConexion();

    $accesoPrestamos = new AccesoPrestamos($conexion);
    $prestamos = $accesoPrestamos->listarPrestamos();
    
$conectorPDO->desconectar();

require_once RUTA_VISTA . "/inventarioPrestamos.php";

?>
