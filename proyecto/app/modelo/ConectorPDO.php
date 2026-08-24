<?php

/**
 * Encapsula la creación y el cierre de una conexión PDO hacia MySQL.
 */
class ConectorPDO
{
    private string $servername;
    private string $username;
    private string $password;
    private string $dbname;
    private ?PDO $conexion;

    /**
     * Constructor parametrizado con los datos de conexión a la base de datos.
     * @param string $servername Servidor (y puerto opcional) de la base de datos.
     * @param string $username Usuario de la base de datos.
     * @param string $password Contraseña del usuario.
     * @param string $dbname Nombre de la base de datos.
     */
    public function __construct (string $servername, string $username, string $password, string $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
        $this->conexion = null;
    }

    /**
     * Abre la conexión PDO con los datos provistos en el constructor.
     * @return PDO La conexión establecida, o null si ocurrió un error.
     */
    public function establecerConexion(): PDO {
        try {
            $this->conexion = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);

            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error al conectar..." . $e->getMessage();
        }
        return $this->conexion;
    }

    /**
     * Libera la referencia a la conexión activa.
     */
    public function desconectar() {
        $this->conexion = null;
    }
};


?>