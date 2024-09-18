<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';

use App\Utils\Logger;

$logger = new Logger('db_init.log');

try {
    $logger->info("Début de l'initialisation de la base de données");

    $db = new SQLite3(__DIR__ . '/database/cms.sqlite');

    // Création de la table users
    $db->exec('
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
    ');
    $logger->info("Table 'users' créée avec succès");

    // Création de la table posts
    $db->exec('
    CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        author_id INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (author_id) REFERENCES users(id)
    )
    ');
    $logger->info("Table 'posts' créée avec succès");

    // Création d'un utilisateur admin par défaut
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $email = 'admin@example.com';

    $stmt = $db->prepare('INSERT OR IGNORE INTO users (username, password, email) VALUES (:username, :password, :email)');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':password', $password, SQLITE3_TEXT);
    $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    $stmt->execute();
    
    $logger->info("Utilisateur admin créé avec succès");

    $logger->info("Initialisation de la base de données terminée avec succès");
    echo "Base de données initialisée avec succès.\n";
} catch (Exception $e) {
    $logger->error("Erreur lors de l'initialisation de la base de données : " . $e->getMessage());
    echo "Erreur lors de l'initialisation de la base de données : " . $e->getMessage() . "\n";
}