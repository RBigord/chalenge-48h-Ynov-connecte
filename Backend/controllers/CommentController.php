<?php
require_once __DIR__ . '/../../Backend/models/Comment.php';

class CommentController {
    private function checkAuth() {
        if (!isset($_SESSION['id_user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
            exit();
        }
    }

    public function getComments() {
        $this->checkAuth();
        if (empty($_GET['id_post'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID du post manquant dans l\'URL.']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $commentManager = new Comment($pdo);
            $lesCommentaires = $commentManager->getCommentsByPost($_GET['id_post']);

            echo json_encode(['status' => 'success', 'data' => $lesCommentaires]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
        }
    }

    public function addComment() {
        $this->checkAuth();
        $donnees = json_decode(file_get_contents("php://input"), true);

        if (empty($donnees['id_post']) || empty(trim($donnees['contenu']))) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Données incomplètes.']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $commentManager = new Comment($pdo);
            $commentManager->addComment($donnees['id_post'], $_SESSION['id_user'], htmlspecialchars(trim($donnees['contenu'])));

            http_response_code(201);
            echo json_encode(['status' => 'success', 'message' => 'Commentaire ajouté.']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
        }
    }
}
