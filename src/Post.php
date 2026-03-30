<?php
class Post
{
    private PDO $db;

    public function __construct(PDO $databaseConnection)
    {
        $this->db = $databaseConnection;
    }

    /**
     * 1. CRÉER UN POST (Avec ou sans image)
     */
    public function createPost($id_user, $contenu, $image = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO POST (contenu, image, id_user) 
            VALUES (:contenu, :image, :id_user)
        ");
        return $stmt->execute([
            ':contenu' => $contenu,
            ':image' => $image, // Peut être null si l'étudiant ne met pas d'image
            ':id_user' => $id_user
        ]);
    }

    /**
     * 2. RÉCUPÉRER LE FIL D'ACTUALITÉ (Feed)
     * Utilisation d'une jointure (JOIN) pour récupérer le nom de l'auteur !
     */
    public function getFeed()
    {
        $stmt = $this->db->prepare("
            SELECT POST.id_post, POST.contenu, POST.image, POST.date_post, 
                   USER.id_user, USER.nom AS auteur_nom, USER.avatar AS auteur_avatar
            FROM POST
            INNER JOIN USER ON POST.id_user = USER.id_user
            ORDER BY POST.date_post DESC -- Du plus récent au plus ancien
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * 3. GÉRER LES LIKES (Le système "Toggle")
     * Si l'utilisateur a déjà liké, on retire le like. Sinon, on l'ajoute.
     */
    public function toggleLike($id_user, $id_post)
    {
        // Étape A : On vérifie si le like existe déjà
        $checkStmt = $this->db->prepare("SELECT id_like FROM POST_LIKE WHERE id_user = :user AND id_post = :post");
        $checkStmt->execute([':user' => $id_user, ':post' => $id_post]);
        
        if ($checkStmt->rowCount() > 0) {
            // Le like existe -> On l'enlève (Dislike / Unlike)
            $delStmt = $this->db->prepare("DELETE FROM POST_LIKE WHERE id_user = :user AND id_post = :post");
            return $delStmt->execute([':user' => $id_user, ':post' => $id_post]);
        } else {
            // Le like n'existe pas -> On l'ajoute
            $insStmt = $this->db->prepare("INSERT INTO POST_LIKE (id_user, id_post) VALUES (:user, :post)");
            return $insStmt->execute([':user' => $id_user, ':post' => $id_post]);
        }
    }

    /**
     * 4. COMPTER LE NOMBRE DE LIKES D'UN POST
     */
    public function getLikesCount($id_post)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total_likes FROM POST_LIKE WHERE id_post = :post");
        $stmt->execute([':post' => $id_post]);
        $result = $stmt->fetch();
        return $result['total_likes'];
    }
}
