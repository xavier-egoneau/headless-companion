<?php

namespace App\Models;

use Database;

class User {
    private $id;
    private $username;
    private $password;
    private $email;
    private $created_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public static function findByUsername($username) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ? new User($row) : null;
    }

    public function save() {
        $db = Database::getInstance();
        if ($this->id) {
            $stmt = $db->prepare("UPDATE users SET username = :username, password = :password, email = :email WHERE id = :id");
            $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        } else {
            $stmt = $db->prepare("INSERT INTO users (username, password, email, created_at) VALUES (:username, :password, :email, :created_at)");
            $stmt->bindValue(':created_at', $this->created_at, SQLITE3_TEXT);
        }
        $stmt->bindValue(':username', $this->username, SQLITE3_TEXT);
        $stmt->bindValue(':password', password_hash($this->password, PASSWORD_DEFAULT), SQLITE3_TEXT);
        $stmt->bindValue(':email', $this->email, SQLITE3_TEXT);
        $stmt->execute();
    }

    // Getters and setters...
}