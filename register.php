<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../Backend/models/Auth.php';
require_once __DIR__ . '/../Backend/models/User.php';

Auth::startSecureSession();

$message = '';
$isError = false;

if (!empty($_SESSION['logged_in'])) {
	header('Location: profile.php');
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$csrfToken = $_POST['csrf_token'] ?? null;
	$password = $_POST['password'] ?? '';
	$passwordConfirm = $_POST['password_confirm'] ?? '';

	if (!Auth::verifyCsrfToken($csrfToken)) {
		$message = 'Requete invalide.';
		$isError = true;
	} elseif (!hash_equals($password, $passwordConfirm)) {
		$message = 'Donnees invalides.';
		$isError = true;
	} else {
		try {
			$database = new Database();
			$user = new User($database->connect());
			$result = $user->register($_POST['email'] ?? '', $password);
			$message = $result['message'];
			$isError = !$result['success'];

			if ($result['success']) {
				header('Location: index.php');
				exit;
			}
		} catch (Throwable $e) {
			$message = 'Une erreur est survenue.';
			$isError = true;
		}
	}
}

$token = Auth::csrfToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Inscription</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<main>
		<h1>Inscription</h1>
		<?php if ($message !== ''): ?>
			<p style="color: <?php echo $isError ? '#b91c1c' : '#166534'; ?>;">
				<?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
			</p>
		<?php endif; ?>

		<form method="post" action="">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

			<label for="email">Email</label>
			<input id="email" type="email" name="email" required autocomplete="email">

			<label for="password">Mot de passe</label>
			<input id="password" type="password" name="password" required autocomplete="new-password">

			<label for="password_confirm">Confirmer le mot de passe</label>
			<input id="password_confirm" type="password" name="password_confirm" required autocomplete="new-password">

			<button type="submit">S'inscrire</button>
		</form>

		<p><a href="index.php">Deja un compte ? Connexion</a></p>
	</main>
</body>
</html>
