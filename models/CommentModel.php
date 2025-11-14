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
            $stmt->bind_param(":id", $id);
            $stmt->bind_param(":email", $email);
            $stmt->bind_param(":comment", $comment);
            $stmt->execute();

            return true;
        } catch (Error $error) {
            return false;
        }
    }


    public function loadComments(){
        try {
            $query = "SELECT id_comment, id_story, email, text_comment, date_comment FROM comments_story WHERE active = 1;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $comments =  $stmt->fetchAll(PDO::FETCH_ASSOC);;

                return $comments;

        } catch (Error $error) {
            return false;
        }
    }
}
