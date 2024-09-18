<?php

namespace App\Models;

use Database;
use App\Utils\Logger;

class Post {
    private $id;
    private $title;
    private $content;
    private $authorId;
    private $createdAt;
    private static $logger;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->authorId = $data['author_id'] ?? null;
        $this->createdAt = $data['created_at'] ?? date('Y-m-d H:i:s');
        
        self::initLogger();
    }
    private static function initLogger() {
        if (self::$logger === null) {
            self::$logger = new Logger('post_model.log');
        }
    }

    public static function findAll() {
        self::initLogger();
        self::$logger->info("Début de l'exécution de Post::findAll()");
        try {
            $db = Database::getInstance();
            self::$logger->info("Connexion à la base de données établie");
            
            $result = $db->query("SELECT * FROM posts ORDER BY created_at DESC");
            self::$logger->info("Requête SQL exécutée");
            
            $posts = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $posts[] = new self($row);
            }
            self::$logger->info("Nombre de posts récupérés : " . count($posts));
            
            return $posts;
        } catch (\Exception $e) {
            self::$logger->error("Erreur dans Post::findAll(): " . $e->getMessage());
            self::$logger->error("Trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public static function findById($id) {
        self::initLogger();
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ? new self($row) : null;
    }

    public function save() {
        self::initLogger();
        $db = Database::getInstance();
        if ($this->id) {
            $stmt = $db->prepare("UPDATE posts SET title = :title, content = :content, author_id = :author_id WHERE id = :id");
            $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        } else {
            $stmt = $db->prepare("INSERT INTO posts (title, content, author_id, created_at) VALUES (:title, :content, :author_id, :created_at)");
            $stmt->bindValue(':created_at', $this->createdAt, SQLITE3_TEXT);
        }
        $stmt->bindValue(':title', $this->title, SQLITE3_TEXT);
        $stmt->bindValue(':content', $this->content, SQLITE3_TEXT);
        $stmt->bindValue(':author_id', $this->authorId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        if (!$this->id) {
            $this->id = $db->lastInsertRowID();
        }

        self::$logger->info("Post sauvegardé avec ID: " . $this->id);
        return $result !== false;
    }

    public function update($data) {
        self::initLogger();
        $this->title = $data['title'] ?? $this->title;
        $this->content = $data['content'] ?? $this->content;
        return $this->save();
    }

    public function delete() {
        self::initLogger();
        if (!$this->id) {
            self::$logger->error("Tentative de suppression d'un post sans ID");
            return false;
        }
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
        $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        self::$logger->info("Post supprimé avec ID: " . $this->id);
        return $result !== false;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author_id' => $this->authorId,
            'created_at' => $this->createdAt
        ];
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getContent() { return $this->content; }
    public function getAuthorId() { return $this->authorId; }
    public function getCreatedAt() { return $this->createdAt; }

    // Setters
    public function setTitle($title) { $this->title = $title; }
    public function setContent($content) { $this->content = $content; }
    public function setAuthorId($authorId) { $this->authorId = $authorId; }
}