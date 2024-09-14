<?php

namespace App\Models;

use Database;

class Post {
    private $id;
    private $title;
    private $content;
    private $author_id;
    private $created_at;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->author_id = $data['author_id'] ?? null;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public static function findAll($page = 1, $perPage = 10) {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        $result = $db->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
        $posts = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $posts[] = new Post($row);
        }
        return array_map(function($post) {
            return $post->toArray();
        }, $posts);
    }

    public static function findById($id) {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ? new Post($row) : null;
    }

    public function update($data) {
        $this->title = $data['title'] ?? $this->title;
        $this->content = $data['content'] ?? $this->content;
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE posts SET title = :title, content = :content WHERE id = :id");
        $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        $stmt->bindValue(':title', $this->title, SQLITE3_TEXT);
        $stmt->bindValue(':content', $this->content, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function delete() {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
        $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        $stmt->execute();
    }

    public static function count() {
        $db = Database::getInstance();
        $result = $db->query("SELECT COUNT(*) as count FROM posts");
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row['count'];
    }


    public function save() {
        $db = Database::getInstance();
        if ($this->id) {
            $stmt = $db->prepare("UPDATE posts SET title = :title, content = :content, author_id = :author_id WHERE id = :id");
            $stmt->bindValue(':id', $this->id, SQLITE3_INTEGER);
        } else {
            $stmt = $db->prepare("INSERT INTO posts (title, content, author_id, created_at) VALUES (:title, :content, :author_id, :created_at)");
            $stmt->bindValue(':created_at', $this->created_at, SQLITE3_TEXT);
        }
        $stmt->bindValue(':title', $this->title, SQLITE3_TEXT);
        $stmt->bindValue(':content', $this->content, SQLITE3_TEXT);
        $stmt->bindValue(':author_id', $this->author_id, SQLITE3_INTEGER);
        $stmt->execute();

        if (!$this->id) {
            $this->id = $db->getConnection()->lastInsertRowID();
        }
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getContent() { return $this->content; }
    public function getAuthorId() { return $this->author_id; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters
    public function setTitle($title) { $this->title = $title; }
    public function setContent($content) { $this->content = $content; }
    public function setAuthorId($author_id) { $this->author_id = $author_id; }

    // Méthode pour convertir l'objet en tableau (utile pour la sérialisation JSON)
    public function toArray() {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author_id' => $this->author_id,
            'created_at' => $this->created_at
        ];
    }
}