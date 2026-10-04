<?php

// /**
//  * Clase encargada de modificar los datos
//  * relacionados con usuarios del sistema.
//  */
// class ModificarDatosUsuario
// {
//     private PDO $conexion;

//     /**
//      * Constructor parametrizado que recibe una conexión
//      * a la base de datos.
//      *
//      * @param PDO $conexion Conexión a la base de datos. PRECONDICIÓN: No debe ser NULL.
//      */
//     public function __construct(PDO $conexion)
//     {
//         $this->conexion = $conexion;
//     }

//     /**
//      * Modifica los datos de un usuario y su rol.
//      *
//      * @param string $cedula Cédula del usuario a modificar.
//      * @param string $nombre Nuevo nombre.
//      * @param string $apellido Nuevo apellido.
//      * @param string $claveHash Nuevo hash de contraseña.
//      * @param string $rol Rol seleccionado (Administrador, Tecnico o Solicitante).
//      *
//      * @return bool TRUE si la modificación se realiza correctamente, FALSE en caso contrario.
//      */
//     public function modificarUsuario(string $cedula, string $nombre, string $apellido, string $claveHash, string $rol): bool {
//         try {
//             $this->conexion->beginTransaction();

//             $sqlUsuario = "UPDATE USUARIO SET nombre = :nombre, apellido = :apellido, claveHash = :claveHash WHERE cedula = :cedula";
//             $consultaUsuario = $this->conexion->prepare($sqlUsuario);
//             $consultaUsuario->execute(["nombre" => $nombre, "apellido" => $apellido, "claveHash" => $claveHash, "cedula" => $cedula]);

//             switch ($rol) {
//                 case "Administrador":
//                     $tablaRol = "ADMINISTRADOR";
//                     break;
//                 case "Tecnico":
//                     $tablaRol = "TECNICO";
//                     break;
//                 case "Solicitante":
//                     $tablaRol = "SOLICITANTE";
//                     break;
//                 default:
//                     $this->conexion->rollBack();
//                     return false;
//             }

//             // Quita al usuario de los otros roles, ya que este panel solo permite un rol activo a la vez.
//             foreach (["ADMINISTRADOR", "TECNICO", "SOLICITANTE"] as $tabla) {
//                 if ($tabla === $tablaRol) {
//                     continue;
//                 }

//                 $consultaQuitarRol = $this->conexion->prepare("DELETE FROM $tabla WHERE cedula = :cedula");
//                 $consultaQuitarRol->execute(["cedula" => $cedula]);
//             }

//             $consultaBuscarRol = $this->conexion->prepare("SELECT cedula FROM $tablaRol WHERE cedula = :cedula");
//             $consultaBuscarRol->execute(["cedula" => $cedula]);

//             if ($consultaBuscarRol->fetch(PDO::FETCH_ASSOC) === false) {
//                 $consultaAsignarRol = $this->conexion->prepare("INSERT INTO $tablaRol (cedula) VALUES (:cedula)");
//                 $consultaAsignarRol->execute(["cedula" => $cedula]);
//             }

//             $this->conexion->commit();
//             return true;

//         } catch (PDOException $error) {
//             if ($this->conexion->inTransaction()) {
//                 $this->conexion->rollBack();
//             }

//             error_log("Error en ModificarDatosUsuario: " . $error->getMessage());

//             return false;
//         }
//     }
// }
?>