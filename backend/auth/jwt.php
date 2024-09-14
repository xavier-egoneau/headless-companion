<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

define('JWT_SECRET', 'your_secret_key_here'); // À remplacer par une clé secrète sécurisée

function generateJWT($user_id) {
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; // Token valide pendant 1 heure

    $payload = [
        'iat' => $issuedAt,
        'exp' => $expirationTime,
        'user_id' => $user_id
    ];

    return JWT::encode($payload, JWT_SECRET, 'HS256');
}

function verifyJWT($token) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        return $decoded->user_id;
    } catch (Exception $e) {
        return false;
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