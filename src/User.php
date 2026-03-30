<?php
// Fichier : User.php

class User {
    private $db;

    public function __construct(PDO $databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * RÉCUPÉRER LE PROFIL COMPLET
     */
    public function getProfile($id_user) {
        $stmt = $this->db->prepare("
            SELECT id_user, nom, email, formation, campus, annee_etude, bio, avatar, role, date_inscription 
            FROM USER WHERE id_user = :id
        ");
        $stmt->execute([':id' => $id_user]);
        return $stmt->fetch();
    }

    /**
     * METTRE À JOUR LE PROFIL (Bio, Avatar, etc.)
     */
    public function updateProfile($id_user, $bio, $contact) {
        $stmt = $this->db->prepare("
            UPDATE USER SET bio = :bio, contact = :contact WHERE id_user = :id
        ");
        return $stmt->execute([
            ':bio' => $bio,
            ':contact' => $contact,
            ':id' => $id_user
        ]);
    }

    /**
     * AJOUTER UNE COMPÉTENCE 
     */
    public function addSkill($id_user, $id_skill, $niveau) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO USER_SKILL (id_user, id_skill, niveau) 
                VALUES (:user, :skill, :niveau)
            ");
            return $stmt->execute([
                ':user' => $id_user,
                ':skill' => $id_skill,
                ':niveau' => $niveau
            ]);
        } catch (PDOException $e) {
            return false; // Déjà ajouté
        }
    }

    /**
     * RECHERCHE D'UN UTILISATEUR
     */
    public function searchUserByName($searchQuery) {
        $stmt = $this->db->prepare("
            SELECT id_user, nom, campus, formation, avatar 
            FROM USER WHERE nom LIKE :search
        ");
        $stmt->execute([':search' => '%' . $searchQuery . '%']);
        return $stmt->fetchAll();
    }

    /**
     * ENVOYER UNE DEMANDE D'AMI
     */
    public function sendFriendRequest($id_sender, $id_receiver) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO FRIEND_REQUEST (id_sender, id_receiver) 
                VALUES (:sender, :receiver)
            ");
            return $stmt->execute([
                ':sender' => $id_sender,
                ':receiver' => $id_receiver
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>