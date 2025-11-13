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
        } catch (PDOException $e) {
            $data = ["error" => $e->getMessage()];
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
            
        } catch (PDOException $e) {
            error_log("Error en nuevoUsuario: " . $e->getMessage());
            return [
                "success" => false, 
                "error" => $e->getMessage(),
                "error_code" => $e->getCode()
            ];
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

            $query = "UPDATE usuarios SET nam_e = :name, last_name = :lastname, alias = :alias, image_avatar = :image 
                      WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":lastname", $lastname);
            $stmt->bindParam(":alias", $alias);
            $stmt->bindParam(":image", $image);
            $stmt->bindParam(":email", $email);
            
            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "rows_affected" => $stmt->rowCount()
                ];
            }
            return ["success" => false, "error" => "No se pudo actualizar usuario"];
            
        } catch (PDOException $e) {
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            return [
                "success" => false, 
                "error" => $e->getMessage()
            ];
        }
    }

    public function actualizarPassword($email, $password)
    {
        try {
            $query = "UPDATE usuarios SET pass_word = :password WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":email", $email);
            
            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "rows_affected" => $stmt->rowCount()
                ];
            }
            return ["success" => false, "error" => "No se pudo actualizar contraseña"];
            
        } catch (PDOException $e) {
            error_log("Error en actualizarPassword: " . $e->getMessage());
            return [
                "success" => false, 
                "error" => $e->getMessage()
            ];
        }
    }

    // Método adicional para obtener usuario por email
    public function obtenerUsuarioPorEmail($email)
    {
        try {
            $query = "SELECT id, email, nam_e, last_name, alias, image_avatar FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en obtenerUsuarioPorEmail: " . $e->getMessage());
            return false;
        }
    }

    // Método para verificar si el email ya existe
    public function emailExiste($email)
    {
        try {
            $query = "SELECT id FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            return $stmt->fetch() !== false;
            
        } catch (PDOException $e) {
            error_log("Error en emailExiste: " . $e->getMessage());
            return false;
        }
    }
}
?>
