<?php

class Auth
{
    private PDO $db;
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 900;

    public function __construct(PDO $dbConnection)
    {
        $this->db = $dbConnection;
        self::startSecureSession();
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

    // Compatibility method kept for flows that register via Auth class.
    public function register($nom, $email, $password, $formation, $campus, $annee_etude): bool
    {
        $normalizedEmail = strtolower(trim((string) $email));
        $password = (string) $password;

        if (!filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL) || !$this->isStrongPassword($password)) {
            return false;
        }

        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare(
                'INSERT INTO `USER` (nom, email, password, formation, campus, annee_etude) VALUES (:nom, :email, :password, :formation, :campus, :annee_etude)'
            );

            return $stmt->execute([
                ':nom' => $nom,
                ':email' => $normalizedEmail,
                ':password' => $hashedPassword,
                ':formation' => $formation,
                ':campus' => $campus,
                ':annee_etude' => $annee_etude,
            ]);
        } catch (PDOException $e) {
            return false;
        }
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

        $user = $this->findUserByEmail($normalizedEmail);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->recordFailure($attemptKey);
            return ['success' => false, 'message' => 'Identifiants invalides.'];
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $this->rehashPassword((int) $user['id'], $password, (string) $user['source']);
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_email'] = (string) $user['email'];
        $_SESSION['logged_in'] = true;

        // Backward-compatible session keys used by older pages.
        $_SESSION['id_user'] = (int) $user['id'];
        $_SESSION['nom'] = (string) ($user['nom'] ?? '');
        $_SESSION['role'] = (string) ($user['role'] ?? 'etudiant');

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

    public function isLoggedIn(): bool
    {
        return !empty($_SESSION['logged_in']) || !empty($_SESSION['id_user']);
    }

    private function findUserByEmail(string $email): ?array
    {
        try {
            $stmt = $this->db->prepare('SELECT id, email, password_hash FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            if ($user) {
                $user['source'] = 'users';
                return $user;
            }
        } catch (PDOException $e) {
            // Fallback to legacy schema.
        }

        try {
            $stmt = $this->db->prepare('SELECT id_user AS id, email, password AS password_hash, nom, role FROM `USER` WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            if ($user) {
                $user['source'] = 'USER';
                return $user;
            }
        } catch (PDOException $e) {
            return null;
        }

        return null;
    }

    private function rehashPassword(int $id, string $plainPassword, string $source): void
    {
        $newHash = password_hash($plainPassword, PASSWORD_DEFAULT);

        try {
            if ($source === 'users') {
                $stmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
                $stmt->execute(['password_hash' => $newHash, 'id' => $id]);
                return;
            }

            $stmt = $this->db->prepare('UPDATE `USER` SET password = :password WHERE id_user = :id');
            $stmt->execute(['password' => $newHash, 'id' => $id]);
        } catch (PDOException $e) {
            // Keep login successful even if rehash write fails.
        }
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

    private function isStrongPassword(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        $hasLower = preg_match('/[a-z]/', $password);
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasDigit = preg_match('/\d/', $password);
        $hasSymbol = preg_match('/[^A-Za-z0-9]/', $password);

        return (bool) ($hasLower && $hasUpper && $hasDigit && $hasSymbol);
    }
}
