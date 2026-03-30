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
        <?php if ($errorMessage !== ''): ?>
            <p style="color: #b91c1c;"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="action" value="logout">
            <button type="submit">Se deconnecter</button>
        </form>

        <p><a href="profile.php">Retour au profil</a></p>
    </main>
</body>
</html>

