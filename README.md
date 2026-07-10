# Portfolio 2026

Ce projet est maintenant préparé pour afficher des projets depuis une base de données MySQL et pour être géré depuis une page d’administration simple.

## Ce qui a été ajouté

### Pages PHP
- index.php : page publique qui affiche les projets depuis la base de données
- admin/login.php : page de connexion à l’administration
- admin/dashboard.php : tableau de bord pour gérer les projets
- admin/save_project.php : ajout d’un projet
- admin/edit_project.php : modification d’un projet
- admin/delete_project.php : suppression d’un projet
- admin/logout.php : déconnexion

### Configuration
- config/database.php : connexion à la base MySQL
- database.sql : script SQL pour créer la table projets

## Structure attendue

```text
portfolio/
│
├── index.php
├── database.sql
├── config/
│   └── database.php
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── save_project.php
│   ├── edit_project.php
│   ├── delete_project.php
│   └── logout.php
└── assets/
```

## Base de données MySQL

Créez une base dans phpMyAdmin puis importez le fichier database.sql.

Le script crée la table suivante :

```sql
CREATE TABLE projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    lien VARCHAR(255) DEFAULT NULL,
    categorie VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Connexion à la base

Dans le fichier config/database.php, remplacez ces valeurs par celles de votre hébergement :

```php
$host = 'localhost';
$dbname = 'portfolio_db';
$username = 'root';
$password = '';
```

## Identifiants de test de l’admin

- Email : admin@portfolio.com
- Mot de passe : admin123

## Comment tester

### 1. Créer la base dans phpMyAdmin
- Ouvrez phpMyAdmin
- Créez une base de données, par exemple portfolio_db
- Importez le fichier database.sql

### 2. Configurer la connexion
- Ouvrez config/database.php
- Mettez vos vrais identifiants MySQL

### 3. Ouvrir le site
- Ouvrez le dossier du projet dans votre serveur local ou votre hébergeur PHP
- Visitez la page index.php

### 4. Tester l’administration
- Allez sur admin/login.php
- Connectez-vous avec les identifiants ci-dessus
- Ajoutez un projet depuis le tableau de bord

## Comment modifier ce qui a été ajouté

### Modifier la page publique
Le contenu affiché sur la page d’accueil se trouve dans index.php.
Vous pouvez changer :
- le titre
- le style
- l’affichage des projets
- le lien vers l’admin

### Modifier l’admin
Le tableau de bord se trouve dans admin/dashboard.php.
Vous pouvez changer :
- le design du formulaire
- les champs affichés
- les textes

### Modifier la base de données
Les colonnes utilisées sont :
- titre
- description
- image
- lien
- categorie

Si vous voulez ajouter un nouveau champ, il faut le faire dans :
- la table MySQL
- le formulaire d’ajout
- la page d’affichage

### Modifier la connexion MySQL
Tout se passe dans config/database.php.

## Conseils importants

- Sur un hébergement réel, ne laissez pas les identifiants de test en production
- Pour un vrai projet, il vaut mieux remplacer le login simple par un système plus sécurisé
- Les images peuvent être stockées soit par URL, soit plus tard via upload

## Prochaine amélioration possible

Vous pouvez ensuite ajouter :
- un vrai système d’authentification sécurisé
- l’upload d’images
- une page de détail pour chaque projet
- une meilleure interface d’admin
