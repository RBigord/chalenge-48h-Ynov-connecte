<?php
// Fichier : api_login.php

// 1. On démarre la session PHP AVANT d'envoyer la moindre donnée
session_start();

// 2. LES HEADERS MAGIQUES (CORS) - Indispensables pour le Frontend !
// ⚠️ Attention : En mode API avec des sessions, 'Access-Control-Allow-Origin' ne peut pas être '*'
// Il faut idéalement mettre l'URL exacte du Frontend (ex: 'http://localhost:3000' ou 'http://127.0.0.1:5500')
// Pour le test local, on peut récupérer l'origine automatiquement :
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin"); 
header("Access-Control-Allow-Credentials: true"); // TRÈS IMPORTANT : Autorise l'envoi du cookie de session !
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Gestion de la requête de pré-vérification (Preflight OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 3. On inclut nos classes
require_once '../config/Database.php';
require_once '../src/Auth.php';

// 4. On lit le JSON envoyé par le Frontend
$donneesFrontend = json_decode(file_get_contents("php://input"), true);

// 5. Vérification des données entrantes
if (empty($donneesFrontend['email']) || empty($donneesFrontend['password'])) {
    http_response_code(400); // 400 = Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Email et mot de passe requis.']);
    exit();
}

try {
    $pdo = Database::getInstance();
    $auth = new Auth($pdo);

    // 6. On tente la connexion avec notre méthode login()
    $connexionReussie = $auth->login($donneesFrontend['email'], $donneesFrontend['password']);

    // 7. On répond au Frontend en fonction du résultat
    if ($connexionReussie) {
        http_response_code(200); // 200 = OK
        echo json_encode([
            'status' => 'success', 
            'message' => 'Connexion réussie !',
            'user' => [
                'id' => $_SESSION['id_user'],
                'nom' => $_SESSION['nom'],
                'role' => $_SESSION['role']
            ]
        ]);
    } else {
        http_response_code(401); // 401 = Unauthorized (Mauvais identifiants)
        echo json_encode(['status' => 'error', 'message' => 'Email ou mot de passe incorrect.']);
    }

} catch (Exception $e) {
    http_response_code(500); // 500 = Erreur Serveur
    echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur.']);
}
?>