<?php
// Fichier : Auth.php

class Auth {
    private $db;

    public function __construct(PDO $databaseConnection) {
        $this->db = $databaseConnection;
        // On s'assure que la session est démarrée pour stocker les infos de connexion
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * INSCRIPTION
     */
    public function register($nom, $email, $password, $formation, $campus, $annee_etude) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("
                INSERT INTO USER (nom, email, password, formation, campus, annee_etude) 
                VALUES (:nom, :email, :password, :formation, :campus, :annee_etude)
            ");

            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':formation' => $formation,
                ':campus' => $campus,
                ':annee_etude' => $annee_etude
            ]);

            return true;
        } catch (PDOException $e) {
            return false; // Échec (ex: l'email existe déjà)
        }
    }

    /**
     * CONNEXION
     */
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM USER WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        // Vérification du mot de passe
        if ($user && password_verify($password, $user['password'])) {
            // On stocke les infos essentielles dans la session
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
        return false;
    }

    /**
     * DÉCONNEXION
     */
    public function logout() {
        session_unset();
        session_destroy();
    }

    /**
     * VÉRIFICATION SI CONNECTÉ
     */
    public function isLoggedIn() {
        return isset($_SESSION['id_user']);
    }
}
?>