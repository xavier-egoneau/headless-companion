<?php

use App\Utils\Logger;

class Database {
    private static $instance = null;
    private $connection;
    private $logger;

    private function __construct() {
        $this->logger = new Logger('database.log');
        $this->logger->info("Tentative de connexion à la base de données");
        
        try {
            $this->connection = new SQLite3(__DIR__ . '/../../database/cms.sqlite');
            $this->logger->info("Connexion à la base de données établie avec succès");
        } catch (\Exception $e) {
            $this->logger->error("Erreur de connexion à la base de données: " . $e->getMessage());
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query($sql) {
        $this->logger->info("Exécution de la requête: " . $sql);
        $result = $this->connection->query($sql);
        if ($result === false) {
            $this->logger->error("Erreur dans l'exécution de la requête: " . $this->connection->lastErrorMsg());
        }
        return $result;
    }

    public function getConnection() {
        return $this->connection;
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