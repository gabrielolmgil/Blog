<?php
namespace models;

use PDO;
use PDOException;

class Comment {
    private $conn;
    private $table_name = "comments";

    // Atributos privados del comentario
    private $id;
    private $content;
    private $post_id;
    private $user_id;
    private $created_at;

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

    // Crear un nuevo comentario
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (content, post_id, user_id, created_at)
                  VALUES (:content, :post_id, :user_id, :created_at)";

        $stmt = $this->conn->prepare($query);

        // Enlazar valores
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':created_at', $this->created_at);

        // Ejecutar la consulta
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al crear el comentario: " . $e->getMessage());
        }
    }

    // Leer todos los comentarios de un post
    public function readAll($post_id) {
        $query = "SELECT id, content, post_id, user_id, created_at FROM " . $this->table_name . " WHERE post_id = :post_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Leer un comentario específico por ID
    public function readOne($id) {
        $query = "SELECT id, content, post_id, user_id, created_at FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar un comentario existente
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET content = :content, updated_at = current_timestamp()
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Enlazar valores
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':content', $this->content);

        // Ejecutar la consulta
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al actualizar el comentario: " . $e->getMessage());
        }
    }

    // Eliminar un comentario
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        // Ejecutar la consulta
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error al eliminar el comentario: " . $e->getMessage());
        }
    }
        // Método para verificar si el usuario ya está suscrito a un post
    public function isSubscribed() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id AND post_id = :post_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->execute();

        // Si hay una fila, significa que el usuario ya está suscrito
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // Método para suscribir al usuario a un post
    public function subscribe() {
        $sql = "INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content); // Asegúrate de que $content tenga un valor.
        $stmt->execute();
    }

    public function addComment() {
        $query = "INSERT INTO " . $this->table_name . " (post_id, user_id, content, created_at) 
                  VALUES (:post_id, :user_id, :content, :created_at)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':created_at', $this->created_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getCommentsByPost() {
        $query = "SELECT c.content, c.created_at, u.name 
                  FROM " . $this->table_name . " c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.post_id = :post_id
                  ORDER BY c.created_at ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $this->post_id);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Obtener los comentarios de un post
    public function getComments($postId) {
        $query = "SELECT comments.id, comments.content, comments.created_at, users.name 
                FROM comments 
                JOIN users ON comments.user_id = users.id 
                WHERE comments.post_id = :post_id
                ORDER BY comments.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':post_id', $postId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>