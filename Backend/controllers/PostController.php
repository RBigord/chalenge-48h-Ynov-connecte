<?php
require_once __DIR__ . '/../../Backend/models/Post.php';

class PostController
{

    // Méthode privée pour vérifier la connexion
    private function checkAuth()
    {
        if (!isset($_SESSION['id_user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
            exit();
        }
    }

    public function getPosts()
    {
        $this->checkAuth();
        try {
            $pdo = Database::getInstance();
            $postManager = new Post($pdo);
            $lesPosts = $postManager->getFeed();

            http_response_code(200);
            echo json_encode(['status' => 'success', 'data' => $lesPosts]);
        }
        catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
        }
    }

    public function createPost()
    {
        $this->checkAuth();

        // Form Data est reçu dans $_POST et $_FILES
        if (empty($_POST['contenu'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Le contenu du post est vide.']);
            return;
        }

        $id_user = $_SESSION['id_user'];
        $contenu = htmlspecialchars(trim($_POST['contenu']));
        $cheminImage = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];

            if ($fileSize > (5 * 1024 * 1024)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Image trop lourde (Max 5Mo).']);
                return;
            }

            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = uniqid('post_', true) . '.' . $fileExtension;
                $destDirPath = __DIR__ . '/../../uploads/posts';
                $destPath = $destDirPath . '/' . $newFileName;
                $publicPath = 'uploads/posts/' . $newFileName;

                if (!is_dir($destDirPath)) {
                    mkdir($destDirPath, 0777, true);
                }

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $cheminImage = $publicPath;
                }
                else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la sauvegarde de l\'image.']);
                    return;
                }
            }
            else {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Format non autorisé (JPG, PNG, GIF).']);
                return;
            }
        }

        try {
            $pdo = Database::getInstance();
            $postManager = new Post($pdo);
            $postManager->createPost($id_user, $contenu, $cheminImage);

            http_response_code(201);
            echo json_encode(['status' => 'success', 'message' => 'Post publié avec succès !']);
        }
        catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
        }
    }

    public function toggleLike()
    {
        $this->checkAuth();
        $donnees = json_decode(file_get_contents("php://input"), true);

        if (empty($donnees['id_post'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID du post manquant.']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $postManager = new Post($pdo);
            $postManager->toggleLike($_SESSION['id_user'], $donnees['id_post']);
            $nouveauTotal = $postManager->getLikesCount($donnees['id_post']);

            echo json_encode(['status' => 'success', 'nouveau_total' => $nouveauTotal]);
        }
        catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
        }
    }
}
