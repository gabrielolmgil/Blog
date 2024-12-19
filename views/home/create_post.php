<?php
require_once __DIR__ . '/../../models/post.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/PostController.php';

use controllers\PostController;
use models\Post;
use config\Database;
session_start();


$database = new Database();
$db = $database->getConnection();


$post = new Post($db);
$controller = new PostController($post);

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $data = [
        'title' => $_POST['title'] ?? null,
        'content' => $_POST['content'] ?? null,
        'user_id' => $_SESSION['user_id'] ?? null,
    ];

    try {
        // Crear el post utilizando el controlador
        $controller->create($data);
        header("Location: /blog/views/home/index.php");
    } catch (Exception $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

?>
