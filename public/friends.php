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
    <title>CampusConnect - Amis</title>
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
                <li><a href="friends.php"  class="nav-link active"><i class="fa-solid fa-user-group"></i> Amis</a></li>
                <li><a href="settings.php" class="nav-link"><i class="fa-solid fa-gear"></i>           Réglages</a></li>
                <li><a href="logout.php"   class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Zone centrale -->
    <main class="feed-center">
        <div class="card">
            <h2>Gérer mes amis</h2>
            <p class="muted small">Ajoutez de nouvelles connexions ou retrouvez vos amis.</p>

            <div class="add-friend-form">
                <input type="text" id="invite-friend-input"
                       placeholder="Email ou nom d'utilisateur…">
                <button class="btn-primary" id="btn-invite-friend">Inviter</button>
            </div>

            <h3 style="margin-top:20px;">
                Mes amis (<span id="friends-count">0</span>)
            </h3>
            <ul class="friends-list" id="friends-list-container"></ul>
        </div>
    </main>

    <!-- Sidebar droite -->
    <aside class="sidebar-right">
        <div class="card">
            <h2>Suggestions</h2>
            <p class="muted small">Des personnes que vous pourriez connaître.</p>
        </div>
    </aside>

</div>

<script type="module" src="js/app.js"></script>
</body>
</html>
