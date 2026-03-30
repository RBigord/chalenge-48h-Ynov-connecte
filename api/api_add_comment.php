<?php
// Fichier : api/api_add_comment.php
session_start();

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit();
}

require_once '../config/Database.php';
require_once '../src/Comment.php';

$donnees = json_decode(file_get_contents("php://input"), true);

if (empty($donnees['id_post']) || empty(trim($donnees['contenu']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Données incomplètes.']);
    exit();
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
?>