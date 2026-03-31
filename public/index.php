<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusConnect - Connexion</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<section class="view-auth" style="display:flex;">
    <div class="auth-shell">

        <!-- COLONNE GAUCHE : présentation -->
        <div class="auth-left">
            <div class="auth-brand">
                <div class="auth-brand-icon"><i class="fa-solid fa-lock"></i></div>
                <div>
                    <div class="auth-brand-title">CampusConnect</div>
                    <div class="auth-brand-sub">Secure campus community access</div>
                </div>
            </div>

            <h2>Welcome to your campus hub</h2>
            <p class="auth-lead">
                Sign in or create an account to join your university network : events, groups,
                and verified campus resources — all protected with enterprise-grade security.
            </p>

            <div class="auth-image-grid">
                <div class="auth-image-tile">
                    <img alt="Campus" src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80">
                </div>
                <div class="auth-image-tile">
                    <img alt="Student" src="https://picsum.photos/seed/campus-student/900/500">
                </div>
            </div>

            <div class="auth-protected">
                <i class="fa-solid fa-shield-halved"></i>
                <div>
                    <div class="auth-protected-title">Protected &amp; Verified</div>
                    <div class="auth-protected-sub">
                        All accounts require campus-issued email and optional ID verification
                        for groups and events.
                    </div>
                </div>
            </div>

            <div class="auth-support muted">
                Need help? Contact IT Support : it-support@university.edu — (555) 210-1010
            </div>
        </div>

        <!-- COLONNE DROITE : formulaires login / signup -->
        <div class="auth-right">
            <div class="auth-card">

                <div class="auth-card-head">
                    <div>
                        <div class="auth-card-title">Welcome back</div>
                        <div class="auth-card-sub">Sign in or create an account to continue</div>
                    </div>
                    <div class="auth-secure">
                        <span class="secure-pill">TLS Encrypted</span>
                    </div>
                </div>

                <div id="auth-message-box" style="display:none; padding:10px; margin-bottom:15px; border-radius:5px;"></div>

                <!-- Onglets -->
                <div class="auth-tabs">
                    <button class="auth-tab active" type="button" data-auth-form="login">Login</button>
                    <button class="auth-tab"        type="button" data-auth-form="signup">Sign-up</button>
                </div>

                <!-- Formulaire LOGIN -->
                <form id="form-login" class="auth-form" data-auth-form="login" method="post" action="">
                    <label class="auth-label" for="email">Email</label>
                    <input class="auth-input" id="email" type="email" name="email"
                           placeholder="Votre email" required autocomplete="email">

                    <label class="auth-label" for="password">Mot de passe</label>
                    <div class="auth-input-with-icon">
                        <input class="auth-input" id="password" type="password" name="password"
                               placeholder="Votre mot de passe" required autocomplete="current-password">
                        <i class="fa-solid fa-eye auth-eye" id="toggle-password" style="cursor:pointer;"></i>
                    </div>

                    <div class="auth-row">
                        <label class="auth-checkbox">
                            <input type="checkbox" name="remember"> <span>Se souvenir de moi</span>
                        </label>
                        <a href="#" class="auth-link">Mot de passe oublié ?</a>
                    </div>

                    <button class="auth-primary" type="submit">Se connecter</button>
                    <div class="auth-rec">
                        <span class="rec-pill">Campus-only membership required</span>
                    </div>
                </form>

                <!-- Formulaire SIGNUP -->
                <form id="form-signup" class="auth-form" data-auth-form="signup"
                      style="display:none;" method="post" action="">

                    <div class="auth-form-grid">
                        <div>
                            <label class="auth-label" for="signup_fullname">Nom complet</label>
                            <input class="auth-input" id="signup_fullname" type="text"
                                   name="full_name" placeholder="Votre nom" required>
                        </div>
                        <div>
                            <label class="auth-label" for="signup_email">Email campus</label>
                            <input class="auth-input" id="signup_email" type="email"
                                   name="email" placeholder="vous@campus.edu"
                                   required autocomplete="email">
                        </div>
                    </div>

                    <label class="auth-label" for="signup_password">Mot de passe</label>
                    <div class="auth-form-grid2">
                        <input class="auth-input" id="signup_password" type="password"
                               name="password" placeholder="Mot de passe"
                               required autocomplete="new-password">
                        <input class="auth-input" id="signup_password_confirm" type="password"
                               name="password_confirm" placeholder="Confirmer"
                               required autocomplete="new-password">
                    </div>

                    <div class="auth-form-select-row" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:10px;">
                        <div>
                            <label class="auth-label">Formation</label>
                            <select class="auth-input" name="formation">
                                <option value="informatique">Informatique</option>
                                <option value="communication">Communication</option>
                                <option value="design">Design</option>
                            </select>
                        </div>
                        <div>
                            <label class="auth-label">Année</label>
                            <select class="auth-input" name="annee_etude">
                                <option value="1">1ère année</option>
                                <option value="2">2ème année</option>
                                <option value="3">3ème année</option>
                            </select>
                        </div>
                    </div>

                    <div class="auth-terms" style="margin-top:14px;">
                        <label class="auth-checkbox">
                            <input type="checkbox" required>
                            <span>J'accepte les CGU &amp; la Politique de confidentialité</span>
                        </label>
                    </div>

                    <button class="auth-primary" type="submit">Créer un compte</button>

                    <div class="auth-footer-links">
                        <a href="#" class="auth-link">CGU</a>
                        <a href="#" class="auth-link">Confidentialité</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>

<script>
// Affichage des messages d'erreur/succès
function showMessage(msg, isError) {
    const box = document.getElementById('auth-message-box');
    box.textContent = msg;
    box.style.display = 'block';
    box.style.backgroundColor = isError ? '#fee2e2' : '#dcfce7';
    box.style.color = isError ? '#b91c1c' : '#166534';
}

// Gestion des onglets login / signup
document.querySelectorAll('.auth-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const target = tab.dataset.authForm;
        document.querySelectorAll('.auth-form').forEach(f => {
            f.style.display = f.dataset.authForm === target ? 'block' : 'none';
        });
        document.getElementById('auth-message-box').style.display = 'none'; // reset message
    });
});

// Afficher / masquer le mot de passe
const togglePassword = document.getElementById('toggle-password');
const passwordInput  = document.getElementById('password');
if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', () => {
        const isText = passwordInput.type === 'text';
        passwordInput.type = isText ? 'password' : 'text';
        togglePassword.classList.toggle('fa-eye',      isText);
        togglePassword.classList.toggle('fa-eye-slash', !isText);
    });
}

// Interception AJAX pour LOGIN
document.getElementById('form-login').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    try {
        const res = await fetch('../Backend/public/index.php?route=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.status === 'success') {
            window.location.href = 'feed.php';
        } else {
            showMessage(result.message, true);
        }
    } catch (err) {
        showMessage("Erreur réseau ou serveur inaccessible.", true);
    }
});

// Interception AJAX pour SIGNUP
document.getElementById('form-signup').addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(e.target));
    data.nom = data.full_name; // Mapping pour l'API
    
    if (data.password !== data.password_confirm) {
        showMessage("Les mots de passe ne correspondent pas.", true);
        return;
    }
    
    try {
        const res = await fetch('../Backend/public/index.php?route=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.status === 'success') {
            showMessage("Inscription réussie ! Vous pouvez vous connecter.", false);
            document.querySelector('[data-auth-form="login"]').click(); // switch tab
            document.getElementById('form-signup').reset();
        } else {
            showMessage(result.message, true);
        }
    } catch (err) {
        showMessage("Erreur lors de l'inscription.", true);
    }
});
</script>
</body>
</html>
