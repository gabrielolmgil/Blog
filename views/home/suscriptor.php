<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/post.php';
require_once __DIR__ . '/../../models/comment.php';

use config\Database;
use models\Post;
use models\Comment;

$database = new Database();
$db = $database->getConnection();

$postModel = new Post($db);
$commentModel = new Comment($db);

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $posts = $postModel->readAll(); // Obtener todos los posts

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subscribe'])) {
        $post_id = $_POST['post_id'];

        // Verificar si ya está suscrito
        if (!$commentModel->isSubscribed()) {
            $commentModel->user_id = $user_id;
            $commentModel->post_id = $post_id;
            $commentModel->created_at = date('Y-m-d H:i:s');
            $commentModel->subscribe();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
        $post_id = $_POST['post_id'];
        $content = $_POST['content'];

        // Agregar el comentario
        $commentModel->post_id = $post_id;
        $commentModel->user_id = $user_id;
        $commentModel->content = $content;
        $commentModel->created_at = date('Y-m-d H:i:s');

        if ($commentModel->addComment()) {
            header("Location: suscriptor.php?post_id=" . $post_id); // Redirige al post donde se hizo el comentario
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suscripción a Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-6">
    <div class="w-full max-w-3xl bg-red-100 shadow-md rounded-lg p-6">
        <!-- Botón de Cerrar Sesión -->
        <a href="../user/logout.php" class="text-red-500 hover:underline block mb-6">Cerrar Sesión</a>

        <!-- Título Principal -->
        <h1 class="text-2xl font-bold text-gray-900 mb-6 text-center">Suscripción a Posts</h1>

        <!-- Lista de Posts Disponibles -->
        <?php if (isset($posts) && count($posts) > 0): ?>
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Posts Disponibles</h2>
            <ul class="space-y-6">
                <?php foreach ($posts as $post): ?>
                    <li class="bg-white shadow-sm rounded-lg p-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($post['content']); ?></p>


                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600 text-center">No hay posts disponibles.</p>
        <?php endif; ?>
    </div>
</body>
</html>