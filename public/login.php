<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Auth.php';

Auth::startSecureSession();

$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? null;
    if (!Auth::verifyCsrfToken($csrfToken)) {
        $message = 'Requete invalide.';
        $isError = true;
    } else {
        try {
            $database = new Database();
            $auth = new Auth($database->connect());
            $result = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');
            if ($result['success']) {
                header('Location: feed.php');
                exit;
            }
            $message = $result['message'];
            $isError = true;
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
    <title>Y Campus Connect - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <section id="view-auth" class="view-auth" style="display:flex;">
		<div class="auth-shell">
			<div class="auth-left">
				<div class="auth-brand">
					<div class="auth-brand-icon"><i class="fa-solid fa-lock"></i></div>
					<div>
						<div class="auth-brand-title">Y Campus Connect</div>
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
						<button href='login.php' class="auth-tab active" type="button" data-auth-form="login" data-translate-key="auth_tab_login">Login</button>
						<button href='register.php' class="auth-tab" type="button" data-auth-form="signup" data-translate-key="auth_tab_signup">Sign-up</button>
					</div>

					<form id="form-login" class="auth-form" data-auth-form="login" method="post" action="">
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

						<label class="auth-label" for="email">Email</label>
						<input class="auth-input" id="email" type="email" name="email" placeholder="Enter your email" required autocomplete="email" />

						<label class="auth-label" for="password">Password</label>
						<div class="auth-input-with-icon">
							<input class="auth-input" id="password" type="password" name="password" placeholder="Your password" required autocomplete="current-password" />
							<i class="fa-solid fa-eye auth-eye"></i>
						</div>

						<div class="auth-row">
							<label class="auth-checkbox">
								<input type="checkbox" checked />
								<span>Remember me</span>
							</label>
							<a href="#" class="auth-link">Forgot password?</a>
						</div>

						<button class="auth-primary" type="submit" id="btn-auth-login" data-translate-key="auth_signin_button">Sign in</button>
						<div class="auth-rec">
							<span class="rec-pill">reCAPTCHA protected</span>
							<span class="auth-link muted">Campus-only membership required</span>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>

    <script src="src/Auth.php"></script>
        
</body>
</html>