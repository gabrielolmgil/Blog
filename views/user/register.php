<?php
// Iniciar sesión para redirigir después del registro
session_start();

// Incluir el archivo de autenticación y el modelo de Usuario
require_once __DIR__ . '/../../utils/Auth.php';
require_once __DIR__ . '/../../models/user.php';
require_once __DIR__ . '/../../config/database.php';

use config\Database;
use models\User;
use utils\Auth;

// Conectar a la base de datos
$database = new Database();
$db = $database->getConnection();

// Crear una instancia del modelo de Usuario
$userModel = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'user';

    if (empty($email) || empty($name) || empty($password) || empty($confirm_password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Validar hash generado
        if ($hashed_password === false) {
            $error = "Error al procesar la contraseña.";
        } else {
            $userModel->name = $name;
            $userModel->email = $email;
            $userModel->password = $hashed_password;
            $userModel->role = $role;

            try {
                $userModel->create();
                header("Location: login.php");
                exit;
            } catch (Exception $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-red-100 shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 text-center mb-6">Registrarse</h1>

        <!-- Mensaje de error -->
        <?php if (isset($error)): ?>
            <div class="bg-red-200 text-red-700 p-4 mb-4 rounded-lg">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Formulario de registro -->
        <form action="register.php" method="POST" class="space-y-6">
            <!-- Nombre de Usuario -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-900">Nombre de usuario:</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    class="w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-red-300 focus:border-red-300" 
                    required>
            </div>

            <!-- Correo Electrónico -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-900">Correo electrónico:</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    class="w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-red-300 focus:border-red-300" 
                    required>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-900">Contraseña:</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    class="w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-red-300 focus:border-red-300" 
                    required>
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-900">Confirmar contraseña:</label>
                <input 
                    type="password" 
                    name="confirm_password" 
                    id="confirm_password" 
                    class="w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-sm p-2 focus:ring-red-300 focus:border-red-300" 
                    required>
            </div>

            <!-- Botón de Registro -->
            <button 
                type="submit" 
                class="w-full bg-red-300 text-white py-2 rounded-lg shadow-md hover:bg-red-400 transition">
                Registrarse
            </button>
        </form>

        <!-- Enlace a inicio de sesión -->
        <p class="mt-6 text-gray-600 text-center">
            ¿Ya tienes cuenta? 
            <a href="login.php" class="text-red-500 hover:underline">Inicia sesión aquí</a>.
        </p>
    </div>
</body>
</html>