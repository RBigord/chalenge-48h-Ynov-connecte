<?php
// Fichier : api_register.php

// 1. LES HEADERS MAGIQUES (Pour autoriser le Frontend à te parler)
header('Access-Control-Allow-Origin: *'); // Autorise n'importe quel frontend à se connecter (parfait pour le dev)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Si le navigateur fait une requête de vérification (OPTIONS), on arrête là avec un succès
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. On inclut nos classes
require_once '../config/Database.php';
require_once '../src/Auth.php';

// 3. On récupère les données envoyées par le Frontend en JSON
// (En API, on n'utilise plus $_POST, on lit le corps de la requête !)
$donneesFrontend = json_decode(file_get_contents("php://input"), true);

// 4. On vérifie que les données sont bien là
if (
    empty($donneesFrontend['nom']) || 
    empty($donneesFrontend['email']) || 
    empty($donneesFrontend['password'])
) {
    http_response_code(400); // 400 = Mauvaise requête
    echo json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs obligatoires.']);
    exit();
}

try {
    // 5. On instancie nos objets
    $pdo = Database::getInstance();
    $auth = new Auth($pdo);

    // 6. On exécute la logique métier
    $inscriptionReussie = $auth->register(
        $donneesFrontend['nom'], 
        $donneesFrontend['email'], 
        $donneesFrontend['password'], 
        $donneesFrontend['formation'] ?? 'Informatique', // Valeur par défaut si non fourni
        $donneesFrontend['campus'] ?? 'Paris',
        $donneesFrontend['annee_etude'] ?? 'B1'
    );

    // 7. On renvoie la réponse au Frontend UNIQUEMENT en JSON !
    if ($inscriptionReussie) {
        http_response_code(201); // 201 = Créé avec succès
        echo json_encode(['status' => 'success', 'message' => 'Compte créé avec succès !']);
    } else {
        http_response_code(409); // 409 = Conflit (ex: email déjà pris)
        echo json_encode(['status' => 'error', 'message' => 'Cet email est déjà utilisé.']);
    }

} catch (Exception $e) {
    http_response_code(500); // 500 = Erreur de ton côté (serveur)
    echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur.']);
}
?>