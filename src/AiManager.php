<?php

class AiManager
{
    // Ta clé API Google Gemini
    private $apiKey = "AIzaSyBgyRXv7Cl-gocO-87fBjaDYUWHg93oNtU"; 

    // URL du modèle Gemini sans la clé (ajoutée au moment de l'appel)
    private $apiUrl = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent";

    /** cette fonction envoie le texte à l'IA et retourne la version améliorée.C'est le cœur de l'intégration novatrice demandée[cite: 9, 44].*/
public function improvePost($content){// 1. On prépare la consigne (le prompt)$prompt = "Réécris ce message pour un réseau social étudiant de manière plus professionnelle et attractive, tout en restant court : " . $content;

        // 2. Structure de données JSON pour Google Gemini
        $data = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];
        // 3. Configuration de l'appel API via cURL
        $ch = curl_init($this->apiUrl . "?key=" . $this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    // 4. Exécution de l'appel
        $response = curl_exec($ch);

        // Vérification si la connexion a échoué
        if (curl_errno($ch)) {
            return "Erreur de connexion : " . curl_error($ch);
        }

        curl_close($ch);

        // 5. Décodage de la réponse
        $json = json_decode($response, true);

        // Si l'IA a répondu avec succès, on extrait le texte
        if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            return $json['candidates'][0]['content']['parts'][0]['text'];
        }

        // Sinon, on affiche la réponse brute pour comprendre ce qui bloque
        return "L'IA n'a pas pu répondre. Détails : " . $response;
    }
}
﻿
