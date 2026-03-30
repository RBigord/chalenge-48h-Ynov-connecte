<?php
// Fichier : api_get_posts.php

session_start();

// HEADERS CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Sécurité : Réservé aux étudiants connectés
if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
    exit();
}

require_once '../config/Database.php';
require_once '../src/Post.php';

try {
    $pdo = Database::getInstance();
    $postManager = new Post($pdo);

    // On utilise la méthode de ta classe Post !
    $lesPosts = $postManager->getFeed();

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'data' => $lesPosts // Le tableau avec tous les posts, les auteurs, les dates...
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur.']);
}
?>