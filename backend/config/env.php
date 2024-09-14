<?php

// Définissez cette variable à 'development' pour l'environnement local,
// et à 'production' pour l'environnement de production
define('APP_ENV', 'development');

// Fonction helper pour vérifier si nous sommes en environnement de développement
function isDevelopment() {
    return APP_ENV === 'development';
}

// Configuration spécifique à l'environnement
if (isDevelopment()) {
    // Afficher toutes les erreurs en développement
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Cacher les erreurs en production
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
}