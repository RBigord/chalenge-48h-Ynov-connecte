<?php
require_once __DIR__ . '/../../Backend/models/Message.php';

class MessageController {
    private function checkAuth() {
        if (!isset($_SESSION['id_user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
            exit();
        }
    }

    public function getMessages() {
        $this->checkAuth();
        if (!isset($_GET['id_contact'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID du contact manquant']);
            return;
        }

        $monId = (int) $_SESSION['id_user'];
        $contactId = intval($_GET['id_contact']);

        if ($contactId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID du contact invalide']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $messageManager = new Message($pdo);
            $conversation = $messageManager->getConversation($monId, $contactId);
            
            echo json_encode(['status' => 'success', 'data' => $conversation]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
        }
    }

    public function getContacts() {
        $this->checkAuth();

        try {
            $pdo = Database::getInstance();
            $messageManager = new Message($pdo);
            $contacts = $messageManager->getContacts((int) $_SESSION['id_user']);

            echo json_encode(['status' => 'success', 'data' => $contacts]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
        }
    }

    public function sendMessage() {
        $this->checkAuth();
        $id_sender = (int) $_SESSION['id_user'];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $isMultipart = stripos($contentType, 'multipart/form-data') !== false;

        $id_receiver = 0;
        $contenu = '';
        $fichierPath = null;

        if ($isMultipart) {
            $id_receiver = (int) ($_POST['id_receiver'] ?? 0);
            $contenu = htmlspecialchars(trim((string) ($_POST['contenu'] ?? '')));

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileSize = (int) $_FILES['image']['size'];

                if ($fileSize > (5 * 1024 * 1024)) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Image trop lourde (max 5Mo).']);
                    return;
                }

                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                if (!in_array($extension, $allowed, true)) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => 'Format image non autorise.']);
                    return;
                }

                $destDir = __DIR__ . '/../../uploads/messages';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0777, true);
                }

                $newName = uniqid('msg_', true) . '.' . $extension;
                $destPath = $destDir . '/' . $newName;

                if (!move_uploaded_file($tmpPath, $destPath)) {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Impossible de sauvegarder l\'image.']);
                    return;
                }

                $fichierPath = 'uploads/messages/' . $newName;
            }
        } else {
            $donneesRecues = json_decode(file_get_contents('php://input'), true);
            $id_receiver = (int) ($donneesRecues['id_receiver'] ?? 0);
            $contenu = htmlspecialchars(trim((string) ($donneesRecues['contenu'] ?? '')));
        }

        if ($id_receiver <= 0 || ($contenu === '' && $fichierPath === null)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Données incomplètes (destinataire, contenu ou image requis)']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $messageManager = new Message($pdo);
            $success = $messageManager->sendMessage($id_sender, $id_receiver, $contenu, $fichierPath);
            
            if ($success) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Message envoyé avec succès !',
                    'data' => [
                        'id_receiver' => $id_receiver,
                        'contenu' => $contenu,
                        'fichiers' => $fichierPath,
                    ],
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Échec de l\'envoi du message']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
        }
    }
}
