<?php

class Auth
{
    private PDO $db;
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900;

    public function __construct(PDO $dbConnection)
    {
        $this->db = $dbConnection;
    }

    public static function startSecureSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        session_start();
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public function login(string $email, string $password): array
    {
        $normalizedEmail = strtolower(trim($email));

        if (!filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL) || $password === '') {
            return ['success' => false, 'message' => 'Identifiants invalides.'];
        }

        $attemptKey = $this->attemptKey($normalizedEmail);
        if ($this->isLocked($attemptKey)) {
            return ['success' => false, 'message' => 'Trop de tentatives. Reessayez plus tard.'];
        }

        $stmt = $this->db->prepare('SELECT id, email, password_hash FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $normalizedEmail]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->recordFailure($attemptKey);
            return ['success' => false, 'message' => 'Identifiants invalides.'];
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
            $updateStmt->execute([
                'password_hash' => $newHash,
                'id' => $user['id'],
            ]);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in'] = true;

        $this->clearFailures($attemptKey);
        return ['success' => true, 'message' => 'Connexion reussie.'];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    private function attemptKey(string $email): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        return hash('sha256', $email . '|' . $ip);
    }

    private function isLocked(string $key): bool
    {
        $attempts = $_SESSION['login_attempts'][$key] ?? null;
        if (!$attempts) {
            return false;
        }

        if ($attempts['count'] < self::MAX_ATTEMPTS) {
            return false;
        }

        return (time() - (int) $attempts['last_attempt']) < self::LOCKOUT_SECONDS;
    }

    private function recordFailure(string $key): void
    {
        $now = time();
        if (empty($_SESSION['login_attempts'][$key])) {
            $_SESSION['login_attempts'][$key] = ['count' => 1, 'last_attempt' => $now];
            return;
        }

        $_SESSION['login_attempts'][$key]['count']++;
        $_SESSION['login_attempts'][$key]['last_attempt'] = $now;
    }

    private function clearFailures(string $key): void
    {
        unset($_SESSION['login_attempts'][$key]);
    }
}
