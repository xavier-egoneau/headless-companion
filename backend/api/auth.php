<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../auth/jwt.php';
require_once __DIR__ . '/../utils/Logger.php';

use App\Models\User;
use App\Utils\Logger;

$logger = new Logger('auth.log');

header('Content-Type: application/json');

try {
    $logger->info("Tentative d'authentification reçue");

    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    $logger->info("Tentative de connexion pour l'utilisateur: " . $username);

    if (empty($username) || empty($password)) {
        throw new Exception("Le nom d'utilisateur et le mot de passe sont requis");
    }

    $user = User::findByUsername($username);

    if (!$user) {
        $logger->warning("Utilisateur non trouvé: " . $username);
        throw new Exception("Identifiants invalides");
    }

    if (!$user->verifyPassword($password)) {
        $logger->warning("Mot de passe incorrect pour l'utilisateur: " . $username);
        throw new Exception("Identifiants invalides");
    }

    $token = generateJWT($user->getId());
    $refreshToken = generateRefreshToken($user->getId());
    $logger->info("Authentification réussie pour l'utilisateur: " . $username);
    echo json_encode(['token' => $token, 'refreshToken' => $refreshToken]);

} catch (Exception $e) {
    $logger->error("Erreur d'authentification: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}