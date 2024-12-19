<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión y si tiene el rol de 'admin'

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/user.php';

use config\Database;
use models\User;

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);

// Variables para manejar el estado de la acción
$action = isset($_GET['action']) ? $_GET['action'] : 'view';  // 'view' es la acción por defecto

// Crear usuario
if ($action == 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $role = trim($_POST['role']);

    $userModel->name = $name;
    $userModel->email = $email;
    $userModel->password = $password;
    $userModel->role = $role;

    if ($userModel->create()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Hubo un error al crear el usuario.";
    }
}

// Eliminar usuario
if ($action == 'delete' && isset($_GET['id'])) {
    $userId = $_GET['id'];

    if ($userModel->delete($userId)) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Hubo un error al eliminar el usuario.";
    }
}

// Editar usuario
if ($action == 'edit' && isset($_GET['id'])) {
    $userId = $_GET['id'];
    $userData = $userModel->readOne($userId);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']) ? password_hash(trim($_POST['password']), PASSWORD_DEFAULT) : $userData['password'];
        $role = trim($_POST['role']);

        $userModel->id = $userId;
        $userModel->name = $name;
        $userModel->email = $email;
        $userModel->password = $password;
        $userModel->role = $role;

        if ($userModel->update()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Hubo un error al actualizar el usuario.";
        }
    }
}

// Listar usuarios
$users = $userModel->readAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-6">
    <div class="w-full max-w-4xl bg-red-100 shadow-md rounded-lg p-6">
        <!-- Encabezado -->
        <header class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard - Administrador</h1>
            <a href="../user/logout.php" class="text-red-500 hover:underline">Cerrar sesión</a>
        </header>

        <!-- Sección para crear o editar usuarios -->
        <?php if ($action == 'create'): ?>
        <section class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Crear nuevo usuario</h2>
            <form action="dashboard.php?action=create" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900">Nombre:</label>
                    <input type="text" name="name" class="w-full bg-white rounded-lg shadow-sm p-2" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900">Correo electrónico:</label>
                    <input type="email" name="email" class="w-full bg-white rounded-lg shadow-sm p-2" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900">Contraseña:</label>
                    <input type="password" name="password" class="w-full bg-white rounded-lg shadow-sm p-2" required>
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-900">Rol:</label>
                    <select name="role" class="w-full bg-white rounded-lg shadow-sm p-2" required>
                        <option value="Admin">Admin</option>
                        <option value="Escritor">Escritor</option>
                        <option value="Suscriptor">Suscriptor</option>
                    </select>
                </div>
                <button type="submit" class="bg-red-300 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-400">Crear Usuario</button>
            </form>
        </section>
        <?php elseif ($action == 'edit' && isset($userData)): ?>
        <section class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Editar usuario</h2>
            <form action="dashboard.php?action=edit&id=<?php echo $userId; ?>" method="POST" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900">Nombre:</label>
                    <input type="text" name="name" value="<?php echo $userData['name']; ?>" class="w-full bg-white rounded-lg shadow-sm p-2" required>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900">Correo electrónico:</label>
                    <input type="email" name="email" value="<?php echo $userData['email']; ?>" class="w-full bg-white rounded-lg shadow-sm p-2" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900">Contraseña (dejar en blanco para no cambiar):</label>
                    <input type="password" name="password" class="w-full bg-white rounded-lg shadow-sm p-2">
                </div>
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-900">Rol:</label>
                    <select name="role" class="w-full bg-white rounded-lg shadow-sm p-2" required>
                        <option value="Admin" <?php echo $userData['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="Escritor" <?php echo $userData['role'] == 'Escritor' ? 'selected' : ''; ?>>Escritor</option>
                        <option value="Suscriptor" <?php echo $userData['role'] == 'Suscriptor' ? 'selected' : ''; ?>>Suscriptor</option>
                    </select>
                </div>
                <button type="submit" class="bg-red-300 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-400">Actualizar Usuario</button>
            </form>
        </section>
        <?php else: ?>
        <section>
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Gestión de usuarios</h2>
            <a href="dashboard.php?action=create" class="bg-red-300 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-400 inline-block mb-6">Crear nuevo usuario</a>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Usuarios Registrados</h3>
            <table class="w-full bg-white rounded-lg shadow-md overflow-hidden">
                <thead class="bg-red-300 text-white">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Nombre</th>
                        <th class="p-4">Correo</th>
                        <th class="p-4">Rol</th>
                        <th class="p-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr class="border-b">
                        <td class="p-4"><?php echo $user['id']; ?></td>
                        <td class="p-4"><?php echo $user['name']; ?></td>
                        <td class="p-4"><?php echo $user['email']; ?></td>
                        <td class="p-4"><?php echo $user['role']; ?></td>
                        <td class="p-4">
                            <a href="dashboard.php?action=edit&id=<?php echo $user['id']; ?>" class="text-blue-500 hover:underline mr-2">Editar</a>
                            <a href="dashboard.php?action=delete&id=<?php echo $user['id']; ?>" class="text-red-500 hover:underline" onclick="return confirm('¿Estás seguro de eliminar a este usuario?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <?php endif; ?>
    </div>
</body>
</html>

