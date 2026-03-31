# chalenge-48h-Ynov-connecte

Réseau social inter-campus Ynov réalisé pour un challenge 48h.

## Architecture cible (active)

Le projet utilise une architecture unique :

- Front web server-rendered dans `public/`
- API MVC dans `Backend/`
- Configuration DB unifiée dans `config/Database.php`

Les pages `public/*.php` sont connectées a la base via les endpoints `Backend/public/index.php?route=...`.

## Points d'entree

- Application web : `public/index.php`
- API MVC : `Backend/public/index.php`

## Arborescence reelle (principale)

```text
config/
	Database.php                # Config DB unique (PDO)

Backend/
	public/
		index.php                 # Front controller API MVC
	route/
		api.php                   # Table des routes API
	controllers/
		AuthController.php
		UserController.php
		PostController.php
		CommentController.php
		MessageController.php
		AIController.php
	models/
		Auth.php
		User.php
		Post.php
		Comment.php
		Message.php
		Admin.php
		ia_logic.php

public/
	index.php                   # Login/Signup
	feed.php
	messages.php
	profile.php
	friends.php
	settings.php
	register.php
	logout.php
	css/style.css
	js/app.js
	js/ApiService.js
	partials/header.php

sql/
	db.sql                      # Schema principal MySQL
```

## Legacy deprecie

- Dossier `api/` : conserve temporairement pour compatibilite, mais deprecie.
- Dossier `z/` : deprecie, redirige vers `public/index.php`.
- Fichier racine `api_assistant.php` : deprecie (HTTP 410), utiliser les routes MVC IA.

## Configuration environnement

Copier `.env.example` vers `.env` (ou `Backend/.env`) puis adapter :

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `YNOV_CHALLENGE_KEY` (optionnel, pour l'IA)

## Tuto de demarrage (local)

1. Installer les prerequis

- PHP 8.1+
- Composer
- MySQL/MariaDB

2. Installer les dependances PHP (depuis la racine du projet)

```bash
composer install
```

3. Creer la base de donnees puis importer le schema

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS ynov_connecte CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p ynov_connecte < sql/db.sql
```

4. Configurer l'environnement

- Copier `.env.example` en `.env` (ou `Backend/.env` selon votre organisation).
- Renseigner `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

5. Lancer le serveur PHP local a la racine du repo

```bash
php -S localhost:8000 -t .
```

6. Ouvrir l'application dans le navigateur

- Page d'accueil/login : `http://localhost:8000/public/index.php`
- API MVC (test direct) : `http://localhost:8000/Backend/public/index.php`

## Tuto de test rapide

1. Test fonctionnel front

- Creer un compte depuis `public/index.php`.
- Se connecter.
- Creer un post dans le feed.
- Envoyer un message a un contact.

2. Test smoke des endpoints API (dans le navigateur ou Postman)

- `GET http://localhost:8000/Backend/public/index.php?route=posts`
- `GET http://localhost:8000/Backend/public/index.php?route=profile`
- `GET http://localhost:8000/Backend/public/index.php?route=friends`

3. Test d'un endpoint POST via `curl` (exemple register)

```bash
curl -X POST "http://localhost:8000/Backend/public/index.php?route=register" \
	-H "Content-Type: application/json" \
	-d '{"name":"Test User","email":"test@example.com","password":"Test1234!"}'
```

Si vous obtenez une reponse JSON coherente (succes ou erreur metier), l'API est bien accessible.

## API principales

- `POST ?route=login`
- `POST ?route=register`
- `POST ?route=logout`
- `GET ?route=profile`
- `GET ?route=posts`
- `GET ?route=friends`
- `GET ?route=contacts`
- `GET ?route=messages&id_contact=...`
- `POST ?route=send_message`
- `POST ?route=create_post`
- `POST ?route=toggle_like`
- `GET ?route=comments&id_post=...`
- `POST ?route=add_comment`

Base URL API locale : `../Backend/public/index.php`
