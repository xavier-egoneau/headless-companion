<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../auth/jwt.php';

use App\Models\Post;

header('Content-Type: application/json');

// Débogage
error_log("Requête reçue sur /api/posts");
error_log("Méthode: " . $_SERVER['REQUEST_METHOD']);
error_log("URI: " . $_SERVER['REQUEST_URI']);

// Vérification du token JWT
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token manquant ou invalide']);
    exit;
}

$token = $matches[1];
$userId = verifyJWT($token);

if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'Token invalide']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);
$postId = $uri[3] ?? null;

switch ($method) {
    case 'GET':
        error_log("Traitement de la requête GET");
        if ($postId) {
            // ... (code pour un post spécifique)
        } else {
            $page = $_GET['page'] ?? 1;
            $perPage = $_GET['per_page'] ?? 10;
            error_log("Page: $page, PerPage: $perPage");
            $posts = Post::findAll($page, $perPage);
            $totalPosts = Post::count();
            $totalPages = ceil($totalPosts / $perPage);
            error_log("Nombre de posts trouvés: " . count($posts));
            echo json_encode([
                'posts' => $posts,
                'page' => (int)$page,
                'per_page' => (int)$perPage,
                'total_posts' => $totalPosts,
                'total_pages' => $totalPages
            ]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $post = new Post($data);
        $post->setAuthorId($userId);
        $post->save();
        echo json_encode(['message' => 'Post créé avec succès', 'post' => $post->toArray()]);
        break;

    case 'PUT':
        if (!$postId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID du post manquant']);
            break;
        }
        $post = Post::findById($postId);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['error' => 'Post non trouvé']);
            break;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $post->update($data);
        echo json_encode(['message' => 'Post mis à jour avec succès', 'post' => $post->toArray()]);
        break;

    case 'DELETE':
        if (!$postId) {
            http_response_code(400);
            echo json_encode(['error' => 'ID du post manquant']);
            break;
        }
        $post = Post::findById($postId);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['error' => 'Post non trouvé']);
            break;
        }
        $post->delete();
        echo json_encode(['message' => 'Post supprimé avec succès']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}