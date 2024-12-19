<?php
namespace controllers;

use models\Post; // Asegúrate de usar la clase Post del namespace models

class PostController {
    private $postModel;

    // Constructor para inicializar el modelo Post
    public function __construct(Post $post) {
        $this->postModel = $post;
    }

    // Crear una nueva publicación
    public function create($data) {
        // Asignar los datos del post al modelo
        $this->postModel->title = $data['title'];
        $this->postModel->content = $data['content'];
        $this->postModel->user_id = $data['user_id'];
        

        // Intentar crear la publicación
        if ($this->postModel->create()) {
            return ["success" => "Publicación creada con éxito."];
        } else {
            return ["error" => "Hubo un problema al crear la publicación."];
        }
    }

    // Leer todas las publicaciones
    public function readAll() {
        $posts = $this->postModel->readAll();
        if ($posts) {
            return ["success" => "Publicaciones obtenidas con éxito.", "posts" => $posts];
        } else {
            return ["error" => "No se encontraron publicaciones."];
        }
    }

    // Leer una publicación específica por ID
    public function readOne($id) {
        $post = $this->postModel->readOne($id);
        if ($post) {
            return ["success" => "Publicación obtenida con éxito.", "post" => $post];
        } else {
            return ["error" => "Publicación no encontrada."];
        }
    }

    // Actualizar una publicación existente
    public function update($id, $data) {
        // Asignar los nuevos valores al modelo
        $this->postModel->id = $id;
        $this->postModel->title = $data['title'];
        $this->postModel->content = $data['content'];
        $this->postModel->user_id = $data['user_id'];

        // Intentar actualizar la publicación
        if ($this->postModel->update()) {
            return ["success" => "Publicación actualizada con éxito."];
        } else {
            return ["error" => "Hubo un problema al actualizar la publicación."];
        }
    }

    // Eliminar una publicación
    public function delete($id) {
        // Intentar eliminar la publicación
        if ($this->postModel->delete($id)) {
            return ["success" => "Publicación eliminada con éxito."];
        } else {
            return ["error" => "Hubo un problema al eliminar la publicación."];
        }
    }
}