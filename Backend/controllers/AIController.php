<?php

require_once __DIR__ . '/../../Backend/models/Auth.php';
require_once __DIR__ . '/../../Backend/models/ia_logic.php';

class AiController {
    
    public function resumeNews() {
        if (!isset($_SESSION['id_user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
            exit();
        }

        $donneesFrontend = json_decode(file_get_contents("php://input"), true);
        $texteNews = trim($donneesFrontend['texte_news'] ?? '');
        
        if ($texteNews === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Veuillez saisir une actualité à résumer.']);
            return;
        }

        try {
            $ia = new InnovationIA(null); // Key setup inside ia_logic.php or via environment
            $iaResult = $ia->resumerNews($texteNews);

            echo json_encode(['status' => 'success', 'data' => $iaResult]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur IA: ' . $e->getMessage()]);
        }
    }

    public function aiJob() {
        if (!isset($_SESSION['id_user'])) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Non autorisé']);
            exit();
        }

        $donneesFrontend = json_decode(file_get_contents("php://input"), true);
        $competences = trim($donneesFrontend['competences'] ?? '');
        
        if ($competences === '') {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Veuillez saisir vos compétences.']);
            return;
        }

        try {
            $ia = new InnovationIA();
            $iaResult = $ia->aideCandidature($competences);

            echo json_encode(['status' => 'success', 'data' => $iaResult]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur IA: ' . $e->getMessage()]);
        }
    }
}
