<?php

namespace App\Models;

use Database;
use App\Utils\Logger;

class User {
    private $id;
    private $username;
    private $password;
    private $email;

    private static $logger;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->email = $data['email'] ?? '';
        
        self::initLogger();
    }

    private static function initLogger() {
        if (!self::$logger) {
            self::$logger = new Logger('user_model.log');
        }
    }

    public static function findByUsername($username) {
        self::initLogger();
        self::$logger->info("Recherche de l'utilisateur: " . $username);
        
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        
        if ($row) {
            self::$logger->info("Utilisateur trouvé: " . $username);
            return new self($row);
        } else {
            self::$logger->warning("Utilisateur non trouvé: " . $username);
            return null;
        }
    }

    public function verifyPassword($password) {
        self::initLogger();
        self::$logger->info("Vérification du mot de passe pour l'utilisateur: " . $this->username);
        
        $result = password_verify($password, $this->password);
        
        if ($result) {
            self::$logger->info("Mot de passe correct pour l'utilisateur: " . $this->username);
        } else {
            self::$logger->warning("Mot de passe incorrect pour l'utilisateur: " . $this->username);
        }
        
        return $result;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setPassword($password) {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function save() {
        $db = Database::getInstance();
        if ($this->id) {
            // Update existing user
            $stmt = $db->prepare("UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id");
            $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        } else {
            // Insert new user
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        }
        $stmt->bindValue(':username', $this->username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $this->email, SQLITE3_TEXT);
        $stmt->bindValue(':password', $this->password, SQLITE3_TEXT);
        $stmt->execute();

        if (!$this->id) {
            $this->id = $db->lastInsertRowID();
        }
    }

    public function delete() {
        if (!$this->id) {
            throw new \Exception("Cannot delete a user without an ID.");
        }
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email
            // Note: We don't include the password in the array for security reasons
        ];
    }

    

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters
    //public function setUsername($username) { $this->username = $username; }
   // public function setEmail($email) { $this->email = $email; }
    //public function setPassword($password) { $this->password = password_hash($password, PASSWORD_DEFAULT); }
}