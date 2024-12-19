<?php
session_start();

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/post.php';
require_once __DIR__ . '/../../models/comment.php';
require_once __DIR__ . '/../../models/user.php';  

use config\Database;
use models\Post;
use models\Comment;
use models\User; 

// Inicializar las variables para evitar errores de "variable indefinida"
$name = ''; 
$posts = [];  // Para almacenar los posts del usuario
$comments = [];  // Para almacenar los comentarios de un post

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_id'])) {
    // Obtener el nombre de usuario desde la base de datos
    $database = new Database();
    $db = $database->getConnection();
    $userModel = new User($db);  // Usar tu modelo de usuario
    $user_id = $_SESSION['user_id'];
    
    // Obtener el nombre del usuario
    $user = $userModel->find($user_id); // Método que deberías crear en tu clase User
    $name = $user['name']; // Suponiendo que el campo se llama 'name'

    // Obtener los posts del usuario
    $postModel = new Post($db);
    $posts = $postModel->getPostsByUser($user_id);  // Método para leer posts por user_id

    // Comprobar si se está editando un post
    if (isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
        $post = $postModel->readOne($post_id); // Obtener el post desde la base de datos

        // Obtener los comentarios del post
        $commentModel = new Comment($db);
        $comments = $commentModel->getComments($post_id); // Obtener los comentarios para el post
    }
} else {
    // Si el usuario no está autenticado, redirigir a login
    header("Location: ../user/login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido, <?php echo htmlspecialchars($name); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-6 text-center">
    <div class="w-full max-w-3xl bg-red-100 shadow-md rounded-lg p-6 mt-44">
        <h1 class="text-2xl font-bold text-gray-900 mb-4 ">
            Bienvenido, <?php echo htmlspecialchars($name); ?>!
        </h1>
        <a href="../user/logout.php" class="text-red-500 hover:underline mb-6 block">Cerrar sesión</a>

        <!-- Mostrar los posts del usuario -->
        <h2 class="text-xl font-semibold text-black mb-4">Mis Posts</h2>
        <?php if (count($posts) > 0): ?>
            <ul class="space-y-2">
                <?php foreach ($posts as $post): ?>
                    <li>
                        <a href="index.php?post_id=<?php echo $post['id']; ?>" class="text-red-500  hover:underline">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-900">No tienes posts creados.</p>
        <?php endif; ?>

        <!-- Formulario para crear o editar un post -->
        <h2 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
            <?php echo isset($post) ? 'Editar Post' : 'Crear Nuevo Post'; ?>
        </h2>
        <form action="/blog/views/home/create_post.php" method="POST" class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-900">Título:</label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="<?php echo isset($post) ? htmlspecialchars($post['title']) : ''; ?>" 
                    class="w-full  bg-white rounded-lg shadow-sm h-8 p-2" 
                    required>
            </div>
            <div>
                <label for="content" class="block text-sm font-medium text-gray-900">Contenido:</label>
                <textarea 
                    name="content" 
                    id="content" 
                    class="w-full rounded-lg shadow-sm p-2" 
                    required><?php echo isset($post) ? htmlspecialchars($post['content']) : ''; ?></textarea>
            </div>
            <button 
                type="submit" 
                class="bg-red-300 text-white px-4 py-2 rounded-lg shadow-md hover:bg-red-400">
                <?php echo isset($post) ? 'Actualizar Post' : 'Crear Post'; ?>
            </button>
        </form>

        <?php if (isset($message)): ?>
            <div class="bg-green-100 text-green-700 p-4 mt-4 rounded-lg">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>

        <!-- Mostrar los comentarios del post -->
        <?php if (isset($post) && !empty($comments)): ?>
            <h3 class="text-lg font-semibold text-gray-700 mt-8 mb-4">Comentarios:</h3>
            <ul class="space-y-4">
                <?php foreach ($comments as $comment): ?>
                    <li class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <strong class="text-gray-800"><?php echo htmlspecialchars($comment['name']); ?>:</strong>
                        <p class="text-gray-600"><?php echo htmlspecialchars($comment['content']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif (isset($post)): ?>
            <p class="text-gray-600 mt-4">No hay comentarios en este post.</p>
        <?php endif; ?>
    </div>
</body>
</html>