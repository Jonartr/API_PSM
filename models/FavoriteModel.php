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
            $stmt->bind_param(":id", $id);
            $stmt->bind_param(":email", $email);
            $stmt->execute();

            return true;
        } catch (Error $error) {
            return false;
        }
    }

    public function loadFavorite()
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
                  WHERE favorites.email = :email
                  ORDER BY publicacion.creation_date DESC";

            $stmt = $this->db->prepare($query);
             $stmt->bind_param(":id", $query);
            $stmt->execute();

            $result = $stmt->get_result();

            $favorites = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $favorites[] = $row;
                }


                return $favorites;
            } else {

                return [];
            }
        } catch (Error $error) {
            return [];
        }
    }
}
