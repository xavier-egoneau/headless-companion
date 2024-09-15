# headless-companion
Headless cms / twig/sass/php/api

## Table des matières

1. [Introduction](#introduction)
2. [Structure du projet](#structure-du-projet)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Utilisation de l'API](#utilisation-de-lapi)
6. [Interface d'administration](#interface-dadministration)
7. [Gestion des templates avec Twig](#gestion-des-templates-avec-twig)
8. [Système de logging](#système-de-logging)
9. [Dépannage](#dépannage)

## Introduction

Ce CMS headless est une application PHP légère qui fournit une API RESTful pour la gestion de contenu, avec une interface d'administration intégrée. Il utilise SQLite comme base de données, Twig pour le rendu des templates, et intègre un système de logging personnalisé.

## Structure du projet

```
project-root/
│
├── backend/
│   ├── api/
│   │   ├── auth.php
│   │   └── posts.php
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

L'API fournit les endpoints suivants :

- `POST /api/auth` : Authentification et obtention d'un token JWT
- `GET /api/posts` : Récupération de tous les posts
- `GET /api/posts/{id}` : Récupération d'un post spécifique
- `POST /api/posts` : Création d'un nouveau post
- `PUT /api/posts/{id}` : Mise à jour d'un post existant
- `DELETE /api/posts/{id}` : Suppression d'un post

Tous les endpoints, à l'exception de l'authentification, nécessitent un token JWT valide dans l'en-tête `Authorization`.

## Interface d'administration

L'interface d'administration est accessible à `/admin`. Elle permet de :

- Se connecter
- Voir la liste des posts
- Créer un nouveau post
- Modifier un post existant
- Supprimer un post

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