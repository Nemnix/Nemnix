// <?php
// 
// /**
//  * Carga la lista de usuarios del sistema y la pasa a la vista panelAdministradores.
//  */
// 
// require_once __DIR__ . "/../../config/config.php";
// require_once __DIR__ . "/../../config/env.php";
// require_once RUTA_MODELO . "/ConectorPDO.php";
// require_once RUTA_MODELO . "/AccesoDatosUsuario.php";
// 
// $conectorPDO = new ConectorPDO(DB_HOST . ":" . DB_PUERTO, DB_USUARIO, DB_CLAVE, DB_NOMBRE);
// $conexion = $conectorPDO->establecerConexion();
// 
//     $accesoDatosUsuario = new AccesoDatosUsuario($conexion);
//     $usuarios = $accesoDatosUsuario->listarUsuarios();
// 
// $conectorPDO->desconectar();
// 
// require_once RUTA_VISTA . "/panelAdministradores.php";
// 
// ?>
