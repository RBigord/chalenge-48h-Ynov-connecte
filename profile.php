<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Backend/models/Auth.php';
require_once __DIR__ . '/../Backend/models/User.php';

Auth::startSecureSession();

if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$profile = null;
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

try {
    $database = new Database();
    $user = new User($database->connect());
    $profile = $user->loadProfile((int) $_SESSION['user_id']);

    if (!$profile) {
        $errorMessage = 'Profil introuvable.';
    }
} catch (Throwable $e) {
    $errorMessage = 'Une erreur est survenue.';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main>
        <h1>Profil</h1>

        <?php if ($errorMessage !== ''): ?>
            <p style="color: #b91c1c;"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
            <p>ID: <?php echo (int) $profile['id']; ?></p>
            <p>Email: <?php echo htmlspecialchars($profile['email'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Inscrit le: <?php echo htmlspecialchars((string) $profile['created_at'], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="logout">
            <button type="submit">Se deconnecter</button>
        </form>

        <p><a href="ymatch.php">Acceder a Ymatch</a></p>
        <p><a href="index.php">Retour</a></p>
    </main>
</body>
</html>