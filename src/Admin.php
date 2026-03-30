<?php
// Fichier : Admin.php

// On inclusion du fichier parent pour pouvoir en hériter
require_once 'User.php'; 

// 1. L'HÉRITAGE (extends) : Admin possède  toutes les méthodes de User
class Admin extends User {

    // Le constructeur appelle celui du parent (User) pour configurer la base de données
    public function __construct(PDO $databaseConnection) {
        parent::__construct($databaseConnection); 
    }

    /**
     * 2. LE POLYMORPHISME : On redéfinit une méthode existante
     * Un Admin a un profil différent d'un étudiant classique.
     */
    public function getProfile($id_user) {
        // On récupère d'abord les infos de base grâce à la méthode du parent
        $profil = parent::getProfile($id_user);
        
        // S'il existe, on ajoute les spécificités de l'Admin !
        if ($profil) {
            $profil['badge_special'] = "👑 Administrateur ";
            $profil['permissions'] = "Publication de News Ynov";
        }
        
        return $profil;
    }

    /**
     * MÉTHODE EXCLUSIVE : Seul l'Admin peut faire ça
     * Publier une information dans la section "News d'Ynov"
     */
    public function publishNews($id_user, $titre, $contenu, $fichiers = null) {
        $stmt = $this->db->prepare("
            INSERT INTO NEWS (titre, contenu, fichiers, id_user) 
            VALUES (:titre, :contenu, :fichiers, :id_user)
        ");
        
        return $stmt->execute([
            ':titre' => $titre,
            ':contenu' => $contenu,
            ':fichiers' => $fichiers,
            ':id_user' => $id_user
        ]);
    }
}
?>