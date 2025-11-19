<?php
require_once(__DIR__ . '/../config/Database.php');

class Publicaciones
{

    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function nuevoPost($post)
    {
        $title = $post['title'];
        $descr = $post['description'];
        $email = $post['email'];


        try {
            $query = "INSERT INTO publicacion (title_story, descr_story,email) VALUES (:title, :description, :email);";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $descr);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $postId = $this->conn->lastInsertId();

            return $postId;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return  $data;
        }
    }

    public function actualizarPost($post)
    {
        $title = $post['title'];
        $descr = $post['description'];
        $email = $post['email'];
        $id = $post['idstory'];

        try {
            $query = "UPDATE publicacion set title_story = :title, descr_story = :description WHERE id_story = :id ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":description", $descr);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $postId = $this->conn->lastInsertId();

            return $postId;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return  $data;
        }
    }

    public function loadPost()
    {
        try {
            $query = "SELECT 
                    publicacion.id_story, 
                    title_story, 
                    descr_story, 
                    creation_date,
                    publicacion.email, 
                    file_path 
                  FROM publicacion 
                  INNER JOIN image_story ON publicacion.id_story = image_story.id_story
                  ORDER BY publicacion.creation_date DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);;

            return $posts;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return  $data;
        }
    }


    public function loadImage($data)
    {
        $image = $data['image'];
        $email = $data['email'];
        $idphoto = $data['idphoto'];

        try {
            $query = "INSERT INTO image_story (id_story, email,file_path) VALUES (:idphoto,:email,:image); ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":idphoto", $idphoto);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            return true;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return  $data;
        }
    
    }

    public function updateImage($data)
    {
        $image = $data['image'];
        $idphoto = $data['idphoto'];

        
        try {
            $query = "UPDATE image_story set file_path = :image WHERE id_story = :idphoto";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":idphoto", $idphoto);
            $stmt->execute();
            return true;
        } catch (Error $error) {
            $data = ["error" => $error->getMessage()];
            return  $data;
        }
    }
}
