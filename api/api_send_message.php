<?php
// Fichier : api_send_message.php

session_start();
require_once '../config/Database.php';
require_once '../src/Message.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
    exit();
}

// Les API modernes reçoivent souvent les données sous forme de JSON brut ("php://input")
// On lit ce que le Frontend nous a envoyé et on le décode
$donneesRecues = json_decode(file_get_contents('php://input'), true);

if (!isset($donneesRecues['id_receiver']) || empty(trim($donneesRecues['contenu']))) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Données incomplètes (id_receiver ou contenu manquant)']);
    exit();
}

$id_sender = $_SESSION['id_user'];
$id_receiver = intval($donneesRecues['id_receiver']);
$contenu = htmlspecialchars(trim($donneesRecues['contenu'])); // Sécurité contre les failles XSS !

try {
    $pdo = Database::getInstance();
    $messageManager = new Message($pdo);
    
    // On sauvegarde le message en base de données
    $success = $messageManager->sendMessage($id_sender, $id_receiver, $contenu);
    
    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Message envoyé avec succès !']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Échec de l\'envoi du message']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur serveur']);
}
?>