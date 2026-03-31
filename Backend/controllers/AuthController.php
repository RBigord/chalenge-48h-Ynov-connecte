<?php

require_once __DIR__ . '/../../Backend/models/Auth.php';

class AuthController {
    
    public function login() {
        $donneesFrontend = json_decode(file_get_contents("php://input"), true);

        if (empty($donneesFrontend['email']) || empty($donneesFrontend['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Email et mot de passe requis.']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $auth = new Auth($pdo);
            $result = $auth->login($donneesFrontend['email'], $donneesFrontend['password']);

            if ($result['success']) {
                http_response_code(200);
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Connexion réussie !',
                    'user' => [
                        'id' => $_SESSION['id_user'] ?? null,
                        'nom' => $_SESSION['nom'] ?? null,
                        'role' => $_SESSION['role'] ?? null
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(['status' => 'error', 'message' => $result['message']]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur interne du serveur.']);
        }
    }

    public function register() {
        $donneesFrontend = json_decode(file_get_contents("php://input"), true);

        if (empty($donneesFrontend['nom']) || empty($donneesFrontend['email']) || empty($donneesFrontend['password'])) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs obligatoires.']);
            return;
        }

        try {
            $pdo = Database::getInstance();
            $auth = new Auth($pdo);

            $inscriptionReussie = $auth->register(
                $donneesFrontend['nom'], 
                $donneesFrontend['email'], 
                $donneesFrontend['password'], 
                $donneesFrontend['formation'] ?? 'Informatique',
                $donneesFrontend['campus'] ?? 'Paris',
                $donneesFrontend['annee_etude'] ?? 'B1'
            );

            if ($inscriptionReussie) {
                http_response_code(201);
                echo json_encode(['status' => 'success', 'message' => 'Compte créé avec succès !']);
            } else {
                http_response_code(409);
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'inscription ou email déjà utilisé (le mot de passe doit contenir 8 chars, maj/min, chiffre, spécial).']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage() . ' dans ' . $e->getFile() . ':' . $e->getLine()]);
        }
    }

    public function logout() {
        Auth::logout();
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Déconnexion réussie.']);
    }
}
