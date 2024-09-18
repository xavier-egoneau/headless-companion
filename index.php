<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';

use App\Utils\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$logger = new Logger('app.log');

// Initialisation de Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader, [
    'cache' => __DIR__ . '/cache/twig',
    'auto_reload' => true,
]);

try {
    $logger->info("Requête reçue: " . $_SERVER['REQUEST_URI']);
    
    // Gestion des CORS
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");

    $request = $_SERVER['REQUEST_URI'];

    if ($request === '/' || $request === '') {
        $logger->info("Rendu de la page d'accueil");
        echo $twig->render('home.twig');
    } elseif (strpos($request, '/api/posts') === 0) {
        $logger->info("Redirection vers posts.php");
        require __DIR__ . '/backend/api/posts.php';
    } elseif ($request === '/api/auth') {
        $logger->info("Redirection vers auth.php");
        require __DIR__ . '/backend/api/auth.php';
    } elseif ($request === '/admin' || $request === '/admin/') {
        $logger->info("Rendu de la page admin");
        echo $twig->render('admin.twig', ['currentTime' => new DateTime()]);
    } else {
        $logger->warning("Route non trouvée: " . $request);
        http_response_code(404);
        echo $twig->render('404.twig');
    }
} catch (Throwable $e) {
    $logger->error("Erreur non gérée: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    http_response_code(500);
    echo $twig->render('error.twig', ['error' => 'Une erreur interne est survenue']);
}