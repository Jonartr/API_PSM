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

            $exist = "SELECT id_story, email FROM favorites WHERE id_story = :id AND email = :email";
            $stmt = $this->db->prepare($exist);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $favorite = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$favorite) {
                $query = "INSERT INTO favorites (id_story, email) VALUES (:id,:email);";

                $stmt = $this->db->prepare($query);
                $stmt->bindParam(":id", $id);
                $stmt->bindParam(":email", $email);
                $stmt->execute();

                return true;
            }
            else{
                $data = ["message" => "Post ya esta en favoritos"];
                return $data;
            }
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }

    public function deleteFavorite($data){
          try {

            $id = $data['id'];
            $email = $data['email'];

            $exist = "DELETE FROM favorites WHERE idfavorito = :id AND email = :email";
            $stmt = $this->db->prepare($exist);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            return true;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }

    public function loadFavorite()
    {

        try {
            $query = "SELECT 
                    p.id_story as 'idfavorito', 
                    p.title_story, 
                    p.descr_story, 
                    p.creation_date,
                    u.image_avatar,
                    u.alias,
                    p.email as 'publicador',
                    f.email as 'favorito',
					f.date_agree,
                    GROUP_CONCAT(CONCAT(i.file_path)) as file_path
                FROM publicacion p
                INNER JOIN image_story i ON p.id_story = i.id_story
                INNER JOIN favorites f ON p.id_story = f.id_story
                INNER JOIN usuarios u ON f.email = u.email
                GROUP BY p.id_story, p.title_story, p.descr_story, p.creation_date, 
                        f.date_agree, p.email, f.email
                ORDER BY p.creation_date DESC;";

            $stmt = $this->db->prepare($query);
            $stmt->execute();


            $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($favorites as &$post) {
                if ($post['file_path']) {
                    $post['file_path'] = explode(',', $post['file_path']);
                    // Agregar URL base si es necesario
                    $post['file_path'] = array_map(function ($path) {
                        return "$path";
                    }, $post['file_path']);
                } else {
                    $post['file_path'] = [];
                }
            }


            return $favorites;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }
}
