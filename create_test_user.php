<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';
require_once __DIR__ . '/backend/models/User.php';

use App\Models\User;

$testUsername = 'test_user';
$testPassword = 'test_password';
$testEmail = 'test@example.com';

try {
    $existingUser = User::findByUsername($testUsername);
    
    if ($existingUser) {
        echo "L'utilisateur de test existe déjà.\n";
    } else {
        $testUser = new User([
            'username' => $testUsername,
            'password' => $testPassword,
            'email' => $testEmail
        ]);
        $testUser->save();
        echo "Utilisateur de test créé avec succès.\n";
    }
    
    // Vérification
    $savedUser = User::findByUsername($testUsername);
    if ($savedUser && $savedUser->verifyPassword($testPassword)) {
        echo "Vérification réussie : l'utilisateur peut s'authentifier.\n";
    } else {
        echo "Erreur : La vérification du mot de passe a échoué.\n";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}