<?php
require_once(__DIR__ . '/../models/UserModel.php');

class UserController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new Usuarios();
    }

    public function updateProfile()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'multipart/form-data') !== false) {
            $name = $_POST['name'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $alias = $_POST['alias'];
            $password = $_POST['password'];

            if (!isset($name) || !isset($lastname) || !isset($email) || !isset($alias)|| !isset($password)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $imagenPath = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

                $imagenPath = $this->uploadImage($_FILES['imagen'], $alias);

                if ($imagenPath != null) {
                    $userData = [
                        'email' =>  $email,
                        'name' =>  $name,
                        'lastname' => $lastname,
                        'alias' => $alias,
                        'password' => $password,
                        'image' =>  $imagenPath ? "https://apipsm-production.up.railway.app/$imagenPath" : null
                    ];


                    $userRegister = $this->userModel->actualizarUsuario($userData);

                    if ($userRegister) {
                        $this->sendResponse(201, $userData);
                    } else {
                        $this->sendResponse(404, ["message" => "Error al actualizar usuario"]);
                    }
                } else {
                    $this->sendResponse(404, ["message" => "Error al cargar imagen " . $imagenPath]);
                }
            } else {
                $this->sendResponse(404, ["message" => "No hay imagen cargada"]);
            }
        } else {
            $this->sendResponse(404, ["message" => "Formato incorrecto"]);
        }
    }


    public function uploadImage($image, $alias)
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
