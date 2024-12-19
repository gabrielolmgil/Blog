<?php
namespace utils;

class Auth {
    // Iniciar sesión (autenticar usuario)
    public static function login($userId) {
        // Establecer la variable de sesión con el ID del usuario
        session_start();
        $_SESSION['user_id'] = $userId;  // Almacenar el ID del usuario en la sesión
    }

    // Verificar si el usuario está autenticado
    public static function check() {
        session_start();
        // Comprobar si la variable de sesión del usuario está definida
        return isset($_SESSION['user_id']);
    }

    // Obtener el ID del usuario autenticado
    public static function userId() {
        session_start();
        return $_SESSION['user_id'] ?? null; // Retorna el ID del usuario o null si no está autenticado
    }

    // Cerrar sesión (destruir sesión)
    public static function logout() {
        session_start();
        // Eliminar todas las variables de sesión
        session_unset();
        // Destruir la sesión
        session_destroy();
    }
}
?>