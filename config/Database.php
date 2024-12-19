<?php
namespace config;
use PDO;
use PDOException;


class Database{
    private $conn;
    private $host = 'localhost';
    private $db_name = 'blog';
    private $name = 'root';
    private $password = '';

    //metodo para obtener la conexion
    public function getConnection(){
        $this->conn = null;

        try{
            //establecer la conexion usando PDO
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->name, $this->password);

            //configurar el conjunto de caracteres UTF-8
            $this->conn->exec("set names utf8");

            //establecer el modo de errores de PDO a excepcion
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $exception){
            echo "Error de conexion: " . $exception->getMessage();
        }

        return $this->conn;
    }
}


?>