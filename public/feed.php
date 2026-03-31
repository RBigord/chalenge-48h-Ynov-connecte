<?php
// Fichier : public/feed.php
session_start();

// 1. SÉCURITÉ : Redirection si non connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: index.php?route=login');
    exit();
}

// 2. RÉCUPÉRATION DES INFOS DE L'UTILISATEUR (Depuis la session ou la BDD)
$userName = $_SESSION['full_name'] ?? 'Étudiant';
$userAvatar = $_SESSION['avatar'] ?? 'images/default-avatar.png'; // Mets un chemin par défaut si vide

// 3. RÉCUPÉRATION DES ÉVÉNEMENTS DEPUIS LA BDD
require_once __DIR__ . '/../config/Database.php';
try {
    $pdo = Database::getInstance();
    // On récupère les 3 prochains événements (adapte le nom de la table si besoin)
    $stmt = $pdo->query("SELECT titre, lieu, date_event FROM events ORDER BY date_event ASC LIMIT 3");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $events = []; // En cas d'erreur ou si la table n'existe pas encore
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect - Fil d'actualité</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php require_once __DIR__ . '/partials/header.php'; ?>

<div class="layout-grid container">

    <aside class="sidebar-left">
        <nav class="main-nav">
            <ul>
                <li><a href="feed.php"     class="nav-link active"><i class="fa-solid fa-house"></i>    Accueil</a></li>
                <li><a href="messages.php" class="nav-link"><i class="fa-solid fa-envelope"></i>  Messages</a></li>
                <li><a href="profile.php"  class="nav-link"><i class="fa-solid fa-user"></i>      Profil</a></li>
                <li><a href="friends.php"  class="nav-link"><i class="fa-solid fa-user-group"></i> Amis</a></li>
                <li><a href="settings.php" class="nav-link"><i class="fa-solid fa-gear"></i>      Réglages</a></li>
                <li><a href="logout.php"   class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a></li>
            </ul>
        </nav>
    </aside>

    <main class="feed-center">

        <div class="create-post card">
            <div class="create-post-header">
                <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Profil" class="avatar-small current-user-avatar">
                <textarea id="quick-post-textarea" placeholder="Quoi de neuf sur le campus, <?= htmlspecialchars(explode(' ', $userName)[0]) ?> ?" rows="2"></textarea>
            </div>
            <div class="create-post-actions">
                <button class="btn-icon btn-open-composer" type="button">
                    <i class="fa-solid fa-image"></i> Photo
                </button>
                <button class="btn-icon btn-open-composer" type="button">
                    <i class="fa-solid fa-video"></i> Vidéo
                </button>
                <button class="btn-icon" id="btn-tag-post">
                    <i class="fa-solid fa-tags"></i> Tag
                </button>
                <button class="btn-primary btn-open-composer" type="button">Publier</button>
            </div>
        </div>

        <div id="posts-container"></div>
    </main>

    <aside class="sidebar-right">
        <div class="events-widget card">
            <h2>Campus News &amp; Events</h2>
            <ul class="events-list" id="events-container">
                
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <?php 
                            // Formater la date (ex: "15 Oct")
                            $dateObj = new DateTime($event['date_event']);
                            $jour = $dateObj->format('d');
                            $mois = $dateObj->format('M');
                        ?>
                        <li class="event-item">
                            <div class="event-date">
                                <span class="day"><?= htmlspecialchars($jour) ?></span><br>
                                <span style="font-size: 0.7rem;"><?= htmlspecialchars($mois) ?></span>
                            </div>
                            <div class="event-details">
                                <h3><?= htmlspecialchars($event['titre']) ?></h3>
                                <p><?= htmlspecialchars($event['lieu']) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="event-item">
                        <div class="event-details"><p>Aucun événement à venir.</p></div>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
        <div class="card">
            <h2>Annonces</h2>
            <div class="muted small">
                Bienvenue <?= htmlspecialchars($userName) ?> ! Complétez votre profil campus pour débloquer les contrôles de visibilité.
            </div>
        </div>
    </aside>

</div>

<div id="composer-overlay" class="composer-overlay" aria-hidden="true" style="display:none;">
    <div class="composer-dialog" role="dialog" aria-modal="true" aria-label="Créer un post">
        <button class="composer-cancel" id="btn-composer-cancel" type="button">Annuler</button>
        <div class="composer-columns">
            <div class="composer-left">
                <div class="composer-topline">
                    <div class="composer-user">
                        <img class="avatar-small current-user-avatar" src="<?= htmlspecialchars($userAvatar) ?>" alt="Profil">
                        <div>
                            <div class="composer-user-title">
                                <span class="current-user-name"><?= htmlspecialchars($userName) ?></span>
                            </div>
                            <div class="composer-user-sub muted">Création d'un post...</div>
                        </div>
                    </div>
                    <div class="composer-mini-chip">Audience <strong>Campus</strong></div>
                </div>
                <div class="composer-editor">
                    <textarea id="composer-textarea" placeholder="Partagez ce qui se passe sur le campus…"></textarea>
                </div>
                <div class="composer-attachments" id="composer-attachments"></div>
                <div class="composer-success" id="composer-success" style="display:none; color:green; padding:10px; margin-top:10px;"></div>
            </div>
        </div>
        <div class="composer-actions">
            <button class="btn-icon" id="btn-composer-image" type="button">
                <i class="fa-solid fa-image"></i> Photo
            </button>
            <button class="btn-icon" id="btn-composer-video" type="button" disabled>
                <i class="fa-solid fa-video"></i> Vidéo
            </button>
            <input type="file" id="composer-image-input" accept="image/*" style="display:none;">
            <div style="flex-grow: 1;"></div>
            <button class="btn-secondary cancel-btn" type="button">Brouillon</button>
            <button class="btn-primary composer-publish" type="button">Publier</button>
        </div>
    </div>
</div>

<script type="module" src="js/app.js"></script>
</body>
</html>