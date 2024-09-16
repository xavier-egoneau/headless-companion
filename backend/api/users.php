<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Logger.php';

use App\Models\User;
use App\Utils\Logger;

$logger = new Logger('users_api.log');

header('Content-Type: application/json');

// Vérification du token JWT (à implémenter)

$method = $_SERVER['REQUEST_METHOD'];
$userId = isset($_GET['id']) ? intval($_GET['id']) : null;

try {
    switch ($method) {
        case 'GET':
            if ($userId) {
                $user = User::findById($userId);
                if ($user) {
                    echo json_encode($user->toArray());
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Utilisateur non trouvé']);
                }
            } else {
                $users = User::findAll();
                echo json_encode(['users' => array_map(fn($u) => $u->toArray(), $users)]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $user = new User($data);
            $user->save();
            echo json_encode(['message' => 'Utilisateur créé avec succès', 'user' => $user->toArray()]);
            break;

        case 'PUT':
            if (!$userId) {
                http_response_code(400);
                echo json_encode(['error' => 'ID utilisateur manquant']);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $user = User::findById($userId);
            if ($user) {
                $user->setUsername($data['username'] ?? $user->getUsername());
                $user->setEmail($data['email'] ?? $user->getEmail());
                if (isset($data['password'])) {
                    $user->setPassword($data['password']);
                }
                $user->save();
                echo json_encode(['message' => 'Utilisateur mis à jour avec succès', 'user' => $user->toArray()]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Utilisateur non trouvé']);
            }
            break;

        case 'DELETE':
            if (!$userId) {
                http_response_code(400);
                echo json_encode(['error' => 'ID utilisateur manquant']);
                break;
            }
            $user = User::findById($userId);
            if ($user) {
                $user->delete();
                echo json_encode(['message' => 'Utilisateur supprimé avec succès']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Utilisateur non trouvé']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            break;
    }
} catch (Exception $e) {
    $logger->error("Erreur dans l'API utilisateurs : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur interne du serveur']);
}