<?php

require_once __DIR__ . '/../models/Post.php';

use App\Models\Post;

$method = $_SERVER['REQUEST_METHOD'];
$user_id = getUserIdFromToken($jwt);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $post = Post::findById($_GET['id']);
            echo json_encode($post);
        } else {
            $posts = Post::findAll();
            echo json_encode($posts);
        }
        break;
    
    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $post = new Post($data);
        $post->save();
        echo json_encode(['message' => 'Post created successfully']);
        break;
    
    case 'PUT':
        if (isset($_GET['id'])) {
            $data = json_decode(file_get_contents('php://input'), true);
            $post = Post::findById($_GET['id']);
            if ($post) {
                $post->title = $data['title'] ?? $post->title;
                $post->content = $data['content'] ?? $post->content;
                $post->save();
                echo json_encode(['message' => 'Post updated successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Post not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'No post ID provided']);
        }
        break;
    
    case 'DELETE':
        if (isset($_GET['id'])) {
            $post = Post::findById($_GET['id']);
            if ($post) {
                $post->delete();
                echo json_encode(['message' => 'Post deleted successfully']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Post not found']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'No post ID provided']);
        }
        break;
    
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}