<?php
namespace models;

use PDO;
use PDOException;
use Exception;

class Post {
    private $conn;
    private $table_name = "posts";

    // Atributos privados del post
    private $id;
    private $title;
    private $content;
    private $user_id;
    private $publish_date;

    // Constructor para recibir la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método mágico para obtener el valor de un atributo
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new \Exception("Propiedad '$name' no existe en la clase " . __CLASS__);
    }

    // Método mágico para establecer el valor de un atributo
    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
            return;
        }
        throw new \Exception("Propiedad '$name' no puede ser establecida en la clase " . __CLASS__);
    }

    // Crear un nuevo post en la base de datos
    // Crear un nuevo post en la base de datos
    public function create() {
        if (empty($this->title) || empty($this->content)) {
            throw new \Exception("El título y el contenido no pueden estar vacíos.");
        }

        $query = "INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new \Exception("Error al crear el post.");
        }
    }

    // Leer todos los posts
    public function readAll() {
        $query = "SELECT id, title, content, user_id, publish_date FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Leer un post específico por ID
    public function readOne($id) {
        $query = "SELECT id, title, content, publish_date, user_id FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un post existente
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET title = :title, content = :content, user_id = :user_id
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Enlazar valores
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':user_id', $this->user_id);

        // Ejecutar la consulta
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar el post: " . $e->getMessage());
        }
    }

    // Eliminar un post
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        // Ejecutar la consulta
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar el post: " . $e->getMessage());
        }
    }

    // Obtener los comentarios de un post
    public function getComments($postId) {
        $query = "SELECT comments.id, comments.content, comments.publish_date, users.name 
                FROM comments 
                JOIN users ON comments.user_id = users.id 
                WHERE comments.post_id = :post_id
                ORDER BY comments.publish_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Método para obtener los posts de un usuario específico
    public function getPostsByUser($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}