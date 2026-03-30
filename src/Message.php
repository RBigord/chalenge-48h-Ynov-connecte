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
            INSERT INTO MESSAGE (id_sender, id_receiver, contenu, fichiers) 
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
            FROM MESSAGE M
            INNER JOIN USER U ON M.id_sender = U.id_user
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
}
