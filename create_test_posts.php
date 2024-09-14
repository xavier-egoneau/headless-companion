<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';
require_once __DIR__ . '/backend/models/Post.php';

use App\Models\Post;

try {
    $testPosts = [
        ['title' => 'Premier post de test', 'content' => 'Contenu du premier post de test', 'author_id' => 1],
        ['title' => 'Deuxième post de test', 'content' => 'Contenu du deuxième post de test', 'author_id' => 1],
    ];

    foreach ($testPosts as $postData) {
        $post = new Post($postData);
        $post->save();
        echo "Post créé : " . $post->getTitle() . "\n";
    }

    echo "Posts de test créés avec succès.\n";
} catch (Exception $e) {
    echo "Erreur lors de la création des posts de test : " . $e->getMessage() . "\n";
}
