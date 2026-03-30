<?php
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/ia_logic.php';

Auth::startSecureSession();

if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';
$iaResult = '';
$csrfToken = Auth::csrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'logout') {
    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errorMessage = 'Requete invalide.';
    } else {
        Auth::logout();
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ai_resume') {
    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errorMessage = 'Requete invalide.';
    } else {
        $texteNews = trim((string) ($_POST['texte_news'] ?? ''));
        if ($texteNews === '') {
            $errorMessage = 'Veuillez saisir une actualite a resumer.';
        } else {
            $ia = new InnovationIA($maCle ?? null);
            $iaResult = $ia->resumerNews($texteNews);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'ai_job') {
    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errorMessage = 'Requete invalide.';
    } else {
        $competences = trim((string) ($_POST['competences'] ?? ''));
        if ($competences === '') {
            $errorMessage = 'Veuillez saisir vos competences.';
        } else {
            $ia = new InnovationIA($maCle ?? null);
            $iaResult = $ia->aideCandidature($competences);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ymatch</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main>
        <h1>Ymatch Job Board</h1>
        <?php if ($errorMessage !== ''): ?>
            <p style="color: #b91c1c;"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <?php if ($iaResult !== ''): ?>
            <section>
                <h2>Suggestion IA</h2>
                <p><?php echo nl2br(htmlspecialchars($iaResult, ENT_QUOTES, 'UTF-8')); ?></p>
            </section>
        <?php endif; ?>

        <p>
            Ymatch est la plateforme de mise en relation de l'ecole Ynov entre etudiants et entreprises.
            Elle permet de trouver des offres de stage, d'alternance et d'emploi adaptees aux profils des apprenants.
        </p>

        <p>
            Acceder a Ymatch:
            <a href="https://www.ymatch.com" target="_blank" rel="noopener noreferrer">www.ymatch.com</a>
        </p>

        <section>
            <h2>Resumer une actualite</h2>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="ai_resume">
                <textarea name="texte_news" rows="5" placeholder="Collez ici une actualite Ynov"></textarea>
                <button type="submit">Resumer avec lIA</button>
            </form>
        </section>

        <section>
            <h2>Aide candidature stage</h2>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="ai_job">
                <input type="text" name="competences" placeholder="Ex: PHP, SQL, JavaScript, gestion de projet">
                <button type="submit">Obtenir des suggestions</button>
            </form>
        </section>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="logout">
            <button type="submit">Se deconnecter</button>
        </form>

        <p><a href="profile.php">Retour au profil</a></p>
    </main>
</body>
</html>

