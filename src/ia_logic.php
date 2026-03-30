<?php

$maCle = null;
$cheminEnv = __DIR__ . '/.env';

if (file_exists($cheminEnv)) {
    $lignes = file($cheminEnv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $ligne) {
        
        if (strpos(trim($ligne), '#') === 0) continue;
        
        
        if (strpos($ligne, '=') !== false) {
            list($nom, $valeur) = explode('=', $ligne, 2);
            if (trim($nom) === 'Ynov_Challenge_Key') {
                $maCle = trim($valeur);
            }
        }
    }
}

// CLASSE D'INTERACTION AVEC L'API
class InnovationIA {
    private $apiKey;
    private $baseUrl = "https://api.openai.com/v1/chat/completions";

    public function __construct($key) {
        $this->apiKey = $key;
    }

    // FONCTIONNALITÉ 1 : Résumer les News Ynov
    public function resumerNews($texteLong) {
        $prompt = "Tu es l'assistant du campus Ynov. Résume cette actualité en 2 phrases simples et accrocheuses pour les étudiants.";
        return $this->appelAPI($prompt, $texteLong);
    }

    // FONCTIONNALITÉ 2 : Aide au Job Board 
    public function aideCandidature($competences) {
        $prompt = "Tu es un coach carrière à Ynov. En fonction de ces compétences : $competences, suggère 3 types de stages précis et donne un conseil pour postuler.";
        return $this->appelAPI($prompt, "Aide-moi à trouver un stage adapté à mon profil.");
    }

    // LE MOTEUR D'APPEL À L'API 
    private function appelAPI($systemPrompt, $userContent) {
        if (empty($this->apiKey)) {
            return "Erreur : La clé API n'a pas été trouvée dans le fichier .env";
        }

        $ch = curl_init($this->baseUrl);
        
        $postData = [
            "model" => "gpt-3.5-turbo", 
            "messages" => [
                ["role" => "system", "content" => $systemPrompt],
                ["role" => "user", "content" => $userContent]
            ],
            "temperature" => 0.7
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->apiKey
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);
        
        // Retourne la réponse ou un message d'erreur si l'API OpenAI sature
        return $decoded['choices'][0]['message']['content'] ?? "L'IA est indisponible pour le moment.";
    }
}


$monIA = new InnovationIA($maCle);

