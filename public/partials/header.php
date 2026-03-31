<?php
// partials/header.php
// Totalement découplé : Plus aucune logique PHP liée aux Modèles ou à la BDD.
?>
<style>
    /* Cache le contenu tant que la session n'est pas validée par l'API */
    body.is-verifying-session .container { display: none; }
    body.is-verifying-session::before { 
        content: "Chargement..."; 
        display: block; 
        text-align: center; 
        margin-top: 50px; 
        font-family: sans-serif;
        color: #666;
    }
</style>
<script>
// Guard Javascript pour vérifier l'API
document.documentElement.classList.add('is-verifying-session');
document.addEventListener("DOMContentLoaded", async () => {
    try {
        const res = await fetch('../Backend/public/index.php?route=profile');
        if (res.status === 401 || !res.ok) {
            window.location.href = 'index.php';
            return;
        }
        const json = await res.json();
        if (json.status === 'success') {
            document.body.classList.remove('is-verifying-session');
            document.getElementById('main-header').style.display = 'flex';
            
            const user = json.data;
            window.CAMPUSCONNECT_ME = user;
            const avatarUrl = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.nom) + '&background=2563eb&color=fff';
            
            // Met à jour l'avatar du header
            document.getElementById('header-avatar').src = avatarUrl;
            document.getElementById('header-avatar').title = user.nom;
            
            // Met à jour dynamiquement tous les avatars et noms dans la page
            document.querySelectorAll('.current-user-avatar').forEach(img => img.src = avatarUrl);
            document.querySelectorAll('.current-user-name').forEach(el => el.textContent = user.nom);
            document.querySelectorAll('.current-user-email').forEach(el => el.textContent = user.email || 'Étudiant');
            
        } else {
            window.location.href = 'index.php';
        }
    } catch(e) {
        window.location.href = 'index.php';
    }

    // Gestion de la déconnexion
    const logoutBtn = document.getElementById('logout-btn-header');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            await fetch('../Backend/public/index.php?route=logout', { method: 'POST' });
            window.location.href = 'index.php';
        });
    }
});
</script>

<header class="main-header" id="main-header" style="display:none;">
    <div class="header-left">
        <h1 class="logo"><i class="fa-solid fa-graduation-cap"></i> CampusConnect</h1>
    </div>
    <div class="header-center">
        <div class="search-bar">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Rechercher des étudiants, posts…">
        </div>
    </div>
    <div class="header-right">
        <a href="feed.php" class="btn-bot <?php echo basename($_SERVER['PHP_SELF']) === 'feed.php'     ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i>
        </a>
        <a href="messages.php" class="btn-bot <?php echo basename($_SERVER['PHP_SELF']) === 'messages.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-envelope"></i>
        </a>
        <a href="profile.php" style="display:flex;align-items:center;">
            <img src="" alt="Profil" class="avatar-small" id="header-avatar" title="Chargement...">
        </a>
        <a href="#" id="logout-btn-header" class="btn-bot" title="Déconnexion">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</header>
