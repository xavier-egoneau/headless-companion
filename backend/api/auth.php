<?php

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../auth/jwt.php';

use App\Models\User;

header('Content-Type: application/json');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$method = $_SERVER['REQUEST_METHOD'];

function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    $jsonResponse = json_encode($data);
    error_log("Sending response: " . $jsonResponse);
    echo $jsonResponse;
    exit;
}

try {
    error_log("Received request: " . $method);
    
    switch ($method) {
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            error_log("Received input: " . json_encode($input));
            
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';

            error_log("Login attempt for user: " . $username);

            if (empty($username) || empty($password)) {
                sendJsonResponse(['error' => 'Username and password are required'], 400);
            }

            $user = User::findByUsername($username);

            if (!$user) {
                error_log("User not found: " . $username);
                sendJsonResponse(['error' => 'Invalid credentials'], 401);
            }

            if (!$user->verifyPassword($password)) {
                error_log("Incorrect password for user: " . $username);
                sendJsonResponse(['error' => 'Invalid credentials'], 401);
            }

            $token = generateJWT($user->getId());
            sendJsonResponse(['token' => $token]);

        case 'GET':
            sendJsonResponse(['message' => 'Auth endpoint is working. Use POST to login.']);

        default:
            sendJsonResponse(['error' => 'Method not allowed'], 405);
    }
} catch (Exception $e) {
    error_log("Unexpected error in auth.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendJsonResponse(['error' => 'Internal Server Error', 'message' => $e->getMessage()], 500);
}