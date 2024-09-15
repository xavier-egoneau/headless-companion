<?php

require_once __DIR__ . '/../../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/../../cache/twig',
    'auto_reload' => true, // Utile en développement
]);

return $twig;