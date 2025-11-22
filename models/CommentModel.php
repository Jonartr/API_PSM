<?php

require_once(__DIR__ . '/../config/Database.php');

class Comentario
{

    private $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }


    public function insertarComentario($data)
    {
        try {

            $id = $data['id'];
            $email = $data['email'];
            $comment = $data['comment'];


            $query = "INSERT INTO comments_story (id_story, email, text_comment) VALUES (:id,:email,:comment);";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":comment", $comment);
            $stmt->execute();

            return true;
        } catch (Error $error) {
             $data = ["error" => $error->getMessage()];
            return  $data;
        }
    }


    public function loadComments(){
        try {
            $query = "SELECT c.id_comment, c.id_story, c.email, c.text_comment as comment,
             c.date_comment, u.image_avatar, u.alias FROM comments_story c INNER JOIN usuarios u on c.email = u.email WHERE c.active = 1;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $comments =  $stmt->fetchAll(PDO::FETCH_ASSOC);;

                return $comments;

        } catch (Error $error) {
             $data = ["error" => $error->getMessage()];
            return  $data;
        }
    }
}
