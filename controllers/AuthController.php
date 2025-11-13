<?php
require_once(__DIR__ . '/../models/UserModel.php');
//Clase para Login 
class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new Usuarios();
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            $this->sendResponse(
                400,
                ["success" => false, "message" => "Campos incompletos"]
            );
        } else {

            $loginuser = $this->userModel->loginUsuario($data['email'], $data['password']);

            if ($loginuser) {
                $this->sendResponse(201, $loginuser);
            } else {
                $this->sendResponse(404, ["message" => "Datos incorrectos","data" => $data, "login" =>$loginuser]);
            }
        }
    }


    public function register()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'multipart/form-data') !== false) {
            $name = $_POST['name'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $alias = $_POST['alias'];
            $password = $_POST['password'];

            if (!isset($name) || !isset($lastname) || !isset($email) || !isset($alias) || !isset($password)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $imagenPath = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

                $imagenPath = $this->uploadImage($_FILES['imagen'],$alias);

                if ($imagenPath != null) {
                    $userData = [
                        'email' =>  $email,
                        'password' =>  $password,
                        'name' =>  $name,
                        'lastname' => $lastname,
                        'alias' => $alias,
                        'image' =>  $imagenPath ? "http://192.168.100.215/PSM/$imagenPath" : null
                    ];


                    $userRegister = $this->userModel->nuevoUsuario($userData);

                    if ($userRegister) {
                        $this->sendResponse(201, ["message" => "Registro de usuario correcto", "image" => $imagenPath]);
                    } else {
                        $this->sendResponse(404, ["message" => "Error al registrar usuario"]);
                    }
                } else {
                    $this->sendResponse(404, ["message" => "Error al cargar imagen " . $imagenPath ]);
                }
            } else {
                $this->sendResponse(404, ["message" => "No hay imagen cargada"]);
            }
        } else {
            $this->sendResponse(404, ["message" => "Formato incorrecto"]);
        }
    }

    public function uploadImage($image,$alias)
    {
        $uploadPath = "data/userprofile/images/";


        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $fileName = $alias . '_' . time() . '.' . $fileExtension;
        $filePath = $uploadPath . $fileName;

        if (move_uploaded_file($image['tmp_name'], $filePath)) {
            error_log("Imagen guardada en: $filePath");
            return $filePath;
        } else {
            error_log("Error moviendo archivo: " . $image['tmp_name'] . " a " . $filePath);
            $message = "Error moviendo archivo: " . $image['tmp_name'] . " a " . $filePath;
            return $message;
        }
    }


    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
