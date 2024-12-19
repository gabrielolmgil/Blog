<?php
session_start();

$message = '';  // Inicializar el mensaje

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/user.php';

use config\Database;
use models\User;

// Crear instancia de la base de datos y del modelo User
$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Por favor, ingresa tu correo y contraseña.";
    } else {
        $user = $userModel->findByEmail($email);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                // Guardar los datos del usuario en la sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];  // Guardamos el name en la sesión

                // Redirección por rol
                switch (strtolower($user['role'])) {
                    case 'admin':
                        header("Location: ../home/dashboard.php");
                        break;
                    case 'writer':
                        header("Location: ../home/index.php");
                        break;
                    case 'user':
                        header("Location: ../home/suscriptor.php");
                        break;
                    default:
                        $message = "Rol no válido.";
                }
                exit;
            } else {
                $message = "Correo o contraseña incorrectos.";
            }
        } else {
            $message = "Correo o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-red-100 shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 text-center mb-6">Iniciar sesión</h1>
        <form action="login.php" method="POST" class="space-y-6">
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

            <!-- Botón de Inicio de Sesión -->
            <button 
                type="submit" 
                class="w-full bg-red-300 text-white py-2 rounded-lg shadow-md hover:bg-red-400 transition">
                Iniciar sesión
            </button>
        </form>

        <!-- Mensaje de error o éxito -->
        <?php if (!empty($message)): ?>
            <div class="bg-green-100 text-green-700 p-4 mt-4 rounded-lg">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Registro -->
        <p class="mt-6 text-gray-600 text-center">
            ¿No tienes cuenta? 
            <a href="register.php" class="text-red-500 hover:underline">Regístrate aquí</a>.
        </p>
    </div>
</body>
</html>
