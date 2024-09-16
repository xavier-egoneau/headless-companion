<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../auth/jwt.php';
require_once __DIR__ . '/../utils/Logger.php';

use App\Models\Post;
use App\Utils\Logger;
header('Content-Type: application/json');

// Activation de l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$logger = new Logger('posts.log');
$logger->info("Requête reçue : " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI']);
$logger->info("En-têtes reçus : " . json_encode(getallheaders()));


try {
    $logger->info("Début du traitement de la requête posts");

    // Vérification du token JWT
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    $logger->info("Auth Header: " . $authHeader);

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $logger->error("Token manquant ou invalide");
        http_response_code(401);
        echo json_encode(['error' => 'Token manquant ou invalide']);
        exit;
    }

    $token = $matches[1];
    $userId = verifyJWT($token);

    if (!$userId) {
        $logger->error("Token invalide");
        throw new Exception('Token invalide');
    }

    $logger->info("Token validé pour l'utilisateur ID: " . $userId);

    $posts = Post::findAll();
    $logger->info("Nombre de posts trouvés : " . count($posts));

    $postsArray = array_map(function($post) {
        return $post->toArray();
    }, $posts);

    echo json_encode(['posts' => $postsArray]);
    $logger->info("Posts récupérés et envoyés avec succès");

} catch (Exception $e) {
    $logger->error("Erreur dans posts.php: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur', 'message' => $e->getMessage()]);
}