<?php
require_once(__DIR__ . '/../models/PostModel.php');

class PostController
{
    private $postModel;

    public function __construct()
    {
        $this->postModel = new Publicaciones();
    }

    public function prueba_imagenes()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';


        if (strpos($contentType, 'multipart/form-data') !== false) {
            if (isset($_FILES['imagenes']) && $_FILES['imagenes']['error'] === UPLOAD_ERR_OK) {
                foreach ($_FILES['imagenes'] as $Imagen) {
                  $imageName =  $this->saveImagePost($_FILES['imagenes'], "Hola");
                 $imageFile =  "https://apipsm-production.up.railway.app/$imageName";
                     $this->sendResponse(201, ["message" => $imageFile]);
                }
            }
        }
    }

    public function create()
    {

        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';


        if (strpos($contentType, 'multipart/form-data') !== false) {

            $title = $_POST['title'];
            $description = $_POST['description'];
            $email = $_POST['email'];


            if (!isset($title) || !isset($description) || !isset($email)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $data = [
                'title' => $title,
                'description' => $description,
                'email' => $email

            ];

            $result = $this->postModel->nuevoPost($data);
            $photodata = null;
            $imagenPath = null;
            if ($result != null) {

                if (isset($_FILES['imagenes']) && $_FILES['imagenes']['error'] === UPLOAD_ERR_OK) {

                    $imagenPath = $this->saveImagePost($_FILES['imagen'], $email);
                    if ($imagenPath != null) {
                        $photodata = [
                            "image" => $imagenPath ? "https://apipsm-production.up.railway.app/$imagenPath" : null,
                            "email" => $email,
                            "idphoto" => $result
                        ];

                        $result_2 =  $this->postModel->loadImage($photodata);

                        if ($result_2) {
                            $this->sendResponse(201, ["message" => "Publicacion creada"]);
                        } else {
                            $this->sendResponse(404, ["message" => "Error al crear publicacion"]);
                        }
                    }
                } else {
                    $this->sendResponse(404, ["message" => "No se encontro imagen"]);
                }

                $this->sendResponse(201, ["message" => "Publicacion Correcta", "valor" => $result, "photodata:" => $photodata, "path" =>  $imagenPath]);
            } else {
                $this->sendResponse(401, ["message" => "Error al crear publicacion"]);
            }
        }
    }

    public function update()
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';


        if (strpos($contentType, 'multipart/form-data') !== false) {

            $title = $_POST['title'];
            $description = $_POST['description'];
            $email = $_POST['email'];
            $id = $_POST['idstory'];


            if (!isset($title) || !isset($description)) {
                $this->sendResponse(404, ["message" => "Datos incompletos"]);
            }

            $data = [
                'title' => $title,
                'description' => $description,
                'email' => $email,
                'idstory' => $id

            ];

            $result = $this->postModel->actualizarPost($data);
            $photodata = null;
            $imagenPath = null;
            if ($result != null) {

                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {

                    $imagenPath = $this->saveImagePost($_FILES['imagen'], $email);
                    if ($imagenPath != null) {
                        $photodata = [
                            "image" => $imagenPath ? "https://apipsm-production.up.railway.app/$imagenPath" : null,
                            "idstory" => $id,
                            'email' => $email,
                            "idphoto" => $id
                        ];

                        $result_2 =  $this->postModel->updateImage($photodata);

                        if ($result_2) {
                            $this->sendResponse(201, ["message" => "Publicacion actualizada"]);
                        } else {
                            $this->sendResponse(404, ["message" => "Error al actualizar publicacion"]);
                        }
                    }
                } else {
                    $this->sendResponse(404, ["message" => "No se encontro imagen"]);
                }

                $this->sendResponse(201, ["message" => "Publicacion Correcta", "valor" => $result, "photodata:" => $photodata, "path" =>  $imagenPath]);
            } else {
                $this->sendResponse(401, ["message" => "Error al actualizar publicacion"]);
            }
        }
    }

    private function saveImagePost($image, $email)
    {
        $uploadPath = "data/post/ " . $email . "/" . "images/";


        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fileExtension = pathinfo($image['name'], PATHINFO_EXTENSION);
        $fileName = $email . '_' . time() . '.' . $fileExtension;
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

    public function getpost()
    {
        $result = $this->postModel->loadPost();
        $this->sendResponse(200, $result);
    }



    private function sendResponse($statusCode, $data)
    {
        http_response_code($statusCode);
        echo json_encode($data);
    }
}
