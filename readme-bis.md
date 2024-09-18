# CMS Headless PHP

## Description

Ce projet est un CMS (Content Management System) headless développé en PHP. Il fournit une API RESTful pour gérer le contenu, avec une interface d'administration simple. Le système utilise SQLite comme base de données, Twig pour le rendu des templates, et inclut une authentification JWT.

## Fonctionnalités

- API RESTful pour la gestion des posts
- Authentification sécurisée avec JWT
- Interface d'administration pour gérer les posts
- Utilisation de SQLite pour une configuration de base de données simple
- Logging avancé pour le débogage

## Prérequis

- PHP 7.4 ou supérieur
- Composer
- SQLite3
- Extension PHP SQLite

## Installation

1. Clonez le dépôt :
   ```
   git clone https://github.com/votre-username/cms-headless-php.git
   cd cms-headless-php
   ```

2. Installez les dépendances via Composer :
   ```
   composer install
   ```
   Cette commande installera également la base de données par défaut.

3. Configurez votre serveur web pour pointer vers le dossier `public/` du projet.

4. Assurez-vous que les dossiers `logs/` et `database/` sont accessibles en écriture par PHP.

## Configuration

1. Copiez le fichier `.env.example` en `.env` et ajustez les variables selon vos besoins :
   ```
   cp .env.example .env
   ```

2. Modifiez les paramètres dans `backend/config/env.php` si nécessaire.

## Utilisation

### API Endpoints

- `POST /api/auth` : Authentification et obtention d'un token JWT
- `GET /api/posts` : Récupération de tous les posts
- `GET /api/posts/{id}` : Récupération d'un post spécifique
- `POST /api/posts` : Création d'un nouveau post
- `PUT /api/posts/{id}` : Mise à jour d'un post existant
- `DELETE /api/posts/{id}` : Suppression d'un post

### Interface d'administration

Accédez à l'interface d'administration en naviguant vers `/admin` dans votre navigateur. Utilisez les identifiants par défaut :

- Username : admin
- Password : admin123

**Important** : Changez ce mot de passe immédiatement après votre première connexion.

## Développement

### Structure du projet

```
project-root/
│
├── backend/            # Logique côté serveur
│   ├── api/            # Endpoints de l'API
│   ├── config/         # Fichiers de configuration
│   ├── models/         # Modèles de données
│   ├── utils/          # Utilitaires (ex: Logger)
│   └── auth/           # Gestion de l'authentification
│
├── database/           # Fichier SQLite
│
├── logs/               # Fichiers de logs
│
├── templates/          # Templates Twig
│
├── public/             # Point d'entrée public
│   └── index.php
│
├── vendor/             # Dépendances (généré par Composer)
│
└── tests/              # Tests unitaires et d'intégration
```

### Exécution des tests

Pour exécuter les tests unitaires, utilisez la commande suivante :

```
vendor/bin/phpunit tests
```

## Contribution

Les contributions sont les bienvenues ! Veuillez suivre ces étapes pour contribuer :

1. Forkez le projet
2. Créez votre branche de fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commitez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## Support

Si vous rencontrez des problèmes ou avez des questions, veuillez ouvrir un issue dans le dépôt GitHub du projet.

