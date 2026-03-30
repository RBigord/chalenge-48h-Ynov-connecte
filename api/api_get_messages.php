<?php
// Fichier : api_get_messages.php

// 1. On démarre la session et on inclut nos classes
session_start();
require_once '../config/Database.php';
require_once '../src/Message.php';

// 2. OBLIGATOIRE POUR UNE API : On dit au navigateur qu'on renvoie du JSON, pas du HTML !
header('Content-Type: application/json; charset=utf-8');

// 3. Sécurité : Vérifier si l'utilisateur est bien connecté
if (!isset($_SESSION['id_user'])) {
    // On renvoie un code d'erreur HTTP 401 (Non autorisé) et un message JSON
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

// 4. Vérifier si l'ID du contact est bien fourni dans l'URL (?id_contact=...)
if (!isset($_GET['id_contact'])) {
    http_response_code(400); // 400 = Bad Request (Mauvaise requête)
    echo json_encode(['status' => 'error', 'message' => 'ID du contact manquant']);
    exit();
}

$monId = $_SESSION['id_user'];
$contactId = intval($_GET['id_contact']); // On s'assure que c'est bien un nombre entier

// 5. On utilise notre architecture POO pour récupérer les données
try {
    $pdo = Database::getInstance();
    $messageManager = new Message($pdo);
    
    $conversation = $messageManager->getConversation($monId, $contactId);
    
    // 6. On renvoie le succès et les données en JSON !
    echo json_encode([
        'status' => 'success',
        'data' => $conversation
    ]);

} catch (Exception $e) {
    // S'il y a un crash de la base de données, on renvoie une erreur propre 500
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}
?>