<?php
// Fichier : api/api_toggle_like.php
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
require_once '../src/Post.php';

$donnees = json_decode(file_get_contents("php://input"), true);

if (empty($donnees['id_post'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID du post manquant.']);
    exit();
}

try {
    $pdo = Database::getInstance();
    $postManager = new Post($pdo);
    
    // On ajoute ou on enlève le like
    $postManager->toggleLike($_SESSION['id_user'], $donnees['id_post']);
    
    // On récupère le nouveau compte de likes
    $nouveauTotal = $postManager->getLikesCount($donnees['id_post']);

    echo json_encode(['status' => 'success', 'nouveau_total' => $nouveauTotal]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
}
?>