<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../auth/jwt.php';
require_once __DIR__ . '/../utils/Logger.php';
require_once __DIR__ . '/../config/database.php';

use App\Models\Post;
use App\Utils\Logger;

$logger = new Logger('posts.log');

header('Content-Type: application/json');

// Activation de l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$logger->info("Début du traitement de la requête posts");
$logger->info("Méthode de la requête: " . $_SERVER['REQUEST_METHOD']);
$logger->info("URI de la requête: " . $_SERVER['REQUEST_URI']);

try {
    // Vérification du token JWT
    $headers = getallheaders();
    $logger->info("En-têtes reçus: " . json_encode($headers));
    
    $authHeader = $headers['Authorization'] ?? '';
    $logger->info("Auth Header: " . $authHeader);

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $logger->error("Token manquant ou invalide");
        throw new Exception('Token manquant ou invalide');
    }

    $token = $matches[1];
    $userId = verifyJWT($token);

    if (!$userId) {
        $logger->error("Token invalide");
        throw new Exception('Token invalide');
    }

    $logger->info("Token validé pour l'utilisateur ID: " . $userId);

    $method = $_SERVER['REQUEST_METHOD'];
    $urlParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $postId = isset($urlParts[2]) ? intval($urlParts[2]) : null;

    switch ($method) {
        case 'GET':
            if ($postId) {
                $logger->info("Récupération du post avec ID: " . $postId);
                $post = Post::findById($postId);
                if ($post) {
                    echo json_encode($post->toArray());
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Post non trouvé']);
                }
            } else {
                $logger->info("Récupération de tous les posts");
                $posts = Post::findAll();
                $logger->info("Nombre de posts trouvés: " . count($posts));
                echo json_encode(['posts' => array_map(function($post) { return $post->toArray(); }, $posts)]);
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
            throw new Exception('Méthode non autorisée');
    }

} catch (Exception $e) {
    $logger->error("Erreur dans posts.php: " . $e->getMessage());
    $logger->error("Trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur', 'message' => $e->getMessage()]);
}

$logger->info("Fin du traitement de la requête posts");