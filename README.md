# chalenge-48h-Ynov-connecte
projet créer dans le cadre du chalenge 48h sur la création d'un réseaux social  inter campus de Ynov pour maitre en relation les élèves 

/nom-de-votre-projet
│
├── /config                # Configuration de la base de données
│   └── Database.php       # Classe de connexion PDO (Encapsulation)
│
├── /src                   # Logique métier (Classes PHP)
│   ├── User.php           # Gestion des profils et compétences (Votre partie)
│   ├── Auth.php           # Inscription/Connexion et Sessions (Votre partie)
│   ├── Post.php           # Gestion du fil d'actualité (B1/B2 dédié)
│   ├── Message.php        # Système de messagerie (B1/B2 dédié)
│   └── AiManager.php      # Intégration de l'API Gemini/IA (B1/B2 dédié)
│
├── /public                # Fichiers accessibles aux navigateurs
│   ├── /css
│   │   └── style.css      # Tailwind compilé
│   ├── /js
│   │   └── app.js         # Scripts JavaScript (Interactivité)
│   ├── index.php          # Point d'entrée (Connexion/Accueil)
│   ├── register.php       # Formulaire d'inscription
│   ├── profile.php        # Page de profil personnalisable (Votre partie)
│   └── ymatch.php         # Page dédiée au job board Ymatch (Votre partie)
│
├── /assets                # Images, logo original et icônes
├── /sql                   # Script de création de la BDD et MCD
├── .gitignore             # Pour exclure le dossier vendor ou config sensible
├── README.md              # Documentation obligatoire pour le dépôt
└── tailwind.config.js     # Configuration de Tailwind CSS
