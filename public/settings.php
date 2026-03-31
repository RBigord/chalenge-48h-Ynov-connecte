<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header('Location: index.php?route=login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect - Réglages</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php require_once __DIR__ . '/partials/header.php'; ?>

<div class="layout-grid container">

    <!-- Sidebar gauche -->
    <aside class="sidebar-left">
        <nav class="main-nav">
            <ul>
                <li><a href="feed.php"     class="nav-link"><i class="fa-solid fa-house"></i>          Accueil</a></li>
                <li><a href="messages.php" class="nav-link"><i class="fa-solid fa-envelope"></i>       Messages</a></li>
                <li><a href="profile.php"  class="nav-link"><i class="fa-solid fa-user"></i>           Profil</a></li>
                <li><a href="friends.php"  class="nav-link"><i class="fa-solid fa-user-group"></i>     Amis</a></li>
                <li><a href="settings.php" class="nav-link active"><i class="fa-solid fa-gear"></i>    Réglages</a></li>
                <li><a href="logout.php"   class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Zone centrale -->
    <main class="feed-center">
        <div class="card">
            <h2>Réglages du compte</h2>
            <p class="muted small">Gérez vos préférences et la confidentialité de votre compte.</p>

            <form method="post" action="settings.php" style="margin-top:20px;">
                <input type="hidden" name="csrf_token"
                       value="">

                <div class="side-toggle">
                    <div>
                        <div class="toggle-title">Notifications par email</div>
                        <div class="toggle-sub muted small">Recevoir un email pour les nouveaux messages.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="notif_email" checked>
                        <span class="slider round"></span>
                    </label>
                </div>

                <div class="side-toggle">
                    <div>
                        <div class="toggle-title">Profil public</div>
                        <div class="toggle-sub muted small">Visible en dehors du campus.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="public_profile">
                        <span class="slider round"></span>
                    </label>
                </div>

                <div class="side-toggle">
                    <div>
                        <div class="toggle-title">Mode sombre</div>
                        <div class="toggle-sub muted small">Interface en thème sombre.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="dark_mode" checked>
                        <span class="slider round"></span>
                    </label>
                </div>

                <button class="btn-primary" type="submit" style="margin-top:24px;">
                    Enregistrer les modifications
                </button>
            </form>

            <hr style="border-color:var(--border-color);margin:28px 0;">

            <h3 style="color:#ef4444;">Zone de danger</h3>
            <p class="muted small">Ces actions sont irréversibles.</p>
            <button class="btn-danger-full" type="button" style="margin-top:12px;max-width:280px;">
                <i class="fa-solid fa-trash"></i> Supprimer mon compte
            </button>
        </div>
    </main>

    <!-- Sidebar droite -->
    <aside class="sidebar-right">
        <div class="card">
            <h2>Sécurité</h2>
            <p class="muted small">Votre session est chiffrée (TLS) et votre mot de passe hashé (bcrypt).</p>
            <div class="muted small" style="margin-top:12px;">
                Connecté en tant que <strong class="current-user-name">Chargement...</strong>
            </div>
        </div>
    </aside>

</div>

<script type="module" src="js/app.js"></script>
</body>
</html>
