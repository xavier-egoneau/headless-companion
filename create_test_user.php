<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';
require_once __DIR__ . '/backend/models/User.php';

use App\Models\User;

$testUser = new User([
    'username' => 'test_user',
    'password' => 'test_password',
    'email' => 'test@example.com'
]);

try {
    $testUser->save();
    echo "Utilisateur de test créé avec succès.\n";
    
    // Vérification
    $savedUser = User::findByUsername('test_user');
    if ($savedUser && $savedUser->verifyPassword('test_password')) {
        echo "Vérification réussie : l'utilisateur peut s'authentifier.\n";
    } else {
        echo "Erreur : La vérification du mot de passe a échoué.\n";
    }
} catch (Exception $e) {
    echo "Erreur lors de la création de l'utilisateur de test : " . $e->getMessage() . "\n";
}