<?php
require_once __DIR__ . '/../src/Auth.php';

Auth::startSecureSession();

if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errorMessage = '';
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
        <p>
            Ymatch est la plateforme de mise en relation de l'ecole Ynov entre etudiants et entreprises.
            Elle permet de trouver des offres de stage, d'alternance et d'emploi adaptees aux profils des apprenants.
        </p>

        <p>
            Acceder a Ymatch:
            <a href="https://www.ymatch.com" target="_blank" rel="noopener noreferrer">www.ymatch.com</a>
        </p>

        <p><a href="profile.php">Retour au profil</a></p>
    </main>
</body>
</html>

