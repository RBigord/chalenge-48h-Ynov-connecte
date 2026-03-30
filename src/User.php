<?php

class User
{
    private PDO $db;

    public function __construct(PDO $dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function register(string $email, string $password): array
    {
        $normalizedEmail = strtolower(trim($email));

        if (!filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Donnees invalides.'];
        }

        if (!$this->isStrongPassword($password)) {
            return ['success' => false, 'message' => 'Mot de passe trop faible.'];
        }

        $checkStmt = $this->db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $checkStmt->execute(['email' => $normalizedEmail]);
        if ($checkStmt->fetch()) {
            return ['success' => false, 'message' => 'Donnees invalides.'];
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO users (email, password_hash, created_at) VALUES (:email, :password_hash, NOW())');
        $stmt->execute([
            'email' => $normalizedEmail,
            'password_hash' => $hash,
        ]);

        return ['success' => true, 'message' => 'Inscription reussie.'];
    }

    public function loadProfile(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, email, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $profile = $stmt->fetch();

        return $profile ?: null;
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

