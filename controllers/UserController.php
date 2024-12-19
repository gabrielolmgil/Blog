<?php
namespace controllers;

use models\User;

class UserController {
    private $userModel;

    // Constructor que recibe una instancia del modelo User
    public function __construct(User $user) {
        $this->userModel = $user;
    }

    // Método para registrar un nuevo usuario
    public function register($data) {
        // Validar datos de entrada
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            throw new \Exception("Todos los campos son obligatorios.");
        }

        // Verificar si el correo electrónico ya está registrado
        if ($this->userModel->emailExists($data['email'])) {
            throw new \Exception("El correo electrónico ya está registrado.");
        }

        // Hash de la contraseña antes de guardar
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Llamada al método del modelo para guardar el nuevo usuario
        if ($this->userModel->create($data)) {
            return "Usuario registrado con éxito.";
        } else {
            throw new \Exception("Error al registrar el usuario.");
        }
    }

    // Método para iniciar sesión
    public function login($email, $password) {
        // Verificar si el usuario existe
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            throw new \Exception("El usuario no existe.");
        }

        // Verificar la contraseña
        if (!password_verify($password, $user['password'])) {
            throw new \Exception("La contraseña es incorrecta.");
        }

        // Aquí podrías iniciar la sesión o generar un token si usas JWT
        $_SESSION['user_id'] = $user['id'];

        return "Inicio de sesión exitoso.";
    }

    // Método para obtener los detalles de un usuario
    public function getUserDetails($userId) {
        $user = $this->userModel->getUserById($userId);
        if (!$user) {
            throw new \Exception("El usuario no existe.");
        }

        return $user;
    }

    // Método para actualizar los detalles del usuario
    public function updateUser($userId, $data) {
        // Validación de datos, por ejemplo, si se cambia la contraseña, se debe volver a hashear
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Actualizar datos del usuario
        if ($this->userModel->update($userId, $data)) {
            return "Datos de usuario actualizados correctamente.";
        } else {
            throw new \Exception("Error al actualizar los datos del usuario.");
        }
    }
}
?>