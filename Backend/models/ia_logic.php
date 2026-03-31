<?php

// CLASSE D'INTERACTION AVEC L'API GOOGLE GEMINI
class InnovationIA {
    private $apiKey;
    private $baseUrl = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent";

    public function __construct($key = null) {
        $this->apiKey = $key ?: $this->loadApiKey();
    }

    private function loadApiKey() {
        // Essayer la variable d'environnement d'abord
        $fromEnv = getenv('GOOGLE_API_KEY');
        if ($fromEnv !== false && trim($fromEnv) !== '') {
            return trim($fromEnv);
        }

        // Chercher dans les fichiers .env
        $candidates = [
            __DIR__ . '/../.env',
            __DIR__ . '/../../.env',
        ];

        foreach ($candidates as $path) {
            if (!is_file($path)) {
                continue;
            }

            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                continue;
            }

            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                    continue;
                }

                list($name, $value) = explode('=', $line, 2);
                if (trim($name) === 'GOOGLE_API_KEY') {
                    return trim($value, " \t\n\r\0\x0B\"'");
                }
            }
        }

        return null;
    }

    // FONCTIONNALITÉ 1 : Résumer les News Ynov
    public function resumerNews($texteLong) {
        $prompt = "Tu es l'assistant du campus Ynov. Résume cette actualité en 2 phrases simples et accrocheuses pour les étudiants : " . $texteLong;
        return $this->appelAPI($prompt);
    }

    // FONCTIONNALITÉ 2 : Aide au Job Board 
    public function aideCandidature($competences) {
        $prompt = "Tu es un coach carrière à Ynov. En fonction de ces compétences : $competences, suggère 3 types de stages précis et donne un conseil pour postuler.";
        return $this->appelAPI($prompt);
    }

    // LE MOTEUR D'APPEL À L'API GEMINI
    private function appelAPI($prompt) {
        if (empty($this->apiKey)) {
            return "Erreur : la clé API GOOGLE_API_KEY est manquante dans le fichier .env";
        }

        $url = $this->baseUrl . "?key=" . urlencode($this->apiKey);
        
        $postData = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "maxOutputTokens" => 1024,
            ]
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_TIMEOUT => 30
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Erreur API Gemini ($httpCode): $response");
            return "L'IA est indisponible pour le moment. Code erreur: $httpCode";
        }

        $decoded = json_decode($response, true);
        
        // Extraire le texte généré de la réponse Gemini
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            return $decoded['candidates'][0]['content']['parts'][0]['text'];
        }

        error_log("Réponse Gemini inattendue: " . json_encode($decoded));
        return "L'IA n'a pas pu générer une réponse valide.";
    }
}


