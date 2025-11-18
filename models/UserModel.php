<?php
require_once(__DIR__ . '/../config/Database.php');

class Usuarios
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function loginUsuario($email, $password)
    {
        try {
            $query = "SELECT email, nam_e, last_name, alias, image_avatar FROM usuarios WHERE email = :email AND pass_word = :password";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);


            return $user;
        } catch (PDOException $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }

    public function nuevoUsuario($datos)
    {
        try {
            $email = $datos["email"] ?? '';
            $password = $datos["password"] ?? '';
            $name = $datos["name"] ?? '';
            $lastname = $datos["lastname"] ?? '';
            $alias = $datos["alias"] ?? '';
            $image = $datos["image"] ?? '';

            $query = "INSERT INTO usuarios (email, pass_word, nam_e, last_name, alias, image_avatar) 
                      VALUES (:email, :password, :name, :lastname, :alias, :image)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":lastname", $lastname);
            $stmt->bindParam(":alias", $alias);
            $stmt->bindParam(":image", $image);

            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "user_id" => $this->conn->lastInsertId()
                ];
            }
            return ["success" => false, "error" => "No se pudo insertar usuario"];
        } catch (PDOException $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }

    public function actualizarUsuario($datos)
    {
        try {
            $email = $datos["email"] ?? '';
            $name = $datos["name"] ?? '';
            $lastname = $datos["lastname"] ?? '';
            $alias = $datos["alias"] ?? '';
            $image = $datos["image"] ?? '';
             $password = $datos["password"] ?? '';

            $query = "UPDATE usuarios SET nam_e = :name, last_name = :lastname, alias = :alias, image_avatar = :image,
                     pass_word = :password WHERE email = :email";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":lastname", $lastname);
            $stmt->bindParam(":alias", $alias);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $password);
            
            $stmt->execute();
                
         	$updateQuery = "SELECT email, nam_e, last_name, alias, image_avatar FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch (PDOException $error) {
            $data = ["error" => $error->getMessage()];
            return $data;
        }
    }


    public function emailExiste($email)
    {
        try {
            $query = "SELECT email FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            $data = ["error" => $error->getMessage()];
            return  $data;
        }
    }
}
