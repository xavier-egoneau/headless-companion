<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Utils\Logger;

define('JWT_SECRET', 'your_secret_key_here'); // À remplacer par une clé secrète sécurisée

function generateJWT($userId) {
    $logger = new Logger('jwt.log');
    $logger->info("Génération du JWT pour l'utilisateur ID: " . $userId);

    $secretKey  = 'your_secret_key';  // À remplacer par une vraie clé secrète
    $issuedAt   = new DateTimeImmutable();
    $expire     = $issuedAt->modify('+1 hour')->getTimestamp();
    $serverName = "your_server_name";
    
    $data = [
        'iat'  => $issuedAt->getTimestamp(),
        'iss'  => $serverName,
        'nbf'  => $issuedAt->getTimestamp(),
        'exp'  => $expire,
        'userId' => $userId,
    ];

    try {
        $jwt = JWT::encode($data, $secretKey, 'HS256');
        $logger->info("JWT généré avec succès pour l'utilisateur ID: " . $userId);
        return $jwt;
    } catch (Exception $e) {
        $logger->error("Erreur lors de la génération du JWT: " . $e->getMessage());
        throw $e;
    }
}

function verifyJWT($token) {
    $logger = new Logger('jwt.log');
    $logger->info("Vérification du JWT: " . $token);

    $secretKey = 'your_secret_key';  // Assurez-vous que c'est la même clé que celle utilisée pour générer le token

    try {
        $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
        $logger->info("JWT décodé avec succès. User ID: " . $decoded->userId);
        return $decoded->userId;
    } catch (Exception $e) {
        $logger->error("Erreur de vérification du JWT: " . $e->getMessage());
        return null;
    }
}
function getUserIdFromToken($token) {
    $user_id = verifyJWT($token);
    if (!$user_id) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
        exit;
    }
    return $user_id;
}