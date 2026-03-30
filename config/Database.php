<?php

class Database {
    // Variable statique qui va stocker l'unique instance de notre connexion
    private static $instance = null;
    
    // Variable qui contient l'objet PDO
    private $pdo;

    // Le constructeur est privé ! On ne peut pas faire "new Database()" depuis l'extérieur.
    // C'est le principe du Singleton.
    private function __construct() {
        $host = 'localhost';
        $dbname = 'ynov_network';
        $user = 'root'; 
        $password = ''; 

        try {
            // Création de la connexion PDO
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
            
            // SÉCURITÉ : On force PDO à afficher des erreurs claires s'il y a un problème SQL
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // PRATIQUE : On demande à PDO de toujours nous renvoyer les données sous forme de tableau associatif
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            // S'il y a une erreur de connexion, on arrête tout et on affiche le message
            die("Erreur critique de connexion à la base de données : " . $e->getMessage());
        }
    }

    // La méthode magique du Singleton : elle crée la connexion si elle n'existe pas, 
    // ou la réutilise si elle est déjà ouverte.
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        // On retourne l'objet PDO contenu dans l'instance
        return self::$instance->pdo;
    }
}
?>