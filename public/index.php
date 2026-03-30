<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Auth.php';

Auth::startSecureSession();

$message = '';
$isError = false;

if (!empty($_SESSION['logged_in'])) {
	header('Location: profile.php');
	exit;
}

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
			$message = $result['message'];
			$isError = !$result['success'];

			if ($result['success']) {
				header('Location: profile.php');
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
	<title>CampusConnect - News Feed</title>
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

	<section id="view-app" style="display:none;">
		<header class="main-header">
			<div class="header-left">
				<h1 class="logo"><i class="fa-solid fa-graduation-cap"></i> CampusConnect</h1>
			</div>

			<div class="header-center">
				<div class="search-bar">
					<i class="fa-solid fa-magnifying-glass"></i>
					<input type="text" placeholder="Rechercher des etudiants, posts...">
				</div>
			</div>

			<div class="header-right">
				<button class="btn-bot" id="btn-lang-switcher" title="Changer de langue">
					<i class="fa-solid fa-language"></i> FR
				</button>
				<button class="btn-new-post" id="btn-newpost" title="New Post">
					<i class="fa-solid fa-circle-plus"></i> New Post
				</button>
				<div class="profile-icon">
					<img src="https://ui-avatars.com/api/?name=Leila+Martinez&amp;background=2563eb&amp;color=fff" alt="Profil">
				</div>
			</div>
		</header>

		<div class="layout-grid container">
			<aside class="sidebar-left">
				<nav class="main-nav">
					<ul>
						<li><a href="#" id="link-home" class="nav-link active" data-target="view-feed"><i class="fa-solid fa-house"></i> <span data-translate-key="nav_home">Home</span></a></li>
						<li><a href="#" id="link-messages" class="nav-link" data-target="view-messages"><i class="fa-solid fa-envelope"></i> <span data-translate-key="nav_messages">Messages</span></a></li>
						<li><a href="#" id="link-profile" class="nav-link" data-target="view-profile"><i class="fa-solid fa-user"></i> <span data-translate-key="nav_profile">Profile</span></a></li>
						<li><a href="#" id="link-friends" class="nav-link" data-target="view-friends"><i class="fa-solid fa-user-group"></i> <span data-translate-key="nav_friends">Amis</span></a></li>
						<li><a href="#" id="link-settings" class="nav-link" data-target="view-settings"><i class="fa-solid fa-gear"></i> <span data-translate-key="nav_settings">Reglages</span></a></li>
						<li><a href="#" class="logout"><i class="fa-solid fa-right-from-bracket"></i> <span data-translate-key="nav_logout">Logout</span></a></li>
					</ul>
				</nav>
			</aside>

			<main class="feed-center">
				<section id="view-feed">
					<div class="create-post card">
						<div class="create-post-header">
							<img src="https://ui-avatars.com/api/?name=Leila+Martinez&amp;background=2563eb&amp;color=fff" alt="Profil" class="avatar-small">
							<textarea placeholder="Quoi de neuf sur le campus ?" rows="2"></textarea>
						</div>
						<div class="create-post-actions">
							<button class="btn-icon btn-open-composer" type="button"><i class="fa-solid fa-image"></i> Photo</button>
							<button class="btn-icon btn-open-composer" type="button"><i class="fa-solid fa-video"></i> Video</button>
							<button class="btn-icon" id="btn-tag-post"><i class="fa-solid fa-tags"></i> Tag</button>
							<button class="btn-icon btn-open-composer" type="button"><i class="fa-solid fa-users"></i> Audience</button>
							<button class="btn-primary btn-open-composer" type="button">Post</button>
						</div>
					</div>

					<div id="posts-container"></div>
				</section>

				<section id="view-profile" style="display:none;">
					<div class="profile-page">
						<div class="profile-cover">
							<div class="profile-cover-image" style="background-image:url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&amp;fit=crop&amp;w=1600&amp;q=80');"></div>
						</div>
						<div class="profile-header-card card">
							<div class="profile-header-top">
								<img src="https://ui-avatars.com/api/?name=Leila+Martinez&amp;background=2563eb&amp;color=fff" alt="Profile" class="profile-avatar-large" id="profile-picture">
								<div class="profile-identity">
									<h2 id="profile-name">Leila Martinez</h2>
									<p class="profile-bio" id="profile-bio">B.S. Computer Science, Class of 2025 · Software Engineering Major</p>
									<div class="profile-contact">
										<div class="contact-item-row"><i class="fa-solid fa-envelope"></i><span class="muted small" id="profile-email">leila.martinez@campus.univ.edu</span></div>
										<div class="contact-item-row"><i class="fa-solid fa-location-dot"></i><span class="muted small" id="profile-location">Oakwood Campus · San Mateo, CA</span></div>
									</div>
								</div>
								<div class="profile-actions">
									<button class="btn-outline-sm" type="button" id="btn-follow-profile" style="display: none;">Follow</button>
									<button class="btn-outline-sm" type="button" id="btn-message-profile" style="display: none;">Message</button>
									<button class="btn-primary btn-edit-sm" type="button" id="btn-edit-profile" style="display: none;"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</button>
								</div>
							</div>
							<div class="profile-stats-grid">
								<div class="stat-box"><span class="stat-value" id="stat-posts">128</span><span class="stat-label" data-translate-key="profile_competences">Competences</span></div>
								<div class="stat-box"><span class="stat-value" id="stat-followers">3,842</span><span class="stat-label" data-translate-key="profile_followers">Followers</span></div>
								<div class="stat-box"><span class="stat-value" id="stat-following">412</span><span class="stat-label" data-translate-key="profile_following">Following</span></div>
								<div class="stat-box"><span class="stat-value" id="stat-groups">9</span><span class="stat-label" data-translate-key="profile_groups">Groups</span></div>
							</div>
							<div class="profile-tabs">
								<button class="profile-tab active" data-profile-tab="competences" type="button" data-translate-key="profile_tab_competences">Competences</button>
								<button class="profile-tab" data-profile-tab="about" type="button" data-translate-key="profile_tab_about">About</button>
								<button class="profile-tab" data-profile-tab="activities" type="button" data-translate-key="profile_tab_activities">Activities</button>
								<button class="profile-tab" data-profile-tab="photos" type="button" data-translate-key="profile_tab_photos">Photos &amp; Media</button>
							</div>
						</div>
						<div class="profile-feed" id="profile-posts-container"></div>
					</div>
				</section>

				<section id="view-messages" style="display:none;">
					<div class="messages-container card">
						<div class="messages-sidebar">
							<div class="messages-actions"><button class="btn-primary new-discussion-btn" id="btn-new-discussion" type="button"><i class="fa-solid fa-pen-to-square"></i> New discussion</button></div>
							<div class="messages-search"><input type="text" placeholder="Rechercher..."></div>
							<ul class="contacts-list" id="contacts-list"></ul>
						</div>
						<div class="messages-chat">
							<div class="chat-header" id="chat-header">Selectionnez une conversation</div>
							<div class="chat-history" id="chat-history"></div>
							<div class="chat-input-area">
								<input type="text" placeholder="Ecrire un message..." id="chat-input">
								<button class="btn-primary" id="btn-send-message"><i class="fa-solid fa-paper-plane"></i></button>
							</div>
						</div>
					</div>
				</section>

				<section id="view-friends" style="display:none;">
					<div class="card">
						<h2 data-translate-key="friends_manage">Gerer les amis</h2>
						<p class="muted small">Ajoutez de nouvelles connexions ou retrouvez vos amis.</p>
						<div class="add-friend-form">
							<input type="text" id="invite-friend-input" placeholder="Entrez l'email ou le nom d'utilisateur...">
							<button class="btn-primary" id="btn-invite-friend">Inviter</button>
						</div>
						<h3>Mes Amis (<span id="friends-count">0</span>)</h3>
						<ul class="friends-list" id="friends-list-container"></ul>
					</div>
				</section>

				<section id="view-settings" style="display:none;">
					<div class="card">
						<h2 data-translate-key="settings_title">Reglages du compte</h2>
						<p class="muted small">Gerez vos preferences et la confidentialite de votre compte.</p>
						<div class="side-toggle" style="margin-top: 20px;">
							<div>
								<div class="toggle-title">Notifications par email</div>
								<div class="toggle-sub muted small">Recevoir un email pour les nouveaux messages.</div>
							</div>
							<label class="switch"><input type="checkbox" checked><span class="slider round"></span></label>
						</div>
						<button class="btn-primary" style="margin-top: 20px;">Enregistrer les modifications</button>
					</div>
				</section>
			</main>

			<aside class="sidebar-right">
				<section id="right-feed">
					<div class="events-widget card">
						<h2>Campus News &amp; Events</h2>
						<ul class="events-list" id="events-container">
							<li class="event-item"><div class="event-date"><span class="day">Today</span></div><div class="event-details"><h3>48h Challenge</h3><p>Campus Ynov</p></div></li>
							<li class="event-item"><div class="event-date"><span class="day">Mar</span></div><div class="event-details"><h3>BDE Gala</h3><p>Salle des fetes</p></div></li>
							<li class="event-item"><div class="event-date"><span class="day">Jeu</span></div><div class="event-details"><h3>BDS Sports Day</h3><p>Stade Universitaire</p></div></li>
						</ul>
					</div>
					<div class="card profile-sidecard">
						<h2>Announcements</h2>
						<div class="announcement muted small">Keep your campus verified profile to unlock visibility controls.</div>
					</div>
				</section>

				<section id="right-profile" style="display:none;">
					<div class="card profile-sidecard">
						<h2>Mutual Connections</h2>
						<div class="mutual-row">
							<div class="mutual-item"><img class="avatar-small" src="https://ui-avatars.com/api/?name=Marcus+Green&amp;background=1e293b&amp;color=fff" alt="Marcus Green"><div><div class="mutual-name">Marcus Green</div><div class="mutual-meta muted small">Mutual</div></div></div>
							<div class="mutual-item"><img class="avatar-small" src="https://ui-avatars.com/api/?name=Aisha+Khan&amp;background=1e293b&amp;color=fff" alt="Aisha Khan"><div><div class="mutual-name">Aisha Khan</div><div class="mutual-meta muted small">Mutual</div></div></div>
							<div class="mutual-item"><img class="avatar-small" src="https://ui-avatars.com/api/?name=Daniel+Park&amp;background=1e293b&amp;color=fff" alt="Daniel Park"><div><div class="mutual-name">Daniel Park</div><div class="mutual-meta muted small">Mutual</div></div></div>
						</div>
					</div>
					<div class="card profile-sidecard">
						<h2>Common Courses</h2>
						<div class="pill-list"><span class="pill-pill">CS 321 · Web Systems</span><span class="pill-pill">HCI 410 · User Interfaces</span><span class="pill-pill">MATH 207 · Discrete Math</span></div>
					</div>
					<div class="card profile-sidecard">
						<h2>Quick Actions</h2>
						<button class="btn-secondary-full" type="button" id="btn-send-message-side"><i class="fa-regular fa-paper-plane"></i> Send Message</button>
						<button class="btn-secondary-full" type="button" id="btn-add-friend"><i class="fa-solid fa-user-plus"></i> Add Friend</button>
						<button class="btn-danger-full" type="button" id="btn-report-user"><i class="fa-solid fa-flag"></i> Report</button>
					</div>
					<div class="card profile-sidecard">
						<h2>Visitor Privacy</h2>
						<div class="side-toggle"><div><div class="toggle-title">Allow messages</div><div class="toggle-sub muted small">Changes apply to non-connection visitors.</div></div><label class="switch"><input type="checkbox" checked><span class="slider round"></span></label></div>
						<div class="side-toggle"><div class="toggle-title">Show courses</div><label class="switch"><input type="checkbox" checked><span class="slider round"></span></label></div>
						<div class="side-toggle"><div class="toggle-title">Show photos</div><label class="switch"><input type="checkbox" checked><span class="slider round"></span></label></div>
					</div>
					<div class="card profile-sidecard">
						<h2>Privacy Tips</h2>
						<div class="muted small">Set visibility to Campus-only to limit access. Connect campus directories to enable verification badges.</div>
					</div>
				</section>
			</aside>
		</div>

		<div id="composer-overlay" class="composer-overlay" aria-hidden="true" style="display:none;">
			<div class="composer-dialog" role="dialog" aria-modal="true" aria-label="Create Post">
				<button class="composer-cancel" id="btn-composer-cancel" type="button">Cancel</button>
				<div class="composer-columns">
					<div class="composer-left">
						<div class="composer-topline">
							<div class="composer-user">
								<img class="avatar-small" src="https://ui-avatars.com/api/?name=Student&amp;background=0070f3&amp;color=fff" alt="Student" />
								<div>
									<div class="composer-user-title">Student · Computer Science</div>
									<div class="composer-user-sub muted">Draft: saved 2m ago</div>
								</div>
							</div>
							<div class="composer-mini-chip">Audience <strong>Campus</strong></div>
						</div>
						<div class="editor-toolbar">
							<button class="toolbar-btn" type="button"><i class="fa-solid fa-bold"></i></button>
							<button class="toolbar-btn" type="button"><i class="fa-solid fa-italic"></i></button>
							<button class="toolbar-btn" type="button"><i class="fa-solid fa-underline"></i></button>
							<span class="toolbar-spacer"></span>
							<button class="toolbar-btn primaryish" type="button"><i class="fa-solid fa-wand-magic-sparkles"></i> Post</button>
						</div>
						<div class="composer-editor">
							<textarea placeholder="Share what’s happening on campus, announce an event, or post updates..."></textarea>
							<div class="composer-editor-hint muted">Support: No harassment, hate speech or spam.</div>
						</div>
						<div class="composer-section">
							<div class="section-title">Attach Media</div>
							<div class="media-dropzone">
								<div class="muted">Drag &amp; drop files here or browse</div>
								<div class="media-buttons">
									<button class="media-btn" type="button"><i class="fa-regular fa-image"></i> Upload Image</button>
									<button class="media-btn" type="button"><i class="fa-solid fa-file-video"></i> Upload Video</button>
								</div>
								<div class="media-thumbs"><div class="thumb muted">Thumbnail</div><div class="thumb muted">Thumbnail</div></div>
							</div>
						</div>
						<div class="composer-section">
							<div class="section-title">Tag People / Clubs / Courses</div>
							<input class="section-input" type="text" placeholder="Search people, clubs or courses" />
							<div class="mention-row"><button class="mention-pill" type="button">Use to mention</button><div class="mention-chips"><span class="chip">Computer Science</span><span class="chip">Design Club</span></div></div>
						</div>
						<div class="composer-section">
							<div class="section-title">Add Location</div>
							<div class="location-row"><input class="section-input" type="text" placeholder="Search campus locations, buildings or addresses" /><button class="find-btn" type="button"><i class="fa-solid fa-location-crosshairs"></i> Find</button></div>
							<div class="location-preview muted">Map preview</div>
						</div>
						<div class="composer-section">
							<div class="section-title">Schedule / Event</div>
							<div class="schedule-row">
								<label class="radio-pill"><input type="radio" name="sched" checked /><span>Schedule</span></label>
								<label class="radio-pill"><input type="radio" name="sched" /><span>Make it an Event</span></label>
							</div>
							<div class="schedule-grid">
								<div class="field"><div class="field-label">Event Name</div><input class="section-input" placeholder="Event Name" /></div>
								<div class="field"><div class="field-label">Date / Time</div><input class="section-input" placeholder="MM/DD/YYYY HH:MM" /></div>
								<div class="field"><div class="field-label">Location</div><input class="section-input" placeholder="Campus location" /></div>
								<div class="field"><div class="field-label">Description</div><input class="section-input" placeholder="Event details" /></div>
							</div>
						</div>
						<div class="composer-section">
							<div class="section-title">Choose Audience</div>
							<div class="audience-grid">
								<label class="audience-option"><input type="radio" name="audience" checked /><span>Public</span><div class="muted small">Visible on campus + web.</div></label>
								<label class="audience-option"><input type="radio" name="audience" /><span>Campus</span><div class="muted small">Only students and staff.</div></label>
								<label class="audience-option"><input type="radio" name="audience" /><span>Alumni</span><div class="muted small">Visible for alumni only.</div></label>
							</div>
						</div>
					</div>
					<div class="composer-right">
						<div class="live-preview">
							<div class="live-preview-title">Live Preview</div>
							<div class="live-preview-meta muted small">Visibility on web: Campus-only</div>
							<div class="preview-post">
								<div class="preview-user"><img class="avatar-small" src="https://ui-avatars.com/api/?name=Student&amp;background=1d4ed8&amp;color=fff" alt="Student" /><div><div class="preview-name">Student · Computer Science</div><div class="muted small">Just now</div></div></div>
								<div class="preview-text muted small">Share what’s happening on campus…</div>
								<div class="preview-media"><div class="preview-thumb"></div><div class="preview-thumb"></div></div>
							</div>
						</div>
					</div>
				</div>
				<div class="composer-bottom-bar">
					<div class="composer-success">Success! Your post has been scheduled and will publish at 7:02 AM on 04/08/2026.</div>
					<div class="composer-actions">
						<button class="btn-primary composer-publish" type="button">Publish</button>
						<button class="btn-secondary draft-btn" type="button">Save Draft</button>
						<button class="btn-secondary cancel-btn" type="button">Cancel</button>
						<div class="composer-autosave muted small">AutoSave: Last saved 2m ago</div>
						<div class="composer-kb muted small">Shortcuts: Ctrl+Enter Publish · Ctrl+S Save Draft</div>
					</div>
				</div>
			</div>
		</div>

		<div id="chatbot-widget" class="chatbot-widget" style="display:none;">
			<div class="chatbot-header">
				<div class="chatbot-title"><i class="fa-solid fa-robot"></i> Campus Bot</div>
				<button class="chatbot-close" id="btn-chatbot-close" type="button"><i class="fa-solid fa-xmark"></i></button>
			</div>
			<div class="chatbot-body" id="chatbot-body"><div class="bot-msg">Salut, je suis Campus Bot. Je peux t'aider pour profil, posts et messages.</div></div>
			<div class="chatbot-input-row">
				<input type="text" id="chatbot-input" placeholder="Ecris ton message..." />
				<button id="btn-chatbot-send" type="button"><i class="fa-solid fa-paper-plane"></i></button>
			</div>
		</div>
	</section>

	<script type="module" src="js/app.js"></script>
</body>
</html>
