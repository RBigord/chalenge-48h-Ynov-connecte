<?php
// Fichier : api/api_logout.php
session_start();

$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

// On vide les variables de session et on détruit la session
session_unset();
session_destroy();

http_response_code(200);
echo json_encode(['status' => 'success', 'message' => 'Déconnexion réussie.']);
?>