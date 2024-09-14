<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once '../vendor/autoload.php';
require_once '../config/database.php';
require_once '../auth/jwt.php';

$request_method = $_SERVER["REQUEST_METHOD"];
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Vérifier le token JWT
$headers = getallheaders();
$jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

if (!$jwt || !verifyJWT($jwt)) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

// Router simple
switch ($request_path) {
    case '/api/posts':
        require 'posts.php';
        break;
    case '/api/users':
        require 'users.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Not found"]);
        break;
}