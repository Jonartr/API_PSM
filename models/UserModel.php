<?php
require_once(__DIR__ . '/../config/Database.php');

class Usuarios
{
    private $db;
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function loginUsuario($email, $password)
    {
        $query = "SELECT email, nam_e, last_name, alias, image_avatar FROM usuarios WHERE email = ? AND pass_word = ?;";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    public function nuevoUsuario($datos)
    {
        try {

            $email = $datos["email"];
            $password = $datos["password"];
            $name = $datos["name"];
            $lastname = $datos["lastname"];
            $alias = $datos["alias"];
            $image = $datos["image"];

            $query = "INSERT INTO usuarios (email, pass_word, nam_e, last_name, alias,image_avatar) VALUES (?,?,?,?,?,?);";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ssssss", $email, $password, $name, $lastname, $alias, $image);
            $stmt->execute();

            return true;
        } catch (Error $error) {
            http_response_code(400);
            echo json_encode(["message" => $error]);
            return false;
        }
    }

    public function actualizarUsuario($datos)
    {

        try {
            $email = $datos["email"];
            $name = $datos["name"];
            $lastname = $datos["lastname"];
            $alias = $datos["alias"];
            $image = $datos["image"];

            $query = "UPDATE usuarios SET nam_e = ?, last_name = ?, alias = ?, image_avatar = ? WHERE email = ?;";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("sssss",  $name,  $lastname, $alias, $image, $email);
            $stmt->execute();
            return true;
        } catch (Error $error) {
            http_response_code(400);
            echo json_encode(["message" => $error]);
            return false;
        }
    }

    public function actualizarPassword($email, $password)
    {

        try {

            $query = "UPDATE usuarios SET pass_word = ? WHERE email = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ss", $password, $email);
            $stmt->execute();
            return true;
        } catch (Error $error) {
            http_response_code(400);
            echo json_encode(["message" => $error]);
            return false;
        }
    }
}
