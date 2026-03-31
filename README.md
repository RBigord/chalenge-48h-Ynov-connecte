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
