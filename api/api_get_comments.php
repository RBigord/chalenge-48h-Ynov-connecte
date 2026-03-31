<?php
// Fichier : api/api_get_comments.php
session_start();

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit();
}

require_once '../config/Database.php';
require_once '../src/Comment.php';

if (empty($_GET['id_post'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'ID du post manquant dans l\'URL.']);
    exit();
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
?>