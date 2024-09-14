<?php

// Nettoyage de toute sortie précédente
ob_start();

// Forcer l'affichage des erreurs en développement
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure la configuration d'environnement
require_once __DIR__ . '/backend/config/env.php';

// Le reste de la configuration reste inchangé
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';

// Nettoyage de toute sortie générée par les includes
ob_clean();

// Gestion des CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestion des requêtes OPTIONS (pre-flight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$request = $_SERVER['REQUEST_URI'];

try {
    if (strpos($request, '/api/posts') === 0) {
        require __DIR__ . '/backend/api/posts.php';
    } elseif ($request === '/api/auth') {
        require __DIR__ . '/backend/api/auth.php';
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
} catch (Exception $e) {
    error_log("Erreur inattendue dans index.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
}