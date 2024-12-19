<?php
namespace controllers;

use models\Post;

class HomeController {
    private $postModel;

    // Constructor para inicializar el modelo Post
    public function __construct(Post $post) {
        $this->postModel = $post;
    }

    // Mostrar la página principal con todas las publicaciones
    public function index() {
        // Obtener todas las publicaciones
        $posts = $this->postModel->readAll();

        // Pasar las publicaciones a la vista
        include 'views/home/index.php'; // Incluir la vista con los datos de las publicaciones
    }

    // Mostrar una publicación específica
    public function show($id) {
        // Obtener una publicación por su ID
        $post = $this->postModel->readOne($id);

        // Si no se encuentra la publicación, pasar un mensaje de error
        if (!$post) {
            $errorMessage = "Publicación no encontrada.";
            include 'views/home/show.php'; // Vista para mostrar el mensaje de error
            return; // Finalizar el método
        }

        // Si la publicación existe, pasarla a la vista
        include 'views/home/show.php'; // Vista para mostrar la publicación
    }
}