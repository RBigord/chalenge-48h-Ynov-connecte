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
    <title>CampusConnect - Messages</title>
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
                <li><a href="feed.php"     class="nav-link"><i class="fa-solid fa-house"></i>         Accueil</a></li>
                <li><a href="messages.php" class="nav-link active"><i class="fa-solid fa-envelope"></i> Messages</a></li>
                <li><a href="profile.php"  class="nav-link"><i class="fa-solid fa-user"></i>          Profil</a></li>
                <li><a href="friends.php"  class="nav-link"><i class="fa-solid fa-user-group"></i>    Amis</a></li>
                <li><a href="settings.php" class="nav-link"><i class="fa-solid fa-gear"></i>          Réglages</a></li>
                <li><a href="logout.php"   class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Zone centrale : messagerie -->
    <main class="feed-center" style="grid-column: 2 / 4;">
        <div class="messages-container card">

            <!-- Sidebar contacts -->
            <div class="messages-sidebar">
                <div class="messages-actions">
                    <button class="btn-primary new-discussion-btn" id="btn-new-discussion" type="button">
                        <i class="fa-solid fa-pen-to-square"></i> Nouvelle discussion
                    </button>
                </div>
                <div class="messages-search">
                    <input type="text" placeholder="Rechercher…">
                </div>
                <ul class="contacts-list" id="contacts-list"></ul>
            </div>

            <!-- Zone de chat -->
            <div class="messages-chat">
                <div class="chat-header" id="chat-header">
                    Sélectionnez une conversation
                </div>
                <div class="chat-history" id="chat-history"></div>
                <div class="chat-input-area">
                    <input type="text" placeholder="Écrire un message…" id="chat-input">
                    <input type="file" id="chat-image-input" accept="image/*" style="max-width:180px;">
                    <button class="btn-primary" id="btn-send-message">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div>
    </main>

</div>

<script type="module" src="js/app.js"></script>
</body>
</html>
