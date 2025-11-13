<?php
require_once(__DIR__ . '/../config/Database.php');

class Like
{
    private $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }


    public function uplike($data)
    {
        try {

            $id = $data['id'];
            $email = $data['email'];
            $like = $data['like'];


            $query = "INSERT INTO votes_story (id_story, email, like_vote) VALUES (:id,:email,:like);";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":like", $like);
            $stmt->execute();

            return true;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }

    public function loadLike()
    {
        try {

            $query = "SELECT id_story, email, like_vote FROM votes_story;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $result = $stmt->get_result();

            $like = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return  $like;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }
}
