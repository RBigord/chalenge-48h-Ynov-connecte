<?php
require_once __DIR__ . '/../../Backend/models/User.php';

class UserController {
    private function checkAuth() {
        if (!isset($_SESSION['id_user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté ou session expirée.']);
            exit();
        }
    }

    public function getProfile() {
        $this->checkAuth();
        try {
            $monId = $_SESSION['id_user'];
            $pdo = Database::getInstance();
            $userManager = new User($pdo);
            $monProfil = $userManager->getProfile($monId);

            if ($monProfil) {
                unset($monProfil['password']);
                http_response_code(200);
                echo json_encode(['status' => 'success', 'data' => $monProfil]);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Profil introuvable.']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur.']);
        }
    }

    public function getFriends() {
        $this->checkAuth();

        try {
            $pdo = Database::getInstance();
            $userManager = new User($pdo);
            $friends = $userManager->getFriends((int) $_SESSION['id_user']);

            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => $friends]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur.']);
        }
    }

    public function getUserById() {
        $this->checkAuth();

        $idUser = (int) ($_GET['id_user'] ?? 0);
        if ($idUser <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'id_user invalide.']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $userManager = new User($pdo);
            $user = $userManager->getBasicUserById($idUser);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Utilisateur introuvable.']);
                return;
            }

            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => $user]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur.']);
        }
    }
}
