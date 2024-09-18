<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Utils\Logger;

define('JWT_SECRET', 'your_secret_key_here'); // À remplacer par une clé secrète sécurisée
define('JWT_EXPIRATION', 3600); // 1 heure en secondes
define('JWT_REFRESH_EXPIRATION', 604800); // 1 semaine en secondes

function generateJWT($userId) {
    $logger = new Logger('jwt.log');
    $logger->info("Génération du JWT pour l'utilisateur ID: " . $userId);

    $issuedAt   = new DateTimeImmutable();
    $expire     = $issuedAt->modify('+' . JWT_EXPIRATION . ' seconds')->getTimestamp();
    $serverName = "your_server_name";
    
    $data = [
        'iat'  => $issuedAt->getTimestamp(),
        'iss'  => $serverName,
        'nbf'  => $issuedAt->getTimestamp(),
        'exp'  => $expire,
        'userId' => $userId,
    ];

    try {
        $jwt = JWT::encode($data, JWT_SECRET, 'HS256');
        $logger->info("JWT généré avec succès pour l'utilisateur ID: " . $userId);
        return $jwt;
    } catch (Exception $e) {
        $logger->error("Erreur lors de la génération du JWT: " . $e->getMessage());
        throw $e;
    }
}

function generateRefreshToken($userId) {
    $logger = new Logger('jwt.log');
    $logger->info("Génération du refresh token pour l'utilisateur ID: " . $userId);

    $issuedAt   = new DateTimeImmutable();
    $expire     = $issuedAt->modify('+' . JWT_REFRESH_EXPIRATION . ' seconds')->getTimestamp();
    $serverName = "your_server_name";
    
    $data = [
        'iat'  => $issuedAt->getTimestamp(),
        'iss'  => $serverName,
        'nbf'  => $issuedAt->getTimestamp(),
        'exp'  => $expire,
        'userId' => $userId,
        'type' => 'refresh'
    ];

    try {
        $jwt = JWT::encode($data, JWT_SECRET, 'HS256');
        $logger->info("Refresh token généré avec succès pour l'utilisateur ID: " . $userId);
        return $jwt;
    } catch (Exception $e) {
        $logger->error("Erreur lors de la génération du refresh token: " . $e->getMessage());
        throw $e;
    }
}

function verifyJWT($token) {
    $logger = new Logger('jwt.log');
    $logger->info("Vérification du JWT: " . $token);

    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        $logger->info("JWT décodé avec succès. User ID: " . $decoded->userId);
        return $decoded->userId;
    } catch (Exception $e) {
        $logger->error("Erreur de vérification du JWT: " . $e->getMessage());
        return null;
    }
}

function refreshJWT($refreshToken) {
    $logger = new Logger('jwt.log');
    $logger->info("Rafraîchissement du JWT");

    try {
        $decoded = JWT::decode($refreshToken, new Key(JWT_SECRET, 'HS256'));
        if ($decoded->type !== 'refresh') {
            throw new Exception('Token invalide');
        }
        $userId = $decoded->userId;
        $newToken = generateJWT($userId);
        $logger->info("Nouveau JWT généré pour l'utilisateur ID: " . $userId);
        return $newToken;
    } catch (Exception $e) {
        $logger->error("Erreur lors du rafraîchissement du JWT: " . $e->getMessage());
        return null;
    }
}