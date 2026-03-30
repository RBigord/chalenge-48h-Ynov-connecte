<?php
// Fichier : api_get_profile.php

// 1. Démarrage de la session (Indispensable pour savoir qui fait la requête)
session_start();

// 2. LES HEADERS MAGIQUES (CORS) - Toujours les mêmes pour autoriser le Frontend
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true"); // TRÈS IMPORTANT pour lire le cookie de session !
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: GET, OPTIONS"); // Ici c'est un GET, on ne modifie rien
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. Sécurité absolue : On vérifie si le serveur se souvient de cet utilisateur
if (!isset($_SESSION['id_user'])) {
    http_response_code(401); // 401 = Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté ou session expirée.']);
    exit();
}

// 4. On inclut nos classes
require_once '../config/Database.php';
require_once '../src/User.php';

try {
    // 5. On récupère l'ID directement depuis la mémoire sécurisée du serveur (la session)
    $monId = $_SESSION['id_user'];

    $pdo = Database::getInstance();
    $userManager = new User($pdo);

    // 6. On utilise notre méthode POO pour récupérer toutes les infos du profil
    $monProfil = $userManager->getProfile($monId);

    if ($monProfil) {
        // On enlève le mot de passe du tableau avant de l'envoyer au Frontend (Sécurité !)
        unset($monProfil['password']);

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'data' => $monProfil // Contient le nom, email, bio, avatar, formation, etc.
        ]);
    } else {
        http_response_code(404); // 404 = Not Found
        echo json_encode(['status' => 'error', 'message' => 'Profil introuvable.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur.']);
}
?>