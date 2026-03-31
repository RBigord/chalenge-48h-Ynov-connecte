<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/User.php';

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
	<section id="view-auth" class="view-auth" style="display:flex;">
		<div class="auth-shell">
			<div class="auth-left">
				<div class="auth-brand">
					<div class="auth-brand-icon"><i class="fa-solid fa-lock"></i></div>
					<div>
						<div class="auth-brand-title">CampusConnect</div>
						<div class="auth-brand-sub">Secure campus community access</div>
					</div>
				</div>

				<h2 data-translate-key="auth_welcome_title">Welcome to your campus hub</h2>
				<p class="auth-lead" data-translate-key="auth_welcome_subtitle">
					Sign in or create an account to join your university network: events, groups, and verified campus resources - all protected with enterprise-grade security.
				</p>

				<div class="auth-image-grid">
					<div class="auth-image-tile">
						<img alt="Campus" src="https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&amp;fit=crop&amp;w=900&amp;q=80" />
					</div>
					<div class="auth-image-tile">
						<img alt="Student" src="https://picsum.photos/seed/campus-student/900/500" />
					</div>
				</div>

				<div class="auth-protected">
					<i class="fa-solid fa-shield-halved"></i>
					<div>
						<div class="auth-protected-title">Protected &amp; Verified</div>
						<div class="auth-protected-sub">
							All accounts require campus-issued email and optional ID verification for groups and events.
						</div>
					</div>
				</div>

				<div class="auth-support muted">
					Need help? Contact IT Support: it-support@university.edu - (555) 210-1010
				</div>
			</div>

			<div class="auth-right">
				<div class="auth-card">
					<button class="btn-lang-auth" id="btn-lang-switcher-auth" title="Changer de langue">
						<i class="fa-solid fa-language"></i> FR
					</button>
					<div class="auth-card-head">
						<div>
							<div class="auth-card-title" data-translate-key="auth_card_title">Welcome back</div>
							<div class="auth-card-sub" data-translate-key="auth_card_subtitle">Sign in or create an account to continue to CampusConnect</div>
						</div>
						<div class="auth-secure">
							<span class="secure-pill">TLS Encrypted</span>
						</div>
					</div>

					<?php if ($message !== ''): ?>
						<p style="color: <?php echo $isError ? '#b91c1c' : '#166534'; ?>; margin: 10px 0;">
							<?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
						</p>
					<?php endif; ?>

					<div class="auth-tabs">
						<button class="auth-tab active" type="button" data-auth-form="login" data-translate-key="auth_tab_login">Login</button>
						<button class="auth-tab" type="button" data-auth-form="signup" data-translate-key="auth_tab_signup">Sign-up</button>
					</div>
					
					<form id="form-signup" class="auth-form" data-auth-form="signup" style="display:none;" method="post" action="register.php">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
						<div class="auth-form-grid">
							<div>
								<label class="auth-label" for="signup_fullname">Full name</label>
								<input class="auth-input" id="signup_fullname" type="text" name="full_name" placeholder="Your full name" />
							</div>
							<div>
								<label class="auth-label" for="signup_email">Campus email</label>
								<input class="auth-input" id="signup_email" type="email" name="email" placeholder="you@campus.edu" required autocomplete="email" />
							</div>
						</div>

						<label class="auth-label" for="signup_password">Password</label>
						<div class="auth-form-grid2">
							<input class="auth-input" id="signup_password" type="password" name="password" placeholder="Password" required autocomplete="new-password" />
							<input class="auth-input" id="signup_password_confirm" type="password" name="password_confirm" placeholder="Confirm password" required autocomplete="new-password" />
						</div>

						<div class="auth-form-select-row">
							<div>
								<label class="auth-label">Major</label>
								<select class="auth-input">
									<option>Computer Science</option>
								</select>
							</div>
							<div>
								<label class="auth-label">Year</label>
								<select class="auth-input">
									<option>First year</option>
								</select>
							</div>
						</div>

						<label class="auth-label">Student ID verification</label>
						<button class="auth-secondary" type="button">Upload</button>

						<div class="auth-terms">
							<div class="auth-help muted">Accepted formats: JPG, PNG, PDF Max 5MB.</div>
							<label class="auth-checkbox">
								<input type="checkbox" checked />
								<span>I agree to the Terms &amp; Privacy</span>
							</label>
						</div>

						<button class="auth-primary" type="submit" id="btn-auth-signup" data-translate-key="auth_create_button">Create account</button>
						<div class="auth-footer-links">
							<a href="#" class="auth-link">Terms</a>
							<a href="#" class="auth-link">Privacy</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>
</body>
</html>
