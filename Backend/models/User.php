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

        try {
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
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Une erreur est survenue.'];
        }
    }

    public function loadProfile(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare('SELECT id, email, created_at FROM users WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $profile = $stmt->fetch();
            if ($profile) {
                return $profile;
            }
        } catch (PDOException $e) {
            // Fallback to legacy schema.
        }

        try {
            $stmt = $this->db->prepare('SELECT id_user AS id, email, date_inscription AS created_at FROM `USER` WHERE id_user = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $profile = $stmt->fetch();

            return $profile ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getProfile($id_user)
    {
        $stmt = $this->db->prepare(
            'SELECT id_user, nom, email, formation, campus, annee_etude, bio, avatar, role, date_inscription FROM `USER` WHERE id_user = :id'
        );
        $stmt->execute([':id' => $id_user]);
        return $stmt->fetch();
    }

    public function updateProfile($id_user, $bio, $contact)
    {
        $stmt = $this->db->prepare('UPDATE `USER` SET bio = :bio, contact = :contact WHERE id_user = :id');
        return $stmt->execute([
            ':bio' => $bio,
            ':contact' => $contact,
            ':id' => $id_user,
        ]);
    }

    public function addSkill($id_user, $id_skill, $niveau)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO USER_SKILL (id_user, id_skill, niveau) VALUES (:user, :skill, :niveau)');
            return $stmt->execute([
                ':user' => $id_user,
                ':skill' => $id_skill,
                ':niveau' => $niveau,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function searchUserByName($searchQuery)
    {
        $stmt = $this->db->prepare('SELECT id_user, nom, campus, formation, avatar FROM `USER` WHERE nom LIKE :search');
        $stmt->execute([':search' => '%' . $searchQuery . '%']);
        return $stmt->fetchAll();
    }

    public function getFriends($id_user, $limit = 20)
    {
        $stmt = $this->db->prepare(
            'SELECT id_user, nom, campus, formation, annee_etude, avatar
             FROM `USER`
             WHERE id_user <> :id_user
             ORDER BY date_inscription DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':id_user', (int) $id_user, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBasicUserById(int $id_user): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id_user, nom, formation, annee_etude, avatar
             FROM `USER`
             WHERE id_user = :id_user
             LIMIT 1'
        );
        $stmt->execute([':id_user' => $id_user]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function sendFriendRequest($id_sender, $id_receiver)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO FRIEND_REQUEST (id_sender, id_receiver) VALUES (:sender, :receiver)');
            return $stmt->execute([
                ':sender' => $id_sender,
                ':receiver' => $id_receiver,
            ]);
        } catch (PDOException $e) {
            return false;
        }
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
