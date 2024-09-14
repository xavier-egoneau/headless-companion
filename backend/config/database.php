<?php

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new SQLite3(__DIR__ . '/../../database/cms.sqlite');
            if (!$this->connection) {
                throw new Exception("Impossible de se connecter à la base de données");
            }
        } catch (Exception $e) {
            error_log("Erreur de connexion à la base de données : " . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        return $this->connection->query($sql);
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
}

// Initialisation de la connexion
try {
    Database::getInstance();
    // Supprimé le message "Connexion à la base de données réussie."
} catch (Exception $e) {
    error_log("Erreur lors de l'initialisation de la base de données : " . $e->getMessage());
    // Ne pas afficher l'erreur directement, laissez l'API gérer les erreurs
}