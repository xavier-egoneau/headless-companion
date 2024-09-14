<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnostic CMS Headless</h1>";

// Vérification de la version PHP
echo "<h2>Version PHP</h2>";
echo "<p>Version PHP : " . phpversion() . "</p>";

// Vérification des extensions
echo "<h2>Extensions PHP</h2>";
$required_extensions = ['sqlite3', 'json', 'pdo_sqlite'];
foreach ($required_extensions as $ext) {
    echo "<p>$ext : " . (extension_loaded($ext) ? "Chargée" : "Non chargée") . "</p>";
}

// Test de connexion à la base de données
echo "<h2>Connexion à la base de données</h2>";
try {
    require_once __DIR__ . '/backend/config/database.php';
    $db = Database::getInstance();
    echo "<p>Connexion à la base de données : Réussie</p>";
} catch (Exception $e) {
    echo "<p>Erreur de connexion à la base de données : " . $e->getMessage() . "</p>";
}

// Vérification des fichiers importants
echo "<h2>Vérification des fichiers</h2>";
$important_files = [
    '/index.php',
    '/backend/api/auth.php',
    '/backend/api/posts.php',
    '/backend/models/User.php',
    '/backend/models/Post.php',
    '/backend/auth/jwt.php'
];
foreach ($important_files as $file) {
    echo "<p>" . $file . " : " . (file_exists(__DIR__ . $file) ? "Existe" : "N'existe pas") . "</p>";
}

// Test des endpoints API
echo "<h2>Test des endpoints API</h2>";
$endpoints = ['/api/auth', '/api/posts'];
foreach ($endpoints as $endpoint) {
    echo "<h3>Test de $endpoint</h3>";
    echo "<pre>";
    // Simulation d'une requête interne
    $_SERVER['REQUEST_URI'] = $endpoint;
    ob_start();
    include __DIR__ . '/index.php';
    $output = ob_get_clean();
    echo htmlspecialchars($output);
    echo "</pre>";
}