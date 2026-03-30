<?php
// Fichier : Comment.php

class Comment {
    // On garde notre connexion protégée
    protected $db;

    public function __construct(PDO $databaseConnection) {
        $this->db = $databaseConnection;
    }

    /**
     * 1. AJOUTER UN COMMENTAIRE SOUS UN POST
     */
    public function addComment($id_post, $id_user, $contenu) {
        // La date se mettra toute seule grâce à notre DEFAULT CURRENT_TIMESTAMP en SQL !
        $stmt = $this->db->prepare("
            INSERT INTO COMMENT (id_post, id_user, contenu) 
            VALUES (:id_post, :id_user, :contenu)
        ");
        
        return $stmt->execute([
            ':id_post' => $id_post,
            ':id_user' => $id_user,
            ':contenu' => $contenu
        ]);
    }

    /**
     * 2. RÉCUPÉRER TOUS LES COMMENTAIRES D'UN POST
     * Utilisation d'un JOIN pour avoir le nom de celui qui commente.
     */
    public function getCommentsByPost($id_post) {
        // On trie par date ASC (Ascendant) pour afficher du plus vieux au plus récent (comme sur Discord ou Teams)
        $stmt = $this->db->prepare("
            SELECT C.id_comment, C.contenu, C.date_comment, 
                   U.id_user, U.nom AS auteur_nom, U.avatar AS auteur_avatar
            FROM COMMENT C
            INNER JOIN USER U ON C.id_user = U.id_user
            WHERE C.id_post = :id_post
            ORDER BY C.date_comment ASC 
        ");
        
        $stmt->execute([':id_post' => $id_post]);
        return $stmt->fetchAll();
    }

    /**
     * 3. COMPTER LE NOMBRE DE COMMENTAIRES (Optionnel mais super pour l'UX !)
     * Permet d'afficher "💬 3 commentaires" sous le post avant même de les ouvrir.
     */
    public function getCommentCount($id_post) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM COMMENT WHERE id_post = :id_post");
        $stmt->execute([':id_post' => $id_post]);
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>