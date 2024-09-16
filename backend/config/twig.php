<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/../../cache/twig',
    'auto_reload' => true, // Ceci est important pour le développement
    'debug' => true // Activez le mode debug pour le développement
]);

// Ajoutez l'extension Debug si nécessaire
$twig->addExtension(new \Twig\Extension\DebugExtension());

return $twig; 