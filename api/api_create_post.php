<?php
// Fichier : api_create_post.php

session_start();

// HEADERS CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit();
}

require_once '../config/Database.php';
require_once '../src/Post.php';

// ATTENTION : Le Frontend envoie du FormData, donc on utilise $_POST et pas php://input !
if (empty($_POST['contenu'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Le contenu du post est vide.']);
    exit();
}

$id_user = $_SESSION['id_user'];
$contenu = htmlspecialchars(trim($_POST['contenu'])); // Sécurité XSS
$cheminImage = null;

// GESTION DE L'IMAGE (Si le Frontend a envoyé un fichier)
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileSize = $_FILES['image']['size'];
    
    // Limite à 5Mo
    if ($fileSize > (5 * 1024 * 1024)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Image trop lourde (Max 5Mo).']);
        exit();
    }

    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = uniqid('post_', true) . '.' . $fileExtension;
        $destPath = 'uploads/posts/' . $newFileName;
        
        if(move_uploaded_file($fileTmpPath, $destPath)) {
            $cheminImage = $destPath; 
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la sauvegarde de l\'image.']);
            exit();
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Format non autorisé (JPG, PNG, GIF).']);
        exit();
    }
}

try {
    $pdo = Database::getInstance();
    $postManager = new Post($pdo);
    
    // On sauvegarde en base de données
    $postManager->createPost($id_user, $contenu, $cheminImage);

    http_response_code(201);
    echo json_encode(['status' => 'success', 'message' => 'Post publié avec succès !']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
}
?>