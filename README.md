# Documentation CMS Headless

## Table des matières

1. [Introduction](#introduction)
2. [Structure du projet](#structure-du-projet)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Utilisation de l'API](#utilisation-de-lapi)
   - [API Posts](#api-posts)
   - [API Users](#api-users)
6. [Interface d'administration](#interface-dadministration)
7. [Gestion des templates avec Twig](#gestion-des-templates-avec-twig)
8. [Système de logging](#système-de-logging)
9. [Dépannage](#dépannage)
10. [Mise à jour du schéma de la base de données](#mise-à-jour-du-schéma-de-la-base-de-données)

## Introduction

Ce CMS headless est une application PHP légère qui fournit une API RESTful pour la gestion de contenu et d'utilisateurs, avec une interface d'administration intégrée. Il utilise SQLite comme base de données, Twig pour le rendu des templates, et intègre un système de logging personnalisé.

## Structure du projet

```
project-root/
│
├── backend/
│   ├── api/
│   │   ├── auth.php
│   │   ├── posts.php
│   │   └── users.php
│   ├── config/
│   │   ├── database.php
│   │   ├── env.php
│   │   └── twig.php
│   ├── models/
│   │   ├── Post.php
│   │   └── User.php
│   └── utils/
│       └── Logger.php
│
├── cache/
│   └── twig/
│
├── logs/
│
├── templates/
│   ├── layout.twig
│   ├── home.twig
│   ├── post.twig
│   ├── admin.twig
│   ├── 404.twig
│   └── error.twig
│
├── vendor/
│
├── index.php
└── composer.json
```

## Installation


1. Clonez le dépôt :
   ```
   git clone [URL_DU_REPO]
   ```

2. Installez les dépendances avec Composer :
   ```
   composer install
   ```

3. Créez les dossiers `cache/twig` et `logs` à la racine du projet :
   ```
   mkdir -p cache/twig logs
   ```

4. Assurez-vous que PHP a les permissions d'écriture dans ces dossiers.

5. Configurez votre serveur web pour pointer vers le dossier racine du projet.

## Configuration

1. Base de données : La configuration de la base de données SQLite se trouve dans `backend/config/database.php`.

2. Environnement : Les variables d'environnement sont gérées dans `backend/config/env.php`.

3. Twig : La configuration de Twig est dans `backend/config/twig.php`.

## Utilisation de l'API

L'API fournit des endpoints pour la gestion des posts et des utilisateurs. Tous les endpoints, à l'exception de l'authentification, nécessitent un token JWT valide dans l'en-tête `Authorization`.

### API Posts

- `GET /api/posts` : Récupération de tous les posts
- `GET /api/posts/{id}` : Récupération d'un post spécifique
- `POST /api/posts` : Création d'un nouveau post
- `PUT /api/posts/{id}` : Mise à jour d'un post existant
- `DELETE /api/posts/{id}` : Suppression d'un post

### API Users

- `POST /api/auth` : Authentification et obtention d'un token JWT
- `GET /api/users` : Récupération de tous les utilisateurs (admin seulement)
- `GET /api/users/{id}` : Récupération d'un utilisateur spécifique (admin ou propriétaire)
- `POST /api/users` : Création d'un nouvel utilisateur (admin seulement)
- `PUT /api/users/{id}` : Mise à jour d'un utilisateur existant (admin ou propriétaire)
- `DELETE /api/users/{id}` : Suppression d'un utilisateur (admin seulement)

Exemple de requête pour créer un nouvel utilisateur :

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -d '{"username":"newuser", "password":"securepassword", "email":"newuser@example.com"}'
```

## Interface d'administration

L'interface d'administration est accessible à `/admin`. Elle permet de :

- Se connecter
- Voir la liste des posts
- Créer un nouveau post
- Modifier un post existant
- Supprimer un post
- Gérer les utilisateurs (pour les administrateurs)

## Gestion des templates avec Twig
Les templates Twig se trouvent dans le dossier `templates/`. Le layout principal est `layout.twig`, et les autres templates étendent ce layout.

Pour ajouter une nouvelle page :

1. Créez un nouveau template Twig dans le dossier `templates/`.
2. Ajoutez une nouvelle route dans `index.php` pour rendre ce template.

## Système de logging

Le système de logging personnalisé est implémenté dans `backend/utils/Logger.php`. Il permet de logger des messages avec différents niveaux de sévérité (DEBUG, INFO, WARNING, ERROR).

Utilisation :

```php
$logger = new Logger('app.log', Logger::DEBUG);
$logger->info("Ceci est un message d'information");
$logger->error("Une erreur est survenue");
```

Les logs sont stockés dans le dossier `logs/`.

## Dépannage

- Si vous rencontrez des erreurs liées aux permissions, assurez-vous que PHP a les droits d'écriture dans les dossiers `cache/twig` et `logs`.
- En cas d'erreur "Class not found", exécutez `composer dump-autoload` pour régénérer l'autoloader.
- Pour les problèmes liés à la base de données, vérifiez que le fichier SQLite existe et que PHP a les permissions pour y accéder et le modifier.

Pour plus d'aide, consultez les logs dans le dossier `logs/` ou activez l'affichage des erreurs PHP en développement.

## Sécurité

- L'authentification utilise des tokens JWT avec une durée de validité limitée.
- Les mots de passe sont hachés avant d'être stockés dans la base de données.
- L'accès aux endpoints sensibles est restreint aux utilisateurs authentifiés et autorisés.
- Les entrées utilisateur sont validées et nettoyées pour prévenir les injections SQL et XSS.
- CORS est configuré pour limiter l'accès à l'API aux domaines autorisés.

Pour renforcer la sécurité :
- Utilisez HTTPS en production.
- Mettez régulièrement à jour les dépendances.
- Effectuez des audits de sécurité périodiques.




## Mise à jour du schéma de la base de données

Lorsque vous apportez des modifications au schéma de la base de données, il est important de suivre une procédure structurée pour garantir que tous les environnements (développement, test, production) restent synchronisés. Voici la procédure recommandée :

### 1. Création d'un script de migration

Pour chaque modification du schéma, créez un nouveau script SQL dans le dossier `database/migrations/`. Nommez le fichier avec un timestamp et une brève description, par exemple : `2023_09_15_add_category_to_posts.sql`.

Exemple de contenu pour ce fichier :

```sql
-- 2023_09_15_add_category_to_posts.sql
ALTER TABLE posts ADD COLUMN category VARCHAR(50);
```

### 2. Mise à jour du schéma principal

Mettez à jour le fichier `database/schema.sql` pour refléter l'état final de la structure de la base de données après toutes les migrations.

### 3. Script de mise à jour

Créez ou mettez à jour un script PHP `update_database.php` à la racine du projet :

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/config/database.php';

$db = Database::getInstance();

// Obtenez la liste des fichiers de migration
$migrations = glob(__DIR__ . '/database/migrations/*.sql');

// Triez les migrations par nom de fichier (qui commence par la date)
sort($migrations);

foreach ($migrations as $migration) {
    $migrationName = basename($migration);
    
    // Vérifiez si la migration a déjà été appliquée
    $result = $db->query("SELECT * FROM migrations WHERE name = '$migrationName'");
    if ($result->fetchArray()) {
        echo "Migration $migrationName déjà appliquée.\n";
        continue;
    }
    
    // Appliquez la migration
    $sql = file_get_contents($migration);
    $db->exec($sql);
    
    // Enregistrez la migration comme appliquée
    $db->exec("INSERT INTO migrations (name) VALUES ('$migrationName')");
    
    echo "Migration $migrationName appliquée avec succès.\n";
}

echo "Mise à jour de la base de données terminée.\n";
```

### 4. Procédure de mise à jour

Pour mettre à jour la base de données :

1. Arrêtez l'application si elle est en cours d'exécution.
2. Faites une sauvegarde de la base de données actuelle.
3. Exécutez le script de mise à jour :
   ```
   php update_database.php
   ```
4. Vérifiez les logs pour vous assurer que toutes les migrations ont été appliquées avec succès.
5. Redémarrez l'application.

### 5. Gestion des erreurs

Si une erreur se produit pendant la mise à jour :
1. Consultez les logs d'erreur.
2. Corrigez le problème dans le script de migration concerné.
3. Restaurez la sauvegarde de la base de données si nécessaire.
4. Réexécutez le script de mise à jour.

### 6. Environnements multiples

Assurez-vous d'appliquer ces mises à jour sur tous vos environnements (développement, test, production) en suivant la même procédure.

### 7. Versioning

N'oubliez pas de versionner vos scripts de migration et les mises à jour du schéma principal avec votre code source.

En suivant cette procédure, vous pouvez gérer efficacement les modifications de votre schéma de base de données tout au long du cycle de vie de votre application.
