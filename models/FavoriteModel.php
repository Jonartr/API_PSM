<?php

require_once(__DIR__ . '/../config/Database.php');

class Favorito
{

    private $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function addFavorite($data)
    {
        try {

            $id = $data['id'];
            $email = $data['email'];


            $query = "INSERT INTO favorites (id_story, email) VALUES (:id,:email);";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            return true;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }

    public function loadFavorite($email)
    {

        try {
            $query = "SELECT 
                    publicacion.id_story as 'idfavorito', 
                    title_story, 
                    descr_story, 
                    creation_date,
                    file_path,
                    publicacion.email as 'publicador',
                    favorites.email as 'favorito'
                  FROM publicacion 
                  INNER JOIN image_story ON publicacion.id_story = image_story.id_story
                  INNER JOIN favorites ON publicacion.id_story = favorites.id_story
                  ORDER BY publicacion.creation_date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute();


            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

             return $favorites;
   
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }
}
