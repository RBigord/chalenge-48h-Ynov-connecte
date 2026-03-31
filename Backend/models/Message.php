<?php
class Message
{
    protected PDO $db;

    public function __construct(PDO $databaseConnection)
    {
        $this->db = $databaseConnection;
    }

    /**
     * 1. ENVOYER UN MESSAGE
     */
    public function sendMessage($id_sender, $id_receiver, $contenu, $fichiers = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO `MESSAGE` (id_sender, id_receiver, contenu, fichiers) 
            VALUES (:sender, :receiver, :contenu, :fichiers)
        ");
        
        return $stmt->execute([
            ':sender' => $id_sender,
            ':receiver' => $id_receiver,
            ':contenu' => $contenu,
            ':fichiers' => $fichiers
        ]);
    }

    /**
     * 2. RÉCUPÉRER UNE CONVERSATION (Entre 2 étudiants)
     * La requête SQL vérifie les messages envoyés dans les DEUX sens !
     */
    public function getConversation($id_user1, $id_user2)
    {
        $stmt = $this->db->prepare("
            SELECT M.id_message, M.contenu, M.date_message, M.fichiers, M.id_sender,
                   U.nom AS sender_nom, U.avatar AS sender_avatar
            FROM `MESSAGE` M
            INNER JOIN `USER` U ON M.id_sender = U.id_user
            WHERE (M.id_sender = :u1 AND M.id_receiver = :u2) 
               OR (M.id_sender = :u2 AND M.id_receiver = :u1)
            ORDER BY M.date_message ASC -- Du plus ancien au plus récent (pour l'affichage type chat)
        ");
        
        $stmt->execute([
            ':u1' => $id_user1,
            ':u2' => $id_user2
        ]);
        
        return $stmt->fetchAll();
    }

    public function getContacts($id_user)
    {
        $stmt = $this->db->prepare(" 
            SELECT
                U.id_user,
                U.nom,
                U.avatar,
                M.contenu AS last_message,
                M.date_message AS last_message_at
            FROM `USER` U
            INNER JOIN (
                SELECT
                    CASE WHEN id_sender = :uid1 THEN id_receiver ELSE id_sender END AS contact_id,
                    MAX(id_message) AS last_message_id
                FROM `MESSAGE`
                WHERE id_sender = :uid2 OR id_receiver = :uid3
                GROUP BY CASE WHEN id_sender = :uid4 THEN id_receiver ELSE id_sender END
            ) C ON C.contact_id = U.id_user
            INNER JOIN `MESSAGE` M ON M.id_message = C.last_message_id
            ORDER BY M.date_message DESC
        ");

        $stmt->execute([
            ':uid1' => $id_user,
            ':uid2' => $id_user,
            ':uid3' => $id_user,
            ':uid4' => $id_user,
        ]);

        return $stmt->fetchAll();
    }
}
